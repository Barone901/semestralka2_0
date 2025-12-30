<div class="rounded-2xl border bg-white">
    <div class="px-4 py-3 text-sm font-semibold">Kategórie</div>

    <nav class="px-2 pb-2">
        @foreach($categories as $cat)
            <a href="{{ route('category.show', $cat->slug) }}"
               class="flex items-center justify-between rounded-xl px-3 py-2 text-sm hover:bg-gray-50
                {{ request()->is('kategoria/'.$cat->slug) ? 'bg-gray-100 font-semibold' : '' }}">
                <span>{{ $cat->nazov }}</span>
                @if($cat->children->count()) <span class="text-gray-400">›</span> @endif
            </a>

            @if($cat->children->count())
                <div class="ml-4 mb-2 border-l pl-3">
                    @foreach($cat->children as $child)
                        <a href="{{ route('category.show', $child->slug) }}"
                           class="block rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50
                      {{ request()->is('kategoria/'.$child->slug) ? 'bg-gray-100 font-semibold' : '' }}">
                            {{ $child->nazov }}
                        </a>
                    @endforeach
                </div>
            @endif
        @endforeach
    </nav>
</div>
