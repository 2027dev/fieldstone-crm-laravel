@props(['slices' => []])

@php
    $total = collect($slices)->sum('value');
    $radius = 60;
    $circumference = 2 * M_PI * $radius;
    $offset = 0;
@endphp

<div class="flex flex-wrap items-center justify-center gap-8">
    <svg viewBox="0 0 160 160" class="size-44 -rotate-90" role="img" aria-label="Deal status split">
        @if ($total > 0)
            @foreach ($slices as $slice)
                @php
                    $fraction = $slice['value'] / $total;
                    $length = $fraction * $circumference;
                @endphp
                <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="{{ $slice['color'] }}" stroke-width="26"
                        stroke-dasharray="{{ $length }} {{ $circumference - $length }}"
                        stroke-dashoffset="{{ -$offset }}" />
                @php $offset += $length; @endphp
            @endforeach
        @else
            <circle cx="80" cy="80" r="{{ $radius }}" fill="none" stroke="#e3e4e9" stroke-width="26" />
        @endif
    </svg>

    <ul class="space-y-2.5">
        @foreach ($slices as $slice)
            <li class="flex items-center gap-2.5 text-sm">
                <span class="size-3 shrink-0 rounded-sm" style="background-color: {{ $slice['color'] }}"></span>
                <span class="w-14 text-ink-700">{{ $slice['label'] }}</span>
                <span class="font-semibold text-ink-900 tabular-nums">{{ $slice['value'] }}</span>
                <span class="text-xs text-ink-400">
                    {{ $total > 0 ? round($slice['value'] / $total * 100) : 0 }}%
                </span>
            </li>
        @endforeach
    </ul>
</div>
