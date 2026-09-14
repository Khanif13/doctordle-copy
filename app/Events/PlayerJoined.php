<?php

namespace App\Events;

use App\Models\GameSession;
use App\Models\Player;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PlayerJoined implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public GameSession $gameSession, public Player $player,) {}
    public function broadcastOn(): array
    {
        return [new Channel("game-session.{$this->gameSession->id}"),];
    }
    public function broadcastAs(): string
    {
        return 'PlayerJoined';
    }
    public function broadcastWith(): array
    {
        return ['player_id' => $this->player->id, 'nickname' => $this->player->nickname,];
    }
}
