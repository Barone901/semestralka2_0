<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller pre kontaktny formular.
 */
class ContactController extends Controller
{
    /**
     * Zobrazi kontaktny formular.
     */
    public function index(): View
    {
        return view('pages.contact.index');
    }

    /**
     * Spracuje odoslanie kontaktneho formulara s validaciou.
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        return redirect()->route('contact')
            ->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
