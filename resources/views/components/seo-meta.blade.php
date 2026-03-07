@props([
    'title' => 'SipaduHOK - PKBM House Of Knowledge',
    'description' => 'PKBM House Of Knowledge adalah lembaga pendidikan non-formal yang berkomitmen untuk memberikan layanan pendidikan berkualitas bagi semua kalangan, termasuk anak-anak berkebutuhan khusus.',
    'keywords' => 'PKBM, House Of Knowledge, Pendidikan Non Formal, Pendidikan Inklusi, Homeschooling, Sekolah Alternatif, PKBM Bekasi, Kejar Paket A, Kejar Paket B, Kejar Paket C',
    'image' => asset('img/hero-bg.jpg')
])

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="title" content="{{ $title }}">
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="PKBM House Of Knowledge">

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
<meta property="og:site_name" content="PKBM House Of Knowledge">

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
      "name": "PKBM House Of Knowledge",
      "alternateName": "SipaduHOK",
      "url": "{{ config('app.url') }}",
      "logo": {
        "@type": "ImageObject",
        "url": "{{ asset('img/logo.png') }}",
        "width": 200,
        "height": 200
      },
      "description": "{{ $description }}",
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "Jl. Ruko Reni Jaya No.22, RW.23, Pamulang Bar., Kec. Pamulang",
        "addressLocality": "Tangerang Selatan",
        "addressRegion": "Banten",
        "postalCode": "15417",
        "addressCountry": "ID"
      },
      "contactPoint": {
        "@type": "ContactPoint",
        "telephone": "+62-858-1125-8534",
        "contactType": "customer service"
      },
      "sameAs": []
    },
    {
      "@type": "EducationalOrganization",
      "@id": "{{ config('app.url') }}#educational-org",
      "name": "PKBM House Of Knowledge",
      "url": "{{ config('app.url') }}",
      "logo": "{{ asset('img/logo.png') }}"
    },
    {
      "@type": "WebSite",
      "@id": "{{ config('app.url') }}#website",
      "url": "{{ config('app.url') }}",
      "name": "PKBM House Of Knowledge",
      "description": "{{ $description }}"
    }
  ]
}
</script>
