<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Shipment;
use App\Models\Driver;

class ShipmentsIndex extends Component
{
    use WithPagination;

    public $statusFilter = '';
    public $driverFilter = '';

    protected $queryString = [
        'statusFilter' => ['except' => ''],
        'driverFilter' => ['except' => ''],
    ];

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingDriverFilter()
    {
        $this->resetPage();
    }

    /**
     * Mark shipment as delivered
     */
    public function markAsDelivered($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $shipment->status = 'delivered';
        $shipment->delivered_at = now();
        $shipment->save();

        // Update corresponding order
        $order = $shipment->order;
        if ($order) {
            $order->status = 'delivered';
            $order->save();

            // Update corresponding ticket
            $ticket = $order->ticket;
            if ($ticket) {
                $ticket->status = 'delivered';
                $ticket->save();
            }
        }

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Pedido Entregado!',
            'text' => 'El envío y el pedido han sido marcados como entregados.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    /**
     * Mark shipment as failed
     */
    public function markAsFailed($shipmentId)
    {
        $shipment = Shipment::findOrFail($shipmentId);
        $shipment->status = 'failed';
        $shipment->save();

        // Update corresponding order/ticket if needed
        $this->dispatch('swal', [
            'icon' => 'warning',
            'title' => 'Entrega Fallida',
            'text' => 'El envío ha sido marcado como fallido/no entregado.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function render()
    {
        $shipmentsQuery = Shipment::with(['order.user', 'driver'])
            ->orderBy('created_at', 'desc');

        if ($this->statusFilter) {
            $shipmentsQuery->where('status', $this->statusFilter);
        }

        if ($this->driverFilter) {
            $shipmentsQuery->where('driver_id', $this->driverFilter);
        }

        $shipments = $shipmentsQuery->paginate(10);
        $drivers = Driver::orderBy('name', 'asc')->get();

        return view('livewire.admin.shipments-index', [
            'shipments' => $shipments,
            'drivers' => $drivers,
        ])->layout('layouts.admin', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
                ['name' => 'Envíos'],
            ],
        ]);
    }
}
