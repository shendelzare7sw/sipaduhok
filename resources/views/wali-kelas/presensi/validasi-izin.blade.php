@extends('layouts.sneat')

@section('title', 'Validasi Izin Ketidakhadiran')
@section('page-title', 'Validasi Izin')
@section('page-subtitle', 'Validasi pengajuan izin dan sakit siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
<style>
    .izin-empty-icon {
        width: 54px;
        height: 54px;
        margin: 0 auto 14px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: rgba(16, 185, 129, .12);
        color: #10b981;
    }

    .izin-empty-icon i {
        font-size: 1.5rem;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="d-flex justify-content-end mb-3">
        <div class="w-100 w-sm-auto">
            <a href="{{ route('wali.presensi.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali ke Presensi
            </a>
        </div>
    </div>

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
                                        @php
                                            // Extract bukti using column first, then legacy regex
                                            $buktiPath = $presensi->bukti_file;
                                            $keteranganText = $presensi->keterangan ?? '-';

                                            if (!$buktiPath && preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan, $matches)) {
                                                $buktiPath = $matches[1];
                                                $keteranganText = preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan);
                                            }
                                        @endphp

                                        <div class="text-muted small mb-3">
                                            <i class="fas fa-comment me-1"></i>
                                            <strong>Keterangan:</strong> {{ $keteranganText }}
                                        </div>

                                        @if($buktiPath)
                                            @php
                                                $extension = pathinfo($buktiPath, PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                $isPdf = strtolower($extension) === 'pdf';
                                            @endphp

                                            <div class="mb-3">
                                                <div class="card border-primary">
                                                    <div class="card-header bg-light py-2">
                                                        <small class="fw-bold text-primary">
                                                            <i class="fas fa-paperclip me-1"></i>Bukti Lampiran
                                                        </small>
                                                    </div>
                                                    <div class="card-body p-2">
                                                        @if($isImage)
                                                            <!-- Preview Gambar -->
                                                            <div class="text-center mb-2">
                                                                <img src="{{ asset('storage/' . $buktiPath) }}"
                                                                     alt="Bukti"
                                                                     class="img-fluid rounded"
                                                                     style="max-height: 200px; cursor: pointer;"
                                                                     data-bs-toggle="modal"
                                                                     data-bs-target="#previewModal{{ $presensi->id }}">
                                                            </div>
                                                            <div class="d-grid">
                                                                <a href="{{ asset('storage/' . $buktiPath) }}"
                                                                   download
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download me-1"></i>Download Gambar
                                                                </a>
                                                            </div>
                                                        @elseif($isPdf)
                                                            <!-- Preview PDF -->
                                                            <div class="d-grid gap-2">
                                                                <button type="button"
                                                                   class="btn btn-sm btn-danger"
                                                                   data-bs-toggle="modal"
                                                                   data-bs-target="#previewModalPdf{{ $presensi->id }}">
                                                                    <i class="fas fa-file-pdf me-1"></i>Lihat PDF
                                                                </button>
                                                                <a href="{{ asset('storage/' . $buktiPath) }}"
                                                                   download
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download me-1"></i>Download PDF
                                                                </a>
                                                            </div>
                                                        @else
                                                            <!-- File lainnya -->
                                                            <div class="d-grid">
                                                                <a href="{{ asset('storage/' . $buktiPath) }}"
                                                                   download
                                                                   class="btn btn-sm btn-primary">
                                                                    <i class="fas fa-download me-1"></i>Download File ({{ strtoupper($extension) }})
                                                                </a>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endif

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
                @endforeach
            </div>
        </div>

        <!-- Modals - Outside the card -->
        @foreach($pengajuanPending as $presensi)
            @php
                $buktiPath = $presensi->bukti_file;
                if (!$buktiPath && preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan ?? '', $matches)) {
                    $buktiPath = $matches[1];
                }
                $extension = $buktiPath ? pathinfo($buktiPath, PATHINFO_EXTENSION) : '';
                $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                $isPdf = strtolower($extension) === 'pdf';
            @endphp

            <!-- Modal Preview Gambar -->
            @if($buktiPath && $isImage)
                <div class="modal fade" id="previewModal{{ $presensi->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-image me-2"></i>Preview Bukti - {{ $presensi->siswa->nama_lengkap }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body text-center p-0">
                                <img src="{{ asset('storage/' . $buktiPath) }}"
                                     alt="Bukti"
                                     class="img-fluid"
                                     style="max-width: 100%; height: auto;">
                            </div>
                            <div class="modal-footer">
                                <a href="{{ asset('storage/' . $buktiPath) }}"
                                   download
                                   class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i>Download
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal Preview PDF -->
            @if($buktiPath && $isPdf)
                <div class="modal fade" id="previewModalPdf{{ $presensi->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content" style="height: 90vh;">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    <i class="fas fa-file-pdf me-2"></i>Preview Bukti PDF - {{ $presensi->siswa->nama_lengkap }}
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0 h-100">
                                <iframe src="" data-src="{{ route('wali.presensi.preview-bukti', $presensi->id) }}" width="100%" height="100%" style="border:none;"></iframe>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ asset('storage/' . $buktiPath) }}" download class="btn btn-primary">
                                    <i class="fas fa-download me-1"></i>Download PDF
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

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
    @else
        <!-- Empty State -->
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <div class="izin-empty-icon">
                    <i class="fas fa-check"></i>
                </div>
                <h5 class="fw-bold mb-2">Tidak Ada Pengajuan Pending</h5>
                <p class="text-muted mb-0">Semua pengajuan izin sudah divalidasi</p>
            </div>
        </div>
    @endif

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lazy load PDF iframes
        var modals = document.querySelectorAll('.modal');
        modals.forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                var iframe = modal.querySelector('iframe');
                if (iframe && !iframe.getAttribute('src')) {
                    iframe.setAttribute('src', iframe.getAttribute('data-src'));
                }
            });
        });
    });
</script>
@endsection
