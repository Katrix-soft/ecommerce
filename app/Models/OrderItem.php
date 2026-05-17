<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'variant_id',
        'name',
        'quantity',
        'price',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    /**
     * Get the order that contains this item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the variant purchased.
     */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(Variant::class);
    }
}
