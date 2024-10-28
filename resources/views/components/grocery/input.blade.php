@props([
    'type' => 'text',
    'name' => '',
    'label' => '',
    'value' => '',
    'placeholder' => ''
])

<div class="mb-4">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700 mb-2">{{ $label }}</label>
    @endif

    @if($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" placeholder="{{ $placeholder }}">{{ $value }}</textarea>
    @elseif($type === 'radio')
        @foreach($options as $option)
            <div class="flex items-center">
                <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}-{{ $option['value'] }}" value="{{ $option['value'] }}" {{ $option['value'] == $value ? 'checked' : '' }} class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                <label for="{{ $name }}-{{ $option['value'] }}" class="ml-2 block text-sm text-gray-900">
                    {{ $option['label'] }}
                </label>
            </div>
        @endforeach
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
    @endif
</div>
