/**
 * Frontend Services Index
 *
 * Centrálny export pre všetky API services.
 * Tieto services komunikujú s backend API.
 */

import ApiService from './api';
import ProductService from './productService';
import CartService from './cartService';

export {
    ApiService,
    ProductService,
    CartService,
};

// Default export pre pohodlný prístup
export default {
    Api: ApiService,
    Product: ProductService,
    Cart: CartService,
};

