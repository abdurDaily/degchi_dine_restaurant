@props([
    'title' => 'Page Title',
    'subtitle' => null,
    'eyebrow' => null,
    'eyebrowIcon' => null,
    'meta' => null,
    'backLink' => null,
    'backText' => 'Back to Home',
    'showBorder' => true,
    'variant' => 'default',
    'class' => null,
])

@php
    $heartSvg = '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>';

    $headerClass = match($variant) {
        'detail' => 'blog-detail-header',
        default => 'page-hero',
    };

    $showBorder = filter_var($showBorder, FILTER_VALIDATE_BOOLEAN);
@endphp

<div @class([$headerClass, 'header-border' => $showBorder, $class])>
    <div class="container">
        <div class="page-hero-inner">

            @if($backLink)
                <a href="{{ $backLink }}" class="page-hero-back-link">
                    <iconify-icon icon="solar:alt-arrow-left-linear"></iconify-icon>
                    <span>{{ $backText }}</span>
                </a>
            @endif

            <div class="page-hero-eyebrow">
                @if($eyebrowIcon)
                    {!! $eyebrowIcon !!}
                @else
                    {!! $heartSvg !!}
                @endif
                {{ $eyebrow }}
            </div>

            <h1>{{ $title }}</h1>

            @if($subtitle)
                <p>{{ $subtitle }}</p>
            @endif

            @if($meta)
                <div class="page-hero-meta">
                    {!! $meta !!}
                </div>
            @endif

            {{ $slot }}
        </div>
    </div>

    @if($showBorder)
        <span class="header-border-line"></span>
    @endif
</div>
