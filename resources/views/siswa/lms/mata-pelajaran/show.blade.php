@extends('layouts.lms')

@section('title', $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Materi, Tugas, dan Ujian')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/siswa/lms/mata-pelajaran/show.js'])
@endpush

@section('content')
<div class="siswa-lms-mapel-show-page">
<!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard LMS
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item active">
            <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
        </div>
    </div>

    <!-- Header Info -->
    <div class="section-card mapel-hero-card">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mapel-title">
                    <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
                </h2>
                <p class="mapel-meta">
                    Kode Mapel: {{ $mataPelajaran->kode_mapel }} | Jenjang: {{ $mataPelajaran->jenjang }}
                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="mapel-icon-box">
                    <i class="fas fa-graduation-cap fa-2x"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. MATERI -->
    <div class="section-card">
        <h3 class="section-heading">
            <i class="fas fa-file-alt"></i> Materi Pembelajaran
        </h3>

        @php
            $materiByDate = [];
            foreach($materiList as $materi) {
                $dateKey = $materi->tanggal_upload->format('Y-m-d');
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');

                if($dateKey === $today) {
                    $label = 'Hari Ini (' . $materi->tanggal_upload->translatedFormat('d M Y') . ')';
                } elseif($dateKey === $yesterday) {
                    $label = 'Kemarin (' . $materi->tanggal_upload->translatedFormat('d M Y') . ')';
                } else {
                    $label = $materi->tanggal_upload->translatedFormat('d M Y');
                }

                if(!isset($materiByDate[$dateKey])) {
                    $materiByDate[$dateKey] = ['label' => $label, 'items' => []];
                }
                $materiByDate[$dateKey]['items'][] = $materi;
            }
        @endphp

        @if(count($materiByDate) > 0)
            @foreach($materiByDate as $dateKey => $data)
                @php $isToday = strpos($data['label'], 'Hari Ini') === 0; @endphp
                <div class="date-group-header">
                    <span>{{ $data['label'] }} ({{ count($data['items']) }})</span>
                    <i class="fas fa-chevron-down date-group-icon {{ $isToday ? 'open' : '' }}"></i>
                </div>
                <div class="date-group-content {{ $isToday ? 'show' : '' }}">
                    @foreach($data['items'] as $materi)
                        <div class="item-list">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="item-content">
                                    <h5 class="item-title">
                                        {{ $materi->judul_materi }}
                                        @if($materi->created_at->diffInDays(now()) < 3)
                                            <span class="badge-new">BARU</span>
                                        @endif
                                    </h5>
                                    <p class="item-description">
                                        {{ Str::limit($materi->deskripsi, 150) }}
                                    </p>
                                    <small class="item-meta">
                                        <i class="fas fa-file me-1"></i> {{ strtoupper($materi->tipe_file ?? 'File') }}
                                        <span class="mx-2">-</span>
                                        <i class="far fa-clock me-1"></i> {{ $materi->created_at->format('H:i') }}
                                    </small>
                                </div>
                                <div>
                                    <a href="{{ route('siswa.lms.mapel.materi', [$mataPelajaran->id, $materi->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-eye"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="no-items-date">
                <i class="fas fa-folder-open fa-2x mb-2"></i>
                <p class="empty-text">Belum ada materi yang tersedia</p>
            </div>
        @endif
    </div>

    <!-- 2. TUGAS -->
    <div class="section-card">
        <h3 class="section-heading">
            <i class="fas fa-tasks"></i> Tugas
        </h3>

        @php
            $tugasByDate = [];
            foreach($tugasList as $tugas) {
                $dateKey = $tugas->tanggal_deadline->format('Y-m-d');
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');

                if($dateKey === $today) {
                    $label = 'Hari Ini (' . $tugas->tanggal_deadline->translatedFormat('d M Y') . ')';
                } elseif($dateKey === $yesterday) {
                    $label = 'Kemarin (' . $tugas->tanggal_deadline->translatedFormat('d M Y') . ')';
                } else {
                    $label = $tugas->tanggal_deadline->translatedFormat('d M Y');
                }

                if(!isset($tugasByDate[$dateKey])) {
                    $tugasByDate[$dateKey] = ['label' => $label, 'items' => []];
                }
                $tugasByDate[$dateKey]['items'][] = $tugas;
            }
        @endphp

        @if(count($tugasByDate) > 0)
            @foreach($tugasByDate as $dateKey => $data)
                @php $isToday = strpos($data['label'], 'Hari Ini') === 0; @endphp
                <div class="date-group-header">
                    <span>{{ $data['label'] }} ({{ count($data['items']) }})</span>
                    <i class="fas fa-chevron-down date-group-icon {{ $isToday ? 'open' : '' }}"></i>
                </div>
                <div class="date-group-content {{ $isToday ? 'show' : '' }}">
                    @foreach($data['items'] as $tugas)
                        <div class="item-list">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="item-content">
                                    <h5 class="item-title">
                                        {{ $tugas->judul_tugas }}
                                        @if($tugas->tanggal_deadline->isFuture())
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Ditutup</span>
                                        @endif
                                    </h5>
                                    <p class="item-description">
                                        {{ Str::limit($tugas->deskripsi, 150) }}
                                    </p>
                                    <small class="item-meta">
                                        <i class="fas fa-clock"></i>
                                        {{ $tugas->tanggal_deadline->format('H:i') }}
                                        @if($tugas->tanggal_deadline->isFuture())
                                            <span class="deadline-safe-text">({{ $tugas->tanggal_deadline->copy()->locale('id')->diffForHumans() }})</span>
                                        @else
                                            <span class="deadline-expired-text">(Sudah Lewat)</span>
                                        @endif
                                    </small>
                                </div>
                                <div>
                                    <a href="{{ route('siswa.lms.mapel.tugas.show', [$mataPelajaran->id, $tugas->id]) }}"
                                        class="btn btn-primary btn-sm">
                                        <i class="fas fa-pencil-alt"></i> Kerjakan
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="no-items-date">
                <i class="fas fa-clipboard-list fa-2x mb-2"></i>
                <p class="empty-text">Belum ada tugas yang tersedia</p>
            </div>
        @endif
    </div>

    <!-- 3. LATIHAN -->
    <div class="section-card">
        <h3 class="section-heading">
            <i class="fas fa-pencil-ruler"></i> Latihan
        </h3>

        @php
            $latihanList = \App\Models\Ujian::where('tipe_ujian', 'latihan')
                ->where('mata_pelajaran_id', $mataPelajaran->id)
                ->where('kelas_id', $siswa->kelas_id)
                ->orderBy('tanggal_mulai', 'desc')
                ->get();

            $latihanByDate = [];
            foreach($latihanList as $latihan) {
                $dateKey = $latihan->tanggal_mulai->format('Y-m-d');
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');

                if($dateKey === $today) {
                    $label = 'Hari Ini (' . $latihan->tanggal_mulai->translatedFormat('d M Y') . ')';
                } elseif($dateKey === $yesterday) {
                    $label = 'Kemarin (' . $latihan->tanggal_mulai->translatedFormat('d M Y') . ')';
                } else {
                    $label = $latihan->tanggal_mulai->translatedFormat('d M Y');
                }

                if(!isset($latihanByDate[$dateKey])) {
                    $latihanByDate[$dateKey] = ['label' => $label, 'items' => []];
                }
                $latihanByDate[$dateKey]['items'][] = $latihan;
            }
        @endphp

        @if(count($latihanByDate) > 0)
            @foreach($latihanByDate as $dateKey => $data)
                @php $isToday = strpos($data['label'], 'Hari Ini') === 0; @endphp
                <div class="date-group-header">
                    <span>{{ $data['label'] }} ({{ count($data['items']) }})</span>
                    <i class="fas fa-chevron-down date-group-icon {{ $isToday ? 'open' : '' }}"></i>
                </div>
                <div class="date-group-content {{ $isToday ? 'show' : '' }}">
                    @foreach($data['items'] as $latihan)
                        <div class="item-list">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="item-content">
                                    <h5 class="item-title">
                                        {{ $latihan->judul_ujian }}
                                        <span class="badge content-type-badge-latihan">
                                            Latihan
                                        </span>
                                    </h5>
                                    <p class="item-description">
                                        {{ $latihan->deskripsi ?? 'Latihan pembelajaran interaktif' }}
                                    </p>
                                    <small class="item-meta">
                                        <i class="fas fa-stopwatch"></i> Durasi:
                                        {{ $latihan->durasi_menit > 0 ? $latihan->durasi_menit . ' menit' : 'Tidak terbatas' }}
                                        | <i class="fas fa-clock"></i> {{ $latihan->tanggal_mulai->format('H:i') }} - {{ $latihan->tanggal_selesai->format('H:i') }}
                                    </small>
                                </div>
                                <div>
                                    @if(!$latihan->is_active)
                                        <button class="btn btn-secondary btn-sm btn-disabled-muted" disabled>
                                            <i class="fas fa-lock me-1"></i> Belum Dirilis
                                        </button>
                                    @elseif($latihan->isOngoing())
                                        <a href="{{ route('siswa.lms.mapel.latihan.show', [$mataPelajaran->id, $latihan->id]) }}"
                                            class="btn btn-warning btn-sm">
                                            <i class="fas fa-play"></i> Mulai Latihan
                                        </a>
                                    @elseif($latihan->tanggal_mulai->isFuture())
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-lock"></i> Belum Dimulai
                                        </button>
                                    @else
                                        @if($latihan->tampilkan_nilai)
                                            <a href="{{ route('siswa.lms.mapel.latihan.show', [$mataPelajaran->id, $latihan->id]) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-poll"></i> Lihat Hasil
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="no-items-date">
                <i class="fas fa-pencil-ruler fa-2x mb-2"></i>
                <p class="empty-text">Belum ada latihan yang tersedia</p>
            </div>
        @endif
    </div>

    <!-- 4. UJIAN -->
    <div class="section-card">
        <h3 class="section-heading">
            <i class="fas fa-file-signature"></i> Ujian
        </h3>

        @php
            $ujianByDate = [];
            foreach($ujianList as $ujian) {
                $dateKey = $ujian->tanggal_mulai->format('Y-m-d');
                $today = now()->format('Y-m-d');
                $yesterday = now()->subDay()->format('Y-m-d');

                if($dateKey === $today) {
                    $label = 'Hari Ini (' . $ujian->tanggal_mulai->translatedFormat('d M Y') . ')';
                } elseif($dateKey === $yesterday) {
                    $label = 'Kemarin (' . $ujian->tanggal_mulai->translatedFormat('d M Y') . ')';
                } else {
                    $label = $ujian->tanggal_mulai->translatedFormat('d M Y');
                }

                if(!isset($ujianByDate[$dateKey])) {
                    $ujianByDate[$dateKey] = ['label' => $label, 'items' => []];
                }
                $ujianByDate[$dateKey]['items'][] = $ujian;
            }
        @endphp

        @if(count($ujianByDate) > 0)
            @foreach($ujianByDate as $dateKey => $data)
                @php $isToday = strpos($data['label'], 'Hari Ini') === 0; @endphp
                <div class="date-group-header">
                    <span>{{ $data['label'] }} ({{ count($data['items']) }})</span>
                    <i class="fas fa-chevron-down date-group-icon {{ $isToday ? 'open' : '' }}"></i>
                </div>
                <div class="date-group-content {{ $isToday ? 'show' : '' }}">
                    @foreach($data['items'] as $ujian)
                        <div class="item-list">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="item-content">
                                    <h5 class="item-title">
                                        {{ $ujian->judul_ujian }}
                                        <span class="badge content-type-badge-ujian">
                                            {{ $ujian->tipe_label }}
                                        </span>
                                    </h5>
                                    <p class="item-description">
                                        {{ $ujian->deskripsi ?? 'Ujian ' . $ujian->tipe_label }}
                                    </p>
                                    <small class="item-meta">
                                        <i class="fas fa-stopwatch"></i> Durasi:
                                        {{ $ujian->durasi_menit > 0 ? $ujian->durasi_menit . ' menit' : 'Tidak terbatas' }}
                                        | <i class="fas fa-clock"></i> {{ $ujian->tanggal_mulai->format('H:i') }} - {{ $ujian->tanggal_selesai->format('H:i') }}
                                    </small>
                                </div>
                                <div>
                                    @if(!$ujian->is_active)
                                        <button class="btn btn-secondary btn-sm btn-disabled-muted" disabled>
                                            <i class="fas fa-lock me-1"></i> Belum Dirilis
                                        </button>
                                    @elseif($ujian->isOngoing())
                                        @php $aksesService = app(\App\Services\ValidasiAksesService::class); @endphp
                                        @if($ujian->requiresValidation() && (!$aksesService->cekAksesUjian($siswa) || !$siswa->validasi_ujian_wali))
                                            <button class="btn btn-secondary btn-sm btn-disabled-muted" disabled>
                                                <i class="fas fa-lock me-1"></i> Belum Memiliki Akses
                                            </button>
                                        @else
                                            <a href="{{ route('siswa.lms.mapel.ujian.show', [$mataPelajaran->id, $ujian->id]) }}"
                                                class="btn btn-danger btn-sm">
                                                <i class="fas fa-play"></i> Mulai Ujian
                                            </a>
                                        @endif
                                    @elseif($ujian->tanggal_mulai->isFuture())
                                        <button class="btn btn-secondary btn-sm" disabled>
                                            <i class="fas fa-lock"></i> Belum Dimulai
                                        </button>
                                    @else
                                        @if($ujian->tampilkan_nilai)
                                            <a href="{{ route('siswa.lms.mapel.ujian.show', [$mataPelajaran->id, $ujian->id]) }}"
                                                class="btn btn-info btn-sm">
                                                <i class="fas fa-poll"></i> Lihat Hasil
                                            </a>
                                        @else
                                            <button class="btn btn-secondary btn-sm" disabled>
                                                <i class="fas fa-check"></i> Selesai
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach
        @else
            <div class="no-items-date">
                <i class="fas fa-pen-square fa-2x mb-2"></i>
                <p class="empty-text">Belum ada ujian yang tersedia</p>
            </div>
        @endif
    </div>

    <!-- 5. Forum Diskusi -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="section-heading-inline">
                <i class="fas fa-comments"></i> Forum Diskusi
            </h3>
            <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}" class="btn btn-outline-primary btn-sm">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        @php
            $latestForums = \App\Models\ForumDiskusi::where('mata_pelajaran_id', $mataPelajaran->id)
                ->where('kelas_id', $siswa->kelas_id)
                ->with('user', 'replies')
                ->orderBy('is_pinned', 'desc')
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        @endphp

        @forelse($latestForums as $forum)
            <a href="{{ route('siswa.lms.mapel.forum.show', [$mataPelajaran->id, $forum->id]) }}"
                class="item-list forum-preview-link d-flex justify-content-between align-items-center text-decoration-none text-dark">
                <div>
                    <h6 class="mb-1 forum-preview-title">
                        @if($forum->is_pinned)<i class="fas fa-thumbtack text-warning me-1"></i>@endif
                        {{ Str::limit($forum->judul, 50) }}
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-user me-1"></i>{{ $forum->user->name ?? 'Guru' }}
                        <span class="mx-2">-</span>
                        <i class="far fa-clock me-1"></i>{{ $forum->created_at->copy()->locale('id')->diffForHumans() }}
                    </small>
                </div>
                <span class="badge bg-primary forum-reply-badge">
                    <i class="fas fa-comment me-1"></i>{{ $forum->replies->count() }}
                </span>
            </a>
        @empty
            <div class="empty-preview">
                <i class="fas fa-comments fa-2x mb-2 empty-preview-icon"></i>
                <p class="empty-preview-text">Belum ada diskusi untuk mata pelajaran ini</p>
            </div>
        @endforelse
    </div>

    <!-- 6. KELAS VIRTUAL (Meeting) -->
    <div class="section-card meeting-section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h3 class="section-heading-success">
                <i class="fas fa-video"></i> Kelas Virtual (Meeting)
            </h3>
            <a href="{{ route('siswa.lms.mapel.meeting.index', $mataPelajaran->id) }}"
                class="btn btn-outline-success btn-sm">
                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>

        @php
            $meetingList = \App\Models\LmsMeeting::where('kelas_id', $siswa->kelas_id)
                ->where('mata_pelajaran_id', $mataPelajaran->id)
                ->where('is_active', true)
                ->orderBy('waktu_mulai', 'asc')
                ->take(5)
                ->get();
        @endphp

        @forelse($meetingList as $meeting)
            <div class="item-list">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="badge bg-info text-dark me-2">
                            {{ ucfirst(str_replace('_', ' ', $meeting->platform)) }}
                        </span>
                        @if($meeting->waktu_mulai->isFuture())
                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Akan Datang</span>
                        @else
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Live</span>
                        @endif

                        <h5 class="mt-2 mb-1 fw-bold meeting-title">{{ $meeting->judul }}</h5>
                        <small class="text-muted">
                            <i class="far fa-calendar-alt me-1"></i> {{ $meeting->waktu_mulai->translatedFormat('d M Y') }}
                            <i class="far fa-clock ms-2 me-1"></i> {{ $meeting->waktu_mulai->format('H:i') }}
                        </small>
                    </div>
                    <div>
                        <a href="{{ $meeting->link_meeting }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fas fa-video me-1"></i> Gabung
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-preview">
                <i class="fas fa-video fa-2x mb-2 empty-preview-icon"></i>
                <p class="empty-preview-text">Belum ada jadwal meeting aktif saat ini</p>
            </div>
        @endforelse
    </div>
</div>
@endsection