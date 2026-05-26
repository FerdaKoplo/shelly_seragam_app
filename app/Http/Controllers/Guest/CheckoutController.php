<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\CheckoutOrder;
use App\Models\ProdukKatalog;
use App\Services\CheckoutTransaksiService;
use App\Services\XenditPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    protected CheckoutTransaksiService $checkoutTransaksiService;
    protected XenditPaymentService $xenditPaymentService;

    public function __construct(
        CheckoutTransaksiService $checkoutTransaksiService,
        XenditPaymentService $xenditPaymentService
    ) {
        $this->checkoutTransaksiService = $checkoutTransaksiService;
        $this->xenditPaymentService = $xenditPaymentService;
    }

    public function __invoke(Request $request)
    {
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
            $externalId = 'ORDER-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));

            $subtotal = (int) collect($katalogItems)->sum(function ($item) {
                return ((int) ($item['price'] ?? 0)) * ((int) ($item['quantity'] ?? 1));
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
            $this->checkoutTransaksiService->ensureTransaksiFromCheckoutOrder($order);

            try {
                $invoiceUrl = $this->xenditPaymentService->createInvoice($order, $katalogItems);
                return redirect()->away($invoiceUrl);
            } catch (\Exception $e) {
                return back()
                    ->withErrors(['payment_method' => $e->getMessage()])
                    ->withInput();
            }
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

            $this->checkoutTransaksiService->ensureTransaksiFromCheckoutOrder($order);

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
    }
}
