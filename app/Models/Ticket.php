<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    protected $fillable = [
        'order_id',
        'status',
        'pdf_path',
    ];

    /**
     * Get the order that owns the ticket.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
