<x-layouts.default-layout :title="$product->name">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid gap-8 lg:grid-cols-2">

            {{-- Image --}}
            <div class="overflow-hidden rounded-2xl border bg-white">
                <div class="aspect-[4/3] bg-gray-100">
                    @if($product->image_url)
                        <img
                            src="{{ str_starts_with($product->image_url, 'http')
                                ? $product->image_url
                                : asset('storage/' . $product->image_url) }}"
                            alt="{{ $product->name }}"
                            class="h-full w-full object-cover"
                        />
                    @endif
                </div>
            </div>

            {{-- Info --}}
            <div>
                <div class="text-sm text-gray-500">
                    Kategória:
                    <span class="font-medium text-gray-900">
                        {{ $product->category?->name ?? '—' }}
                    </span>
                </div>

                <h1 class="mt-2 text-3xl font-bold tracking-tight">{{ $product->name }}</h1>

                <div class="mt-4 flex items-center gap-4">
                    <div class="text-2xl font-bold">
                        {{ number_format($product->price, 2, ',', ' ') }} €
                    </div>

                    @if($product->stock > 0)
                        <span class="rounded-full bg-green-50 px-3 py-1 text-sm font-medium text-green-700">
                            Skladom: {{ $product->stock }}
                        </span>
                    @else
                        <span class="rounded-full bg-red-50 px-3 py-1 text-sm font-medium text-red-700">
                            Vypredané
                        </span>
                    @endif
                </div>

                <div class="mt-6 text-gray-700 leading-relaxed whitespace-pre-line">
                    {{ $product->description }}
                </div>

                <div class="mt-8 flex flex-wrap gap-3">
                    <button class="rounded-xl bg-black px-5 py-3 text-sm text-white hover:opacity-90">
                        Do košíka
                    </button>
                    <a href="{{ route('home') }}" class="rounded-xl border px-5 py-3 text-sm hover:bg-gray-50">
                        Späť na domov
                    </a>
                </div>
            </div>
        </div>

        {{-- Related --}}
        @if($related->count())
            <section class="mt-12">
                <h2 class="text-xl font-bold tracking-tight">Podobné produkty</h2>

                <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach($related as $p)
                        <a href="{{ route('product.show', $p) }}"
                           class="group overflow-hidden rounded-2xl border bg-white">
                            <div class="aspect-[4/3] bg-gray-100">
                                @if($p->image_url)
                                    <img
                                        src="{{ str_starts_with($p->image_url, 'http')
                                            ? $p->image_url
                                            : asset('storage/' . $p->image_url) }}"
                                        alt="{{ $p->name }}"
                                        class="h-full w-full object-cover"
                                    />
                                @endif
                            </div>
                            <div class="p-4">
                                <div class="font-semibold group-hover:underline">{{ $p->name }}</div>
                                <div class="mt-2 font-bold">{{ number_format($p->price, 2, ',', ' ') }} €</div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
    </div>

</x-layouts.default-layout>
