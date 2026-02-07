<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    {{-- Kodovanie stranky (UTF-8) --}}
    <meta charset="UTF-8">

    {{-- Responsivne nastavenie pre mobily (spravna sirka a zoom) --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF token pre formulare a ajax requesty (ochrana) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Nazov stranky: pouzije $title ak existuje, inak nazov aplikacie z configu --}}
    <title>{{ $title ?? config('app.name', 'Eshop') }}</title>

    {{-- Preconnect na CDN fontov (rychlejsie nacitanie) --}}
    <link rel="preconnect" href="https://fonts.bunny.net">

    {{-- Nacitanie fontu Figtree --}}
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    {{-- Vite build: tailwind css + tvoj app js --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Volitelne: extra veci do head (napr. meta tagy, extra css na konkretnu stranku) --}}
    {{ $head ?? '' }}
</head>

{{--
    Body triedy:
    - font-sans + antialiased = krajsie vykreslenie textu
    - min-h-screen = minimalne vyska celej obrazovky
    - bg-gray-50 + text-gray-900 = zakladne farby
    - flex flex-col = layout do stlpca (header hore, footer dole)
--}}
<body class="font-sans antialiased min-h-screen flex flex-col">

{{-- Header / navbar (spolocny pre vsetky stranky) --}}
@include('components.layouts.partials.header')

{{-- Prepnutie layoutu podla $type --}}
@if($type === 'guest')
    {{--
        Guest layout:
        - pouziva sa na login/register
        - obsah je vycentrovany
        - uzsia karta, aby to vyzeralo ako formular
    --}}
    <div class="flex-grow flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div class="card w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{-- $slot = obsah konkretnej stranky --}}
            {{ $slot }}
        </div>
    </div>

@elseif($type === 'app')
    {{--
        App layout:
        - normalne stranky aplikacie
        - moze mat volitelny $header nad obsahom
    --}}
    @isset($header)
        <header class="card bg-white shadow">
            {{--
                Wrapper sirky (sirsi layout):
                - max-w-screen-2xl = vacsia max sirka stranky
                - mx-auto = vycentruje kontajner
                - px-* = padding podla velkosti obrazovky
            --}}
            <div class="max-w-screen-2xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

    {{--
        Hlavny obsah:
        - max-w-screen-2xl = sirsi layout
        - w-full + mx-auto = roztiahne sa do max sirky a vycentruje
        - py-8 = odsadenie zhora/zdola
        - flex-grow = aby footer ostal dole
    --}}
    <main class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        {{ $slot }}
    </main>

@else
    {{--
        Default layout:
        - fallback ked $type nie je nastaveny
        - rovnaka sirka ako app layout
    --}}
    <main class="mx-auto max-w-screen-2xl px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full">
        {{ $slot }}
    </main>
@endif

{{-- Footer (spolocny pre vsetky stranky) --}}
@include('components.layouts.partials.footer')

{{-- Volitelne skripty na konci (napr. JS len pre konkretnu stranku) --}}
{{ $scripts ?? '' }}
</body>
</html>
