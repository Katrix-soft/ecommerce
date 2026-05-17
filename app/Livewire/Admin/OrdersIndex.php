<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Order;
use App\Models\Ticket;
use App\Models\Driver;
use App\Models\Shipment;

class OrdersIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    
    // Detail Modal state
    public $selectedOrder = null;
    public $showModal = false;
    
    // Shipment creation state
    public $showShipmentForm = false;
    public $selectedDriverId = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function viewOrder($orderId)
    {
        $this->selectedOrder = Order::with(['user', 'items', 'ticket', 'shipment.driver'])->find($orderId);
        $this->showModal = true;
        $this->showShipmentForm = false;
        $this->selectedDriverId = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedOrder = null;
    }

    /**
     * Change ticket status to "ready_to_ship"
     */
    public function markAsReady($orderId)
    {
        $order = Order::find($orderId);
        if ($order) {
            $order->status = 'processing';
            $order->save();

            $ticket = $order->ticket;
            if (!$ticket) {
                $ticket = Ticket::create([
                    'order_id' => $order->id,
                    'status' => 'pending',
                ]);
            }
            $ticket->status = 'ready_to_ship';
            $ticket->save();

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Pedido Preparado!',
                'text' => 'El ticket ha sido marcado como listo para despachar.',
                'confirmButtonColor' => '#7c3aed',
            ]);

            if ($this->selectedOrder && $this->selectedOrder->id == $orderId) {
                $this->viewOrder($orderId);
            }
        }
    }

    /**
     * Open shipment form
     */
    public function openShipmentForm()
    {
        $this->showShipmentForm = true;
    }

    /**
     * Generate shipment and assign to driver
     */
    public function generateShipment()
    {
        $this->validate([
            'selectedDriverId' => 'required|exists:drivers,id',
        ], [
            'selectedDriverId.required' => 'Debes seleccionar un conductor.',
            'selectedDriverId.exists' => 'El conductor seleccionado no es válido.',
        ]);

        if (!$this->selectedOrder) return;

        $order = Order::find($this->selectedOrder->id);
        
        // Update Order status
        $order->status = 'shipped';
        $order->save();

        // Update Ticket status
        $ticket = $order->ticket;
        if ($ticket) {
            $ticket->status = 'dispatched';
            $ticket->save();
        }

        // Create Shipment
        Shipment::create([
            'order_id' => $order->id,
            'driver_id' => $this->selectedDriverId,
            'status' => 'in_transit',
            'shipped_at' => now(),
        ]);

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Envío Generado!',
            'text' => 'El pedido está en tránsito y asignado al conductor.',
            'confirmButtonColor' => '#7c3aed',
        ]);

        $this->viewOrder($order->id);
    }

    public function render()
    {
        $ordersQuery = Order::with(['user', 'ticket', 'shipment'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $ordersQuery->where(function ($query) {
                $query->where('id', 'like', '%' . $this->search . '%')
                    ->orWhereHas('user', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('email', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->statusFilter) {
            $ordersQuery->where('status', $this->statusFilter);
        }

        $orders = $ordersQuery->paginate(10);
        $drivers = Driver::where('is_active', true)->get();

        return view('livewire.admin.orders-index', [
            'orders' => $orders,
            'drivers' => $drivers,
        ])->layout('layouts.admin', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
                ['name' => 'Órdenes'],
            ],
        ]);
    }
}
