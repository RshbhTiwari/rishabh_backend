@if(session('status'))
<div class="bg-skin-page shadow rounded-lg mb-5 px-4 py-2">
    <div class="text-lg font-bold text-green-600">
        {{ session('status') }}
    </div>
</div>
@endif
@if(session('error'))
<div class="bg-skin-page shadow rounded-lg mb-5 px-4 py-2">
    <div class="text-lg font-bold text-red-600">
        {{ session('error') }}
    </div>
</div>
@endif