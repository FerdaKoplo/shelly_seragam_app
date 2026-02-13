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
        'stok'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

    public function fotos()
    {
        return $this->hasMany(FotoProdukKatalog::class, 'produk_id', 'produk_id');
    }

}
