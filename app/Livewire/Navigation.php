<?php

namespace App\Livewire;

use App\Models\Family;  
use Livewire\Attributes\Computed;
use Livewire\Component;

class Navigation extends Component
{

    public $families;
    public $family_id;

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
