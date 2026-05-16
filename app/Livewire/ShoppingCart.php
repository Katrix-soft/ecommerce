<?php

namespace App\Livewire;

use Livewire\Component;
use Gloudemans\Shoppingcart\Facades\Cart;

class ShoppingCart extends Component
{
    public function increaseQty($rowId)
    {
        $item = Cart::instance('shopping')->get($rowId);
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

    protected function syncCart()
    {
        if (auth()->check()) {
            Cart::instance('shopping')->store(auth()->id());
        }
    }

    public function render()
    {
        return view('livewire.shopping-cart', [
            'cart' => Cart::instance('shopping')->content(),
            'total' => Cart::instance('shopping')->total(),
            'subtotal' => Cart::instance('shopping')->subtotal(),
        ]);
    }
}
