@props([
    'title',
    'subtitle' => null,
    'kicker' => null,
    'icon' => 'fa-solid fa-utensils',
    'showDivider' => true,
    'align' => 'center',
    'class' => null,
])

<div @class(['section-heading-block', 'mb-5', 'text-center', 'reveal', $class]) style="{{ $align === 'start' ? 'text-align: left' : '' }}">
    @if($kicker)
        <span class="shb-kicker">{{ $kicker }}</span>
    @endif

    <h2 class="shb-title {{ $align === 'center' ? 'mt-3 mb-2' : 'mt-2 mb-2' }}">
        {{ $title }}
    </h2>

    @if($showDivider)
        @if($icon === 'line')
            <div class="shb-divider mx-auto"></div>
        @else
            <div class="shb-divider-wrap mx-auto">
                <span></span>
                <i class="{{ $icon }}"></i>
                <span></span>
            </div>
        @endif
    @endif

    @if($subtitle)
        <p class="shb-subtitle {{ $align === 'center' ? 'mx-auto' : '' }}">
            {{ $subtitle }}
        </p>
    @endif

    {{ $slot }}
</div>
