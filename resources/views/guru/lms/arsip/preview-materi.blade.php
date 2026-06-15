@php
    $materi = $konten;
    $previewTitle = $materi->judul_materi ?? '-';
    $kontenLabel = 'Materi';

    $extension = $materi->file_materi ? strtolower(pathinfo($materi->file_materi, PATHINFO_EXTENSION)) : null;
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);

    if ($materi->file_materi) {
        $hashKey = crc32($materi->file_materi . now()->timestamp);
        \Illuminate\Support\Facades\Cache::put('docview_' . $hashKey, $materi->file_materi, 3600);
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
        <i class="fas fa-book-open me-2 preview-title-icon-materi"></i>{{ $materi->judul_materi }}
    </h2>

    <div class="preview-meta-row">
        @if($materi->mataPelajaran)
            <span><i class="fas fa-book"></i> {{ $materi->mataPelajaran->nama_mapel }}</span>
        @endif
        @if($materi->kelas)
            <span><i class="fas fa-school"></i> {{ $materi->kelas->nama_kelas }}</span>
        @endif
        @if($materi->kelas?->tahunAjaran)
            <span><i class="fas fa-calendar-alt"></i> TA {{ $materi->kelas->tahunAjaran->nama_tahun_ajaran }}</span>
        @endif
        @if($materi->tanggal_upload)
            <span><i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($materi->tanggal_upload)->locale('id')->translatedFormat('d F Y') }}</span>
        @endif
        @if($materi->tipe_file)
            <span><i class="fas fa-file"></i> {{ strtoupper($materi->tipe_file) }}</span>
        @endif
    </div>

    @if($materi->kategori)
        <div class="preview-section">
            <div class="preview-section-label">Kategori</div>
            <div class="preview-section-body">{{ $materi->kategori === 'modul_ajar' ? 'Modul Ajar' : 'Materi' }}</div>
        </div>
    @endif

    @if($materi->deskripsi)
        <div class="preview-section">
            <div class="preview-section-label">Deskripsi</div>
            <div class="preview-section-body">{!! nl2br(e($materi->deskripsi)) !!}</div>
        </div>
    @endif

    @if($materi->file_materi)
        <div class="preview-section">
            <div class="preview-section-label">Berkas Materi</div>
            @if($isImage)
                <div class="inline-preview-box">
                    <img src="{{ $filePreviewUrl }}" alt="{{ $materi->judul_materi }}" class="preview-image">
                </div>
            @elseif($isPdf)
                <div class="inline-preview-box">
                    <iframe src="{{ $filePreviewUrl }}" class="inline-pdf-frame" title="Preview PDF Materi"></iframe>
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
                        {{ basename($materi->file_materi) }} ({{ strtoupper($extension) }})
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2 flex-wrap mt-3">
                <a href="{{ $filePreviewUrl }}" target="_blank" rel="noopener"
                    class="btn btn-primary preview-action-btn">
                    <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                </a>
                <a href="{{ asset('storage/' . $materi->file_materi) }}" download
                    class="btn btn-outline-primary preview-action-btn">
                    <i class="fas fa-download me-1"></i>Download
                </a>
            </div>
        </div>
    @elseif($materi->url_materi)
        <div class="preview-section">
            <div class="preview-section-label">Link Materi Eksternal</div>
            <div class="preview-file-box">
                <i class="fas fa-link"></i>
                <div class="preview-section-body preview-link-body">
                    {{ $materi->url_materi }}
                </div>
                <a href="{{ $materi->url_materi }}" target="_blank" rel="noopener"
                    class="btn btn-primary preview-action-btn">
                    <i class="fas fa-external-link-alt me-1"></i>Buka Link
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-warning preview-warning">
            <i class="fas fa-exclamation-triangle me-1"></i>Tidak ada berkas atau link materi.
        </div>
    @endif
@endsection
