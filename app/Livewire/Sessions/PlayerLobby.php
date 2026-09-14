<?php

namespace App\Livewire\Sessions;

use App\Models\GameSession;
use App\Models\Player;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.guest')]
class PlayerLobby extends Component
{
    #[Locked]
    public GameSession $gameSession;

    #[Locked]
    public Player $player;

    public function mount(GameSession $gameSession)
    {
        $token = Cookie::get("player_token_{$gameSession->id}");

        $player = $token
            ? Player::where('game_session_id', $gameSession->id)
            ->where('session_token', $token)
            ->first()
            : null;

        if (! $player) {
            return redirect()->route('sessions.join');
        }

        $this->gameSession = $gameSession;
        $this->player = $player;
    }

    public function render()
    {
        return view('livewire.sessions.player-lobby');
    }
}
