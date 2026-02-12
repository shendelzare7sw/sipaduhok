@extends('layouts.lms')

@section('title', 'Detail Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Detail pengumuman')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
<style>
.detail-wrapper {
    width: 100%;
    padding: 0;
}

.back-to-inbox {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    margin-bottom: 16px;
    color: #5f6368;
    text-decoration: none;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.2s;
}

.back-to-inbox:hover {
    background-color: #f1f3f4;
    color: #202124;
}

.detail-container {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
    overflow: hidden;
}

.detail-header {
    padding: 24px 40px 20px 40px;
    border-bottom: 1px solid #e0e0e0;
}

.detail-subject {
    font-size: 22px;
    font-weight: 400;
    color: #202124;
    margin: 0 0 16px 0;
    line-height: 28px;
}

.detail-labels {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 16px;
}

.label-badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 500;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.label-badge.priority-high {
    background-color: #fce8e6;
    color: #d93025;
}

.label-badge.priority-normal {
    background-color: #e8f0fe;
    color: #1a73e8;
}

.detail-info {
    display: flex;
    align-items: flex-start;
    gap: 16px;
    padding: 16px 0;
}

.sender-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 500;
    font-size: 16px;
    flex-shrink: 0;
}

.sender-details {
    flex: 1;
}

.sender-name {
    font-size: 14px;
    font-weight: 500;
    color: #202124;
    margin: 0 0 2px 0;
}

.sender-meta {
    font-size: 12px;
    color: #5f6368;
    display: flex;
    align-items: center;
    gap: 8px;
}

.detail-body {
    padding: 24px 40px 32px 40px;
}

.message-content {
    font-size: 14px;
    line-height: 1.7;
    color: #202124;
    margin-bottom: 24px;
    white-space: pre-wrap;
}

.attachment-section {
    border-top: 1px solid #e0e0e0;
    padding-top: 20px;
}

.attachment-label {
    font-size: 12px;
    color: #5f6368;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.attachment-preview-container {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 16px;
    margin-bottom: 12px;
}

.preview-iframe {
    width: 100%;
    height: 600px;
    border: 1px solid #dadce0;
    border-radius: 4px;
    background: white;
}

.preview-image {
    width: 100%;
    height: auto;
    max-height: 600px;
    object-fit: contain;
    border-radius: 4px;
    background: white;
}

.no-preview {
    text-align: center;
    padding: 40px 20px;
    color: #5f6368;
}

.no-preview i {
    font-size: 48px;
    margin-bottom: 12px;
    color: #dadce0;
}

.attachment-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 4px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
    border: 1px solid #dadce0;
    background: white;
    color: #5f6368;
}

.btn-action:hover {
    background-color: #f8f9fa;
    border-color: #5f6368;
    color: #202124;
}

.btn-action.primary {
    background-color: #1a73e8;
    border-color: #1a73e8;
    color: white;
}

.btn-action.primary:hover {
    background-color: #1557b0;
    border-color: #1557b0;
    color: white;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
}

/* Responsive */
@media (max-width: 768px) {
    .detail-header,
    .detail-body {
        padding-left: 20px;
        padding-right: 20px;
    }
    
    .detail-subject {
        font-size: 18px;
    }
}
</style>
@endpush

@section('content')
<div class="detail-wrapper">
    <a href="{{ route('siswa.lms.pengumuman.index') }}" class="back-to-inbox">
        <i class="fas fa-arrow-left"></i>
        <span>Kembali ke inbox</span>
    </a>

    <div class="detail-container">
        <div class="detail-header">
            <h1 class="detail-subject">{{ $pengumuman->judul }}</h1>
            
            <div class="detail-labels">
                <span class="label-badge {{ $pengumuman->prioritas == 'tinggi' ? 'priority-high' : 'priority-normal' }}">
                    {{ $pengumuman->prioritas == 'tinggi' ? 'Penting' : 'Informasi' }}
                </span>
            </div>

            <div class="detail-info">
                <div class="sender-avatar">
                    <i class="fas fa-school"></i>
                </div>
                <div class="sender-details">
                    <p class="sender-name">SIPADUHOK - Pengumuman Sekolah</p>
                    <div class="sender-meta">
                        <span>kepada saya</span>
                        <span>•</span>
                        <span>{{ \Carbon\Carbon::parse($pengumuman->tanggal_pengumuman)->translatedFormat('d F Y') }}</span>
                        <span>•</span>
                        <span>{{ $pengumuman->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="detail-body">
            <div class="message-content">{{ $pengumuman->isi_pengumuman }}</div>

            @if($pengumuman->lampiran_surat)
                <div class="attachment-section">
                    <div class="attachment-label">
                        <i class="fas fa-paperclip"></i>
                        <span>1 Lampiran</span>
                    </div>

                    @php
                        $extension = strtolower(pathinfo($pengumuman->lampiran_surat, PATHINFO_EXTENSION));
                        $isPdf = $extension === 'pdf';
                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $previewUrl = route('storage.preview', ['path' => $pengumuman->lampiran_surat]);
                        $downloadUrl = asset('storage/' . $pengumuman->lampiran_surat);
                    @endphp

                    <div class="attachment-preview-container">
                        @if($isPdf)
                            <iframe src="{{ $previewUrl }}" class="preview-iframe"></iframe>
                        @elseif($isImage)
                            <img src="{{ $downloadUrl }}" alt="Lampiran" class="preview-image">
                        @else
                            <div class="no-preview">
                                <i class="fas fa-file"></i>
                                <p>Preview tidak tersedia untuk tipe file ini</p>
                            </div>
                        @endif
                    </div>

                    <div class="attachment-actions">
                        <a href="{{ $downloadUrl }}" download class="btn-action primary">
                            <i class="fas fa-download"></i>
                            <span>Download</span>
                        </a>
                        @if($isPdf || $isImage)
                            <a href="{{ $previewUrl }}" target="_blank" class="btn-action">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Buka di tab baru</span>
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
