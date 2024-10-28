@props([
    'id' => '',
    'customer' => '',
    'status' => '',
    'subtotal' => '',
    'created_date' => '',
    'updated_date' => ''
])

<tr>
    <td colspan="5" class="md:hidden px-2 py-2 border-skin-primary border-b border-t-2">
        <div class="flex justify-between">
            <a href="{{ route('order.view', ['id' => $id]) }}" title="View" class="font-bold text-skin-link hover:text-skin-link/90 hover:underline">{{ $status }}</a>
        </div>
    </td>
</tr>
<tr>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r text-sm">{{ $id }}</td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r text-sm">{{ $status }}</td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r text-sm">
        @if($customer)
        <span class="block mb-1">{{ $customer->email }}</span>
        <div class="flex font-normal text-sm opacity-60">
            <span class="mr-2">{{ $customer->name }}</span>
        </div>
        @endif
    </td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r text-sm">{{ $subtotal }}</td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap text-sm">
        <span class="block">{{ date('m/d/Y g:ia', strtotime($created_date)) }}</span>
    </td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap text-sm">
        <span class="block">{{ date('m/d/Y g:ia', strtotime($updated_date)) }}</span>
    </td>
    <td class="hidden md:table-cell px-2 py-2 border-skin-primary border-b border-r whitespace-nowrap text-center">
        <a href="{{ route('order.view', ['id' => $id]) }}" title="Edit" class="inline-block text-skin-link hover:text-skin-link/90">
            <x-grocery.icon name="view" class="h-6 w-6" />
        </a>

        <form action="{{ route('order.delete', $id) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="inline-block text-red-500 hover:text-red-700">
                <x-grocery.icon name="delete" class="h-6 w-6" />
            </button>
        </form>
    </td>
</tr>