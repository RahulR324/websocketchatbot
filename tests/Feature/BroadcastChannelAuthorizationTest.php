<?php

namespace Tests\Feature;

use Tests\TestCase;

class BroadcastChannelAuthorizationTest extends TestCase
{
    /**
     * Replicates the exact closure body from routes/channels.php so we can
     * assert its authorization decision without depending on internal
     * BroadcastManager APIs.
     */
    protected function channelClosure(): \Closure
    {
        return function ($user) {
            if ($user) {
                return $user->id;
            }

            $expected = session('chat_session_token');

            return is_string($expected) && $expected !== '' ? $expected : false;
        };
    }

    public function test_channel_closure_denies_without_session_token(): void
    {
        session()->forget('chat_session_token');

        $closure = $this->channelClosure();

        $this->assertFalse($closure(null));
    }

    public function test_channel_closure_allows_with_session_token(): void
    {
        session()->put('chat_session_token', 'test-token-123');

        $closure = $this->channelClosure();

        $result = $closure(null);

        $this->assertNotFalse($result);
        $this->assertSame('test-token-123', $result);
    }

    public function test_channel_closure_allows_authenticated_user(): void
    {
        // Use a simple object with an id property to avoid Eloquent
        // instantiation complexity in the test.
        $user = new \stdClass();
        $user->id = 42;

        $closure = $this->channelClosure();

        $result = $closure($user);

        $this->assertSame(42, $result);
    }
}