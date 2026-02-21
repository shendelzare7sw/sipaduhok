@extends('layouts.sneat')

@section('title', 'Riwayat Pengajuan Izin - ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div class="mb-3 mb-md-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('orang-tua.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Riwayat Pengajuan Izin</li>
                    </ol>
                </nav>
                <h4 class="fw-bold mb-1">Riwayat Pengajuan Izin</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                    <span class="mx-2">|</span>
                    <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                </p>
            </div>
            <div>
                <a href="{{ route('orang-tua.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali
                </a>
            </div>
        </div>

        @if($pengajuanIzin->count() > 0)
            <!-- Riwayat List -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2 text-primary"></i>
                        Riwayat Pengajuan Izin ({{ $pengajuanIzin->count() }})
                    </h5>
                </div>
                <div class="card-body p-0">
                    @foreach($pengajuanIzin as $index => $presensi)
                        @php
                            $isValidated = str_contains($presensi->keterangan, 'Divalidasi');
                            $isApproved = str_contains($presensi->keterangan, 'disetujui');
                            $isRejected = $presensi->status === 'alpha' && str_contains($presensi->keterangan, 'ditolak');

                            // Extract bukti
                            $buktiPath = null;
                            if (preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan, $matches)) {
                                $buktiPath = $matches[1];
                            }
                        @endphp

                        <div class="border-bottom p-4 {{ $index % 2 == 0 ? 'bg-white' : 'bg-light' }}">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <div
                                                class="avatar-initial rounded-circle
                                                                    {{ $isRejected ? 'bg-label-danger' : ($presensi->status == 'sakit' ? 'bg-label-warning' : 'bg-label-info') }}">
                                                <i
                                                    class="fas {{ $isRejected ? 'fa-times' : ($presensi->status == 'sakit' ? 'fa-notes-medical' : 'fa-file-alt') }}"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-2">
                                                <span
                                                    class="badge
                                                                        {{ $isRejected ? 'bg-danger' : ($presensi->status == 'sakit' ? 'bg-warning' : 'bg-info') }}">
                                                    {{ strtoupper($presensi->status) }}
                                                </span>
                                                @if($isValidated)
                                                    @if($isApproved)
                                                        <span class="badge bg-success ms-2"><i class="fas fa-check me-1"></i> Disetujui</span>
                                                    @elseif($isRejected)
                                                        <span class="badge bg-danger ms-2"><i class="fas fa-times me-1"></i> Ditolak</span>
                                                    @endif
                                                @else
                                                    <span class="badge bg-warning ms-2"><i class="fas fa-clock me-1"></i> Menunggu Validasi</span>
                                                @endif
                                            </h6>
                                            <div class="text-muted small mb-2">
                                                <i class="fas fa-calendar me-1"></i>
                                                <strong>Tanggal:</strong>
                                                {{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                            </div>
                                            <div class="text-muted small mb-2">
                                                <i class="fas fa-comment me-1"></i>
                                                <strong>Keterangan:</strong>
                                                {{ preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan) }}
                                            </div>

                                            @if($buktiPath)
                                                <div class="mt-2">
                                                    <small class="text-primary">
                                                        <i class="fas fa-paperclip me-1"></i>Ada lampiran bukti
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    @if(!$isValidated)
                                        <!-- Bisa diedit jika belum divalidasi -->
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('orang-tua.presensi.edit-izin', $presensi->id) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit me-1"></i>Edit Pengajuan
                                            </a>
                                        </div>
                                    @else
                                        <div class="alert alert-sm {{ $isApproved ? 'alert-success' : 'alert-danger' }} mb-0">
                                            <small>
                                                <strong>{{ $isApproved ? 'Sudah Disetujui' : 'Ditolak' }}</strong><br>
                                                Tidak dapat diedit lagi
                                            </small>
                                        </div>
                                    @endif
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
                        <div class="avatar-initial rounded-circle bg-label-info">
                            <i class="fas fa-inbox fa-3x"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2">Belum Ada Pengajuan Izin</h5>
                    <p class="text-muted mb-3">Anda belum pernah mengajukan izin untuk {{ $siswa->nama_lengkap }}</p>
                    <a href="{{ route('orang-tua.presensi.ajukan-izin', $siswa->id) }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>Ajukan Izin Sekarang
                    </a>
                </div>
            </div>
        @endif

    </div>
@endsection