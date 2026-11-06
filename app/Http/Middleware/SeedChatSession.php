<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SeedChatSession
{
    /**
     * Handle an incoming request.
     *
     * Without an authentication flow in place this middleware still gives
     * every browser its own opaque session token. That token is what the
     * Reverb channel closure in routes/channels.php checks, so anonymous
     * users can use the chat but cannot forge another visitor's channel
     * subscription.
     */
    public function handle(Request $request, Closure $next)
    {
        if (! session()->has('chat_session_token')) {
            session()->put('chat_session_token', (string) Str::uuid());
        }

        return $next($request);
    }
}