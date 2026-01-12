<x-layouts.default-layout title="Domov">

    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex gap-6 py-6">

            {{-- Sidebar (desktop) --}}
            <aside class="hidden lg:block w-72 shrink-0">
                @include('partials.categories-sidebar', ['categories' => $categories])
            </aside>

            {{-- Main --}}
            <main class="min-w-0 flex-1">

                {{-- Hero --}}
                <section class="rounded-2xl border bg-white p-8 md:p-12">
                    <div class="grid gap-10 md:grid-cols-2 md:items-center">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Nová kolekcia • limitované dizajny</p>
                            <h1 class="mt-3 text-3xl md:text-5xl font-bold tracking-tight">
                                Originálne kúsky, ktoré nikto iný nemá
                            </h1>
                            <p class="mt-4 text-gray-600 leading-relaxed">
                                Vyber si hotový dizajn alebo si nechaj upraviť vlastné oblečenie.
                                Správa objednávok pre zamestnancov a dizajny pre grafikov.
                            </p>

                            <div class="mt-6 flex flex-wrap gap-3">
                                <a href="#products" class="rounded-xl bg-black px-5 py-3 text-sm text-white hover:opacity-90">
                                    Pozrieť produkty
                                </a>
                                <a href="#" class="rounded-xl border px-5 py-3 text-sm hover:bg-gray-50">
                                    Ako to funguje
                                </a>
                            </div>

                            <div class="mt-8 grid grid-cols-3 gap-4 text-center">
                                <div class="rounded-xl bg-gray-50 p-4">
                                    <div class="text-lg font-bold">24–48h</div>
                                    <div class="text-xs text-gray-500">príprava</div>
                                </div>
                                <div class="rounded-xl bg-gray-50 p-4">
                                    <div class="text-lg font-bold">4.8★</div>
                                    <div class="text-xs text-gray-500">hodnotenie</div>
                                </div>
                                <div class="rounded-xl bg-gray-50 p-4">
                                    <div class="text-lg font-bold">Top kvalita</div>
                                    <div class="text-xs text-gray-500">materiál</div>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 border p-8">
                            <div class="text-sm font-semibold text-gray-900">Rýchly prehľad rolí</div>
                            <ul class="mt-4 space-y-3 text-sm text-gray-700">
                                <li class="flex gap-2"><span>🎨</span> Grafik nahráva dizajny / obrázky</li>
                                <li class="flex gap-2"><span>📦</span> Zamestnanec spracúva objednávky</li>
                                <li class="flex gap-2"><span>🛠️</span> Admin spravuje celý systém</li>
                            </ul>

                            <div class="mt-6 rounded-xl bg-white border p-4 text-sm text-gray-700">
                                @auth
                                    Prihlásený ako <span class="font-semibold">{{ auth()->user()->name }}</span>.
                                @else
                                    Nie si prihlásený – vytvor si účet a sleduj objednávky.
                                @endauth
                            </div>
                        </div>
                    </div>
                </section>

                {{-- Products --}}
                <section id="products" class="mt-10">
                    <div class="flex items-end justify-between">
                        <h2 class="text-xl font-bold tracking-tight">Produkty</h2>
                    </div>

                    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @forelse($products as $product)
                            <a href="{{ route('product.show', $product) }}"
                               class="group block overflow-hidden rounded-2xl border bg-white">

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

                                <div class="p-4">
                                    <h3 class="font-semibold group-hover:underline">{{ $product->name }}</h3>
                                    <p class="mt-1 text-sm text-gray-600 line-clamp-2">
                                        {{ $product->description }}
                                    </p>

                                    <div class="mt-4 flex items-center justify-between">
                                        <span class="font-bold">{{ number_format($product->price, 2, ',', ' ') }} €</span>
                                        <button type="button" class="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50">
                                            Do košíka
                                        </button>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="rounded-2xl border bg-white p-6 text-sm text-gray-600">
                                Zatiaľ tu nie sú žiadne produkty.
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </section>

            </main>
        </div>
    </div>

</x-layouts.default-layout>
