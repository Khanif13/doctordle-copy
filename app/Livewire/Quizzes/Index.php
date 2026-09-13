<?php

namespace App\Livewire\Quizzes;

use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Index extends Component
{
    public string $title = '';
    public string $description = '';

    public function createQuiz(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $quiz = Quiz::create([
            'title' => $this->title,
            'description' => $this->description,
            'created_by' => Auth::id(),
        ]);

        $this->reset(['title', 'description']);

        $this->redirect(route('quizzes.manage', $quiz), navigate: true);
    }

    public function deleteQuiz(Quiz $quiz): void
    {
        abort_unless($quiz->created_by === Auth::id(), 403);

        $quiz->delete();
    }

    public function render()
    {
        return view('livewire.quizzes.index', [
            'quizzes' => Quiz::where('created_by', Auth::id())
                ->withCount('questions')
                ->latest()
                ->get(),
        ]);
    }
}
