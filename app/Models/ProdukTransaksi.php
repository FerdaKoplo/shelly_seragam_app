<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukTransaksi extends Model
{
    use HasFactory;

    protected $primaryKey = 'produk_transaksi_id';

    protected $fillable = [
        'transaksi_id',
        'produk_id',
        'quantity',
        'size',
        'subtotal'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }
}
