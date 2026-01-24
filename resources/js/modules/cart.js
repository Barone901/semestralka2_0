/**
 * Cart Module
 *
 * Správa nákupného košíka - pridávanie, odoberanie, aktualizácia
 */

import CartService from '../services/cartService';
import Toast from './toast';

const Cart = {
    countElement: null,
    dropdownElement: null,
    dropdownContent: null,
    isDropdownOpen: false,

    init() {
        this.countElement = document.getElementById('cart-count');
        this.dropdownElement = document.getElementById('cart-dropdown');
        this.dropdownContent = document.getElementById('cart-dropdown-content');
        this.bindEvents();
        this.refresh();
    },

    bindEvents() {
        document.addEventListener('click', (e) => {
            const addBtn = e.target.closest('[data-add-to-cart]');
            if (addBtn) {
                e.preventDefault();
                const productId = addBtn.dataset.addToCart;
                const quantity = addBtn.dataset.quantity || 1;
                this.add(productId, quantity);
            }

            const removeBtn = e.target.closest('[data-remove-from-cart]');
            if (removeBtn) {
                e.preventDefault();
                const productId = removeBtn.dataset.removeFromCart;
                this.remove(productId);
            }

            const clearBtn = e.target.closest('[data-clear-cart]');
            if (clearBtn) {
                e.preventDefault();
                this.clear();
            }
        });

        document.addEventListener('change', (e) => {
            if (e.target.matches('[data-cart-quantity]')) {
                const productId = e.target.dataset.cartQuantity;
                const quantity = parseInt(e.target.value) || 0;
                this.update(productId, quantity);
            }
        });

        this.initDropdown();
    },

    initDropdown() {
        const cartWrapper = document.getElementById('cart-wrapper');
        const accountDropdown = document.getElementById('account-dropdown');

        if (cartWrapper && this.dropdownElement) {
            let hideTimeout;

            const isAccountOpen = () =>
                accountDropdown && !accountDropdown.classList.contains('hidden');

            const showDropdown = () => {

                if (isAccountOpen()) return;

                clearTimeout(hideTimeout);
                this.dropdownElement.classList.remove('hidden', 'opacity-0', 'translate-y-2');
                this.dropdownElement.classList.add('opacity-100', 'translate-y-0');
                this.isDropdownOpen = true;
            };

            const hideDropdown = () => {
                hideTimeout = setTimeout(() => {
                    this.dropdownElement.classList.add('opacity-0', 'translate-y-2');
                    setTimeout(() => {
                        this.dropdownElement.classList.add('hidden');
                    }, 200);
                    this.isDropdownOpen = false;
                }, 150);
            };

            cartWrapper.addEventListener('mouseenter', showDropdown);
            cartWrapper.addEventListener('mouseleave', hideDropdown);

            this.dropdownElement.addEventListener('mouseenter', () => clearTimeout(hideTimeout));
            this.dropdownElement.addEventListener('mouseleave', hideDropdown);
        }
    },


    async add(productId, quantity = 1) {
        try {
            const data = await CartService.addProduct(parseInt(productId), parseInt(quantity));
            this.updateUI(data.cart);
            Toast.success(data.message);
        } catch (error) {
            Toast.error(error.message);
        }
    },

    async update(productId, quantity) {
        try {
            const data = await CartService.updateQuantity(parseInt(productId), parseInt(quantity));
            this.updateUI(data.cart);
            Toast.success(data.message);
        } catch (error) {
            Toast.error(error.message);
        }
    },

    async remove(productId) {
        try {
            const data = await CartService.removeProduct(parseInt(productId));
            this.updateUI(data.cart);
            Toast.success(data.message);
        } catch (error) {
            Toast.error(error.message);
        }
    },

    async clear() {
        try {
            const data = await CartService.clear();
            this.updateUI(data.cart);
            Toast.success(data.message);
        } catch (error) {
            Toast.error(error.message);
        }
    },

    async refresh() {
        try {
            const data = await CartService.getCart();
            this.updateUI(data);
        } catch (error) {
            console.error('Failed to refresh cart:', error);
        }
    },

    updateUI(cart) {
        if (this.countElement) {
            this.countElement.textContent = cart.count;
            this.countElement.classList.toggle('hidden', cart.count === 0);
        }

        if (this.dropdownContent) {
            this.renderDropdown(cart);
        }

        const cartPageContent = document.getElementById('cart-page-content');
        if (cartPageContent) {
            this.renderCartPage(cart, cartPageContent);
        }

        document.dispatchEvent(new CustomEvent('cart:updated', { detail: cart }));
    },

    renderDropdown(cart) {
        if (cart.items.length === 0) {
            this.dropdownContent.innerHTML = `
                <div class="p-6 text-center text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <p class="text-sm">Your cart is empty</p>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.slice(0, 4).map(item => `
            <div class="flex gap-3 p-3 hover:bg-gray-50">
                <div class="w-14 h-14 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    ${item.image_url
            ? `<img src="${item.image_url}" alt="${item.name}" class="w-full h-full object-cover">`
            : '<div class="w-full h-full flex items-center justify-center text-gray-300">📦</div>'
        }
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${item.url}" class="text-sm font-medium hover:underline line-clamp-1">${item.name}</a>
                    <div class="text-xs text-gray-500">${item.quantity}× ${item.formatted_price}</div>
                    <div class="text-sm font-semibold">${item.formatted_subtotal}</div>
                </div>
                <button data-remove-from-cart="${item.id}" class="text-gray-400 hover:text-red-500 p-1">
                    <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        `).join('');

        const moreItems = cart.items.length > 4 ? `
            <div class="px-3 py-2 text-xs text-gray-500 text-center border-t">
                + ${cart.items.length - 4} more items
            </div>
        ` : '';

        this.dropdownContent.innerHTML = `
            <div class="max-h-72 overflow-y-auto divide-y">
                ${itemsHtml}
                ${moreItems}
            </div>
            <div class="p-3 border-t bg-gray-50">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-sm text-gray-600">Total:</span>
                    <span class="text-lg font-bold">${cart.formatted_total}</span>
                </div>
                <div class="flex gap-2">
                    <a href="/cart" class="flex-1 text-center py-2 px-3 border rounded-lg text-sm hover:bg-white transition-colors">
                        View Cart
                    </a>
                    <a href="/checkout" class="flex-1 text-center py-2 px-3 bg-black text-white rounded-lg text-sm hover:opacity-90 transition-opacity">
                        Checkout
                    </a>
                </div>
            </div>
        `;
    },

    renderCartPage(cart, container) {
        if (cart.items.length === 0) {
            container.innerHTML = `
                <div class="text-center py-16">
                    <svg class="w-20 h-20 mx-auto mb-4 text-gray-300" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <h2 class="text-xl font-semibold mb-2">Your cart is empty</h2>
                    <p class="text-gray-500 mb-6">Add some items to your cart and come back.</p>
                    <a href="/" class="inline-flex items-center gap-2 bg-black text-white px-6 py-3 rounded-xl hover:opacity-90 transition-opacity">
                        <svg class="w-5 h-5" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Continue Shopping
                    </a>
                </div>
            `;
            return;
        }

        const itemsHtml = cart.items.map(item => `
            <div class="flex gap-4 p-4 bg-white rounded-xl border">
                <div class="w-24 h-24 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                    ${item.image_url
            ? `<img src="${item.image_url}" alt="${item.name}" class="w-full h-full object-cover">`
            : '<div class="w-full h-full flex items-center justify-center text-gray-300 text-2xl">📦</div>'
        }
                </div>
                <div class="flex-1 min-w-0">
                    <a href="${item.url}" class="font-semibold hover:underline">${item.name}</a>
                    <div class="text-sm text-gray-500 mt-1">Price: ${item.formatted_price}</div>
                    <div class="flex items-center gap-3 mt-3">
                        <div class="flex items-center border rounded-lg">
                            <button
                                type="button"
                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                onclick="Eshop.Cart.update(${item.id}, ${item.quantity - 1})"
                            >−</button>
                            <input
                                type="number"
                                value="${item.quantity}"
                                min="0"
                                max="99"
                                data-cart-quantity="${item.id}"
                                class="w-12 h-8 text-center text-sm border-x focus:outline-none"
                            />
                            <button
                                type="button"
                                class="w-8 h-8 flex items-center justify-center hover:bg-gray-100 transition-colors"
                                onclick="Eshop.Cart.update(${item.id}, ${item.quantity + 1})"
                            >+</button>
                        </div>
                        <button
                            data-remove-from-cart="${item.id}"
                            class="text-red-500 hover:text-red-700 text-sm flex items-center gap-1"
                        >
                            <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Remove
                        </button>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-lg font-bold">${item.formatted_subtotal}</div>
                </div>
            </div>
        `).join('');

        container.innerHTML = `
            <div class="grid lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Items in Cart (${cart.count})</h2>
                        <button data-clear-cart class="text-sm text-red-500 hover:text-red-700 flex items-center gap-1">
                            <svg class="w-4 h-4" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Clear Cart
                        </button>
                    </div>
                    ${itemsHtml}
                </div>
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl border p-6 sticky top-24">
                        <h3 class="font-semibold mb-4">Order Summary</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span>${cart.formatted_total}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="text-green-600">Free</span>
                            </div>
                            <div class="border-t pt-3 flex justify-between text-lg font-bold">
                                <span>Total</span>
                                <span>${cart.formatted_total}</span>
                            </div>
                        </div>
                        <a href="/checkout" class="mt-6 w-full block text-center bg-black text-white py-3 px-4 rounded-xl hover:opacity-90 transition-opacity">
                            Proceed to Checkout
                        </a>
                        <a href="/" class="mt-3 w-full block text-center border py-3 px-4 rounded-xl hover:bg-gray-50 transition-colors">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        `;
    },
};

export default Cart;
