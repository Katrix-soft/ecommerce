<?php

namespace App\Livewire\Forms\Admin\Options;

use Livewire\Form;
use App\Models\Option;

class NewOptionForm extends Form
{
    public $name = '';
    public $type = '1';
    public $features = [];

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'type' => 'required|in:1,2',
            'features' => 'required|array|min:1',
            'features.*.value' => 'required|string|max:255',
            'features.*.description' => 'required|string|max:255',
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

        $this->reset();

        return $option;
    }
}
