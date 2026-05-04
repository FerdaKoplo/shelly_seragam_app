<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Guest\CartController;
use App\Http\Controllers\Guest\KatalogController;
use App\Http\Controllers\Guest\LandingController;
use App\Http\Controllers\User\KatalogProdukController;
use App\Http\Controllers\User\KelolaTransaksiController;
use App\Http\Controllers\User\ManageKustomisasiController;
use App\Http\Controllers\User\PegawaiController;
use App\Http\Controllers\User\StatistikPenjualanController;
use App\Http\Controllers\User\VoucherController;
use App\Models\ProdukKatalog;
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
 * Checkout
 */
Route::match(['GET', 'POST'], '/checkout', function (Request $request) {
    $type = $request->input('type', $request->query('type', 'katalog'));
    $checkoutNotes = (string) $request->input('notes', $request->session()->get('cart_notes', ''));

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

            'shipping_id' => ['required', 'in:reg,exp'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);
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
        ['id' => 'reg', 'label' => 'Regular', 'price' => 15000],
        ['id' => 'exp', 'label' => 'Express', 'price' => 35000],
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

    // Payment gateway redirect (Midtrans Snap)
    // Triggered only when the customer actually submits the checkout form.
    if (
        $request->isMethod('post')
        && $request->hasAny(['full_name', 'email', 'phone', 'address', 'city', 'province', 'postal_code', 'shipping_id'])
        && $request->input('payment_method') === 'midtrans'
        && $type === 'katalog'
    ) {
        $serverKey = (string) env('MIDTRANS_SERVER_KEY', '');
        $isProduction = (bool) env('MIDTRANS_IS_PRODUCTION', false);
        $snapBaseUrl = $isProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        if ($serverKey === '') {
            return back()
                ->withErrors(['payment_method' => 'Midtrans belum dikonfigurasi (MIDTRANS_SERVER_KEY).'])
                ->withInput();
        }

        $orderId = 'ORDER-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

        $itemDetails = collect($katalogItems)->map(function ($item) {
            return [
                'id' => (string) ($item['katalog_id'] ?? $item['id'] ?? ''),
                'price' => (int) ($item['price'] ?? 0),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'name' => (string) ($item['name'] ?? 'Produk'),
            ];
        })->values()->all();

        $grossAmount = collect($itemDetails)->sum(function ($item) {
            return ((int) $item['price']) * ((int) $item['quantity']);
        });

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $grossAmount,
            ],
            'item_details' => $itemDetails,
            'customer_details' => [
                'first_name' => (string) $request->input('full_name'),
                'email' => (string) $request->input('email'),
                'phone' => (string) $request->input('phone'),
                'shipping_address' => [
                    'address' => (string) $request->input('address'),
                    'city' => (string) $request->input('city'),
                    'postal_code' => (string) $request->input('postal_code'),
                    'country_code' => 'IDN',
                ],
            ],
        ];

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->asJson()
            ->post($snapBaseUrl . '/snap/v1/transactions', $payload);

        if (!$response->successful()) {
            return back()
                ->withErrors(['payment_method' => 'Gagal membuat pembayaran Midtrans. Coba lagi.'])
                ->withInput();
        }

        $redirectUrl = (string) ($response->json('redirect_url') ?? '');
        if ($redirectUrl === '') {
            return back()
                ->withErrors(['payment_method' => 'Response Midtrans tidak valid (redirect_url kosong).'])
                ->withInput();
        }

        return redirect()->away($redirectUrl);
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
Route::prefix('admin')->group(function () {
    Route::get('/manage-transaksi', function () {
        return view('pages.user.transaksi.index');
    })->name('manage.transaksi');

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
        Route::post('/', [KelolaTransaksiController::class, 'store'])->name('.store');
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
