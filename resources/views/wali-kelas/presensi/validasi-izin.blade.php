@extends('layouts.sneat')

@section('title', 'Validasi Izin Ketidakhadiran')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('wali.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('wali.presensi.index') }}">Presensi</a></li>
                    <li class="breadcrumb-item active">Validasi Izin</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Validasi Izin Ketidakhadiran</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-school me-1"></i>{{ $kelas->nama_kelas ?? '-' }}
            </p>
        </div>
        <div>
            <a href="{{ route('wali.presensi.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Presensi
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Stats Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex align-items-center">
                <div class="avatar flex-shrink-0 me-3">
                    <span class="avatar-initial rounded bg-label-warning">
                        <i class="fas fa-clock fa-lg"></i>
                    </span>
                </div>
                <div>
                    <small class="text-muted d-block">Menunggu Validasi</small>
                    <h3 class="mb-0 fw-bold text-warning">{{ $pengajuanPending->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    @if($pengajuanPending->count() > 0)
        <!-- Pengajuan List -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom">
                <h5 class="mb-0">
                    <i class="fas fa-list-check me-2 text-primary"></i>
                    Pengajuan Menunggu Validasi
                </h5>
            </div>
            <div class="card-body p-0">
                @foreach($pengajuanPending as $index => $presensi)
                    <div class="border-bottom p-4 {{ $index % 2 == 0 ? 'bg-white' : 'bg-light' }}">
                        <div class="row align-items-center">
                            <!-- Student Info -->
                            <div class="col-lg-7 mb-3 mb-lg-0">
                                <div class="d-flex align-items-start">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <div class="avatar-initial rounded-circle {{ $presensi->status == 'sakit' ? 'bg-label-warning' : 'bg-label-info' }}">
                                            <i class="fas {{ $presensi->status == 'sakit' ? 'fa-notes-medical' : 'fa-file-alt' }}"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2">
                                            {{ $presensi->siswa->nama_lengkap }}
                                            <span class="badge {{ $presensi->status == 'sakit' ? 'bg-warning' : 'bg-info' }} ms-2">
                                                {{ strtoupper($presensi->status) }}
                                            </span>
                                        </h6>
                                        <div class="text-muted small mb-2">
                                            <i class="fas fa-id-card me-1"></i>
                                            <strong>NIS:</strong> {{ $presensi->siswa->nis }}
                                        </div>
                                        <div class="text-muted small mb-2">
                                            <i class="fas fa-calendar me-1"></i>
                                            <strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                        </div>
                                        <div class="text-muted small mb-3">
                                            <i class="fas fa-comment me-1"></i>
                                            <strong>Keterangan:</strong> {{ $presensi->keterangan ?? '-' }}
                                        </div>

                                        @if($presensi->inputBy)
                                            <div class="alert alert-info py-2 px-3 mb-0 small">
                                                <i class="fas fa-user me-1"></i>
                                                Diajukan oleh: <strong>{{ $presensi->inputBy->name }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="col-lg-5">
                                <div class="d-grid gap-2">
                                    <form action="{{ route('wali.presensi.proses-validasi-izin', $presensi->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="setuju">
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-check me-2"></i>Setujui Izin
                                        </button>
                                    </form>

                                    <button type="button" class="btn btn-danger w-100"
                                            data-bs-toggle="modal"
                                            data-bs-target="#tolakModal{{ $presensi->id }}">
                                        <i class="fas fa-times me-2"></i>Tolak (Ubah ke Alpha)
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Tolak -->
                    <div class="modal fade" id="tolakModal{{ $presensi->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <form action="{{ route('wali.presensi.proses-validasi-izin', $presensi->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="tolak">

                                    <div class="modal-header bg-danger">
                                        <h5 class="modal-title text-white">
                                            <i class="fas fa-exclamation-triangle me-2"></i>Tolak Pengajuan Izin
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>

                                    <div class="modal-body">
                                        <div class="alert alert-danger border-start border-danger border-4">
                                            <div class="d-flex">
                                                <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                                                <div>
                                                    <strong>Peringatan!</strong><br>
                                                    Status presensi akan otomatis berubah menjadi <strong>ALPHA</strong>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Siswa</label>
                                            <input type="text" class="form-control" value="{{ $presensi->siswa->nama_lengkap }}" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Tanggal</label>
                                            <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}" readonly>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Alasan Penolakan (Opsional)</label>
                                            <textarea name="keterangan" class="form-control" rows="3" placeholder="Masukkan alasan penolakan..."></textarea>
                                        </div>
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            <i class="fas fa-times me-1"></i>Batal
                                        </button>
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-ban me-1"></i>Ya, Tolak Pengajuan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="avatar avatar-xl mx-auto mb-3">
                    <div class="avatar-initial rounded-circle bg-label-success">
                        <i class="fas fa-check-circle fa-3x"></i>
                    </div>
                </div>
                <h5 class="fw-bold mb-2">Tidak Ada Pengajuan Pending</h5>
                <p class="text-muted mb-0">Semua pengajuan izin sudah divalidasi</p>
            </div>
        </div>
    @endif

</div>
@endsection
