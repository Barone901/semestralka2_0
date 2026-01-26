<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Controller pre spravu adries pouzivatela (dorucenie a fakturacia).
 */
class UserAddressController extends Controller
{
    /**
     * Zobrazi zoznam vsetkych adries pouzivatela.
     */
    public function index(): View
    {
        $user = Auth::user();

        $addresses = $user->addresses()
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        $shippingAddresses = $addresses->where('type', 'shipping');
        $billingAddresses = $addresses->where('type', 'billing');

        return view('pages.addresses.index', [
            'addresses' => $addresses,
            'shippingAddresses' => $shippingAddresses,
            'billingAddresses' => $billingAddresses,
        ]);
    }

    /**
     * Zobrazi formular pre vytvorenie novej adresy.
     */
    public function create(Request $request): View
    {
        $type = (string) $request->get('type', 'shipping');

        return view('pages.addresses.create', [
            'type' => $type,
        ]);
    }

    /**
     * Ulozi novu adresu a nastavi ako predvolenu ak je pozadovane.
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

        if (!empty($validated['is_default'])) {
            $user->addresses()
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        $validated['user_id'] = $user->id;
        $validated['is_default'] = !empty($validated['is_default']);
        $validated['country'] = $validated['country'] ?? 'United States';

        UserAddress::create($validated);

        $typeText = $validated['type'] === 'shipping' ? 'Shipping' : 'Billing';

        return redirect()->route('addresses.index')
            ->with('success', "{$typeText} address has been successfully added.");
    }

    /**
     * Zobrazi formular pre upravu existujucej adresy.
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
     * Aktualizuje existujucu adresu s validaciou vlastnictva.
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
     * Vymaze adresu po overeni vlastnictva.
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
     * Nastavi adresu ako predvolenu pre dany typ.
     */
    public function setDefault(UserAddress $address): RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $user = Auth::user();

        $user->addresses()
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        $address->update(['is_default' => true]);

        $typeText = $address->type === 'shipping' ? 'Shipping' : 'Billing';

        return redirect()->route('addresses.index')
            ->with('success', "{$typeText} address has been set as default.");
    }
}
