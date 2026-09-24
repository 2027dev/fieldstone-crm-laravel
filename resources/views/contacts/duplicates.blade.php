<x-layouts.app title="Merge duplicates" breadcrumb="Contacts">
    <x-slot:subnav>
        <x-contacts-subnav />
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="card p-5">
            <h2 class="text-[17px] font-bold text-ink-900">Potential duplicate people</h2>
            <p class="mt-1 text-sm text-ink-700">
                Records are grouped when they share an email address or an identical name. Pick the record to keep —
                deals, activities, notes and emails from the others are re-pointed to it.
            </p>
        </div>

        <div class="mt-5 space-y-4">
            @forelse ($groups as $group)
                <form method="POST" action="{{ route('contacts.duplicates.merge') }}" class="card p-5">
                    @csrf
                    <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">
                        {{ $group->count() }} matching records
                    </p>

                    <ul class="mt-3 space-y-2">
                        @foreach ($group as $index => $person)
                            <li class="flex items-center gap-3 rounded-lg border border-line px-3 py-2.5">
                                <input type="radio" name="keep_id" value="{{ $person->id }}" @checked($index === 0)
                                       class="text-indigo-brand" aria-label="Keep {{ $person->name }}">
                                <input type="checkbox" name="merge_ids[]" value="{{ $person->id }}" @checked($index !== 0)
                                       class="rounded border-gray-300" aria-label="Merge {{ $person->name }}">
                                <x-avatar :name="$person->name" size="xs" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-ink-900">{{ $person->name }}</p>
                                    <p class="truncate text-xs text-ink-500">
                                        {{ $person->email ?: 'No email' }} · {{ $person->organization?->name ?: 'No organization' }}
                                    </p>
                                </div>
                                <span class="shrink-0 text-xs text-ink-400">Added {{ $person->created_at->format('M j, Y') }}</span>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4 flex justify-end">
                        <button type="submit" class="btn-primary btn-sm"><x-icon name="convert" class="size-4" /> Merge selected</button>
                    </div>
                </form>
            @empty
                <x-empty-state icon="check-circle" title="No duplicates found"
                               description="Your contact list looks clean. We'll surface matches here as soon as two records share an email or name." />
            @endforelse
        </div>

        @if ($organizationGroups->isNotEmpty())
            <div class="card mt-6 p-5">
                <h2 class="text-[17px] font-bold text-ink-900">Potential duplicate organizations</h2>
                <ul class="mt-3 space-y-2">
                    @foreach ($organizationGroups as $group)
                        <li class="rounded-lg border border-line px-3 py-2.5 text-sm">
                            {{ $group->pluck('name')->implode(' · ') }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-layouts.app>
