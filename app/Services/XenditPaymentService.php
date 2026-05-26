<?php

namespace App\Services;

use App\Models\CheckoutOrder;
use App\Models\PaymentInvoice;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class XenditPaymentService
{
    protected string $secretKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->secretKey = (string) env('XENDIT_SECRET_KEY', '');
        $this->baseUrl = rtrim((string) env('XENDIT_BASE_URL', 'https://api.xendit.co'), '/');
    }

    /**
     * Check if the secret key has been configured.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        return $this->secretKey !== '';
    }

    /**
     * Create a Xendit Invoice for a checkout order.
     *
     * @param CheckoutOrder $order
     * @param array $katalogItems
     * @return string Redirect URL
     * @throws \Exception
     */
    public function createInvoice(CheckoutOrder $order, array $katalogItems): string
    {
        if (!$this->isConfigured()) {
            throw new \Exception('Xendit belum dikonfigurasi (XENDIT_SECRET_KEY).');
        }

        $items = collect($katalogItems)->map(function ($item) {
            return [
                'name' => (string) ($item['name'] ?? 'Produk'),
                'quantity' => (int) ($item['quantity'] ?? 1),
                'price' => (int) ($item['price'] ?? 0),
            ];
        })->values()->all();

        $fullName = trim((string) $order->customer_name);
        $nameParts = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $givenNames = trim((string) ($nameParts[0] ?? $fullName));
        $surname = trim(implode(' ', array_slice($nameParts, 1)));

        $customer = [
            'given_names' => $givenNames !== '' ? $givenNames : $fullName,
            'email' => (string) $order->customer_email,
            'mobile_number' => (string) $order->customer_phone,
        ];

        if ($surname !== '') {
            $customer['surname'] = $surname;
        }

        $payload = [
            'external_id' => $order->external_id,
            'amount' => $order->total,
            'currency' => 'IDR',
            'description' => 'Pembayaran pesanan ' . $order->external_id,
            'invoice_duration' => 86400,
            'callback_url' => route('webhooks.xendit.invoice'),
            'success_redirect_url' => url('/checkout') . '?' . http_build_query([
                'checkout_success' => '1',
                'type' => 'katalog',
            ]),
            'failure_redirect_url' => url('/checkout'),
            'payer_email' => (string) $order->customer_email,
            'customer' => $customer,
            'items' => $items,
        ];

        $response = Http::withBasicAuth($this->secretKey, '')
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl . '/v2/invoices', $payload);

        if (!$response->successful()) {
            Log::warning('Xendit invoice creation failed', [
                'status' => $response->status(),
                'body' => $response->json(),
                'raw' => $response->body(),
                'payload' => [
                    'external_id' => $order->external_id,
                    'amount' => $order->total,
                ],
            ]);

            $message = (string) data_get($response->json(), 'message', 'Gagal membuat pembayaran Xendit. Coba lagi.');
            throw new \Exception($message);
        }

        $invoiceUrl = (string) ($response->json('invoice_url') ?? '');
        if ($invoiceUrl === '') {
            throw new \Exception('Response Xendit tidak valid (invoice_url kosong).');
        }

        PaymentInvoice::query()->create([
            'provider' => 'xendit',
            'checkout_order_id' => $order->id,
            'external_id' => $order->external_id,
            'invoice_id' => (string) ($response->json('id') ?? $response->json('invoice_id') ?? ''),
            'status' => (string) ($response->json('status') ?? ''),
            'amount' => $order->total,
            'invoice_url' => $invoiceUrl,
            'expiry_date' => $response->json('expiry_date'),
            'raw_payload' => $response->json(),
        ]);

        return $invoiceUrl;
    }
}
