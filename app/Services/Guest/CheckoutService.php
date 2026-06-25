<?php

namespace App\Services\Guest;

use App\Models\CheckoutOrder;
use App\Models\ProdukKatalog;
use App\Services\CheckoutTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckoutService
{
    public const SUCCESS_MESSAGE = 'Pesanan berhasil dibuat, Anda akan dihubungi oleh CS untuk konfirmasi dan finalisasi harga.';

    public array $shippingOptions = [
        ['id' => 'reg', 'label' => 'Regular', 'duration' => '2-3 hari', 'price' => 15000],
        ['id' => 'exp', 'label' => 'Express', 'duration' => '1 hari', 'price' => 35000],
    ];

    public function __construct(
        private readonly CheckoutTransaksiService $checkoutTransaksiService,
        private readonly XenditInvoiceService $xenditInvoiceService,
    ) {
    }


    public function isCustomerFormSubmitted(Request $request): bool
    {
        return $request->isMethod('post') && $request->hasAny([
            'full_name',
            'email',
            'phone',
            'address',
            'city',
            'province',
            'postal_code',
            'shipping_id',
        ]);
    }

    public function customerValidationRules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[0-9+\-\s]+$/'],

            'address' => ['required', 'string', 'min:5', 'max:255'],
            'city' => ['required', 'string', 'min:2', 'max:100'],
            'province' => ['required', 'string', 'min:2', 'max:100'],
            'postal_code' => ['required', 'string', 'regex:/^[0-9]{4,6}$/'],

            'shipping_id' => ['required', 'string', 'min:1', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function designFilesValidationRules(): array
    {
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'svg', 'cdr'];

        return [
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
        ];
    }

    public function resolveCheckoutNotes(Request $request): string
    {
        return (string) $request->input('notes', $request->session()->get('cart_notes', ''));
    }

    public function decodeOrderPayload(Request $request): array
    {
        if (!$request->filled('order_payload')) {
            return [];
        }

        $decoded = json_decode((string) $request->input('order_payload'), true);

        return json_last_error() === JSON_ERROR_NONE && is_array($decoded) ? $decoded : [];
    }

    public function resolveKatalogItems(Request $request, string $type, array $orderPayload): array
    {
        if ($request->isMethod('post') && $request->filled('katalog_id')) {
            return [$this->buildItemFromKatalog($request)];
        }

        if ($request->isMethod('post') && !empty($orderPayload) && $type === 'katalog') {
            return array_map(fn(array $item) => $this->normalizeItem($item), array_values($orderPayload));
        }

        $cart = array_values($request->session()->get('cart', []));

        return array_map(fn(array $item) => $this->normalizeItem($item), $cart);
    }


    public function storeDesignFiles(Request $request): array
    {
        if (!($request->isMethod('post') && $request->hasFile('design_files'))) {
            return [];
        }

        $uploadedFiles = [];

        foreach ($request->file('design_files', []) as $file) {
            $extension = strtolower($file->getClientOriginalExtension());
            $path = $file->store('uploads/kustom', 'public');
            $uploadedFiles[] = [
                'name' => $file->getClientOriginalName(),
                'url' => Storage::disk('public')->url($path),
                'extension' => $extension,
            ];
        }

        return $uploadedFiles;
    }

    public function buildMockCustomData(Request $request, string $checkoutNotes, array $uploadedFiles): array
    {
        return [
            'title' => 'Kustom',
            'qty' => ($request->input('total_quantity', 1)) . ' pcs',
            'type' => $request->input('category', 'bundle'),
            'price' => (int) $request->input('estimated_total', 1750000),

            'attachments' => $uploadedFiles,

            'notes' => $checkoutNotes,
            'size' => $request->input('size'),
        ];
    }

    public function shippingPriceFor(string $shippingId): int
    {
        return (int) data_get(
            collect($this->shippingOptions)->firstWhere('id', $shippingId),
            'price',
            0
        );
    }

    public function createKatalogOrder(Request $request, array $katalogItems): array
    {
        $items = collect($katalogItems)->map(fn($item) => [
            'name' => (string) ($item['name'] ?? 'Produk'),
            'quantity' => (int) ($item['quantity'] ?? 1),
            'price' => (int) ($item['price'] ?? 0),
        ])->values()->all();

        $subtotal = (int) collect($items)->sum(
            fn($item) => ((int) $item['price']) * ((int) $item['quantity'])
        );

        $shippingMethod = (string) $request->input('shipping_id', '');
        $shippingPrice = $this->shippingPriceFor($shippingMethod);
        $amount = $subtotal + $shippingPrice;

        if ($amount <= 0) {
            return [
                'order' => null,
                'amount' => $amount,
                'items' => $items,
            ];
        }

        $order = CheckoutOrder::query()->create([
            'external_id' => $this->generateExternalId(),
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

        $this->checkoutTransaksiService->ensureTransaksiFromCheckoutOrder($order);

        return [
            'order' => $order,
            'amount' => $amount,
            'items' => $items,
        ];
    }

    public function createKustomOrder(Request $request, array $mockCustomData, string $checkoutNotes): CheckoutOrder
    {
        $order = CheckoutOrder::query()->create([
            'external_id' => $this->generateExternalId(),
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

        return $order;
    }

    public function generateExternalId(): string
    {
        return 'ORDER-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
    }

    public function isXenditPaymentRequest(Request $request, string $type): bool
    {
        return $this->isCustomerFormSubmitted($request)
            && $request->input('payment_method') === 'xendit'
            && $type === 'katalog';
    }

    public function isKustomOrderRequest(Request $request, string $type): bool
    {
        return $request->isMethod('post') && $type === 'kustom' && $this->isCustomerFormSubmitted($request);
    }

    // helper

    private function buildItemFromKatalog(Request $request): array
    {
        $katalogId = (int) $request->input('katalog_id');
        $qty = max(1, (int) $request->input('quantity', 1));
        $size = $request->input('size');

        $katalog = ProdukKatalog::query()->with(['produk', 'fotos'])->findOrFail($katalogId);
        $name = $katalog->produk->nama_produk ?? 'Produk';

        $image = optional($katalog->fotos->first())->foto
            ?? optional($katalog->fotos->first())->path
            ?? optional($katalog->fotos->first())->url
            ?? null;

        return [
            'id' => $katalogId,
            'katalog_id' => $katalogId,
            'name' => $name,
            'price' => (int) $katalog->harga,
            'quantity' => $qty,
            'size' => is_string($size) && trim($size) !== '' ? trim($size) : null,
            'image' => $image,
        ];
    }

    private function normalizeItem(array $item): array
    {
        $rawImage = $item['image'] ?? null;
        $isAbsolute = is_string($rawImage)
            && (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://'));

        $imageUrl = $isAbsolute
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
            'image_url' => $imageUrl,
        ];
    }

}
