@extends('layouts.sneat')

@section('title', 'Detail Catatan')
@section('page-title', 'Detail Catatan')
@section('page-subtitle', 'Informasi lengkap catatan yang dikirim')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/waka/catatan/show.css')
@endsection

@section('content')
@php
    $routePrefix = 'waka';
    $sentAt = $catatan->tanggal_kirim ? \Carbon\Carbon::parse($catatan->tanggal_kirim) : $catatan->created_at;
    $priority = $catatan->prioritas ?: 'biasa';
    $readerCount = $catatan->relationLoaded('pembaca') ? $catatan->pembaca->count() : $catatan->totalPembaca();
@endphp

<div class="catatan-page">
    <div class="catatan-detail-card">
        <div class="catatan-detail-header">
            <h5><i class="fas fa-envelope-open-text text-primary me-2"></i>Detail Catatan</h5>
            <a href="{{ route($routePrefix . '.catatan.index') }}" class="catatan-btn secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>

        <div class="catatan-detail-body">
            <div class="mb-4">
                <div class="catatan-detail-label">Judul</div>
                <div class="catatan-detail-title">{{ $catatan->judul }}</div>
            </div>

            <div class="catatan-detail-grid">
                <div>
                    <div class="catatan-detail-label">Dikirim Oleh</div>
                    <div class="catatan-detail-value">{{ $catatan->pengirim->name ?? '-' }}</div>
                </div>
                <div>
                    <div class="catatan-detail-label">Tanggal Kirim</div>
                    <div class="catatan-detail-value">{{ $sentAt?->format('d F Y, H:i') }} WIB</div>
                </div>
                <div>
                    <div class="catatan-detail-label">Prioritas</div>
                    <span class="catatan-badge {{ $priority }}">
                        @if($priority === 'mendesak')
                            <i class="fas fa-exclamation-circle"></i>
                        @elseif($priority === 'penting')
                            <i class="fas fa-exclamation-triangle"></i>
                        @else
                            <i class="fas fa-file-alt"></i>
                        @endif
                        {{ ucfirst($priority) }}
                    </span>
                </div>
                <div>
                    <div class="catatan-detail-label">Penerima</div>
                    @if($catatan->tipe_penerima === 'semua')
                        <span class="catatan-badge semua"><i class="fas fa-users"></i> Semua Pengguna</span>
                    @elseif($catatan->tipe_penerima === 'role')
                        <span class="catatan-badge role"><i class="fas fa-user-tag"></i> {{ ucwords(str_replace('_', ' ', $catatan->role_penerima)) }}</span>
                    @else
                        <span class="catatan-badge individu"><i class="fas fa-user"></i> {{ $catatan->penerima->name ?? 'Individu' }}</span>
                    @endif
                </div>
            </div>

            <div class="catatan-section-title">
                <i class="fas fa-align-left text-primary"></i>
                Isi Catatan
            </div>
            <div class="catatan-message-box">{{ $catatan->isi_catatan }}</div>

            <div class="catatan-section-title">
                <i class="fas fa-eye text-success"></i>
                Dibaca Oleh ({{ number_format($readerCount) }} orang)
            </div>

            @if($catatan->pembaca->count() > 0)
                <div class="catatan-reader-list">
                    @foreach($catatan->pembaca as $pembaca)
                        <div class="catatan-reader">
                            <span class="catatan-avatar">{{ strtoupper(substr($pembaca->name, 0, 1)) }}</span>
                            <div>
                                <div class="catatan-reader-name">{{ $pembaca->name }}</div>
                                <div class="catatan-reader-time">
                                    <i class="far fa-clock me-1"></i>
                                    Dibaca {{ $pembaca->pivot->dibaca_pada ? \Carbon\Carbon::parse($pembaca->pivot->dibaca_pada)->locale('id')->diffForHumans() : '-' }}
                                </div>
                            </div>
                            <span class="catatan-badge role"><i class="fas fa-check"></i> Sudah dibaca</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="catatan-empty py-4">
                    <i class="fas fa-eye-slash"></i>
                    <h5>Belum ada pembaca</h5>
                    <p class="mb-0">Status akan diperbarui ketika penerima membuka catatan ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
