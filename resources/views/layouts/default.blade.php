<!doctype html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Eshop')</title>

@vite(['resources/css/app.css', 'resources/js/app.js'])
@stack('head')
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
{{-- Header --}}
<header class="bg-white border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <a href="{{ route('home') }}" class="font-bold text-lg">
            Eshop
        </a>

        <nav class="flex items-center gap-4">
            <a href="#" class="text-sm hover:underline">Produkty</a>
            <a href="#" class="text-sm hover:underline">O nás</a>

            @auth
                <a href="{{ route('dashboard') }}" class="text-sm hover:underline">
                    Dashboard
                </a>

                <span class="text-sm text-gray-600">
                        {{ auth()->user()->name }}
                    </span>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm hover:underline text-red-600">
                        Odhlásiť
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="text-sm hover:underline">
                    Prihlásenie
                </a>
                <a href="{{ route('register') }}" class="text-sm hover:underline">
                    Registrácia
                </a>
            @endauth
        </nav>
    </div>
</header>

{{-- Main --}}
<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    @yield('content')
</main>

{{-- Footer --}}
<footer class="border-t bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600">
        © {{ date('Y') }} Eshop
    </div>
</footer>

@stack('scripts')
</body>
</html>
