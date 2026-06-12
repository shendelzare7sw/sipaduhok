@extends('layouts.lms-guru')

@section('title', 'Daftar Materi')
@section('page-title', 'Materi Pembelajaran')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/materi/index.css'])
@endpush

@section('content')
<div class="guru-lms-materi-page">
    <div class="card-custom mb-4">
        <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-md-items-center gap-3">
                <div>
                    <h4 class="mb-0 fs-5 fs-md-4"><i class="fas fa-book me-2"></i>Daftar Materi</h4>
                    <p class="text-muted small mb-0">Kelola materi pembelajaran untuk kelas ini</p>
                </div>
                <div class="d-flex flex-column flex-sm-row gap-2 w-100 w-md-auto">
                    <form action="" method="GET" class="d-flex gap-2 flex-grow-1">
                        <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}" data-auto-submit>
                        @if(request('tanggal'))
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="fas fa-times"></i></a>
                        @endif
                    </form>
                    <a href="{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary btn-sm w-100 w-sm-auto">
                        <i class="fas fa-plus-circle me-1"></i><span class="d-sm-inline">Tambah Materi</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($materiList->count() > 0)
        @php
            // Group by Date for "Timeline" view
            $groupedMateri = $materiList->groupBy(function($item) {
                return $item->tanggal_upload->format('Y-m-d');
            });
        @endphp

        <div class="timeline-container">
            @foreach($groupedMateri as $date => $materis)
                <div class="position-relative mb-4">
                    <div class="d-flex align-items-center mb-3 flex-wrap gap-2">
                        <div class="bg-primary text-white rounded-pill px-2 px-sm-3 py-1 small fw-bold shadow-sm date-pill">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div class="flex-grow-1 d-none d-sm-block border-bottom"></div>
                    </div>

                    <div class="row g-2 g-md-4">
                        @foreach($materis as $materi)
                        <div class="col-12 col-sm-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body d-flex flex-column p-3 p-md-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-start gap-2 gap-md-3 flex-shrink-1 min-w-0">
                                            @php
                                                $iconClass = match($materi->tipe_file) {
                                                    'pdf' => 'fa-file-pdf text-danger',
                                                    'ppt' => 'fa-file-powerpoint text-warning',
                                                    'doc' => 'fa-file-word text-primary',
                                                    'video' => 'fa-file-video text-info',
                                                    'link' => 'fa-link text-secondary',
                                                    default => 'fa-file'
                                                };
                                            @endphp
                                            <i class="fas {{ $iconClass }} fa-lg fa-md-2x flex-shrink-0 mt-1"></i>
                                            <div class="min-w-0">
                                                <h6 class="fw-bold mb-0 text-dark text-truncate fs-6" title="{{ $materi->judul_materi }}">{{ $materi->judul_materi }}</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                                                    <span class="badge bg-light text-dark border file-badge">{{ strtoupper($materi->tipe_file) }}</span>
                                                    <small class="text-muted time-meta">
                                                        <i class="far fa-clock me-1"></i> {{ $materi->created_at->format('H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <p class="text-muted small mb-3 mb-md-4 flex-grow-1 materi-description">
                                        {{ $materi->deskripsi ?? '' }}
                                    </p>

                                    <div class="d-flex justify-content-end gap-2 mt-auto pt-2 pt-md-3 border-top">
                                        <a href="{{ route('guru.lms.materi.edit', [$kelas->id, $mapel->id, $materi->id]) }}"
                                           class="btn btn-sm btn-outline-warning px-2 px-md-3 rounded-pill">
                                            <i class="fas fa-edit me-md-1"></i> <span class="d-none d-sm-inline">Edit</span>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger px-2 px-md-3 rounded-pill"
                                            data-delete-url="{{ route('guru.lms.materi.destroy', [$kelas->id, $mapel->id, $materi->id]) }}">
                                            <i class="fas fa-trash me-md-1"></i> <span class="d-none d-sm-inline">Hapus</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-5 d-flex justify-content-center">
            {{ $materiList->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card-custom text-center py-5 border-0 shadow-sm">
            <div class="mb-3">
                <i class="fas fa-folder-open text-muted empty-icon"></i>
            </div>
            <h5 class="text-muted">Belum ada materi</h5>
            <p class="text-muted small">Mulai dengan menambahkan materi baru untuk kelas ini.</p>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus materi ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="hapus_terkait" value="1" id="hapusTerkaitCheck">
                            <label class="form-check-label small text-danger" for="hapusTerkaitCheck">
                                Hapus juga materi ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/materi/index.js'])
@endpush
