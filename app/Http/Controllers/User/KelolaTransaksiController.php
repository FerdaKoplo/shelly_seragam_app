<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
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

        $query = Transaksi::with(['produkTransaksis.produk', 'orderKustoms.attachments', 'pengiriman', 'user']);

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

        Transaksi::create($validated);

        return redirect()->route('manage.transaksi')->with('success', 'Transaksi berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::findOrFail($id);

        $validated = $request->validate([
            'no_resi_customer' => 'nullable|string|max:255',
            'status' => 'nullable|in:Created,Paid,Delivered,Done',
        ]);

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

    public function getOngkir(Request $request)
    {
        $request->validate([
            'destination' => 'required',
            'weight' => 'required|numeric',
            'courier' => 'required'
        ]);

        $costs = $this->rajaOngkir->getCost(444, $request->destination, $request->weight, $request->courier);

        return response()->json($costs);
    }
}
