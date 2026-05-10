<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;

class Filter extends Component
{
    use WithPagination;


    public $family_id;
    public $options;
    public $selected_features = [];

    public function mount()
    {
        $this->options = \App\Models\Option::whereHas('features.variants.product.subcategory.category', function ($query) {
            $query->where('family_id', $this->family_id);
        })->with([
            'features' => function ($query) {
                $query->whereHas('variants.product.subcategory.category', function ($query) {
                    $query->where('family_id', $this->family_id);
                });
            }
        ])->get()->toArray();
    }

    public function updatedSelectedFeatures()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = \App\Models\Product::whereHas('subcategory.category', function ($query) {
            $query->where('family_id', $this->family_id);
        })
            ->when($this->selected_features, function ($query) {
                $features = \App\Models\Feature::whereIn('id', $this->selected_features)->get();

                foreach ($features->groupBy('option_id') as $featureGroup) {
                    $query->whereHas('variants.features', function ($query) use ($featureGroup) {
                        $query->whereIn('features.id', $featureGroup->pluck('id'));
                    });
                }
            })
            ->paginate(12);

        return view('livewire.filter', compact('products'));
    }

}
