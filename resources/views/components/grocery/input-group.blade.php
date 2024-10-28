@props(['name'])
@props(['value'])
@props(['columns' => '1'])
@props(['subtext' => ''])
@props(['stacked' => 'false'])
@props(['checkboxtoggles' => 'false'])

@php
$classes = ($columns > '1')
    ? 'grid grid-cols-'.$columns.' gap-2'
    : 'space-y-2';
@endphp
@if($checkboxtoggles == 'true')
<div x-data="{ checkboxes: [] }" x-init="checkboxes = [...$el.querySelectorAll('input[type=checkbox]')]">
@endif
@if($stacked == 'true')
<div class="pt-3 pb-4">
    <fieldset>
        <legend class="block mb-2 leading-tight w-full">
            <div class="flex justify-between items-center">
                <div class="text-lg font-bold">{{ $name }}</div>
                @if($checkboxtoggles == 'true')
                    <div class="text-xs text-right rounded bg-gray-100 p-1">
                        <a class="bg-skin-page text-skin-primary cursor-pointer inline-block mr-1 border rounded px-1 py-1 w-12 text-center hover:border-skin-button-primary" x-on:click="checkboxes.forEach(checkbox => checkbox.checked = true)">All</a>
                        <a class="bg-skin-page text-skin-primary cursor-pointer inline-block mr-1 border rounded px-1 py-1 w-12 text-center hover:border-skin-button-primary" x-on:click="checkboxes.forEach(checkbox => checkbox.checked = false)">None</a>
                    </div>
                @endif
            </div>
            @if ($subtext != '')
            <div class="text-sm opacity-80">{{ $subtext }}</div>
            @endif
        </legend>
        <div class="{{ $classes }}">
            {{ $value ?? $slot }}
        </div>
    </fieldset>
</div>
@else
<div class="mt-3 space-y-6 sm:space-y-5">
  <div class="sm:grid sm:grid-cols-3 sm:gap-4 sm:items-start">
    <div class="block sm:mt-px sm:pt-2">
        <div class="text-sm font-bold">{{ $name }}</div>
    </div>
    <div class="mt-1 sm:mt-0 sm:col-span-2 {{ $classes }}">
        @if($checkboxtoggles == 'true')
            <div class="text-xs col-span-{{ $columns }} text-right rounded bg-gray-100 sm:mt-2 p-1">
                <a class="bg-skin-page text-skin-primary cursor-pointer inline-block mr-1 border rounded px-1 py-1 w-12 text-center hover:border-skin-button-primary" x-on:click="checkboxes.forEach(checkbox => checkbox.checked = true)">All</a>
                <a class="bg-skin-page text-skin-primary cursor-pointer inline-block mr-1 border rounded px-1 py-1 w-12 text-center hover:border-skin-button-primary" x-on:click="checkboxes.forEach(checkbox => checkbox.checked = false)">None</a>
            </div>
        @endif
        {{ $value ?? $slot }}
    </div>
  </div>
</div>
@endif
@if($checkboxtoggles == 'true')
</div>
@endif