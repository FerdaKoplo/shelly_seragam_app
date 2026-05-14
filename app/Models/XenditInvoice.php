<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class XenditInvoice extends Model
{
    protected $table = 'xendit_invoices';

    protected $fillable = [
        'external_id',
        'invoice_id',
        'status',
        'amount',
        'invoice_url',
        'expiry_date',
        'paid_at',
        'raw_payload',
    ];

    protected $casts = [
        'amount' => 'integer',
        'expiry_date' => 'datetime',
        'paid_at' => 'datetime',
        'raw_payload' => 'array',
    ];
}

