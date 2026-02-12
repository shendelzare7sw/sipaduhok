@extends('layouts.lms-guru')

@section('title', 'Daftar Materi')
@section('page-title', 'Materi Pembelajaran')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="card-custom mb-4">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h4 class="mb-0"><i class="fas fa-book me-2"></i>Daftar Materi</h4>
                    <p class="text-muted small mb-0">Kelola materi pembelajaran untuk kelas ini</p>
                </div>
                <div class="d-flex gap-2">
                    <form action="" method="GET" class="d-flex gap-2">
                        <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ request('tanggal') }}" onchange="this.form.submit()">
                        @if(request('tanggal'))
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-outline-secondary" title="Reset Filter"><i class="fas fa-times"></i></a>
                        @endif
                    </form>
                    <a href="{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus-circle me-1"></i>Tambah Materi
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
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary text-white rounded-pill px-3 py-1 small fw-bold shadow-sm">
                            {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM Y') }}
                        </div>
                        <div class="flex-grow-1 ms-3 border-bottom"></div>
                    </div>

                    <div class="row g-4">
                        @foreach($materis as $materi)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 border-0 shadow-sm hover-shadow transition-all">
                                <div class="card-body d-flex flex-column p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div class="d-flex align-items-center">
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
                                            <i class="fas {{ $iconClass }} fa-2x me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-0 text-dark">{{ $materi->judul_materi }}</h6>
                                                <div class="d-flex align-items-center gap-2 mt-1">
                                                    <span class="badge bg-light text-dark border">{{ strtoupper($materi->tipe_file) }}</span>
                                                    <small class="text-muted" style="font-size: 0.8rem;">
                                                        <i class="far fa-clock me-1"></i> {{ $materi->created_at->format('H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted small mb-4 flex-grow-1" style="line-height: 1.6;">
                                        {{ Str::limit($materi->deskripsi, 120, '...') }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-end gap-2 mt-auto pt-3 border-top">
                                        <a href="{{ route('guru.lms.materi.edit', [$kelas->id, $mapel->id, $materi->id]) }}" 
                                           class="btn btn-sm btn-outline-warning px-3 rounded-pill">
                                            <i class="fas fa-edit me-1"></i> Edit
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-pill" 
                                            onclick="confirmDelete('{{ route('guru.lms.materi.destroy', [$kelas->id, $mapel->id, $materi->id]) }}')">
                                            <i class="fas fa-trash me-1"></i> Hapus
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
                <i class="fas fa-folder-open text-muted" style="font-size: 64px; opacity: 0.3;"></i>
            </div>
            <h5 class="text-muted">Belum ada materi</h5>
            <p class="text-muted small">Mulai dengan menambahkan materi baru untuk kelas ini.</p>
        </div>
    @endif

    <style>
        .hover-shadow:hover {
            transform: translateY(-5px);
            box_shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
        }
        .transition-all {
            transition: all 0.3s ease;
        }
    </style>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
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
                    <form id="deleteForm" method="POST" style="display: inline;">
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

    @push('scripts')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            // Reset state
            document.getElementById('hapusTerkaitCheck').checked = false;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }
    </script>
    @endpush
@endsection