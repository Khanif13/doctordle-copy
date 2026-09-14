<?php

namespace App\Livewire\Sessions;

use App\Models\GameSession;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class Create extends Component
{
    #[Locked]
    public Quiz $quiz;

    public int $questionCount = 1;
    public int $timerSecondsPerQuestion = 30;
    public int $basePoints = 1000;

    public function mount(Quiz $quiz): void
    {
        abort_unless($quiz->created_by === Auth::id(), 403);
        abort_if($quiz->questions()->count() === 0, 422, 'Add at least one question before starting a session.');

        $this->quiz = $quiz;
        $this->questionCount = $quiz->questions()->count();
    }

    public function createSession(): void
    {
        $totalQuestions = $this->quiz->questions()->count();

        $this->validate([
            'questionCount' => ['required', 'integer', 'min:1', 'max:' . $totalQuestions],
            'timerSecondsPerQuestion' => ['required', 'integer', 'min:5', 'max:300'],
            'basePoints' => ['required', 'integer', 'min:100', 'max:10000'],
        ]);

        $questionIds = $this->quiz->questions()
            ->orderBy('order')
            ->limit($this->questionCount)
            ->pluck('id')
            ->all();

        $gameSession = GameSession::create([
            'quiz_id' => $this->quiz->id,
            'pin' => GameSession::generateUniquePin(),
            'status' => GameSession::STATUS_WAITING,
            'current_question_index' => 0,
            'settings' => [
                'question_ids' => $questionIds,
                'question_count' => count($questionIds),
                'timer_seconds_per_question' => $this->timerSecondsPerQuestion,
                'base_points' => $this->basePoints,
            ],
        ]);

        $this->redirect(route('sessions.host', $gameSession), navigate: true);
    }

    public function render()
    {
        return view('livewire.sessions.create', [
            'totalQuestions' => $this->quiz->questions()->count(),
        ]);
    }
}