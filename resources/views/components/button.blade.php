@props(['link' => 'false'])
@props(['type' => 'primary'])
@props(['disabled' => false])
@props(['newwindow' => ''])
@php
$classes = 'inline-flex items-center justify-center rounded-full font-display font-bold text-sm focus:outline-none transition';
$classes .= ' disabled:bg-gray-300 hover:disabled:bg-gray-300 disabled:cursor-not-allowed';

$classes .= (str_contains($type,'-circle'))
  ? ' h-11 w-11'
  : ' h-11 px-6';

$type = str_replace('-circle','',$type);
switch($type) {
    case 'primary':
        $classes .= ' text-white bg-black hover:bg-skin-button-primary-hover active:bg-skin-button-primary-hover';
        break;
    case 'secondary':
        $classes .= ' text-skin-button-primary hover:text-skin-button-primary-hover border-skin-button-primary hover:border-skin-button-primary-hover border-2';
        break;
    case 'tiertiary':
        $classes .= ' ';
        break;
    case 'danger':
        $classes .= ' text-white bg-red-600 hover:bg-red-800 active:bg-red-800';
        break;
}
@endphp
@if($link == 'false')
<button {{ $disabled == 'true' ? 'disabled' : '' }} {{ $attributes->merge(['type' => 'submit', 'class' => $classes]) }}>
    {{ $slot }}
</button>
@else
<a {{ $disabled == 'true' ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }} {{ $newwindow ? ' target="_blank"' : '' }}>
    {{ $slot }}
</a>
@endif