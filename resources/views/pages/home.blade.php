@extends('layouts.default')

@section('title', 'Domov')

@section('content')
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
                    <a href="#" class="rounded-xl bg-black px-5 py-3 text-sm text-white hover:opacity-90">
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

    {{-- Featured --}}
    <section class="mt-10">
        <div class="flex items-end justify-between">
            <h2 class="text-xl font-bold tracking-tight">Odporúčané produkty</h2>
            <a href="#" class="text-sm text-gray-600 hover:text-black hover:underline">Zobraziť všetky</a>
        </div>

        <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            @for ($i = 1; $i <= 4; $i++)
                <article class="group overflow-hidden rounded-2xl border bg-white">
                    <div class="aspect-[4/3] bg-gray-100"></div>
                    <div class="p-4">
                        <h3 class="font-semibold group-hover:underline">Produkt {{ $i }}</h3>
                        <p class="mt-1 text-sm text-gray-600">Krátky popis produktu.</p>

                        <div class="mt-4 flex items-center justify-between">
                            <span class="font-bold">19,99 €</span>
                            <button class="rounded-xl border px-3 py-2 text-sm hover:bg-gray-50">
                                Do košíka
                            </button>
                        </div>
                    </div>
                </article>
            @endfor
        </div>
    </section>
@endsection
