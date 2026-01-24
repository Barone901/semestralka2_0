@props(['stock'])

@if($stock <= 0)
    <span class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
        Out of Stock
    </span>
@elseif($stock <= 5)
    <span class="rounded-full bg-orange-50 px-3 py-1 text-sm font-medium text-orange-700">
        Last items: {{ $stock }}
    </span>
@else
    <span class="rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
        In Stock: {{ $stock }}
    </span>
@endif

