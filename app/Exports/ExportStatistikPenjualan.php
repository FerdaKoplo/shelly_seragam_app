<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExportStatistikPenjualan implements FromCollection, WithHeadings, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    protected $bulan;
    protected $year;

    public function __construct($bulan = null, $year = null)
    {
        $this->bulan = $bulan;
        $this->year = $year ?? date('Y');
    }
    public function collection()
    {

        try {
            $query = Transaksi::query()->whereYear('tanggal_transaksi', $this->year);
        } catch (\exception $e) {
            return back()->with('error', 'Gagal mendapatkan tanggal transaksi ' . $e->getMessage());
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal_transaksi', $this->bulan);
        }

        return $query->with('user')->get();
    }

    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Nama Customer',
            'Tanggal',
            'Jenis Produk',
            'Total Harga',
            'Status Transaksi',
            'Status Pengiriman',
        ];
    }

    public function map($transaksi): array
    {
        if ($transaksi->produkTransaksis->count() > 0) {
            $jenisProduk = 'Katalog';
        } elseif ($transaksi->orderKustoms->count() > 0) {
            $jenisProduk = 'Kustom';
        } else {
            $jenisProduk = 'Tidak Diketahui';
        }

        $statusPengiriman = $transaksi->pengiriman
            ? $transaksi->pengiriman->status_pengiriman
            : 'Belum Dikirim';
        return [
            $transaksi->transaksi_id,
            $transaksi->nama_customer,
            $transaksi->tanggal_transaksi,
            $jenisProduk,
            $transaksi->total_harga,
            $transaksi->status,
            $statusPengiriman,
        ];
    }
}
