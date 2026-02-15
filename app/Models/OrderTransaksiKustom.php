<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderTransaksiKustom extends Model
{
    use HasFactory;

    protected $primaryKey = 'order_kustom_id';

    protected $table = 'order_transaksi_kustom';
    protected $fillable = [
        'transaksi_id',
        'quantity',
        'ukuran_dipilih',
        'tipe_kustom',
        'catatan',
        'detail_pilihan_kustomisasi'
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function attachments()
    {
        return $this->hasMany(AttachmentTransaksiKustom::class, 'order_kustom_id', 'order_kustom_id');
    }

}
