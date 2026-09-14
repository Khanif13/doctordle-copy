<?php

namespace App\Livewire\Sessions;

use App\Events\PlayerJoined;
use App\Models\GameSession;
use App\Models\Player;
use Illuminate\Support\Facades\Cookie;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class Join extends Component
{
    public string $pin = '';
    public string $nickname = '';

    public function joinSession(): void
    {
        $this->validate([
            'pin' => ['required', 'digits:6'],
            'nickname' => ['required', 'string', 'max:30'],
        ]);

        $gameSession = GameSession::where('pin', $this->pin)->first();

        if (! $gameSession) {
            $this->addError('pin', 'No session found with that PIN.');
            return;
        }

        if ($gameSession->status !== GameSession::STATUS_WAITING) {
            $this->addError('pin', 'This session has already started or finished.');
            return;
        }

        $nicknameTaken = $gameSession->players()
            ->where('nickname', $this->nickname)
            ->exists();

        if ($nicknameTaken) {
            $this->addError('nickname', 'That nickname is already taken in this session.');
            return;
        }

        $player = $gameSession->players()->create([
            'nickname' => $this->nickname,
            'session_token' => Player::generateSessionToken(),
            'joined_at' => now(),
        ]);

        PlayerJoined::dispatch($gameSession, $player);

        Cookie::queue(
            "player_token_{$gameSession->id}",
            $player->session_token,
            60 * 12 // 12 hours
        );

        $this->redirect(route('sessions.play', $gameSession), navigate: true);
    }

    public function render()
    {
        return view('livewire.sessions.join');
    }
}
