/**
 * Product Service
 *
 * API volania pre produkty
 */

import ApiService from './api';

const ProductService = {
    /**
     * Vyhľadávanie produktov.
     */
    async search(query) {
        return ApiService.get('/products/search', { q: query });
    },

    /**
     * Získa detail produktu.
     */
    async getDetail(slug) {
        return ApiService.get(`/products/${slug}`);
    },
};

export default ProductService;

