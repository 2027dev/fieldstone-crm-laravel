<nav class="space-y-1">
    <a href="{{ route('people.index') }}" class="sub-nav {{ request()->routeIs('people.*') ? 'sub-nav-active' : '' }}">
        <x-icon name="user" class="size-[18px]" /> People
    </a>
    <a href="{{ route('organizations.index') }}" class="sub-nav {{ request()->routeIs('organizations.*') ? 'sub-nav-active' : '' }}">
        <x-icon name="building" class="size-[18px]" /> Organizations
    </a>
    <a href="{{ route('contacts.timeline') }}" class="sub-nav {{ request()->routeIs('contacts.timeline') ? 'sub-nav-active' : '' }}">
        <x-icon name="clock" class="size-[18px]" /> Contacts timeline
    </a>
    <a href="{{ route('contacts.duplicates') }}" class="sub-nav {{ request()->routeIs('contacts.duplicates') ? 'sub-nav-active' : '' }}">
        <x-icon name="convert" class="size-[18px]" /> Merge duplicates
    </a>
</nav>
