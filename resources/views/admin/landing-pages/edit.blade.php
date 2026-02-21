@extends('layouts.sneat')

@section('title', 'Edit ' . $landingPage->title)

@section('page-title', 'Manajemen Landing Page')
@section('page-subtitle', 'Edit Konten ' . $landingPage->title)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@php
    // Urutan section sesuai tampilan di halaman depan (home.blade.php) & tentang kami
    $sectionOrder = ['hero', 'intro', 'stats', 'program', 'history', 'why_choose_us', 'about', 'visi', 'misi', 'values', 'news_header', 'gallery_section', 'contact_section', 'cta_section'];
    
    // Urutan khusus untuk PPDB
    $ppdbSectionOrder = ['hero', 'quick_info', 'alur', 'investasi', 'biaya_paud', 'biaya_sd', 'biaya_smp', 'biaya_sma'];

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
        'investasi' => 'Header Biaya / Investasi',
        'biaya_paud' => 'Biaya PAUD',
        'biaya_sd' => 'Biaya SD (Paket A)',
        'biaya_smp' => 'Biaya SMP (Paket B)',
        'biaya_sma' => 'Biaya SMA (Paket C)',
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
        'investasi' => 'Judul dan deskripsi utama untuk bagian biaya.',
        'biaya_paud' => 'Rincian biaya untuk jenjang PAUD.',
        'biaya_sd' => 'Rincian biaya untuk jenjang SD.',
        'biaya_smp' => 'Rincian biaya untuk jenjang SMP.',
        'biaya_sma' => 'Rincian biaya untuk jenjang SMA.',
    ];
    
    // Section yang tidak boleh ditambah/hapus itemnya
    $fixedSections = ['programs', 'kurikulum', 'fasilitas', 'services', 'therapy_types', 'stats', 'contact_info', 'program', 'contact_section', 'news_header', 'gallery_section', 'cta_section', 'hero', 'about', 'history', 'intro', 'why_choose_us', 'quick_info', 'investasi'];
    
    // Sort sections
    $sortedSections = $landingPage->sections->filter(function($section) {
        return $section->section_key !== 'mata_pelajaran_c';
    })->sortBy(function($section) use ($sectionOrder, $ppdbSectionOrder, $landingPage) {
        // Gunakan urutan khusus jika ini halaman PPDB
        $orderList = ($landingPage->slug === 'ppdb') ? $ppdbSectionOrder : $sectionOrder;
        
        $key = array_search($section->section_key, $orderList);
        return $key !== false ? $key : 999;
    })->values();
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    
    {{-- Page Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bx bx-edit-alt text-primary me-2"></i>Edit Landing Page
            </h4>
            <p class="text-muted mb-0">
                <a href="{{ route('admin.landing-pages.index') }}" class="text-decoration-none">Manajemen Landing Page</a>
                <span class="mx-1">/</span>
                <span class="fw-medium">{{ $landingPage->title }}</span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#resetModal">
                <i class="bx bx-reset me-1"></i> Reset
            </button>
            <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-outline-secondary">
                <i class="bx bx-arrow-back me-1"></i> Kembali
            </a>
        </div>
    </div>

    <form action="{{ route('admin.landing-pages.update', $landingPage->slug) }}" method="POST" enctype="multipart/form-data" id="editForm">
        @csrf
        @method('PUT')

        <div class="row">
            
            {{-- Sidebar Navigasi --}}
            <div class="col-lg-3 col-md-4 mb-4">
                <div class="card sticky-top" style="top: 80px; z-index: 100;">
                    <div class="card-header py-3">
                        <h6 class="mb-0 fw-bold">
                            <i class="bx bx-list-ul me-1"></i> Navigasi Section
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($sortedSections as $index => $section)
                                <a href="#section-{{ $section->section_key }}" 
                                   class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 section-nav-link {{ $index === 0 ? 'active' : '' }}">
                                    <span class="badge bg-label-primary rounded-circle">{{ $index + 1 }}</span>
                                    <span class="text-truncate">{{ $sectionLabels[$section->section_key] ?? ucwords(str_replace('_', ' ', $section->section_key)) }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer p-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                    </div>
                </div>
            </div>

            {{-- Konten Section --}}
            <div class="col-lg-9 col-md-8">
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
                            default => ['title' => '', 'description' => '']
                        };
                    @endphp
                    
                    <div class="card mb-4 section-card" id="section-{{ $sectionKey }}" style="scroll-margin-top: 90px;">
                        <input type="hidden" name="sections[{{ $section->id }}][type]" value="{{ $section->type }}">
                        
                        {{-- Section Header --}}
                        <div class="card-header bg-light border-bottom">
                            <div class="d-flex align-items-center gap-3">
                                <span class="badge bg-primary rounded-pill fs-6">{{ $index + 1 }}</span>
                                <div>
                                    <h5 class="mb-0 fw-bold">{{ $label }}</h5>
                                    @if($description)
                                        <small class="text-muted">{{ $description }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="card-body">
                            
                            {{-- Tipe List (dengan items) --}}
                            @if($section->type === 'list')
                                
                                {{-- Header Section (jika ada) --}}
                                @if(!empty($header))
                                    <div class="card bg-label-secondary border-0 mb-4">
                                        <div class="card-body">
                                            <h6 class="card-title fw-bold mb-3">
                                                <i class="bx bx-heading me-1"></i> Pengaturan Header
                                            </h6>
                                            <div class="row g-3">
                                                @foreach($header as $hKey => $hValue)
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-semibold">
                                                            {{ ucwords(str_replace('_', ' ', $hKey)) }}
                                                        </label>
                                                        @if($hKey === 'image' || str_contains($hKey, 'image'))
                                                            <div class="input-group">
                                                                @if($hValue && (str_contains($hValue, '/') || str_contains($hValue, '.')))
                                                                    <span class="input-group-text p-0 overflow-hidden" style="width: 42px;">
                                                                        <img src="{{ asset($hValue) }}" alt="Preview" class="w-100 h-100" style="object-fit: cover; min-height: 38px;" onerror="this.parentElement.style.display='none'">
                                                                    </span>
                                                                @endif
                                                                {{-- Hidden input to preserve old value when no new file is uploaded --}}
                                                                @if($hValue)
                                                                    <input type="hidden" name="sections[{{ $section->id }}][header][{{ $hKey }}]" value="{{ $hValue }}">
                                                                @endif
                                                                <input type="file" class="form-control" name="sections[{{ $section->id }}][header][{{ $hKey }}]" accept="image/*">
                                                            </div>
                                                        @else
                                                            <input type="text" class="form-control" name="sections[{{ $section->id }}][header][{{ $hKey }}]" value="{{ $hValue }}">
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Items List --}}
                                <div class="items-container" data-section-id="{{ $section->id }}">

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

                                                // Gallery Items: Sembunyikan Description
                                                if (str_contains($sectionKey, 'gallery_items') && $key === 'description') {
                                                    continue;
                                                }

                                                // Contact Info: Sembunyikan Description
                                                if (str_contains($sectionKey, 'contact_info') && $key === 'description') {
                                                    continue;
                                                }

                                                // Locations: Sembunyikan Title & Description
                                                if (str_contains($sectionKey, 'locations') && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Group visual related keys
                                                if(in_array($key, ['image', 'icon', 'color', 'icon_color', 'background_image'])) {
                                                    // Explicitly exclude image/icon for jadwal section
                                                    if(str_contains($sectionKey, 'jadwal')) { continue; }
                                                    // Explicitly exclude ONLY icon (upload) for stats section
                                                    if(str_contains($sectionKey, 'stats') && $key === 'icon') { continue; }
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

                                {{-- Template Item (Hidden) for JS Cloning --}}
                                @if($canAddRemove)
                                    <div class="item-template d-none" data-template-for="{{ $section->id }}">
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

                                                // Gallery Items: Sembunyikan Description
                                                if (str_contains($sectionKey, 'gallery_items') && $key === 'description') {
                                                    continue;
                                                }

                                                // Contact Info: Sembunyikan Description
                                                if (str_contains($sectionKey, 'contact_info') && $key === 'description') {
                                                    continue;
                                                }

                                                // Locations: Sembunyikan Title & Description
                                                if (str_contains($sectionKey, 'locations') && in_array($key, ['title', 'description'])) {
                                                    continue;
                                                }

                                                // Group visual related keys
                                                if(in_array($key, ['image', 'icon', 'color', 'icon_color', 'background_image'])) {
                                                    // Explicitly exclude image/icon for jadwal section
                                                    if(str_contains($sectionKey, 'jadwal')) { continue; }
                                                    // Explicitly exclude ONLY icon (upload) for stats section
                                                    if(str_contains($sectionKey, 'stats') && $key === 'icon') { continue; }
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
                                    </div>

                                    <button type="button" class="btn btn-outline-primary w-100 mt-3 add-item" data-section-id="{{ $section->id }}">
                                        <i class="bx bx-plus-circle me-1"></i> Tambah Item Baru
                                    </button>
                                @endif

                            {{-- Tipe Single/Text --}}
                            @else
                                <div class="row g-3">
                                    @foreach($section->content as $key => $value)
                                        @if(is_array($value)) @continue @endif
                                        @if($sectionKey === 'hero' && $key === 'image') @continue @endif
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">
                                                {{ ucwords(str_replace('_', ' ', $key)) }}
                                            </label>
                                            @if($key === 'image' || str_contains($key, 'image') || $key === 'icon')
                                                <div class="input-group">
                                                    @if($value && (str_contains($value, '/') || str_contains($value, '.')))
                                                        <input type="hidden" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value }}">
                                                        <span class="input-group-text p-0 overflow-hidden" style="width: 42px;">
                                                            <img src="{{ asset($value) }}" alt="Preview" class="w-100 h-100" style="object-fit: cover; min-height: 38px;" onerror="this.parentElement.style.display='none'">
                                                        </span>
                                                    @endif
                                                    <input type="file" class="form-control" name="sections[{{ $section->id }}][{{ $key }}]" accept="image/*">
                                                </div>
                                            @elseif(str_contains($key, 'description') || (is_string($value) && strlen($value) > 80))
                                                <textarea class="form-control" name="sections[{{ $section->id }}][{{ $key }}]" rows="3">{{ $value }}</textarea>
                                            @elseif(str_contains($key, 'color'))
                                                <div class="input-group">
                                                    <input type="color" class="form-control form-control-color" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value && substr($value, 0, 1) === '#' ? $value : '#566a7f' }}" style="max-width: 60px;">
                                                    <input type="text" class="form-control" name="sections[{{ $section->id }}][{{ $key }}_text]" value="{{ $value }}" placeholder="Hex Color or Class Name">
                                                </div>
                                            @else
                                                <input type="text" class="form-control" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value }}">
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                        </div>
                    </div>
                @endforeach

                {{-- Tombol Simpan (Mobile) --}}
                <div class="d-lg-none">
                    <button type="submit" class="btn btn-primary btn-lg w-100 mb-4">
                        <i class="bx bx-save me-1"></i> Simpan Semua Perubahan
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal Reset --}}
<div class="modal fade" id="resetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bx bx-error-circle text-warning" style="font-size: 4rem;"></i>
                <h5 class="mt-3 mb-2">Reset ke Default?</h5>
                <p class="text-muted mb-4">Semua perubahan akan hilang dan dikembalikan ke pengaturan awal.</p>
                <div class="d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <a href="{{ route('admin.landing-pages.reset', $landingPage->slug) }}" class="btn btn-danger">Reset</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    html { scroll-behavior: smooth; }
    
    .section-card { transition: box-shadow 0.3s ease; }
    .section-card:target,
    .section-card:focus-within { box-shadow: 0 0 0 3px rgba(105, 108, 255, 0.25); }
    
    .list-group-item.active { background-color: #696cff; border-color: #696cff; }
    .section-nav-link:not(.active):hover { background-color: rgba(105, 108, 255, 0.08); }
    
    .item-wrapper { transition: transform 0.2s ease; }
    .item-wrapper:hover { transform: translateX(4px); }
    
    .input-group-text img { border-radius: 0; }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ===== Event Delegation for Dynamic Content =====
    document.body.addEventListener('click', function(e) {
        
        // --- 1. Handle "Add Item" ---
        const addButton = e.target.closest('.add-item');
        if (addButton) {
            e.preventDefault();
            
            const sectionId = addButton.dataset.sectionId;
            const container = document.querySelector(`.items-container[data-section-id="${sectionId}"]`);
            const templateContainer = document.querySelector(`.item-template[data-template-for="${sectionId}"]`);
            
            if (!templateContainer || !container) {
                console.error('Template or container not found for section: ' + sectionId);
                return;
            }
            
            const templateCard = templateContainer.querySelector('.item-wrapper');
            if (!templateCard) return;
            
            // Clone template
            const clone = templateCard.cloneNode(true);
            
            // Calculate new index
            const index = container.querySelectorAll(':scope > .item-wrapper').length;
            
            // Show clone
            clone.classList.remove('d-none');
            
            // Update header number
            const headerSpan = clone.querySelector('.card-header .fw-bold');
            if (headerSpan) headerSpan.innerHTML = `Item #${index + 1}`;
            
            // Update input names
            clone.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/TEMPLATE_INDEX/g, index);
                }
                input.disabled = false;
            });
            
            // Append
            container.appendChild(clone);
            
            // Scroll to view
            clone.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        // --- 2. Handle "Remove Item" ---
        const removeButton = e.target.closest('.remove-item');
        if (removeButton) {
            e.preventDefault();
            
            if (confirm('Hapus item ini?')) {
                const wrapper = removeButton.closest('.item-wrapper');
                const container = wrapper ? wrapper.closest('.items-container') : null;
                
                if (wrapper) {
                    wrapper.style.transition = 'all 0.3s ease';
                    wrapper.style.transform = 'translateX(-100%)';
                    wrapper.style.opacity = '0';
                    setTimeout(() => {
                        wrapper.remove();
                        if (container) reindexItems(container);
                    }, 300);
                }
            }
            return;
        }
    });

    /**
     * Re-index all items in a container so their name attributes
     * use sequential indices (0, 1, 2...) with no gaps.
     */
    function reindexItems(container) {
        const items = container.querySelectorAll(':scope > .item-wrapper');
        items.forEach((item, newIndex) => {
            const headerSpan = item.querySelector('.card-header .fw-bold');
            if (headerSpan) headerSpan.innerHTML = `Item #${newIndex + 1}`;

            item.querySelectorAll('input, textarea, select').forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(
                        /\[items\]\[\d+\]/g,
                        `[items][${newIndex}]`
                    );
                }
            });
        });
    }

    // ===== Active navigation on scroll =====
    const sections = document.querySelectorAll('.section-card');
    const navLinks = document.querySelectorAll('.section-nav-link');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                navLinks.forEach(link => link.classList.remove('active'));
                const activeLink = document.querySelector(`.section-nav-link[href="#${entry.target.id}"]`);
                if (activeLink) activeLink.classList.add('active');
            }
        });
    }, { rootMargin: '-100px 0px -60% 0px' });
    
    sections.forEach(section => observer.observe(section));
});
</script>
@endsection
