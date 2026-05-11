<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Category;
use App\Models\Family;
use App\Models\Subcategory;
use Livewire\Component;
use Livewire\Attributes\Computed;

class SubcategoryCreate extends Component
{

    public $families;
    public $family_id = '';
    public $category_id = '';
    public $name = '';



    public function mount()
    {
        $this->families = Family::all();
    }

    public function updatedFamilyId($value)
    {
        $this->category_id = '';
        unset($this->categories);
    }

    #[Computed]
    public function categories(){
        return Category::where('family_id', $this->family_id)->get();
    }

    public function save()
    {
        try {
            $this->validate([
                'family_id' => 'required|exists:families,id',
                'category_id' => 'required|exists:categories,id',
                'name' => 'required|string|max:255',
            ], [], [
                'family_id' => 'familia',
                'category_id' => 'categoría',
                'name' => 'nombre',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'El formulario contiene errores',
            ]);
            throw $e;
        }

        Subcategory::create([
            'category_id' => $this->category_id,
            'name' => $this->name,
        ]);

        $this->reset(['family_id', 'category_id', 'name']);
        $this->dispatch('alert', 'Subcategoría creada exitosamente');
    }

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-create');
    }
}
