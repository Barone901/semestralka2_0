<x-layouts.default-layout title="Home">

    {{-- FULL WIDTH BANNER --}}
    <x-shop.banner-slider :banners="$banners" />

    {{-- Zvyšok stránky už v kontajneri --}}
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <section class="py-10">



            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

                {{-- KATALÓG KATEGÓRIÍ --}}
                <section class="mt-2">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold tracking-tight">Product categories</h2>
                        </div>

                        <a href="{{ route('products.index') }}"
                           class="hidden sm:inline-flex items-center rounded-xl border px-4 py-2 text-sm hover:bg-gray-50">
                            View all products
                        </a>
                    </div>

                    <div class="mt-6">
                        <x-shop.categories-grid :categories="$categories" />
                    </div>
                </section>

            </div>
        </section>
    </div>

</x-layouts.default-layout>
