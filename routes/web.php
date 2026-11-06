<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReverbController;

Route::get('/', function () {
    return view('chat');
})->middleware('chat.throttle');

Route::post('/send-message', [ChatController::class, 'send'])
    ->middleware('chat.throttle');

// Public Echo configuration endpoint. Only non-secret values are exposed.
Route::get('/reverb/config', [ReverbController::class, 'config'])
    ->middleware('chat.throttle');

// Channel authorization endpoint. The actual authorization decision is
// made by the closure in routes/channels.php - this route just forwards
// to Laravel's Broadcast::auth() handler.
Route::post('/reverb/auth', [ReverbController::class, 'auth']);