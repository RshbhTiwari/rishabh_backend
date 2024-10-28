<x-app-layout>
    <x-slot name="header">
    </x-slot>
    <div class="mb-2 flex justify-between">
        <x-header-text text="Products" type="h1" />
        <x-button :href="route('product.form', ['id' => 'new'])" link="true">
            {{ __('Add Product') }}
        </x-button>
    </div>
    <div class="bg-skin-page shadow rounded-lg mb-5 mt-5">
        <div class="flex flex-col mb-5">
            @livewire('product-pagination')
        </div>
    </div>
</x-app-layout>