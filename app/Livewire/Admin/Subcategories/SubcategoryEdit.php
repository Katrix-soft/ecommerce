<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Category;
use App\Models\Family;
use App\Models\Subcategory;
use Livewire\Component;
use Livewire\Attributes\Computed;

class SubcategoryEdit extends Component
{

    public $families;
    public $family_id = '';
    public $category_id = '';
    public $name = '';

    public $subcategory;

    public function mount(Subcategory $subcategory)
    {
        $this->subcategory = $subcategory;
        $this->families = Family::all();

        $this->family_id = $subcategory->category->family_id;
        $this->category_id = $subcategory->category_id;
        $this->name = $subcategory->name;
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
       $this->validate([
        'family_id' => 'required|exists:families,id',
        'category_id' => 'required|exists:categories,id',
        'name' => 'required|string|max:255',
       ],[],[
        'family_id' => 'familia',
        'category_id' => 'categoria',
        'name' => 'nombre',
       ]);

       $this->subcategory->update([
        'category_id' => $this->category_id,
        'name' => $this->name,
       ]);

       $this->dispatch('alert', 'Subcategoria actualizada exitosamente');

       return redirect()->route('admin.subcategories.index');
    }

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-edit');
    }
}
