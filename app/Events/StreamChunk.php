<?php

namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamChunk implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $chunk;
    public $finished;

    public function __construct($chunk, $finished = false)
    {
        $this->chunk = $chunk;
        $this->finished = $finished;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('chat-channel'),
        ];
    }

    public function broadcastAs()
    {
        return 'StreamChunk';
    }
}