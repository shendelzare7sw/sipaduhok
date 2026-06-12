@extends('layouts.lms')

@section('title', 'Detail Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Detail pengumuman')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/pengumuman/show.css'])
@endpush

@section('content')
<div class="siswa-lms-pengumuman-show-page">
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
                        <span>-</span>
                        <span>{{ \Carbon\Carbon::parse($pengumuman->tanggal_pengumuman)->translatedFormat('d F Y') }}</span>
                        <span>-</span>
                        <span>{{ $pengumuman->created_at->copy()->locale('id')->diffForHumans() }}</span>
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
</div>
@endsection
