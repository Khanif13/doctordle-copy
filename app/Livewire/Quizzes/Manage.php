<?php

namespace App\Livewire\Quizzes;

use App\Models\Clue;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Manage extends Component
{
    use WithFileUploads;

    #[Locked]
    public Quiz $quiz;

    public string $newCasePrompt = '';
    public string $newCorrectAnswer = '';
    public array $newClueText = [];

    // Bulk CSV import
    public $csvFile = null;
    public ?string $csvSuccessMessage = null;
    public array $csvSkippedRows = [];

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

    /**
     * Import questions + clues from an uploaded CSV.
     *
     * Expected header: case_prompt,correct_answer,clue1,clue2,...
     * Any column after the first two is treated as a clue, in order;
     * empty clue cells are skipped. Column names beyond the first two
     * don't matter, only their position.
     */
    public function importCsv(): void
    {
        $this->csvSuccessMessage = null;
        $this->csvSkippedRows = [];

        $this->validate([
            'csvFile' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);

        $handle = fopen($this->csvFile->getRealPath(), 'r');

        if ($handle === false) {
            $this->addError('csvFile', 'Could not read the uploaded file.');
            return;
        }

        // Skip the header row.
        $header = fgetcsv($handle);

        if ($header === false) {
            $this->addError('csvFile', 'The file appears to be empty.');
            fclose($handle);
            return;
        }

        $nextOrder = ($this->quiz->questions()->max('order') ?? -1) + 1;
        $importedCount = 0;
        $rowNumber = 1; // header was row 1

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            $casePrompt = trim($row[0] ?? '');
            $correctAnswer = trim($row[1] ?? '');

            if ($casePrompt === '' || $correctAnswer === '') {
                $this->csvSkippedRows[] = $rowNumber;
                continue;
            }

            $question = $this->quiz->questions()->create([
                'case_prompt' => $casePrompt,
                'correct_answer' => $correctAnswer,
                'order' => $nextOrder,
            ]);
            $nextOrder++;

            $clueOrder = 0;
            foreach (array_slice($row, 2) as $clueText) {
                $clueText = trim((string) $clueText);

                if ($clueText === '') {
                    continue;
                }

                $question->clues()->create([
                    'clue_text' => $clueText,
                    'order' => $clueOrder,
                ]);
                $clueOrder++;
            }

            $importedCount++;
        }

        fclose($handle);

        $this->csvSuccessMessage = "Imported {$importedCount} question(s).";
        $this->reset('csvFile');
    }

    public function render()
    {
        return view('livewire.quizzes.manage', [
            'questions' => $this->quiz->questions()->with('clues')->orderBy('order')->get(),
        ]);
    }
}
