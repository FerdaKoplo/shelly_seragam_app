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

        $allTransactions = $query->with(['produkTransaksis', 'orderKustoms', 'pengiriman'])->paginate(10)->appends(request()->except('page'));

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_transaksi', $request->bulan);
        }

        $currentYear = date('Y');
        $query->whereYear('tanggal_transaksi', $currentYear);

        $salesQuery = Transaksi::selectRaw('MONTH(tanggal_transaksi) as month, COUNT(*) as total')
            ->whereYear('tanggal_transaksi', $currentYear);

        $salesDataRaw = $salesQuery->groupBy('month')->pluck('total', 'month')->toArray();

        $salesData = [];
        for ($m = 1; $m <= 12; $m++) {
            $salesData[] = $salesDataRaw[$m] ?? 0;
        }

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
        return Excel::download(new ExportStatistikPenjualan($bulan, $year), 'laporan-transaksi.xlsx');
    }
}
