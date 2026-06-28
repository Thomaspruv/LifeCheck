<!-- Meta tags standards -->
<meta name="description" content="{{ $description }}">
@if($noindex)
    <meta name="robots" content="noindex, nofollow">
@else
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
@endif
<link rel="canonical" href="{{ $canonical }}">

<!-- Open Graph -->
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:type" content="{{ $type }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="{{ str_replace('_', '-', app()->getLocale()) }}">

<!-- Twitter Cards -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">

<!-- JSON-LD Schema.org - WebApplication -->
<script type="application/ld+json">
{
    "@@context": "https://schema.org",
    "@@type": "WebApplication",
    "name": "{{ config('app.name') }}",
    "url": "{{ $canonical }}",
    "description": "{{ $description }}",
    "image": "{{ $image }}",
    "applicationCategory": "LifestyleApplication",
    "applicationSubCategory": "MoodTracker",
    "operatingSystem": "Web, iOS, Android",
    "browserRequirements": "Requires JavaScript",
    "inLanguage": ["fr", "en", "es"],
    "author": {
        "@@type": "Organization",
        "name": "{{ config('app.name') }}"
    },
    "offers": {
        "@@type": "Offer",
        "price": "0",
        "priceCurrency": "EUR",
        "availability": "https://schema.org/InStock"
    },
    "featureList": [
        "Suivi quotidien de l'humeur",
        "Journal intime et notes libres",
        "Objectifs et streaks",
        "Badges et récompenses",
        "Statistiques et tendances",
        "Exercices de respiration"
    ],
    "keywords": "bien-être, humeur, santé mentale, suivi émotionnel, journal intime, mindfulness",
    "isAccessibleForFree": true,
    "countriesNotSupported": ""
}
</script>
