<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    public const JENIS_VOUCHER = [
        'nominal',
        'persentase'
    ];
    protected $fillable = [
        'katalog_id',
        'nama_voucher',
        'kode_voucher',
        'deskripsi',
        'nilai_diskon',
        'jenis_voucher',
        'tanggal_mulai',
        'tanggal_berakhir',
        'status',
    ];

    public function katalog()
    {
        return $this->belongsTo(ProdukKatalog::class, 'katalog_id', 'produk_id');
    }
}
