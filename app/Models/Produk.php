<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $primaryKey = 'produk_id';

    protected $fillable = [
        'nama_produk',
        'deskripsi',
        'jenis_produk'
    ];

    public function produkTransaksis()
    {
        return $this->hasMany(ProdukTransaksi::class, 'produk_id', 'produk_id');
    }

    public function detailProduks()
    {
        return $this->hasMany(DetailProduk::class, 'produk_id', 'produk_id');
    }

    public function katalog()
    {
        return $this->hasOne(ProdukKatalog::class, 'produk_id', 'produk_id');
    }

    public function kustom()
    {
        return $this->hasOne(ProdukKustom::class, 'produk_id', 'produk_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoProdukKatalog::class, 'produk_id', 'produk_id');
    }

}
