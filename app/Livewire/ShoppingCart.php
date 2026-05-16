<?php

namespace App\Livewire;

use Gloudemans\Shoppingcart\Facades\Cart;
use Livewire\Component;

class ShoppingCart extends Component
{
    public function mount()
    {
        Cart::instance('shopping');
    }
    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
