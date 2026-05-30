<?php

namespace App\Livewire\Forms\Admin\Options;

use Livewire\Form;
use App\Models\Option;

class NewOptionForm extends Form
{
    public $name = '';
    public $type = '1';
    public $features = [];
    public $category_ids = [];
    public $subcategory_ids = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2',
            'features' => 'required|array|min:1',
            'features.*.value' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:255',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'subcategory_ids' => 'nullable|array',
            'subcategory_ids.*' => 'exists:subcategories,id',
        ];
    }

    public function validationAttributes()
    {
        return [
            'name' => 'nombre',
            'type' => 'tipo',
            'features' => 'valores',
            'features.*.value' => 'valor',
            'features.*.description' => 'descripción',
            'category_ids' => 'categorías',
            'subcategory_ids' => 'subcategorías',
        ];
    }

    public function store()
    {
        $this->validate();

        $option = Option::create([
            'name' => $this->name,
            'type' => $this->type,
        ]);

        foreach ($this->features as $feature) {
            $option->features()->create([
                'value' => $feature['value'],
                'description' => $feature['description'],
            ]);
        }

        if (!empty($this->category_ids)) {
            $option->categories()->attach($this->category_ids);
        }
        if (!empty($this->subcategory_ids)) {
            $option->subcategories()->attach($this->subcategory_ids);
        }

        $this->reset();

        return $option;
    }
}