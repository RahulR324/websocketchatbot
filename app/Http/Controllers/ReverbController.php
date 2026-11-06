<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;

class ReverbController extends Controller
{
    /**
     * Return the public Echo configuration for the current request.
     *
     * Only non-secret values are exposed here. The app key is considered
     * public by the Reverb protocol; the app secret never leaves the server.
     */
    public function config()
    {
        return response()->json([
            'key' => config('reverb.apps.apps.0.key'),
            'wsHost' => config('reverb.apps.apps.0.options.host'),
            'wsPort' => $this->wsPort(),
            'wssPort' => $this->wssPort(),
            'forceTLS' => $this->useTls(),
            'authEndpoint' => '/reverb/auth',
        ]);
    }

    /**
     * Authorize a private/presence channel subscription.
     */
    public function auth(Request $request)
    {
        return Broadcast::auth($request);
    }

    protected function wsPort(): int
    {
        return $this->useTls() ? 443 : (int) config('reverb.apps.apps.0.options.port', 8080);
    }

    protected function wssPort(): int
    {
        return 443;
    }

    protected function useTls(): bool
    {
        return config('reverb.apps.apps.0.options.scheme', 'https') === 'https';
    }
}