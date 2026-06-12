@extends('layouts.sneat')

@section('title', 'Riwayat Pengajuan Izin - ' . $siswa->nama_lengkap)
@section('page-title', 'Riwayat Pengajuan Izin')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/orang-tua/presensi/riwayat-izin.css'])
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y permission-history-page">

        <!-- Page Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
            <div class="mb-3 mb-md-0">
                <h4 class="fw-bold mb-1">Riwayat Pengajuan Izin</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                    <span class="mx-2">|</span>
                    <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                </p>
            </div>
            <div>
                <a href="{{ route('orang-tua.presensi.anak', $siswa->id) }}" class="btn btn-outline-secondary btn-sm">
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
                            $statusValidasi = $presensi->status_validasi;
                            if (!$statusValidasi) {
                                if (str_contains($presensi->keterangan ?? '', 'ditolak')) {
                                    $statusValidasi = 'ditolak';
                                } elseif (str_contains($presensi->keterangan ?? '', 'Divalidasi')) {
                                    $statusValidasi = 'disetujui';
                                }
                            }

                            $isValidated = in_array($statusValidasi, ['disetujui', 'ditolak']);
                            $isApproved = $statusValidasi === 'disetujui';
                            $isRejected = $statusValidasi === 'ditolak';

                            // Extract bukti from column first, then legacy keterangan text.
                            $buktiPath = $presensi->bukti_file;
                            if (!$buktiPath && preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan ?? '', $matches)) {
                                $buktiPath = $matches[1];
                            }

                            $keteranganText = preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan ?? '-');
                            $buktiUrl = $buktiPath ? asset('storage/' . $buktiPath) : null;
                            $buktiExtension = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                            $isBuktiImage = in_array($buktiExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isBuktiPdf = $buktiExtension === 'pdf';
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
                                                {{ $keteranganText }}
                                            </div>

                                            @if($buktiPath)
                                                <div class="mt-2">
                                                    <button type="button"
                                                            class="btn btn-outline-primary btn-sm"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#buktiModal{{ $presensi->id }}">
                                                        <i class="fas fa-paperclip me-1"></i>Lihat Bukti
                                                    </button>
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

                        @if($buktiPath)
                            <div class="modal fade" id="buktiModal{{ $presensi->id }}" tabindex="-1" aria-labelledby="buktiModalLabel{{ $presensi->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="buktiModalLabel{{ $presensi->id }}">
                                                <i class="fas fa-paperclip me-2"></i>Lampiran Bukti
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            @if($isBuktiImage)
                                                <div class="text-center p-3">
                                                    <img src="{{ $buktiUrl }}"
                                                         alt="Lampiran bukti {{ $siswa->nama_lengkap }}"
                                                         class="img-fluid rounded permission-proof-image">
                                                </div>
                                            @elseif($isBuktiPdf)
                                                <iframe src="{{ $buktiUrl }}"
                                                        title="Lampiran bukti {{ $siswa->nama_lengkap }}"
                                                        class="permission-proof-frame"></iframe>
                                            @else
                                                <div class="text-center p-5">
                                                    <i class="fas fa-file fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Format lampiran tidak dapat dipreview.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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
