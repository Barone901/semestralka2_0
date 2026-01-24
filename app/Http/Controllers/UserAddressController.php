<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserAddressController extends Controller
{
    /**
     * Zobrazí zoznam adries používateľa.
     * (shipping + billing)
     */
    public function index(): View
    {
        $user = Auth::user();

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        // Rozdelíme adresy podľa typu
        $shippingAddresses = $addresses->where('type', 'shipping');
        $billingAddresses = $addresses->where('type', 'billing');

        return view('pages.addresses.index', [
            'addresses' => $addresses,
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
        ]);
    }

    /**
     * Zobrazí formulár na vytvorenie adresy.
     * - type sa posiela cez query (?type=shipping alebo ?type=billing)
     */
    public function create(Request $request): View
    {
        $type = (string) $request->get('type', 'shipping');

        return view('pages.addresses.create', [
            'type' => $type,
        ]);
    }

    /**
     * Uloží novú adresu.
     * - validuje vstupy
     * - ak je označená ako default, zruší default pre ostatné adresy rovnakého typu
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'ico' => 'nullable|string|max:20',
            'dic' => 'nullable|string|max:20',
            'ic_dph' => 'nullable|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Ak je nová adresa default, zrušíme default u ostatných adries rovnakého typu
        if (!empty($validated['is_default'])) {
            $user->addresses()
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        // Priradíme adresu k prihlásenému userovi
        $validated['user_id'] = $user->id;

        // is_default chceme mať ako boolean
        $validated['is_default'] = !empty($validated['is_default']);

        // Default krajina (podľa tvojho pôvodného kódu)
        $validated['country'] = $validated['country'] ?? 'United States';

        UserAddress::create($validated);

        $typeText = $validated['type'] === 'shipping' ? 'Shipping' : 'Billing';

        return redirect()->route('addresses.index')
            ->with('success', "{$typeText} address has been successfully added.");
    }

    /**
     * Zobrazí formulár na úpravu adresy.
     * - kontrolujeme, či adresa patrí prihlásenému userovi
     */
    public function edit(UserAddress $address): View
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        return view('pages.addresses.edit', [
            'address' => $address,
        ]);
    }

    /**
     * Aktualizuje existujúcu adresu.
     * - validuje vstupy
     * - ak sa nastaví ako default, zruší default u ostatných adries rovnakého typu
     */
    public function update(Request $request, UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => 'required|in:shipping,billing',
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'street' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'country' => 'nullable|string|max:100',
            'company_name' => 'nullable|string|max:255',
            'ico' => 'nullable|string|max:20',
            'dic' => 'nullable|string|max:20',
            'ic_dph' => 'nullable|string|max:20',
            'is_default' => 'nullable|boolean',
        ]);

        $user = Auth::user();

        // Ak sa adresa nastavuje ako default, zrušíme default u ostatných adries rovnakého typu
        if (!empty($validated['is_default'])) {
            $user->addresses()
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        $validated['is_default'] = !empty($validated['is_default']);
        $validated['country'] = $validated['country'] ?? 'United States';

        $address->update($validated);

        $typeText = $validated['type'] === 'shipping' ? 'Shipping' : 'Billing';

        return redirect()->route('addresses.index')
            ->with('success', "{$typeText} address has been successfully updated.");
    }

    /**
     * Vymaže adresu.
     * - kontrolujeme vlastníctvo adresy
     */
    public function destroy(UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $typeText = $address->type === 'shipping' ? 'Shipping' : 'Billing';

        $address->delete();

        return redirect()->route('addresses.index')
            ->with('success', "{$typeText} address has been successfully deleted.");
    }

    /**
     * Nastaví adresu ako default (podľa typu shipping/billing).
     * - najprv zrušíme default na všetkých adresách daného typu
     * - potom nastavíme túto ako default
     */
    public function setDefault(UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();

        // Zrušíme default u ostatných adries rovnakého typu
        $user->addresses()
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        // Nastavíme túto adresu ako default
        $address->update(['is_default' => true]);

        $typeText = $address->type === 'shipping' ? 'Shipping' : 'Billing';

        return redirect()->route('addresses.index')
            ->with('success', "{$typeText} address has been set as default.");
    }
}
