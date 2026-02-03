@extends('layouts.sneat')

@section('title', 'Detail Pembayaran')
@section('page-title', 'Detail Pembayaran')
@section('page-subtitle', 'Validasi pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .info-table td { padding: 10px 0; }
    .info-table td:first-child { color: #64748b; width: 150px; }
    .student-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }
    .badge-custom { padding: 6px 12px; border-radius: 50px; font-weight: 700; font-size: 11px; }
    .validation-card {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        border: none;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('bendahara.pembayaran.index') }}" class="text-primary text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Pembayaran
        </a>
    </div>

    <div class="row">
        {{-- Info Pembayaran --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-receipt me-2"></i>Informasi Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    <table class="info-table w-100">
                        <tr>
                            <td>Kode Pembayaran</td>
                            <td><code class="fw-bold text-primary small">{{ $pembayaran->kode_pembayaran }}</code></td>
                        </tr>
                        <tr>
                            <td>Jenis Tagihan</td>
                            <td><strong>{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan ?? '-')) }}</strong></td>
                        </tr>
                        <tr>
                            <td>Jumlah Bayar</td>
                            <td class="fs-5 fw-bold text-success">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </td>
                        </tr>
                        <tr>
                            <td>Tanggal Bayar</td>
                            <td>{{ $pembayaran->tanggal_bayar ? $pembayaran->tanggal_bayar->format('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <td>Metode</td>
                            <td>
                                @if($pembayaran->metode_pembayaran === 'tunai')
                                    <span class="badge bg-primary badge-custom shadow-sm">TUNAI</span>
                                @elseif($pembayaran->metode_pembayaran === 'transfer')
                                    <span class="badge bg-success badge-custom shadow-sm">TRANSFER</span>
                                @else
                                    <span class="badge bg-info badge-custom shadow-sm">MIDTRANS</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                @if($pembayaran->status_validasi === 'pending')
                                    @if($pembayaran->metode_pembayaran === 'midtrans')
                                        @php
                                            $isExpired = $pembayaran->created_at < now()->subHours(24);
                                        @endphp
                                        @if($isExpired)
                                            <span class="badge bg-secondary badge-custom shadow-sm"><i class="fas fa-times-circle me-1"></i> KADALUARSA</span>
                                            <div class="small text-muted mt-1">Sesi pembayaran digital telah berakhir</div>
                                        @else
                                            <span class="badge bg-info badge-custom shadow-sm"><i class="fas fa-hourglass-half me-1"></i> MENUNGGU BAYAR</span>
                                            <div class="small text-muted mt-1">
                                                Berlaku hingga {{ $pembayaran->created_at->addHours(24)->format('d M Y H:i') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="badge bg-warning badge-custom text-white shadow-sm"><i class="fas fa-clock me-1"></i> MENUNGGU VALIDASI</span>
                                    @endif
                                @elseif($pembayaran->status_validasi === 'disetujui')
                                    <span class="badge bg-success badge-custom shadow-sm"><i class="fas fa-check-circle me-1"></i> DISETUJUI</span>
                                @else
                                    <span class="badge bg-danger badge-custom shadow-sm"><i class="fas fa-times-circle me-1"></i> DITOLAK</span>
                                @endif
                            </td>
                        </tr>
                        @if($pembayaran->status_validasi !== 'pending')
                            <tr>
                                <td>Divalidasi Oleh</td>
                                <td>{{ $pembayaran->validator->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td>Tanggal Validasi</td>
                                <td>{{ $pembayaran->tanggal_validasi ? $pembayaran->tanggal_validasi->format('d F Y H:i') : '-' }}</td>
                            </tr>
                        @endif
                        @if($pembayaran->catatan)
                            <tr>
                                <td>Catatan</td>
                                <td>{{ $pembayaran->catatan }}</td>
                            </tr>
                        @endif
                    </table>

                    @if($pembayaran->bukti_pembayaran)
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3 fw-bold text-gray-800"><i class="fas fa-paperclip me-2"></i>Bukti Pembayaran</h6>
                            <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-info shadow-sm">
                                <i class="fas fa-eye me-1"></i> Lihat Bukti
                            </a>
                        </div>
                    @endif

                    @if($pembayaran->status_validasi === 'disetujui')
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="mb-3 fw-bold text-gray-800"><i class="fas fa-print me-2"></i>Cetak Kwitansi</h6>
                            <a href="{{ route('bendahara.pembayaran.cetak-kwitansi', $pembayaran->id) }}" 
                               target="_blank" 
                               class="btn btn-success shadow-sm">
                                <i class="fas fa-print me-1"></i> Cetak Kwitansi Pembayaran
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Info Siswa --}}
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-user-graduate me-2"></i>Informasi Siswa
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-3 align-items-start mb-4">
                        <div class="student-avatar">
                            {{ strtoupper(substr($pembayaran->siswa->nama_lengkap ?? 'S', 0, 1)) }}
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-gray-800">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</h5>
                            <p class="mb-0 text-muted small">NISN: {{ $pembayaran->siswa->nisn ?? '-' }}</p>
                        </div>
                    </div>

                    <table class="info-table w-100">
                        <tr>
                            <td style="width: 120px;">Kelas</td>
                            <td>{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }} ({{ $pembayaran->siswa->kelas->jenjang ?? '-' }})</td>
                        </tr>
                        <tr>
                            <td>Cabang</td>
                            <td>{{ $pembayaran->siswa->cabang->nama_cabang ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td>No. Telepon</td>
                            <td>{{ $pembayaran->siswa->telepon_orangtua ?? '-' }}</td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $pembayaran->siswa->id ?? 0) }}" class="btn btn-sm btn-info shadow-sm">
                            <i class="fas fa-history me-1"></i> Lihat Riwayat Pembayaran
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Validasi (jika masih pending) --}}
    @if($pembayaran->status_validasi === 'pending')
        <div class="card shadow mb-4 validation-card">
            <div class="card-body">
                <h5 class="mb-4 fw-bold">
                    <i class="fas fa-check-circle me-2"></i>Validasi Pembayaran
                </h5>

                <form action="{{ route('bendahara.pembayaran.validasi', $pembayaran->id) }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea name="catatan" class="form-control shadow-sm" rows="3" placeholder="Tambahkan catatan jika diperlukan..." style="background: white;">{{ old('catatan') }}</textarea>
                    </div>

                    <div class="d-flex gap-2 flex-wrap">
                        <button type="submit" name="status_validasi" value="disetujui" class="btn btn-light shadow-sm fw-bold">
                            <i class="fas fa-check me-1"></i> Setujui Pembayaran
                        </button>
                        <button type="submit" name="status_validasi" value="ditolak" class="btn btn-outline-light shadow-sm fw-bold" onclick="return confirm('Yakin ingin menolak pembayaran ini?')">
                            <i class="fas fa-times me-1"></i> Tolak Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Info Tagihan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-warning">
                <i class="fas fa-file-invoice-dollar me-2"></i>Informasi Tagihan Terkait
            </h6>
        </div>
        <div class="card-body">
            @if($pembayaran->tagihan)
                <table class="info-table w-100">
                    <tr>
                        <td>Jenis Tagihan</td>
                        <td><strong>{{ ucwords(str_replace('_', ' ', $pembayaran->tagihan->jenis_tagihan)) }}</strong></td>
                    </tr>
                    <tr>
                        <td>Jumlah Tagihan</td>
                        <td class="fw-bold">Rp {{ number_format($pembayaran->tagihan->jumlah, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Jatuh Tempo</td>
                        <td>{{ $pembayaran->tagihan->tanggal_jatuh_tempo ? $pembayaran->tagihan->tanggal_jatuh_tempo->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td>Status Tagihan</td>
                        <td>
                            @if($pembayaran->tagihan->status === 'sudah_bayar')
                                <span class="badge bg-success shadow-sm">Lunas</span>
                            @elseif($pembayaran->tagihan->status === 'terlambat')
                                <span class="badge bg-danger shadow-sm">Terlambat</span>
                            @else
                                <span class="badge bg-warning shadow-sm">Belum Bayar</span>
                            @endif
                        </td>
                    </tr>
                </table>
            @else
                <p class="text-muted mb-0">Tagihan tidak ditemukan.</p>
            @endif
        </div>
    </div>

</div>
</div>
@endsection