<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukKustom extends Model
{
    use HasFactory;

        protected $table = 'produk_kustom';

    protected $primaryKey = 'kustom_id';

    protected $fillable = [
        'produk_id',
        'spesifikasi_khusus'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id', 'produk_id');
    }

}
