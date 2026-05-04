<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Family;
use App\Models\Category;
use Livewire\Component;
use Livewire\Attributes\Computed;

class SubcategoryCreate extends Component
{

    public $families;

    public $subcategories = [
        'family_id' => '',
        'category_id' => '',
        'name' => ''
    ];

    public function mount()
    {
        $this->families = Family::all();
    }

    public function updatedSubcategoriesFamilyId()
    {
        $this->subcategories['category_id'] = '';
    }

    #[Computed () ]
    public function categories()
    {

        return Category::where('family_id', $this->subcategories['family_id'])->get();
    }
    public function save()
    {
        $this->validate([
            'subcategories.family_id' => 'required|exists:families,id',
            'subcategories.category_id' => 'required|exists:categories,id',
            'subcategories.name' => 'required',
        ],[], [
            'subcategories.family_id' => 'familia',
            'subcategories.category_id' => 'categoria',
            'subcategories.name' => 'nombre',
        ]);

        Subcategory::create($this->subcategories);
        session()->flash('swal',[
            'icon' => 'success',
            'title' => 'Subcategoria creada',
            'text' => 'Subcategoria creada exitosamente',
        ]);

        return redirect()->route('admin.subcategories.index');
    }
    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-create');
    }
}
