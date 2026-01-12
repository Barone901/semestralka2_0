<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Eshop') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{ $head ?? '' }}
</head>
<body class="font-sans antialiased min-h-screen bg-gray-50 text-gray-900">
    @include('partials.header')

    <main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot }}
    </main>

    @include('partials.footer')

    {{ $scripts ?? '' }}
</body>
</html>
