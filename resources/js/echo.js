import Echo from 'laravel-echo';
import Pusher from 'pusher-js';
import axios from 'axios';

window.Pusher = Pusher;

/**
 * Fetch the runtime configuration for Laravel Echo from the server.
 *
 * Vite inlines import.meta.env values at build time, which means any
 * value baked into the asset is frozen for every environment the bundle
 * serves. By requesting the config over HTTP at runtime the same build
 * works for local, staging and production without rebuilding.
 */
async function fetchEchoConfig() {
    try {
        const client = window.axios || axios;
        const response = await client.get('/reverb/config');
        return response.data;
    } catch (error) {
        console.warn('Failed to fetch Reverb config, falling back to defaults', error);
        return null;
    }
}

function createEcho(config) {
    const fallback = {
        key: import.meta.env.VITE_REVERB_APP_KEY || '',
        wsHost: import.meta.env.VITE_REVERB_HOST || window.location.hostname || '127.0.0.1',
        wsPort: Number(import.meta.env.VITE_REVERB_PORT) || 8080,
        wssPort: Number(import.meta.env.VITE_REVERB_WSS_PORT) || 443,
        forceTLS: (import.meta.env.VITE_REVERB_SCHEME || 'http') === 'https',
        authEndpoint: import.meta.env.VITE_REVERB_AUTH_ENDPOINT || '/reverb/auth',
    };

    const c = config || fallback;

    return new Echo({
        broadcaster: 'reverb',
        key: c.key,
        wsHost: c.wsHost,
        wsPort: c.wsPort,
        wssPort: c.wssPort,
        forceTLS: c.forceTLS,
        enabledTransports: c.forceTLS ? ['ws', 'wss'] : ['ws'],
        authEndpoint: c.authEndpoint,
    });
}

// Initial Echo instance with env/fallback settings
window.Echo = createEcho(null);

// When DOM loads, fetch runtime configuration and notify listeners
window.addEventListener('DOMContentLoaded', async () => {
    const config = await fetchEchoConfig();
    if (config && config.key) {
        if (window.Echo && typeof window.Echo.disconnect === 'function') {
            window.Echo.disconnect();
        }
        window.Echo = createEcho(config);
        window.dispatchEvent(new CustomEvent('echo:ready', { detail: window.Echo }));
    }
});
