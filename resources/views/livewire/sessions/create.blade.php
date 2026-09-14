<div class="py-8 max-w-lg mx-auto px-4 space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Start a session</h2>
        <a href="{{ route('quizzes.manage', $quiz) }}" wire:navigate class="text-sm text-indigo-600 hover:underline">
            &larr; Back to {{ $quiz->title }}
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <form wire:submit="createSession" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-slate-700">Number of questions</label>
                <input type="number" wire:model="questionCount" min="1" max="{{ $totalQuestions }}"
                    class="mt-1 w-full rounded-lg border-slate-300 shadow-sm">
                <p class="text-xs text-slate-400 mt-1">This quiz has {{ $totalQuestions }} question(s) available. First
                    N in order will be used.</p>
                @error('questionCount')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Timer per question (seconds)</label>
                <input type="number" wire:model="timerSecondsPerQuestion" min="5" max="300"
                    class="mt-1 w-full rounded-lg border-slate-300 shadow-sm">
                @error('timerSecondsPerQuestion')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700">Base points</label>
                <input type="number" wire:model="basePoints" min="100" max="10000" step="100"
                    class="mt-1 w-full rounded-lg border-slate-300 shadow-sm">
                <p class="text-xs text-slate-400 mt-1">Used in the scoring formula: base_points &times; (clues remaining
                    / total clues) &times; time_bonus.</p>
                @error('basePoints')
                    <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-indigo-600 text-white font-medium py-2 hover:bg-indigo-500">
                Generate PIN &amp; open lobby
            </button>
        </form>
    </div>
</div>
