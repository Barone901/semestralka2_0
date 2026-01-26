<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Controller pre potvrdenie hesla pred citlivymi akciami.
 */
class ConfirmablePasswordController extends Controller
{
    /**
     * Zobrazi stranku pre potvrdenie hesla.
     */
    public function show(): View
    {
        return view('auth.confirm-password');
    }

    /**
     * Overi heslo a ulozi cas potvrdenia do session.
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
