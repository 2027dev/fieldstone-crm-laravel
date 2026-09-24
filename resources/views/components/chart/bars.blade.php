@props(['rows' => [], 'color' => '#5b53d6', 'money' => false])

@php
    $max = collect($rows)->max('value') ?: 1;
@endphp

<div class="space-y-3">
    @forelse ($rows as $row)
        <div>
            <div class="flex items-baseline justify-between gap-3 text-sm">
                <span class="min-w-0 truncate font-medium text-ink-700">{{ $row['label'] }}</span>
                <span class="shrink-0 font-semibold text-ink-900 tabular-nums">
                    {{ $money ? '$' . number_format((float) $row['value']) : number_format((float) $row['value']) }}
                    @isset($row['count'])
                        <span class="ml-1 text-xs font-normal text-ink-400">({{ $row['count'] }})</span>
                    @endisset
                </span>
            </div>
            <div class="mt-1.5 h-2.5 overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full transition-all"
                     style="width: {{ max(2, round($row['value'] / $max * 100)) }}%; background-color: {{ $color }}"></div>
            </div>
        </div>
    @empty
        <p class="py-6 text-center text-sm text-ink-400">No data yet.</p>
    @endforelse
</div>
