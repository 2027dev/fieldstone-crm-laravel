@props(['name', 'title', 'width' => 'max-w-2xl', 'open' => false])

<div x-data="{ open: @js((bool) $open) }"
     x-on:open-modal.window="if ($event.detail === '{{ $name }}') open = true"
     x-on:close-modal.window="if ($event.detail === '{{ $name }}') open = false"
     x-on:keydown.escape.window="open = false">
    @isset($trigger)
        <div x-on:click="open = true">{{ $trigger }}</div>
    @endisset

    <template x-teleport="body">
        <div x-show="open" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-navy-950/40" x-show="open" x-transition.opacity
                 x-on:click="open = false"></div>

            <div class="relative flex min-h-full items-start justify-center p-4 sm:p-8">
                <div class="w-full {{ $width }} overflow-hidden rounded-xl bg-white shadow-2xl"
                     x-show="open"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-center justify-between border-b border-line px-5 py-3.5">
                        <h2 class="text-base font-bold text-ink-900">{{ $title }}</h2>
                        <button type="button" class="rounded-md p-1 text-ink-500 hover:bg-gray-100" x-on:click="open = false">
                            <x-icon name="x" class="size-5" />
                            <span class="sr-only">Close</span>
                        </button>
                    </div>

                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>
