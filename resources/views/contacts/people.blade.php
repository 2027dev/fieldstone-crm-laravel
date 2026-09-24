<x-layouts.app title="People" breadcrumb="Contacts">
    <x-slot:subnav>
        <x-contacts-subnav />
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-3">
            <x-modal name="person-create" title="Add person" :open="request()->boolean('new')">
                <x-slot:trigger>
                    <button type="button" class="btn-primary">
                        <x-icon name="plus" class="size-4" stroke-width="2.4" /> Person
                    </button>
                </x-slot:trigger>

                <form method="POST" action="{{ route('people.store') }}" class="space-y-4 p-5">
                    @csrf
                    <div>
                        <label class="field-label" for="p-name">Name</label>
                        <input id="p-name" name="name" class="field" required autofocus placeholder="Jane Doe">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="p-org">Organization</label>
                            <select id="p-org" name="organization_id" class="field">
                                <option value="">—</option>
                                @foreach ($organizations as $organization)
                                    <option value="{{ $organization->id }}">{{ $organization->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="field-label" for="p-job">Job title</label>
                            <input id="p-job" name="job_title" class="field">
                        </div>
                        <div>
                            <label class="field-label" for="p-email">Email</label>
                            <input id="p-email" name="email" type="email" class="field">
                        </div>
                        <div>
                            <label class="field-label" for="p-phone">Phone</label>
                            <input id="p-phone" name="phone" class="field">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="p-label">Label</label>
                            <input id="p-label" name="label" class="field" placeholder="Customer, Hot lead…">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                        <button type="submit" class="btn-primary">Save</button>
                    </div>
                </form>
            </x-modal>

            <form action="{{ route('people.index') }}" method="GET" class="relative w-full max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search people" class="field py-1.5 pl-9 text-sm">
                @if ($filter)
                    <input type="hidden" name="filter" value="{{ $filter }}">
                @endif
            </form>

            <div class="ml-auto flex items-center gap-2">
                <span class="flex items-center gap-1.5 text-sm text-ink-500">
                    <x-icon name="refresh" class="size-4" /> {{ $people->total() }} {{ Str::plural('person', $people->total()) }}
                </span>

                <div class="relative" x-data="{ open: false }">
                    <button type="button" x-on:click="open = !open" class="btn-secondary btn-sm">
                        <x-icon name="filter" class="size-4" /> Filter <x-icon name="chevron-down" class="size-3.5" />
                    </button>
                    <div x-show="open" x-cloak x-on:click.outside="open = false"
                         class="absolute right-0 z-30 mt-2 w-56 overflow-hidden rounded-xl border border-line bg-white py-1 shadow-xl">
                        @foreach ([
                            '' => 'All people',
                            'with-activities' => 'Total activities > 0',
                            'no-activities' => 'No activities',
                            'open-deals' => 'Has open deals',
                        ] as $value => $label)
                            <a href="{{ route('people.index', array_filter(['filter' => $value, 'q' => request('q')])) }}"
                               class="flex items-center justify-between px-4 py-2 text-sm hover:bg-gray-50 {{ $filter === $value ? 'font-semibold text-indigo-brand' : 'text-ink-700' }}">
                                {{ $label }}
                                @if ($filter === $value) <x-icon name="check" class="size-4" /> @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                <a href="{{ route('contacts.duplicates') }}" class="btn-secondary btn-sm" title="More">
                    <x-icon name="more" class="size-4" />
                </a>
            </div>
        </div>

        @if (! $filter && $people->total() > 0)
            <div x-data="{ show: true }" x-show="show" class="mt-4 flex items-start gap-3 rounded-xl bg-indigo-50 px-4 py-3.5">
                <x-icon name="bulb" class="mt-0.5 size-5 shrink-0 text-indigo-brand" />
                <div class="min-w-0 flex-1">
                    <p class="text-[15px] font-bold text-ink-900">Find the right contacts faster</p>
                    <p class="mt-0.5 text-sm text-ink-700">
                        Filters make it easy to manage a growing contact list. Try the suggested filter below or adjust it as needed.
                    </p>
                    <div class="mt-2.5 flex flex-wrap items-center gap-3 text-sm">
                        <span class="chip bg-white text-ink-700">
                            <span class="font-mono text-[10px] text-ink-400">123</span> Total activities &gt; 0
                        </span>
                        <a href="{{ route('contacts.duplicates') }}" class="flex items-center gap-1 text-ink-500 hover:text-ink-900">
                            <x-icon name="filter" class="size-3.5" /> Add condition
                        </a>
                        <a href="{{ route('people.index') }}" class="text-ink-500 hover:text-ink-900">Clear</a>
                    </div>
                </div>
                <a href="{{ route('people.index', ['filter' => 'with-activities']) }}" class="btn-primary btn-sm shrink-0">Apply filter</a>
                <button type="button" x-on:click="show = false" class="shrink-0 text-ink-400 hover:text-ink-700">
                    <x-icon name="x" class="size-5" />
                </button>
            </div>
        @endif

        <div class="card mt-4 overflow-hidden">
            @if ($people->isEmpty())
                <x-empty-state icon="contacts" title="No people yet"
                               description="Add a person so their deals, emails and activities can all be linked together.">
                    <button type="button" class="btn-primary" x-on:click="$dispatch('open-modal', 'person-create')">
                        <x-icon name="plus" class="size-4" /> Person
                    </button>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1140px] border-collapse">
                        <thead>
                            <tr class="table-head">
                                <th class="w-10 px-4 py-2.5"><input type="checkbox" class="rounded border-gray-300" aria-label="Select all"></th>
                                <th class="px-4 py-2.5">Name</th>
                                <th class="px-4 py-2.5">Organization</th>
                                <th class="px-4 py-2.5">Email</th>
                                <th class="px-4 py-2.5">Phone</th>
                                <th class="px-4 py-2.5 text-right">Open deals</th>
                                <th class="px-4 py-2.5 text-right">Closed deals</th>
                                <th class="px-4 py-2.5 text-right">Won value</th>
                                <th class="w-10 px-4 py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($people as $person)
                                <tr class="group hover:bg-gray-50">
                                    <td class="table-cell"><input type="checkbox" class="rounded border-gray-300" aria-label="Select {{ $person->display_name }}"></td>
                                    <td class="table-cell">
                                        <a href="{{ route('people.show', $person) }}" class="flex items-center gap-2.5 font-semibold text-ink-900 hover:text-link">
                                            <x-avatar :name="$person->name" size="xs" />
                                            <span class="whitespace-nowrap">{{ $person->name }}</span>
                                        </a>
                                    </td>
                                    <td class="table-cell">
                                        @if ($person->organization)
                                            <a href="{{ route('organizations.show', $person->organization) }}" class="hover:text-link">{{ $person->organization->name }}</a>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell whitespace-nowrap">
                                        @if ($person->email)
                                            <a href="mailto:{{ $person->email }}" class="text-link hover:underline">{{ $person->email }}</a>
                                            <span class="text-ink-400">({{ $person->email_label }})</span>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell whitespace-nowrap">
                                        @if ($person->phone)
                                            <a href="tel:{{ $person->phone }}" class="text-link hover:underline">{{ $person->phone }}</a>
                                        @else
                                            <span class="text-ink-400">—</span>
                                        @endif
                                    </td>
                                    <td class="table-cell text-right tabular-nums">{{ $person->open_deals_count }}</td>
                                    <td class="table-cell text-right tabular-nums">{{ $person->closed_deals_count }}</td>
                                    <td class="table-cell text-right font-semibold tabular-nums">
                                        {{ $person->won_value ? '$' . number_format((float) $person->won_value) : '—' }}
                                    </td>
                                    <td class="table-cell text-right">
                                        <a href="{{ route('people.show', $person) }}" class="text-ink-400 opacity-0 group-hover:opacity-100 hover:text-ink-900">
                                            <x-icon name="more" class="size-4" />
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($people->hasPages())
                    <div class="border-t border-line px-4 py-3">{{ $people->links() }}</div>
                @endif
            @endif
        </div>

        @if (\App\Models\Person::where('is_sample', true)->exists())
            <div class="mt-6 flex flex-wrap items-center justify-center gap-3 rounded-xl bg-gradient-to-b from-indigo-50/70 to-transparent px-4 py-8">
                <a href="{{ route('contacts.duplicates') }}" class="btn-secondary">
                    <x-icon name="download" class="size-4" /> Import contacts
                </a>
                <form method="POST" action="{{ route('sample-data.destroy') }}">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-secondary">Remove sample data</button>
                </form>
            </div>
        @endif
    </div>
</x-layouts.app>
