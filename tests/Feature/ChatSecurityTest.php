<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class ChatSecurityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('chat-limit:127.0.0.1');
    }

    public function test_send_message_requires_message_field(): void
    {
        $response = $this->postJson('/send-message', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('message');
    }

    public function test_send_message_rejects_oversized_payload(): void
    {
        $response = $this->postJson('/send-message', [
            'message' => str_repeat('a', 2001),
        ]);

        $response->assertStatus(422);
    }

    public function test_send_message_rejects_non_string_message(): void
    {
        $response = $this->postJson('/send-message', [
            'message' => ['not', 'a', 'string'],
        ]);

        $response->assertStatus(422);
    }

    public function test_send_message_rejects_empty_string(): void
    {
        $response = $this->postJson('/send-message', [
            'message' => '   ',
        ]);

        $response->assertStatus(422);
    }

    public function test_send_message_calls_gemini_with_validated_input(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Hello there!']]]],
                ],
            ], 200),
        ]);

        $response = $this->postJson('/send-message', [
            'message' => 'What is PHP?',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'streaming']);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return str_contains($request->url(), 'generativelanguage.googleapis.com')
                && isset($body['contents'][0]['parts'][0]['text']);
        });
    }

    public function test_send_message_returns_user_safe_error_on_gemini_failure(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([], 503),
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([], 503),
        ]);

        $response = $this->postJson('/send-message', [
            'message' => 'Hello',
        ]);

        $response->assertStatus(200);
    }

    public function test_send_message_rate_limit_returns_429_after_threshold(): void
    {
        config()->set('services.chat.rate_limit_per_minute', 3);
        RateLimiter::clear('chat-limit:127.0.0.1');

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'ok']]]],
                ],
            ], 200),
        ]);

        $hit = false;

        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/send-message', [
                'message' => 'Hello number ' . $i,
            ]);

            if ($response->status() === 429) {
                $hit = true;
                break;
            }
        }

        $this->assertTrue($hit, 'Rate limit was not triggered.');
    }

    public function test_reverb_config_endpoint_exposes_only_non_secret_values(): void
    {
        $response = $this->getJson('/reverb/config');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'key',
            'wsHost',
            'wsPort',
            'wssPort',
            'forceTLS',
            'authEndpoint',
        ]);

        $response->assertJsonMissing(['secret']);
    }
}