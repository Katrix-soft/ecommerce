<?php
 
namespace App\Livewire\Products;
 
use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Attributes\Computed;
 
class AddToCart extends Component
{
    public $product;
    public $variant;
    public $quantity;
    public $selectedFeatures = [];
    public $stock;
 
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

        $this->getVariant();
    }

    public function updated($name)
    {
        if (str_starts_with($name, 'selectedFeatures')) {
            $this->getVariant();
        }
    }
 

    public function getVariant()
    {
        $this->variant = \App\Models\Variant::where('product_id', $this->product->id)
            ->whereHas('features', function ($query) {
                $query->whereIn('features.id', $this->selectedFeatures);
            }, '=', count($this->selectedFeatures))
            ->first();

        $this->stock = $this->variant ? $this->variant->stock : 0;

        // Al cambiar de variante, reiniciar la cantidad a 1 (o 0 si no hay stock)
        $this->quantity = $this->stock > 0 ? 1 : 0;
    }
 
    public function addItem()
    {
        Cart::instance('shopping');
        
        $variant = $this->variant;
        $itemId = $variant ? $variant->id : $this->product->id;

        // 1. Obtener el stock actualizado directamente de la base de datos para evitar compras concurrentes duplicadas
        if ($variant) {
            $dbVariant = \App\Models\Variant::find($variant->id);
            $currentDbStock = $dbVariant ? $dbVariant->stock : 0;
            $this->stock = $currentDbStock;
        } else {
            $dbProduct = \App\Models\Product::find($this->product->id);
            $currentDbStock = $dbProduct ? ($dbProduct->stock ?? 100) : 0;
            $this->stock = $currentDbStock;
        }

        // 2. Validar si hay stock disponible en general
        if ($currentDbStock <= 0 || $this->quantity <= 0) {
            $this->quantity = 0;
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No hay suficiente stock',
                'text' => 'No hay suficiente stock para agregar la cantidad seleccionada',
            ]);
            return;
        }

        // 3. Obtener la cantidad de este ítem que ya está en el carrito
        $cartItem = Cart::content()->firstWhere('id', $itemId);
        $qtyInCart = $cartItem ? $cartItem->qty : 0;

        // 4. Validar que la cantidad acumulada no supere el stock real de la base de datos
        if (($qtyInCart + $this->quantity) > $currentDbStock) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No hay suficiente stock',
                'text' => 'No hay suficiente stock para agregar la cantidad seleccionada',
            ]);
            return;
        }

        Cart::add([
            'id' => $itemId,
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
