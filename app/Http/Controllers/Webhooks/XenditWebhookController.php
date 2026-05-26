<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\CheckoutOrder;
use App\Models\PaymentInvoice;
use App\Services\CheckoutTransaksiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class XenditWebhookController extends Controller
{
    public function invoice(Request $request)
    {
        $expectedToken = (string) env('XENDIT_CALLBACK_TOKEN', '');
        if ($expectedToken !== '') {
            $gotToken = (string) $request->header('x-callback-token', '');
            if (!hash_equals($expectedToken, $gotToken)) {
                Log::warning('Xendit webhook unauthorized', [
                    'ip' => $request->ip(),
                    'path' => $request->path(),
                ]);
                return response()->json(['ok' => false, 'message' => 'Unauthorized'], 401);
            }
        }

        $payload = $request->all();
        $externalId = (string) data_get($payload, 'external_id', '');
        if ($externalId === '') {
            return response()->json(['ok' => false, 'message' => 'Missing external_id'], 422);
        }

        $invoiceId = (string) data_get($payload, 'id', data_get($payload, 'invoice_id', ''));
        $status = (string) data_get($payload, 'status', '');
        $amount = (int) data_get($payload, 'amount', 0);
        $invoiceUrl = (string) data_get($payload, 'invoice_url', '');
        $expiryDate = data_get($payload, 'expiry_date');
        $paidAt = data_get($payload, 'paid_at');

        $invoice = PaymentInvoice::query()->where('provider', 'xendit')->firstOrNew(['external_id' => $externalId]);
        $invoice->fill([
            'provider' => 'xendit',
            'invoice_id' => $invoiceId !== '' ? $invoiceId : ($invoice->invoice_id ?? null),
            'status' => $status !== '' ? $status : ($invoice->status ?? null),
            'amount' => $amount > 0 ? $amount : ($invoice->amount ?? 0),
            'invoice_url' => $invoiceUrl !== '' ? $invoiceUrl : ($invoice->invoice_url ?? null),
            'expiry_date' => $expiryDate ?: ($invoice->expiry_date ?? null),
            'paid_at' => $paidAt ?: ($invoice->paid_at ?? null),
            'raw_payload' => $payload,
        ]);
        $invoice->save();

        // Check for both PAID and SETTLED states safely
        if (in_array(strtoupper($status), ['PAID', 'SETTLED'])) {
            $order = CheckoutOrder::query()->where('external_id', $externalId)->first();
            if ($order) {
                $order->status = 'PAID'; 
                $order->paid_at = $invoice->paid_at;
                $order->save();

                app(CheckoutTransaksiService::class)->ensureTransaksiFromCheckoutOrder($order);
            }
        }

        return response()->json(['ok' => true]);
    }
}
