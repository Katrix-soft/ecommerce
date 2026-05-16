<?php

namespace App\Livewire;

use App\Models\Family;  
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Navigation extends Component
{

    public $families;
    public $family_id;

    #[On('cartUpdated')]
    #[On('cart-updated')]
    public function updateCart()
    {
        // Esta función vacía hace que el componente se vuelva a renderizar
        // actualizando así el número de elementos en el carrito
    }

    public function toggleFamily($id)
    {
        if ($this->family_id == $id) {
            $this->family_id = null;
        } else {
            $this->family_id = $id;
        }
    }

    public function mount()
    {
        $this->families = \App\Models\Family::all();
        $first = $this->families->first();
        $this->family_id = $first ? $first->id : null;
    }

    #[Computed()]
    public function categories(){
        return \App\Models\Category::where('family_id', $this->family_id)
        ->with('subcategories')
        ->get();
    }

    #[Computed()] 
    public function familyName()
    {
      
     return $this->family_id ? Family::find($this->family_id)?->name : null;
    }
    
    public function render()
    {
        return view('livewire.navigation');
    }
}
