<?php

namespace App\Livewire;

use Livewire\Component;

class Filter extends Component
{


    public $family_id;
    public $options;

    public function mount()
    {
        $this->options = \App\Models\Option::whereHas('products.subcategory.category', function($query) {
            $query->where('family_id', $this->family_id);
        })->with([
            'features' => function($query) {
                $query->whereHas('variants.product.subcategory.category', function($query) {
                    $query->where('family_id', $this->family_id);
                });
            }
        ])->get();
    }

    public function render()
    {
        $products = \App\Models\Product::whereHas('subcategory.category', function($query) {
            $query->where('family_id', $this->family_id);
        })->paginate(12);

        return view('livewire.filter', compact('products'));
    }

}
