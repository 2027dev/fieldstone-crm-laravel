@props(['title', 'description' => null, 'icon' => 'circle'])

<div class="flex flex-col items-center justify-center px-6 py-20 text-center">
    <span class="mb-5 flex size-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-brand">
        <x-icon :name="$icon" class="size-7" />
    </span>
    <h3 class="text-[22px] font-bold text-ink-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-2 max-w-md text-[15px] text-ink-700">{{ $description }}</p>
    @endif
    @if (! $slot->isEmpty())
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">{{ $slot }}</div>
    @endif
</div>
