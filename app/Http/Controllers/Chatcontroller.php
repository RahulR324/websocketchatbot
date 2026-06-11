<?php

namespace App\Http\Controllers;

use App\Events\StreamChunk;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Services\GeminiService;

class ChatController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }


    public function send(Request $request)
    {
        $question = trim($request->message ?? '');   
        $answer = $this->gemini->ask($question);
        $words = explode(' ', $answer);
        foreach ($words as $word) {
            broadcast(new StreamChunk($word));
            usleep(20000);
        }    

        broadcast(new StreamChunk('', true));

        return response()->json([
            'status' => 'streaming'
        ]);
    }
}