<x-layouts.app :title="$lead->title" breadcrumb="Leads">
    <div class="p-4 lg:p-5">
        <div class="grid gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
            <div class="space-y-5">
                <div class="card p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-brand">
                            <x-icon name="leads" class="size-6" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="text-lg leading-snug font-bold text-ink-900">{{ $lead->title }}</h2>
                            <p class="mt-0.5 text-sm text-ink-500">
                                {{ $lead->source ?: 'Manual entry' }} · added {{ $lead->created_at->diffForHumans() }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @if ($lead->converted_deal_id)
                            <a href="{{ route('deals.show', $lead->converted_deal_id) }}" class="btn-primary btn-sm">
                                <x-icon name="deals" class="size-4" /> View deal
                            </a>
                        @else
                            <form method="POST" action="{{ route('leads.convert', $lead) }}">
                                @csrf
                                <button type="submit" class="btn-primary btn-sm"><x-icon name="convert" class="size-4" /> Convert to deal</button>
                            </form>
                            <form method="POST" action="{{ route('leads.archive', $lead) }}">
                                @csrf
                                <button type="submit" class="btn-secondary btn-sm">
                                    <x-icon name="archive" class="size-4" /> {{ $lead->archived_at ? 'Restore' : 'Archive' }}
                                </button>
                            </form>
                        @endif
                        <button type="button" class="btn-secondary btn-sm" x-on:click="$dispatch('open-modal', 'lead-edit')">
                            <x-icon name="pencil" class="size-4" /> Edit
                        </button>
                    </div>
                </div>

                <div class="card divide-y divide-line">
                    <div class="px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Details</p></div>
                    <dl class="space-y-3 px-5 py-4 text-sm">
                        <div class="flex gap-3">
                            <dt class="w-28 shrink-0 text-ink-500">Contact</dt>
                            <dd class="min-w-0 flex-1">
                                @if ($lead->person)
                                    <a href="{{ route('people.show', $lead->person) }}" class="font-medium text-link hover:underline">{{ $lead->person->name }}</a>
                                @else — @endif
                            </dd>
                        </div>
                        <div class="flex gap-3">
                            <dt class="w-28 shrink-0 text-ink-500">Organization</dt>
                            <dd class="min-w-0 flex-1">
                                @if ($lead->organization)
                                    <a href="{{ route('organizations.show', $lead->organization) }}" class="font-medium text-link hover:underline">{{ $lead->organization->name }}</a>
                                @else — @endif
                            </dd>
                        </div>
                        @foreach ([
                            'Value' => $lead->value ? '$' . number_format((float) $lead->value) : null,
                            'Label' => $lead->label,
                            'Source' => $lead->source,
                            'Owner' => $lead->owner?->name,
                            'Archived' => $lead->archived_at?->format('M j, Y'),
                        ] as $label => $value)
                            @if ($value)
                                <div class="flex gap-3">
                                    <dt class="w-28 shrink-0 text-ink-500">{{ $label }}</dt>
                                    <dd class="min-w-0 flex-1 font-medium text-ink-900">{{ $value }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>

                    @if ($lead->note)
                        <div class="px-5 py-4">
                            <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Lead note</p>
                            <p class="mt-1.5 text-sm whitespace-pre-line text-ink-700">{{ $lead->note }}</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('leads.destroy', $lead) }}" class="p-5"
                          onsubmit="return confirm('Delete this lead?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm w-full"><x-icon name="trash" class="size-4" /> Delete lead</button>
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
                        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                        <input type="hidden" name="person_id" value="{{ $lead->person_id }}">
                        <input name="subject" class="field" placeholder="Qualification call" required>
                        <select name="type" class="field">
                            @foreach (\App\Models\Activity::TYPES as $value => $meta)
                                <option value="{{ $value }}">{{ $meta['label'] }}</option>
                            @endforeach
                        </select>
                        <input name="due_date" type="date" class="field" value="{{ now()->addDay()->toDateString() }}">
                        <button type="submit" class="btn-primary">Add</button>
                    </form>
                </div>

                <x-record-activities :activities="$lead->activities" />
                <x-record-notes :notes="$lead->notes" field="lead_id" :id="$lead->id" />
            </div>
        </div>
    </div>

    <x-modal name="lead-edit" title="Edit lead">
        <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-4 p-5">
            @csrf @method('PATCH')
            <div>
                <label class="field-label" for="el-title">Lead title</label>
                <input id="el-title" name="title" class="field" value="{{ $lead->title }}" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="el-person">Contact person</label>
                    <select id="el-person" name="person_id" class="field">
                        <option value="">—</option>
                        @foreach ($people as $person)
                            <option value="{{ $person->id }}" @selected($lead->person_id === $person->id)>{{ $person->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="el-org">Organization</label>
                    <select id="el-org" name="organization_id" class="field">
                        <option value="">—</option>
                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}" @selected($lead->organization_id === $organization->id)>{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="el-value">Value</label>
                    <input id="el-value" name="value" type="number" step="0.01" min="0" class="field" value="{{ $lead->value }}">
                </div>
                <div>
                    <label class="field-label" for="el-source">Source</label>
                    <input id="el-source" name="source" class="field" value="{{ $lead->source }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="el-label">Label</label>
                    <input id="el-label" name="label" class="field" value="{{ $lead->label }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="el-note">Note</label>
                    <textarea id="el-note" name="note" rows="3" class="field">{{ $lead->note }}</textarea>
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
