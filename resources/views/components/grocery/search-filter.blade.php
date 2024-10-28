@props(['placeholder' => 'Search...'])

<div class="relative">
  <input type="text" class="form-input w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:border-blue-500" placeholder="{{ $placeholder }}" wire:model.live="searchTerm">
  <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.5 15.5l5.1 5.1m-.7-8.6a7.5 7.5 0 1 0-15 0 7.5 7.5 0 0 0 15 0z" />
    </svg>
  </div>
</div>
