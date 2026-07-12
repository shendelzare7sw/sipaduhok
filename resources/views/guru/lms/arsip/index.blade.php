@extends('layouts.sneat')

@section('title', 'Arsip LMS')
@section('page-title', 'Arsip LMS')
@section('page-subtitle', 'Materi, tugas, latihan, dan ujian Anda lintas tahun ajaran')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/guru/lms/arsip/index.css'])
@endsection

@section('content')
<div class="guru-lms-arsip-index-page">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-3">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-archive me-2 text-primary"></i>Arsip LMS</h4>
            <p class="text-muted mb-0">Lihat semua materi, tugas, latihan, dan ujian yang pernah Anda buat lintas tahun ajaran. Salin ke kelas aktif untuk dipakai ulang.</p>
        </div>
    </div>

    @if($kelasMapelTujuan->isEmpty())
        <div class="alert alert-warning d-flex align-items-start">
            <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
            <div>
                <strong>Anda belum ditugaskan mengajar di TA aktif.</strong> Anda masih bisa melihat arsip, tapi tombol "Salin ke Kelas Aktif" akan dinonaktifkan sampai admin assign Anda ke kelas+mapel di TA yang sedang berjalan.
            </div>
        </div>
    @endif

    {{-- Summary --}}
    <div class="arsip-summary">
        <div class="arsip-stat">
            <div class="icon-circle icon-circle--materi"><i class="fas fa-book-open"></i></div>
            <div><div class="label">Materi</div><div class="value">{{ $arsip['materi']->count() }}</div></div>
        </div>
        <div class="arsip-stat">
            <div class="icon-circle icon-circle--tugas"><i class="fas fa-tasks"></i></div>
            <div><div class="label">Tugas</div><div class="value">{{ $arsip['tugas']->count() }}</div></div>
        </div>
        <div class="arsip-stat">
            <div class="icon-circle icon-circle--latihan"><i class="fas fa-pencil-ruler"></i></div>
            <div><div class="label">Latihan</div><div class="value">{{ $arsip['latihan']->count() }}</div></div>
        </div>
        <div class="arsip-stat">
            <div class="icon-circle icon-circle--ujian"><i class="fas fa-file-alt"></i></div>
            <div><div class="label">Ujian</div><div class="value">{{ $arsip['ujian']->count() }}</div></div>
        </div>
    </div>

    {{-- Type tabs --}}
    <div class="type-tabs">
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->except('type'), [])) }}"
           class="type-tab {{ empty($filters['type']) ? 'active' : '' }}">Semua</a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'materi'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'materi' ? 'active' : '' }}">
            <i class="fas fa-book-open me-1"></i>Materi
        </a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'tugas'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'tugas' ? 'active' : '' }}">
            <i class="fas fa-tasks me-1"></i>Tugas
        </a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'latihan'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'latihan' ? 'active' : '' }}">
            <i class="fas fa-pencil-ruler me-1"></i>Latihan
        </a>
        <a href="{{ route('guru.lms.arsip.index', array_merge(request()->all(), ['type' => 'ujian'])) }}"
           class="type-tab {{ ($filters['type'] ?? null) === 'ujian' ? 'active' : '' }}">
            <i class="fas fa-file-alt me-1"></i>Ujian
        </a>
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('guru.lms.arsip.index') }}" class="filter-bar">
        @if($filters['type'])
            <input type="hidden" name="type" value="{{ $filters['type'] }}">
        @endif

        <div class="form-group">
            <label>Tahun Ajaran</label>
            <select name="tahun_ajaran_id" class="form-select form-select-sm">
                <option value="">Semua TA</option>
                @foreach($tahunAjarans as $ta)
                    <option value="{{ $ta->id }}" @selected(($filters['tahun_ajaran_id'] ?? null) == $ta->id)>
                        {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Mata Pelajaran</label>
            <select name="mapel_id" class="form-select form-select-sm">
                <option value="">Semua Mapel</option>
                @foreach($mataPelajarans as $mp)
                    <option value="{{ $mp->id }}" @selected(($filters['mapel_id'] ?? null) == $mp->id)>
                        {{ $mp->nama_mapel }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label>Cari Judul</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                class="form-control form-control-sm" placeholder="kata kunci...">
        </div>

        <div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-filter me-1"></i>Filter
            </button>
            <a href="{{ route('guru.lms.arsip.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
        </div>
    </form>

    @if($totalKonten === 0)
        <div class="empty-state-arsip">
            <i class="fas fa-archive"></i>
            <h5 class="fw-bold mb-1">Belum Ada Konten</h5>
            <p class="mb-0">Anda belum pernah membuat materi/tugas/ujian, atau filter terlalu sempit.</p>
        </div>
    @else
        <form id="bulkSalinForm" method="POST" action="{{ route('guru.lms.arsip.salin-bulk') }}">
            @csrf
            {{-- Pertahankan filter aktif setelah salin (redirect balik ke arsip) --}}
            @if($filters['type'])<input type="hidden" name="type" value="{{ $filters['type'] }}">@endif
            @if($filters['tahun_ajaran_id'] ?? null)<input type="hidden" name="tahun_ajaran_id" value="{{ $filters['tahun_ajaran_id'] }}">@endif
            @if($filters['mapel_id'] ?? null)<input type="hidden" name="mapel_id" value="{{ $filters['mapel_id'] }}">@endif
            @if($filters['search'] ?? null)<input type="hidden" name="search" value="{{ $filters['search'] }}">@endif

        @foreach(['materi' => ['fa-book-open', 'Materi'],
                  'tugas' => ['fa-tasks', 'Tugas'],
                  'latihan' => ['fa-pencil-ruler', 'Latihan'],
                  'ujian' => ['fa-file-alt', 'Ujian']] as $sectionKey => $cfg)
            @if($arsip[$sectionKey]->isNotEmpty())
                <div class="arsip-section">
                    <div class="arsip-section-title">
                        <span class="section-icon section-icon--{{ $sectionKey }}"><i class="fas {{ $cfg[0] }}"></i></span>
                        {{ $cfg[1] }}
                        <span class="badge-count">{{ $arsip[$sectionKey]->count() }}</span>
                    </div>
                    <div class="arsip-grid">
                        @foreach($arsip[$sectionKey] as $item)
                            @php
                                $judul = match($sectionKey) {
                                    'materi' => $item->judul_materi,
                                    'tugas' => $item->judul_tugas,
                                    default => $item->judul_ujian,
                                };
                                $tanggal = match($sectionKey) {
                                    'materi' => $item->tanggal_upload,
                                    default => $item->tanggal_mulai,
                                };
                                $isAktif = $item->kelas?->tahunAjaran?->is_active;
                            @endphp
                            <div class="arsip-card">
                                @if($kelasMapelTujuan->isNotEmpty())
                                    <input type="checkbox" class="arsip-check" name="items[]"
                                           value="{{ $sectionKey }}:{{ $item->id }}"
                                           title="Pilih untuk salin massal">
                                @endif
                                <span class="badge-ta">
                                    {{ $item->kelas?->tahunAjaran?->nama_tahun_ajaran ?? 'TA -' }}
                                    @if($isAktif) - Aktif @endif
                                </span>
                                <div class="card-header-row">
                                    <div class="icon-square icon-square--{{ $sectionKey }}">
                                        <i class="fas {{ $cfg[0] }}"></i>
                                    </div>
                                    <div class="judul">{{ $judul }}</div>
                                </div>
                                <div class="meta">
                                    <div class="meta-row"><i class="fas fa-school"></i>{{ $item->kelas?->nama_kelas ?? '-' }}</div>
                                    <div class="meta-row"><i class="fas fa-book"></i>{{ $item->mataPelajaran?->nama_mapel ?? '-' }}</div>
                                    @if($tanggal)
                                        <div class="meta-row"><i class="fas fa-calendar"></i>{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y') }}</div>
                                    @endif
                                    @if($sectionKey === 'ujian' || $sectionKey === 'latihan')
                                        @php $tipeLabel = method_exists($item, 'getTipeLabelAttribute') ? $item->tipe_label : $item->tipe_ujian; @endphp
                                        <div class="meta-row"><i class="fas fa-tag"></i>{{ $tipeLabel }}</div>
                                    @endif
                                </div>
                                <div class="arsip-actions">
                                    <a href="{{ route('guru.lms.arsip.preview', [$sectionKey, $item->id]) }}"
                                       class="btn-arsip" target="_blank">
                                        <i class="fas fa-eye me-1"></i>Preview
                                    </a>
                                    @if($kelasMapelTujuan->isNotEmpty())
                                        <a href="{{ route('guru.lms.arsip.form-salin', [$sectionKey, $item->id]) }}"
                                           class="btn-arsip btn-arsip-salin">
                                            <i class="fas fa-copy me-1"></i>Salin
                                        </a>
                                    @else
                                        <button type="button" class="btn-arsip" disabled
                                                title="Anda belum ditugaskan ke kelas TA aktif">
                                            <i class="fas fa-ban me-1"></i>Salin
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach

        @if($kelasMapelTujuan->isNotEmpty())
            <div class="bulk-salin-bar" id="bulkBar" hidden>
                <div class="bulk-info">
                    <i class="fas fa-check-square me-1 text-primary"></i><span id="bulkCount">0</span> item dipilih
                </div>
                <div class="bulk-fields">
                    <div class="bulk-target">
                        <label class="bulk-label mb-0 small fw-bold">Tujuan:</label>
                        <select id="bulkTujuan" class="form-select form-select-sm bulk-select">
                            @foreach($kelasMapelTujuan as $t)
                                <option value="{{ $t['kelas_id'] }}|{{ $t['mata_pelajaran_id'] }}">
                                    {{ $t['kelas']?->nama_kelas ?? '-' }} — {{ $t['mata_pelajaran']?->nama_mapel ?? '-' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <label class="bulk-soal small">
                        <input type="checkbox" name="sertakan_soal" value="1" checked> Sertakan soal
                    </label>
                    <input type="hidden" name="kelas_id" id="bulkKelasId">
                    <input type="hidden" name="mata_pelajaran_id" id="bulkMapelId">
                    <div class="bulk-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" id="bulkCancel">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-copy me-1"></i>Salin <span id="bulkCountBtn">0</span> item
                        </button>
                    </div>
                </div>
            </div>
        @endif
        </form>
    @endif
</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/arsip/index.js'])
@endpush
