<x-layouts.app title="Calendar" breadcrumb="Activities">
    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-2">
            <div class="flex overflow-hidden rounded-lg border border-line">
                <a href="{{ route('activities.index') }}" class="flex size-9 items-center justify-center text-ink-500 hover:bg-gray-50">
                    <x-icon name="list" class="size-[18px]" />
                </a>
                <span class="flex size-9 items-center justify-center border-l border-line bg-indigo-50 text-indigo-brand">
                    <x-icon name="activities" class="size-[18px]" />
                </span>
            </div>

            <a href="{{ route('activities.calendar', ['week' => $anchor->subWeek()->toDateString()]) }}" class="btn-secondary btn-sm">
                <x-icon name="chevron-left" class="size-4" />
            </a>
            <a href="{{ route('activities.calendar', ['week' => $anchor->addWeek()->toDateString()]) }}" class="btn-secondary btn-sm">
                <x-icon name="chevron-right" class="size-4" />
            </a>
            <a href="{{ route('activities.calendar') }}" class="btn-secondary btn-sm">Today</a>

            <p class="ml-2 text-[15px] font-bold text-ink-900">
                {{ $anchor->format('M j') }} – {{ $anchor->addDays(6)->format('M j, Y') }}
            </p>

            <button type="button" class="btn-primary ml-auto" x-on:click="$dispatch('open-modal', 'quick-create')">
                <x-icon name="plus" class="size-4" stroke-width="2.4" /> Activity
            </button>
        </div>

        <div class="card mt-4 grid grid-cols-1 divide-y divide-line overflow-hidden md:grid-cols-7 md:divide-x md:divide-y-0">
            @foreach (range(0, 6) as $offset)
                @php
                    $day = $anchor->addDays($offset);
                    $items = $activities[$day->toDateString()] ?? collect();
                @endphp

                <div class="min-h-[420px] {{ $day->isToday() ? 'bg-indigo-50/40' : '' }}">
                    <div class="border-b border-line px-3 py-2.5 text-center">
                        <p class="text-[11px] font-semibold tracking-wide text-ink-500 uppercase">{{ $day->format('D') }}</p>
                        <p class="mt-0.5 text-lg leading-none font-bold {{ $day->isToday() ? 'text-indigo-brand' : 'text-ink-900' }}">
                            {{ $day->format('j') }}
                        </p>
                    </div>

                    <div class="space-y-1.5 p-2">
                        @forelse ($items as $activity)
                            <div class="rounded-lg border-l-[3px] bg-white px-2.5 py-2 shadow-sm ring-1 ring-line
                                        {{ $activity->done ? 'border-brand-600' : ($activity->is_overdue ? 'border-danger' : 'border-indigo-brand') }}">
                                <p class="flex items-center gap-1.5 text-[11px] font-semibold text-ink-500">
                                    <x-icon :name="$activity->icon" class="size-3" />
                                    {{ $activity->due_time ? \Illuminate\Support\Carbon::parse($activity->due_time)->format('g:i A') : 'All day' }}
                                </p>
                                <p class="mt-1 text-[13px] leading-snug font-medium {{ $activity->done ? 'text-ink-400 line-through' : 'text-ink-900' }}">
                                    {{ Str::limit($activity->subject, 48) }}
                                </p>
                                @if ($activity->person)
                                    <p class="mt-1 truncate text-[11px] text-ink-500">{{ $activity->person->name }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="px-1 py-3 text-center text-[11px] text-ink-400">No activities</p>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
