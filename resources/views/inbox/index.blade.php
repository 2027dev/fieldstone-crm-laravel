<x-layouts.app title="Sales Inbox">
    <x-slot:subnav>
        <nav class="space-y-1">
            @foreach ([
                ['inbox', 'Inbox', 'inbox', $counts['unread']],
                ['starred', 'Starred', 'star', 0],
                ['sent', 'Sent', 'reply', 0],
                ['archived', 'Archived', 'archive', 0],
            ] as [$value, $label, $icon, $badge])
                <a href="{{ route('inbox.index', ['folder' => $value]) }}"
                   class="sub-nav {{ $folder === $value ? 'sub-nav-active' : '' }}">
                    <x-icon :name="$icon" class="size-[18px]" />
                    <span class="flex-1">{{ $label }}</span>
                    @if ($badge > 0)
                        <span class="rounded-full bg-indigo-brand px-1.5 text-[11px] font-bold text-white">{{ $badge }}</span>
                    @endif
                </a>
            @endforeach

            <p class="px-3 pt-5 pb-1.5 text-[11px] font-bold tracking-wider text-ink-400 uppercase">Templates</p>
            <a href="{{ route('leads.web-forms') }}" class="sub-nav"><x-icon name="note" class="size-[18px]" /> Web form replies</a>
        </nav>
    </x-slot:subnav>

    <div class="grid h-full grid-cols-1 lg:grid-cols-[360px_minmax(0,1fr)]">
        <div class="min-h-0 overflow-y-auto border-r border-line bg-white">
            <div class="sticky top-0 z-10 border-b border-line bg-white px-4 py-3">
                <form action="{{ route('inbox.index') }}" method="GET" class="relative">
                    <input type="hidden" name="folder" value="{{ $folder }}">
                    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-ink-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Search conversations" class="field py-1.5 pl-9 text-sm">
                </form>
            </div>

            <ul class="divide-y divide-line">
                @forelse ($threads as $item)
                    <li>
                        <a href="{{ route('inbox.show', $item) }}"
                           class="flex gap-3 px-4 py-3.5 transition-colors hover:bg-gray-50 {{ $thread?->is($item) ? 'bg-indigo-50/60' : '' }}">
                            <x-avatar :name="$item->person?->name ?? $item->subject" size="sm" />
                            <div class="min-w-0 flex-1">
                                <div class="flex items-baseline gap-2">
                                    <p class="min-w-0 flex-1 truncate text-sm {{ $item->is_read ? 'font-medium text-ink-700' : 'font-bold text-ink-900' }}">
                                        {{ $item->person?->name ?? 'Unknown sender' }}
                                    </p>
                                    <span class="shrink-0 text-[11px] text-ink-400">{{ $item->last_message_at?->diffForHumans(short: true) }}</span>
                                </div>
                                <p class="mt-0.5 truncate text-[13px] {{ $item->is_read ? 'text-ink-700' : 'font-semibold text-ink-900' }}">
                                    {{ $item->subject }}
                                </p>
                                <p class="mt-0.5 truncate text-xs text-ink-400">
                                    {{ Str::limit(strip_tags((string) $item->latestMessage?->body), 70) }}
                                </p>
                            </div>
                            @if ($item->is_starred)
                                <x-icon name="star" class="mt-1 size-4 shrink-0 fill-amber-400 text-amber-400" />
                            @endif
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-12 text-center text-sm text-ink-400">No conversations in this folder.</li>
                @endforelse
            </ul>
        </div>

        <div class="min-h-0 overflow-y-auto">
            @if (! $thread)
                <x-empty-state icon="inbox" title="Your sales conversations, in one place"
                               description="Pick a conversation on the left to read the thread, reply, and keep everything linked to the right deal.">
                    <a href="{{ route('leads.web-forms') }}" class="btn-secondary"><x-icon name="note" class="size-4" /> Set up web forms</a>
                </x-empty-state>
            @else
                <div class="border-b border-line bg-white px-5 py-4">
                    <div class="flex flex-wrap items-start gap-3">
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-ink-900">{{ $thread->subject }}</h2>
                            <p class="mt-1 flex flex-wrap items-center gap-2 text-sm text-ink-500">
                                @if ($thread->person)
                                    <a href="{{ route('people.show', $thread->person) }}" class="font-medium text-link hover:underline">
                                        {{ $thread->person->name }}
                                    </a>
                                    @if ($thread->person->organization)
                                        <span>· {{ $thread->person->organization->name }}</span>
                                    @endif
                                @endif
                                @if ($thread->deal)
                                    <a href="{{ route('deals.show', $thread->deal) }}" class="chip bg-indigo-50 text-indigo-brand">
                                        <x-icon name="deals" class="size-3" /> {{ Str::limit($thread->deal->title, 34) }}
                                    </a>
                                @endif
                            </p>
                        </div>

                        <form method="POST" action="{{ route('inbox.star', $thread) }}">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">
                                <x-icon name="star" class="size-4 {{ $thread->is_starred ? 'fill-amber-400 text-amber-400' : '' }}" />
                                {{ $thread->is_starred ? 'Starred' : 'Star' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('inbox.archive', $thread) }}">
                            @csrf
                            <button type="submit" class="btn-secondary btn-sm">
                                <x-icon name="archive" class="size-4" /> {{ $thread->folder === 'archived' ? 'Move to inbox' : 'Archive' }}
                            </button>
                        </form>
                    </div>
                </div>

                <div class="space-y-4 p-5">
                    @foreach ($thread->messages as $message)
                        <article class="card overflow-hidden {{ $message->direction === 'outgoing' ? 'ml-6 border-indigo-brand/20 bg-indigo-50/30' : 'mr-6' }}">
                            <header class="flex flex-wrap items-center gap-2 border-b border-line px-4 py-2.5">
                                <x-avatar :name="$message->from_name ?? $message->from_email" size="xs" />
                                <p class="text-sm font-semibold text-ink-900">{{ $message->from_name ?: $message->from_email }}</p>
                                <p class="text-xs text-ink-400">to {{ $message->to_email }}</p>
                                <p class="ml-auto text-xs text-ink-400">{{ $message->sent_at?->format('M j, Y · g:i A') }}</p>
                            </header>
                            <div class="px-4 py-3.5 text-sm leading-relaxed whitespace-pre-line text-ink-900">{{ $message->body }}</div>
                        </article>
                    @endforeach

                    <form method="POST" action="{{ route('inbox.reply', $thread) }}" class="card p-4">
                        @csrf
                        <p class="field-label">Reply</p>
                        <textarea name="body" rows="4" class="field" required placeholder="Write your reply…"></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="btn-primary"><x-icon name="reply" class="size-4" /> Send reply</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
