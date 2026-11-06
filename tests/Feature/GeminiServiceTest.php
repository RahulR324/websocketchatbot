<?php

namespace Tests\Feature;

use App\Exceptions\AiProviderException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class GeminiServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.gemini.api_key', 'test-api-key');
        config()->set('services.gemini.url', 'https://generativelanguage.googleapis.com/v1beta/models/');
        config()->set('services.gemini.model', 'gemini-flash-latest');
        config()->set('services.gemini.max_retries', 0);
    }

    public function test_gemini_service_uses_config_not_env_directly(): void
    {
        config()->set('services.gemini.model', 'gemini-test-model');

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-test-model:generateContent' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'ok']]]],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\GeminiService::class);
        $result = $service->ask('hello');

        $this->assertSame('ok', $result);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'gemini-test-model');
        });
    }

    public function test_gemini_service_does_not_log_user_prompt(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'response']]]],
                ],
            ], 200),
        ]);

        // Capture log output in a temporary file and assert the user prompt
        // never appears in it.
        $logFile = tempnam(sys_get_temp_dir(), 'gemini_test_log');
        config()->set('logging.channels.test', [
            'driver' => 'single',
            'path' => $logFile,
            'level' => 'debug',
        ]);
        config()->set('logging.default', 'test');

        $service = app(\App\Services\GeminiService::class);
        $service->ask('super secret user question');

        $logContent = file_get_contents($logFile);
        @unlink($logFile);

        $this->assertStringNotContainsString('super secret user question', $logContent);
    }

    public function test_gemini_service_handles_429_with_user_safe_message(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([], 429),
        ]);

        $service = app(\App\Services\GeminiService::class);

        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessageMatches('/usage limit/');

        $service->ask('hello');
    }

    public function test_gemini_service_handles_403_with_user_safe_message(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([], 403),
        ]);

        $service = app(\App\Services\GeminiService::class);

        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessageMatches('/Access denied/');

        $service->ask('hello');
    }

    public function test_gemini_service_handles_503_with_user_safe_message(): void
    {
        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([], 503),
        ]);

        $service = app(\App\Services\GeminiService::class);

        $this->expectException(AiProviderException::class);
        $this->expectExceptionMessageMatches('/unavailable/');

        $service->ask('hello');
    }

    public function test_gemini_service_enforces_max_output_tokens(): void
    {
        config()->set('services.gemini.max_output_tokens', 512);

        Http::fake([
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent' => Http::response([
                'candidates' => [
                    ['content' => ['parts' => [['text' => 'ok']]]],
                ],
            ], 200),
        ]);

        $service = app(\App\Services\GeminiService::class);
        $service->ask('hello');

        Http::assertSent(function ($request) {
            $body = json_decode($request->body(), true);

            return ($body['generationConfig']['maxOutputTokens'] ?? null) === 512;
        });
    }
}