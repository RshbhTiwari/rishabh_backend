<x-app-layout>
    <x-slot name="header">
    </x-slot>
    <div class="mb-2">
        <x-header-text text="Orders" type="h1" />
    </div>

    <div class="bg-skin-page shadow rounded-lg mb-5 mt-5">
        <div class="flex flex-col mb-5">
            @livewire('order-pagination')
        </div>
    </div>
</x-app-layout>
