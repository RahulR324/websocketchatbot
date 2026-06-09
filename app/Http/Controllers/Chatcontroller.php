<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\MessageSent;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $question = strtolower(trim($request->message ?? ''));

        $path = storage_path('app/chatbot.json');

        if (!file_exists($path)) {
            $answer = 'JSON file missing';
        } else {

            $data = json_decode(file_get_contents($path), true);

            if (!is_array($data)) {
                $answer = 'Invalid JSON';
            } else {
                $answer = $data[$question]
                    ?? "Sorry, I don't understand.";
            }
        }

        broadcast(new MessageSent($answer));

        return response()->json([
            'success' => true
        ]);
    }
}