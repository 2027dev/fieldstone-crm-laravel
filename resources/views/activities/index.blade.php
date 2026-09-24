<x-layouts.app title="Activities">
    <div class="p-4 lg:p-5">
        <div x-data="{ show: true }" x-show="show"
             class="mb-4 flex items-start gap-4 rounded-xl bg-gradient-to-r from-indigo-50 to-indigo-50/30 px-5 py-4">
            <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-brand">
                <x-icon name="calendar-sync" class="size-6" />
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[15px] font-bold text-ink-900">Set up calendar sync to never miss an important event.</p>
                <p class="mt-0.5 text-sm text-ink-700">Enable calendar sync to seamlessly sync your external calendar with Fieldstone.</p>
            </div>
            <a href="{{ route('activities.calendar') }}" class="btn-secondary btn-sm shrink-0">Open calendar sync</a>
            <button type="button" x-on:click="show = false" class="shrink-0 text-ink-400 hover:text-ink-700">
                <x-icon name="x" class="size-5" />
            </button>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <div class="flex overflow-hidden rounded-lg border border-line">
                <span class="flex size-9 items-center justify-center bg-indigo-50 text-indigo-brand"><x-icon name="list" class="size-[18px]" /></span>
                <a href="{{ route('activities.calendar') }}" class="flex size-9 items-center justify-center border-l border-line text-ink-500 hover:bg-gray-50">
                    <x-icon name="activities" class="size-[18px]" />
                </a>
            </div>

            <x-modal name="activity-create" title="Schedule an activity" :open="request()->boolean('new')">
                <x-slot:trigger>
                    <button type="button" class="btn-primary"><x-icon name="plus" class="size-4" stroke-width="2.4" /> Activity</button>
                </x-slot:trigger>

                <form method="POST" action="{{ route('activities.store') }}" class="space-y-4 p-5">
                    @csrf
                    <div>
                        <label class="field-label" for="a-subject">Subject</label>
                        <input id="a-subject" name="subject" class="field" required autofocus placeholder="Call Jane about the proposal">
                    </div>
                    <div>
                        <span class="field-label">Type</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach (\App\Models\Activity::TYPES as $value => $meta)
                                <label class="flex cursor-pointer items-center gap-1.5 rounded-lg border border-line px-3 py-1.5 text-sm has-checked:border-indigo-brand has-checked:bg-indigo-50 has-checked:text-indigo-brand">
                                    <input type="radio" name="type" value="{{ $value }}" @checked($loop->first) class="sr-only">
                                    <x-icon :name="$meta['icon']" class="size-4" /> {{ $meta['label'] }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div>
                            <label class="field-label" for="a-date">Due date</label>
                            <input id="a-date" name="due_date" type="date" class="field" value="{{ now()->toDateString() }}">
                        </div>
                        <div>
                            <label class="field-label" for="a-time">Time</label>
                            <input id="a-time" name="due_time" type="time" class="field">
                        </div>
                        <div>
                            <label class="field-label" for="a-duration">Duration (min)</label>
                            <input id="a-duration" name="duration_minutes" type="number" min="0" class="field" placeholder="30">
                        </div>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="a-person">Contact person</label>
                            <select id="a-person" name="person_id" class="field">
                                <option value="">—</option>
                                @foreach ($people as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="a-deal">Deal</label>
                            <select id="a-deal" name="deal_id" class="field">
                                <option value="">—</option>
                                @foreach ($deals as $deal)
                                    <option value="{{ $deal->id }}">{{ $deal->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="a-priority">Priority</label>
                            <select id="a-priority" name="priority" class="field">
                                <option value="">—</option>
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="a-location">Location</label>
                            <input id="a-location" name="location" class="field">
                        </div>
                    </div>
                    <div>
                        <label class="field-label" for="a-note">Note</label>
                        <textarea id="a-note" name="note" rows="2" class="field"></textarea>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                        <button type="submit" class="btn-primary">Save activity</button>
                    </div>
                </form>
            </x-modal>

            <a href="{{ route('activities.calendar') }}" class="btn-secondary">
                <x-icon name="activities" class="size-4" /> Meeting scheduler <x-icon name="chevron-down" class="size-3.5" />
            </a>

            <div class="ml-auto flex items-center gap-2">
                <span class="text-sm text-ink-500">{{ $activities->total() }} {{ Str::plural('activity', $activities->total()) }}</span>
                <span class="chip border border-danger/30 text-danger uppercase">Sync inactive</span>
                <form action="{{ route('activities.index') }}" method="GET" class="relative w-44">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search" class="field py-1.5 pl-9 text-sm">
                    <input type="hidden" name="period" value="{{ $period }}">
                </form>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap items-center gap-x-1 gap-y-2 border-b border-line pb-2">
            <a href="{{ route('activities.index', ['period' => $period]) }}"
               class="rounded-md px-2.5 py-1.5 text-sm font-semibold {{ $type === '' ? 'text-ink-900' : 'text-ink-500 hover:text-ink-900' }}">All</a>
            @foreach (\App\Models\Activity::TYPES as $value => $meta)
                <a href="{{ route('activities.index', ['type' => $value, 'period' => $period]) }}"
                   class="flex items-center gap-1.5 rounded-md px-2.5 py-1.5 text-sm font-semibold {{ $type === $value ? 'bg-indigo-50 text-indigo-brand' : 'text-link/90 hover:bg-gray-50' }}">
                    <x-icon :name="$meta['icon']" class="size-4" /> {{ $meta['label'] }}
                </a>
            @endforeach

            <div class="ml-auto flex flex-wrap items-center gap-1">
                @foreach ([
                    'todo' => 'To-do',
                    'overdue' => 'Overdue',
                    'today' => 'Today',
                    'tomorrow' => 'Tomorrow',
                    'this-week' => 'This week',
                    'next-week' => 'Next week',
                    'done' => 'Done',
                    'all' => 'All time',
                ] as $value => $label)
                    <a href="{{ route('activities.index', array_filter(['type' => $type, 'period' => $value])) }}"
                       class="rounded-md px-2.5 py-1.5 text-sm font-medium {{ $period === $value ? 'bg-indigo-50 font-semibold text-indigo-brand' : 'text-ink-500 hover:bg-gray-50' }}">
                        {{ $label }}
                        @if ($value === 'overdue' && $counts['overdue'] > 0)
                            <span class="ml-1 rounded-full bg-danger px-1.5 text-[11px] font-bold text-white">{{ $counts['overdue'] }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div class="card mt-4 overflow-hidden">
            @if ($activities->isEmpty())
                <x-empty-state icon="activities" title="Nothing scheduled here"
                               description="Arrange the details of a call, meeting or task to advance a deal.">
                    <button type="button" class="btn-primary" x-on:click="$dispatch('open-modal', 'activity-create')">
                        <x-icon name="plus" class="size-4" /> Activity
                    </button>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1320px] border-collapse">
                        <thead>
                            <tr class="table-head">
                                <th class="w-10 px-4 py-2.5"><input type="checkbox" class="rounded border-gray-300" aria-label="Select all"></th>
                                <th class="w-16 px-4 py-2.5">Done</th>
                                <th class="px-4 py-2.5">Subject</th>
                                <th class="px-4 py-2.5">Due</th>
                                <th class="px-4 py-2.5">Deal</th>
                                <th class="px-4 py-2.5">Priority</th>
                                <th class="px-4 py-2.5">Outcome</th>
                                <th class="px-4 py-2.5">Contact person</th>
                                <th class="px-4 py-2.5">Email</th>
                                <th class="px-4 py-2.5">Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activities as $activity)
                                <tr class="hover:bg-gray-50">
                                    <td class="table-cell"><input type="checkbox" class="rounded border-gray-300" aria-label="Select activity"></td>
                                    <td class="table-cell">
                                        <form method="POST" action="{{ route('activities.toggle', $activity) }}">
                                            @csrf
                                            <button type="submit" class="{{ $activity->done ? 'text-brand-600' : 'text-gray-300 hover:text-brand-600' }}"
                                                    aria-label="Mark {{ $activity->subject }} as {{ $activity->done ? 'not done' : 'done' }}">
                                                <x-icon :name="$activity->done ? 'check-circle' : 'circle'" class="size-5" />
                                            </button>
                                        </form>
                                    </td>
                                    <td class="table-cell">
                                        <span class="flex items-center gap-2 {{ $activity->done ? 'text-ink-400 line-through' : '' }}">
                                            <x-icon :name="$activity->icon" class="size-4 text-ink-500" />
                                            <span class="font-medium">{{ $activity->subject }}</span>
                                        </span>
                                    </td>
                                    <td class="table-cell {{ $activity->is_overdue ? 'font-semibold text-danger' : 'text-ink-700' }}">
                                        @if ($activity->due_date)
                                            {{ $activity->due_date->format('M j') }}
                                            @if ($activity->due_time)
                                                <span class="text-ink-400">{{ \Illuminate\Support\Carbon::parse($activity->due_time)->format('g:i A') }}</span>
                                            @endif
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if ($activity->deal)
                                            <a href="{{ route('deals.show', $activity->deal) }}" class="hover:text-link">{{ $activity->deal->title }}</a>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if ($activity->priority)
                                            <span class="chip {{ ['low' => 'bg-gray-100 text-ink-700', 'medium' => 'bg-amber-50 text-warn', 'high' => 'bg-red-50 text-danger'][$activity->priority] }}">
                                                {{ ucfirst($activity->priority) }}
                                            </span>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell text-ink-700">{{ $activity->outcome ?: '—' }}</td>
                                    <td class="table-cell">
                                        @if ($activity->person)
                                            <a href="{{ route('people.show', $activity->person) }}"
                                               class="chip border border-line text-ink-900 hover:border-indigo-brand/40">{{ $activity->person->name }}</a>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell">
                                        @if ($activity->person?->email)
                                            <span class="chip bg-gray-100 text-ink-700">{{ $activity->person->email }}</span>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell whitespace-nowrap">{{ $activity->person?->phone ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($activities->hasPages())
                    <div class="border-t border-line px-4 py-3">{{ $activities->links() }}</div>
                @endif
            @endif
        </div>

        @if (\App\Models\Activity::where('is_sample', true)->exists())
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3 rounded-xl bg-gradient-to-b from-indigo-50/70 to-transparent px-4 py-8">
                <a href="{{ route('activities.calendar') }}" class="btn-secondary"><x-icon name="refresh" class="size-4" /> Sync calendar</a>
                <form method="POST" action="{{ route('sample-data.destroy') }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-secondary">Remove sample data</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.app>
