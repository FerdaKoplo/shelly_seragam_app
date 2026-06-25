<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\Guest\CheckoutService;
use App\Services\Guest\XenditInvoiceService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutService $checkoutService,
        private readonly XenditInvoiceService $xenditInvoiceService,
    ) {
    }

    public function __invoke(Request $request)
    {
        $type = $request->input('type', $request->query('type', 'katalog'));
        $checkoutNotes = $this->checkoutService->resolveCheckoutNotes($request);

        if ($request->isMethod('get') && $request->boolean('checkout_success')) {
            if ($type === 'katalog') {
                $request->session()->forget('cart');
            }

            return redirect()->route('checkout', ['type' => $type])
                ->with('success', CheckoutService::SUCCESS_MESSAGE);
        }

        if ($this->checkoutService->isCustomerFormSubmitted($request)) {
            $request->validate($this->checkoutService->customerValidationRules());
        }

        $orderPayload = $this->checkoutService->decodeOrderPayload($request);
        $katalogItems = $this->checkoutService->resolveKatalogItems($request, $type, $orderPayload);

        if ($request->isMethod('post') && $request->hasFile('design_files')) {
            $request->validate($this->checkoutService->designFilesValidationRules());
        }

        $uploadedFiles = $this->checkoutService->storeDesignFiles($request);
        $mockCustomData = $this->checkoutService->buildMockCustomData($request, $checkoutNotes, $uploadedFiles);

        if ($this->checkoutService->isXenditPaymentRequest($request, $type)) {
            ['order' => $order, 'amount' => $amount, 'items' => $items] = $this->checkoutService->createKatalogOrder($request, $katalogItems);

            if (!$order) {
                return back()
                    ->withErrors(['payment_method' => 'Subtotal pesanan tidak valid. Periksa kembali keranjang Anda.'])
                    ->withInput();
            }

            $result = $this->xenditInvoiceService->createInvoiceForOrder($request, $order, $amount, $items);

            if (!$result['success']) {
                return back()
                    ->withErrors(['payment_method' => $result['error']])
                    ->withInput();
            }

            return redirect()->away($result['invoice_url']);
        }

        if ($this->checkoutService->isKustomOrderRequest($request, $type)) {
            $this->checkoutService->createKustomOrder($request, $mockCustomData, $checkoutNotes);

            return redirect()->route('checkout', ['type' => 'kustom'])
                ->with('success', CheckoutService::SUCCESS_MESSAGE);
        }

        return view('pages.guest.checkout.checkout', [
            'type' => $type,
            'items' => $katalogItems,
            'customData' => $mockCustomData,
            'checkoutNotes' => $checkoutNotes,
            'shippingOptions' => $this->checkoutService->shippingOptions,
        ]);
    }
}
