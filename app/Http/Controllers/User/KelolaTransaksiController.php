<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AttachmentTransaksiKustom;
use App\Models\OrderTransaksiKustom;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class KelolaTransaksiController extends Controller
{
    protected $rajaOngkir;

    public function __construct()
    {
        $this->rajaOngkir = new \App\Services\RajaOngkirService();
    }
    public function index(Request $request)
    {

        $query = Transaksi::with(['produkTransaksis.produk', 'orderKustoms.attachments', 'pengiriman', 'user', 'paymentInvoice']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaksi_id', 'like', "%{$search}%")
                    ->orWhere('nama_customer', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            if ($request->filter == 'katalog') {
                $query->has('produkTransaksis');
            } elseif ($request->filter == 'kustom') {
                $query->has('orderKustoms');
            }
        }

        $transaksis = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pages.user.transaksi.index', compact('transaksis'));
    }

    public function checkResi(Request $request)
    {
        $request->validate([
            'resi' => 'required',
            'kurir' => 'required'
        ]);

        $trackingData = $this->rajaOngkir->trackWaybill($request->resi, $request->kurir);

        return response()->json($trackingData);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_customer' => 'required|string|max:255',
            'no_hp_customer' => 'required|string|max:20',
            'alamat_customer' => 'required|string',
            'no_resi_customer' => 'required|string|max:255',
            'status' => 'required|in:Created,Paid,Delivered,Done',
            'tanggal_transaksi' => 'required|date',
            'total_harga' => 'required|numeric|min:0'
        ]);


        $validated['tanggal_transaksi'] = date('Y-m-d', strtotime($validated['tanggal_transaksi']));
        $validated['pegawai_id'] = optional($request->user())->user_id;

        Transaksi::create($validated);

        return redirect()->route('manage.transaksi')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|in:Created,Paid,Delivered,Done',
            'no_resi_customer' => [
                'nullable',
                'string',
                'max:255',
                'required_if:status,Delivered'
            ],
        ], [
            'no_resi_customer.required_if' => 'Nomor Resi Wajib Diisi',
        ]);

        if ($request->filled('status') && $request->status !== $transaksi->status) {

            $allowedNextState = [
                'Created' => 'Paid',
                'Paid' => 'Delivered',
                'Delivered' => 'Done',
                'Done' => null,
            ];

            $validNext = $allowedNextState[$transaksi->status] ?? null;

            if ($request->status !== $validNext) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Perubahan status tidak valid'
                ]);
            }
        }

        $transaksi->update([
            'no_resi_customer' => $validated['no_resi_customer'] ?? $transaksi->no_resi_customer,
            'status' => $validated['status'] ?? $transaksi->status,
        ]);

        if ($transaksi->pengiriman && $request->filled('no_resi_customer')) {
            $transaksi->pengiriman->update([
                'status_pengiriman' => 'dikirim'
            ]);
        }

        return back()->with('success', 'Detail transaksi berhasil diperbarui.');
    }


    public function uploadTransaksiKustomPayment(Request $request)
    {
        $validated = $request->validate([
            'order_kustom_id' => 'required|exists:order_transaksi_kustom,order_kustom_id',
            'file_payment' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
        ]);

        try {
            $existingPayment = AttachmentTransaksiKustom::where('order_kustom_id', $validated['order_kustom_id'])
                ->where('path', 'like', '%payments/kustom%')
                ->first();

            if ($existingPayment) {
                $message = ['type' => 'error', 'text' => 'Bukti pembayaran untuk pesanan ini sudah diunggah sebelumnya.'];
            } elseif (!$request->hasFile('file_payment')) {
                $message = ['type' => 'error', 'text' => 'File pembayaran tidak ditemukan dalam request.'];
            } else {
                $this->storeKustomPayment($request, $validated['order_kustom_id']);
                $message = ['type' => 'success', 'text' => 'Bukti pembayaran kustom berhasil diunggah.'];
            }
        } catch (\Exception $e) {
            $message = ['type' => 'error', 'text' => 'Terjadi kesalahan saat mengunggah file: ' . $e->getMessage()];
        }

        return back()->with($message['type'], $message['text']);
    }

    private function storeKustomPayment(Request $request, int $orderKustomId): void
    {
        $file = $request->file('file_payment');
        $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('payments/kustom', $filename, 'public');

        AttachmentTransaksiKustom::create([
            'order_kustom_id' => $orderKustomId,
            'path' => $path,
        ]);

        $orderKustom = OrderTransaksiKustom::findOrFail($orderKustomId);
        $transaksi = Transaksi::findOrFail($orderKustom->transaksi_id);

        if ($transaksi->status === 'Created') {
            $transaksi->update(['status' => 'Paid']);
        }
    }
}
