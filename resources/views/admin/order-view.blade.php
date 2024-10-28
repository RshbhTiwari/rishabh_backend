<x-app-layout>
    <x-slot name="header">
    </x-slot>
    <div class="bg-white shadow rounded-lg p-5">
        <div class="px-4 py-5 sm:px-4 sm:py-3">
            <x-header-text text="Order: Details" type="h1" />
        </div>

        <div class="grid grid-cols-2 gap-2 text-sm p-2">
            <div><strong>Status:</strong> {{ $order->status }}</div>
            <div><strong>Payment Status:</strong> {{ $order->payment_status }}</div>
            <div><strong>Payment Method:</strong> {{ $order->payment_method ?: 'N/A' }}</div>
            <div><strong>Currency:</strong> {{ $order->currency }}</div>
        </div>
    </div>

    @if($order->user)
    <div class="bg-white shadow rounded-lg p-5 mt-4 ">
        <div class="px-4 py-5 sm:px-4 sm:py-3">
            <x-header-text text="Customer: Details" type="h1" />
        </div>
        <div class="grid grid-cols-2 gap-2 text-sm p-2">
            <div><strong>Name:</strong> {{ $order->user->name }}</div>
            <div><strong>Email:</strong> {{ $order->user->email }}</div>

            <div><strong>Gender:</strong> {{ $order->user->gender ?: 'N/A' }}</div>
            <div><strong>Contact:</strong> {{ $order->user->contact ?: 'N/A' }}</div>
        </div>
    </div>
    @endif

    <div class="bg-white shadow rounded-lg p-5 mt-4 ">
        <div class="grid grid-cols-2 gap-2 text-sm p-2">
            @if($order->addresses)
            @foreach($order->addresses as $address)
       
            @if($address->type == 'billing')
            
            <div>
                <div class="mb-2">
                    <x-header-text text="Billing: Address" type="h1" />
                </div>
                <div><strong>Address:</strong> {{ $address->addressname }}</div>
                <div><strong>Type:</strong> {{ $address->addrestype }}</div>
                <div><strong>Name:</strong> {{ $address->addressname }}</div>
                <div><strong>City:</strong> {{ $address->city }}</div>
                <div><strong>State:</strong> {{ $address->state }}</div>
                <div><strong>Postal Code:</strong> {{ $address->postal_code }}</div>
                <div><strong>Landmark Name:</strong> {{ $address->landmarkname }}</div>
                <div><strong>Contact:</strong> {{ $address->contact }}</div>
                <div><strong>Email:</strong> {{ $address->email }}</div>
            </div>
            @else
            <div>
                <div class="mb-2">
                    <x-header-text text="Shipping: Address" type="h1" />
                </div>
                <div><strong>Address:</strong> {{ $address->addressname }}</div>
                <div><strong>Type:</strong> {{ $address->addrestype }}</div>
                <div><strong>Name:</strong> {{ $address->addressname }}</div>
                <div><strong>City:</strong> {{ $address->city }}</div>
                <div><strong>State:</strong> {{ $address->state }}</div>
                <div><strong>Postal Code:</strong> {{ $address->postal_code }}</div>
                <div><strong>Landmark Name:</strong> {{ $address->landmarkname }}</div>
                <div><strong>Contact:</strong> {{ $address->contact }}</div>
                <div><strong>Email:</strong> {{ $address->email }}</div>
            </div>
            @endif
            @endforeach
            @endif
        </div>
    </div>

    <div class="bg-skin-page shadow rounded-lg mt-5 mb-5">
        <div class="px-4 py-5 sm:px-4 sm:py-3">
            <x-header-text text="Order: Items" type="h1" />
        </div>
        <div class="min-w-full">
            <table class="min-w-full border-separate" style="border-spacing: 0">
                <thead class="bg-skin-border/50">
                    <tr>
                        <x-grocery.table-th name="Name" class="text-left" hidemobile="true" />
                        <x-grocery.table-th name="Quantity" class="text-left" hidemobile="true" />
                        <x-grocery.table-th name="Price" class="text-left" hidemobile="true" />
                        <x-grocery.table-th name="Discount" class="text-left" hidemobile="true" />
                        <x-grocery.table-th name="Total Price" class="text-left" hidemobile="true" />
                    </tr>
                </thead>
                <tbody class="skin-border-100">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-yellow-50">
                        <td class="md:hidden border-b border-t-2 border-skin-primary px-2 py-2">
                            {{ $item->product_name }}
                        </td>
                    </tr>
                    <tr class="hover:bg-yellow-50">
                        <td class="hidden md:table-cell px-2 py-2 align-top border-skin-primary border-b border-r">
                            {{ $item->product_name }}
                        </td>
                        <td class="hidden md:table-cell px-2 py-2 align-top border-skin-primary border-b border-r">
                            {{ $item->quantity }}
                        </td>
                        <td class="hidden md:table-cell px-2 py-2 align-top border-skin-primary border-b border-r">
                            {{ $item->price }}
                        </td>
                        <td class="hidden md:table-cell px-2 py-2 align-top border-skin-primary border-b border-r">
                            {{ $item->discount }}
                        </td>
                        <td class="hidden md:table-cell px-2 py-2 align-top border-skin-primary border-b border-r">
                            {{ $item->total_price }}
                        </td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="4" class="px-2 py-2 border-skin-primary border-b">
                            <!-- Empty cell to align right -->
                        </td>
                        <td class="px-2 py-2 border-skin-primary border-b text-right">
                            <div><strong>Subtotal:</strong> {{ number_format($order->subtotal, 2) }}</div>
                            <div><strong>Tax:</strong> {{ number_format($order->tax, 2) }}</div>
                            <div><strong>Shipping:</strong> {{ number_format($order->shipping, 2) }}</div>
                            <div><strong>Total:</strong> {{ number_format($order->total, 2) }}</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>