<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller
{
    /**
     * Display a printable dispatch ticket for the given order.
     */
    public function print(Order $order)
    {
        $order->load(['user', 'items', 'ticket', 'shipment.driver']);
        
        return view('admin.orders.print', [
            'order' => $order,
        ]);
    }
}
