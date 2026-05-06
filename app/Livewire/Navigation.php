<?php

namespace App\Livewire;

use Livewire\Component;

class Navigation extends Component
{

    public $families;

    public function mount()
    {

        $this->families = \App\Models\Family::all();

    }

    public function render()
    {
        return view('livewire.navigation');
    }
}
