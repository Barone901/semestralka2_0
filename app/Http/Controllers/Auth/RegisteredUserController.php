<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Zobrazí registračný formulár.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Spracuje registráciu:
     * - validácia je v RegisterRequest
     * - vytvorí usera
     * - priradí rolu "customer" (Spatie)
     * - prihlási usera
     * - presmeruje na dashboard
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make((string) $validated['password']),
        ]);

        $user->assignRole('customer');

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
