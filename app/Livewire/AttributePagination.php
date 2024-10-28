<?php

namespace App\Livewire;

use App\Models\Attribute;
use Livewire\Component;
use Livewire\WithPagination;

class AttributePagination extends Component
{
    use WithPagination;

    public $searchTerm = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public function sortBy(string $columnName)
    {
        if($this->sortField === $columnName) {
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

        return view('livewire.attribute-pagination', [
            'attributes' => Attribute::query()->where('name', 'like', $searchTerm)
                ->orWhere('id', 'like', $searchTerm)
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(20),
        ]);
    }
}
