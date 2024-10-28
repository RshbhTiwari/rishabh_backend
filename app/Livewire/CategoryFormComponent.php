<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Category;
class CategoryFormComponent extends Component
{
    public $category;

    public function mount($category = null)
    {
        $this->category = $category ? Category::find($category) : new Category();
    }


    public function render()
    {
        return view('livewire.category-form-component');
    }
}
