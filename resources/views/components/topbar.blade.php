@props(['title' => '', 'breadcrumb' => null])

<header class="flex h-[60px] shrink-0 items-center gap-4 border-b border-line bg-white px-4 lg:px-5">
    <div class="flex min-w-0 max-w-[26rem] items-center gap-2">
        <h1 class="truncate text-[17px] font-bold text-ink-900">
            @if ($breadcrumb)
                <span class="font-semibold text-ink-500">{{ $breadcrumb }}</span>
                <span class="mx-1 text-ink-400">/</span>
            @endif
            {{ $title }}
        </h1>
    </div>

    <form action="{{ route('search') }}" method="GET" class="mx-auto hidden w-full max-w-md md:block">
        <div class="relative">
            <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-[18px] -translate-y-1/2 text-ink-400" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search Fieldstone"
                   class="field rounded-full py-2 pl-10 text-sm" autocomplete="off">
        </div>
    </form>

    <div class="ml-auto flex items-center gap-2">
        <button type="button" x-on:click="$dispatch('open-modal', 'quick-create')"
                class="flex size-9 items-center justify-center rounded-full border border-line text-ink-700 hover:bg-gray-50"
                title="Create">
            <x-icon name="plus" class="size-5" />
        </button>

        <a href="{{ route('insights.index') }}"
           class="hidden size-9 items-center justify-center rounded-full bg-indigo-brand text-white hover:bg-indigo-soft sm:flex"
           title="Insights">
            <x-icon name="sparkles" class="size-[18px]" />
        </a>

        <a href="{{ route('activities.index', ['type' => 'call', 'period' => 'todo']) }}"
           class="hidden shrink-0 items-center gap-2 rounded-full bg-ink-500 px-3.5 py-2 text-[13px] font-semibold whitespace-nowrap text-white hover:bg-ink-700 lg:inline-flex">
            <x-icon name="phone" class="size-4" /> Calls to make
        </a>

        <a href="{{ route('deals.index') }}" class="hidden size-9 items-center justify-center rounded-full text-ink-500 hover:bg-gray-100 xl:flex" title="Deals">
            <x-icon name="shop" class="size-5" />
        </a>
        <a href="{{ route('setup.index') }}" class="hidden size-9 items-center justify-center rounded-full text-ink-500 hover:bg-gray-100 xl:flex" title="Setup guide">
            <x-icon name="question" class="size-5" />
        </a>
        <a href="{{ route('leads.index') }}" class="hidden size-9 items-center justify-center rounded-full text-ink-500 hover:bg-gray-100 xl:flex" title="Leads">
            <x-icon name="bulb" class="size-5" />
        </a>

        <div class="relative" x-data="{ open: false }">
            <button type="button" x-on:click="open = !open" class="flex items-center">
                <x-avatar :name="auth()->user()->name" size="sm" />
            </button>

            <div x-show="open" x-cloak x-transition x-on:click.outside="open = false"
                 class="absolute right-0 z-40 mt-2 w-60 overflow-hidden rounded-xl border border-line bg-white shadow-xl">
                <div class="border-b border-line px-4 py-3">
                    <p class="truncate text-sm font-bold text-ink-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-xs text-ink-500">{{ auth()->user()->email }}</p>
                    @if (auth()->user()->company_name)
                        <p class="mt-1 truncate text-xs text-ink-400">{{ auth()->user()->company_name }}</p>
                    @endif
                </div>
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink-700 hover:bg-gray-50">
                    <x-icon name="user" class="size-4" /> Profile & settings
                </a>
                <a href="{{ route('setup.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-sm text-ink-700 hover:bg-gray-50">
                    <x-icon name="setup" class="size-4" /> Setup guide
                </a>
                <form method="POST" action="{{ route('logout') }}" class="border-t border-line">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-2.5 px-4 py-2.5 text-left text-sm text-ink-700 hover:bg-gray-50">
                        <x-icon name="logout" class="size-4" /> Log out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
