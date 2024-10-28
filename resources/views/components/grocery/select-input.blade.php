@props([
    'label' => '',
    'id' => '',
    'name' => '',
    'value' => '',
    'data' => [],
    'disabled' => false,
    'multiple' => false,
    'placeholder' => 'Select an option',
])

@php
$classes = 'appearance-none block h-10 py-2 border border-skin-primary focus:outline-none focus:z-10 relative';
$classes .= ' w-full rounded-lg';
$classes .= ($disabled == true)
  ? ' text-gray-500 bg-gray-100 hover:bg-gray-100 focus:ring-gray-200 cursor-not-allowed'
  : ' text-skin-primary bg-skin-page focus:ring-skin-button-primary focus:border-skin-button-primary';
@endphp

<div class="mt-3 space-y-6 sm:space-y-5">
    <div class="{{ $label ? 'sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start' : '' }}">
        @if($label)
            <label for="{{ $name }}" class="block text-sm font-bold sm:mt-px sm:pt-2">{{ $label }}</label>
        @endif
        <div class="mt-1 sm:mt-0 sm:col-span-2">
            <select id="{{ $id }}" name="{{ $name }}" {{ $disabled ? 'disabled' : '' }} {{ $multiple ? 'multiple' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
                <option value="" disabled {{ !$value ? 'selected' : '' }}>{{ $placeholder }}</option>
                @foreach($data as $key => $item)
                    <option value="{{ $key }}" {{ $multiple ? (in_array($key, (array) $value) ? 'selected' : '') : ($key == $value ? 'selected' : '') }}>{{ $item }}</option>
                @endforeach
            </select>
        </div>
    </div>
</div>