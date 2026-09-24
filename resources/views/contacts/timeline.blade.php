<x-layouts.app title="Contacts timeline" breadcrumb="Contacts">
    <x-slot:subnav>
        <x-contacts-subnav />
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('contacts.timeline', ['month' => $anchor->subMonth()->format('Y-m-d')]) }}" class="btn-secondary btn-sm">
                <x-icon name="chevron-left" class="size-4" />
            </a>
            <p class="text-[15px] font-bold text-ink-900">{{ $anchor->format('F Y') }}</p>
            <a href="{{ route('contacts.timeline', ['month' => $anchor->addMonth()->format('Y-m-d')]) }}" class="btn-secondary btn-sm">
                <x-icon name="chevron-right" class="size-4" />
            </a>
            <a href="{{ route('contacts.timeline') }}" class="btn-secondary btn-sm">Today</a>

            <p class="ml-auto text-sm text-ink-500">Each dot is a scheduled activity for that contact.</p>
        </div>

        <div class="card mt-4 overflow-x-auto">
            <table class="w-full border-collapse">
                <thead>
                    <tr class="table-head">
                        <th class="sticky left-0 z-10 min-w-[220px] bg-gray-50 px-4 py-2.5">Contact</th>
                        @foreach ($days as $day)
                            <th class="w-7 px-0 py-2.5 text-center text-[11px] font-semibold {{ $anchor->day($day)->isToday() ? 'text-indigo-brand' : 'text-ink-400' }}">
                                {{ $day }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse ($people as $person)
                        @php $byDay = $person->activities->groupBy(fn ($a) => (int) $a->due_date->day); @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="sticky left-0 z-10 border-b border-line bg-white px-4 py-2 hover:bg-gray-50">
                                <a href="{{ route('people.show', $person) }}" class="flex items-center gap-2.5 text-sm font-medium text-ink-900 hover:text-link">
                                    <x-avatar :name="$person->name" size="xs" />
                                    <span class="min-w-0 truncate">{{ $person->name }}</span>
                                </a>
                            </td>
                            @foreach ($days as $day)
                                <td class="border-b border-line px-0 py-2 text-center">
                                    @if ($byDay->has($day))
                                        <span class="mx-auto flex size-4 items-center justify-center rounded-full text-[9px] font-bold text-white
                                                     {{ $byDay[$day]->every->done ? 'bg-brand-600' : 'bg-indigo-brand' }}"
                                              title="{{ $byDay[$day]->pluck('subject')->implode(', ') }}">
                                            {{ $byDay[$day]->count() > 1 ? $byDay[$day]->count() : '' }}
                                        </span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($days) + 1 }}" class="px-4 py-10 text-center text-sm text-ink-400">
                                No contacts to show.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-layouts.app>
