<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
class GeminiService
{
    public function ask($question)
    {
        $prompt = "

        You are an expert teacher.
        Rules:
        - Explain concepts in simple and easy-to-understand language.
        - Assume the user is a beginner unless the question is advanced.
        - Break complex topics into smaller sections.
        - Use simple language.
        - Give examples.
        - Use headings and bullet points.
        - Maximum 300 words unless requested otherwise.
        - Make answers easy for students to understand.

        Question:
        {$question}

        ";

        $response = Http::connectTimeout(5)
        ->timeout(120)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => env('GEMINI_API_KEY')
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]
        );
        if (!$response->successful()) {

            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body' => $response->body()
            ]);

            return match ($response->status()) {

                400 => 'Invalid request sent to AI service.',

                401 => 'Invalid Gemini API key.',

                403 => 'Access denied to Gemini API.',

                404 => 'Requested AI model not found.',

                429 => 'The AI service has reached its usage limit. Please try again after 24 hours.',

                500 => 'Gemini server error. Please try again later.',

                502 => 'Bad gateway from AI service.',

                503 => 'Gemini service is currently unavailable. Please try again in a few minutes.',

                504 => 'AI service timeout. Please try again.',

                default => 'Unable to generate response at the moment.'
            };
        }

        $data = $response->json();
        Log::info('Gemini Response',[
            'status'=> $response->status(),
            'body' => $response->json()
        ]);
        return $data['candidates'][0]['content']['parts'][0]['text']
           ?? 'Unable to generate response.';
    }

    public function generateQuiz($topic)
    {
        $prompt = "

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

        $response = Http::connectTimeout(5)
        ->timeout(120)
        ->withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => env('GEMINI_API_KEY')
        ])->post(
            'https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent',
            [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]
        );

        $data = $response->json();
        Log::info('Gemini Response',[
            'status'=> $response->status(),
            'body' => $response->json()
        ]);

        return json_decode(
            $data['candidates'][0]['content']['parts'][0]['text'],
            true
        );
    }
}