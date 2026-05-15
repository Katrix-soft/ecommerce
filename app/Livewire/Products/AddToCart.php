<?php

namespace App\Livewire\Products;

use Livewire\Component;
use Cart;

class AddToCart extends Component
{
    public $product;
    public $quantity;
    public $selectedFeatures = [];

    public function mount()
    {
        $this->quantity = 1;

        foreach ($this->product->options as $option) {
            $this->selectedFeatures[$option->id] = $option->pivot->feature_id[0];
        }
    }

    public function getVariantProperty()
    {
        return \App\Models\Variant::where('product_id', $this->product->id)
            ->whereHas('features', function ($query) {
                $query->whereIn('features.id', $this->selectedFeatures);
            }, '=', count($this->selectedFeatures))
            ->first();
    }

    public function addItem()
    {
        Cart::instance('shopping');
        
        Cart::add([
            'id' => $this->product->id,
            'name' => $this->product->name,
            'qty' => $this->quantity,
            'price' => $this->product->price,
            'options' => [
                'image' => $this->variant ? $this->variant->image : $this->product->image,
                'sku' => $this->variant ? $this->variant->sku : $this->product->sku,
                'features' => \App\Models\Feature::whereIn('id', $this->selectedFeatures)
                    ->with('option')
                    ->get()
                    ->pluck('description', 'option.name')
                    ->toArray()
            ]
        ]);

        if (auth()->check()) {
            Cart::store(auth()->id());
        }

        $this->dispatch('swal', [
            'icon'=>'success',
            'title'=>'Producto agregado',
            'text'=>'Producto agregado al carrito',
        ]);

        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.products.add-to-cart');
    }
}
