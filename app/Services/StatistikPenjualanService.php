<?php

namespace App\Services;

use App\Models\Transaksi;
use Illuminate\Http\Request;

class StatistikPenjualanService
{
    public function getDashboardData(Request $request): array
    {
        $currentYear = date('Y');
        $query = Transaksi::query()->whereYear('tanggal_transaksi', $currentYear);

        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_transaksi', $request->bulan);
        }

        $allTransactions = (clone $query)
            ->with(['produkTransaksis', 'orderKustoms', 'pengiriman'])
            ->orderBy('tanggal_transaksi', 'desc')
            ->paginate(10)
            ->appends($request->query());

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

        return [
            'allTransactions' => $allTransactions,
            'totalOrders' => $totalOrders,
            'totalRegularProducts' => $totalRegularProducts,
            'totalCustomOrders' => $totalCustomOrders,
            'totalProductSold' => $totalProductSold,
            'salesData' => $salesData,
            'totalRevenue' => $totalRevenue,
        ];
    }
}