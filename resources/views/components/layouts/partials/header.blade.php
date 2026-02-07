@php use Illuminate\Support\Facades\Route; @endphp

<header class="site-header sticky top-0 z-50">

    {{-- ROW 1: logo + search + profile (account) --}}
    <div class="header-top bg-white border-b">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center gap-4">

                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center">
                    <img src="{{ asset('images/triumfLogo.png') }}" alt="Triumf" class="h-10 w-auto"/>
                </a>

                {{-- Right side --}}
                <div class="ml-auto flex items-center gap-3 w-full max-w-4xl justify-end">

                    {{-- Search (desktop) --}}
                    <div class="relative w-full max-w-xl hidden md:block">
                        <input
                            type="text"
                            id="search-input"
                            placeholder="Search product..."
                            class="w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black/10 focus:border-transparent"
                        />
                        <svg class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                             stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>

                        <div id="search-results"
                             class="absolute top-full left-0 right-0 mt-1 bg-white border rounded-xl shadow-lg overflow-hidden z-50 hidden"></div>
                    </div>

                    {{-- Profile / account (desktop) --}}
                    <div class="hidden md:flex items-center">
                        @include('components.layouts.partials.account')
                    </div>

                </div>
            </div>

            {{-- Mobile search under row 1 --}}
            <div class="md:hidden pb-3">
                <input
                    type="text"
                    id="search-input-mobile"
                    placeholder="Search product..."
                    class="w-full rounded-md border border-gray-200 bg-gray-50 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black/10"
                />
            </div>
        </div>

        {{-- ROW 2: menu links + cart --}}
        <div class="site-header-bottom header-bottom text-white">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="h-12 flex items-center justify-between">

                    {{-- Desktop nav --}}
                    <nav class="site-nav hidden lg:flex items-center gap-8 text-sm font-medium">
                        <a href="{{ route('home') }}" class="hover:opacity-90">Home</a>
                        <a href="{{ route('products.index') }}" class="hover:opacity-90">Products</a>
                        <a href="{{ route('pages.index') }}" class="hover:opacity-90">Articles</a>
                        <a href="{{ route('about') }}" class="hover:opacity-90">About us</a>
                        <a href="{{ route('contact') }}" class="hover:opacity-90">Contact</a>
                    </nav>

                    {{-- Right side (cart) --}}
                    <div class="flex items-center gap-2">

                        {{-- Cart (desktop + mobile) --}}
                        <div id="cart-wrapper" class="relative">
                            <a
                                href="{{ Route::has('cart.index') ? route('cart.index') : url('/cart') }}"
                                id="cart-btn"
                                class="relative inline-flex items-center gap-2 rounded-md border border-white/20 px-3 py-2 text-sm hover:bg-white/10"
                            >
                                <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>

                                <span class="hidden sm:inline">Cart</span>

                                <span id="cart-count"
                                      class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center hidden">0</span>
                            </a>

                            <div id="cart-dropdown"
                                 class="hidden opacity-0 translate-y-2 transition-all duration-200 absolute right-0 top-full mt-2 w-80 bg-[#141414] text-white rounded-xl border border-white/10 shadow-xl z-50 overflow-hidden">
                                <div class="p-3 border-b border-white/10">
                                    <h3 class="font-semibold text-sm">Cart</h3>
                                </div>
                                <div id="cart-dropdown-content">
                                    <div class="p-6 text-center text-white/60">
                                        <div class="animate-pulse">Loading...</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Mobile menu button --}}
                        <button id="menuBtn" type="button"
                                class="lg:hidden inline-flex items-center gap-2 rounded-md px-3 py-2 text-sm hover:bg-white/10">
                            <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                            Menu
                        </button>

                    </div>
                </div>

                {{-- Mobile nav dropdown --}}
                <nav id="mobileNav" class="lg:hidden hidden pb-3 border-t border-white/20 text-sm">
                    <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 hover:bg-white/10">Home</a>
                    <a href="{{ route('products.index') }}" class="block rounded-md px-3 py-2 hover:bg-white/10">Products</a>
                    <a href="{{ route('pages.index') }}" class="block rounded-md px-3 py-2 hover:bg-white/10">Articles</a>
                    <a href="{{ route('home') }}" class="block rounded-md px-3 py-2 hover:bg-white/10">About us</a>
                    <a href="{{ route('contact') }}" class="block rounded-md px-3 py-2 hover:bg-white/10">Contact</a>
                </nav>
            </div>
        </div>

    </div>
</header>
