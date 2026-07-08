@php
    $tugas = $konten;
    $previewTitle = $tugas->judul_tugas ?? '-';
    $kontenLabel = 'Tugas';

    $extension = $tugas->file_tugas ? strtolower(pathinfo($tugas->file_tugas, PATHINFO_EXTENSION)) : null;
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);

    // Preview URL: token acak terikat pemilik (lihat helper preview_url()).
    $filePreviewUrl = preview_url($tugas->file_tugas);
@endphp

@extends('monitoring-lms.preview.wrapper', [
    'previewTitle' => $previewTitle,
    'kontenLabel' => $kontenLabel,
])

@section('preview-content')
    <h2 class="preview-section-title">
        <i class="fas fa-tasks me-2 preview-title-icon-tugas"></i>{{ $tugas->judul_tugas }}
    </h2>

    <div class="preview-meta-row">
        <span class="badge preview-type-badge preview-type-tugas">
            Tugas
        </span>
        @if($tugas->guru)
            <span><i class="fas fa-user-tie"></i> {{ $tugas->guru->nama_lengkap }}</span>
        @endif
        @if($tugas->mataPelajaran)
            <span><i class="fas fa-book"></i> {{ $tugas->mataPelajaran->nama_mapel }}</span>
        @endif
        @if($tugas->kelas)
            <span><i class="fas fa-school"></i> {{ $tugas->kelas->nama_kelas }}</span>
        @endif
        @if($tugas->tanggal_mulai)
            <span><i class="fas fa-play-circle"></i> Mulai: {{ $tugas->tanggal_mulai->locale('id')->translatedFormat('d M Y') }}</span>
        @endif
        @if($tugas->tanggal_deadline)
            <span><i class="fas fa-flag-checkered"></i> Tenggat: {{ $tugas->tanggal_deadline->locale('id')->translatedFormat('d M Y') }}</span>
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
            <div class="preview-section-label">Instruksi Tugas</div>
            <div class="preview-section-body">{!! nl2br(e($tugas->deskripsi)) !!}</div>
        </div>
    @endif

    {{-- Inline file preview --}}
    @if($tugas->file_tugas)
        <div class="preview-section">
            <div class="preview-section-label">Berkas Tugas</div>

            @if($isImage)
                <div class="inline-preview-box">
                    <img src="{{ $filePreviewUrl }}" alt="{{ $tugas->judul_tugas }}"
                         class="preview-media-image">
                </div>
            @elseif($isPdf)
                <div class="inline-preview-box">
                    <iframe src="{{ $filePreviewUrl }}"
                            class="inline-pdf-frame"
                            title="Pratinjau PDF Tugas"></iframe>
                </div>
            @elseif($isVideo)
                <div class="inline-preview-box">
                    <video controls class="preview-media-video">
                        <source src="{{ $filePreviewUrl }}" type="video/{{ $extension }}">
                        Browser Anda tidak mendukung video.
                    </video>
                </div>
            @else
                <div class="preview-file-box">
                    <i class="fas fa-file-alt"></i>
                    <div class="preview-section-body preview-file-name">
                        {{ basename($tugas->file_tugas) }} ({{ strtoupper($extension) }})
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2 flex-wrap mt-3">
                <a href="{{ $filePreviewUrl }}" target="_blank" rel="noopener"
                    class="btn btn-primary preview-action-button">
                    <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                </a>
                <a href="{{ asset('storage/' . $tugas->file_tugas) }}" download
                    class="btn btn-outline-primary preview-action-button">
                    <i class="fas fa-download me-1"></i>Unduh
                </a>
            </div>
        </div>
    @endif

    {{-- Settings summary --}}
    <div class="preview-section">
        <div class="preview-section-label">Pengaturan Tugas</div>
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
