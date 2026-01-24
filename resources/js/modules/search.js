/**
 * Search Module
 *
 * Vyhľadávanie produktov s autocomplete
 */

import ProductService from '../services/productService';

const Search = {
    input: null,
    results: null,
    debounceTimer: null,

    init() {
        this.input = document.getElementById('search-input');
        this.results = document.getElementById('search-results');

        if (!this.input) return;

        this.bindEvents();
    },

    bindEvents() {
        this.input.addEventListener('input', (e) => {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.search(e.target.value);
            }, 300);
        });

        this.input.addEventListener('focus', () => {
            if (this.input.value.length >= 2) {
                this.showResults();
            }
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (!this.input.contains(e.target) && !this.results?.contains(e.target)) {
                this.hideResults();
            }
        });

        // Keyboard navigation
        this.input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.hideResults();
            }
        });
    },

    async search(query) {
        if (query.length < 2) {
            this.hideResults();
            return;
        }

        try {
            const products = await ProductService.search(query);
            this.renderResults(products);
        } catch (error) {
            console.error('Search error:', error);
        }
    },

    renderResults(products) {
        if (!this.results) {
            this.createResultsContainer();
        }

        if (products.length === 0) {
            this.results.innerHTML = `
                <div class="p-4 text-sm text-gray-500">
                    No results found for this search.
                </div>
            `;
        } else {
            this.results.innerHTML = products.map(product => `
                <a href="${product.url}" class="flex items-center gap-3 p-3 hover:bg-gray-50 transition-colors">
                    <div class="w-12 h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0">
                        ${product.image_url
                            ? `<img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover">`
                            : ''
                        }
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="font-medium text-sm truncate">${product.name}</div>
                        <div class="text-xs text-gray-500">${product.category || ''}</div>
                    </div>
                    <div class="text-sm font-bold">${product.formatted_price}</div>
                </a>
            `).join('');
        }

        this.showResults();
    },

    createResultsContainer() {
        this.results = document.createElement('div');
        this.results.id = 'search-results';
        this.results.className = 'absolute top-full left-0 right-0 mt-1 bg-white border rounded-xl shadow-lg overflow-hidden z-50 hidden';
        this.input.parentElement.style.position = 'relative';
        this.input.parentElement.appendChild(this.results);
    },

    showResults() {
        if (this.results) {
            this.results.classList.remove('hidden');
        }
    },

    hideResults() {
        if (this.results) {
            this.results.classList.add('hidden');
        }
    },
};

export default Search;

