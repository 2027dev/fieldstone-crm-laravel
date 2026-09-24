@props(['activities', 'title' => 'Activities'])

<div class="card">
    <div class="border-b border-line px-5 py-3">
        <p class="text-[12px] font-semibold tracking-wide text-ink-500 uppercase">{{ $title }}</p>
    </div>

    <ul class="divide-y divide-line">
        @forelse ($activities as $activity)
            <li class="flex items-start gap-3 px-5 py-3">
                <form method="POST" action="{{ route('activities.toggle', $activity) }}" class="mt-0.5">
                    @csrf
                    <button type="submit" class="{{ $activity->done ? 'text-brand-600' : 'text-ink-400 hover:text-brand-600' }}"
                            aria-label="Toggle activity">
                        <x-icon :name="$activity->done ? 'check-circle' : 'circle'" class="size-5" />
                    </button>
                </form>

                <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-md bg-gray-100 text-ink-500">
                    <x-icon :name="$activity->icon" class="size-3.5" />
                </span>

                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium {{ $activity->done ? 'text-ink-400 line-through' : 'text-ink-900' }}">
                        {{ $activity->subject }}
                    </p>
                    <p class="mt-0.5 text-xs {{ $activity->is_overdue ? 'font-semibold text-danger' : 'text-ink-500' }}">
                        {{ $activity->type_label }}
                        @if ($activity->due_date)
                            · {{ $activity->due_date->format('M j, Y') }}
                            @if ($activity->due_time) at {{ \Illuminate\Support\Carbon::parse($activity->due_time)->format('g:i A') }} @endif
                            @if ($activity->is_overdue) · Overdue @endif
                        @endif
                    </p>
                </div>
            </li>
        @empty
            <li class="px-5 py-6 text-sm text-ink-400">No activities scheduled.</li>
        @endforelse
    </ul>
</div>
