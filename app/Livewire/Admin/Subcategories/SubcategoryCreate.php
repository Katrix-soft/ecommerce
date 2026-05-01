<?php

namespace App\Livewire\Admin\Subcategories;

use App\Models\Family;
use Livewire\Component;

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

    public function render()
    {
        return view('livewire.admin.subcategories.subcategory-create');
    }
}
