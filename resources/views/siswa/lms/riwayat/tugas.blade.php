@extends('layouts.sneat')

@section('title', 'Detail Riwayat - ' . ($tugas->judul_tugas ?? 'Tugas'))
@section('page-title', 'Detail Riwayat Tugas')
@section('page-subtitle', 'Pengumpulan dan nilai dari TA sebelumnya')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .riwayat-banner {
        background: linear-gradient(135deg, #6b7280, #4b5563);
        color: white; border-radius: 12px;
        padding: 16px 20px; margin-bottom: 18px;
        display: flex; align-items: center; gap: 14px;
    }
    .riwayat-banner i { font-size: 1.4rem; }
    .riwayat-banner .title { font-weight: 700; font-size: 0.95rem; }
    .riwayat-banner .subtitle { font-size: 0.78rem; opacity: 0.9; }

    .detail-card {
        background: white; border: 1px solid #e5e7eb;
        border-radius: 12px; padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .detail-section { margin-bottom: 22px; }
    .detail-label {
        font-size: 11px; font-weight: 700;
        color: #64748b; text-transform: uppercase;
        letter-spacing: .5px; margin-bottom: 6px;
    }
    .detail-body { color: #1e293b; line-height: 1.7; font-size: 14px; }
    .meta-pills {
        display: flex; flex-wrap: wrap; gap: 8px;
        margin-bottom: 18px; padding-bottom: 18px;
        border-bottom: 1px solid #e5e7eb;
    }
    .meta-pill {
        background: #f1f5f9; color: #475569;
        padding: 4px 12px; border-radius: 999px;
        font-size: 12px;
    }
    .meta-pill i { margin-right: 4px; color: #94a3b8; }

    .nilai-box {
        background: rgba(22,163,74,.05);
        border: 1px solid rgba(22,163,74,.3);
        border-radius: 10px;
        padding: 18px;
        text-align: center;
    }
    .nilai-box .label { font-size: 11px; text-transform: uppercase; color: #64748b; font-weight: 700; }
    .nilai-box .value { font-size: 2.5rem; font-weight: 800; color: #15803d; line-height: 1; }
    .nilai-box.belum { background: rgba(100,116,139,.05); border-color: rgba(100,116,139,.3); }
    .nilai-box.belum .value { color: #64748b; }

    .file-link {
        display: inline-flex; align-items: center; gap: 8px;
        background: #f8fafc; border: 1px solid #e5e7eb;
        padding: 10px 14px; border-radius: 8px;
        text-decoration: none; color: #1e293b;
        font-size: 13px; font-weight: 500;
    }
    .file-link:hover { border-color: #4361ee; color: #4361ee; }

    .feedback-box {
        background: rgba(67,97,238,.05);
        border-left: 3px solid #4361ee;
        padding: 12px 16px;
        border-radius: 8px;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.riwayat.index') }}">Riwayat LMS</a></li>
            <li class="breadcrumb-item active">Detail Tugas</li>
        </ol>
    </nav>

    <div class="riwayat-banner">
        <i class="fas fa-history"></i>
        <div>
            <div class="title">Mode Riwayat — Read Only</div>
            <div class="subtitle">Anda hanya bisa melihat. Tidak bisa submit ulang dari halaman ini.</div>
        </div>
    </div>

    <div class="detail-card">
        <h4 class="fw-bold mb-2">
            <i class="fas fa-tasks me-2" style="color: #d97706;"></i>{{ $tugas->judul_tugas ?? '-' }}
        </h4>

        <div class="meta-pills">
            @if($tugas->kelas?->tahunAjaran)
                <span class="meta-pill"><i class="fas fa-calendar-alt"></i>TA {{ $tugas->kelas->tahunAjaran->nama_tahun_ajaran }}</span>
            @endif
            @if($tugas->kelas)
                <span class="meta-pill"><i class="fas fa-school"></i>{{ $tugas->kelas->nama_kelas }}</span>
            @endif
            @if($tugas->mataPelajaran)
                <span class="meta-pill"><i class="fas fa-book"></i>{{ $tugas->mataPelajaran->nama_mapel }}</span>
            @endif
            @if($tugas->guru)
                <span class="meta-pill"><i class="fas fa-user-tie"></i>{{ $tugas->guru->nama_lengkap }}</span>
            @endif
            @if($tugas->tanggal_deadline)
                <span class="meta-pill"><i class="fas fa-flag-checkered"></i>Deadline: {{ \Carbon\Carbon::parse($tugas->tanggal_deadline)->locale('id')->translatedFormat('d M Y') }}</span>
            @endif
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                @if($tugasSiswa->nilai !== null)
                    <div class="nilai-box">
                        <div class="label">Nilai Anda</div>
                        <div class="value">{{ number_format((float) $tugasSiswa->nilai, 1) }}</div>
                    </div>
                @else
                    <div class="nilai-box belum">
                        <div class="label">Status</div>
                        <div class="value" style="font-size: 1.1rem;">{{ str_replace('_', ' ', $tugasSiswa->status ?? '-') }}</div>
                    </div>
                @endif
            </div>
            <div class="col-md-8">
                <div class="detail-section">
                    <div class="detail-label">Submit Saya</div>
                    @if($tugasSiswa->tanggal_submit)
                        <div class="detail-body">
                            <i class="fas fa-clock me-1 text-muted"></i>
                            Dikumpulkan: {{ $tugasSiswa->tanggal_submit->locale('id')->translatedFormat('d M Y, H:i') }}
                            @if($tugasSiswa->isLate())
                                <span class="badge bg-warning ms-2">Terlambat</span>
                            @endif
                            @if($tugasSiswa->pengulangan_ke)
                                <small class="text-muted ms-2">(Pengulangan ke-{{ $tugasSiswa->pengulangan_ke }})</small>
                            @endif
                        </div>
                    @else
                        <div class="text-muted">Belum dikumpulkan.</div>
                    @endif
                </div>
            </div>
        </div>

        @if($tugas->deskripsi)
            <div class="detail-section">
                <div class="detail-label">Instruksi Tugas</div>
                <div class="detail-body">{!! nl2br(e($tugas->deskripsi)) !!}</div>
            </div>
        @endif

        @if($tugasSiswa->jawaban_text)
            <div class="detail-section">
                <div class="detail-label">Jawaban / Catatan Saya</div>
                <div class="detail-body">{!! nl2br(e($tugasSiswa->jawaban_text)) !!}</div>
            </div>
        @endif

        @if($tugasSiswa->file_jawaban)
            <div class="detail-section">
                <div class="detail-label">Berkas yang Saya Upload</div>
                <a href="{{ asset('storage/' . $tugasSiswa->file_jawaban) }}" target="_blank" rel="noopener" class="file-link">
                    <i class="fas fa-file"></i>
                    {{ basename($tugasSiswa->file_jawaban) }}
                </a>
            </div>
        @endif

        @if($tugasSiswa->feedback_guru)
            <div class="detail-section">
                <div class="detail-label">Feedback dari Guru</div>
                <div class="feedback-box">{!! nl2br(e($tugasSiswa->feedback_guru)) !!}</div>
            </div>
        @endif

        <div class="text-end">
            <a href="{{ route('siswa.lms.riwayat.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Riwayat
            </a>
        </div>
    </div>
</div>
@endsection
