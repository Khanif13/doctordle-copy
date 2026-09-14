<?php

namespace App\Livewire\Sessions;

use App\Models\GameSession;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class HostLobby extends Component
{
    #[Locked]
    public GameSession $gameSession;

    public function mount(GameSession $gameSession): void
    {
        abort_unless($gameSession->quiz->created_by === Auth::id(), 403);
        $this->gameSession = $gameSession;
    }

    #[On('echo:game-session.{gameSession.id},PlayerJoined')]
    public function onPlayerJoined(): void
    {
        // No body needed — render() below already re-queries players
        // on every request, and this attribute just triggers that
        // request whenever the PlayerJoined event arrives over Echo.
    }

    public function render()
    {
        return view('livewire.sessions.host-lobby', [
            'quiz' => $this->gameSession->quiz,
            'players' => $this->gameSession->players()->orderBy('joined_at')->get(),
        ]);
    }
}
