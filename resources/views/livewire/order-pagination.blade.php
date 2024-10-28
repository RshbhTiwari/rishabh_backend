<div>
    <div class="inline-block min-w-full align-middle">
        <table class="min-w-full border-separate border-b" style="border-spacing: 0">
        <thead class="bg-skin-page">
        <tr>
            <th colspan="9" class="sticky top-10 md:top-0 z-10 h-10 bg-skin-page border-t border-skin-primary rounded-t-lg overflow-hidden print:hidden">
                <x-grocery.search-filter placeholder="Filter Orders..." />
            </th>
        </tr>
        <tr class="">
            <x-grocery.table-th name="ID" wire:click="sortBy('id')" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Status" wire:click="sortBy('status')" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Customer" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Subtotal" wire:click="sortBy('subtotal')" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Created at" wire:click="sortBy('created_at')" class="text-left" filter="true" sort="true" />
            <x-grocery.table-th name="Updated at" wire:click="sortBy('created_at')" class="text-left" filter="true" sort="true" />
            <x-grocery.table-th name="Actions" class="border-l" hidemobile="true" filter="true" />
        </tr>
        </thead>
        <tbody class="bg-skin-page divide-y divide-skin-primary">
            @foreach($orders as $order)
            <x-grocery.order-one-row
                :id="$order->id"
                :customer="$order->user"
                :status="$order->payment_status"
                :subtotal="$order->subtotal"
                :created_date="$order->created_at"
                :updated_date="$order->updated_at"
            />
            @endforeach
        </tbody>
        </table>
    </div>
    <div class="px-4 py-3">
        @if($orders->total() <= $orders->perPage())
            <p class="text-sm text-gray-700 leading-5">{{ $orders->total() }} results</p>
        @endif
        {{ $orders->links() }}
    </div>
</div>