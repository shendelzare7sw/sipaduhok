@php
    $materi = $konten;
    $previewTitle = $materi->judul_materi ?? '-';
    $kontenLabel = 'Materi';

    $extension = $materi->file_materi ? strtolower(pathinfo($materi->file_materi, PATHINFO_EXTENSION)) : null;
    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    $isPdf = $extension === 'pdf';
    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg']);

    // Generate hashed preview URL to bypass IDM interception
    if ($materi->file_materi) {
        $hashKey = crc32($materi->file_materi . now()->timestamp);
        \Illuminate\Support\Facades\Cache::put('docview_' . $hashKey, $materi->file_materi, 3600);
        $filePreviewUrl = route('document.preview', $hashKey);
    } else {
        $filePreviewUrl = null;
    }
@endphp

@extends('monitoring-lms.preview.wrapper', [
    'previewTitle' => $previewTitle,
    'kontenLabel' => $kontenLabel,
])

@section('preview-content')
    <h2 class="preview-section-title">
        <i class="fas fa-book-open me-2" style="color: #0284c7;"></i>{{ $materi->judul_materi }}
    </h2>

    <div class="preview-meta-row">
        @if($materi->guru)
            <span><i class="fas fa-user-tie"></i> {{ $materi->guru->nama_lengkap }}</span>
        @endif
        @if($materi->mataPelajaran)
            <span><i class="fas fa-book"></i> {{ $materi->mataPelajaran->nama_mapel }}</span>
        @endif
        @if($materi->kelas)
            <span><i class="fas fa-school"></i> {{ $materi->kelas->nama_kelas }}</span>
        @endif
        @if($materi->tanggal_upload)
            <span><i class="fas fa-calendar"></i> {{ $materi->tanggal_upload->locale('id')->translatedFormat('d F Y') }}</span>
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

    {{-- Inline file preview --}}
    @if($materi->file_materi)
        <div class="preview-section">
            <div class="preview-section-label">Berkas Materi</div>

            @if($isImage)
                <div class="inline-preview-box">
                    <img src="{{ $filePreviewUrl }}" alt="{{ $materi->judul_materi }}"
                         style="max-width: 100%; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                </div>
            @elseif($isPdf)
                <div class="inline-preview-box">
                    <iframe src="{{ $filePreviewUrl }}"
                            class="inline-pdf-frame"
                            title="Preview PDF Materi"></iframe>
                </div>
            @elseif($isVideo)
                <div class="inline-preview-box">
                    <video controls style="width: 100%; max-height: 70vh; border-radius: 10px;">
                        <source src="{{ $filePreviewUrl }}" type="video/{{ $extension }}">
                        Browser Anda tidak mendukung video.
                    </video>
                </div>
            @else
                <div class="preview-file-box">
                    <i class="fas fa-file-alt"></i>
                    <div class="preview-section-body" style="margin-bottom: 12px;">
                        {{ basename($materi->file_materi) }} ({{ strtoupper($extension) }})
                    </div>
                </div>
            @endif

            <div class="d-flex gap-2 flex-wrap mt-3">
                <a href="{{ $filePreviewUrl }}" target="_blank" rel="noopener"
                    class="btn btn-primary" style="border-radius: 8px;">
                    <i class="fas fa-external-link-alt me-1"></i>Buka di Tab Baru
                </a>
                <a href="{{ asset('storage/' . $materi->file_materi) }}" download
                    class="btn btn-outline-primary" style="border-radius: 8px;">
                    <i class="fas fa-download me-1"></i>Download
                </a>
            </div>
        </div>
    @elseif($materi->url_materi)
        <div class="preview-section">
            <div class="preview-section-label">Link Materi Eksternal</div>
            <div class="preview-file-box">
                <i class="fas fa-link"></i>
                <div class="preview-section-body" style="margin-bottom: 12px; word-break: break-all;">
                    {{ $materi->url_materi }}
                </div>
                <a href="{{ $materi->url_materi }}" target="_blank" rel="noopener"
                    class="btn btn-primary" style="border-radius: 8px;">
                    <i class="fas fa-external-link-alt me-1"></i>Buka Link
                </a>
            </div>
        </div>
    @else
        <div class="alert alert-warning" style="border-radius: 10px; border: none; background: rgba(217, 119, 6, 0.08); color: #92400e;">
            <i class="fas fa-exclamation-triangle me-1"></i>Belum ada berkas atau link materi yang diunggah.
        </div>
    @endif
@endsection
