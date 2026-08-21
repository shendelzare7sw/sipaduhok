@props([
    'title' => 'HOK Homeschooling - LMS & Pembayaran',
    'description' => 'Portal HOK Homeschooling untuk pembelajaran daring dan pembayaran tagihan orang tua.',
    'keywords' => 'HOK Homeschooling, LMS homeschooling, pembayaran tagihan homeschooling',
    'image' => asset('img/hero-bg.jpg')
])

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="HOK Homeschooling">

<!-- Favicons -->
<link rel="icon" type="image/png" href="{{ asset('img/logo/logo.png') }}">
<link rel="apple-touch-icon" href="{{ asset('img/logo/logo.png') }}">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url()->current() }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $image }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:alt" content="{{ $title }}">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="HOK Homeschooling">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:url" content="{{ url()->current() }}">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $image }}">
<meta name="twitter:creator" content="@sipaduHOK">
<meta name="twitter:site" content="@sipaduHOK">

<!-- Canonical Link -->
<link rel="canonical" href="{{ url()->current() }}">

<!-- Schema.org Markup -->
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "{{ config('app.url') }}#organization",
      "name": "HOK Homeschooling",
      "alternateName": "SipaduHOK",
      "url": "{{ config('app.url') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ asset('img/logo/logo.png') }}",
        "width": 200,
        "height": 200
      },
      "description": "{{ $description }}",
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+62-858-1125-8534",
        "contactType": "customer service"
      },
      "sameAs": []
    },
    {
      "@type": "WebSite",
      "@id": "{{ config('app.url') }}#website",
      "url": "{{ config('app.url') }}",
      "name": "HOK Homeschooling",
      "description": "{{ $description }}"
    }
  ]
}
</script>
