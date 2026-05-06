<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;


use App\Models\Option;
use App\Models\Feature;
use Livewire\Attributes\Computed;

class ProductVariants extends Component
{
    public $product;
    public $variant = [
        'option_id' => '',
        'features' => [
            [
                'id' => '',
                'value' => '',
                'description' => ''
            ]
        ]
    ];
    
    public $showModal = false;

    public function create()
    {
        $this->showModal = true;
    }

    public function addFeature()
    {
        $this->variant['features'][] = [
            'id' => '',
            'value' => '',
            'description' => ''
        ];
    }

    public function removeFeature($index)
    {
        unset($this->variant['features'][$index]);
        $this->variant['features'] = array_values($this->variant['features']); // Re-index array
    }



    #[Computed()]
    public function options()
    {
        return Option::all();
    }

    #[Computed()]
    public function features()
    {
        return Feature::where('option_id', $this->variant['option_id'])->get();
    }

    public function updated($property, $value)
    {
        // El formato de $property es "variant.features.0.id"
        if (str_starts_with($property, 'variant.features.') && str_ends_with($property, '.id')) {
            $parts = explode('.', $property); // [variant, features, 0, id]
            $index = $parts[2];
            
            $feature = Feature::find($value);
            if ($feature) {
                $this->variant['features'][$index]['value'] = $feature->value;
                $this->variant['features'][$index]['description'] = $feature->description;
            }
        }
    }

    public function deleteOption($optionId)
    {
        $this->product->options()->detach($optionId);
        $this->product->load('options'); // Recargar las opciones para refrescar la UI
    }

    public function render()
    {
        return view('livewire.admin.products.product-variants');
    }

    public function save()
    {
        $this->validate([
            'variant.option_id' => 'required',
            'variant.features.*.id' => 'required',
            'variant.features.*.value' => 'required',
            'variant.features.*.description' => 'required',
        ]);

        $this->product->options()->attach($this->variant['option_id'], [
            'feature_id' => $this->variant['features']
        ]);

        $this->reset(['variant', 'showModal']);
        
        $this->dispatch('variant-saved'); // Opcional: para cerrar modal o mostrar mensaje
    }
}
