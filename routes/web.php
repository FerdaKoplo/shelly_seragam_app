<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\KatalogController;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\Guest\ShippingController;
use App\Http\Controllers\User\KatalogProdukController;
use App\Http\Controllers\User\KelolaTransaksiController;
use App\Http\Controllers\User\ManageKustomisasiController;
use App\Http\Controllers\User\PegawaiController;
use App\Http\Controllers\User\StatistikPenjualanController;
use App\Http\Controllers\User\VoucherController;
use App\Http\Controllers\Webhooks\XenditWebhookController;
use App\Models\CheckoutOrder;
use App\Models\ProdukKatalog;
use App\Models\PaymentInvoice;
use App\Services\CheckoutTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// auth
Route::get('/login', function () {
    return view('pages.auth.login');
});
Route::post('/login', LoginController::class)->name('login');

Route::post('/logout', LogoutController::class)->middleware('auth')->name('logout');

// guest routes
Route::get('/', [LandingController::class, 'index'])->name('home');

Route::get('/kustom', function () {
    return view('pages.guest.kustom.index');
})->name('kustom');

Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

Route::get('/katalog/{id}', [KatalogController::class, 'show'])
    ->whereNumber('id')
    ->name('product.show');

/**
 * Keranjang
 */
Route::get('/keranjang', [CartController::class, 'index'])->name('keranjang');

Route::prefix('keranjang')->name('cart.')->group(function () {
    Route::post('/add/{katalog_id}', [CartController::class, 'add'])
        ->whereNumber('katalog_id')
        ->name('add');

    Route::patch('/update/{katalog_id}', [CartController::class, 'update'])
        ->whereNumber('katalog_id')
        ->name('update');

    Route::patch('/notes', [CartController::class, 'updateNotes'])
        ->name('notes.update');

    Route::delete('/remove/{katalog_id}', [CartController::class, 'remove'])
        ->whereNumber('katalog_id')
        ->name('remove');

    Route::delete('/clear', [CartController::class, 'clear'])
        ->name('clear');
});

/**
 * RajaOngkir (Customer checkout helper)
 */
Route::prefix('shipping')->name('shipping.')->group(function () {
    Route::get('/destinations', [ShippingController::class, 'destinations'])->name('destinations');
    Route::post('/cost', [ShippingController::class, 'cost'])->name('cost');
});

/**
 * Webhooks
 */
Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::post('/xendit/invoice', [XenditWebhookController::class, 'invoice'])->name('xendit.invoice');
});

/**
 * Checkout
 */
Route::match(['GET', 'POST'], '/checkout', function (Request $request) {
    $type = $request->input('type', $request->query('type', 'katalog'));
    $checkoutNotes = (string) $request->input('notes', $request->session()->get('cart_notes', ''));

    $orderSuccessMessage = 'Pesanan berhasil dibuat, Anda akan dihubungi oleh CS untuk konfirmasi dan finalisasi harga.';

    // After Xendit payment, browser lands here with ?checkout_success=1 — swap for clean URL + flash (see x-shared.notification).
    if ($request->isMethod('get') && $request->boolean('checkout_success')) {
        if ($type === 'katalog') {
            $request->session()->forget('cart');
        }

        return redirect()->route('checkout', ['type' => $type])
            ->with('success', $orderSuccessMessage);
    }

    // Validate only when the customer form is actually being submitted.
    // (We also use POST to open checkout from product detail.)
    if ($request->isMethod('post') && $request->hasAny([
        'full_name',
        'email',
        'phone',
        'address',
        'city',
        'province',
        'postal_code',
        'shipping_id',
    ])) {
        $request->validate([
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9+\-\s]+$/'],

            'address' => ['required', 'string', 'min:5', 'max:255'],
            'city' => ['required', 'string', 'min:2', 'max:100'],
            'province' => ['required', 'string', 'min:2', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{4,6}$/'],

            // Value comes from dynamic RajaOngkir/Komerce cost mapping on the checkout page
            // (e.g. "jne-reg", "jne-jtr", etc). Avoid hard-coding reg/exp.
            'shipping_id' => ['required', 'string', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
    }

    $orderPayload = [];
    if ($request->filled('order_payload')) {
        $decodedPayload = json_decode((string) $request->input('order_payload'), true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decodedPayload)) {
            $orderPayload = $decodedPayload;
        }
    }

    // Direct checkout (single item) from product detail page
    if ($request->isMethod('post') && $request->filled('katalog_id')) {
        $katalogId = (int) $request->input('katalog_id');
        $qty = max(1, (int) $request->input('quantity', 1));
        $size = $request->input('size');

        $katalog = ProdukKatalog::query()->with(['produk', 'fotos'])->findOrFail($katalogId);
        $name = $katalog->produk->nama_produk ?? 'Produk';

        $image = optional($katalog->fotos->first())->foto
            ?? optional($katalog->fotos->first())->path
            ?? optional($katalog->fotos->first())->url
            ?? null;

        $katalogItems = [[
            'id' => $katalogId,
            'katalog_id' => $katalogId,
            'name' => $name,
            'price' => (int) $katalog->harga,
            'quantity' => $qty,
            'size' => is_string($size) && trim($size) !== '' ? trim($size) : null,
            'image' => $image,
        ]];
    } elseif ($request->isMethod('post') && !empty($orderPayload) && $type === 'katalog') {
        $katalogItems = array_map(function ($item) {
            $rawImage = $item['image'] ?? null;
            $isAbsolute = is_string($rawImage)
                && (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://'));

            $item['image_url'] = $isAbsolute
                ? $rawImage
                : ($rawImage ? asset('storage/' . ltrim($rawImage, '/')) : 'https://picsum.photos/id/1/600/800');

            return [
                'id' => $item['id'] ?? $item['katalog_id'] ?? null,
                'katalog_id' => $item['katalog_id'] ?? $item['id'] ?? null,
                'name' => $item['name'] ?? 'Produk',
                'price' => (int) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'size' => $item['size'] ?? null,
                'image' => $item['image'] ?? null,
                'image_url' => $item['image_url'],
            ];
        }, array_values($orderPayload));
    } else {
        $katalogItems = array_map(function ($item) {
            $rawImage = $item['image'] ?? null;
            $isAbsolute = is_string($rawImage)
                && (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://'));

            $item['image_url'] = $isAbsolute
                ? $rawImage
                : ($rawImage ? asset('storage/' . ltrim($rawImage, '/')) : 'https://picsum.photos/id/1/600/800');

            return [
                'id' => $item['id'] ?? $item['katalog_id'] ?? null,
                'katalog_id' => $item['katalog_id'] ?? $item['id'] ?? null,
                'name' => $item['name'] ?? 'Produk',
                'price' => (int) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'size' => $item['size'] ?? null,
                'image' => $item['image'] ?? null,
                'image_url' => $item['image_url'],
            ];
        }, array_values($request->session()->get('cart', [])));
    }

    $shippingOptions = [
        ['id' => 'reg', 'label' => 'Regular', 'duration' => '2-3 hari', 'price' => 15000],
        ['id' => 'exp', 'label' => 'Express', 'duration' => '1 hari', 'price' => 35000],
    ];

    $uploadedFiles = [];

    if ($request->isMethod('post') && $request->hasFile('design_files')) {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg', 'cdr'];
        $request->validate([
            'design_files' => ['array'],
            'design_files.*' => [
                'file',
                'max:10240',
                function ($attribute, $value, $fail) use ($allowedExtensions) {
                    $extension = strtolower($value->getClientOriginalExtension());
                    if (!in_array($extension, $allowedExtensions, true)) {
                        $fail('Format file tidak didukung. Gunakan .jpg, .png, .svg, atau .cdr');
                    }
                },
            ],
        ]);

        foreach ($request->file('design_files', []) as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->store('uploads/kustom', 'public');
            $uploadedFiles[] = [
                'name' => $file->getClientOriginalName(),
                'url' => Storage::disk('public')->url($path),
                'extension' => $extension,
            ];
        }
    }

    $mockCustomData = [
        'title' => 'Kustom',
        'qty' => ($request->input('total_quantity', 1)) . ' pcs',
        'type' => $request->input('category', 'bundle'),
        'price' => (int) $request->input('estimated_total', 1750000),

        'attachments' => $uploadedFiles,

        'notes' => $checkoutNotes,
        'size' => $request->input('size'),
    ];

    // Payment gateway redirect (Xendit Invoice)
    // Triggered only when the customer actually submits the checkout form.
    if (
        $request->isMethod('post')
        && $request->hasAny(['full_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'shipping_id'])
        && $request->input('payment_method') === 'xendit'
        && $type === 'katalog'
    ) {
        $secretKey = (string) env('XENDIT_SECRET_KEY', '');
        $baseUrl = rtrim((string) env('XENDIT_BASE_URL', 'https://api.xendit.co'), '/');

        if ($secretKey === '') {
            return back()
                ->withErrors(['payment_method' => 'Xendit belum dikonfigurasi (XENDIT_SECRET_KEY).'])
                ->withInput();
        }

        $externalId = 'ORDER-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $items = collect($katalogItems)->map(function ($item) {
            return [
                'name' => (string) ($item['name'] ?? 'Produk'),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'price' => (int) ($item['price'] ?? 0),
            ];
        })->values()->all();

        $subtotal = (int) collect($items)->sum(function ($item) {
            return ((int) $item['price']) * ((int) $item['quantity']);
        });

        $shippingMethod = (string) $request->input('shipping_id', '');
        $shippingPrice = (int) data_get(
            collect($shippingOptions)->firstWhere('id', $shippingMethod),
            'price',
            0
        );
        $amount = $subtotal + $shippingPrice;

        if ($amount <= 0) {
            return back()
                ->withErrors(['payment_method' => 'Subtotal pesanan tidak valid. Periksa kembali keranjang Anda.'])
                ->withInput();
        }

        $order = CheckoutOrder::query()->create([
            'external_id' => $externalId,
            'status' => 'CREATED',
            'type' => 'katalog',
            'customer_name' => (string) $request->input('full_name'),
            'customer_email' => (string) $request->input('email'),
            'customer_phone' => (string) $request->input('phone'),
            'address' => (string) $request->input('address'),
            'city' => (string) $request->input('city'),
            'province' => (string) $request->input('province'),
            'postal_code' => (string) $request->input('postal_code'),
            'destination_id' => (int) $request->input('destination_id', 0) ?: null,
            'shipping_id' => $shippingMethod,
            'shipping_cost' => $shippingPrice,
            'subtotal' => $subtotal,
            'total' => $amount,
            'items' => $katalogItems,
            'notes' => (string) $request->input('notes', ''),
        ]);

        // Mirror checkout order into "transaksi" so it appears in admin "Manage Transaksi".
        app(CheckoutTransaksiService::class)->ensureTransaksiFromCheckoutOrder($order);

        $fullName = trim((string) $request->input('full_name'));
        $nameParts = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $givenNames = trim((string) ($nameParts[0] ?? $fullName));
        $surname = trim(implode(' ', array_slice($nameParts, 1)));

        $customer = [
            'given_names' => $givenNames !== '' ? $givenNames : $fullName,
            'email' => (string) $request->input('email'),
            'mobile_number' => (string) $request->input('phone'),
        ];

        if ($surname !== '') {
            $customer['surname'] = $surname;
        }

        $payload = [
            'external_id' => $externalId,
            'amount' => $amount,
            'currency' => 'IDR',
            'description' => 'Pembayaran pesanan ' . $externalId,
            'invoice_duration' => 86400,
            'callback_url' => route('webhooks.xendit.invoice'),
            'success_redirect_url' => url('/checkout') . '?' . http_build_query([
                'checkout_success' => '1',
                'type' => 'katalog',
            ]),
            'failure_redirect_url' => url('/checkout'),
            'payer_email' => (string) $request->input('email'),
            'customer' => $customer,
            'items' => $items,
        ];

        $response = Http::withBasicAuth($secretKey, '')
            ->acceptJson()
            ->asJson()
            ->post($baseUrl . '/v2/invoices', $payload);

        if (!$response->successful()) {
            \Log::warning('Xendit invoice creation failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'raw' => $response->body(),
                'payload' => [
                    'external_id' => $externalId,
                    'amount' => $amount,
                ],
            ]);

            $message = (string) data_get($response->json(), 'message', 'Gagal membuat pembayaran Xendit. Coba lagi.');

            return back()
                ->withErrors(['payment_method' => $message])
                ->withInput();
        }

        $invoiceUrl = (string) ($response->json('invoice_url') ?? '');
        if ($invoiceUrl === '') {
            return back()
                ->withErrors(['payment_method' => 'Response Xendit tidak valid (invoice_url kosong).'])
                ->withInput();
        }

        PaymentInvoice::query()->create([
            'provider' => 'xendit',
            'checkout_order_id' => $order->id,
            'external_id' => $externalId,
            'invoice_id' => (string) ($response->json('id') ?? $response->json('invoice_id') ?? ''),
            'status' => (string) ($response->json('status') ?? ''),
            'amount' => $amount,
            'invoice_url' => $invoiceUrl,
            'expiry_date' => $response->json('expiry_date'),
            'raw_payload' => $response->json(),
        ]);

        return redirect()->away($invoiceUrl);
    }

    if (
        $request->isMethod('post')
        && $type === 'kustom'
        && $request->hasAny([
            'full_name',
            'email',
            'phone',
            'address',
            'city',
            'province',
            'postal_code',
            'shipping_id',
        ])
    ) {
        $externalId = 'ORDER-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $order = CheckoutOrder::query()->create([
            'external_id' => $externalId,
            'status' => 'CREATED',
            'type' => 'kustom',
            'customer_name' => (string) $request->input('full_name'),
            'customer_email' => (string) $request->input('email'),
            'customer_phone' => (string) $request->input('phone'),
            'address' => (string) $request->input('address'),
            'city' => (string) $request->input('city'),
            'province' => (string) $request->input('province'),
            'postal_code' => (string) $request->input('postal_code'),
            'destination_id' => (int) $request->input('destination_id', 0) ?: null,
            'shipping_id' => (string) $request->input('shipping_id', ''),
            'shipping_cost' => 0,
            'subtotal' => (int) ($mockCustomData['price'] ?? 0),
            'total' => (int) ($mockCustomData['price'] ?? 0),
            'items' => [
                'category' => $mockCustomData['type'] ?? 'kustom',
                'total_quantity' => (int) $request->input('total_quantity', 1),
                'size' => $mockCustomData['size'] ?? null,
                'attachments' => $mockCustomData['attachments'] ?? [],
            ],
            'notes' => $checkoutNotes,
        ]);

        app(CheckoutTransaksiService::class)->ensureTransaksiFromCheckoutOrder($order);

        return redirect()->route('checkout', ['type' => 'kustom'])
            ->with('success', $orderSuccessMessage);
    }

    return view('pages.guest.checkout.checkout', [
        'type' => $type,
        'items' => $katalogItems,
        'customData' => $mockCustomData,
        'checkoutNotes' => $checkoutNotes,
        'shippingOptions' => $shippingOptions,
    ]);
})->name('checkout');

// user routes
Route::prefix('admin')->middleware('auth')->group(function () {
    Route::prefix('manage-katalog')->name('manage.katalog')->group(function () {
        Route::get('/', [KatalogProdukController::class, 'index'])->name('');
        Route::get('/create', [KatalogProdukController::class, 'create'])->name('.create');
        Route::post('/', [KatalogProdukController::class, 'store'])->name('.store');
        Route::get('/{id}/edit', [KatalogProdukController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [KatalogProdukController::class, 'update'])->name('.update');

        Route::put('/{id}/archive', [KatalogProdukController::class, 'archive'])->name('.archive');
        Route::put('/{id}/restore', [KatalogProdukController::class, 'restore'])->name('.restore');

        Route::delete('/{id}', [KatalogProdukController::class, 'destroy'])->name('.destroy');
    });

    Route::prefix('statistik-transaksi')->name('statistik.transaksi')->group(function () {
        Route::get('/', [StatistikPenjualanController::class, 'index']);
        Route::get('/export', [StatistikPenjualanController::class, 'export'])->name('.export');
    });

    Route::prefix('manage-kustom')->name('manage.kustom')->group(function () {
        Route::get('/', [ManageKustomisasiController::class, 'index'])->name('');
        Route::get('/create', [ManageKustomisasiController::class, 'create'])->name('.create');
        Route::post('/', [ManageKustomisasiController::class, 'store'])->name('.store');
        Route::get('/{id}/edit', [ManageKustomisasiController::class, 'edit'])->name('.edit');
        Route::put('/{id}', [ManageKustomisasiController::class, 'update'])->name('.update');
        Route::delete('/{id}', [ManageKustomisasiController::class, 'destroy'])->name('.destroy');
    });

    Route::prefix('manage-transaksi')->name('manage.transaksi')->group(function () {
        Route::get('/', [KelolaTransaksiController::class, 'index']);
        Route::get('/check-resi', [KelolaTransaksiController::class, 'checkResi'])->name('.check-resi');
        Route::get('/get-ongkir', [KelolaTransaksiController::class, 'getOngkir'])->name('.get-ongkir');
        Route::get('/search-destination', [KelolaTransaksiController::class, 'searchDestination'])->name('.search-destination');
        Route::post('/', [KelolaTransaksiController::class, 'store'])->name('.store');
        Route::post('/upload-payment', [KelolaTransaksiController::class, 'uploadTransaksiKustomPayment'])->name('.upload-payment');
        Route::put('/{id}', [KelolaTransaksiController::class, 'update'])->name('.update');
    });


    Route::prefix('manage-voucher')->name('manage.voucher')->group(function () {
        Route::get('/', [VoucherController::class, 'index']);
        Route::get('/create', [VoucherController::class, 'create'])->name('.create');
        Route::get('/{id}/edit', [VoucherController::class, 'edit'])->name('.edit');

        Route::post('/', [VoucherController::class, 'store'])->name('.store');

        Route::put('/{id}', [VoucherController::class, 'update'])->name('.update');

        Route::patch('/{id}/deactivate', [VoucherController::class, 'deactiveVoucher'])->name('.deactivate');

        Route::delete('/{id}', [VoucherController::class, 'destroy'])->name('.destroy');
    });

    Route::get('/traffic', function () {
        return view('pages.user.admin.traffic.index');
    })->name('traffic');

    Route::prefix('manage-pegawai')->name('manage.pegawai')->group(function () {
        Route::get('/', [PegawaiController::class, 'index']);
        Route::post('/', [PegawaiController::class, 'store'])->name('.store');
        Route::put('/{user_id}', [PegawaiController::class, 'update'])->name('.update');
        Route::delete('/{user_id}', [PegawaiController::class, 'destroy'])->name('.destroy');
    });
});
