<x-layouts.app :title="$person->display_name" breadcrumb="Contacts">
    <div class="p-4 lg:p-5">
        <div class="grid gap-5 lg:grid-cols-[340px_minmax(0,1fr)]">
            <div class="space-y-5">
                <div class="card p-5">
                    <div class="flex items-start gap-4">
                        <x-avatar :name="$person->name" size="lg" />
                        <div class="min-w-0">
                            <h2 class="truncate text-xl font-bold text-ink-900">{{ $person->name }}</h2>
                            <p class="text-sm text-ink-500">{{ $person->job_title ?: 'No job title' }}</p>
                            @if ($person->organization)
                                <a href="{{ route('organizations.show', $person->organization) }}" class="mt-1 inline-flex items-center gap-1.5 text-sm text-link hover:underline">
                                    <x-icon name="building" class="size-3.5" /> {{ $person->organization->name }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="mt-5 flex flex-wrap gap-2">
                        @if ($person->phone)
                            <a href="tel:{{ $person->phone }}" class="btn-secondary btn-sm"><x-icon name="phone" class="size-4" /> Call</a>
                        @endif
                        @if ($person->email)
                            <a href="mailto:{{ $person->email }}" class="btn-secondary btn-sm"><x-icon name="mail" class="size-4" /> Email</a>
                        @endif
                        <button type="button" class="btn-secondary btn-sm" x-on:click="$dispatch('open-modal', 'person-edit')">
                            <x-icon name="pencil" class="size-4" /> Edit
                        </button>
                    </div>
                </div>

                <div class="card divide-y divide-line">
                    <div class="px-5 py-3">
                        <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Details</p>
                    </div>
                    <dl class="space-y-3 px-5 py-4 text-sm">
                        @foreach ([
                            'Email' => $person->email,
                            'Phone' => $person->phone,
                            'Job title' => $person->job_title,
                            'Label' => $person->label,
                            'Owner' => $person->owner?->name,
                            'Created' => $person->created_at->format('M j, Y'),
                        ] as $label => $value)
                            <div class="flex gap-3">
                                <dt class="w-24 shrink-0 text-ink-500">{{ $label }}</dt>
                                <dd class="min-w-0 flex-1 font-medium break-words text-ink-900">{{ $value ?: '—' }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>

                <div class="card p-5">
                    <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Open deals</p>
                    <div class="mt-3 space-y-2">
                        @forelse ($person->deals->where('status', 'open') as $deal)
                            <a href="{{ route('deals.show', $deal) }}" class="flex items-center justify-between rounded-lg border border-line px-3 py-2 text-sm hover:border-indigo-brand/40 hover:bg-indigo-50/40">
                                <span class="min-w-0 flex-1 truncate font-medium">{{ $deal->title }}</span>
                                <span class="ml-3 shrink-0 font-semibold tabular-nums">${{ number_format((float) $deal->value) }}</span>
                            </a>
                        @empty
                            <p class="text-sm text-ink-400">No open deals.</p>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('people.destroy', $person) }}" class="mt-5 border-t border-line pt-4"
                          onsubmit="return confirm('Delete this contact? This cannot be undone.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn-danger btn-sm w-full"><x-icon name="trash" class="size-4" /> Delete contact</button>
                    </form>
                </div>
            </div>

            <div class="space-y-5">
                <x-record-notes :notes="$person->notes" :field="'person_id'" :id="$person->id" />
                <x-record-activities :activities="$person->activities" />

                <div class="card">
                    <div class="border-b border-line px-5 py-3">
                        <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Email conversations</p>
                    </div>
                    <ul class="divide-y divide-line">
                        @forelse ($person->emailThreads as $thread)
                            <li>
                                <a href="{{ route('inbox.show', $thread) }}" class="flex items-start gap-3 px-5 py-3 hover:bg-gray-50">
                                    <x-icon name="mail" class="mt-0.5 size-4 shrink-0 text-ink-400" />
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-ink-900">{{ $thread->subject }}</p>
                                        <p class="truncate text-xs text-ink-500">{{ Str::limit(strip_tags((string) $thread->latestMessage?->body), 90) }}</p>
                                    </div>
                                    <span class="shrink-0 text-xs text-ink-400">{{ $thread->last_message_at?->diffForHumans() }}</span>
                                </a>
                            </li>
                        @empty
                            <li class="px-5 py-6 text-sm text-ink-400">No email conversations yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <x-modal name="person-edit" title="Edit person">
        <form method="POST" action="{{ route('people.update', $person) }}" class="space-y-4 p-5">
            @csrf @method('PATCH')
            <div>
                <label class="field-label" for="e-name">Name</label>
                <input id="e-name" name="name" class="field" value="{{ $person->name }}" required>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="field-label" for="e-org">Organization</label>
                    <select id="e-org" name="organization_id" class="field">
                        <option value="">—</option>
                        @foreach ($organizations as $organization)
                            <option value="{{ $organization->id }}" @selected($person->organization_id === $organization->id)>{{ $organization->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="field-label" for="e-job">Job title</label>
                    <input id="e-job" name="job_title" class="field" value="{{ $person->job_title }}">
                </div>
                <div>
                    <label class="field-label" for="e-email">Email</label>
                    <input id="e-email" name="email" type="email" class="field" value="{{ $person->email }}">
                </div>
                <div>
                    <label class="field-label" for="e-phone">Phone</label>
                    <input id="e-phone" name="phone" class="field" value="{{ $person->phone }}">
                </div>
                <div class="sm:col-span-2">
                    <label class="field-label" for="e-label">Label</label>
                    <input id="e-label" name="label" class="field" value="{{ $person->label }}">
                </div>
            </div>
            <div class="flex justify-end gap-2 border-t border-line pt-4">
                <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                <button type="submit" class="btn-primary">Save changes</button>
            </div>
        </form>
    </x-modal>
</x-layouts.app>
