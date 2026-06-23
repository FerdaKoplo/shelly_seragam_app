<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaksi_id';

    protected $table = 'transaksi';

    protected $fillable = [
        'pegawai_id',
        'checkout_external_id',
        'nama_customer',
        'no_hp_customer',
        'alamat_customer',
        'no_resi_customer',
        'status',
        'tanggal_transaksi',
        'total_harga'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'pegawai_id', 'user_id');
    }

    public function produkTransaksis()
    {
        return $this->hasMany(ProdukTransaksi::class, 'transaksi_id', 'transaksi_id');
    }

    public function pengiriman()
    {
        return $this->hasOne(Pengiriman::class, 'transaksi_id', 'transaksi_id');
    }

    public function orderKustoms()
    {
        return $this->hasMany(OrderTransaksiKustom::class, 'transaksi_id', 'transaksi_id');
    }



    public function paymentInvoice()
    {
        // Points to the PaymentInvoice using checkout_external_id matched against external_id
        return $this->hasOne(PaymentInvoice::class, 'external_id', 'checkout_external_id');
    }

    protected static function booted(): void
    {
        $clearStatistikCache = function () {
            \Illuminate\Support\Facades\Cache::flush();
        };

        static::saved($clearStatistikCache);
        static::deleted($clearStatistikCache);
    }
}
