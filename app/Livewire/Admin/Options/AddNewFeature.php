<?php

namespace App\Livewire\Admin\Options;

use Livewire\Component;
use App\Models\Option;
use App\Models\Feature;

class AddNewFeature extends Component
{
    public $option;
    public $value = '';
    public $description = '';

    public function mount(Option $option)
    {
        $this->option = $option;
    }

    public function addFeature()
    {
        $this->validate([
            'value' => 'required|string|max:255',
            'description' => 'required|string|max:255',
        ], [], [
            'value' => 'valor',
            'description' => 'descripción',
        ]);

        $this->option->features()->create([
            'value' => $this->value,
            'description' => $this->description,
        ]);

        $this->reset(['value', 'description']);
        
        // Refresh the parent component
        $this->dispatch('featureAdded');
        
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Agregado',
            'text' => 'Valor agregado correctamente',
        ]);
    }

    public function render()
    {
        return view('livewire.admin.options.add-new-feature');
    }
}
