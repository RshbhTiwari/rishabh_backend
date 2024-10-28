<div>
    <div class="inline-block min-w-full align-middle">
        <table class="min-w-full border-separate border-b" style="border-spacing: 0">
        <thead class="bg-skin-page">
        <tr>
            <th colspan="9" class="sticky top-10 md:top-0 z-10 h-10 bg-skin-page border-t border-skin-primary rounded-t-lg overflow-hidden print:hidden">
                <x-grocery.search-filter placeholder="Filter User..." />
            </th>
        </tr>
        <tr class="">
            <x-grocery.table-th name="ID" wire:click="sortBy('id')" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Name" wire:click="sortBy('name')" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Email" wire:click="sortBy('email')" class="text-left border-r-2" hidemobile="true" filter="true" sort="true" />
            <x-grocery.table-th name="Created at" wire:click="sortBy('created_at')" class="text-left" filter="true" sort="true" />
            <x-grocery.table-th name="Updated at" wire:click="sortBy('updated_at')" class="text-left" filter="true" sort="true" />
            <x-grocery.table-th name="Actions" class="border-l" hidemobile="true" filter="true" />
        </tr>
        </thead>
        <tbody class="bg-skin-page divide-y divide-skin-primary">
        @foreach($users as $user)  
            <x-grocery.user-one-row :id="$user->id" :name="$user->name"  :email="$user->email"  :created_date="$user->created_at" :updated_date="$user->updated_at" />
        @endforeach
        </tbody>
        </table>
    </div>
   
</div>