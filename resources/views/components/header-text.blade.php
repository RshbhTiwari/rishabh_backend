@if($type =='h1')
<h1 class="text-2xl sm:text-3xl font-display font-bold text-skin-header">
    {{ $text }}
</h1>
@elseif($type =='h2')
<h2 class="text-xl sm:text-2xl font-display font-bold text-skin-header">
    {{ $text }}
</h2>
@elseif($type =='h3')
<h3 class="text-lg sm:text-xl font-display font-bold text-skin-header">
    {{ $text }}
</h3>
@elseif($type =='h4')
<h4 class="font-display font-bold text-skin-header">
    {{ $text }}
</h4>
@endif
    