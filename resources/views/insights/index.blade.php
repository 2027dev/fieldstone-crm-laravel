<x-layouts.app title="Insights">
    <x-slot:subnav>
        <nav class="space-y-1">
            <p class="flex items-center gap-2 px-3 pt-1 pb-2 text-[12px] font-bold tracking-wider text-ink-500 uppercase">
                <x-icon name="grid" class="size-4" /> Dashboards
            </p>
            <span class="sub-nav sub-nav-active">Sales performance</span>
            <p class="px-3 pt-5 pb-1.5 text-[12px] font-bold tracking-wider text-ink-500 uppercase">Reports</p>
            <a href="{{ route('deals.list') }}" class="sub-nav">Deal list</a>
            <a href="{{ route('activities.index', ['period' => 'all']) }}" class="sub-nav">Activity log</a>
            <a href="{{ route('leads.index') }}" class="sub-nav">Lead sources</a>
            <a href="{{ route('contacts.timeline') }}" class="sub-nav">Contact engagement</a>
        </nav>
    </x-slot:subnav>

    <div class="p-4 lg:p-5">
        <div class="flex flex-wrap items-center gap-3">
            <h2 class="text-[17px] font-bold text-ink-900">Sales performance</h2>
            <span class="chip bg-indigo-50 text-indigo-brand">{{ $pipeline?->name ?? 'All pipelines' }}</span>

            <div class="ml-auto flex items-center gap-1">
                @foreach ([3, 6, 12] as $option)
                    <a href="{{ route('insights.index', ['months' => $option]) }}"
                       class="rounded-md px-2.5 py-1.5 text-sm font-medium {{ $months === $option ? 'bg-indigo-50 font-semibold text-indigo-brand' : 'text-ink-500 hover:bg-gray-50' }}">
                        {{ $option }} months
                    </a>
                @endforeach
            </div>
        </div>

        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
            @php
                $cards = [
                    ['label' => 'Open pipeline', 'value' => '$' . number_format($scorecards['open_value']), 'sub' => $scorecards['open_count'] . ' open deals', 'icon' => 'deals', 'tone' => 'text-indigo-brand bg-indigo-50'],
                    ['label' => 'Won revenue', 'value' => '$' . number_format($scorecards['won_value']), 'sub' => $scorecards['won_count'] . ' deals won', 'icon' => 'check-circle', 'tone' => 'text-brand-600 bg-brand-50'],
                    ['label' => 'Win rate', 'value' => $scorecards['win_rate'] . '%', 'sub' => 'Avg deal $' . number_format($scorecards['avg_deal']), 'icon' => 'insights', 'tone' => 'text-warn bg-amber-50'],
                    ['label' => 'Activities to do', 'value' => (string) $scorecards['activities_due'], 'sub' => $scorecards['overdue'] . ' overdue', 'icon' => 'activities', 'tone' => 'text-danger bg-red-50'],
                ];
            @endphp

            @foreach ($cards as $card)
                <div class="card p-4">
                    <div class="flex items-start justify-between">
                        <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">{{ $card['label'] }}</p>
                        <span class="flex size-8 items-center justify-center rounded-lg {{ $card['tone'] }}">
                            <x-icon :name="$card['icon']" class="size-4" />
                        </span>
                    </div>
                    <p class="mt-2.5 text-[26px] leading-none font-bold text-ink-900 tabular-nums">{{ $card['value'] }}</p>
                    <p class="mt-1.5 text-xs text-ink-500">{{ $card['sub'] }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-4 grid gap-4 lg:grid-cols-2">
            <section class="card p-5">
                <div class="flex items-center justify-between">
                    <h3 class="text-[15px] font-bold text-ink-900">Open pipeline by stage</h3>
                    <a href="{{ route('deals.index') }}" class="text-xs font-semibold text-link hover:underline">View board</a>
                </div>
                <div class="mt-4">
                    <x-chart.bars :rows="$stageFunnel" money color="#5b53d6" />
                </div>
            </section>

            <section class="card p-5">
                <h3 class="text-[15px] font-bold text-ink-900">Deal outcomes</h3>
                <div class="mt-6">
                    <x-chart.donut :slices="$statusSplit" />
                </div>
            </section>

            <section class="card p-5 lg:col-span-2">
                <div class="flex items-center justify-between">
                    <h3 class="text-[15px] font-bold text-ink-900">Revenue over the last {{ $months }} months</h3>
                    <a href="{{ route('deals.list', ['status' => 'won']) }}" class="text-xs font-semibold text-link hover:underline">View won deals</a>
                </div>
                <div class="mt-5">
                    <x-chart.columns :rows="$monthlyPerformance" />
                </div>
            </section>

            <section class="card p-5">
                <h3 class="text-[15px] font-bold text-ink-900">Top accounts by value</h3>
                <div class="mt-4">
                    <x-chart.bars :rows="$topOrganizations" money color="#2f9e5e" />
                </div>
            </section>

            <section class="card p-5">
                <h3 class="text-[15px] font-bold text-ink-900">Activity mix</h3>
                <div class="mt-4">
                    <x-chart.bars :rows="$activityMix" color="#c9761a" />
                </div>
            </section>
        </div>

        <div class="mt-6 rounded-xl bg-gradient-to-b from-indigo-50/70 to-transparent px-6 py-10 text-center">
            <h3 class="text-2xl font-bold text-ink-900">Identify growth opportunities. Take action.</h3>
            <p class="mx-auto mt-2 max-w-2xl text-[15px] text-ink-700">
                Your reporting dashboard tracks every deal, activity and contact in Fieldstone CRM so you can make
                informed decisions at the right time.
            </p>
            <div class="mt-5 flex flex-wrap items-center justify-center gap-3">
                <a href="{{ route('deals.index') }}" class="btn-primary">Open pipeline</a>
                <a href="{{ route('deals.list', ['status' => 'all']) }}" class="btn-secondary">
                    <x-icon name="insights" class="size-4" /> Browse all deals
                </a>
                <a href="{{ route('activities.index', ['period' => 'overdue']) }}" class="btn-secondary">
                    <x-icon name="clock" class="size-4" /> Overdue activities
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
