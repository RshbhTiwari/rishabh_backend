<?php

namespace App\Livewire;

use App\Models\Category;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryPagination extends Component
{
    use WithPagination;

    public $searchTerm = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $id = null;
    public function mount($id = null)
    {
        $this->id = $id;
    }

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
        $searchTerm = '%' . $this->searchTerm . '%';

        return view('livewire.category-pagination', [
            'categories' => Category::query()
                ->where('parent_id',   $this->id )
                ->where('name', 'like', $searchTerm)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(20),
        ]);
    }
}
