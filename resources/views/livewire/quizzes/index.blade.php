<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Quizzes</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto px-4 space-y-6">

        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="font-medium text-slate-800 mb-4">New quiz</h3>
            <form wire:submit="createQuiz" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700">Title</label>
                    <input type="text" wire:model="title"
                        class="mt-1 w-full rounded-lg border-slate-300 shadow-sm">
                    @error('title') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700">Description</label>
                    <textarea wire:model="description" rows="2"
                        class="mt-1 w-full rounded-lg border-slate-300 shadow-sm"></textarea>
                    @error('description') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit"
                    class="rounded-lg bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-500">
                    Create quiz
                </button>
            </form>
        </div>

        <div class="bg-white shadow rounded-lg divide-y">
            @forelse ($quizzes as $quiz)
                <div class="p-4 flex items-center justify-between">
                    <div>
                        <a href="{{ route('quizzes.manage', $quiz) }}" wire:navigate
                            class="font-medium text-indigo-600 hover:underline">
                            {{ $quiz->title }}
                        </a>
                        <p class="text-sm text-slate-500">{{ $quiz->questions_count }} question(s)</p>
                    </div>
                    <button wire:click="deleteQuiz({{ $quiz->id }})"
                        wire:confirm="Delete this quiz and all its questions?"
                        class="text-sm text-red-600 hover:text-red-800">
                        Delete
                    </button>
                </div>
            @empty
                <p class="p-4 text-sm text-slate-500">No quizzes yet — create one above.</p>
            @endforelse
        </div>

    </div>
</x-app-layout>