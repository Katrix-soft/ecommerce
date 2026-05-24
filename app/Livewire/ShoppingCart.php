<?php

namespace App\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCart extends Component
{
    public function increaseQty($rowId)
    {
        $item = Cart::instance('shopping')->get($rowId);
        
        // 1. Obtener el stock real de la variante
        $variant = \App\Models\Variant::find($item->id);
        $stock = $variant ? $variant->stock : 0;

        // 2. Validar que no se supere el stock disponible
        if ($item->qty >= $stock) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No hay suficiente stock',
                'text' => 'No hay suficiente stock para agregar la cantidad seleccionada',
            ]);
            return;
        }

        Cart::instance('shopping')->update($rowId, $item->qty + 1);
        $this->syncCart();
    }

    public function decreaseQty($rowId)
    {
        $item = Cart::instance('shopping')->get($rowId);
        if ($item->qty > 1) {
            Cart::instance('shopping')->update($rowId, $item->qty - 1);
            $this->syncCart();
        }
    }

    public function removeItem($rowId)
    {
        Cart::instance('shopping')->remove($rowId);
        $this->syncCart();
        $this->dispatch('cart-updated');
    }

    public function clearCart()
    {
        Cart::instance('shopping')->destroy();
        $this->syncCart();
        $this->dispatch('cart-updated');
    }

    public function checkout()
    {
        // Doble validación en servidor para mayor seguridad
        $cart = Cart::instance('shopping')->content();
        $itemIds = $cart->pluck('id')->toArray();
        $stocks = \App\Models\Variant::whereIn('id', $itemIds)->pluck('stock', 'id')->toArray();

        $hasValidItems = false;
        foreach ($cart as $item) {
            $stock = $stocks[$item->id] ?? 0;
            if ($item->qty <= $stock) {
                $hasValidItems = true;
            }
        }

        if (!$hasValidItems) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'No hay suficiente stock',
                'text' => 'No tienes ningún producto con stock disponible para comprar.',
            ]);
            return;
        }

        return redirect()->route('checkout');
    }

    protected function syncCart()
    {
        if (auth()->check()) {
            Cart::instance('shopping')->store(auth()->id());
        }
    }

    public function render()
    {
        $cart = Cart::instance('shopping')->content();
        
        // Cargar todos los stocks de las variantes de forma masiva (para evitar N+1 queries)
        $itemIds = $cart->pluck('id')->toArray();
        $stocks = \App\Models\Variant::whereIn('id', $itemIds)->pluck('stock', 'id')->toArray();

        $subtotalVal = 0;
        $hasStockErrors = false;
        $hasValidItems = false;

        foreach ($cart as $item) {
            $stock = $stocks[$item->id] ?? 0;
            if ($item->qty <= $stock) {
                $subtotalVal += $item->qty * $item->price;
                $hasValidItems = true;
            } else {
                $hasStockErrors = true;
            }
        }

        return view('livewire.shopping-cart', [
            'cart' => $cart,
            'stocks' => $stocks,
            'hasStockErrors' => $hasStockErrors,
            'hasValidItems' => $hasValidItems,
            'total' => $subtotalVal,
            'subtotal' => $subtotalVal,
        ]);
    }
}
