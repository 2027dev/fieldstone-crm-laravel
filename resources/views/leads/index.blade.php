<x-layouts.app title="Leads Inbox" breadcrumb="Leads">
    <x-slot:subnav>
        <x-leads-subnav :counts="$counts" />
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-3">
            <x-modal name="lead-create" title="Add lead" :open="request()->boolean('new')">
                <x-slot:trigger>
                    <button type="button" class="btn-primary"><x-icon name="plus" class="size-4" stroke-width="2.4" /> Lead</button>
                </x-slot:trigger>

                <form method="POST" action="{{ route('leads.store') }}" class="space-y-4 p-5">
                    @csrf
                    <div>
                        <label class="field-label" for="l-title">Lead title</label>
                        <input id="l-title" name="title" class="field" required autofocus placeholder="Inbound demo request">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="l-person">Contact person</label>
                            <select id="l-person" name="person_id" class="field">
                                <option value="">—</option>
                                @foreach ($people as $person)
                                    <option value="{{ $person->id }}">{{ $person->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="l-org">Organization</label>
                            <select id="l-org" name="organization_id" class="field">
                                <option value="">—</option>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="l-value">Value</label>
                            <input id="l-value" name="value" type="number" step="0.01" min="0" class="field">
                        </div>
                        <div>
                            <label class="field-label" for="l-source">Source</label>
                            <input id="l-source" name="source" class="field" placeholder="Web form, Referral…">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="l-label">Label</label>
                            <input id="l-label" name="label" class="field" placeholder="Hot, Warm, Cold">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="l-note">Note</label>
                            <textarea id="l-note" name="note" rows="3" class="field"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                        <button type="submit" class="btn-primary">Save lead</button>
                    </div>
                </form>
            </x-modal>

            <form action="{{ route('leads.index') }}" method="GET" class="relative w-full max-w-xs">
                <input type="hidden" name="view" value="{{ $view }}">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search leads" class="field py-1.5 pl-9 text-sm">
            </form>

            <span class="ml-auto text-sm text-ink-500">{{ $leads->total() }} {{ Str::plural('lead', $leads->total()) }}</span>
        </div>

        @if ($leads->isEmpty())
            <x-empty-state icon="leads" title="Add your first lead"
                           description="Organize and qualify incoming opportunities here – then convert the right ones into deals.">
                <button type="button" class="btn-secondary" x-on:click="$dispatch('open-modal', 'lead-create')">
                    <x-icon name="plus" class="size-4" /> Lead
                </button>
                <a href="{{ route('leads.web-forms') }}" class="btn-secondary">
                    <x-icon name="download" class="size-4" /> Import leads
                </a>
            </x-empty-state>
        @else
            <div class="card mt-4 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1240px] border-collapse">
                        <thead>
                            <tr class="table-head">
                                <th class="w-10 px-4 py-2.5"><input type="checkbox" class="rounded border-gray-300" aria-label="Select all"></th>
                                <th class="px-4 py-2.5">Title</th>
                                <th class="px-4 py-2.5">Contact person</th>
                                <th class="px-4 py-2.5">Organization</th>
                                <th class="px-4 py-2.5">Label</th>
                                <th class="px-4 py-2.5">Source</th>
                                <th class="px-4 py-2.5">Next activity</th>
                                <th class="px-4 py-2.5 text-right">Value</th>
                                <th class="px-4 py-2.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($leads as $lead)
                                <tr class="hover:bg-gray-50">
                                    <td class="table-cell"><input type="checkbox" class="rounded border-gray-300" aria-label="Select lead"></td>
                                    <td class="table-cell">
                                        <a href="{{ route('leads.show', $lead) }}" class="font-semibold text-ink-900 hover:text-link">{{ $lead->title }}</a>
                                    </td>
                                    <td class="table-cell">{{ $lead->person?->name ?: '—' }}</td>
                                    <td class="table-cell">{{ $lead->organization?->name ?: '—' }}</td>
                                    <td class="table-cell">
                                        @if ($lead->label)
                                            <span class="chip {{ ['Hot' => 'bg-red-50 text-danger', 'Warm' => 'bg-amber-50 text-warn', 'Cold' => 'bg-blue-50 text-link'][$lead->label] ?? 'bg-gray-100 text-ink-700' }}">
                                                {{ $lead->label }}
                                            </span>
                                        @else — @endif
                                    </td>
                                    <td class="table-cell text-ink-700">{{ $lead->source ?: '—' }}</td>
                                    <td class="table-cell">
                                        @if ($lead->open_activities_count > 0)
                                            <span class="chip bg-indigo-50 text-indigo-brand">{{ $lead->open_activities_count }} scheduled</span>
                                        @else
                                            <span class="chip bg-red-50 text-danger">No activity</span>
                                        @endif
                                    </td>
                                    <td class="table-cell text-right font-semibold tabular-nums">
                                        {{ $lead->value ? '$' . number_format((float) $lead->value) : '—' }}
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex items-center justify-end gap-1.5">
                                            @if (! $lead->converted_deal_id)
                                                <form method="POST" action="{{ route('leads.convert', $lead) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-secondary btn-sm" title="Convert to deal">
                                                        <x-icon name="convert" class="size-4" /> Convert
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('leads.archive', $lead) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-ghost btn-sm" title="{{ $lead->archived_at ? 'Restore' : 'Archive' }}">
                                                        <x-icon name="archive" class="size-4" />
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('deals.show', $lead->converted_deal_id) }}" class="btn-secondary btn-sm">
                                                    View deal <x-icon name="chevron-right" class="size-3.5" />
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($leads->hasPages())
                    <div class="border-t border-line px-4 py-3">{{ $leads->links() }}</div>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>
