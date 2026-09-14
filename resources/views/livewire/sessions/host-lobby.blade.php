<div class="py-8 max-w-lg mx-auto px-4 space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $quiz->title }}</h2>
        <a href="{{ route('quizzes.manage', $quiz) }}" wire:navigate class="text-sm text-indigo-600 hover:underline">
            &larr; Back to quiz
        </a>
    </div>

    <div class="bg-white shadow rounded-lg p-8 text-center">
        <p class="text-sm text-slate-500 mb-2">Join PIN</p>
        <p class="text-5xl font-bold tracking-widest text-indigo-600">{{ $gameSession->pin }}</p>
        <p class="text-xs text-slate-400 mt-3">
            {{ $gameSession->settings['question_count'] }} question(s) &middot;
            {{ $gameSession->settings['timer_seconds_per_question'] }}s each &middot;
            {{ $gameSession->settings['base_points'] }} base points
        </p>
    </div>

    <div class="bg-white shadow rounded-lg p-6">
        <h3 class="font-medium text-slate-800 mb-4">
            Players joined ({{ $players->count() }})
        </h3>

        @forelse ($players as $player)
            <div class="text-sm text-slate-700 py-1">{{ $player->nickname }}</div>
        @empty
            <p class="text-sm text-slate-400">Waiting for players to join with the PIN above...</p>
        @endforelse

        <p class="text-xs text-amber-600 mt-4">
            This list won't update live yet — that's the next feature (Reverb broadcasting). For now, refresh the page
            to see newly joined players once the join screen exists.
        </p>
    </div>
</div>
