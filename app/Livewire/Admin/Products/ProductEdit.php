<?php

namespace App\Livewire\Admin\Products;

use Livewire\Component;

class ProductEdit extends Component
{
    public $product;

    public $productEdit;

    public $family_id = '';
    public $category_id = '';
    public $subcategory_id = '';

    public function mount($product)
    {
        $this->productEdit = $product->only('sku','name','description','price','subcategory_id');
        $this->subcategory_id = $product->subcategory_id;
        $this->category_id = $product->category_id;
        $this->family_id = $product->family_id;

    }
        public function updatedFamilyId($value)
    {
        $this->category_id = '';
        $this->subcategory_id = '';
        unset($this->categories);
        unset($this->subcategories);
    }

    public function render()
    {
        return view('livewire.admin.products.product-edit');
    }
}
