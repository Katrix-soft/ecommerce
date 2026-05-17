<?php
 
namespace App\Livewire\Products;
 
use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Attributes\Computed;
 
class AddToCart extends Component
{
    public $product;
    public $quantity;
    public $selectedFeatures = [];
 
    public function mount()
    {
        $this->quantity = 1;
 
        foreach ($this->product->options as $option) {
            $featureData = $option->pivot->feature_id;
            if (is_array($featureData) && count($featureData) > 0) {
                $first = $featureData[0];
                $this->selectedFeatures[$option->id] = is_array($first) ? ($first['id'] ?? null) : $first;
            }
        }
    }
 
    #[Computed()]
    public function variant()
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
        
        $variant = $this->variant;
 
        Cart::add([
            'id' => $variant ? $variant->id : $this->product->id,
            'name' => $this->product->name,
            'qty' => $this->quantity,
            'price' => $variant ? ($variant->price ?? $this->product->price) : $this->product->price,
            'options' => [
                'image' => $variant ? $variant->image : $this->product->image,
                'sku' => $variant ? $variant->sku : $this->product->sku,
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
 
        $this->dispatch('cartUpdated');
 
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Producto agregado',
            'text' => 'Producto agregado al carrito',
        ]);
 
        $this->dispatch('cart-updated');
    }
 
    public function render()
    {
        return view('livewire.products.add-to-cart');
    }
}
