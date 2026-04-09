<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardMoved implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $boardId,
        public array $card
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("board.{$this->boardId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'card.moved';
    }

    public function broadcastWith(): array
    {
        return ['card' => $this->card];
    }
}
