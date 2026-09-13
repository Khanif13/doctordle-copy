<?php

namespace App\Livewire\Quizzes;

use App\Models\Clue;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Manage extends Component
{
    #[Locked]
    public Quiz $quiz;

    // New-question form
    public string $newCasePrompt = '';
    public string $newCorrectAnswer = '';

    // New-clue text, keyed by question id: [$questionId => $text]
    public array $newClueText = [];

    public function mount(Quiz $quiz): void
    {
        abort_unless($quiz->created_by === Auth::id(), 403);
        $this->quiz = $quiz;
    }

    public function addQuestion(): void
    {
        $this->validate([
            'newCasePrompt' => ['required', 'string'],
            'newCorrectAnswer' => ['required', 'string', 'max:255'],
        ]);

        $nextOrder = ($this->quiz->questions()->max('order') ?? -1) + 1;

        $this->quiz->questions()->create([
            'case_prompt' => $this->newCasePrompt,
            'correct_answer' => $this->newCorrectAnswer,
            'order' => $nextOrder,
        ]);

        $this->reset(['newCasePrompt', 'newCorrectAnswer']);
    }

    public function deleteQuestion(Question $question): void
    {
        abort_unless($question->quiz_id === $this->quiz->id, 403);
        $question->delete();
    }

    public function moveQuestion(Question $question, string $direction): void
    {
        abort_unless($question->quiz_id === $this->quiz->id, 403);

        $siblings = $this->quiz->questions()->orderBy('order')->get();
        $index = $siblings->search(fn($q) => $q->id === $question->id);
        $swapWith = $direction === 'up' ? $index - 1 : $index + 1;

        if (! isset($siblings[$swapWith])) {
            return;
        }

        $other = $siblings[$swapWith];
        [$orderA, $orderB] = [$question->order, $other->order];

        $question->update(['order' => $orderB]);
        $other->update(['order' => $orderA]);
    }

    public function addClue(Question $question): void
    {
        abort_unless($question->quiz_id === $this->quiz->id, 403);

        $text = trim($this->newClueText[$question->id] ?? '');

        if ($text === '') {
            return;
        }

        $nextOrder = ($question->clues()->max('order') ?? -1) + 1;

        $question->clues()->create([
            'clue_text' => $text,
            'order' => $nextOrder,
        ]);

        unset($this->newClueText[$question->id]);
    }

    public function deleteClue(Clue $clue): void
    {
        abort_unless($clue->question->quiz_id === $this->quiz->id, 403);
        $clue->delete();
    }

    public function render()
    {
        return view('livewire.quizzes.manage', [
            'questions' => $this->quiz->questions()->with('clues')->orderBy('order')->get(),
        ]);
    }
}
