@props([
    'data' => null,
])

@php
    $embedUrl = $data['embed_url'] ?? '';
    $width = (int) $data['width'] ?? 16;
    $height = (int) $data['height'] ?? 9;
    $responsive = $data['responsive'] ?? true;
@endphp

@if ($data)
<div
    @class([
        'responsive' => $responsive
    ])
>
    <iframe
        src="{{ $embedUrl }}"
        width="{{ $responsive ? ($width * 10) : $width }}"
        height="{{ $responsive ? ($height * 10) : $height }}"
        style="aspect-ratio:{{ $width }}/{{ $height }}; width: 100%; height: auto;"
        {{ $attributes->except('data') }}
    ></iframe>
</div>
@endif
