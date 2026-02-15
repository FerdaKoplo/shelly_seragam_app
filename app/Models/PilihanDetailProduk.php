<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilihanDetailProduk extends Model
{
    use HasFactory;

    protected $table = 'pilihan_detail_produk';

    protected $primaryKey = 'pilihan_detail_id';

    protected $fillable = [
        'detail_produk_id',
        'opsi',
        'pengaruh_harga'
    ];
    protected $casts = [
        'opsi' => 'array',
    ];
    public function detailProduk()
    {
        return $this->belongsTo(DetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }

}
