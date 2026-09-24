@props(['notes', 'field', 'id'])

<div class="card">
    <div class="border-b border-line px-5 py-3">
        <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Notes</p>
    </div>

    <form method="POST" action="{{ route('notes.store') }}" class="border-b border-line p-4">
        @csrf
        <input type="hidden" name="{{ $field }}" value="{{ $id }}">
        <textarea name="body" rows="2" class="field resize-y" placeholder="Add a note…" required></textarea>
        <div class="mt-2 flex justify-end">
            <button type="submit" class="btn-primary btn-sm">Save note</button>
        </div>
    </form>

    <ul class="divide-y divide-line">
        @forelse ($notes as $note)
            <li class="flex items-start gap-3 px-5 py-3.5">
                <x-avatar :name="$note->user?->name ?? 'System'" size="xs" />
                <div class="min-w-0 flex-1">
                    <p class="text-sm whitespace-pre-line text-ink-900">{{ $note->body }}</p>
                    <p class="mt-1 text-xs text-ink-400">
                        {{ $note->user?->name ?? 'System' }} · {{ $note->created_at->diffForHumans() }}
                    </p>
                </div>
                <form method="POST" action="{{ route('notes.destroy', $note) }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-ink-400 hover:text-danger" aria-label="Delete note">
                        <x-icon name="trash" class="size-4" />
                    </button>
                </form>
            </li>
        @empty
            <li class="px-5 py-6 text-sm text-ink-400">No notes yet.</li>
        @endforelse
    </ul>
</div>
