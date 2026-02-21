{{-- resources/views/ketua/catatan/show.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Detail Catatan')

@section('page-title', 'Detail Catatan')
@section('page-subtitle', 'Lihat detail catatan yang telah dikirim')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
}

.card-body {
    padding: 24px;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.detail-section {
    margin-bottom: 24px;
}

.detail-label {
    font-weight: 600;
    color: #6b7280;
    font-size: 13px;
    text-transform: uppercase;
    margin-bottom: 8px;
}

.detail-value {
    color: #1a1a1a;
    font-size: 15px;
    line-height: 1.6;
}

.badge {
    padding: 6px 14px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    display: inline-block;
}

.badge-biasa {
    background: #e5e7eb;
    color: #4b5563;
}

.badge-penting {
    background: #fef3c7;
    color: #92400e;
}

.badge-mendesak {
    background: #fee2e2;
    color: #991b1b;
}

.badge-semua {
    background: #dbeafe;
    color: #1e40af;
}

.badge-role {
    background: #d1fae5;
    color: #065f46;
}

.badge-individu {
    background: #fce7f3;
    color: #9f1239;
}

.pembaca-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 400px;
    overflow-y: auto;
}

.pembaca-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.pembaca-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 16px;
}

.pembaca-info {
    flex: 1;
}

.pembaca-name {
    font-weight: 600;
    color: #1a1a1a;
    margin-bottom: 2px;
}

.pembaca-time {
    font-size: 12px;
    color: #6b7280;
}

.empty-pembaca {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.divider {
    height: 1px;
    background: #e5e7eb;
    margin: 24px 0;
}
</style>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 0 1rem;">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-envelope-open"></i> Detail Catatan</h5>
            <a href="{{ route('admin.catatan.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
        <div class="card-body">
            {{-- Judul --}}
            <div class="detail-section">
                <div class="detail-label">Judul</div>
                <div class="detail-value" style="font-size: 20px; font-weight: 600;">
                    {{ $catatan->judul }}
                </div>
            </div>

            {{-- Info Grid --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 24px;">
                <div class="detail-section" style="margin: 0;">
                    <div class="detail-label">Dikirim Oleh</div>
                    <div class="detail-value">
                        <strong>{{ $catatan->pengirim->name }}</strong>
                    </div>
                </div>

                <div class="detail-section" style="margin: 0;">
                    <div class="detail-label">Tanggal Kirim</div>
                    <div class="detail-value">
                        {{ $catatan->tanggal_kirim->format('d F Y, H:i') }} WIB
                    </div>
                </div>

                <div class="detail-section" style="margin: 0;">
                    <div class="detail-label">Prioritas</div>
                    <div class="detail-value">
                        <span class="badge badge-{{ $catatan->prioritas }}">
                            @if($catatan->prioritas === 'biasa') <i class="fas fa-file-alt"></i>
                            @elseif($catatan->prioritas === 'penting') <i class="fas fa-exclamation-triangle"></i>
                            @else <i class="fas fa-exclamation-circle"></i>
                            @endif
                            {{ strtoupper($catatan->prioritas) }}
                        </span>
                    </div>
                </div>

                <div class="detail-section" style="margin: 0;">
                    <div class="detail-label">Penerima</div>
                    <div class="detail-value">
                        @if($catatan->tipe_penerima === 'semua')
                            <span class="badge badge-semua"><i class="fas fa-bullhorn"></i> Semua Pengguna</span>
                        @elseif($catatan->tipe_penerima === 'role')
                            <span class="badge badge-role">
                                <i class="fas fa-users"></i> {{ ucwords(str_replace('_', ' ', $catatan->role_penerima)) }}
                            </span>
                        @else
                            <span class="badge badge-individu">
                                <i class="fas fa-user"></i> {{ $catatan->penerima->name ?? 'User' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="divider"></div>

            {{-- Isi Catatan --}}
            <div class="detail-section">
                <div class="detail-label">Isi Catatan</div>
                <div class="detail-value" style="background: #f9fafb; padding: 20px; border-radius: 8px; white-space: pre-wrap;">{{ $catatan->isi_catatan }}</div>
            </div>

            <div class="divider"></div>

            {{-- Daftar Pembaca --}}
            <div class="detail-section">
                <div class="detail-label" style="margin-bottom: 16px;">
                    Dibaca Oleh ({{ $catatan->totalPembaca() }} orang)
                </div>
                
                @if($catatan->pembaca->count() > 0)
                <div class="pembaca-list">
                    @foreach($catatan->pembaca as $pembaca)
                    <div class="pembaca-item">
                        <div class="pembaca-avatar">
                            {{ strtoupper(substr($pembaca->name, 0, 1)) }}
                        </div>
                        <div class="pembaca-info">
                            <div class="pembaca-name">{{ $pembaca->name }}</div>
                            <div class="pembaca-time">
                                <i class="fas fa-clock"></i>
                                Dibaca {{ $pembaca->pivot->dibaca_pada->diffForHumans() }}
                            </div>
                        </div>
                        <div>
                            <span class="badge" style="background: #d1fae5; color: #065f46;">
                                <i class="fas fa-check"></i> Sudah dibaca
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="empty-pembaca">
                    <i class="fas fa-eye-slash" style="font-size: 36px; color: #d1d5db; margin-bottom: 12px;"></i>
                    <p style="font-weight: 500;">Belum ada yang membaca catatan ini</p>
                    <small>Status akan update otomatis ketika ada yang membaca</small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection