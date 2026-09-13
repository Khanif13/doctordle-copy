<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->title }}</h2>
            <a href="{{ route('quizzes.index') }}" wire:navigate class="text-sm text-indigo-600 hover:underline">
                &larr; Back to quizzes
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 space-y-6">

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-medium text-slate-800 mb-4">Add a question</h3>
            <form wire:submit="addQuestion" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Case prompt</label>
                    <textarea wire:model="newCasePrompt" rows="3" class="mt-1 w-full rounded-lg border-slate-300 shadow-sm"
                        placeholder="e.g. 34F presents with sudden-onset..."></textarea>
                    @error('newCasePrompt')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Correct answer</label>
                    <input type="text" wire:model="newCorrectAnswer"
                        class="mt-1 w-full rounded-lg border-slate-300 shadow-sm" placeholder="e.g. Ectopic pregnancy">
                    @error('newCorrectAnswer')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-500">
                    Add question
                </button>
            </form>
        </div>

        <div class="space-y-4">
            @forelse ($questions as $index => $question)
                <div class="bg-white shadow rounded-lg p-6" wire:key="question-{{ $question->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1">
                            <p class="text-xs font-medium text-slate-400 mb-1">Question {{ $index + 1 }}</p>
                            <p class="text-slate-800">{{ $question->case_prompt }}</p>
                            <p class="text-sm text-emerald-600 mt-1">Answer: {{ $question->correct_answer }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1 shrink-0">
                            <div class="flex gap-1">
                                <button wire:click="moveQuestion({{ $question->id }}, 'up')"
                                    class="text-xs text-slate-400 hover:text-slate-700">&uarr;</button>
                                <button wire:click="moveQuestion({{ $question->id }}, 'down')"
                                    class="text-xs text-slate-400 hover:text-slate-700">&darr;</button>
                            </div>
                            <button wire:click="deleteQuestion({{ $question->id }})"
                                wire:confirm="Delete this question and its clues?"
                                class="text-xs text-red-600 hover:text-red-800">
                                Delete
                            </button>
                        </div>
                    </div>

                    <div class="mt-4 pl-4 border-l-2 border-slate-100 space-y-2">
                        <p class="text-xs font-medium text-slate-400">Clues (revealed in order)</p>

                        @foreach ($question->clues as $clueIndex => $clue)
                            <div class="flex items-center justify-between text-sm" wire:key="clue-{{ $clue->id }}">
                                <span class="text-slate-700">{{ $clueIndex + 1 }}. {{ $clue->clue_text }}</span>
                                <button wire:click="deleteClue({{ $clue->id }})"
                                    class="text-xs text-red-500 hover:text-red-700">
                                    Remove
                                </button>
                            </div>
                        @endforeach

                        <form wire:submit="addClue({{ $question->id }})" class="flex gap-2 pt-2">
                            <input type="text" wire:model="newClueText.{{ $question->id }}"
                                placeholder="Add a clue..."
                                class="flex-1 rounded-lg border-slate-300 shadow-sm text-sm">
                            <button type="submit"
                                class="rounded-lg bg-slate-100 text-slate-700 px-3 py-1 text-sm hover:bg-slate-200">
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-500">No questions yet — add one above.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>
