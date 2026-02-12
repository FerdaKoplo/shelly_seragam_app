<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailProduk extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_produk_id';

    protected $fillable = [
        'produk_id',
        'nama_detail',
        'deskripsi_detail'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function pilihanDetails()
    {
        return $this->hasMany(PilihanDetailProduk::class, 'detail_produk_id', 'detail_produk_id');
    }

}
