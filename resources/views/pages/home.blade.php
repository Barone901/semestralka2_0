@extends('layouts.default')

@section('title', 'Domov')

@section('content')
    {{-- Hero --}}
    <section class="bg-white rounded-xl border p-8">
        <div class="grid gap-6 md:grid-cols-2 md:items-center">
            <div>
                <h1 class="text-3xl font-bold">
                    Originálne dizajny na oblečenie
                </h1>
                <p class="mt-3 text-gray-600">
                    Vyber si hotové dizajny alebo si nechaj upraviť vlastné oblečenie.
                    Rýchla výroba, kvalitná potlač, férové ceny.
                </p>

                <div class="mt-6 flex gap-3">
                    <a href="#" class="inline-flex items-center rounded-lg bg-black text-white px-4 py-2 text-sm">
                        Pozrieť produkty
                    </a>
                    <a href="#" class="inline-flex items-center rounded-lg border px-4 py-2 text-sm">
                        Ako to funguje
                    </a>
                </div>

                <div class="mt-6 text-xs text-gray-500">
                    @auth
                        Prihlásený ako <span class="font-medium">{{ auth()->user()->name }}</span>.
                    @else
                        Nie si prihlásený — môžeš si vytvoriť účet a sledovať objednávky.
                    @endauth
                </div>
            </div>

            <div class="rounded-xl bg-gray-100 border p-6">
                <div class="text-sm font-semibold">Rýchly prehľad</div>
                <ul class="mt-3 space-y-2 text-sm text-gray-700">
                    <li>✅ Dizajny od grafikov</li>
                    <li>✅ Objednávky spracúva zamestnanec</li>
                    <li>✅ Admin spravuje celý systém</li>
                </ul>
                <div class="mt-6 grid grid-cols-2 gap-3 text-center">
                    <div class="rounded-lg bg-white border p-4">
                        <div class="text-2xl font-bold">24–48h</div>
                        <div class="text-xs text-gray-500">príprava objednávky</div>
                    </div>
                    <div class="rounded-lg bg-white border p-4">
                        <div class="text-2xl font-bold">4.8★</div>
                        <div class="text-xs text-gray-500">hodnotenie zákazníkov</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- “Featured products” placeholder --}}
    <section class="mt-10">
        <div class="flex items-end justify-between">
            <h2 class="text-xl font-bold">Odporúčané produkty</h2>
            <a href="#" class="text-sm hover:underline">Zobraziť všetky</a>
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            @for ($i = 1; $i <= 4; $i++)
                <div class="bg-white border rounded-xl overflow-hidden">
                    <div class="h-36 bg-gray-100"></div>
                    <div class="p-4">
                        <div class="font-semibold">Produkt {{ $i }}</div>
                        <div class="text-sm text-gray-600">Krátky popis produktu.</div>
                        <div class="mt-3 flex items-center justify-between">
                            <span class="font-bold">19,99 €</span>
                            <button class="text-sm rounded-lg border px-3 py-1 hover:bg-gray-50">
                                Do košíka
                            </button>
                        </div>
                    </div>
                </div>
            @endfor
        </div>
    </section>
@endsection
