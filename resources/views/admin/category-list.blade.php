<x-app-layout>
    <x-slot name="header">
    </x-slot>
    @php
    $id = request()->route('id') ?? null;
    @endphp
    <div class="mb-2 flex justify-between">
        <x-header-text text="Categories" type="h1" />
        <x-button 
            :href="route('category.form', ['id' => 'new', 'parent_id' => $id])" 
            link="true">
            {{ $id ? __('Add Subcategory') : __('Add Category') }}
        </x-button>
    </div>

    <div class="bg-skin-page shadow rounded-lg mb-5 mt-5">
        <div class="flex flex-col mb-5">
            @livewire('category-pagination', ['id' => $id])
        </div>
    </div>
</x-app-layout>
