<x-layouts.default-layout title="Registrácia" type="guest">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-center">Registrácia</h2>
        <p class="text-center text-gray-600 text-sm mt-1">Vytvorte si nový účet</p>
    </div>

    <form method="POST" action="{{ route('register') }}" data-validate>
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Meno')" />
            <x-text-input
                id="name"
                class="block mt-1 w-full"
                type="text"
                name="name"
                :value="old('name')"
                data-rules="required|minLength:2|maxLength:255"
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                data-rules="required|email"
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Heslo')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                data-rules="required|password"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <p class="mt-1 text-xs text-gray-500">
                Heslo musí mať min. 8 znakov, veľké a malé písmeno a číslo.
            </p>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Potvrdenie hesla')" />
            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                data-rules="required|match:#password:Heslá sa nezhodujú."
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6">
            <x-button variant="primary" class="w-full justify-center">
                Zaregistrovať sa
            </x-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        Už máte účet?
        <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">
            Prihláste sa
        </a>
    </div>
</x-layouts.default-layout>
