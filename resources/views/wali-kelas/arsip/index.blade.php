@extends('layouts.sneat')

@section('title', 'Arsip Kelas Saya')
@section('page-title', 'Arsip Kelas Saya')
@section('page-subtitle', 'Akses read-only ke kelas yang pernah Anda walikan (lintas tahun ajaran)')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
    .arsip-stat-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
        margin-bottom: 22px;
    }
    .arsip-stat-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .arsip-stat-card .icon-wrap {
        width: 44px; height: 44px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px;
    }
    .arsip-stat-card .stat-value { font-size: 22px; font-weight: 700; line-height: 1; color: #1e293b; }
    .arsip-stat-card .stat-label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-top: 4px; }

    .ta-section { margin-bottom: 28px; }
    .ta-header {
        display: flex; align-items: center; gap: 10px;
        padding: 10px 14px;
        background: linear-gradient(90deg, #ede9fe, #f3e8ff);
        border-left: 4px solid #8b5cf6;
        border-radius: 6px;
        margin-bottom: 14px;
        font-weight: 700;
        color: #6b21a8;
        font-size: 14px;
    }
    .ta-header .badge-aktif {
        background: #16a34a; color: white;
        font-size: 10px; padding: 2px 8px; border-radius: 4px;
        margin-left: auto;
    }

    .kelas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 14px;
    }
    .kelas-card {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 16px;
        display: flex; flex-direction: column;
        text-decoration: none;
        color: inherit;
        transition: all 0.2s;
    }
    .kelas-card:hover {
        border-color: #8b5cf6;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
        transform: translateY(-2px);
        text-decoration: none;
        color: inherit;
    }
    .kelas-card .kelas-name { font-size: 18px; font-weight: 700; color: #1e293b; }
    .kelas-card .kelas-meta { font-size: 12px; color: #64748b; margin-top: 4px; }
    .kelas-card .stat-row { display: flex; gap: 10px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #f1f5f9; }
    .kelas-card .stat-mini { flex: 1; }
    .kelas-card .stat-mini .num { font-size: 16px; font-weight: 700; color: #4361ee; }
    .kelas-card .stat-mini .lbl { font-size: 10px; color: #94a3b8; text-transform: uppercase; }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #64748b;
    }
    .empty-state i { font-size: 4rem; color: #cbd5e1; display: block; margin-bottom: 16px; }
</style>

<div class="container-xxl">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat ringkas --}}
    <div class="arsip-stat-row">
        <div class="arsip-stat-card">
            <div class="icon-wrap" style="background: #ede9fe; color: #7c3aed;">
                <i class="fas fa-archive"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalKelas }}</div>
                <div class="stat-label">Total Kelas</div>
            </div>
        </div>
        <div class="arsip-stat-card">
            <div class="icon-wrap" style="background: #dbeafe; color: #2563eb;">
                <i class="fas fa-history"></i>
            </div>
            <div>
                <div class="stat-value">{{ $kelasGrouped->count() }}</div>
                <div class="stat-label">Tahun Ajaran</div>
            </div>
        </div>
    </div>

    @if($totalKelas === 0)
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <h5 class="fw-bold">Belum Ada Arsip</h5>
            <p class="mb-0">Anda belum pernah diassign sebagai wali kelas pada kelas manapun.</p>
        </div>
    @else
        @foreach($kelasGrouped as $taName => $kelasList)
            <div class="ta-section">
                <div class="ta-header">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Tahun Ajaran {{ $taName }}</span>
                    @if($kelasList->first()?->tahunAjaran?->is_active)
                        <span class="badge-aktif">AKTIF</span>
                    @endif
                </div>

                <div class="kelas-grid">
                    @foreach($kelasList as $kelas)
                        <a href="{{ route('wali.arsip.show', $kelas->id) }}" class="kelas-card">
                            <div class="kelas-name">
                                <i class="fas fa-chalkboard-teacher" style="color: #8b5cf6;"></i>
                                {{ $kelas->nama_kelas }}
                            </div>
                            <div class="kelas-meta">
                                {{ $kelas->jenjang }} ·
                                {{ $kelas->cabang->nama_cabang ?? '-' }}
                            </div>

                            <div class="stat-row">
                                <div class="stat-mini">
                                    <div class="num">{{ $kelas->siswa_count }}</div>
                                    <div class="lbl">Siswa</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="num" style="color: #16a34a;">{{ $kelas->rapor_terbit }}</div>
                                    <div class="lbl">Rapor Terbit</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="num" style="color: #f59e0b;">{{ $kelas->rapor_total }}</div>
                                    <div class="lbl">Rapor Total</div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
