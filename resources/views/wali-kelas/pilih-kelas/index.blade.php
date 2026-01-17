@extends('layouts.sneat')

@section('title', 'Pilih Kelas')
@section('page-title', 'Pilih Kelas')
@section('page-subtitle', 'Pilih kelas yang ingin Anda kelola')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .kelas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        padding: 0;
    }

    .kelas-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        overflow: hidden;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        border: 2px solid transparent;
    }

    .kelas-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
        border-color: #8b5cf6;
    }

    .kelas-card.selected {
        border-color: #10b981;
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }

    .kelas-card-header {
        padding: 24px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        position: relative;
    }

    .kelas-card-header.paud { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
    .kelas-card-header.sd { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
    .kelas-card-header.smp { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
    .kelas-card-header.sma { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

    .kelas-nama {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .kelas-jenjang {
        font-size: 14px;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .kelas-card-body {
        padding: 20px 24px;
    }

    .kelas-info-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }

    .kelas-info-row:last-child {
        border-bottom: none;
    }

    .kelas-info-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .kelas-info-content {
        flex: 1;
    }

    .kelas-info-label {
        font-size: 12px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .kelas-info-value {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
    }

    .kelas-card-footer {
        padding: 16px 24px;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
    }

    .btn-pilih {
        width: 100%;
        padding: 12px 20px;
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-pilih:hover {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        transform: translateY(-1px);
    }

    .selected-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: white;
        color: #10b981;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .page-intro {
        text-align: center;
        max-width: 600px;
        margin: 0 auto 32px;
    }

    .page-intro h2 {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 12px;
    }

    .page-intro p {
        font-size: 16px;
        color: #6b7280;
        line-height: 1.6;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 24px;">
    <div class="page-intro">
        <h2>Selamat Datang, {{ $waliKelas->nama_lengkap }}</h2>
        <p>Anda ditugaskan sebagai wali kelas untuk {{ $kelasList->count() }} kelas. Silakan pilih kelas yang ingin Anda kelola.</p>
    </div>

    <div class="kelas-grid">
        @foreach($kelasList as $kelas)
            <div class="kelas-card {{ $currentSelectedId == $kelas->id ? 'selected' : '' }}">
                <div class="kelas-card-header {{ strtolower($kelas->jenjang) }}">
                    @if($currentSelectedId == $kelas->id)
                        <div class="selected-badge">
                            <i class="fas fa-check-circle"></i> Aktif
                        </div>
                    @endif
                    <div class="kelas-nama">{{ $kelas->nama_kelas }}</div>
                    <div class="kelas-jenjang">{{ $kelas->jenjang }}</div>
                </div>
                <div class="kelas-card-body">
                    <div class="kelas-info-row">
                        <div class="kelas-info-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="kelas-info-content">
                            <div class="kelas-info-label">Cabang</div>
                            <div class="kelas-info-value">{{ $kelas->cabang->nama_cabang ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="kelas-info-row">
                        <div class="kelas-info-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="kelas-info-content">
                            <div class="kelas-info-label">Tahun Ajaran</div>
                            <div class="kelas-info-value">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="kelas-info-row">
                        <div class="kelas-info-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="kelas-info-content">
                            <div class="kelas-info-label">Jumlah Siswa</div>
                            <div class="kelas-info-value">{{ $kelas->siswa->count() }} Siswa</div>
                        </div>
                    </div>
                </div>
                <div class="kelas-card-footer">
                    <form action="{{ route('wali.pilih-kelas.select', $kelas) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn-pilih">
                            @if($currentSelectedId == $kelas->id)
                                <i class="fas fa-check"></i> Sudah Dipilih
                            @else
                                <i class="fas fa-arrow-right"></i> Pilih Kelas Ini
                            @endif
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
