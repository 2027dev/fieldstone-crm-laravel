<x-layouts.app title="Deal list" breadcrumb="Deals">
    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-2">
            <x-modal name="deal-create" title="Add deal">
                <x-slot:trigger>
                    <button type="button" class="btn-primary"><x-icon name="plus" class="size-4" stroke-width="2.4" /> Deal</button>
                </x-slot:trigger>
                <x-deal-form :stages="$stageOptions" :people="$people" :organizations="$organizations" />
            </x-modal>

            <div class="flex overflow-hidden rounded-lg border border-line">
                <a href="{{ route('deals.index', ['pipeline' => $pipeline->id]) }}"
                   class="flex size-9 items-center justify-center text-ink-500 hover:bg-gray-50">
                    <x-icon name="columns" class="size-[18px]" />
                </a>
                <span class="flex size-9 items-center justify-center border-l border-line bg-indigo-50 text-indigo-brand">
                    <x-icon name="list" class="size-[18px]" />
                </span>
            </div>

            <div class="flex items-center gap-1">
                @foreach (['open' => 'Open', 'won' => 'Won', 'lost' => 'Lost', 'all' => 'All'] as $value => $label)
                    <a href="{{ route('deals.list', ['pipeline' => $pipeline->id, 'status' => $value]) }}"
                       class="rounded-md px-2.5 py-1.5 text-sm font-medium {{ $status === $value ? 'bg-indigo-50 font-semibold text-indigo-brand' : 'text-ink-500 hover:bg-gray-50' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <form action="{{ route('deals.list') }}" method="GET" class="relative w-56">
                <input type="hidden" name="pipeline" value="{{ $pipeline->id }}">
                <input type="hidden" name="status" value="{{ $status }}">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search deals" class="field py-1.5 pl-9 text-sm">
            </form>

            <span class="ml-auto text-sm text-ink-500">{{ $deals->total() }} {{ Str::plural('deal', $deals->total()) }}</span>
        </div>

        <div class="card mt-4 overflow-hidden">
            @if ($deals->isEmpty())
                <x-empty-state icon="deals" title="No deals here yet"
                               description="Create an opportunity to move it through your sales process and close faster.">
                    <button type="button" class="btn-primary" x-on:click="$dispatch('open-modal', 'deal-create')">
                        <x-icon name="plus" class="size-4" /> Deal
                    </button>
                </x-empty-state>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] border-collapse">
                        <thead>
                            <tr class="table-head">
                                <th class="px-4 py-2.5">Title</th>
                                <th class="px-4 py-2.5">Organization</th>
                                <th class="px-4 py-2.5">Contact person</th>
                                <th class="px-4 py-2.5">Stage</th>
                                <th class="px-4 py-2.5">Status</th>
                                <th class="px-4 py-2.5">Expected close</th>
                                <th class="px-4 py-2.5 text-right">Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deals as $deal)
                                <tr class="hover:bg-gray-50">
                                    <td class="table-cell">
                                        <a href="{{ route('deals.show', $deal) }}" class="font-semibold text-ink-900 hover:text-link">{{ $deal->title }}</a>
                                    </td>
                                    <td class="table-cell">{{ $deal->organization?->name ?: '—' }}</td>
                                    <td class="table-cell">{{ $deal->person?->name ?: '—' }}</td>
                                    <td class="table-cell">{{ $deal->stage->name }}</td>
                                    <td class="table-cell">
                                        <span class="chip {{ ['open' => 'bg-indigo-50 text-indigo-brand', 'won' => 'bg-brand-50 text-brand-600', 'lost' => 'bg-red-50 text-danger'][$deal->status] }}">
                                            {{ ucfirst($deal->status) }}
                                        </span>
                                    </td>
                                    <td class="table-cell text-ink-700">{{ $deal->expected_close_date?->format('M j, Y') ?: '—' }}</td>
                                    <td class="table-cell text-right font-semibold tabular-nums">${{ number_format((float) $deal->value) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($deals->hasPages())
                    <div class="border-t border-line px-4 py-3">{{ $deals->links() }}</div>
                @endif
            @endif
        </div>
    </div>
</x-layouts.app>
