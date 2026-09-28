@props(['settings' => null])

@php
    $socialSettings = $settings ?: cache()->remember('home_contact_settings', 1800, function () {
        return \App\Models\Setting::where('setting_group', 'contact_section')->get()->keyBy('key');
    });

    $socialDefinitions = [
        'contact_facebook_url' => [
            'label' => 'Facebook',
            'icon' => 'bi bi-facebook',
            'default' => 'https://www.facebook.com/DegchiDine',
        ],
        'contact_facebook_group_url' => [
            'label' => 'Facebook Group',
            'icon' => 'bi bi-people-fill',
            'default' => '',
        ],
        'contact_instagram_url' => [
            'label' => 'Instagram',
            'icon' => 'bi bi-instagram',
            'default' => '',
        ],
        'contact_twitter_url' => [
            'label' => 'Twitter / X',
            'icon' => 'bi bi-twitter-x',
            'default' => '',
        ],
        'contact_tripadvisor_url' => [
            'label' => 'TripAdvisor',
            'icon' => 'bi bi-star',
            'default' => '',
        ],
    ];

    $activeSocials = collect($socialDefinitions)
        ->map(fn ($definition, $key) => [
            'label' => $definition['label'],
            'icon' => $definition['icon'],
            'url' => trim(optional($socialSettings->get($key))->value ?? '') ?: $definition['default'],
        ])
        ->filter(fn ($social) => filled($social['url']))
        ->values();
@endphp

@foreach ($activeSocials as $social)
    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $social['label'] }}"
        title="{{ $social['label'] }}"><i class="{{ $social['icon'] }}"></i></a>
@endforeach
