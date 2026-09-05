@extends('layouts.app')

@section('title', 'Rapor')
@section('page-title', 'E-Rapor Digital')
@section('page-subtitle', 'Hasil capaian belajar siswa per semester')


@section('styles')
<style>
    /* === 1. STATUS AKSES BOX === */
    .status-access-card {
        border: none;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .status-header {
        padding: 15px 25px;
        background: #f8f9fc;
        border-bottom: 1px solid #edf2f9;
        font-weight: 800;
        color: #4e73df;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }
    .validation-step {
        display: flex;
        align-items: center;
        padding: 15px 25px;
        border-bottom: 1px solid #f1f5f9;
        transition: background 0.3s;
    }
    .validation-step:last-child { border-bottom: none; }
    .step-icon {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-size: 1.2rem;
    }
    .step-success { background: #e6fffa; color: #38b2ac; }
    .step-pending { background: #fff5f5; color: #e53e3e; }

    /* === 2. RAPOR DOCUMENT CARDS === */
    .document-card {
        background: white;
        border-radius: 15px;
        border: 1px solid #e3e6f0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .document-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #4e73df;
    }
    .doc-icon-container {
        font-size: 3rem;
        color: #e3e6f0;
        position: absolute;
        right: -10px;
        bottom: -10px;
        opacity: 0.3;
        transform: rotate(-15deg);
    }
    .locked-overlay {
        background: rgba(255, 255, 255, 0.6);
        backdrop-filter: blur(2px);
        border-radius: 15px;
    }

    /* Gradient Button */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        color: white;
    }
    .btn-gradient-success {
        background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
        border: none;
        color: white;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0" style="max-width: 1100px; margin: 0 auto;">

    <div class="row mb-4">
        <div class="col-12">
            <div class="status-access-card shadow-sm">
                <div class="status-header">
                    <i class="fas fa-user-shield me-2"></i> Gerbang Validasi E-Rapor
                </div>
                <div class="card-body p-0">
                    <div class="row g-0">
                        <div class="col-md-6 border-end">
                            <div class="validation-step">
                                <div class="step-icon {{ $aksesRapor['bendahara'] ? 'step-success' : 'step-pending' }}">
                                    <i class="fas fa-money-check-alt"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0">Validasi Administrasi</div>
                                    <div class="text-xs {{ $aksesRapor['bendahara'] ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $aksesRapor['bendahara'] ? 'PEMBAYARAN LUNAS' : 'MENUNGGU PELUNASAN' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="validation-step">
                                <div class="step-icon {{ $aksesRapor['wali'] ? 'step-success' : 'step-pending' }}">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0">Validasi Wali Kelas</div>
                                    <div class="text-xs {{ $aksesRapor['wali'] ? 'text-success' : 'text-danger' }} fw-bold">
                                        {{ $aksesRapor['wali'] ? 'DATA TELAH DIVALIDASI' : 'PROSES PENILAIAN' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if($bolehLihat)
                    <div class="bg-success text-white text-center py-2 small fw-bold">
                        <i class="fas fa-lock-open me-2"></i> AKSES DIBUKA: SILAKAN UNDUH RAPOR ANDA
                    </div>
                @else
                    <div class="bg-warning text-dark text-center py-2 small fw-bold">
                        <i class="fas fa-lock me-2"></i> AKSES TERKUNCI: LENGKAPI VALIDASI DI ATAS
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h6 class="m-0 fw-bold text-muted"><i class="fas fa-copy me-2 text-primary"></i>Arsip Rapor Digital</h6>
        <span class="badge bg-primary px-3 shadow-sm">{{ count($raporList) }} DOKUMEN</span>
    </div>

    <div class="row">
        @forelse($raporList as $rapor)
            <div class="col-md-6 mb-4">
                <div class="document-card shadow-sm h-100 {{ !$bolehLihat ? 'opacity-75' : '' }}">
                    <div class="card-body p-4 position-relative">
                        <div class="doc-icon-container">
                            <i class="fas fa-file-pdf"></i>
                        </div>

                        <div class="row align-items-start">
                            <div class="col">
                                <span class="badge bg-info px-2 py-1 mb-2 fw-bold" style="font-size: 10px;">
                                    SEMESTER {{ strtoupper($rapor->semester) }}
                                </span>
                                <h5 class="fw-bold text-dark mb-1">T.A {{ $rapor->tahunAjaran->nama_tahun_ajaran }}</h5>
                                <div class="small text-muted mb-3">
                                    <i class="fas fa-chalkboard-teacher me-1"></i> {{ $rapor->kelas->nama_kelas }}
                                </div>
                                <div class="d-flex align-items-center text-xs text-muted mb-4">
                                    <i class="far fa-calendar-alt me-1"></i> Rilis: {{ $rapor->tanggal_terbit ? $rapor->tanggal_terbit->translatedFormat('d F Y') : '-' }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            @if($bolehLihat)
                                <div class="col-6">
                                    <a href="{{ route('siswa.sia.rapor.akhir-semester', $rapor->id) }}" class="btn btn-gradient-primary btn-sm w-100 fw-bold shadow-sm py-2">
                                        <i class="fas fa-eye me-1"></i> LIHAT
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('siswa.sia.rapor.download', $rapor->id) }}" class="btn btn-gradient-success btn-sm w-100 fw-bold shadow-sm py-2">
                                        <i class="fas fa-download me-1"></i> CETAK
                                    </a>
                                </div>
                            @else
                                <div class="col-12 text-center py-2 bg-light rounded border">
                                    <i class="fas fa-lock me-2 text-muted"></i>
                                    <span class="small fw-bold text-muted">Akses Dokumen Belum Terbuka</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card shadow-sm border-0 py-5 text-center bg-white" style="border-radius: 15px;">
                    <div class="card-body">
                        <i class="fas fa-folder-open fa-4x text-muted mb-3" style="opacity: 0.2;"></i>
                        <h5 class="text-muted fw-bold">Belum ada rapor yang diterbitkan</h5>
                        <p class="small text-muted px-5">Rapor digital Anda akan muncul di sini secara otomatis setelah periode penilaian selesai dan divalidasi oleh sekolah.</p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>

    <div class="card border-start-info shadow-sm mt-2 mb-5" style="border-radius: 10px;">
        <div class="card-body py-3">
            <h6 class="fw-bold text-info small mb-2"><i class="fas fa-info-circle me-2"></i>INFORMASI PENGGUNAAN</h6>
            <ul class="text-muted small mb-0 ps-3">
                <li>Rapor dapat diakses penuh (Lihat & Cetak) hanya jika status **Bendahara** dan **Wali Kelas** sudah berwarna hijau.</li>
                <li>Pastikan seluruh kewajiban administrasi sekolah sudah diselesaikan untuk membuka gembok akses otomatis.</li>
                <li>Jika terdapat perbedaan data nilai, silakan ajukan sanggah langsung kepada Guru Mata Pelajaran terkait.</li>
            </ul>
        </div>
    </div>

</div>
</div>
@endsection
