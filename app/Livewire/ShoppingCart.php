<?php

namespace App\Livewire;

use Livewire\Component;

class ShoppingCart extends Component
{
    public function mount()
    {
        Cart::instance('shopping')
    }
    public function render()
    {
        return view('livewire.shopping-cart');
    }
}
