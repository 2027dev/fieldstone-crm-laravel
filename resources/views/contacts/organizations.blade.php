<x-layouts.app title="Organizations" breadcrumb="Contacts">
    <x-slot:subnav>
        <x-contacts-subnav />
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-3">
            <x-modal name="organization-create" title="Add organization" :open="request()->boolean('new')">
                <x-slot:trigger>
                    <button type="button" class="btn-primary">
                        <x-icon name="plus" class="size-4" stroke-width="2.4" /> Organization
                    </button>
                </x-slot:trigger>

                <form method="POST" action="{{ route('organizations.store') }}" class="space-y-4 p-5">
                    @csrf
                    <div>
                        <label class="field-label" for="o-name">Name</label>
                        <input id="o-name" name="name" class="field" required autofocus placeholder="Acme Inc.">
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="field-label" for="o-industry">Industry</label>
                            <input id="o-industry" name="industry" class="field">
                        </div>
                        <div>
                            <label class="field-label" for="o-employees">Employees</label>
                            <input id="o-employees" name="employee_count" type="number" min="0" class="field">
                        </div>
                        <div>
                            <label class="field-label" for="o-website">Website</label>
                            <input id="o-website" name="website" class="field" placeholder="acme.com">
                        </div>
                        <div>
                            <label class="field-label" for="o-label">Label</label>
                            <input id="o-label" name="label" class="field">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="field-label" for="o-address">Address</label>
                            <input id="o-address" name="address" class="field">
                        </div>
                    </div>
                    <div class="flex justify-end gap-2 border-t border-line pt-4">
                        <button type="button" class="btn-secondary" x-on:click="open = false">Cancel</button>
                        <button type="submit" class="btn-primary">Save</button>
                    </div>
                </form>
            </x-modal>

            <form action="{{ route('organizations.index') }}" method="GET" class="relative w-full max-w-xs">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search organizations" class="field py-1.5 pl-9 text-sm">
            </form>

            <span class="ml-auto flex items-center gap-1.5 text-sm text-ink-500">
                <x-icon name="refresh" class="size-4" /> {{ $organizations->total() }} {{ Str::plural('organization', $organizations->total()) }}
            </span>
        </div>

        <div class="card mt-4 overflow-hidden">
            @if ($organizations->isEmpty())
                <x-empty-state icon="building" title="No organizations yet"
                               description="Group your contacts under the companies they work for to see the full account picture.">
                    <button type="button" class="btn-primary" x-on:click="$dispatch('open-modal', 'organization-create')">
                        <x-icon name="plus" class="size-4" /> Organization
                    </button>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1080px] border-collapse">
                        <thead>
                            <tr class="table-head">
                                <th class="w-10 px-4 py-2.5"><input type="checkbox" class="rounded border-gray-300" aria-label="Select all"></th>
                                <th class="px-4 py-2.5">Name</th>
                                <th class="px-4 py-2.5">Industry</th>
                                <th class="px-4 py-2.5">Address</th>
                                <th class="px-4 py-2.5 text-right">People</th>
                                <th class="px-4 py-2.5 text-right">Open deals</th>
                                <th class="px-4 py-2.5 text-right">Closed deals</th>
                                <th class="px-4 py-2.5 text-right">Won value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($organizations as $organization)
                                <tr class="hover:bg-gray-50">
                                    <td class="table-cell"><input type="checkbox" class="rounded border-gray-300" aria-label="Select {{ $organization->name }}"></td>
                                    <td class="table-cell">
                                        <a href="{{ route('organizations.show', $organization) }}" class="flex items-center gap-2.5 font-semibold text-ink-900 hover:text-link">
                                            <span class="flex size-7 items-center justify-center rounded-md bg-gray-100 text-ink-500">
                                                <x-icon name="building" class="size-4" />
                                            </span>
                                            {{ $organization->name }}
                                        </a>
                                    </td>
                                    <td class="table-cell">{{ $organization->industry ?: '—' }}</td>
                                    <td class="table-cell text-ink-700">{{ $organization->address ?: '—' }}</td>
                                    <td class="table-cell text-right tabular-nums">{{ $organization->people_count }}</td>
                                    <td class="table-cell text-right tabular-nums">{{ $organization->open_deals_count }}</td>
                                    <td class="table-cell text-right tabular-nums">{{ $organization->closed_deals_count }}</td>
                                    <td class="table-cell text-right font-semibold tabular-nums">
                                        {{ $organization->won_value ? '$' . number_format((float) $organization->won_value) : '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($organizations->hasPages())
                    <div class="border-t border-line px-4 py-3">{{ $organizations->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
