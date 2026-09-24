<x-layouts.app title="Setup guide">
    <div class="relative overflow-hidden bg-gradient-to-br from-indigo-50 via-indigo-50/60 to-white">
        <div class="relative z-10 max-w-3xl px-6 py-10 lg:px-10">
            <h2 class="text-[30px] leading-tight font-bold text-ink-900">
                Hi, <span class="text-brand-600">{{ auth()->user()->first_name }}</span>! Let's get you set up
            </h2>
            <p class="mt-3 max-w-lg text-[15px] leading-relaxed text-ink-700">
                Connect {{ auth()->user()->company_name ?: 'your team' }} with Fieldstone CRM. Follow these milestones
                to customize your workspace.
            </p>

            <div class="mt-7 max-w-md">
                <div class="h-2 overflow-hidden rounded-full bg-white/80 ring-1 ring-indigo-brand/10">
                    <div class="h-full rounded-full bg-indigo-brand transition-all duration-500"
                         style="width: {{ $progress['percent'] }}%"></div>
                </div>
                <p class="mt-2.5 text-center text-[12px] font-bold tracking-wider text-ink-500 uppercase">
                    {{ $progress['completed'] }}/{{ $progress['total'] }} suggested tasks completed
                </p>
            </div>
        </div>

        <svg class="pointer-events-none absolute top-6 right-8 hidden h-48 lg:block" viewBox="0 0 320 190" fill="none" aria-hidden="true">
            <rect x="60" y="10" width="220" height="130" rx="10" fill="#c7c4f5" />
            <rect x="150" y="140" width="40" height="22" fill="#5b53d6" />
            <path d="M104 34l9.3 19.2 21 3-15.2 14.8 3.6 20.9L104 82l-18.7 9.9 3.6-20.9L73.7 56.2l21-3z" fill="#3fb26a" />
            <circle cx="182" cy="66" r="22" fill="#ffffff" />
            <rect x="218" y="44" width="44" height="44" fill="#ffffff" />
            <circle cx="272" cy="26" r="12" fill="#1e1b4c" />
            <path d="M272 38v26h-14" stroke="#1e1b4c" stroke-width="3" />
        </svg>
    </div>

    <div class="px-4 py-8 lg:px-10">
        <div class="mx-auto max-w-4xl space-y-6">
            <div class="card flex items-start gap-3 px-5 py-4">
                <span class="mt-0.5 flex size-5 shrink-0 items-center justify-center rounded-full bg-indigo-brand text-white">
                    <x-icon name="check" class="size-3" stroke-width="3" />
                </span>
                <div>
                    <p class="text-[15px] font-semibold text-ink-400 line-through">Set up account</p>
                    <p class="text-sm text-ink-400 line-through">
                        You've successfully joined Fieldstone CRM! Continue by customizing your account to your needs.
                    </p>
                </div>
            </div>

            <p class="text-[12px] font-bold tracking-wider text-ink-500 uppercase">Your to-do list</p>

            @foreach ($groups as $group)
                @php
                    $doneCount = collect($group['tasks'])->where('done', true)->count();
                    $totalCount = count($group['tasks']);
                @endphp

                <section class="card overflow-hidden" x-data="{ open: {{ $doneCount < $totalCount ? 'true' : 'false' }} }">
                    <button type="button" x-on:click="open = !open"
                            class="flex w-full items-center gap-3 px-5 py-4 text-left hover:bg-gray-50">
                        <span class="flex size-5 shrink-0 items-center justify-center rounded-full border-2 {{ $doneCount === $totalCount ? 'border-brand-600 bg-brand-600 text-white' : 'border-gray-300 text-transparent' }}">
                            <x-icon name="check" class="size-3" stroke-width="3" />
                        </span>
                        <span class="flex-1 text-[15px] font-bold text-ink-900">{{ $group['title'] }}</span>

                        <span class="hidden h-1.5 w-24 overflow-hidden rounded-full bg-gray-200 sm:block">
                            <span class="block h-full rounded-full bg-brand-600" style="width: {{ $totalCount ? round($doneCount / $totalCount * 100) : 0 }}%"></span>
                        </span>
                        <span class="text-sm text-ink-500">{{ $doneCount }} of {{ $totalCount }} tasks</span>
                        <x-icon name="chevron-up" class="size-4 text-ink-500 transition-transform" x-bind:class="!open && 'rotate-180'" />
                    </button>

                    <div x-show="open" x-collapse>
                        <ul class="divide-y divide-line border-t border-line">
                            @foreach ($group['tasks'] as $task)
                                <li class="flex items-start gap-4 px-5 py-4 {{ $task['done'] ? 'bg-gray-50/60' : '' }}">
                                    <span class="mt-0.5 flex size-8 shrink-0 items-center justify-center rounded-lg {{ $task['done'] ? 'bg-brand-50 text-brand-600' : 'bg-gray-100 text-ink-500' }}">
                                        <x-icon :name="$task['icon']" class="size-[18px]" />
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="text-[15px] font-bold {{ $task['done'] ? 'text-ink-400 line-through' : 'text-ink-900' }}">
                                                {{ $task['title'] }}
                                            </p>
                                            <span class="chip bg-gray-100 text-ink-500">
                                                <x-icon name="clock" class="size-3" /> {{ $task['duration'] }}
                                            </span>
                                        </div>
                                        <p class="mt-1 text-sm text-ink-700">{{ $task['description'] }}</p>

                                        <div class="mt-3 flex flex-wrap items-center gap-2">
                                            @if ($task['done'])
                                                <form method="POST" action="{{ route('setup.uncomplete', $task['key']) }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-secondary btn-sm">Mark as not done</button>
                                                </form>
                                            @else
                                                <a href="{{ $task['route'] }}" class="btn-primary btn-sm">{{ $task['cta'] }}</a>
                                                <form method="POST" action="{{ route('setup.complete', $task['key']) }}">
                                                    @csrf
                                                    <button type="submit" class="btn-ghost btn-sm">Skip</button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>

                                    <span class="hidden shrink-0 items-center gap-2 sm:flex">
                                        @if ($task['done'])
                                            <x-icon name="check-circle" class="size-5 text-brand-600" />
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </section>
            @endforeach

            <div class="flex items-center justify-center gap-2 pt-2 text-sm text-link">
                <x-icon name="chat" class="size-[18px]" />
                <a href="{{ route('inbox.index') }}" class="font-semibold hover:underline">Chat with us</a>
            </div>
        </div>
    </div>
</x-layouts.app>
