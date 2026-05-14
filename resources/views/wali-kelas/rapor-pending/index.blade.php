@extends('layouts.sneat')

@section('title', 'Rapor Pending Saya')
@section('page-title', 'Rapor Pending Saya')
@section('page-subtitle', 'Rapor draft & revisi dari semua kelas yang pernah Anda walikan')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
<style>
    .pending-summary {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }
    .pending-stat {
        background: white; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 14px;
        display: flex; align-items: center; gap: 12px;
    }
    .pending-stat .icon-circle {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: white; font-size: 1rem; flex-shrink: 0;
    }
    .pending-stat .label { font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: .5px; }
    .pending-stat .value { font-size: 1.5rem; font-weight: 800; color: #1e293b; }

    .grouped-section { margin-bottom: 28px; }
    .group-header {
        background: white; border: 1px solid #e5e7eb;
        border-bottom: none; border-radius: 10px 10px 0 0;
        padding: 12px 16px;
        font-weight: 700; color: #1e293b;
        display: flex; align-items: center; gap: 10px;
    }
    .group-header .badge-ta {
        background: rgba(67,97,238,.08); color: #4361ee;
        padding: 2px 10px; border-radius: 999px;
        font-size: 11px; font-weight: 700;
    }

    .rapor-item {
        background: white; border: 1px solid #e5e7eb;
        border-top: none;
        padding: 12px 16px;
        display: flex; gap: 14px; align-items: center;
    }
    .rapor-item:last-child { border-radius: 0 0 10px 10px; }
    .rapor-item .info { flex: 1; }
    .rapor-item .judul { font-weight: 700; color: #1e293b; font-size: 14px; }
    .rapor-item .meta { font-size: 11px; color: #64748b; margin-top: 4px; }
    .rapor-item .meta i { margin-right: 4px; color: #94a3b8; }

    .badge-status {
        padding: 3px 10px; border-radius: 999px;
        font-size: 10px; font-weight: 700;
    }
    .badge-status.draft { background: rgba(100,116,139,.1); color: #475569; }
    .badge-status.pending { background: rgba(217,119,6,.1); color: #92400e; }
    .badge-status.revisi { background: rgba(220,38,38,.1); color: #b91c1c; }

    .empty-state { text-align: center; padding: 42px 20px; color: #64748b; }
    .empty-state i { font-size: 2.4rem; color: #cbd5e1; display: block; margin-bottom: 12px; }

    @media (max-width: 767.98px) {
        .pending-summary {
            grid-template-columns: 1fr;
            gap: 10px;
        }

        .pending-stat {
            min-height: 78px;
        }

        .group-header,
        .rapor-item {
            align-items: flex-start;
            flex-direction: column;
        }

        .group-header .ms-auto {
            margin-left: 0 !important;
        }

        .rapor-item .btn {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-none">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-history me-2 text-primary"></i>Rapor Pending Saya</h4>
            <p class="text-muted mb-0">
                Rapor yang masih draft, perlu dikirim, atau diminta revisi — dari semua kelas yang pernah Anda walikan
                (termasuk TA yang sudah lewat). Akses ini ada agar rapor TA lalu tidak terjebak setelah promosi.
            </p>
        </div>
    </div>

    <div class="d-none">
        <i class="fas fa-info-circle me-2 mt-1"></i>
        <div>
            <strong>Cara kerja:</strong> Sistem menarik kelas yang pernah Anda walikan dari <code>wali_kelas_assignments</code>
            tanpa filter tahun ajaran. Klik "Buka Rapor" untuk masuk ke halaman edit rapor.
            @if($kelasIds->isEmpty())
                <br><strong class="text-warning">Anda belum pernah ditugaskan sebagai wali kelas — daftar di bawah akan kosong.</strong>
            @endif
        </div>
    </div>

    @if($kelasIds->isEmpty())
        <div class="alert alert-warning d-flex align-items-start">
            <i class="fas fa-exclamation-circle me-2 mt-1"></i>
            <div>Anda belum pernah ditugaskan sebagai wali kelas. Daftar rapor pending akan kosong.</div>
        </div>
    @endif

    <div class="pending-summary">
        <div class="pending-stat">
            <div class="icon-circle" style="background: #64748b;"><i class="fas fa-file-alt"></i></div>
            <div><div class="label">Draft Belum Dikirim</div><div class="value">{{ $totalDraft }}</div></div>
        </div>
        <div class="pending-stat">
            <div class="icon-circle" style="background: #d97706;"><i class="fas fa-paper-plane"></i></div>
            <div><div class="label">Menunggu Ketua</div><div class="value">{{ $totalKirim }}</div></div>
        </div>
        <div class="pending-stat">
            <div class="icon-circle" style="background: #dc2626;"><i class="fas fa-exclamation-triangle"></i></div>
            <div><div class="label">Diminta Revisi</div><div class="value">{{ $totalRevisi }}</div></div>
        </div>
    </div>

    @if($raporList->isEmpty())
        <div class="empty-state">
            <i class="fas fa-check-circle text-success"></i>
            <h5 class="fw-bold mb-1">Tidak Ada Rapor Pending</h5>
            <p class="mb-0">Semua rapor Anda sudah selesai/diterbitkan, atau belum ada rapor draft yang dibuat.</p>
        </div>
    @else
        @php $byKelas = $raporList->groupBy('kelas_id'); @endphp
        @foreach($byKelas as $kelasId => $rapors)
            @php $kelas = $rapors->first()->kelas; @endphp
            <div class="grouped-section">
                <div class="group-header">
                    <i class="fas fa-school text-primary"></i>
                    {{ $kelas?->nama_kelas ?? 'Kelas -' }}
                    <span class="badge-ta">TA {{ $kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}{{ $kelas?->tahunAjaran?->is_active ? ' · Aktif' : '' }}</span>
                    <span class="ms-auto small text-muted">{{ $rapors->count() }} rapor</span>
                </div>
                @foreach($rapors as $rapor)
                    <div class="rapor-item">
                        <div class="info">
                            <div class="judul">
                                {{ $rapor->siswa?->nama_lengkap ?? 'Siswa #' . $rapor->siswa_id }}
                            </div>
                            <div class="meta">
                                <i class="fas fa-calendar-alt"></i>Semester {{ $rapor->semester ?? '-' }}
                                · {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }}
                                @if($rapor->updated_at)
                                    · Terakhir diubah {{ $rapor->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}
                                @endif
                            </div>
                            @if(!empty($rapor->catatan_revisi_ketua))
                                <div class="mt-2 p-2" style="background: rgba(220,38,38,.05); border-left: 3px solid #dc2626; border-radius: 6px; font-size: 12px;">
                                    <strong class="text-danger">Catatan Ketua:</strong>
                                    {{ \Illuminate\Support\Str::limit($rapor->catatan_revisi_ketua, 200) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            @if($rapor->status_review_ketua === 'revisi')
                                <span class="badge-status revisi">Revisi</span>
                            @elseif($rapor->status_review_ketua === 'pending')
                                <span class="badge-status pending">Menunggu Ketua</span>
                            @else
                                <span class="badge-status draft">Draft</span>
                            @endif
                        </div>
                        <div>
                            <a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-pen me-1"></i>Buka Rapor
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
</div>
@endsection
