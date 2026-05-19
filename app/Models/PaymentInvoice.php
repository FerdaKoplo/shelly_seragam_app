<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentInvoice extends Model
{
    protected $table = 'payment_invoices';

    protected $fillable = [
        'provider',
        'checkout_order_id',
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
        'checkout_order_id' => 'integer',
        'amount' => 'integer',
        'expiry_date' => 'datetime',
        'paid_at' => 'datetime',
        'raw_payload' => 'array',
    ];
}

