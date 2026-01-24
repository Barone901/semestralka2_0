<x-layouts.default-layout title="Prihlásenie" type="guest">
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-center">Prihlásenie</h2>
        <p class="text-center text-gray-600 text-sm mt-1">Vitajte späť!</p>
    </div>

    <form method="POST" action="{{ route('login') }}" data-validate>
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                data-rules="required|email"
                autofocus
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
                data-rules="required|minLength:8"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">Zapamätať si ma</span>
            </label>
        </div>

        <div class="flex items-center justify-between mt-6">
            @if (Route::has('password.request'))
                <a class="text-sm text-gray-600 hover:text-gray-900 hover:underline" href="{{ route('password.request') }}">
                    Zabudli ste heslo?
                </a>
            @endif

            <x-button variant="primary">
                Prihlásiť sa
            </x-button>
        </div>
    </form>

    <div class="mt-6 text-center text-sm text-gray-600">
        Nemáte účet?
        <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">
            Zaregistrujte sa
        </a>
    </div>
</x-layouts.default-layout>
