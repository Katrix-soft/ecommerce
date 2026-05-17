<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Driver;

class DriversIndex extends Component
{
    use WithPagination;

    public $search = '';
    
    // Modal states
    public $showModal = false;
    public $isEditMode = false;
    
    // Form fields
    public $driverId = null;
    public $name = '';
    public $dni = '';
    public $phone = '';
    public $license = '';
    public $is_active = true;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->isEditMode = false;
        $this->showModal = true;
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $driver = Driver::findOrFail($id);
        $this->driverId = $driver->id;
        $this->name = $driver->name;
        $this->dni = $driver->dni;
        $this->phone = $driver->phone;
        $this->license = $driver->license;
        $this->is_active = $driver->is_active;
        
        $this->isEditMode = true;
        $this->showModal = true;
    }

    public function resetForm()
    {
        $this->driverId = null;
        $this->name = '';
        $this->dni = '';
        $this->phone = '';
        $this->license = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function saveDriver()
    {
        $rules = [
            'name' => 'required|string|min:3|max:255',
            'dni' => 'required|string|unique:drivers,dni,' . ($this->driverId ?? 'NULL') . ',id',
            'phone' => 'required|string|min:6',
            'license' => 'required|string',
            'is_active' => 'boolean',
        ];

        $messages = [
            'name.required' => 'El nombre es obligatorio.',
            'name.min' => 'El nombre debe tener al menos 3 caracteres.',
            'dni.required' => 'El DNI es obligatorio.',
            'dni.unique' => 'Ya existe un conductor registrado con este DNI.',
            'phone.required' => 'El teléfono es obligatorio.',
            'license.required' => 'La licencia/patente es obligatoria.',
        ];

        $this->validate($rules, $messages);

        if ($this->isEditMode) {
            $driver = Driver::findOrFail($this->driverId);
            $driver->update([
                'name' => $this->name,
                'dni' => $this->dni,
                'phone' => $this->phone,
                'license' => $this->license,
                'is_active' => $this->is_active,
            ]);

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Conductor Actualizado!',
                'text' => 'El conductor ha sido editado exitosamente.',
                'confirmButtonColor' => '#7c3aed',
            ]);
        } else {
            Driver::create([
                'name' => $this->name,
                'dni' => $this->dni,
                'phone' => $this->phone,
                'license' => $this->license,
                'is_active' => $this->is_active,
            ]);

            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => '¡Conductor Registrado!',
                'text' => 'El conductor ha sido agregado exitosamente.',
                'confirmButtonColor' => '#7c3aed',
            ]);
        }

        $this->closeModal();
    }

    public function toggleStatus($id)
    {
        $driver = Driver::findOrFail($id);
        $driver->is_active = !$driver->is_active;
        $driver->save();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Estado Modificado',
            'text' => 'El estado del conductor ha sido modificado.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function deleteDriver($id)
    {
        $driver = Driver::findOrFail($id);
        
        // Check if driver has active/in transit shipments
        $hasActiveShipments = $driver->shipments()->where('status', 'in_transit')->exists();
        if ($hasActiveShipments) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No se puede eliminar',
                'text' => 'Este conductor tiene envíos en tránsito actualmente.',
                'confirmButtonColor' => '#7c3aed',
            ]);
            return;
        }

        $driver->delete();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => '¡Conductor Eliminado!',
            'text' => 'El conductor ha sido removido.',
            'confirmButtonColor' => '#7c3aed',
        ]);
    }

    public function render()
    {
        $driversQuery = Driver::orderBy('name', 'asc');

        if ($this->search) {
            $driversQuery->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('dni', 'like', '%' . $this->search . '%')
                    ->orWhere('license', 'like', '%' . $this->search . '%');
            });
        }

        $drivers = $driversQuery->paginate(10);

        return view('livewire.admin.drivers-index', [
            'drivers' => $drivers,
        ])->layout('layouts.admin', [
            'breadcrumbs' => [
                ['name' => 'Dashboard', 'route' => route('admin.dashboard')],
                ['name' => 'Conductores'],
            ],
        ]);
    }
}
