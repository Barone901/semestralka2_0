<x-layouts.default-layout title="Checkout">

    <div class="py-8">

        {{-- Page Header --}}
        <x-ui.page-header title="Checkout" subtitle="Fill in your shipping and billing details to complete your order." />

        {{-- Guest Info Banner --}}
        @if($isGuest)
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 text-blue-800 rounded-lg flex items-center justify-between">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>You are checking out as a guest. <a href="{{ route('login') }}" class="font-semibold underline">Log in</a> or
                        <a href="{{ route('register') }}" class="font-semibold underline">create an account</a> to save your addresses and track orders.</span>
                </div>
            </div>
        @endif

        {{-- Flash Messages --}}
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Checkout Form --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- SHIPPING ADDRESS --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-2 text-indigo-600" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Shipping Address
                        </h2>

                        {{-- Saved Shipping Addresses Selection (only for authenticated users) --}}
                        @if(!$isGuest && $shippingAddresses->isNotEmpty())
                            <div class="mb-6 pb-6 border-b border-gray-200">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Use saved shipping address</label>
                                <select id="shipping-address-select"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors bg-white">
                                    <option value="">-- Enter new address --</option>
                                    @foreach($shippingAddresses as $address)
                                        <option value="{{ $address->id }}"
                                                data-name="{{ $address->full_name }}"
                                                data-email="{{ $address->email }}"
                                                data-phone="{{ $address->phone }}"
                                                data-address="{{ $address->street }}"
                                                data-city="{{ $address->city }}"
                                                data-postal="{{ $address->postal_code }}"
                                                data-country="{{ $address->country }}"
                                                {{ $address->is_default ? 'selected' : '' }}>
                                            {{ $address->full_name }} - {{ $address->street }}, {{ $address->city }}
                                            @if($address->is_default) (Default) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Shipping Name --}}
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                <input type="text" name="shipping_name" id="shipping_name"
                                       value="{{ old('shipping_name', $defaultShippingAddress?->full_name ?? $user?->full_name ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_name') border-red-500 @enderror"
                                       placeholder="John Doe"
                                       required>
                                @error('shipping_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Shipping Email --}}
                            <div>
                                <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                <input type="email" name="shipping_email" id="shipping_email"
                                       value="{{ old('shipping_email', $defaultShippingAddress?->email ?? $user?->email ?? '') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_email') border-red-500 @enderror"
                                       placeholder="john@example.com"
                                       required>
                                @error('shipping_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Shipping Phone --}}
                            <div>
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone *</label>
                                <input type="tel" name="shipping_phone" id="shipping_phone"
                                       value="{{ old('shipping_phone', $defaultShippingAddress?->phone ?? '') }}"
                                       placeholder="+421 XXX XXX XXX"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_phone') border-red-500 @enderror"
                                       required>
                                @error('shipping_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Shipping Postal Code --}}
                            <div>
                                <label for="shipping_postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                                <input type="text" name="shipping_postal_code" id="shipping_postal_code"
                                       value="{{ old('shipping_postal_code', $defaultShippingAddress?->postal_code ?? '') }}"
                                       placeholder="XXX XX"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_postal_code') border-red-500 @enderror"
                                       required>
                                @error('shipping_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Shipping City --}}
                            <div>
                                <label for="shipping_city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                <input type="text" name="shipping_city" id="shipping_city"
                                       value="{{ old('shipping_city', $defaultShippingAddress?->city ?? '') }}"
                                       placeholder="Bratislava"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_city') border-red-500 @enderror"
                                       required>
                                @error('shipping_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>

                            {{-- Shipping Country --}}
                            <div>
                                <label for="shipping_country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <input type="text" name="shipping_country" id="shipping_country"
                                       value="{{ old('shipping_country', $defaultShippingAddress?->country ?? 'Slovakia') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            {{-- Shipping Address --}}
                            <div class="md:col-span-2">
                                <label for="shipping_address" class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                                <input type="text" name="shipping_address" id="shipping_address"
                                       value="{{ old('shipping_address', $defaultShippingAddress?->street ?? '') }}"
                                       placeholder="Main Street 123"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('shipping_address') border-red-500 @enderror"
                                       required>
                                @error('shipping_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- BILLING ADDRESS --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                                <svg class="w-6 h-6 mr-2 text-green-600" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Billing Address
                            </h2>
                            <label class="flex items-center text-sm text-gray-600 cursor-pointer">
                                <input type="checkbox" id="same-as-shipping" class="mr-2 h-4 w-4 text-indigo-600 rounded">
                                Same as shipping
                            </label>
                        </div>

                        {{-- Saved Billing Addresses Selection (only for authenticated users) --}}
                        @if(!$isGuest && $billingAddresses->isNotEmpty())
                            <div class="mb-6 pb-6 border-b border-gray-200" id="billing-address-selector">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Use saved billing address</label>
                                <select id="billing-address-select"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors bg-white">
                                    <option value="">-- Enter new address --</option>
                                    @foreach($billingAddresses as $address)
                                        <option value="{{ $address->id }}"
                                                data-name="{{ $address->full_name }}"
                                                data-email="{{ $address->email }}"
                                                data-phone="{{ $address->phone }}"
                                                data-address="{{ $address->street }}"
                                                data-city="{{ $address->city }}"
                                                data-postal="{{ $address->postal_code }}"
                                                data-country="{{ $address->country }}"
                                                data-company="{{ $address->company_name }}"
                                                data-ico="{{ $address->ico }}"
                                                data-dic="{{ $address->dic }}"
                                                data-icdph="{{ $address->ic_dph }}"
                                                {{ $address->is_default ? 'selected' : '' }}>
                                            {{ $address->full_name }}@if($address->company_name) ({{ $address->company_name }})@endif - {{ $address->street }}, {{ $address->city }}
                                            @if($address->is_default) (Default) @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div id="billing-fields">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Billing Name --}}
                                <div>
                                    <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input type="text" name="billing_name" id="billing_name"
                                           value="{{ old('billing_name', $defaultBillingAddress?->full_name ?? $user?->full_name ?? '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('billing_name') border-red-500 @enderror"
                                           placeholder="John Doe"
                                           required>
                                    @error('billing_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                {{-- Billing Email --}}
                                <div>
                                    <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="billing_email" id="billing_email"
                                           value="{{ old('billing_email', $defaultBillingAddress?->email ?? $user?->email ?? '') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('billing_email') border-red-500 @enderror"
                                           placeholder="john@example.com"
                                           required>
                                    @error('billing_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                {{-- Billing Phone --}}
                                <div>
                                    <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="tel" name="billing_phone" id="billing_phone"
                                           value="{{ old('billing_phone', $defaultBillingAddress?->phone ?? '') }}"
                                           placeholder="+421 XXX XXX XXX"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                </div>

                                {{-- Billing Postal Code --}}
                                <div>
                                    <label for="billing_postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal Code *</label>
                                    <input type="text" name="billing_postal_code" id="billing_postal_code"
                                           value="{{ old('billing_postal_code', $defaultBillingAddress?->postal_code ?? '') }}"
                                           placeholder="XXX XX"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('billing_postal_code') border-red-500 @enderror"
                                           required>
                                    @error('billing_postal_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                {{-- Billing City --}}
                                <div>
                                    <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">City *</label>
                                    <input type="text" name="billing_city" id="billing_city"
                                           value="{{ old('billing_city', $defaultBillingAddress?->city ?? '') }}"
                                           placeholder="Bratislava"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('billing_city') border-red-500 @enderror"
                                           required>
                                    @error('billing_city')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>

                                {{-- Billing Country --}}
                                <div>
                                    <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                    <input type="text" name="billing_country" id="billing_country"
                                           value="{{ old('billing_country', $defaultBillingAddress?->country ?? 'Slovakia') }}"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                </div>

                                {{-- Billing Address --}}
                                <div class="md:col-span-2">
                                    <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Street Address *</label>
                                    <input type="text" name="billing_address" id="billing_address"
                                           value="{{ old('billing_address', $defaultBillingAddress?->street ?? '') }}"
                                           placeholder="Main Street 123"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 @error('billing_address') border-red-500 @enderror"
                                           required>
                                    @error('billing_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                            </div>

                            {{-- Company Info (optional) --}}
                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-medium text-gray-700 mb-4">Company Information (optional)</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="md:col-span-2">
                                        <label for="billing_company_name" class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                        <input type="text" name="billing_company_name" id="billing_company_name"
                                               value="{{ old('billing_company_name', $defaultBillingAddress?->company_name ?? '') }}"
                                               placeholder="Your Company Ltd."
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="billing_ico" class="block text-sm font-medium text-gray-700 mb-2">Company ID (IČO)</label>
                                        <input type="text" name="billing_ico" id="billing_ico"
                                               value="{{ old('billing_ico', $defaultBillingAddress?->ico ?? '') }}"
                                               placeholder="12345678"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="billing_dic" class="block text-sm font-medium text-gray-700 mb-2">Tax ID (DIČ)</label>
                                        <input type="text" name="billing_dic" id="billing_dic"
                                               value="{{ old('billing_dic', $defaultBillingAddress?->dic ?? '') }}"
                                               placeholder="1234567890"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                    <div>
                                        <label for="billing_ic_dph" class="block text-sm font-medium text-gray-700 mb-2">VAT ID (IČ DPH)</label>
                                        <input type="text" name="billing_ic_dph" id="billing_ic_dph"
                                               value="{{ old('billing_ic_dph', $defaultBillingAddress?->ic_dph ?? '') }}"
                                               placeholder="SK1234567890"
                                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ORDER NOTES & PAYMENT --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        {{-- Note --}}
                        <div class="mb-6">
                            <label for="note" class="block text-sm font-medium text-gray-700 mb-2">Order Notes</label>
                            <textarea name="note" id="note" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                      placeholder="Special requests, delivery instructions...">{{ old('note') }}</textarea>
                        </div>

                        {{-- Payment Method --}}
                        <div class="pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Method</h3>
                            <label class="flex items-center p-4 border-2 border-indigo-500 bg-indigo-50 rounded-lg cursor-pointer">
                                <input type="radio" name="payment_method" value="cod" checked class="h-5 w-5 text-indigo-600">
                                <div class="ml-4">
                                    <span class="font-medium text-gray-900">Cash on Delivery</span>
                                    <p class="text-sm text-gray-500">Pay when you receive the package (+€3.00)</p>
                                </div>
                                <span class="ml-auto font-semibold text-indigo-600">€3.00</span>
                            </label>
                        </div>

                        {{-- Submit Button --}}
                        <div class="mt-8">
                            <button type="submit" id="submit-order-btn"
                                    class="w-full bg-indigo-600 text-white py-4 px-6 rounded-lg font-semibold hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 transition-all">
                                <span id="submit-text">Place Order</span>
                                <span id="submit-loading" class="hidden">
                                    <svg class="animate-spin inline-block w-5 h-5 mr-2" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Order Summary --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Order Summary</h2>

                        {{-- Cart Items --}}
                        <div class="space-y-4 max-h-80 overflow-y-auto">
                            @foreach($cart['items'] as $item)
                                <div class="flex items-center gap-4 pb-4 border-b border-gray-100 last:border-0">
                                    @if($item['image_url'])
                                        <img src="{{ $item['image_url'] }}" alt="{{ $item['name'] }}" class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <svg class="w-8 h-8 text-gray-400" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">{{ $item['name'] }}</h4>
                                        <p class="text-sm text-gray-500">{{ $item['quantity'] }}x {{ $item['formatted_price'] }}</p>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $item['formatted_subtotal'] }}</span>
                                </div>
                            @endforeach
                        </div>

                        {{-- Totals --}}
                        <div class="mt-6 pt-6 border-t border-gray-200 space-y-3">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium text-gray-900">{{ $cart['formatted_total'] }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Shipping (COD)</span>
                                <span class="font-medium text-gray-900">€{{ number_format($shippingCost, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold pt-3 border-t border-gray-200">
                                <span class="text-gray-900">Total</span>
                                <span class="text-indigo-600">€{{ number_format($total, 2) }}</span>
                            </div>
                        </div>

                        {{-- Back to cart --}}
                        <a href="{{ route('cart.index') }}" class="mt-6 block text-center text-sm text-indigo-600 hover:text-indigo-800 transition-colors">
                            ← Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <x-slot name="scripts">
        <script>
            // Shipping address selection (only for authenticated users)
            const shippingSelect = document.getElementById('shipping-address-select');
            if (shippingSelect) {
                const selectedShipping = shippingSelect.options[shippingSelect.selectedIndex];
                if (selectedShipping.value) {
                    fillShippingFields(selectedShipping);
                }

                shippingSelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    if (selected.value) {
                        fillShippingFields(selected);
                    }
                });
            }

            // Billing address selection (only for authenticated users)
            const billingSelect = document.getElementById('billing-address-select');
            if (billingSelect) {
                const selectedBilling = billingSelect.options[billingSelect.selectedIndex];
                if (selectedBilling.value) {
                    fillBillingFields(selectedBilling);
                }

                billingSelect.addEventListener('change', function() {
                    const selected = this.options[this.selectedIndex];
                    if (selected.value) {
                        fillBillingFields(selected);
                    }
                });
            }

            // Same as shipping checkbox
            const sameAsShipping = document.getElementById('same-as-shipping');
            const billingFields = document.getElementById('billing-fields');
            const billingSelector = document.getElementById('billing-address-selector');

            sameAsShipping.addEventListener('change', function() {
                if (this.checked) {
                    copyShippingToBilling();
                    if (billingSelector) billingSelector.style.display = 'none';
                } else {
                    if (billingSelector) billingSelector.style.display = 'block';
                }
            });

            function fillShippingFields(option) {
                document.getElementById('shipping_name').value = option.dataset.name || '';
                document.getElementById('shipping_email').value = option.dataset.email || '';
                document.getElementById('shipping_phone').value = option.dataset.phone || '';
                document.getElementById('shipping_address').value = option.dataset.address || '';
                document.getElementById('shipping_city').value = option.dataset.city || '';
                document.getElementById('shipping_postal_code').value = option.dataset.postal || '';
                document.getElementById('shipping_country').value = option.dataset.country || 'Slovakia';

                // If same as shipping is checked, also update billing
                if (sameAsShipping.checked) {
                    copyShippingToBilling();
                }
            }

            function fillBillingFields(option) {
                document.getElementById('billing_name').value = option.dataset.name || '';
                document.getElementById('billing_email').value = option.dataset.email || '';
                document.getElementById('billing_phone').value = option.dataset.phone || '';
                document.getElementById('billing_address').value = option.dataset.address || '';
                document.getElementById('billing_city').value = option.dataset.city || '';
                document.getElementById('billing_postal_code').value = option.dataset.postal || '';
                document.getElementById('billing_country').value = option.dataset.country || 'Slovakia';
                document.getElementById('billing_company_name').value = option.dataset.company || '';
                document.getElementById('billing_ico').value = option.dataset.ico || '';
                document.getElementById('billing_dic').value = option.dataset.dic || '';
                document.getElementById('billing_ic_dph').value = option.dataset.icdph || '';
            }

            function copyShippingToBilling() {
                document.getElementById('billing_name').value = document.getElementById('shipping_name').value;
                document.getElementById('billing_email').value = document.getElementById('shipping_email').value;
                document.getElementById('billing_phone').value = document.getElementById('shipping_phone').value;
                document.getElementById('billing_address').value = document.getElementById('shipping_address').value;
                document.getElementById('billing_city').value = document.getElementById('shipping_city').value;
                document.getElementById('billing_postal_code').value = document.getElementById('shipping_postal_code').value;
                document.getElementById('billing_country').value = document.getElementById('shipping_country').value;
            }

            // Update billing when shipping changes if same-as-shipping is checked
            document.querySelectorAll('[id^="shipping_"]').forEach(field => {
                field.addEventListener('input', function() {
                    if (sameAsShipping.checked) {
                        const billingField = document.getElementById(this.id.replace('shipping_', 'billing_'));
                        if (billingField) {
                            billingField.value = this.value;
                        }
                    }
                });
            });

            // Form submission loading state
            document.getElementById('checkout-form').addEventListener('submit', function() {
                const btn = document.getElementById('submit-order-btn');
                const text = document.getElementById('submit-text');
                const loading = document.getElementById('submit-loading');

                btn.disabled = true;
                text.classList.add('hidden');
                loading.classList.remove('hidden');
            });
        </script>
    </x-slot>

</x-layouts.default-layout>

