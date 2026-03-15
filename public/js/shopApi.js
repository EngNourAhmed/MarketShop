function tradyNormalizeApiBaseUrl(baseUrl) {
    if (!baseUrl) return baseUrl;

    const trimmed = String(baseUrl).replace(/\/+$/, '');
    if (trimmed.endsWith('/api')) return trimmed;
    return `${trimmed}/api`;
}

function tradyResolveApiBaseUrl() {
    const fromStorage = localStorage.getItem('TRADY_API_BASE_URL');
    if (fromStorage) return tradyNormalizeApiBaseUrl(fromStorage);

    const protocol = window.location?.protocol;
    const origin = window.location?.origin;
    const port = String(window.location?.port ?? '');

    // If opened via file:// or no origin, use Laravel default.
    if (!origin || origin === 'null' || protocol === 'file:') {
        return tradyNormalizeApiBaseUrl('http://127.0.0.1:8000/api');
    }

    // If frontend is served from Laravel itself.
    if (port === '8000') {
        return tradyNormalizeApiBaseUrl(`${origin}/api`);
    }

    // Common case: frontend on Apache/Live Server, backend on Laravel :8000.
    return tradyNormalizeApiBaseUrl('http://127.0.0.1:8000/api');
}

function tradyGetAuthToken() {
    return localStorage.getItem('TRADY_AUTH_TOKEN');
}

function tradySetAuthToken(token) {
    if (token) localStorage.setItem('TRADY_AUTH_TOKEN', token);
    else localStorage.removeItem('TRADY_AUTH_TOKEN');
}

const TRADY_API_BASE_URL = tradyResolveApiBaseUrl();

async function tradyFetchJson(path, options = {}) {
    const url = `${TRADY_API_BASE_URL}${path}`;
    const token = tradyGetAuthToken();

    const res = await fetch(url, {
        ...options,
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
            ...(options.headers || {}),
        },
    });

    if (!res.ok) {
        const text = await res.text();
        const err = new Error(`Request failed (${res.status}): ${text}`);
        err.status = res.status;
        err.body = text;
        throw err;
    }

    return res.json();
}

function tradyResource(resourceName) {
    return {
        index: () => tradyFetchJson(`/${resourceName}`),
        store: (data) => tradyFetchJson(`/${resourceName}`, { method: 'POST', body: JSON.stringify(data ?? {}) }),
        show: (id) => tradyFetchJson(`/${resourceName}/${encodeURIComponent(id)}`),
        update: (id, data) => tradyFetchJson(`/${resourceName}/${encodeURIComponent(id)}`, { method: 'PUT', body: JSON.stringify(data ?? {}) }),
        destroy: (id) => tradyFetchJson(`/${resourceName}/${encodeURIComponent(id)}`, { method: 'DELETE' }),
    };
}

window.tradyApi = {
    baseUrl: TRADY_API_BASE_URL,
    setBaseUrl: (baseUrl) => localStorage.setItem('TRADY_API_BASE_URL', tradyNormalizeApiBaseUrl(baseUrl)),
    clearBaseUrl: () => localStorage.removeItem('TRADY_API_BASE_URL'),

    getToken: tradyGetAuthToken,
    setToken: tradySetAuthToken,
    clearToken: () => tradySetAuthToken(null),

    fetchJson: tradyFetchJson,

    auth: {
        login: ({ email, password }) => tradyFetchJson('/login', { method: 'POST', body: JSON.stringify({ email, password }) }),
        register: (data) => tradyFetchJson('/register', { method: 'POST', body: JSON.stringify(data ?? {}) }),
        logout: () => tradyFetchJson('/logout', { method: 'POST' }),
        refresh: () => tradyFetchJson('/refresh', { method: 'POST' }),
    },

    resources: {
        sales: tradyResource('sales'),
        purchases: tradyResource('purchases'),
        products: tradyResource('products'),
        customers: tradyResource('customers'),
        orders: tradyResource('orders'),
        cart: tradyResource('cart'),
        invoices: tradyResource('invoices'),
        payments: tradyResource('payments'),
        expenses: tradyResource('expenses'),
        inventory: tradyResource('inventory'),
        suppliers: tradyResource('suppliers'),
        shipping: tradyResource('shipping'),
        commissions: tradyResource('commissions'),
        cards: tradyResource('cards'),
        withdraws: tradyResource('withdraws'),
        ads: tradyResource('ads'),
        debts: tradyResource('debts'),
        users: tradyResource('users'),
        shippingCompanies: tradyResource('shipping-companies'),
    },
};
