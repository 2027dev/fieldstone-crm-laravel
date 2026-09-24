@props(['name' => '', 'size' => 'md'])

@php
    $clean = preg_replace('/^\[Sample\]\s*/', '', (string) $name);
    $parts = preg_split('/\s+/', trim($clean)) ?: [''];
    $initials = strtoupper(mb_substr($parts[0] ?? '', 0, 1) . (count($parts) > 1 ? mb_substr(end($parts), 0, 1) : ''));

    $palette = ['#5b53d6', '#1c7c4a', '#c9761a', '#2f6bd8', '#9333ea', '#0f766e', '#be123c', '#4d7c0f'];
    $color = $palette[crc32($clean) % count($palette)];

    $sizes = [
        'xs' => 'size-6 text-[10px]',
        'sm' => 'size-8 text-[11px]',
        'md' => 'size-10 text-xs',
        'lg' => 'size-14 text-base',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex shrink-0 items-center justify-center rounded-full font-bold text-white ' . ($sizes[$size] ?? $sizes['md'])]) }}
      style="background-color: {{ $color }}" title="{{ $clean }}">
    {{ $initials ?: '?' }}
</span>
