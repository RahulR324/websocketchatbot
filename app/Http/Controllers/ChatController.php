<?php

namespace App\Http\Controllers;

use App\Events\StreamChunk;
use App\Exceptions\AiProviderException;
use App\Services\FallbackAiService;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected $ai;

    public function __construct(FallbackAiService $ai)
    {
        $this->ai = $ai;
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $question = trim($validated['message']);

        if ($question === '') {
            return response()->json(['error' => 'Message cannot be empty.'], 422);
        }

        $isQuiz = (bool) preg_match(
            '/\b(quiz|practice questions|mcqs?|test me|mock test|exam prep)\b/i',
            $question
        );

        try {
            if ($isQuiz) {
                $quiz = $this->ai->generateQuiz($question);

                return response()->json([
                    'type' => 'quiz',
                    'quiz' => $quiz,
                ]);
            }

            $answer = $this->ai->ask($question);

            try {
                broadcast(new StreamChunk($answer, true));
            } catch (\Throwable $e) {
                // Reverb/broadcaster unavailable; HTTP response still delivers the answer.
            }

            return response()->json([
                'status' => 'ok',
                'answer' => $answer,
            ]);
        } catch (AiProviderException $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 200);
        }
    }
}
