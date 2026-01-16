@extends('layouts.sneat')

@section('title', 'Edit ' . $landingPage->title)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@php
    // Field descriptions for better UX
    $fieldDescriptions = [
        'badge' => 'Teks kecil yang muncul di atas judul utama',
        'title' => 'Judul utama section',
        'title_1' => 'Bagian pertama judul',
        'title_2' => 'Bagian kedua judul', 
        'title_highlight_1' => 'Teks yang di-highlight (warna kuning)',
        'title_highlight_2' => 'Teks highlight kedua',
        'description' => 'Deskripsi atau paragraf penjelasan',
        'description_1' => 'Paragraf pertama',
        'description_2' => 'Paragraf kedua',
        'button_text' => 'Teks yang muncul di tombol',
        'button_link' => 'URL tujuan saat tombol diklik (contoh: /program-inklusi)',
        'image' => 'Gambar/foto profil',
        'background_image' => 'Gambar latar belakang section',
        'experience_years' => 'Jumlah tahun pengalaman (contoh: 14+)',
        'active_students' => 'Jumlah siswa aktif (contoh: 200+)',
        'value' => 'Angka atau nilai statistik (contoh: 200+, 98%)',
        'label' => 'Label/keterangan untuk nilai statistik',
        'icon_color' => 'Warna ikon (primary, secondary, accent-yellow, accent-orange)',
        'link' => 'URL halaman tujuan',
        'color' => 'Warna tema card (primary, secondary, accent-orange, accent-yellow)',
        'features' => 'Daftar fitur/keunggulan',
        'name' => 'Nama lengkap beserta gelar',
        'position' => 'Jabatan/posisi (contoh: Guru Bahasa Indonesia)',
        'category' => 'Kategori/role (guru, terapis, psikolog)',
        'department' => 'Bidang/departemen (contoh: Bidang Kesetaraan)',
    ];
@endphp

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Manajemen Landing Page /</span> Edit {{ $landingPage->title }}
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Konten Halaman</h5>
                    <a href="{{ route('admin.landing-pages.index') }}" class="btn btn-secondary">Kembali</a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.landing-pages.update', $landingPage->slug) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="nav-align-top mb-4">
                            <ul class="nav nav-tabs" role="tablist">
                                @foreach($landingPage->sections as $index => $section)
                                <li class="nav-item">
                                    <button type="button" class="nav-link {{ $index === 0 ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-{{ $section->section_key }}">
                                        {{ ucwords(str_replace('_', ' ', $section->section_key)) }}
                                    </button>
                                </li>
                                @endforeach
                            </ul>
                            <div class="tab-content">
                                @foreach($landingPage->sections as $index => $section)
                                <div class="tab-pane fade {{ $index === 0 ? 'show active' : '' }}" id="navs-top-{{ $section->section_key }}" role="tabpanel">
                                    
                                    <input type="hidden" name="sections[{{ $section->id }}][type]" value="{{ $section->type }}">

                                    {{-- Handle List Type (Repeaters) --}}
                                    @if($section->type === 'list')
                                        <div class="alert alert-info">
                                            <i class="bx bx-info-circle"></i> Bagian ini adalah list (daftar item). Anda dapat mengedit item yang ada.
                                        </div>
                                        
                                        @php
                                            // Handle both structures: direct array OR {items: [...]}
                                            $items = [];
                                            $header = [];
                                            $content = $section->content;
                                            
                                            if (isset($content['items']) && is_array($content['items'])) {
                                                $items = $content['items'];
                                                $header = $content['header'] ?? [];
                                            } elseif (is_array($content) && !empty($content) && isset($content[0])) {
                                                // Direct array structure (like Stats)
                                                $items = $content;
                                            }
                                        @endphp

                                        {{-- Render header fields if exists --}}
                                        @if(!empty($header))
                                            <div class="border p-3 rounded mb-4 bg-white">
                                                <h6 class="fw-bold text-primary mb-3"><i class="bx bx-heading"></i> Header Section</h6>
                                                <div class="row">
                                                    @foreach($header as $key => $value)
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label text-capitalize fw-semibold">
                                                                {{ str_replace('_', ' ', $key) }}
                                                                @if(isset($fieldDescriptions[$key]))
                                                                    <small class="text-muted d-block fw-normal">{{ $fieldDescriptions[$key] }}</small>
                                                                @endif
                                                            </label>
                                                            <input type="text" class="form-control" name="sections[{{ $section->id }}][header][{{ $key }}]" value="{{ $value }}">
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Render items --}}
                                        @foreach($items as $itemIndex => $item)
                                            <div class="border p-3 rounded mb-3 bg-light list-item-container">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <h6 class="fw-bold mb-0"><i class="bx bx-list-ul"></i> Item #{{ $itemIndex + 1 }}</h6>
                                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item-btn" onclick="removeItem(this)">
                                                        <i class="bx bx-trash"></i> Hapus
                                                    </button>
                                                </div>
                                                <div class="row">
                                                    @foreach($item as $key => $value)
                                                        <div class="col-md-6 mb-3">
                                                            <label class="form-label text-capitalize fw-semibold">
                                                                {{ str_replace('_', ' ', $key) }}
                                                                @if(isset($fieldDescriptions[$key]))
                                                                    <small class="text-muted d-block fw-normal">{{ $fieldDescriptions[$key] }}</small>
                                                                @endif
                                                            </label>
                                                            @if($key === 'image' || str_contains($key, 'image'))
                                                                <div class="d-flex align-items-center gap-3">
                                                                    @if($value)
                                                                        <img src="{{ asset($value) }}" alt="Preview" class="d-block rounded" height="50" width="50" style="object-fit: cover">
                                                                    @endif
                                                                    <input type="hidden" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}">
                                                                    <input type="file" class="form-control" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]">
                                                                </div>
                                                            @elseif($key === 'description' || strlen($value ?? '') > 100)
                                                                <textarea class="form-control" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" rows="2">{{ $value }}</textarea>
                                                            @else
                                                                <input type="text" class="form-control" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}">
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach

                                        {{-- Add New Item Button - Only for simple sections --}}
                                        @php
                                            // Complex sections with icons/special styling should NOT have Add button
                                            $complexSections = ['programs', 'kurikulum', 'fasilitas', 'services', 'therapy_types', 'coordinators'];
                                            $canAddItems = !in_array($section->section_key, $complexSections);
                                        @endphp
                                        
                                        @if($canAddItems)
                                        <div class="mt-3">
                                            <button type="button" class="btn btn-outline-success btn-sm add-item-btn" 
                                                data-section-id="{{ $section->id }}"
                                                data-item-count="{{ count($items) }}"
                                                data-item-keys="{{ json_encode(array_keys($items[0] ?? [])) }}">
                                                <i class="bx bx-plus"></i> Tambah Item Baru
                                            </button>
                                        </div>
                                        <div id="new-items-container-{{ $section->id }}"></div>
                                        @else
                                        <div class="mt-3">
                                            <small class="text-muted">
                                                <i class="bx bx-info-circle"></i> 
                                                Section ini memiliki template khusus. Gunakan tombol "Reset to Default" untuk mengembalikan item yang sudah dihapus.
                                            </small>
                                        </div>
                                        @endif

                                    @else
                                        {{-- Handle Text/Rich Text/Image (Key-Value) --}}
                                        <div class="row">
                                            @foreach($section->content as $key => $value)
                                                <div class="col-md-12 mb-3">
                                                    <label class="form-label text-capitalize fw-semibold">
                                                        {{ str_replace(['_', '1', '2'], [' ', ' 1', ' 2'], $key) }}
                                                        @if(isset($fieldDescriptions[$key]))
                                                            <small class="text-muted d-block fw-normal">{{ $fieldDescriptions[$key] }}</small>
                                                        @endif
                                                    </label>
                                                    
                                                    @if($key === 'image' || str_contains($key, 'image'))
                                                        {{-- Image Input --}}
                                                        <input type="hidden" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value }}">
                                                        <div class="d-flex align-items-start gap-4">
                                                            @if($value)
                                                                <img src="{{ asset($value) }}" alt="Preview" class="d-block rounded" height="100" width="100" id="uploadedAvatar-{{ $section->id }}-{{ $key }}" style="object-fit: cover" />
                                                            @endif
                                                            <div class="button-wrapper">
                                                                <label for="upload-{{ $section->id }}-{{ $key }}" class="btn btn-primary me-2 mb-4" tabindex="0">
                                                                    <span class="d-none d-sm-block">Upload gambar baru</span>
                                                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                                                    <input type="file" id="upload-{{ $section->id }}-{{ $key }}" class="account-file-input" hidden accept="image/png, image/jpeg" name="sections[{{ $section->id }}][{{ $key }}]" />
                                                                </label>
                                                                <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 5MB</p>
                                                            </div>
                                                        </div>

                                                    @elseif($section->type === 'rich_text' && (str_contains($key, 'description') || str_contains($key, 'content')))
                                                        {{-- Textarea --}}
                                                        <textarea class="form-control" name="sections[{{ $section->id }}][{{ $key }}]" rows="5">{{ $value }}</textarea>
                                                    
                                                    @elseif(is_array($value))
                                                        {{-- Handle Array (e.g. features list) --}}
                                                         <div class="border p-2 rounded">
                                                            @foreach($value as $subKey => $subValue)
                                                                <div class="input-group mb-2">
                                                                    <span class="input-group-text">{{ $subKey + 1 }}</span>
                                                                    <input type="text" class="form-control" name="sections[{{ $section->id }}][{{ $key }}][]" value="{{ $subValue }}">
                                                                </div>
                                                            @endforeach
                                                         </div>

                                                    @else
                                                        {{-- Standard Text Input --}}
                                                        <input type="text" class="form-control" name="sections[{{ $section->id }}][{{ $key }}]" value="{{ $value }}">
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Simpan Perubahan</button>
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#resetModal">
                                <i class="bx bx-reset"></i> Reset to Default
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<div class="modal fade" id="resetModal" tabindex="-1" aria-labelledby="resetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="resetModalLabel">
                    <i class="bx bx-error-circle"></i> Konfirmasi Reset
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bx bx-refresh bx-spin" style="font-size: 4rem; color: #dc3545;"></i>
                </div>
                <p class="text-center fw-semibold">Apakah Anda yakin ingin mereset halaman ini?</p>
                <p class="text-center text-muted">Tindakan ini akan mengembalikan <strong>SEMUA konten</strong> halaman "{{ $landingPage->title }}" ke pengaturan awal (default). Perubahan yang sudah Anda buat akan hilang.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="{{ route('admin.landing-pages.reset', $landingPage->slug) }}" class="btn btn-danger">
                    <i class="bx bx-check"></i> Ya, Reset Sekarang
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Function to handle file input change and preview
        function handleImagePreview(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Look for existing preview image in parent container
                    const container = input.closest('.d-flex') || input.closest('.col-md-6') || input.parentNode;
                    let preview = container.querySelector('img');
                    
                    if (preview) {
                        // Update existing preview
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    } else {
                        // Create new preview image
                        const newPreview = document.createElement('img');
                        newPreview.src = e.target.result;
                        newPreview.className = 'd-block rounded mb-2';
                        newPreview.style.height = '60px';
                        newPreview.style.width = '60px';
                        newPreview.style.objectFit = 'cover';
                        // Insert before the file input
                        input.parentNode.insertBefore(newPreview, input);
                    }
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Attach event listeners to all file inputs (including dynamically added ones)
        document.addEventListener('change', function(e) {
            if (e.target && e.target.type === 'file') {
                handleImagePreview(e.target);
            }
        });

        // Handle Add New Item buttons
        document.querySelectorAll('.add-item-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const sectionId = this.dataset.sectionId;
                let itemCount = parseInt(this.dataset.itemCount);
                const keys = JSON.parse(this.dataset.itemKeys);
                const container = document.getElementById('new-items-container-' + sectionId);

                // Create new item HTML
                const newItemDiv = document.createElement('div');
                newItemDiv.className = 'border p-3 rounded mb-3 bg-light mt-3 list-item-container';
                newItemDiv.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0 text-success"><i class="bx bx-plus-circle"></i> Item Baru #${itemCount + 1}</h6>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeItem(this)">
                            <i class="bx bx-trash"></i> Hapus
                        </button>
                    </div>
                    <div class="row">
                        ${keys.map(key => {
                            const label = key.replace(/_/g, ' ');
                            const isImage = key === 'image' || key.includes('image');
                            return `
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-capitalize fw-semibold">${label}</label>
                                    ${isImage ? 
                                        `<input type="file" class="form-control" name="sections[${sectionId}][items][${itemCount}][${key}]">` :
                                        `<input type="text" class="form-control" name="sections[${sectionId}][items][${itemCount}][${key}]" value="">`
                                    }
                                </div>
                            `;
                        }).join('')}
                    </div>
                `;

                container.appendChild(newItemDiv);
                
                // Update item count
                this.dataset.itemCount = itemCount + 1;
            });
        });
    });

    // Global function to remove any item (existing or new) - MUST be outside DOMContentLoaded
    function removeItem(button) {
        const itemContainer = button.closest('.list-item-container');
        if (itemContainer) {
            itemContainer.remove();
        }
    }
</script>
@endsection
