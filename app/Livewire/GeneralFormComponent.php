<?php

namespace App\Livewire;

use Livewire\Component;

class GeneralFormComponent extends Component
{

    public $name;
    public $description;
    public $shortDescription;
    
    public function render()
    {
        return view('livewire.general-form-component');
    }
}
