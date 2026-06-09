<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\ProdukKatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CheckoutController extends Controller
{
    public function __invoke(Request $request)
    {
        $type = $request->input('type', $request->query('type', 'katalog'));
        $checkoutNotes = (string) $request->input('notes', $request->session()->get('cart_notes', ''));

        $katalogItems = $this->resolveItems($request);
        $shippingOptions = $this->shippingOptions();
        $uploadedFiles = $this->storeDesignFiles($request);

        $mockCustomData = [
            'title' => 'Kustom',
            'qty' => ($request->input('total_quantity', 1)) . ' pcs',
            'type' => $request->input('category', 'bundle'),
            'price' => (int) $request->input('estimated_total', 1750000),
            'attachments' => $uploadedFiles,
            'notes' => $checkoutNotes,
            'size' => $request->input('size'),
        ];

        return view('pages.guest.checkout.checkout', [
            'type' => $type,
            'items' => $katalogItems,
            'customData' => $mockCustomData,
            'checkoutNotes' => $checkoutNotes,
            'shippingOptions' => $shippingOptions,
        ]);
    }

    private function resolveItems(Request $request): array
    {
        if ($request->isMethod('post') && $request->filled('katalog_id')) {
            return $this->resolveSingleItem($request);
        }

        return array_map(function ($item) {
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

    private function resolveSingleItem(Request $request): array
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

        return [[
            'id' => $katalogId,
            'katalog_id' => $katalogId,
            'name' => $name,
            'price' => (int) $katalog->harga,
            'quantity' => $qty,
            'size' => is_string($size) && trim($size) !== '' ? trim($size) : null,
            'image' => $image,
        ]];
    }

    private function shippingOptions(): array
    {
        return [
            ['id' => 'reg', 'label' => 'Regular', 'price' => 15000],
            ['id' => 'exp', 'label' => 'Express', 'price' => 35000],
        ];
    }

    private function storeDesignFiles(Request $request): array
    {
        $uploadedFiles = [];

        if (!($request->isMethod('post') && $request->hasFile('design_files'))) {
            return $uploadedFiles;
        }

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

        return $uploadedFiles;
    }
}
