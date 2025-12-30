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
@include('partials.header')

<main class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-8">
    @yield('content')
</main>

@include('partials.footer')

@stack('scripts')
</body>
</html>
