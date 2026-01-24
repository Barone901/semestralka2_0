/**
 * ApiService
 * - jednotný wrapper pre všetky API volania (/api/...)
 * - CSRF sa berie z meta tagu
 */
const API_BASE_URL = '/api';

const getCsrfToken = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") ?? "";

const ApiService = {
    async request(endpoint, options = {}) {
        const url = endpoint.startsWith("http")
            ? endpoint
            : `${API_BASE_URL}${endpoint}`;

        const defaultOptions = {
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": getCsrfToken(),
            },
        };

        const response = await fetch(url, {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...options.headers,
            },
        });

        // Niekedy môže server vrátiť prázdne body → poistka
        const text = await response.text();
        const data = text ? JSON.parse(text) : null;

        if (!response.ok) {
            const err = new Error((data && data.message) || "Nastala chyba");
            err.status = response.status;
            err.data = data;
            throw err;
        }

        return data;
    },

    get(endpoint, params = {}) {
        const url = new URL(`${API_BASE_URL}${endpoint}`, window.location.origin);
        Object.entries(params).forEach(([k, v]) => {
            if (v !== undefined && v !== null && v !== "") url.searchParams.append(k, v);
        });

        // tu už URL obsahuje /api, takže voláme request cez url.pathname+search bez prepisu base
        return this.request(url.pathname.replace("/api", "") + url.search, { method: "GET" });
    },

    post(endpoint, data = {}) {
        return this.request(endpoint, { method: "POST", body: JSON.stringify(data) });
    },
};

export default ApiService;
