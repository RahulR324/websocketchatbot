<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use App\Services\FallbackAiService;
use App\Services\GeminiService;
use App\Services\GroqService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FallbackAiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.gemini.api_key', 'test-gemini-key');
        config()->set('services.gemini.url', 'https://generativelanguage.googleapis.com/v1beta/models/');
        config()->set('services.gemini.model', 'gemini-flash-latest');
        config()->set('services.gemini.max_retries', 0);

        config()->set('services.groq.api_key', 'test-groq-key');
        config()->set('services.groq.url', 'https://api.groq.com/openai/v1/chat/completions');
        config()->set('services.groq.model', 'llama-3.1-8b-instant');
        config()->set('services.groq.max_retries', 0);
    }

    public function test_fallback_uses_groq_when_gemini_fails(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([], 503),
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'Groq answer']],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\FallbackAiService::class);
        $result = $service->ask('hello');

        $this->assertSame('Groq answer', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.groq.com');
        });
    }

    public function test_fallback_throws_when_both_providers_fail(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([], 503),
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([], 503),
        ]);

        $service = app(\App\Services\FallbackAiService::class);

        $this->expectException(AiProviderException::class);

        $service->ask('hello');
    }

    public function test_fallback_uses_gemini_when_available(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'Gemini answer']]]],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\FallbackAiService::class);
        $result = $service->ask('hello');

        $this->assertSame('Gemini answer', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'generativelanguage.googleapis.com');
        });
    }
}
