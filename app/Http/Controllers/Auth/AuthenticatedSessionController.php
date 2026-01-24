<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Zobrazí prihlasovaciu stránku (login form).
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Spracuje prihlásenie:
     * - overí údaje (LoginRequest->authenticate())
     * - zregeneruje session (ochrana proti session fixation)
     * - presmeruje na stránku, ktorú user chcel (intended), inak na dashboard
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Odhlási používateľa:
     * - logout
     * - invalidácia session
     * - nový CSRF token
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
