<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Pendidikan SD - SMP - SMA | PKBM House Of Knowledge</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .hero-overlay {
            background: linear-gradient(135deg, rgba(22, 95, 172, 0.9) 0%, rgba(40, 127, 59, 0.8) 100%);
        }

        .card-hover {
            transition: .3s ease;
        }

        .card-hover:hover {
            transform: translateY(-5px);
        }

        .tab-btn.active {
            background-color: #165fac;
            color: white;
        }

        /* --- Animasi Smooth Saat Ganti Tab --- */
        .fade-in {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeInSoft .35s ease-out forwards;
        }

        @keyframes fadeInSoft {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

</head>

<body class="bg-gray-50">
    @php
        $heroSection = $page->getSection('hero');
        $heroContent = $heroSection->content ?? [];

        // Get Paket A, B, C sections
        $paketASection = $page->getSection('paket_a');
        $paketAContent = $paketASection->content ?? [];

        $paketBSection = $page->getSection('paket_b');
        $paketBContent = $paketBSection->content ?? [];

        $paketCSection = $page->getSection('paket_c');
        $paketCContent = $paketCSection->content ?? [];
    @endphp

    <x-navbar></x-navbar>

    <!-- HERO -->
    <section class="relative h-[350px] flex items-center justify-center"
        style="background-image: url('{{ asset($heroContent['background_image'] ?? 'img/hero-bg.jpg') }}'); background-size: cover; background-position: center;">
        <div class="hero-overlay absolute inset-0"></div>
        <div class="relative z-10 text-center text-white px-4">
            <nav class="text-sm mb-4">
                <a href="{{ url('/') }}" class="hover:underline">Beranda</a>
                <span class="mx-2">/</span>
                <span class="font-semibold">{{ $heroContent['title'] ?? 'Program Pendidikan' }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-bold">{{ $heroContent['title'] ?? 'Program Pendidikan Kesetaraan' }}
            </h1>
            <p class="mt-4 text-white/90">{{ $heroContent['subtitle'] ?? 'Paket A • Paket B • Paket C' }}</p>
        </div>
    </section>

    <!-- SWITCH TAB -->
    <section class="py-10 bg-white shadow-sm border-b">
        <div class="max-w-5xl mx-auto px-4 flex justify-center gap-4">
            <button class="tab-btn px-6 py-3 rounded-full border font-medium" data-tab="sd">Paket A (SD)</button>
            <button class="tab-btn px-6 py-3 rounded-full border font-medium" data-tab="smp">Paket B (SMP)</button>
            <button class="tab-btn px-6 py-3 rounded-full border font-medium" data-tab="sma">Paket C (SMA)</button>
        </div>
    </section>

    <!-- SD • Paket A -->
    <div id="tab-sd" class="tab-content">
        @include('partials.program-sd-content', ['content' => $paketAContent])
    </div>

    <!-- SMP • Paket B -->
    <div id="tab-smp" class="tab-content hidden">
        @include('partials.program-smp-content', ['content' => $paketBContent])
    </div>

    <!-- SMA • Paket C -->
    <div id="tab-sma" class="tab-content hidden">
        @include('partials.program-sma-content', ['content' => $paketCContent])
    </div>

    <!-- SIMPLE SCRIPT TO SWITCH TAB -->
    <script>
        const tabs = document.querySelectorAll('.tab-btn');
        const contents = document.querySelectorAll('.tab-content');

        tabs.forEach(btn => {
            btn.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                contents.forEach(c => c.classList.add('hidden'));

                btn.classList.add('active');
                const activeContent = document.getElementById('tab-' + btn.dataset.tab);

                // FIX → Jangan sembunyikan elemen, cukup reset opacity
                activeContent.style.opacity = 0;

                // tampilkan
                activeContent.classList.remove('hidden');

                // animasi fade
                setTimeout(() => {
                    activeContent.classList.add('fade-in');
                }, 10);
            });
        });

        // default tab
        tabs[0].click();
    </script>



    <x-footer />

</body>

</html>