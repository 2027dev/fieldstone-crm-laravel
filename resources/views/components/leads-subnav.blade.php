@props(['counts' => []])

<nav class="space-y-1">
    <a href="{{ route('leads.index') }}" class="sub-nav {{ request()->routeIs('leads.index') && request('view', 'inbox') === 'inbox' ? 'sub-nav-active' : '' }}">
        <x-icon name="inbox" class="size-[18px]" />
        <span class="flex-1">Leads Inbox</span>
        @if (($counts['inbox'] ?? 0) > 0)
            <span class="text-xs font-semibold text-ink-400">{{ $counts['inbox'] }}</span>
        @endif
    </a>
    <a href="{{ route('leads.index', ['view' => 'archived']) }}" class="sub-nav {{ request('view') === 'archived' ? 'sub-nav-active' : '' }}">
        <x-icon name="archive" class="size-[18px]" />
        <span class="flex-1">Archived</span>
    </a>
    <a href="{{ route('leads.index', ['view' => 'converted']) }}" class="sub-nav {{ request('view') === 'converted' ? 'sub-nav-active' : '' }}">
        <x-icon name="convert" class="size-[18px]" />
        <span class="flex-1">Converted</span>
    </a>

    <p class="px-3 pt-5 pb-1.5 text-[11px] font-bold tracking-wider text-ink-400 uppercase">LeadBooster</p>
    <a href="{{ route('leads.web-forms') }}" class="sub-nav {{ request()->routeIs('leads.web-forms') ? 'sub-nav-active' : '' }}">
        <x-icon name="note" class="size-[18px]" /> Web Forms
    </a>
    @foreach ([['Live Chat', 'chat'], ['Chatbot', 'sparkles'], ['Prospector', 'target']] as [$label, $icon])
        <span class="sub-nav cursor-default text-ink-400 hover:bg-transparent">
            <x-icon :name="$icon" class="size-[18px]" />
            <span class="flex-1">{{ $label }}</span>
            <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-bold text-ink-400">SOON</span>
        </span>
    @endforeach

    <p class="px-3 pt-5 pb-1.5 text-[11px] font-bold tracking-wider text-ink-400 uppercase">Add-ons</p>
    <span class="sub-nav cursor-default text-ink-400 hover:bg-transparent">
        <x-icon name="globe" class="size-[18px]" />
        <span class="flex-1">Web Visitors</span>
        <span class="rounded bg-gray-100 px-1.5 py-0.5 text-[10px] font-bold text-ink-400">SOON</span>
    </span>
</nav>
