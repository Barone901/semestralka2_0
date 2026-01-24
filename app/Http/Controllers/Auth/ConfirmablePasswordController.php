<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConfirmablePasswordController extends Controller
{
    /**
     * Zobrazí stránku "Potvrď heslo".
     * (Používa sa pred citlivými akciami – napr. zmena emailu, zmazanie účtu…)
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Overí zadané heslo:
     * - ak je zlé → validačná chyba
     * - ak je OK → uloží čas potvrdenia do session
     * - presmeruje na intended alebo dashboard
     */
    public function store(Request $request): RedirectResponse
    {
        $valid = Auth::guard('web')->validate([
            'email' => $request->user()->email,
            'password' => $request->password,
        ]);

        if (!$valid) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }

        $request->session()->put('auth.password_confirmed_at', time());

        return redirect()->intended(route('dashboard', absolute: false));
    }
}
