<?php

namespace App\Http\Controllers\User;

use App\Exports\ExportStatistikPenjualan;
use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Services\StatistikPenjualanService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Exports\TransaksiExport;

class StatistikPenjualanController extends Controller
{
    public function index(Request $request, StatistikPenjualanService $statistikService)
    {
        $queryString = json_encode($request->all());
        $cacheKey = 'statistik_penjualan_' . md5($queryString);

        $data = Cache::remember($cacheKey, now()->addMinutes(30), function () use ($request, $statistikService) {
            return $statistikService->getDashboardData($request);
        });

        return view('pages.user.admin.statistik-transaksi.index', $data);
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
