<?php

namespace App\Contracts;

/**
 * Contract for AI providers used by the chatbot.
 *
 * Implementations must never log or return the user's prompt text.
 */
interface AiService
{
    /**
     * Send a question to the AI teacher model and return the answer text.
     *
     * @throws AiProviderException when the provider cannot produce an answer.
     */
    public function ask(string $question): string;

    /**
     * Generate a quiz on the supplied topic and return decoded JSON.
     *
     * @return array|null Null when the provider cannot produce a quiz.
     * @throws AiProviderException when the provider cannot produce a quiz.
     */
    public function generateQuiz(string $topic): ?array;
}
