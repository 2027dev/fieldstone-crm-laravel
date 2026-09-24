@php
    $totals = $stages->mapWithKeys(fn ($stage) => [
        $stage->id => [
            'count' => ($dealsByStage[$stage->id] ?? collect())->count(),
            'value' => ($dealsByStage[$stage->id] ?? collect())->sum(fn ($deal) => (float) $deal->value),
        ],
    ]);
    $pipelineValue = $totals->sum('value');
@endphp

<x-layouts.app title="Pipeline" breadcrumb="Deals">
    <div class="flex h-full flex-col">
        <div class="flex flex-wrap items-center gap-2 px-4 pt-4 lg:px-5">
            <x-modal name="deal-create" title="Add deal" :open="request()->boolean('new')">
                <x-slot:trigger>
                    <button type="button" class="btn-primary"><x-icon name="plus" class="size-4" stroke-width="2.4" /> Deal</button>
                </x-slot:trigger>
                <x-deal-form :stages="$stageOptions" :people="$people" :organizations="$organizations" />
            </x-modal>

            <div class="relative" x-data="{ open: false }">
                <button type="button" x-on:click="open = !open" class="btn-secondary">
                    <x-icon name="columns" class="size-4" /> {{ $pipeline->name }} <x-icon name="chevron-down" class="size-3.5" />
                </button>
                <div x-show="open" x-cloak x-on:click.outside="open = false"
                     class="absolute left-0 z-30 mt-2 w-56 overflow-hidden rounded-xl border border-line bg-white py-1 shadow-xl">
                    @foreach ($pipelines as $option)
                        <a href="{{ route('deals.index', ['pipeline' => $option->id]) }}"
                           class="block px-4 py-2 text-sm hover:bg-gray-50 {{ $option->is($pipeline) ? 'font-semibold text-indigo-brand' : 'text-ink-700' }}">
                            {{ $option->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="flex overflow-hidden rounded-lg border border-line">
                <span class="flex size-9 items-center justify-center bg-indigo-50 text-indigo-brand"><x-icon name="columns" class="size-[18px]" /></span>
                <a href="{{ route('deals.list', ['pipeline' => $pipeline->id]) }}"
                   class="flex size-9 items-center justify-center border-l border-line text-ink-500 hover:bg-gray-50">
                    <x-icon name="list" class="size-[18px]" />
                </a>
            </div>

            <form action="{{ route('deals.index') }}" method="GET" class="relative w-56">
                <input type="hidden" name="pipeline" value="{{ $pipeline->id }}">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search deals" class="field py-1.5 pl-9 text-sm">
            </form>

            <div class="ml-auto flex items-center gap-3 text-sm">
                <span class="text-ink-500">{{ $totals->sum('count') }} open deals</span>
                <span class="font-bold text-ink-900 tabular-nums">${{ number_format($pipelineValue) }}</span>
            </div>
        </div>

        <div class="min-h-0 flex-1 overflow-x-auto p-4 lg:p-5" x-data="pipelineBoard('{{ route('deals.move', ['deal' => '__ID__']) }}')">
            <div class="flex h-full min-w-max gap-3">
                @foreach ($stages as $stage)
                    @php $stageDeals = $dealsByStage[$stage->id] ?? collect(); @endphp

                    <section class="flex w-[290px] shrink-0 flex-col rounded-xl bg-white/70 ring-1 ring-line"
                             x-on:dragover.prevent="overStage = {{ $stage->id }}"
                             x-on:dragleave="overStage === {{ $stage->id }} && (overStage = null)"
                             x-on:drop="drop($event, {{ $stage->id }})"
                             x-bind:class="overStage === {{ $stage->id }} && 'ring-2 ring-indigo-brand/50'">
                        <header class="rounded-t-xl border-b border-line bg-white px-3.5 py-3">
                            <div class="flex items-center justify-between">
                                <p class="truncate text-[13px] font-bold tracking-wide text-ink-900 uppercase">{{ $stage->name }}</p>
                                <span class="chip bg-gray-100 text-ink-500">{{ $stage->probability }}%</span>
                            </div>
                            <p class="mt-1 text-xs text-ink-500">
                                {{ $totals[$stage->id]['count'] }} deals ·
                                <span class="font-semibold text-ink-700 tabular-nums">${{ number_format($totals[$stage->id]['value']) }}</span>
                            </p>
                            <div class="mt-2 h-1 overflow-hidden rounded-full bg-gray-100">
                                <div class="h-full rounded-full bg-indigo-brand"
                                     style="width: {{ $pipelineValue > 0 ? round($totals[$stage->id]['value'] / $pipelineValue * 100) : 0 }}%"></div>
                            </div>
                        </header>

                        <div id="stage-drop-{{ $stage->id }}" class="min-h-[120px] flex-1 space-y-2 overflow-y-auto p-2.5">
                            @foreach ($stageDeals as $deal)
                                <article id="deal-card-{{ $deal->id }}" data-stage="{{ $stage->id }}" draggable="true"
                                         x-on:dragstart="start($event, {{ $deal->id }})" x-on:dragend="end()"
                                         class="cursor-grab rounded-lg border border-line bg-white p-3 shadow-sm transition hover:border-indigo-brand/40 hover:shadow active:cursor-grabbing">
                                    <a href="{{ route('deals.show', $deal) }}" class="block">
                                        <p class="text-[14px] leading-snug font-semibold text-ink-900">{{ $deal->title }}</p>

                                        @if ($deal->organization)
                                            <p class="mt-1 truncate text-xs text-ink-500">{{ $deal->organization->name }}</p>
                                        @elseif ($deal->person)
                                            <p class="mt-1 truncate text-xs text-ink-500">{{ $deal->person->name }}</p>
                                        @endif

                                        <div class="mt-2.5 flex items-center justify-between">
                                            <span class="text-[15px] font-bold text-ink-900 tabular-nums">${{ number_format((float) $deal->value) }}</span>
                                            <div class="flex items-center gap-1.5">
                                                @if ($deal->open_activities_count > 0)
                                                    <span class="chip bg-indigo-50 text-indigo-brand">
                                                        <x-icon name="activities" class="size-3" /> {{ $deal->open_activities_count }}
                                                    </span>
                                                @else
                                                    <span class="chip bg-red-50 text-danger" title="No activity scheduled">
                                                        <x-icon name="flag" class="size-3" />
                                                    </span>
                                                @endif
                                                @if ($deal->person)
                                                    <x-avatar :name="$deal->person->name" size="xs" />
                                                @endif
                                            </div>
                                        </div>

                                        @if ($deal->expected_close_date)
                                            <p class="mt-2 text-[11px] text-ink-400">
                                                Expected close {{ $deal->expected_close_date->format('M j, Y') }}
                                            </p>
                                        @endif
                                    </a>
                                </article>
                            @endforeach

                            @if ($stageDeals->isEmpty())
                                <p class="rounded-lg border border-dashed border-line px-3 py-6 text-center text-xs text-ink-400">
                                    Drop deals here
                                </p>
                            @endif
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.app>
