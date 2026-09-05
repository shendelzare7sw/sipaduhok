@extends('layouts.app')

@section('title', 'Edit ' . $landingPage->title)

@section('page-title', 'Manajemen Landing Page')
@section('page-subtitle', 'Edit Konten ' . $landingPage->title)


@php
    // Urutan section sesuai tampilan di halaman depan (home.blade.php) & tentang kami
    $sectionOrder = ['hero', 'intro', 'stats', 'program', 'history', 'why_choose_us', 'about', 'visi', 'misi', 'values', 'news_header', 'gallery_section', 'contact_section', 'cta_section'];
    
    // Urutan khusus untuk PPDB
    $ppdbSectionOrder = ['hero', 'quick_info', 'alur', 'syarat_header', 'syarat_paud', 'syarat_paket_a', 'syarat_paket_b', 'syarat_paket_c', 'syarat_inklusi', 'investasi', 'biaya_paud', 'biaya_sd', 'biaya_smp', 'biaya_sma'];

    // Nama yang lebih mudah dipahami untuk setiap section
    $sectionLabels = [
        'hero' => 'Hero / Banner Utama',
        'intro' => 'Intro / Sambutan',
        'stats' => 'Statistik',
        'program' => 'Program Kami',
        'history' => 'Sejarah Sekolah',
        'why_choose_us' => 'Mengapa Memilih Kami',
        'about' => 'Tentang Kami',
        'news_header' => 'Header Berita',
        'gallery_section' => 'Galeri',
        'contact_section' => 'Kontak / Lokasi',
        'cta_section' => 'Call to Action (CTA)',
        'visi' => 'Visi Sekolah',
        'misi' => 'Misi Sekolah',
        'values' => 'Nilai-Nilai (Values)',
        // Label khusus PPDB
        'quick_info' => 'Info Cepat & Statistik',
        'alur' => 'Alur Pendaftaran',
        'syarat_header' => 'Header Syarat Pendaftaran',
        'syarat_paud' => 'Syarat PAUD',
        'syarat_paket_a' => 'Syarat SD (Paket A)',
        'syarat_paket_b' => 'Syarat SMP (Paket B)',
        'syarat_paket_c' => 'Syarat SMA (Paket C)',
        'syarat_inklusi' => 'Syarat Pendidikan Inklusi',
        'investasi' => 'Header Biaya / Investasi',
        'biaya_paud' => 'Biaya PAUD',
        'biaya_sd' => 'Biaya SD (Paket A)',
        'biaya_smp' => 'Biaya SMP (Paket B)',
        'biaya_sma' => 'Biaya SMA (Paket C)',
        // Label khusus Terapi
        'therapy_types' => 'Jenis Layanan Terapi',
        'alur_terapi' => 'Alur Layanan Terapi',
    ];
    
    // Deskripsi singkat untuk setiap section
    $sectionDescriptions = [
        'hero' => 'Bagian paling atas halaman, berisi judul utama, gambar, dan tombol aksi.',
        'intro' => 'Bagian pengenalan atau sambutan singkat.',
        'stats' => 'Menampilkan angka statistik seperti jumlah siswa, guru, dll.',
        'program' => 'Daftar program pendidikan yang ditawarkan.',
        'history' => 'Linimasa sejarah perjalanan sekolah.',
        'why_choose_us' => 'Daftar keunggulan atau alasan memilih sekolah ini.',
        'about' => 'Informasi umum tentang sekolah/lembaga.',
        'news_header' => 'Header untuk bagian berita/artikel terbaru.',
        'gallery_section' => 'Konfigurasi galeri foto.',
        'contact_section' => 'Informasi kontak dan lokasi cabang.',
        'cta_section' => 'Bagian ajakan untuk mendaftar atau menghubungi.',
        'visi' => 'Visi utama sekolah.',
        'misi' => 'Daftar misi sekolah.',
        'values' => 'Nilai-nilai utama yang dipegang teguh sekolah.',
        // Deskripsi PPDB
        'quick_info' => 'Informasi singkat mengenai periode, biaya pendaftaran, dan kuota.',
        'alur' => 'Langkah-langkah pendaftaran.',
        'syarat_header' => 'Judul utama bagian Syarat Pendaftaran.',
        'syarat_paud' => 'Dokumen persyaratan untuk jenjang PAUD.',
        'syarat_paket_a' => 'Dokumen persyaratan untuk jenjang SD (Paket A).',
        'syarat_paket_b' => 'Dokumen persyaratan untuk jenjang SMP (Paket B).',
        'syarat_paket_c' => 'Dokumen persyaratan untuk jenjang SMA (Paket C).',
        'syarat_inklusi' => 'Dokumen persyaratan untuk pendidikan inklusi.',
        'investasi' => 'Judul dan deskripsi utama untuk bagian biaya.',
        'biaya_paud' => 'Rincian biaya untuk jenjang PAUD.',
        'biaya_sd' => 'Rincian biaya untuk jenjang SD.',
        'biaya_smp' => 'Rincian biaya untuk jenjang SMP.',
        'biaya_sma' => 'Rincian biaya untuk jenjang SMA.',
        // Deskripsi Terapi
        'therapy_types' => 'Daftar jenis layanan terapi yang tersedia.',
        'alur_terapi' => 'Langkah-langkah alur layanan terapi.',
    ];
    
    // Section yang tidak boleh ditambah/hapus itemnya
    $fixedSections = ['programs', 'kurikulum', 'fasilitas', 'services', 'stats', 'contact_info', 'program', 'contact_section', 'news_header', 'gallery_section', 'cta_section', 'hero', 'about', 'history', 'intro', 'why_choose_us', 'quick_info', 'investasi', 'social_media'];
    
    // Sort sections
    $sortedSections = $landingPage->sections->filter(function($section) use ($landingPage) {
        if ($landingPage->slug === 'home' && in_array($section->section_key, ['gallery_section', 'contact_section'])) {
            return false; // Hide gallery & contact from home edit
        }
        return $section->section_key !== 'mata_pelajaran_c';
    })->sortBy(function($section) use ($sectionOrder, $ppdbSectionOrder, $landingPage) {
        // Gunakan urutan khusus jika ini halaman PPDB
        $orderList = ($landingPage->slug === 'ppdb') ? $ppdbSectionOrder : $sectionOrder;
        
        $key = array_search($section->section_key, $orderList);
        return $key !== false ? $key : 999;
    })->values();
@endphp

@section('content')
<div class="min-w-0 w-full space-y-4" data-landing-page-editor x-data="landingPageEditor()" @keydown.escape.window="resetOpen = false">
    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="min-w-0"><p class="text-xs font-bold text-brand-600">Editor halaman publik</p><h2 class="mt-1 truncate text-xl font-extrabold text-slate-950">{{ $landingPage->title }}</h2><p class="mt-1 text-sm text-slate-500">Atur isi dan visibilitas setiap bagian tanpa mengubah alamat halaman.</p></div>
        <div class="grid grid-cols-2 gap-2 sm:flex"><button type="button" @click="resetOpen = true" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-amber-50 px-4 text-sm font-bold text-amber-800 ring-1 ring-amber-200 hover:bg-amber-100"><i class="fas fa-rotate-left" aria-hidden="true"></i>Reset</button><a href="{{ route('admin.landing-pages.index') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a></div>
    </header>

    <form action="{{ route('admin.landing-pages.update', $landingPage->slug) }}" method="POST" enctype="multipart/form-data" id="editForm" @submit="submitting = true">
        @csrf
        @method('PUT')

        <div class="sticky top-20 z-30 mb-4 grid grid-cols-[minmax(0,1fr)_2.75rem] gap-2 rounded-2xl border border-slate-200 bg-white/95 p-2 shadow-lg backdrop-blur lg:hidden">
            <label class="relative min-w-0">
                <span class="sr-only">Pindah ke bagian halaman</span>
                <i class="fas fa-list-ol pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-brand-600" aria-hidden="true"></i>
                <select x-model="activeSection" @change="goToSection(activeSection)" class="h-11 w-full min-w-0 appearance-none rounded-xl border border-slate-300 bg-white !pl-10 pr-8 text-xs font-bold text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    @foreach($sortedSections as $index => $section)
                        <option value="{{ $section->section_key }}">{{ $index + 1 }}. {{ $sectionLabels[$section->section_key] ?? ucwords(str_replace('_', ' ', $section->section_key)) }}{{ $section->is_visible ? '' : ' (tersembunyi)' }}</option>
                    @endforeach
                </select>
                <i class="fas fa-chevron-down pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 text-[10px] text-slate-400" aria-hidden="true"></i>
            </label>
            <button type="submit" :disabled="submitting" class="flex h-11 w-11 items-center justify-center rounded-xl bg-brand-600 text-white shadow-sm hover:bg-brand-700 disabled:cursor-wait disabled:opacity-60" aria-label="Simpan perubahan" title="Simpan perubahan"><i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" aria-hidden="true"></i></button>
        </div>

        <div class="grid min-w-0 gap-4 lg:grid-cols-[16rem_minmax(0,1fr)] lg:items-start">
            <aside class="hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:sticky lg:top-24 lg:flex lg:max-h-[calc(100vh-7rem)] lg:flex-col lg:self-start">
                <header class="border-b border-slate-200 px-4 py-3"><h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fas fa-list-ol text-brand-600" aria-hidden="true"></i>Navigasi bagian</h3><p class="mt-1 text-xs text-slate-500">{{ $sortedSections->count() }} bagian dapat dikelola.</p></header>
                <nav class="min-h-0 flex-1 space-y-1 overflow-y-auto p-2" aria-label="Bagian halaman">
                            @foreach($sortedSections as $index => $section)
                                <a href="#section-{{ $section->section_key }}"
                                   @click="activeSection = '{{ $section->section_key }}'"
                                   :class="activeSection === '{{ $section->section_key }}' ? 'border-brand-200 bg-brand-50 text-brand-800' : 'border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                                   class="flex items-center gap-2 rounded-xl border px-3 py-2.5 text-xs font-bold no-underline">
                                    <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-[10px] font-extrabold ring-1 ring-slate-200">{{ $index + 1 }}</span>
                                    <span class="min-w-0 flex-1 truncate {{ $section->is_visible ? '' : 'line-through opacity-60' }}">{{ $sectionLabels[$section->section_key] ?? ucwords(str_replace('_', ' ', $section->section_key)) }}</span>
                                    @if(!$section->is_visible)
                                        <i class="fas fa-eye-slash shrink-0 text-slate-400" title="Tersembunyi" aria-hidden="true"></i>
                                    @endif
                                </a>
                            @endforeach
                </nav>
                <div class="border-t border-slate-200 p-3"><button type="submit" :disabled="submitting" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white hover:bg-brand-700 disabled:cursor-wait disabled:opacity-60"><i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" aria-hidden="true"></i><span x-text="submitting ? 'Menyimpan...' : 'Simpan perubahan'"></span></button></div>
            </aside>

            <div class="min-w-0 space-y-4">
                @foreach($sortedSections as $index => $section)
                    @php
                        $sectionKey = $section->section_key;
                        $label = $sectionLabels[$sectionKey] ?? ucwords(str_replace('_', ' ', $sectionKey));
                        $description = $sectionDescriptions[$sectionKey] ?? '';
                        $canAddRemove = !in_array($sectionKey, $fixedSections);
                        
                        $items = [];
                        $header = [];
                        if ($section->type === 'list') {
                            $content = $section->content;
                            if (isset($content['items']) && is_array($content['items'])) {
                                $items = $content['items'];
                                $header = $content['header'] ?? [];
                            } elseif (is_array($content) && !empty($content) && isset($content[0])) {
                                $items = $content;
                            }
                            
                            // Initialize header for specific sections if empty
                            if (str_contains($sectionKey, 'why_choose_us') && empty($header)) {
                                $header = ['title' => 'Mengapa Memilih Kami?', 'description' => '', 'badge' => 'Keunggulan Kami'];
                            }
                            // Also for history
                            if (str_contains($sectionKey, 'history') && empty($header)) {
                                $header = ['title' => 'Sejarah PKBM House Of Knowledge', 'description' => '', 'badge' => 'Perjalanan Kami'];
                            }
                            
                            // Initialize header for misi/values
                             if (str_contains($sectionKey, 'misi') && empty($header)) {
                                $header = ['title' => 'Misi PKBM House Of Knowledge', 'badge' => 'Misi Kami'];
                            }
                             if (str_contains($sectionKey, 'values') && empty($header)) {
                                $header = ['title' => 'Nilai-Nilai Kami', 'description' => 'Prinsip yang menjadi landasan kami'];
                            }

                            // Initialize header for Biaya sections
                            if (str_contains($sectionKey, 'biaya_')) {
                                if (empty($header)) {
                                    $level = strtoupper(str_replace('biaya_', '', $sectionKey));
                                    $header = ['title' => $level, 'image' => null];
                                } elseif (!isset($header['image'])) {
                                    // Always ensure image key exists for upload
                                    $header['image'] = null;
                                }
                            }
                        }

                        // Define default item structure based on section key
                        $defaultItem = match(true) {
                            str_contains($sectionKey, 'stats') => ['value' => '', 'label' => ''],
                            $sectionKey === 'program_links' || $sectionKey === 'quick_links' => ['label' => '', 'url' => ''],
                            str_contains($sectionKey, 'program') => ['icon' => null, 'color' => 'primary', 'title' => '', 'description' => '', 'link' => '#'],
                            str_contains($sectionKey, 'biaya_') => ['name' => '', 'price' => '', 'type' => 'pokok'],
                            str_contains($sectionKey, 'why_choose_us') => ['icon' => null, 'icon_color' => '#165fac', 'title' => '', 'description' => ''],
                            str_contains($sectionKey, 'history') => ['year' => '', 'title' => '', 'description' => '', 'image' => null, 'color' => '#165fac'],
                            str_contains($sectionKey, 'misi') => ['title' => '', 'description' => '', 'color' => 'primary'],
                            str_contains($sectionKey, 'values') => ['icon' => 'fas fa-star', 'title' => '', 'icon_color' => 'orange'],
                            str_contains($sectionKey, 'jadwal') => ['time' => '', 'activity' => ''],
                            str_contains($sectionKey, 'staff_list') => ['image' => null, 'name' => '', 'department' => '', 'category' => '', 'position' => ''],
                            str_contains($sectionKey, 'leaders') || str_contains($sectionKey, 'coordinators') || str_contains($sectionKey, 'staff') => ['image' => null, 'color' => '#165fac', 'name' => '', 'department' => '', 'position' => ''],
                            str_contains($sectionKey, 'mata_pelajaran') => ['icon' => null, 'icon_color' => '#165fac', 'title' => '', 'description' => '', 'card_color' => '#ffffff'],
                            str_contains($sectionKey, 'team') => ['icon' => null, 'color' => '#facc15', 'title' => '', 'description' => '', 'link' => '#'],
                            str_contains($sectionKey, 'jurusan') => ['icon' => null, 'icon_color' => '#165fac', 'title' => '', 'description' => '', 'features' => '', 'card_gradient_start' => '#ffffff', 'card_gradient_end' => '#ffffff'],
                            str_contains($sectionKey, 'prospek') => ['icon' => null, 'icon_color' => '#165fac', 'title' => '', 'description' => '', 'subtitle' => ''],
                            str_contains($sectionKey, 'categories') => ['color' => '#165fac', 'title' => '', 'description' => '', 'key' => '', 'label' => ''],
                            str_contains($sectionKey, 'gallery_items') => ['image' => null, 'title' => '', 'date' => '', 'category' => ''],
                            str_contains($sectionKey, 'ruang_') || str_contains($sectionKey, 'area_') || str_contains($sectionKey, 'perpustakaan') || str_contains($sectionKey, 'gallery') => ['image' => null, 'title' => '', 'description' => ''],
                            str_contains($sectionKey, 'locations') => ['color' => '#165fac', 'area' => '', 'name' => '', 'address' => '', 'map_link' => '', 'map_embed' => ''],
                            str_contains($sectionKey, 'keunggulan') => ['icon' => null, 'color' => '#165fac', 'title' => '', 'description' => ''],
                            str_contains($sectionKey, 'therapy_types') => ['color' => '#165fac', 'title' => '', 'description' => '', 'features' => ''],
                            str_contains($sectionKey, 'alur_terapi') => ['title' => '', 'description' => ''],
                            str_contains($sectionKey, 'syarat_') => ['text' => ''],
                            str_contains($sectionKey, 'social_media') => ['platform' => '', 'link' => ''],
                            default => ['title' => '', 'description' => '']
                        };
                    @endphp
                    
                    <section class="scroll-mt-36 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm lg:scroll-mt-24 {{ $section->is_visible ? '' : 'opacity-75' }}" id="section-{{ $sectionKey }}" data-editor-section="{{ $sectionKey }}" x-data="{ visible: {{ $section->is_visible ? 'true' : 'false' }} }">
                        <input type="hidden" name="sections[{{ $section->id }}][type]" value="{{ $section->type }}">
                        <input type="hidden" name="sections[{{ $section->id }}][is_visible]" value="0">

                        <header class="border-b border-slate-200 bg-slate-50 p-4 sm:p-5">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-600 text-sm font-extrabold text-white">{{ $index + 1 }}</span>
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="min-w-0"><h3 class="font-extrabold text-slate-950">{{ $label }}</h3>@if($description)<p class="mt-1 text-xs leading-5 text-slate-500">{{ $description }}</p>@endif</div>
                                        <label class="inline-flex min-h-10 cursor-pointer items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700">
                                            <input type="checkbox" id="visible-{{ $section->id }}" name="sections[{{ $section->id }}][is_visible]" value="1" x-model="visible" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" {{ $section->is_visible ? 'checked' : '' }}>
                                            <span x-text="visible ? 'Ditampilkan' : 'Disembunyikan'"></span>
                                            <i class="fas text-slate-400" :class="visible ? 'fa-eye' : 'fa-eye-slash'" aria-hidden="true"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </header>

                        <div class="p-4 sm:p-5">
                            
                            {{-- Tipe List (dengan items) --}}
                            @if($section->type === 'list')
                                
                                {{-- Header Section (jika ada) --}}
                                @if(!empty($header))
                                    <section class="mb-5 rounded-2xl border border-blue-100 bg-blue-50/60 p-4">
                                            <h4 class="mb-4 flex items-center gap-2 text-sm font-extrabold text-blue-950"><i class="fas fa-heading text-blue-600" aria-hidden="true"></i>Pengaturan header</h4>
                                            <div class="grid gap-4 md:grid-cols-2">
                                                @foreach($header as $hKey => $hValue)
                                                    <label class="block min-w-0"><span class="mb-1.5 block text-xs font-bold text-slate-700">{{ ucwords(str_replace('_', ' ', $hKey)) }}</span>
                                                        @if($hKey === 'image' || str_contains($hKey, 'image'))
                                                            <span class="flex min-w-0 items-center gap-2">
                                                                @if($hValue && (str_contains($hValue, '/') || str_contains($hValue, '.')))
                                                                    <span data-image-preview class="flex h-11 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-white"><img src="{{ asset($hValue) }}" alt="Pratinjau" class="h-full w-full object-cover" x-on:error="$el.parentElement.hidden = true"></span>
                                                                @endif
                                                                @if($hValue)
                                                                    <input type="hidden" name="sections[{{ $section->id }}][header][{{ $hKey }}]" value="{{ $hValue }}">
                                                                @endif
                                                                <input type="file" name="sections[{{ $section->id }}][header][{{ $hKey }}]" accept="image/*" @change="previewImage($event)" class="block min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-2 file:rounded-lg file:border-0 file:bg-brand-50 file:px-2 file:py-1.5 file:font-bold file:text-brand-700">
                                                            </span>
                                                        @elseif(str_contains($hKey, 'color'))
                                                            <span class="flex h-11 items-center gap-3 rounded-xl border border-slate-300 bg-white px-2"><input type="color" name="sections[{{ $section->id }}][header][{{ $hKey }}]" value="{{ $hValue && str_starts_with($hValue, '#') ? $hValue : '#165fac' }}" class="h-8 w-14 cursor-pointer rounded-lg border-0 bg-transparent" title="Pilih warna"><code class="text-xs text-slate-500">{{ $hValue }}</code></span>
                                                        @elseif(str_contains($hKey, 'description') || str_contains($hKey, 'note') || (is_string($hValue) && strlen($hValue) > 80))
                                                            <textarea name="sections[{{ $section->id }}][header][{{ $hKey }}]" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">{{ $hValue }}</textarea>
                                                        @else
                                                            <input type="text" name="sections[{{ $section->id }}][header][{{ $hKey }}]" value="{{ $hValue }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                                                        @endif
                                                    </label>
                                                @endforeach
                                            </div>
                                    </section>
                                @endif

                                {{-- Items List --}}
                                <div class="items-container space-y-4" data-section-id="{{ $section->id }}">

                                    {{-- Layout for List Items --}}
                                    @foreach($items as $itemIndex => $item)
                                        @php
                                            // Merge with default structure
                                            $item = array_merge($defaultItem, (array)$item);

                                            // Separate fields into buckets
                                            $visualFields = [];
                                            $textFields = [];

                                            foreach($item as $key => $value) {
                                                // Check for exclusion (Leaders, Coordinators, Staff: Hide Title & Description)
                                                if ((str_contains($sectionKey, 'leaders') || str_contains($sectionKey, 'coordinators') || str_contains($sectionKey, 'staff') || str_contains($sectionKey, 'staff_list') || str_contains($sectionKey, 'jadwal')) && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Khusus PPDB: Sembunyikan Title & Description pada section biaya
                                                if ($landingPage->slug === 'ppdb' && str_contains($sectionKey, 'biaya_') && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Gallery Categories: Sembunyikan Color, Title, & Description
                                                if ($landingPage->slug === 'galeri' && str_contains($sectionKey, 'categories') && in_array($key, ['color', 'title', 'description'])) {
                                                    continue;
                                                }

                                                // Gallery Items: Sembunyikan Description
                                                if (str_contains($sectionKey, 'gallery_items') && $key === 'description') {
                                                    continue;
                                                }

                                                // Contact Info: Sembunyikan Description
                                                if (str_contains($sectionKey, 'contact_info') && $key === 'description') {
                                                    continue;
                                                }

                                                // Social Media: Sembunyikan Title, Description, Icon, & Color
                                                if (str_contains($sectionKey, 'social_media') && in_array($key, ['title', 'description', 'icon', 'color'])) {
                                                    continue;
                                                }

                                                // Program Links & Quick Links
                                                if (($sectionKey === 'program_links' || $sectionKey === 'quick_links') && in_array($key, ['title', 'description', 'icon', 'color', 'link'])) {
                                                    continue;
                                                }

                                                // Locations: Sembunyikan Title & Description
                                                if (str_contains($sectionKey, 'locations') && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Mata Pelajaran: Sembunyikan Description
                                                if (str_contains($sectionKey, 'mata_pelajaran') && $key === 'description') {
                                                    continue;
                                                }

                                                // Prospek: Sembunyikan Description
                                                if (str_contains($sectionKey, 'prospek') && $key === 'description') {
                                                    continue;
                                                }

                                                // Group visual related keys
                                                if(in_array($key, ['image', 'icon', 'color', 'icon_color', 'card_color', 'card_gradient_start', 'card_gradient_end', 'background_image'])) {
                                                    // Explicitly exclude image/icon for jadwal section
                                                    if(str_contains($sectionKey, 'jadwal')) { continue; }
                                                    // Explicitly exclude ONLY icon (upload) for stats section
                                                    if(str_contains($sectionKey, 'stats') && $key === 'icon') { continue; }
                                                    // Explicitly exclude color for coordinators section
                                                    if(str_contains($sectionKey, 'coordinators') && $key === 'color') { continue; }
                                                    // Explicitly exclude icon_color for keunggulan section
                                                    if(str_contains($sectionKey, 'keunggulan') && $key === 'icon_color') { continue; }
                                                    $visualFields[$key] = $value;
                                                // Exclude internal/legacy fields related to color text inputs
                                                } elseif (str_contains($key, 'color') && (str_ends_with($key, '_text') || str_contains($key, 'text'))) {
                                                    continue;
                                                } else {
                                                    $textFields[$key] = $value;
                                                }
                                            }
                                        @endphp
                                        @include('admin.landing-pages.partials.item-card', ['item' => $item, 'itemIndex' => $itemIndex, 'section' => $section, 'visualFields' => $visualFields, 'textFields' => $textFields, 'canAddRemove' => $canAddRemove, 'isTemplate' => false])
                                    @endforeach
                                </div>

                                {{-- Template item untuk Alpine cloning --}}
                                @if($canAddRemove)
                                    <template data-template-for="{{ $section->id }}">
                                        @php
                                            $templateVisualFields = [];
                                            $templateTextFields = [];
                                            foreach($defaultItem as $key => $value) {
                                                // Apply same exclusions as item rendering

                                                // Leaders, Coordinators, Staff, Staff List, Jadwal: Hide Title & Description
                                                if ((str_contains($sectionKey, 'leaders') || str_contains($sectionKey, 'coordinators') || str_contains($sectionKey, 'staff') || str_contains($sectionKey, 'staff_list') || str_contains($sectionKey, 'jadwal')) && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // PPDB: Sembunyikan Title & Description pada section biaya
                                                if ($landingPage->slug === 'ppdb' && str_contains($sectionKey, 'biaya_') && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Gallery Categories: Sembunyikan Color, Title, & Description
                                                if ($landingPage->slug === 'galeri' && str_contains($sectionKey, 'categories') && in_array($key, ['color', 'title', 'description'])) {
                                                    continue;
                                                }

                                                // Gallery Items: Sembunyikan Description
                                                if (str_contains($sectionKey, 'gallery_items') && $key === 'description') {
                                                    continue;
                                                }

                                                // Contact Info: Sembunyikan Description
                                                if (str_contains($sectionKey, 'contact_info') && $key === 'description') {
                                                    continue;
                                                }

                                                // Social Media: Sembunyikan Title, Description, Icon, & Color
                                                if (str_contains($sectionKey, 'social_media') && in_array($key, ['title', 'description', 'icon', 'color'])) {
                                                    continue;
                                                }

                                                // Program Links & Quick Links
                                                if (($sectionKey === 'program_links' || $sectionKey === 'quick_links') && in_array($key, ['title', 'description', 'icon', 'color', 'link'])) {
                                                    continue;
                                                }

                                                // Locations: Sembunyikan Title & Description
                                                if (str_contains($sectionKey, 'locations') && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Mata Pelajaran: Sembunyikan Description
                                                if (str_contains($sectionKey, 'mata_pelajaran') && $key === 'description') {
                                                    continue;
                                                }

                                                // Prospek: Sembunyikan Description
                                                if (str_contains($sectionKey, 'prospek') && $key === 'description') {
                                                    continue;
                                                }

                                                // Group visual related keys
                                                if(in_array($key, ['image', 'icon', 'color', 'icon_color', 'card_color', 'card_gradient_start', 'card_gradient_end', 'background_image'])) {
                                                    // Explicitly exclude image/icon for jadwal section
                                                    if(str_contains($sectionKey, 'jadwal')) { continue; }
                                                    // Explicitly exclude ONLY icon (upload) for stats section
                                                    if(str_contains($sectionKey, 'stats') && $key === 'icon') { continue; }
                                                    // Explicitly exclude color for coordinators section
                                                    if(str_contains($sectionKey, 'coordinators') && $key === 'color') { continue; }
                                                    // Explicitly exclude icon_color for keunggulan section
                                                    if(str_contains($sectionKey, 'keunggulan') && $key === 'icon_color') { continue; }
                                                    $templateVisualFields[$key] = $value;
                                                // Exclude internal/legacy fields related to color text inputs
                                                } elseif (str_contains($key, 'color') && (str_ends_with($key, '_text') || str_contains($key, 'text'))) {
                                                    continue;
                                                } else {
                                                    $templateTextFields[$key] = $value;
                                                }
                                            }
                                        @endphp
                                        @include('admin.landing-pages.partials.item-card', ['item' => $defaultItem, 'itemIndex' => 'TEMPLATE_INDEX', 'section' => $section, 'visualFields' => $templateVisualFields, 'textFields' => $templateTextFields, 'canAddRemove' => true, 'isTemplate' => true])
                                    </template>

                                    <button type="button" @click="addItem('{{ $section->id }}')" class="mt-4 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl border border-dashed border-brand-300 bg-brand-50 text-sm font-bold text-brand-700 hover:bg-brand-100">
                                        <i class="fas fa-circle-plus" aria-hidden="true"></i>Tambah item baru
                                    </button>
                                @endif

                            {{-- Tipe Single/Text --}}
                            @else
                                <div class="grid gap-4 md:grid-cols-2">
                                    @foreach($section->content as $key => $value)
                                        @if(is_array($value)) @continue @endif
                                        @if($sectionKey === 'hero' && $key === 'image') @continue @endif
                                        <label class="block min-w-0"><span class="mb-1.5 block text-xs font-bold text-slate-700">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                            @if($key === 'image' || str_contains($key, 'image') || $key === 'icon')
                                                <span class="flex min-w-0 items-center gap-2">
                                                    @if($value && (str_contains($value, '/') || str_contains($value, '.')))
                                                        <input type="hidden" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value }}">
                                                        <span data-image-preview class="flex h-11 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50"><img src="{{ asset($value) }}" alt="Pratinjau" class="h-full w-full object-cover" x-on:error="$el.parentElement.hidden = true"></span>
                                                    @endif
                                                    <input type="file" name="sections[{{ $section->id }}][{{ $key }}]" accept="image/*" @change="previewImage($event)" class="block min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-2 file:rounded-lg file:border-0 file:bg-brand-50 file:px-2 file:py-1.5 file:font-bold file:text-brand-700">
                                                </span>
                                            @elseif(str_contains($key, 'description') || (is_string($value) && strlen($value) > 80))
                                                <textarea name="sections[{{ $section->id }}][{{ $key }}]" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">{{ $value }}</textarea>
                                            @elseif(str_contains($key, 'color'))
                                                <span class="grid grid-cols-[3.5rem_minmax(0,1fr)] gap-2"><input type="color" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value && substr($value, 0, 1) === '#' ? $value : '#566a7f' }}" class="h-11 w-full cursor-pointer rounded-xl border border-slate-300 bg-white p-1"><input type="text" name="sections[{{ $section->id }}][{{ $key }}_text]" value="{{ $value }}" placeholder="Kode hex atau nama kelas" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span>
                                            @else
                                                <input type="text" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                            
                        </div>
                    </section>
                @endforeach

                <button type="submit" :disabled="submitting" class="inline-flex h-12 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white shadow-sm hover:bg-brand-700 disabled:cursor-wait disabled:opacity-60 lg:hidden"><i class="fas" :class="submitting ? 'fa-spinner fa-spin' : 'fa-floppy-disk'" aria-hidden="true"></i><span x-text="submitting ? 'Menyimpan...' : 'Simpan semua perubahan'"></span></button>
            </div>
        </div>
    </form>

    <template x-teleport="body">
        <div x-cloak x-show="resetOpen" x-transition.opacity class="fixed inset-0 z-[1100] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" @click.self="resetOpen = false">
            <section x-show="resetOpen" x-transition class="w-full max-w-md rounded-2xl bg-white p-5 shadow-2xl" role="dialog" aria-modal="true" aria-labelledby="reset-title">
                <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-xl text-amber-700"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span><h3 id="reset-title" class="mt-4 text-lg font-extrabold text-slate-950">Reset ke konten awal?</h3><p class="mt-2 text-sm leading-6 text-slate-600">Semua perubahan pada halaman ini akan diganti dengan data bawaan. Tindakan ini memengaruhi konten publik.</p>
                <div class="mt-5 grid grid-cols-2 gap-2"><button type="button" @click="resetOpen = false" class="h-11 rounded-xl border border-slate-300 bg-white text-sm font-bold text-slate-700 hover:bg-slate-50">Batal</button><a href="{{ route('admin.landing-pages.reset', $landingPage->slug) }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-red-600 px-4 text-sm font-bold text-white no-underline hover:bg-red-700">Reset konten</a></div>
            </section>
        </div>
    </template>
</div>

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('landingPageEditor', () => ({
        activeSection: @js($sortedSections->first()?->section_key ?? ''),
        resetOpen: false,
        submitting: false,
        goToSection(sectionKey) {
            document.getElementById(`section-${sectionKey}`)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
        },
        addItem(sectionId) {
            const container = this.$root.querySelector(`.items-container[data-section-id="${sectionId}"]`);
            const template = this.$root.querySelector(`template[data-template-for="${sectionId}"]`);
            if (!container || !template) return;
            const clone = template.content.firstElementChild.cloneNode(true);
            const index = container.querySelectorAll(':scope > .item-wrapper').length;
            clone.querySelector('.item-title').textContent = `Item #${index + 1}`;
            clone.querySelectorAll('input, textarea, select').forEach((field) => {
                if (field.name) field.name = field.name.replaceAll('TEMPLATE_INDEX', String(index));
                field.disabled = false;
            });
            container.appendChild(clone);
            Alpine.initTree(clone);
            clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
        },
        async removeItem(button) {
            const item = button.closest('.item-wrapper');
            const container = item?.closest('.items-container');
            if (!item || !container) return;
            const result = await Swal.fire({ icon: 'warning', title: 'Hapus item ini?', text: 'Item akan dihapus setelah perubahan disimpan.', showCancelButton: true, confirmButtonText: 'Hapus item', cancelButtonText: 'Batal', confirmButtonColor: '#dc2626', reverseButtons: true, focusCancel: true });
            if (!result.isConfirmed) return;
            item.remove();
            this.reindexItems(container);
        },
        reindexItems(container) {
            container.querySelectorAll(':scope > .item-wrapper').forEach((item, index) => {
                item.querySelector('.item-title').textContent = `Item #${index + 1}`;
                item.querySelectorAll('input, textarea, select').forEach((field) => {
                    if (field.name) field.name = field.name.replace(/\[items\]\[\d+\]/g, `[items][${index}]`);
                });
            });
        },
        previewImage(event) {
            const input = event.target;
            const file = input.files?.[0];
            if (!file?.type.startsWith('image/')) return;
            const parent = input.closest('label') || input.parentElement;
            let preview = parent?.querySelector('[data-image-preview]');
            if (!preview) {
                preview = document.createElement('span');
                preview.dataset.imagePreview = '';
                preview.className = 'flex h-11 w-12 shrink-0 items-center justify-center overflow-hidden rounded-xl border border-slate-200 bg-slate-50';
                input.parentElement.prepend(preview);
            }
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = 'Pratinjau gambar baru';
            image.className = 'h-full max-h-24 w-full object-contain';
            preview.replaceChildren(image);
            preview.hidden = false;
        },
        init() {
            this.$root.addEventListener('click', (event) => {
                const button = event.target.closest('.remove-item');
                if (button) { event.preventDefault(); this.removeItem(button); }
            });
            const sections = [...this.$root.querySelectorAll('[data-editor-section]')];
            if ('IntersectionObserver' in window && sections.length) {
                const observer = new IntersectionObserver((entries) => {
                    const visible = entries
                        .filter(entry => entry.isIntersecting)
                        .sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)[0];
                    if (visible) this.activeSection = visible.target.dataset.editorSection;
                }, { rootMargin: '-22% 0px -68% 0px', threshold: 0 });
                sections.forEach(section => observer.observe(section));
            }
        },
    }));
});
</script>
@endpush
@endsection
