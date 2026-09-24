<x-layouts.app :title="$organization->name" breadcrumb="Contacts">
    <div class="p-4 lg:p-5">
        <div class="grid gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
            <div class="space-y-5">
                <div class="card p-5">
                    <div class="flex items-start gap-4">
                        <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-navy-900 text-white">
                            <x-icon name="building" class="size-7" />
                        </span>
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-bold text-ink-900">{{ $organization->name }}</h2>
                            <p class="text-sm text-ink-500">{{ $organization->industry ?: 'No industry set' }}</p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @if ($organization->website)
                            <a href="https://{{ Str::after($organization->website, '://') }}" target="_blank" rel="noopener"
                               class="btn-secondary btn-sm"><x-icon name="globe" class="size-4" /> Website</a>
                        @endif
                        <button type="button" class="btn-secondary btn-sm" x-on:click="$dispatch('open-modal', 'organization-edit')">
                            <x-icon name="pencil" class="size-4" /> Edit
                        </button>
                    </div>
                </div>

                <div class="card divide-y divide-line">
                    <div class="px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Details</p></div>
                    <dl class="space-y-3 px-5 py-4 text-sm">
                        @foreach ([
                            'Address' => $organization->address,
                            'Website' => $organization->website,
                            'Industry' => $organization->industry,
                            'Employees' => $organization->employee_count ? number_format($organization->employee_count) : null,
                            'Owner' => $organization->owner?->name,
                        ] as $label => $value)
                            <div class="flex gap-3">
                                <dt class="w-24 shrink-0 text-ink-500">{{ $label }}</dt>
                                <dd class="min-w-0 flex-1 font-medium break-words text-ink-900">{{ $value ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="card p-5">
                    <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">People</p>
                    <ul class="mt-3 space-y-2">
                        @forelse ($organization->people as $person)
                            <li>
                                <a href="{{ route('people.show', $person) }}" class="flex items-center gap-2.5 rounded-lg px-2 py-1.5 text-sm hover:bg-gray-50">
                                    <x-avatar :name="$person->name" size="xs" />
                                    <span class="min-w-0 flex-1 truncate font-medium">{{ $person->name }}</span>
                                    <span class="shrink-0 text-xs text-ink-500">{{ $person->job_title }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="text-sm text-ink-400">No people linked yet.</li>
                        @endforelse
                    </ul>

                    <form method="POST" action="{{ route('organizations.destroy', $organization) }}" class="mt-5 border-t border-line pt-4"
                          onsubmit="return confirm('Delete this organization?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm w-full"><x-icon name="trash" class="size-4" /> Delete organization</button>
                    </form>
                </div>
            </div>

            <div class="space-y-5">
                <div class="card">
                    <div class="border-b border-line px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Deals</p></div>
                    <ul class="divide-y divide-line">
                        @forelse ($organization->deals as $deal)
                            <li>
                                <a href="{{ route('deals.show', $deal) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                                    <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $deal->title }}</span>
                                    <span class="chip {{ ['open' => 'bg-indigo-50 text-indigo-brand', 'won' => 'bg-brand-50 text-brand-600', 'lost' => 'bg-red-50 text-danger'][$deal->status] }}">
                                        {{ ucfirst($deal->status) }}
                                    </span>
                                    <span class="w-16 shrink-0 text-right text-xs text-ink-500">{{ $deal->stage->name }}</span>
                                    <span class="w-24 shrink-0 text-right text-sm font-semibold tabular-nums">${{ number_format((float) $deal->value) }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-sm text-ink-400">No deals yet.</li>
                        @endforelse
                    </ul>
                </div>

                <x-record-notes :notes="$organization->notes" field="organization_id" :id="$organization->id" />
                <x-record-activities :activities="$organization->activities" />
            </div>
        </div>
    </div>

    <x-modal name="organization-edit" title="Edit organization">
        <form method="POST" action="{{ route('organizations.update', $organization) }}" class="space-y-4 p-5">
            @csrf @method('PATCH')
            <div>
                <label class="field-label" for="eo-name">Name</label>
                <input id="eo-name" name="name" class="field" value="{{ $organization->name }}" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="eo-industry">Industry</label>
                    <input id="eo-industry" name="industry" class="field" value="{{ $organization->industry }}">
                </div>
                <div>
                    <label class="field-label" for="eo-employees">Employees</label>
                    <input id="eo-employees" name="employee_count" type="number" min="0" class="field" value="{{ $organization->employee_count }}">
                </div>
                <div>
                    <label class="field-label" for="eo-website">Website</label>
                    <input id="eo-website" name="website" class="field" value="{{ $organization->website }}">
                </div>
                <div>
                    <label class="field-label" for="eo-label">Label</label>
                    <input id="eo-label" name="label" class="field" value="{{ $organization->label }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="eo-address">Address</label>
                    <input id="eo-address" name="address" class="field" value="{{ $organization->address }}">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
