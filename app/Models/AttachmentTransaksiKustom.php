<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentTransaksiKustom extends Model
{
    use HasFactory;


    protected $primaryKey = 'attachment_id';

    protected $fillable = [
        'order_kustom_id',
        'path'
    ];

    public function orderKustom()
    {
        return $this->belongsTo(OrderTransaksiKustom::class, 'order_kustom_id', 'order_kustom_id');
    }

}
