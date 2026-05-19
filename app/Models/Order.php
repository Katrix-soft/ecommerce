<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'shipping_address',
        'payment_method',
        'payment_status',
        'mp_payment_id',
        'status',
        'shipping_cost',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'shipping_address' => 'array',
    ];

    /**
     * Get the user that placed the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items in the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the dispatch ticket for this order.
     */
    public function ticket(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Ticket::class);
    }

    /**
     * Get the shipment for this order.
     */
    public function shipment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Shipment::class);
    }
}
