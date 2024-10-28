<!-- resources/views/components/table-header.blade.php -->

@props(['name', 'sortField', 'sortDirection', 'sortable' => false, 'filter' => false, 'hidemobile' => false])

<th {{ $attributes->merge(['class' => 'text-left ' . ($attributes->get('class', ''))]) }}>
    @if ($sortable)
        <a href="#" wire:click.prevent="sortBy('{{ $sortField }}')" class="inline-flex items-center space-x-1">
            <span>{{ $name }}</span>
            @if ($sortField === $attributes->get('sortField'))
                @if ($sortDirection === 'asc')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 18V6m0 0v12m0-12h6m-6 0H6"></path>
                    </svg>
                @endif
            @endif
        </a>
    @else
        {{ $name }}
    @endif
</th>
