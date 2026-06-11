<?php

namespace App\Http\Controllers;

use App\Events\StreamChunk;
use Illuminate\Http\Request;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $question = strtolower(trim($request->message ?? ''));

        $path = storage_path('app/chatbot.json');

        if (!file_exists($path)) {
            broadcast(new StreamChunk('Error: Knowledge base is not found.'));
            broadcast(new StreamChunk('', true));
            return response() -> json([
                'status'=> 'error',
                'message'=> 'JSON file not found'
            ], 404);
        }

        $data = json_decode(file_get_contents($path), true);
        if(json_last_error() !== JSON_ERROR_NONE){
            broadcast(new StreamChunk('Error: invalid JSON format'));
            broadcast(new StreamChunk('', true));

            return response() -> json([
                'status'=> 'error',
                'message'=> 'Invalid JSON'
            ],500);
        }    
        $answer = $data[$question] ?? "sorry, I dont understand";
        $words = explode(' ', $answer);
        foreach ($words as $word) {
            broadcast(new StreamChunk($word));
            usleep(30000);
        }    

        broadcast(new StreamChunk('', true));

        return response()->json([
            'status' => 'streaming'
        ]);
    }
}