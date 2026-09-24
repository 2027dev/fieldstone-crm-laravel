<x-layouts.app :title="$deal->title" breadcrumb="Deals">
    <div class="p-4 lg:p-5">
        {{-- Stage progress bar --}}
        <div class="card overflow-hidden">
            <div class="flex flex-wrap items-center gap-3 px-5 py-4">
                <div class="min-w-0 flex-1">
                    <h2 class="truncate text-xl font-bold text-ink-900">{{ $deal->title }}</h2>
                    <p class="mt-0.5 text-sm text-ink-500">
                        <span class="font-semibold text-ink-900 tabular-nums">${{ number_format((float) $deal->value) }}</span>
                        · {{ $deal->pipeline->name }}
                        @if ($deal->expected_close_date)
                            · expected {{ $deal->expected_close_date->format('M j, Y') }}
                        @endif
                    </p>
                </div>

                <span class="chip {{ ['open' => 'bg-indigo-50 text-indigo-brand', 'won' => 'bg-brand-50 text-brand-600', 'lost' => 'bg-red-50 text-danger'][$deal->status] }}">
                    {{ ucfirst($deal->status) }}
                </span>

                @if ($deal->status === 'open')
                    <form method="POST" action="{{ route('deals.status', $deal) }}">
                        @csrf <input type="hidden" name="status" value="won">
                        <button type="submit" class="btn-primary btn-sm"><x-icon name="check" class="size-4" /> Won</button>
                    </form>
                    <button type="button" class="btn-danger btn-sm" x-on:click="$dispatch('open-modal', 'deal-lost')">
                        <x-icon name="x" class="size-4" /> Lost
                    </button>
                @else
                    <form method="POST" action="{{ route('deals.status', $deal) }}">
                        @csrf <input type="hidden" name="status" value="open">
                        <button type="submit" class="btn-secondary btn-sm">Reopen</button>
                    </form>
                @endif

                <button type="button" class="btn-secondary btn-sm" x-on:click="$dispatch('open-modal', 'deal-edit')">
                    <x-icon name="pencil" class="size-4" /> Edit
                </button>
            </div>

            <div class="flex border-t border-line">
                @foreach ($stages as $stage)
                    @php
                        $index = $loop->index;
                        $currentIndex = $stages->search(fn ($s) => $s->id === $deal->stage_id);
                        $reached = $index <= $currentIndex;
                    @endphp
                    <form method="POST" action="{{ route('deals.move', $deal) }}" class="flex-1">
                        @csrf
                        <input type="hidden" name="stage_id" value="{{ $stage->id }}">
                        <button type="submit"
                                class="w-full border-r border-white/40 px-2 py-2.5 text-[12px] font-bold tracking-wide uppercase transition-colors last:border-r-0
                                       {{ $reached ? 'bg-indigo-brand text-white hover:bg-indigo-soft' : 'bg-gray-100 text-ink-500 hover:bg-gray-200' }}">
                            {{ $stage->name }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        <div class="mt-5 grid gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
            <div class="space-y-5">
                <div class="card divide-y divide-line">
                    <div class="px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Details</p></div>
                    <dl class="space-y-3 px-5 py-4 text-sm">
                        <div class="flex gap-3">
                            <dt class="w-28 shrink-0 text-ink-500">Contact</dt>
                            <dd class="min-w-0 flex-1">
                                @if ($deal->person)
                                    <a href="{{ route('people.show', $deal->person) }}" class="font-medium text-link hover:underline">{{ $deal->person->name }}</a>
                                @else — @endif
                            </dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-28 shrink-0 text-ink-500">Organization</dt>
                            <dd class="min-w-0 flex-1">
                                @if ($deal->organization)
                                    <a href="{{ route('organizations.show', $deal->organization) }}" class="font-medium text-link hover:underline">{{ $deal->organization->name }}</a>
                                @else — @endif
                            </dd>
                        </div>
                        @foreach ([
                            'Stage' => $deal->stage->name,
                            'Probability' => $deal->stage->probability . '%',
                            'Label' => $deal->label,
                            'Owner' => $deal->owner?->name,
                            'Created' => $deal->created_at->format('M j, Y'),
                            'Won' => $deal->won_at?->format('M j, Y'),
                            'Lost' => $deal->lost_at?->format('M j, Y'),
                            'Lost reason' => $deal->lost_reason,
                        ] as $label => $value)
                            @if ($value)
                                <div class="flex gap-3">
                                    <dt class="w-28 shrink-0 text-ink-500">{{ $label }}</dt>
                                    <dd class="min-w-0 flex-1 font-medium text-ink-900">{{ $value }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </div>

                <div class="card p-5">
                    <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Weighted value</p>
                    <p class="mt-1.5 text-2xl font-bold text-ink-900 tabular-nums">
                        ${{ number_format((float) $deal->value * $deal->stage->probability / 100) }}
                    </p>
                    <p class="mt-1 text-xs text-ink-500">{{ $deal->stage->probability }}% of ${{ number_format((float) $deal->value) }}</p>

                    <form method="POST" action="{{ route('deals.destroy', $deal) }}" class="mt-5 border-t border-line pt-4"
                          onsubmit="return confirm('Delete this deal?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm w-full"><x-icon name="trash" class="size-4" /> Delete deal</button>
                    </form>
                </div>
            </div>

            <div class="space-y-5">
                <div class="card">
                    <div class="border-b border-line px-5 py-3">
                        <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Schedule an activity</p>
                    </div>
                    <form method="POST" action="{{ route('activities.store') }}" class="grid gap-3 p-4 sm:grid-cols-[minmax(0,1fr)_140px_130px_auto]">
                        @csrf
                        <input type="hidden" name="deal_id" value="{{ $deal->id }}">
                        <input type="hidden" name="person_id" value="{{ $deal->person_id }}">
                        <input type="hidden" name="organization_id" value="{{ $deal->organization_id }}">
                        <input name="subject" class="field" placeholder="Follow-up call" required>
                        <select name="type" class="field">
                            @foreach (\App\Models\Activity::TYPES as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                        <input name="due_date" type="date" class="field" value="{{ now()->addDay()->toDateString() }}">
                        <button type="submit" class="btn-primary">Add</button>
                    </form>
                </div>

                <x-record-activities :activities="$deal->activities" />
                <x-record-notes :notes="$deal->notes" field="deal_id" :id="$deal->id" />
            </div>
        </div>
    </div>

    <x-modal name="deal-edit" title="Edit deal">
        <x-deal-form :stages="$stages" :people="$people" :organizations="$organizations" :deal="$deal" />
    </x-modal>

    <x-modal name="deal-lost" title="Mark deal as lost" width="max-w-md">
        <form method="POST" action="{{ route('deals.status', $deal) }}" class="space-y-4 p-5">
            @csrf
            <input type="hidden" name="status" value="lost">
            <div>
                <label class="field-label" for="lost-reason">Lost reason</label>
                <select id="lost-reason" name="lost_reason" class="field">
                    @foreach (['Lost to competitor', 'Budget frozen', 'No decision made', 'Timing not right', 'Feature gap', 'Other'] as $reason)
                        <option value="{{ $reason }}">{{ $reason }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary bg-danger hover:bg-red-700">Mark as lost</button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
