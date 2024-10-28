<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;

class ProductPagination extends Component
{
    use WithPagination;

    public $searchTerm = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public function sortBy(string $columnName)
    {
        if ($this->sortField === $columnName) {
            $this->sortDirection = $this->swapSortDirection();
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $columnName;
    }

    public function swapSortDirection()
    {
        return $this->sortDirection === 'asc' ? 'desc' : 'asc';
    }

    public function updated()
    {
        $this->resetPage();
    }

    public function render()
    {
        $searchTerm = '%'.$this->searchTerm.'%';

        return view('livewire.product-pagination', [
            'products' => Product::query()
                ->where('is_variant', 0)
                ->where('name', 'like', $searchTerm)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(20),
        ]);
    }
}
