<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CheckoutOrder extends Model
{
    protected $table = 'checkout_orders';

    protected $fillable = [
        'external_id',
        'status',
        'type',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'city',
        'province',
        'postal_code',
        'destination_id',
        'shipping_id',
        'shipping_cost',
        'subtotal',
        'total',
        'items',
        'notes',
        'paid_at',
    ];

    protected $casts = [
        'destination_id' => 'integer',
        'shipping_cost' => 'integer',
        'subtotal' => 'integer',
        'total' => 'integer',
        'items' => 'array',
        'paid_at' => 'datetime',
    ];
}

