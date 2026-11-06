<?php

namespace App\Services;

use App\Contracts\AiService;
use App\Exceptions\AiProviderException;

/**
 * Tries the primary AI provider and transparently falls back to a secondary
 * provider when the primary is unavailable.
 */
class FallbackAiService implements AiService
{
    protected AiService $primary;

    protected AiService $fallback;

    public function __construct(AiService $primary, AiService $fallback)
    {
        $this->primary = $primary;
        $this->fallback = $fallback;
    }

    /**
     * Send a question to the AI teacher model and return the answer text.
     *
     * @throws AiProviderException
     */
    public function ask(string $question): string
    {
        try {
            return $this->primary->ask($question);
        } catch (AiProviderException $primaryError) {
            try {
                return $this->fallback->ask($question);
            } catch (AiProviderException $fallbackError) {
                // Both providers failed. Surface the primary failure so the
                // caller can present a user-safe message.
                throw $primaryError;
            }
        }
    }

    /**
     * Generate a quiz on the supplied topic and return decoded JSON.
     *
     * @throws AiProviderException
     */
    public function generateQuiz(string $topic): ?array
    {
        try {
            return $this->primary->generateQuiz($topic);
        } catch (AiProviderException $primaryError) {
            try {
                return $this->fallback->generateQuiz($topic);
            } catch (AiProviderException $fallbackError) {
                throw $primaryError;
            }
        }
    }
}
