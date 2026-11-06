import { API_CONFIG } from './config.js';

/**
 * ApiClient, pembungkus fetch untuk konsumsi REST API.
 * Menangani JSON parsing, error handling, dan timeout.
 */
class ApiClient {
    constructor(baseUrl) {
        this.baseUrl = baseUrl;
    }

    async request(endpoint, options = {}) {
        const url = `${this.baseUrl}${endpoint}`;
        const config = {
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            ...options,
        };

        // Konversi body object ke JSON string
        if (config.body && typeof config.body === 'object') {
            config.body = JSON.stringify(config.body);
        }

        // Batas waktu permintaan
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), API_CONFIG.TIMEOUT);
        config.signal = controller.signal;

        try {
            const response = await fetch(url, config);

            let data;
            try {
                data = await response.json();
            } catch {
                throw new Error(
                    `Server mengembalikan respons tidak valid (HTTP ${response.status})`
                );
            }

            if (!response.ok) {
                throw new Error(data.message || `HTTP ${response.status}`);
            }

            return data;
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Permintaan melebihi batas waktu. Coba lagi.');
            }
            // TypeError biasanya karena network error (server down)
            if (error instanceof TypeError) {
                throw new Error(
                    'Tidak dapat terhubung ke API. Pastikan backend berjalan di ' +
                    API_CONFIG.BASE_URL
                );
            }
            throw error;
        } finally {
            clearTimeout(timer);
        }
    }

    get(endpoint)          { return this.request(endpoint, { method: 'GET' }); }
    post(endpoint, body)   { return this.request(endpoint, { method: 'POST', body }); }
    put(endpoint, body)    { return this.request(endpoint, { method: 'PUT', body }); }
    delete(endpoint)       { return this.request(endpoint, { method: 'DELETE' }); }
}

export const api = new ApiClient(API_CONFIG.BASE_URL);

/** Service untuk resource Buku */
export const booksApi = {
    list: ()               => api.get('/books'),
    detail: (id)           => api.get(`/books/${id}`),
    create: (data)         => api.post('/books', data),
    update: (id, data)     => api.put(`/books/${id}`, data),
    destroy: (id)          => api.delete(`/books/${id}`),
};

/** Service untuk resource Kategori */
export const categoriesApi = {
    list: ()               => api.get('/categories'),
    detail: (id)           => api.get(`/categories/${id}`),
    create: (data)         => api.post('/categories', data),
    update: (id, data)     => api.put(`/categories/${id}`, data),
    destroy: (id)          => api.delete(`/categories/${id}`),
};