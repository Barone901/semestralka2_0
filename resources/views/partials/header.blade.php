<header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

        <!-- Top row -->
        <div class="h-16 flex items-center justify-between">

            <a href="{{ route('home') }}" class="flex items-center gap-2 font-bold tracking-tight">
                <img src="{{ asset('images/triumfLogo.png') }}" alt="Eshop logo" class="h-10 w-auto" />
            </a>

            <!-- Desktop nav -->
            <nav class="hidden md:flex items-center gap-6 text-sm text-gray-700">
                <a href="{{ route('home') }}" class="hover:text-black">Domov</a>
                <a href="#" class="hover:text-black">Produkty</a>
                <a href="#" class="hover:text-black">O nás</a>
                <a href="#" class="hover:text-black">Kontakt</a>
            </nav>

            <div class="flex items-center gap-3">
                <!-- Mobile menu button (len na mobile) -->
                <button id="menuBtn" type="button"
                        class="md:hidden inline-flex items-center rounded-xl border px-3 py-2 text-sm hover:bg-gray-50">
                    Menu
                </button>

                <a href="#" class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm hover:bg-gray-50">
                    🛒 <span class="hidden sm:inline">Košík</span>
                </a>

                @auth
                    <a href="{{ route('dashboard') }}" class="hidden sm:inline-flex rounded-xl bg-black px-3 py-2 text-sm text-white hover:opacity-90">
                        Dashboard
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            Odhlásiť
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-3 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        Prihlásiť
                    </a>
                    <a href="{{ route('register') }}" class="hidden sm:inline-flex rounded-xl bg-black px-3 py-2 text-sm text-white hover:opacity-90">
                        Registrácia
                    </a>
                @endauth
            </div>
        </div>

        <!-- Mobile roleta (len mobile) -->
        <nav id="mobileNav" class="md:hidden hidden pb-4 text-sm text-gray-700">
            <a href="{{ route('home') }}" class="block rounded-lg px-3 py-2 hover:bg-gray-50">Domov</a>
            <a href="#" class="block rounded-lg px-3 py-2 hover:bg-gray-50">Produkty</a>
            <a href="#" class="block rounded-lg px-3 py-2 hover:bg-gray-50">O nás</a>
            <a href="#" class="block rounded-lg px-3 py-2 hover:bg-gray-50">Kontakt</a>
        </nav>

    </div>
</header>
