@props([
    'items' => [],
    'showPrevious' => false,
    'previousLabel' => 'Back',
])

<nav class="mb-6 text-sm">
    <ol class="flex items-center space-x-2 text-gray-500">
        {{-- Home --}}
        <li>
            <a href="{{ route('home') }}" class="hover:text-indigo-600">Home</a>
        </li>

        {{-- Previous (optional) --}}
        @if($showPrevious && url()->previous() && url()->previous() !== url()->current())
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ url()->previous() }}" class="hover:text-indigo-600">
                    {{ $previousLabel }}
                </a>
            </li>
        @endif

        {{-- Custom items --}}
        @foreach($items as $item)
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-indigo-600">{{ $item['label'] }}</a>
                @else
                    <span class="text-gray-900 font-medium truncate">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
