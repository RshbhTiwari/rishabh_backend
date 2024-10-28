<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl">Category</h2>
    </x-slot>

    <div class="bg-skin-page shadow rounded-lg px-4 pt-1 pb-2">
        <form method="POST" action="{{ route('category.save') }}" enctype="multipart/form-data">
            @php
            $parent_id = request()->route('parent_id');
            @endphp
            <div class="space-y-4 sm:space-y-3">
                <x-validation-errors class="mb-4" />
                @csrf
                <div class="mb-2 flex justify-between">
                    @if($id == 'new')
                    Add New Category
                    @else
                    Admin: Edit Category id: {{ $id }}
                    @endif
                </div>
                <div class="ml-6">
                    <div class="max-w-3xl m-auto">
                        <x-grocery.accordian :category="$category" :categories="$categories" :parent_id="$parent_id" :id="$id" />
                    </div>
                </div>
            </div>
            <div class="mb-12">&nbsp;</div>
            <div class="fixed bottom-0 left-0 md:left-40 right-0">
                <div class="px-4 py-3 ml-4 sm:px-6 border-l border-skin-primary bg-gradient-to-t from-white">
                    <x-button class="h-10 w-full">
                        {{ __('Save') }}
                    </x-button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>