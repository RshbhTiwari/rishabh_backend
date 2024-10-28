<!-- resources/views/components/grocery/textarea.blade.php -->

@props([
    'label' => '',
    'id' => '',
    'name' => '',
    'placeholder' => '',
    'value' => '',
    'required' => false,
    'maxlength' => ''
])

<div class="mb-4">
    @if ($label)
        <label for="{{ $id }}" class="block font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    <textarea id="{{ $id }}" name="{{ $name }}" placeholder="{{ $placeholder }}"
        @if ($maxlength) maxlength="{{ $maxlength }}" @endif
        {{ $attributes->merge(['class' => 'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) }}
        >{{ old($name, $value) }}</textarea>
</div>
