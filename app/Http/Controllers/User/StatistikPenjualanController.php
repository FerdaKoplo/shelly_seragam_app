<?php

namespace App\Http\Controllers\User;

use App\Exports\ExportStatistikPenjualan;
use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransaksiExport;

class StatistikPenjualanController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::query();

        $currentYear = date('Y');
        $query->whereYear('tanggal_transaksi', $currentYear);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_transaksi', $request->bulan);
        }

        $allTransactions = (clone $query)
            ->with(['produkTransaksis', 'orderKustoms', 'pengiriman'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(10)
            ->appends(request()->query());

        $salesQuery = Transaksi::selectRaw('MONTH(tanggal_transaksi) as month, COUNT(*) as total')
            ->whereYear('tanggal_transaksi', $currentYear);

        $salesDataRaw = $salesQuery->groupBy('month')->pluck('total', 'month')->toArray();

        $salesData = [];
        for ($m = 1; $m <= 12; $m++) {
            $salesData[] = $salesDataRaw[$m] ?? 0;
        }

        // --- Bagian Card Statistik (Mengikuti Filter Bulan) ---
        $totalRevenue = (clone $query)->sum('total_harga');

        $totalOrders = (clone $query)->count();

        $totalRegularProducts = (clone $query)
            ->join('produk_transaksi', 'transaksi.transaksi_id', '=', 'produk_transaksi.transaksi_id')
            ->sum('produk_transaksi.quantity');

        $totalCustomOrders = (clone $query)
            ->join('order_transaksi_kustom', 'transaksi.transaksi_id', '=', 'order_transaksi_kustom.transaksi_id')
            ->sum('order_transaksi_kustom.quantity');

        $totalProductSold = $totalRegularProducts + $totalCustomOrders;

        return view('pages.user.admin.statistik-transaksi.index', compact(
            'allTransactions',
            'totalOrders',
            'totalRegularProducts',
            'totalCustomOrders',
            'totalProductSold',
            'salesData',
            'totalRevenue'
        ));
    }

    public function export(Request $request)
    {
        $bulan = $request->bulan;
        $year = date('Y');

        $query = Transaksi::whereYear('tanggal_transaksi', $year);

        if ($bulan) {
            $query->whereMonth('tanggal_transaksi', $bulan);
        }

        if ($query->count() === 0) {
            return back()->with('error', 'Tidak Ada Data Untuk Diexport');
        }

        return Excel::download(new ExportStatistikPenjualan($bulan, $year), 'laporan-transaksi.xlsx');
    }
}
