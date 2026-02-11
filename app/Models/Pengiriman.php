<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengiriman extends Model
{
    use HasFactory;


    protected $primaryKey = 'pengiriman_id';

    protected $fillable = [
        'transaksi_id',
        'alamat_pengiriman',
        'alamat_asal',
        'bobot_pengiriman',
        'status_pengiriman',
        'ongkir',
        'estimasi_pengiriman'
    ];


    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

}
