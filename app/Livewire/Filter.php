<?php

namespace App\Livewire;

use App\Models\Option;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class Filter extends Component
{
    use WithPagination;


    public $family_id;
    public $category_id;
    public $subcategory_id;
    public $options;
    public $selected_features = [];
    public $orderBy = 1;
    public $search;
    

    public function mount()
    {
        $this->options = Option::query()
            ->when($this->subcategory_id, fn($q) => $q->verifySubcategory($this->subcategory_id))
            ->when($this->category_id, fn($q) => $q->verifyCategory($this->category_id))
            ->when($this->family_id, fn($q) => $q->verifyFamily($this->family_id))
            ->with([
                'features' => function ($query) {
                    $query->when($this->subcategory_id, fn($q) => $q->verifySubcategory($this->subcategory_id))
                          ->when($this->category_id, fn($q) => $q->verifyCategory($this->category_id))
                          ->when($this->family_id, fn($q) => $q->verifyFamily($this->family_id));
                }
            ])
            ->get()->toArray();
    }

    #[On('search')]
    public function search($search)
    {
        $this->search = $search;
        $this->resetPage();
    }

    public function updatedSelectedFeatures()
    {
        $this->resetPage();
    }

    public function render()
    {
        $products = \App\Models\Product::query()
            ->when($this->category_id, function ($query) {
                $query->whereHas('subcategory', fn($q) => $q->where('category_id', $this->category_id));
            })
            ->when(!$this->category_id && $this->family_id, function ($query) {
                $query->whereHas('subcategory.category', fn($q) => $q->where('family_id', $this->family_id));
            })
            ->when($this->subcategory_id, function ($query) {
                $query->where('subcategory_id', $this->subcategory_id);
            })
            ->when($this->orderBy == 1, function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($this->orderBy == 2, function ($query) {
                $query->orderBy('price', 'desc');
            })
            ->when($this->orderBy == 3, function ($query) {
                $query->orderBy('price', 'asc');
            })
            ->when($this->selected_features, function ($query) {
                $features = \App\Models\Feature::whereIn('id', $this->selected_features)->get();

                $query->whereHas('variants', function ($query) use ($features) {
                    foreach ($features->groupBy('option_id') as $featureGroup) {
                        $query->whereHas('features', function ($query) use ($featureGroup) {
                            $query->whereIn('features.id', $featureGroup->pluck('id'));
                        });
                    }
                });
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->paginate(12);

        return view('livewire.filter', compact('products'));
    }

}
