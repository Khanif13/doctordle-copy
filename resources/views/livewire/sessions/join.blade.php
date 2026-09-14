<div>
    <h1 class="text-xl font-semibold text-slate-800 mb-6">Join a session</h1>

    <form wire:submit="joinSession" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-slate-700">PIN</label>
            <input type="text" inputmode="numeric" maxlength="6" wire:model="pin" placeholder="123456"
                class="mt-1 w-full rounded-lg border-slate-300 shadow-sm text-center text-2xl tracking-widest">
            @error('pin')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-slate-700">Nickname</label>
            <input type="text" wire:model="nickname" maxlength="30"
                class="mt-1 w-full rounded-lg border-slate-300 shadow-sm">
            @error('nickname')
                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="w-full rounded-lg bg-indigo-600 text-white font-medium py-2 hover:bg-indigo-500">
            Join
        </button>
    </form>
</div>
