@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'image' => null,
    'robots' => null,
    'type' => null,
    'canonical' => null,
])

@php
    $seo = app(\App\Support\SeoSettings::class);
    $metaTitle = $seo->title($title ?: null);
    $metaDescription = $seo->description($description ?: null);
    $metaKeywords = $seo->keywords($keywords ?: null);
    $metaRobots = $seo->robots($robots ?: null);
    $metaType = $seo->ogType($type ?: null);
    $metaImage = $seo->ogImage($image ?: null);
    $metaCanonical = $seo->canonical($canonical ?: null);
    $twitterHandle = $seo->twitterHandle();
    $gaId = $seo->googleAnalyticsId();
    $gtmId = $seo->googleTagManagerId();
    $pixelId = $seo->facebookPixelId();
    $headScripts = $seo->headScripts();
@endphp

<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
@if($metaKeywords)
<meta name="keywords" content="{{ $metaKeywords }}">
@endif
<meta name="robots" content="{{ $metaRobots }}">
<link rel="canonical" href="{{ $metaCanonical }}">

<meta property="og:site_name" content="{{ $seo->siteName() }}">
<meta property="og:title" content="{{ $metaTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $metaType }}">
<meta property="og:url" content="{{ $metaCanonical }}">
<meta property="og:image" content="{{ $metaImage }}">
<meta property="og:image:alt" content="{{ $seo->siteName() }}">

<meta name="twitter:card" content="{{ $seo->twitterCard() }}">
<meta name="twitter:title" content="{{ $metaTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $metaImage }}">
@if($twitterHandle)
<meta name="twitter:site" content="{{ '@' . $twitterHandle }}">
@endif

@if($gaId)
<script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ $gaId }}');
</script>
@endif

@if($gtmId)
<script>
    (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $gtmId }}');
</script>
@endif

@if($pixelId)
<script>
    !function(f,b,e,v,n,t,s)
    {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
    n.callMethod.apply(n,arguments):n.queue.push(arguments)};
    if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
    n.queue=[];t=b.createElement(e);t.async=!0;
    t.src=v;s=b.getElementsByTagName(e)[0];
    s.parentNode.insertBefore(t,s)}(window, document,'script',
    'https://connect.facebook.net/en_US/fbevents.js');
    fbq('init', '{{ $pixelId }}');
    fbq('track', 'PageView');
</script>
<noscript>
    <img height="1" width="1" style="display:none" alt=""
        src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" />
</noscript>
@endif

@if($headScripts)
{!! $headScripts !!}
@endif

@once
@if(request()->routeIs('frontend.home'))
@php
    $contactSettings = cache()->remember('home_contact_settings', 1800, function () {
        return \App\Models\Setting::where('setting_group', 'contact_section')
            ->get()
            ->keyBy('key');
    });
    $contactPhone = optional($contactSettings->get('contact_phone'))->value ?? '01898-795400';
    $contactAddress = optional($contactSettings->get('contact_address'))->value ?? 'Boropool Circle, Kaptan Villa, Halishahar, Chittagong';
@endphp
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Restaurant",
    "name": @json($seo->siteName()),
    "url": @json(url('/')),
    "image": @json($metaImage),
    "description": @json($metaDescription),
    "telephone": @json($contactPhone),
    "servesCuisine": ["Bangladeshi", "Biryani", "Kacchi", "Mezban"],
    "priceRange": "$$",
    "address": {
        "@type": "PostalAddress",
        "streetAddress": @json($contactAddress),
        "addressLocality": "Chittagong",
        "addressCountry": "BD"
    },
    "geo": {
        "@type": "GeoCoordinates",
        "latitude": 22.3569,
        "longitude": 91.7832
    },
    "openingHoursSpecification": {
        "@type": "OpeningHoursSpecification",
        "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"],
        "opens": "17:00",
        "closes": "23:30"
    },
    "sameAs": [
        "https://www.facebook.com/DegchiDine"
    ]
}
</script>
@endif
@endonce
