<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoProdukKatalog extends Model
{
    use HasFactory;

    protected $table = 'foto_produk_katalog';
    protected $fillable = [
        'produk_id',
        'path'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

}
