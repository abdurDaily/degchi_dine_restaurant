@props(['src', 'alt' => '', 'width' => null, 'height' => null, 'class' => null, 'loading' => null, 'decoding' => null, 'fetchpriority' => null, 'onerror' => null, 'sizes' => null])

@php
    $component = new \App\View\Components\ResponsiveImage(
        src: $src,
        alt: $alt,
        width: $width,
        height: $height,
        class: $class,
        loading: $loading,
        decoding: $decoding,
        fetchpriority: $fetchpriority,
        onerror: $onerror,
        sizes: $sizes,
    );

    $hasVariants = $component->hasVariants();
    $originalUrl = str_starts_with($src, 'http') ? $src : asset($src);
@endphp

@if($hasVariants)
<picture>
    <source
        type="image/webp"
        srcset="{{ $component->getVariantUrl(400, 'webp') }} 400w,
                {{ $component->getVariantUrl(800, 'webp') }} 800w,
                {{ $component->getVariantUrl(1200, 'webp') }} 1200w"
        sizes="{{ $sizes ?: '100vw' }}" />
    <source
        type="image/jpeg"
        srcset="{{ $component->getVariantUrl(400, 'jpg') }} 400w,
                {{ $component->getVariantUrl(800, 'jpg') }} 800w,
                {{ $component->getVariantUrl(1200, 'jpg') }} 1200w"
        sizes="{{ $sizes ?: '100vw' }}" />
    <img
        src="{{ $originalUrl }}"
        alt="{!! $alt !!}"
        @if($width) width="{{ $width }}" @endif
        @if($height) height="{{ $height }}" @endif
        @if($class) class="{{ $class }}" @endif
        @if($loading) loading="{{ $loading }}" @endif
        @if($decoding) decoding="{{ $decoding }}" @endif
        @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
        @if($onerror) onerror="{!! $onerror !!}" @endif
    />
</picture>
@else
<img
    src="{{ $originalUrl }}"
    alt="{!! $alt !!}"
    @if($width) width="{{ $width }}" @endif
    @if($height) height="{{ $height }}" @endif
    @if($class) class="{{ $class }}" @endif
    @if($loading) loading="{{ $loading }}" @endif
    @if($decoding) decoding="{{ $decoding }}" @endif
    @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
    @if($onerror) onerror="{!! $onerror !!}" @endif
/>
@endif
