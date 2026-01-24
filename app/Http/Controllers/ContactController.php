<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Zobrazí kontaktný formulár.
     */
    public function index(): View
    {
        return view('pages.contact.index');
    }

    /**
     * Spracuje odoslanie kontaktného formulára.
     *
     * Tu teraz robíš iba:
     * - validáciu
     * - redirect so success hláškou
     *
     * Neskôr vieš doplniť:
     * - odoslanie emailu (Mail::to(...)->send(...))
     * - uloženie do DB
     * - poslanie do CRM
     */
    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|min:10|max:5000',
        ]);

        // $validated máš pripravené, aby si ho vedel použiť na email/DB.

        return redirect()->route('contact')
            ->with('success', 'Thank you for your message! We will get back to you soon.');
    }
}
