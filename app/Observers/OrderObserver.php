<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Ticket;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        Ticket::create([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);
    }
}
