<x-layouts.app title="Search">
    <div class="p-4 lg:p-5">
        <div class="card p-5">
            <form action="{{ route('search') }}" method="GET" class="relative">
                <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 size-5 -translate-y-1/2 text-ink-400" />
                <input type="search" name="q" value="{{ $term }}" autofocus placeholder="Search people, organizations, deals, leads and activities"
                       class="field py-3 pl-11 text-[15px]">
            </form>

            @if ($term !== '')
                <p class="mt-3 text-sm text-ink-500">
                    {{ $total }} {{ Str::plural('result', $total) }} for <span class="font-semibold text-ink-900">"{{ $term }}"</span>
                </p>
            @endif
        </div>

        @if ($term === '')
            <x-empty-state icon="search" title="Search your whole workspace"
                           description="Find a contact, an organization, a deal, a lead or an activity in one place." />
        @elseif ($total === 0)
            <x-empty-state icon="search" title="No matches"
                           description="Try a different name, email address or deal title." />
        @else
            <div class="mt-4 grid gap-4 lg:grid-cols-2">
                @if ($results['people']->isNotEmpty())
                    <section class="card">
                        <div class="border-b border-line px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">People</p></div>
                        <ul class="divide-y divide-line">
                            @foreach ($results['people'] as $person)
                                <li>
                                    <a href="{{ route('people.show', $person) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                                        <x-avatar :name="$person->name" size="xs" />
                                        <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $person->name }}</span>
                                        <span class="shrink-0 text-xs text-ink-500">{{ $person->organization?->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($results['organizations']->isNotEmpty())
                    <section class="card">
                        <div class="border-b border-line px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Organizations</p></div>
                        <ul class="divide-y divide-line">
                            @foreach ($results['organizations'] as $organization)
                                <li>
                                    <a href="{{ route('organizations.show', $organization) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                                        <x-icon name="building" class="size-4 text-ink-400" />
                                        <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $organization->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($results['deals']->isNotEmpty())
                    <section class="card">
                        <div class="border-b border-line px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Deals</p></div>
                        <ul class="divide-y divide-line">
                            @foreach ($results['deals'] as $deal)
                                <li>
                                    <a href="{{ route('deals.show', $deal) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                                        <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $deal->title }}</span>
                                        <span class="shrink-0 text-xs text-ink-500">{{ $deal->stage->name }}</span>
                                        <span class="shrink-0 text-sm font-semibold tabular-nums">${{ number_format((float) $deal->value) }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($results['leads']->isNotEmpty())
                    <section class="card">
                        <div class="border-b border-line px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Leads</p></div>
                        <ul class="divide-y divide-line">
                            @foreach ($results['leads'] as $lead)
                                <li>
                                    <a href="{{ route('leads.show', $lead) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-gray-50">
                                        <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink-900">{{ $lead->title }}</span>
                                        <span class="shrink-0 text-xs text-ink-500">{{ $lead->person?->name }}</span>
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                @if ($results['activities']->isNotEmpty())
                    <section class="card">
                        <div class="border-b border-line px-5 py-3"><p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">Activities</p></div>
                        <ul class="divide-y divide-line">
                            @foreach ($results['activities'] as $activity)
                                <li class="flex items-center gap-3 px-5 py-3">
                                    <x-icon :name="$activity->icon" class="size-4 text-ink-400" />
                                    <span class="min-w-0 flex-1 truncate text-sm font-medium text-ink-900">{{ $activity->subject }}</span>
                                    <span class="shrink-0 text-xs text-ink-500">{{ $activity->due_date?->format('M j') }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </section>
                @endif
            </div>
        @endif
    </div>
</x-layouts.app>
