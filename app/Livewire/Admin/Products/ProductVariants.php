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
        if (!$this->product || !$this->product->subcategory_id) {
            return collect();
        }

        $subcategoryId = $this->product->subcategory_id;
        $categoryId = $this->product->subcategory->category_id;

        return Option::where(function($query) use ($subcategoryId, $categoryId) {
            $query->whereHas('subcategories', function($q) use ($subcategoryId) {
                $q->where('subcategories.id', $subcategoryId);
            })
            ->orWhereHas('categories', function($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            })
            ->orWhere(function($q) {
                $q->doesntHave('categories')->doesntHave('subcategories');
            });
        })->get();
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
    public function generarCombinaciones($arrays)
    {
        $resultado = [[]];

        foreach ($arrays as $array) {
            $nuevaCombinacion = [];
            foreach ($resultado as $combinacion) {
                foreach ($array as $valor) {
                    // Extraemos solo el ID si el valor es un array/objeto de feature
                    $id = is_array($valor) ? $valor['id'] : $valor;
                    $nuevaCombinacion[] = array_merge($combinacion, [$id]);
                }
            }
            $resultado = $nuevaCombinacion;
        }

        return $resultado;
    }
    public function generarVariantes()
    {
        // 1. Borrar variantes existentes
        $this->product->variants()->delete();

        // 2. Obtener las características de cada opción
        $features = $this->product->options->pluck('pivot.feature_id');

        // 3. Generar combinaciones de IDs
        $combinaciones = $this->generarCombinaciones($features);

        // 4. Crear variantes y asociar características
        foreach ($combinaciones as $combinacion) {
            $variant = \App\Models\Variant::create([
                'product_id' => $this->product->id,
            ]);

            // attach espera un array de IDs
            $variant->features()->attach($combinacion);
        }

        $this->dispatch('variant-generated');
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

        $this->generarVariantes(); // Generar automáticamente al guardar una opción

        $this->reset(['variant', 'showModal']);
        
        $this->dispatch('variant-saved'); // Opcional: para cerrar modal o mostrar mensaje
    }
}
