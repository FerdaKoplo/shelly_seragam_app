<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKatalog extends Model
{
    use HasFactory;

    protected $table = 'produk_katalog';

    protected $primaryKey = 'katalog_id';

    protected $fillable = [
        'produk_id',
        'kategori',
        'harga',
        'stok',
        'status'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoProdukKatalog::class, 'produk_id', 'produk_id');
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class, 'katalog_id', 'katalog_id');
    }

    protected static function booted()
    {
        static::saving(function ($katalog) {
            if ($katalog->status !== 'Arsip') {
                $katalog->status = $katalog->stok <= 0 ? 'Pre-Order' : 'Tersedia';
            }
        });
    }

}
