<?php

namespace App\Services;

use App\Contracts\AiService;
use App\Exceptions\AiProviderException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService implements AiService
{
    protected int $connectTimeout;

    protected int $requestTimeout;

    protected int $maxRetries;

    public function __construct()
    {
        $this->connectTimeout = (int) config('services.gemini.connect_timeout', 5);
        $this->requestTimeout = (int) config('services.gemini.request_timeout', 30);
        $this->maxRetries = (int) config('services.gemini.max_retries', 1);
    }

    /**
     * Send a question to the Gemini teacher model and return the answer text.
     *
     * @throws AiProviderException
     */
    public function ask(string $question): string
    {
        $prompt = $this->buildTeacherPrompt($question);

        $response = $this->postToGemini($prompt);

        if (! $response->successful()) {
            throw $this->providerFailure($response);
        }

        $data = $response->json();

        // Log only status + model metadata, never the user prompt text.
        Log::info('Gemini response received', [
            'status' => $response->status(),
            'model' => config('services.gemini.model'),
        ]);

        return $data['candidates'][0]['content']['parts'][0]['text']
            ?? throw new AiProviderException('Unable to generate response.');
    }

    /**
     * Generate a quiz on the supplied topic and return decoded JSON.
     *
     * @throws AiProviderException
     */
    public function generateQuiz(string $topic): ?array
    {
        $prompt = $this->buildQuizPrompt($topic);

        $response = $this->postToGemini($prompt);

        if (! $response->successful()) {
            throw $this->providerFailure($response);
        }

        $data = $response->json();

        // Log only status + model metadata, never the user prompt text.
        Log::info('Gemini quiz response received', [
            'status' => $response->status(),
            'model' => config('services.gemini.model'),
        ]);

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if (! is_string($text) || $text === '') {
            throw new AiProviderException('Unable to generate quiz.');
        }

        // Strip markdown code fences if present
        $clean = trim($text);
        if (preg_match('/^```(?:json)?\s*(.*?)\s*```$/is', $clean, $matches)) {
            $clean = trim($matches[1]);
        }

        $decoded = json_decode($clean, true);

        if (! is_array($decoded) || empty($decoded)) {
            throw new AiProviderException('Unable to generate quiz.');
        }

        return $decoded;
    }

    /**
     * Build the teacher-mode prompt.
     */
    protected function buildTeacherPrompt(string $question): string
    {
        return "You are an expert teacher.\n"
            ."Rules:\n"
            ."- Use simple, clear language.\n"
            ."- Break topics into short sections with headings/bullets.\n"
            ."- Give a brief example when helpful.\n"
            ."- Keep answers concise (about 120-180 words) unless the user asks for detail.\n\n"
            ."Question:\n{$question}";
    }

    /**
     * Build the quiz-generation prompt.
     */
    protected function buildQuizPrompt(string $topic): string
    {
        return "
        You are an expert teacher.

        Create a quiz on: {$topic}

        Rules:
        - Generate exactly 5 questions.
        - Each question must have exactly 4 options.
        - Only one correct answer.
        - Return ONLY JSON.
        - No markdown.
        - No explanation.

        Format:

        [
            {
                \"question\":\"Question\",
                 \"options\":[
                     \"A\",
                     \"B\",
                     \"C\",
                     \"D\"
                 ],
                 \"answer\":\"Correct Option\"
             }
        ]
        ";
    }

    /**
     * POST to the Gemini API with bounded retries.
     */
    protected function postToGemini(string $prompt): Response
    {
        $baseUrl = config('services.gemini.url');
        $model = config('services.gemini.model');
        $apiKey = config('services.gemini.api_key');

        $endpoint = rtrim($baseUrl, '/').'/'.ltrim($model, '/').':generateContent';

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'maxOutputTokens' => (int) config('services.gemini.max_output_tokens', 600),
                'temperature' => (float) config('services.gemini.temperature', 0.5),
            ],
        ];

        $attempt = 0;
        $lastResponse = null;

        while ($attempt <= $this->maxRetries) {
            $lastResponse = Http::connectTimeout($this->connectTimeout)
                ->timeout($this->requestTimeout)
                ->retry(0, 0, null, false)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-goog-api-key' => $apiKey,
                ])
                ->post($endpoint, $payload);

            // Only retry on transient server errors.
            if ($lastResponse->successful() || ! $this->isRetryableStatus($lastResponse->status())) {
                break;
            }

            $attempt++;
            if ($attempt <= $this->maxRetries) {
                usleep(1000000); // 1 second backoff between retries
            }
        }

        return $lastResponse;
    }

    /**
     * Determine whether the HTTP status is transient and worth retrying.
     */
    protected function isRetryableStatus(int $status): bool
    {
        return in_array($status, [429, 500, 502, 503, 504], true);
    }

    /**
     * Convert a failed Gemini response into a provider exception.
     *
     * @throws AiProviderException
     */
    protected function providerFailure(Response $response): AiProviderException
    {
        // Log status and reason only - never the request body or API key.
        Log::warning('Gemini API request failed', [
            'status' => $response->status(),
            'model' => config('services.gemini.model'),
        ]);

        $message = match ($response->status()) {
            400 => 'Invalid request sent to AI service.',
            401 => 'Invalid Gemini API key.',
            403 => 'Access denied to Gemini API.',
            404 => 'Requested AI model not found.',
            429 => 'The AI service has reached its usage limit. Please try again later.',
            500 => 'Gemini server error. Please try again later.',
            502 => 'Bad gateway from AI service.',
            503 => 'Gemini service is currently unavailable. Please try again in a few minutes.',
            504 => 'AI service timeout. Please try again.',
            default => 'Unable to generate response at the moment.',
        };

        return new AiProviderException($message, $response->status());
    }
}
