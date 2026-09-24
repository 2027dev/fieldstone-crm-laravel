@props(['rows' => []])

@php
    $max = collect($rows)->map(fn ($row) => $row['won'] + $row['open'])->max() ?: 1;
@endphp

<div>
    <div class="flex h-56 items-stretch gap-3">
        @foreach ($rows as $row)
            @php
                $wonHeight = round($row['won'] / $max * 100, 1);
                $openHeight = round($row['open'] / $max * 100, 1);
            @endphp
            <div class="flex min-w-0 flex-1 flex-col items-center gap-1.5"
                 title="{{ $row['label'] }} — won ${{ number_format($row['won']) }}, open ${{ number_format($row['open']) }}">
                <div class="flex w-full max-w-14 flex-1 flex-col justify-end overflow-hidden rounded-t-md">
                    <div class="w-full rounded-t-md bg-[#3fb26a]" style="height: {{ $wonHeight }}%"></div>
                    <div class="w-full bg-[#b7e3c7]" style="height: {{ $openHeight }}%"></div>
                </div>
                <span class="text-[11px] font-medium text-ink-500">{{ $row['label'] }}</span>
            </div>
        @endforeach
    </div>

    <div class="mt-4 flex items-center justify-center gap-5 text-xs text-ink-700">
        <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-[#3fb26a]"></span> Won revenue</span>
        <span class="flex items-center gap-1.5"><span class="size-2.5 rounded-sm bg-[#b7e3c7]"></span> Open pipeline created</span>
    </div>
</div>
