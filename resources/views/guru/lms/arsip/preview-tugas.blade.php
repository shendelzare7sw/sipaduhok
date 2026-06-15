@php
    $tugas = $konten;
    $previewTitle = $tugas->judul_tugas ?? '-';
    $kontenLabel = ($tugas->jenis_tugas ?? null) === 'latihan' ? 'Latihan' : 'Tugas';

    $extension = $tugas->file_tugas ? strtolower(pathinfo($tugas->file_tugas, PATHINFO_EXTENSION)) : null;
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);

    if ($tugas->file_tugas) {
        $hashKey = crc32($tugas->file_tugas . now()->timestamp);
        \Illuminate\Support\Facades\Cache::put('docview_' . $hashKey, $tugas->file_tugas, 3600);
        $filePreviewUrl = route('document.preview', $hashKey);
    } else {
        $filePreviewUrl = null;
    }
@endphp

@extends('guru.lms.arsip.preview-wrapper', [
    'previewTitle' => $previewTitle,
    'kontenLabel' => $kontenLabel,
])

@section('preview-content')
    <h2 class="preview-section-title">
        <i class="fas fa-tasks me-2 preview-title-icon-tugas"></i>{{ $tugas->judul_tugas }}
    </h2>

    <div class="preview-meta-row">
        <span class="badge preview-type-badge preview-type-badge-warning">
            {{ $kontenLabel }}
        </span>
        @if($tugas->mataPelajaran)
            <span><i class="fas fa-book"></i> {{ $tugas->mataPelajaran->nama_mapel }}</span>
        @endif
        @if($tugas->kelas)
            <span><i class="fas fa-school"></i> {{ $tugas->kelas->nama_kelas }}</span>
        @endif
        @if($tugas->kelas?->tahunAjaran)
            <span><i class="fas fa-calendar-alt"></i> TA {{ $tugas->kelas->tahunAjaran->nama_tahun_ajaran }}</span>
        @endif
        @if($tugas->tanggal_mulai)
            <span><i class="fas fa-play-circle"></i> Mulai: {{ \Carbon\Carbon::parse($tugas->tanggal_mulai)->locale('id')->translatedFormat('d M Y') }}</span>
        @endif
        @if($tugas->tanggal_deadline)
            <span><i class="fas fa-flag-checkered"></i> Deadline: {{ \Carbon\Carbon::parse($tugas->tanggal_deadline)->locale('id')->translatedFormat('d M Y') }}</span>
        @endif
    </div>

    @if($tugas->judul_bab)
        <div class="preview-section">
            <div class="preview-section-label">Bab</div>
            <div class="preview-section-body">{{ $tugas->judul_bab }}</div>
        </div>
    @endif

    @if($tugas->nama_materi)
        <div class="preview-section">
            <div class="preview-section-label">Materi Terkait</div>
            <div class="preview-section-body">{{ $tugas->nama_materi }}</div>
        </div>
    @endif

    @if($tugas->deskripsi)
        <div class="preview-section">
            <div class="preview-section-label">Instruksi {{ $kontenLabel }}</div>
            <div class="preview-section-body">{!! nl2br(e($tugas->deskripsi)) !!}</div>
        </div>
    @endif

    @if($tugas->file_tugas)
        <div class="preview-section">
            <div class="preview-section-label">Berkas {{ $kontenLabel }}</div>
            @if($isImage)
                <div class="inline-preview-box">
                    <img src="{{ $filePreviewUrl }}" alt="{{ $tugas->judul_tugas }}" class="preview-image">
                </div>
            @elseif($isPdf)
                <div class="inline-preview-box">
                    <iframe src="{{ $filePreviewUrl }}" class="inline-pdf-frame" title="Preview PDF Tugas"></iframe>
                </div>
            @elseif($isVideo)
                <div class="inline-preview-box">
                    <video controls class="preview-video">
                        <source src="{{ $filePreviewUrl }}" type="video/{{ $extension }}">
                        Browser Anda tidak mendukung video.
                    </video>
                </div>
            @else
                <div class="preview-file-box">
                    <i class="fas fa-file-alt"></i>
                    <div class="preview-section-body preview-section-body-spaced">
                        {{ basename($tugas->file_tugas) }} ({{ strtoupper($extension) }})
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2 flex-wrap mt-3">
                <a href="{{ $filePreviewUrl }}" target="_blank" rel="noopener"
                    class="btn btn-primary preview-action-btn">
                    <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                </a>
                <a href="{{ asset('storage/' . $tugas->file_tugas) }}" download
                    class="btn btn-outline-primary preview-action-btn">
                    <i class="fas fa-download me-1"></i>Download
                </a>
            </div>
        </div>
    @endif

    <div class="preview-section">
        <div class="preview-section-label">Pengaturan {{ $kontenLabel }}</div>
        <div class="settings-grid">
            <div class="setting-item">
                <i class="fas fa-eye"></i>
                <span>Tampilkan Nilai: <strong>{{ ($tugas->tampilkan_nilai ?? false) ? 'Ya' : 'Tidak' }}</strong></span>
            </div>
            <div class="setting-item">
                <i class="fas fa-redo"></i>
                <span>Bisa Diulang: <strong>{{ ($tugas->bisa_diulang ?? false) ? 'Ya' : 'Tidak' }}</strong></span>
            </div>
            @if($tugas->bisa_diulang ?? false)
                <div class="setting-item">
                    <i class="fas fa-list-ol"></i>
                    <span>Batas Pengulangan: <strong>{{ $tugas->batas_pengulangan ?? 0 }}</strong></span>
                </div>
            @endif
        </div>
    </div>
@endsection
