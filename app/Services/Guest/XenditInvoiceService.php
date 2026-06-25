<?php

namespace App\Services\Guest;

use App\Models\CheckoutOrder;
use App\Models\PaymentInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Response;

class XenditInvoiceService
{
  public function createInvoiceForOrder(Request $request, CheckoutOrder $order, int $amount, array $items): array
    {
        $outcome = $this->attemptInvoiceCreation($request, $order, $amount, $items);
 
        return $outcome['error'] === null
            ? ['success' => true, 'invoice_url' => $outcome['invoice_url']]
            : $this->failure($outcome['error']);
    }
 
    private function attemptInvoiceCreation(Request $request, CheckoutOrder $order, int $amount, array $items): array
    {
        $config = $this->resolveConfig();
 
        if ($config['secret_key'] === '') {
            return ['error' => 'Xendit belum dikonfigurasi (XENDIT_SECRET_KEY).', 'invoice_url' => null];
        }
 
        $payload = $this->buildInvoicePayload($request, $order, $amount, $items);
 
        $response = Http::withBasicAuth($config['secret_key'], '')
            ->acceptJson()
            ->asJson()
            ->post($config['base_url'] . '/v2/invoices', $payload);
 
        $invoiceUrl = $response->successful() ? (string) ($response->json('invoice_url') ?? '') : '';
 
        $error = match (true) {
            !$response->successful() => $this->logAndExtractError($response, $order, $amount),
            $invoiceUrl === '' => 'Response Xendit tidak valid (invoice_url kosong).',
            default => null,
        };
 
        if ($error === null) {
            $this->persistInvoice($order, $response, $amount, $invoiceUrl);
        }
 
        return ['error' => $error, 'invoice_url' => $error === null ? $invoiceUrl : null];
    }
 
    private function resolveConfig(): array
    {
        return [
            'secret_key' => (string) env('XENDIT_SECRET_KEY', ''),
            'base_url' => rtrim((string) env('XENDIT_BASE_URL', 'https://api.xendit.co'), '/'),
        ];
    }
 
    private function buildInvoicePayload(Request $request, CheckoutOrder $order, int $amount, array $items): array
    {
        return [
            'external_id' => $order->external_id,
            'amount' => $amount,
            'currency' => 'IDR',
            'description' => 'Pembayaran pesanan ' . $order->external_id,
            'invoice_duration' => 86400,
            'callback_url' => route('webhooks.xendit.invoice'),
            'success_redirect_url' => url('/checkout') . '?' . http_build_query([
                'checkout_success' => '1',
                'type' => 'katalog',
            ]),
            'failure_redirect_url' => url('/checkout'),
            'payer_email' => (string) $request->input('email'),
            'customer' => $this->buildCustomer($request),
            'items' => $items,
        ];
    }
 
    private function buildCustomer(Request $request): array
    {
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
 
        return $customer;
    }
 
    private function logAndExtractError(Response $response, CheckoutOrder $order, int $amount): string
    {
        Log::warning('Xendit invoice creation failed', [
            'status' => $response->status(),
            'body' => $response->json(),
            'raw' => $response->body(),
            'payload' => [
                'external_id' => $order->external_id,
                'amount' => $amount,
            ],
        ]);
 
        return (string) data_get($response->json(), 'message', 'Gagal membuat pembayaran Xendit. Coba lagi.');
    }
 
    private function persistInvoice(CheckoutOrder $order, Response $response, int $amount, string $invoiceUrl): void
    {
        PaymentInvoice::query()->create([
            'provider' => 'xendit',
            'checkout_order_id' => $order->id,
            'external_id' => $order->external_id,
            'invoice_id' => (string) ($response->json('id') ?? $response->json('invoice_id') ?? ''),
            'status' => (string) ($response->json('status') ?? ''),
            'amount' => $amount,
            'invoice_url' => $invoiceUrl,
            'expiry_date' => $response->json('expiry_date'),
            'raw_payload' => $response->json(),
        ]);
    }
 
    private function failure(string $error): array
    {
        return [
            'success' => false,
            'error' => $error,
        ];
    }
}