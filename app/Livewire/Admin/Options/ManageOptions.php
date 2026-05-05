<?php

namespace App\Livewire\Admin\Options;

use Livewire\Component;
use App\Models\Option;
use App\Livewire\Forms\Admin\Options\NewOptionForm;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ManageOptions extends Component
{
    use WithPagination;

    #[On('featureAdded')]
    public function refresh() {}

    public NewOptionForm $form;

    public $editing = null;
    public $openModal = false;

    // Properties for editing
    public $name = '';
    public $type = '1';

    // Temporal fields for adding features to the form
    public $value = '';
    public $description = '';

    public function create()
    {
        $this->form->reset();
        $this->reset(['editing', 'value', 'description']);
        $this->openModal = true;
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

        $this->form->features[] = [
            'value' => $this->value,
            'description' => $this->description,
        ];

        $this->reset(['value', 'description']);
    }

    public function removeFeature($index)
    {
        unset($this->form->features[$index]);
        $this->form->features = array_values($this->form->features);
    }

    public function save()
    {
        $this->form->store();

        $this->openModal = false;
        
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Bien hecho',
            'text' => 'Opción creada correctamente',
        ]);
    }

    public function edit($optionId)
    {
        $option = Option::findOrFail($optionId);
        $this->editing = $optionId;
        $this->name = $option->name;
        $this->type = $option->type;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2',
        ]);

        $option = Option::findOrFail($this->editing);
        $option->update([
            'name' => $this->name,
            'type' => $this->type,
        ]);

        $this->editing = null;

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Bien hecho',
            'text' => 'Opción actualizada correctamente',
        ]);
    }

    public function cancelEdit()
    {
        $this->editing = null;
        $this->reset(['name', 'type']);
    }

    public function delete($optionId)
    {
        $option = Option::findOrFail($optionId);
        $option->delete();

        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Bien hecho',
            'text' => 'Opción eliminada correctamente',
        ]);
    }

    public function render()
    {
        $options = Option::with('features')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.options.manage-options', compact('options'));
    }
}
