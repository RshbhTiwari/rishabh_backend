<x-app-layout>
    <x-slot name="header">
    <x-validation-errors class="mb-4" />
        <h2 class="text-2xl">{{ $id === 'new' ? 'Add New Product' : 'Edit Product' }}</h2>
    </x-slot>
    <div class="bg-skin-page shadow rounded-lg px-4 pt-1 pb-2">
        <form method="POST" action="{{ route('product.save') }}" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4 sm:space-y-3">
                <x-grocery.productAccordian :category="$categories" :product="$product" :attribute="$attributList" :variant="$variantList" />
            </div>

            <div class="mb-12">&nbsp;</div>
            <div class="fixed bottom-0 left-0 md:left-40 right-0">
                <div class="px-4 py-3 ml-4 sm:px-6 border-l border-skin-primary bg-gradient-to-t from-white">
                    <x-button class="h-10 w-full">{{ __('Save') }}</x-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
