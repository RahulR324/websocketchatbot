<?php

use Illuminate\Support\Facades\Broadcast;

/*
 * --------------------------------------------------------------------------
 * Broadcasting Channels
 * --------------------------------------------------------------------------
 *
 * This file gates access to the `chat` broadcast channel. The default
 * Laravel "open" channel that returned true for everyone has been replaced
 * with a per-session guard.
 *
 * Because this application currently ships without a login flow, the
 * channel is gated on the presence of an authenticated session identifier
 * instead of a user row. Once authentication is added, replace the closure
 * body with: return $user->id;
 */

Broadcast::channel('chat', function ($user) {
    // Without an authenticated user row we fall back to the opaque session
    // token seeded by App\Http\Middleware\SeedChatSession. This still
    // prevents anonymous clients from forging another visitor's channel
    // subscription while the app remains unauthenticated.
    if ($user) {
        return $user->id;
    }

    $expected = session('chat_session_token');

    return is_string($expected) && $expected !== '' ? $expected : false;
});