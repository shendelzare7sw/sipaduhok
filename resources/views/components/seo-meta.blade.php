@props([
    'title' => 'SipaduHOK - PKBM House Of Knowledge',
    'description' => 'PKBM House Of Knowledge adalah lembaga pendidikan non-formal yang berkomitmen untuk memberikan layanan pendidikan berkualitas bagi semua kalangan, termasuk anak-anak berkebutuhan khusus.',
    'keywords' => 'PKBM House Of Knowledge, SipaduHOK, PKBM Tangerang Selatan, PKBM Pamulang, Pendidikan Non Formal, Pendidikan Inklusi, Homeschooling, Sekolah Alternatif, Paket A, Paket B, Paket C, PAUD',
    'image' => null,
    'robots' => 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
])

@php
    // Satu sumber data lembaga untuk SEO: kontak & sosial media diambil dari CMS footer (Admin > Landing Pages).
    $footerSeo = \Illuminate\Support\Facades\Cache::remember('seo_footer_cms', 600, function () {
        $page = \App\Models\LandingPage::where('slug', 'footer')->with('sections')->first();

        return [
            'about' => $page?->getSection('about')?->content ?? [],
            'kontak' => $page?->getSection('contact_info')?->content ?? [],
        ];
    });
    $sosial = collect(['facebook_url', 'instagram_url', 'youtube_url', 'tiktok_url'])
        ->map(fn ($k) => trim((string) ($footerSeo['about'][$k] ?? '')))
        ->filter(fn ($u) => str_starts_with($u, 'http'))
        ->values()->all();

    $situs = rtrim(url('/'), '/'); // domain yang sedang melayani (sama dengan canonical)
    $urlHalaman = url()->current();
    $beranda = request()->is('/');
    $gambar = $image ?: asset('img/hero-bg.jpg');
    $gambarBawaan = ! $image;
    $namaHalaman = trim(\Illuminate\Support\Str::before($title, ' - ')) ?: $title;

    $organisasi = [
        '@type' => 'EducationalOrganization',
        '@id' => $situs . '/#organization',
        'name' => 'PKBM House Of Knowledge',
        'alternateName' => ['SipaduHOK', 'HOK Homeschooling', 'House Of Knowledge'],
        'url' => $situs . '/',
        'logo' => ['@type' => 'ImageObject', 'url' => asset('img/icons/icon-512.png'), 'width' => 512, 'height' => 512],
        'image' => asset('img/hero-bg.jpg'),
        'description' => 'PKBM House Of Knowledge adalah lembaga pendidikan non-formal di Tangerang Selatan yang menyediakan program PAUD, Paket A, B, C (setara SD, SMP, SMA), pendidikan inklusi, homeschooling, dan terapi.',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Komplek Ruko Reni Jaya Baru, Jl. Ketapang III Blok AF 5 No. 22-23, Pamulang Barat',
            'addressLocality' => 'Tangerang Selatan',
            'addressRegion' => 'Banten',
            'postalCode' => '15417',
            'addressCountry' => 'ID',
        ],
        'telephone' => $footerSeo['kontak']['phone'] ?? '+62 858-1125-8534',
        'email' => $footerSeo['kontak']['email'] ?? 'hokhomeschool@gmail.com',
        'areaServed' => 'Tangerang Selatan',
    ];
    if ($sosial) {
        $organisasi['sameAs'] = $sosial;
    }

    $graf = [
        $organisasi,
        [
            '@type' => 'WebSite',
            '@id' => $situs . '/#website',
            'url' => $situs . '/',
            'name' => 'PKBM House Of Knowledge',
            'alternateName' => 'SipaduHOK',
            'inLanguage' => 'id-ID',
            'publisher' => ['@id' => $situs . '/#organization'],
        ],
        [
            '@type' => 'WebPage',
            '@id' => $urlHalaman . '#webpage',
            'url' => $urlHalaman,
            'name' => $title,
            'description' => $description,
            'inLanguage' => 'id-ID',
            'isPartOf' => ['@id' => $situs . '/#website'],
            'about' => ['@id' => $situs . '/#organization'],
            'primaryImageOfPage' => ['@type' => 'ImageObject', 'url' => $gambar],
        ],
    ];
    if (! $beranda) {
        $graf[2]['breadcrumb'] = ['@id' => $urlHalaman . '#breadcrumb'];
        $graf[] = [
            '@type' => 'BreadcrumbList',
            '@id' => $urlHalaman . '#breadcrumb',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Beranda', 'item' => $situs . '/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $namaHalaman, 'item' => $urlHalaman],
            ],
        ];
    }
    $schema = ['@context' => 'https://schema.org', '@graph' => $graf];
@endphp

<!-- Primary Meta Tags -->
<title>{{ $title }}</title>
<meta name="description" content="{{ $description }}">
<meta name="keywords" content="{{ $keywords }}">
<meta name="author" content="PKBM House Of Knowledge">
<meta name="robots" content="{{ $robots }}">
<meta name="theme-color" content="#165fac">
<link rel="canonical" href="{{ $urlHalaman }}">

<!-- Favicons (versi kecil; logo asli 2.8k px terlalu berat untuk ikon) -->
<link rel="icon" type="image/png" sizes="48x48" href="{{ asset('img/icons/favicon-48.png') }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ asset('img/icons/icon-192.png') }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('img/icons/apple-touch-icon.png') }}">
<link rel="manifest" href="{{ asset('site.webmanifest') }}">

<!-- Open Graph / Facebook / WhatsApp -->
<meta property="og:type" content="website">
<meta property="og:url" content="{{ $urlHalaman }}">
<meta property="og:title" content="{{ $title }}">
<meta property="og:description" content="{{ $description }}">
<meta property="og:image" content="{{ $gambar }}">
@if($gambarBawaan)
<meta property="og:image:width" content="1920">
<meta property="og:image:height" content="1080">
@endif
<meta property="og:image:alt" content="{{ $title }}">
<meta property="og:locale" content="id_ID">
<meta property="og:site_name" content="PKBM House Of Knowledge">

<!-- Twitter -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $description }}">
<meta name="twitter:image" content="{{ $gambar }}">

<!-- Schema.org (JSON-LD) -->
<script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!}</script>
