<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use App\Services\GroqService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class GroqServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.groq.api_key', 'test-groq-key');
        config()->set('services.groq.url', 'https://api.groq.com/openai/v1/chat/completions');
        config()->set('services.groq.model', 'llama-3.1-8b-instant');
        config()->set('services.groq.max_retries', 0);
    }

    public function test_groq_service_uses_config_not_env_directly(): void
    {
        config()->set('services.groq.model', 'groq-test-model');

        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => 'ok']],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\GroqService::class);
        $result = $service->ask('hello');

        $this->assertSame('ok', $result);

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return $body['model'] === 'groq-test-model'
                && str_contains($body['messages'][0]['content'] ?? '', 'hello');
        });
    }

    public function test_groq_service_returns_quiz_from_fallback(): void
    {
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([
                'choices' => [
                    ['message' => ['content' => '{"question":"Q","options":["A","B","C","D"],"answer":"A"}']],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\GroqService::class);
        $result = $service->generateQuiz('PHP');

        $this->assertIsArray($result);
        $this->assertSame('Q', $result['question']);
    }

    public function test_groq_service_throws_on_failure(): void
    {
        Http::fake([
            'https://api.groq.com/openai/v1/chat/completions' => Http::response([], 503),
        ]);

        $service = app(\App\Services\GroqService::class);

        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessageMatches('/unavailable/');

        $service->ask('hello');
    }
}
