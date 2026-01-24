<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ProfileController extends Controller
{
    /**
     * Profil už nemá vlastný view.
     * Keď niekto otvorí /profile, presmerujeme ho na /dashboard a skočíme na sekciu #profile.
     */
    public function edit(Request $request): RedirectResponse
    {
        return redirect()->route('dashboard')->withFragment('profile');

    }

    /**
     * Aktualizuje profil používateľa.
     * - validácia je v ProfileUpdateRequest
     * - ak sa zmenil email, vynulujeme email_verified_at (bude treba znova overiť email)
     * - po uložení sa vrátime späť na dashboard do sekcie profilu
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Breeze používa session('status') napr. 'profile-updated'
        return redirect()->route('dashboard')
            ->with('status', 'profile-updated')
            ->withFragment('profile');
    }

    /**
     * Zmaže účet používateľa.
     * - vyžaduje current_password (Breeze)
     * - odhlási používateľa
     * - vymaže usera
     * - resetne session
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
