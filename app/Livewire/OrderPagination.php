<?php

namespace App\Livewire;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class OrderPagination extends Component
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
        $searchTerm = '%' . $this->searchTerm . '%';

        return view('livewire.order-pagination', [
            'orders' => Order::query()
                ->where(function($query) use ($searchTerm) {
                    $query->where('payment_status', 'like', $searchTerm)
                          ->orWhere('subtotal', 'like', $searchTerm);
                })
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(20),
        ]);
    }
}
