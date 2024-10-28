@props([
    'id' => '',
    'name' => '',
    'created_date' => '',
    'updated_date' => ''
])

<tr>
    <td colspan="3" class="md:hidden px-2 py-2 border-skin-primary border-b border-t-2">
        <div class="flex justify-between">
            <a href="{{ route('attribute.form', ['id' => $id]) }}" title="Edit" class="font-bold text-skin-link hover:text-skin-link/90 hover:underline">{!! $name !!}</a>
        </div>
    </td>
</tr>
<tr>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r text-sm">{{ $id }}</td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap md:whitespace-normal md:min-w-96">
        <a href="{{ route('attribute.form', ['id' => $id]) }}" title="Edit" class="text-skin-link hover:text-skin-link/90 hover:underline">{!! $name !!}</a>
    </td>
     <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap text-sm">
        <span class="block">{{ date('m/d/Y g:ia', strtotime($created_date)) }}</span>
    </td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap text-sm">
        <span class="block">{{ date('m/d/Y g:ia', strtotime($updated_date)) }}</span>
    </td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap text-center">
        <a href="{{ route('attribute.form', ['id' => $id]) }}" title="Edit Attribute" class="inline-block text-skin-link hover:text-skin-link/90">
            <x-grocery.icon name="edit" class="h-6 w-6" />
        </a>
        <form method="POST" action="{{ route('attribute.delete', $id) }}" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this attribute?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="text-red-500 hover:text-red-700">
            <x-grocery.icon name="delete" class="h-6 w-6" />
            </button>
        </form>
    </td>
</tr>
