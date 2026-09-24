@php
    $remaining = \App\Support\SetupGuide::remaining(auth()->user());

    $links = [
        ['route' => 'setup.index', 'active' => 'setup*', 'label' => 'Setup guide', 'icon' => 'setup', 'badge' => $remaining ?: null],
        ['route' => 'people.index', 'active' => ['people*', 'organizations*', 'contacts*'], 'label' => 'Contacts', 'icon' => 'contacts'],
        ['route' => 'activities.index', 'active' => 'activities*', 'label' => 'Activities', 'icon' => 'activities'],
        ['route' => 'deals.index', 'active' => 'deals*', 'label' => 'Deals', 'icon' => 'deals'],
        ['route' => 'leads.index', 'active' => 'leads*', 'label' => 'Leads', 'icon' => 'leads'],
        ['route' => 'insights.index', 'active' => 'insights*', 'label' => 'Insights', 'icon' => 'insights'],
        ['route' => 'inbox.index', 'active' => 'inbox*', 'label' => 'Sales Inbox', 'icon' => 'inbox'],
    ];
@endphp

<aside class="hidden w-[232px] shrink-0 flex-col bg-navy-900 md:flex">
    <div class="px-6 pt-6 pb-5">
        <a href="{{ route('setup.index') }}" class="flex items-center gap-2.5">
            <span class="flex size-8 items-center justify-center rounded-lg bg-brand-600 text-sm font-black text-white">F</span>
            <span class="text-[19px] leading-none font-extrabold tracking-tight text-white">Fieldstone</span>
        </a>
    </div>

    <nav class="flex-1 space-y-0.5 px-3">
        @foreach ($links as $link)
            @php $isActive = request()->routeIs($link['active']); @endphp
            <a href="{{ route($link['route']) }}" class="nav-pill {{ $isActive ? 'nav-pill-active' : '' }}">
                <x-icon :name="$link['icon']" class="size-[18px] shrink-0" />
                <span class="flex-1">{{ $link['label'] }}</span>
                @if (! empty($link['badge']))
                    <span class="flex size-5 items-center justify-center rounded-full bg-[#2f7cf6] text-[11px] font-bold text-white">
                        {{ $link['badge'] }}
                    </span>
                @endif
            </a>
        @endforeach

        <div x-data="{ open: false }" class="relative">
            <button type="button" x-on:click="open = !open" class="nav-pill w-full">
                <x-icon name="more" class="size-[18px] shrink-0" />
                <span class="flex-1 text-left">More</span>
                <x-icon name="chevron-down" class="size-4 transition-transform" x-bind:class="open && 'rotate-180'" />
            </button>

            <div x-show="open" x-cloak x-transition x-on:click.outside="open = false" class="mt-1 space-y-0.5 pl-2">
                <a href="{{ route('contacts.timeline') }}" class="nav-pill text-[14px]">
                    <x-icon name="clock" class="size-[17px]" /> Contacts timeline
                </a>
                <a href="{{ route('activities.calendar') }}" class="nav-pill text-[14px]">
                    <x-icon name="activities" class="size-[17px]" /> Calendar
                </a>
                <a href="{{ route('deals.list') }}" class="nav-pill text-[14px]">
                    <x-icon name="list" class="size-[17px]" /> Deal list
                </a>
                <a href="{{ route('profile.edit') }}" class="nav-pill text-[14px]">
                    <x-icon name="user" class="size-[17px]" /> Profile
                </a>
            </div>
        </div>
    </nav>

    <div class="p-3">
        <div class="rounded-xl bg-navy-800 p-3.5">
            <p class="text-[13px] font-semibold text-white">Free trial</p>
            <p class="mt-1 text-[12px] leading-snug text-white/60">Unlimited contacts, deals and activities while you evaluate.</p>
            <div class="mt-2.5 h-1.5 overflow-hidden rounded-full bg-white/15">
                <div class="h-full rounded-full bg-indigo-soft" style="width: {{ \App\Support\SetupGuide::progress(auth()->user())['percent'] }}%"></div>
            </div>
        </div>
    </div>
</aside>
