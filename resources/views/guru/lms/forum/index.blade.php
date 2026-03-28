@extends('layouts.lms-guru')

@section('title', 'Forum Diskusi')
@section('page-title', 'Diskusi Kelas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
<style>
    .forum-list-item { padding: 16px !important; }
    .forum-list-item .forum-header {
        display: flex; justify-content: space-between; align-items: flex-start;
        margin-bottom: 10px; gap: 8px; flex-wrap: wrap;
    }
    .forum-list-item .forum-badges { display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
    .forum-list-item .forum-meta-right { display: flex; align-items: center; gap: 8px; flex-shrink: 0; }
    .forum-list-item h5 { font-size: 15px; margin-bottom: 6px; }
    .forum-list-item p { font-size: 13px; margin-bottom: 10px; line-height: 1.5; }
    .forum-footer { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 8px; }
    @media (max-width: 576px) {
        .forum-list-item { padding: 14px !important; }
        .forum-list-item h5 { font-size: 14px; }
        .forum-meta-right small { display: none; }
    }
</style>
@endpush

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i>Daftar Diskusi Kelas</h6>
                    <a href="{{ route('guru.lms.forum.create', [$kelas->id, $mapel->id]) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-plus me-1"></i>Buat Diskusi
                    </a>
                </div>
                <div class="p-0">
                    @if($forums->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($forums as $forum)
                                <a href="{{ route('guru.lms.forum.show', [$kelas->id, $mapel->id, $forum->id]) }}"
                                    class="list-group-item list-group-item-action forum-list-item {{ $forum->is_pinned ? 'bg-light' : '' }}">

                                    {{-- Header: badges kiri, waktu+dropdown kanan --}}
                                    <div class="forum-header">
                                        <div class="forum-badges">
                                            @if($forum->is_pinned)
                                                <span class="badge bg-warning text-dark"><i class="fas fa-thumbtack me-1"></i>Pinned</span>
                                            @endif
                                            @if($forum->is_closed)
                                                <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Closed</span>
                                            @endif
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                {{ ucfirst($forum->topik) }}
                                            </span>
                                        </div>
                                        <div class="forum-meta-right">
                                            <small class="text-muted">{{ $forum->created_at->diffForHumans() }}</small>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-link text-muted p-0 px-1" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button class="dropdown-item" onclick="event.preventDefault(); event.stopPropagation(); confirmSyncForum('{{ route('guru.lms.forum.togglePin', [$kelas->id, $mapel->id, $forum->id]) }}', '{{ $forum->is_pinned ? 'Unpin Diskusi' : 'Pin Diskusi' }}')">
                                                            <i class="fas fa-thumbtack me-2 {{ $forum->is_pinned ? 'text-secondary' : 'text-warning' }}"></i>
                                                            {{ $forum->is_pinned ? 'Lepas Pin' : 'Pin Diskusi' }}
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item" onclick="event.preventDefault(); event.stopPropagation(); confirmSyncForum('{{ route('guru.lms.forum.toggleClose', [$kelas->id, $mapel->id, $forum->id]) }}', '{{ $forum->is_closed ? 'Buka Diskusi' : 'Tutup Diskusi' }}')">
                                                            <i class="fas fa-{{ $forum->is_closed ? 'lock-open' : 'lock' }} me-2 {{ $forum->is_closed ? 'text-success' : 'text-secondary' }}"></i>
                                                            {{ $forum->is_closed ? 'Buka Kembali' : 'Tutup Diskusi' }}
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" onclick="event.preventDefault(); event.stopPropagation(); confirmDelete('{{ route('guru.lms.forum.destroy', [$kelas->id, $mapel->id, $forum->id]) }}')">
                                                            <i class="fas fa-trash me-2"></i>Hapus
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <h5 class="fw-bold text-dark">{{ $forum->judul }}</h5>

                                    <p class="text-muted">{{ Str::limit(strip_tags($forum->isi), 130) }}</p>

                                    <div class="forum-footer">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="{{ $forum->isFromTeacher() ? 'bg-success' : 'bg-primary' }} text-white"
                                                    style="width:22px; height:22px; font-size:10px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    {{ substr($forum->user->name ?? 'U', 0, 1) }}
                                                </div>
                                                <small class="fw-semibold text-secondary">
                                                    {{ $forum->user->name ?? 'Unknown' }}
                                                    @if($forum->isFromTeacher())
                                                        <span class="badge bg-success ms-1" style="font-size:8px;">Guru</span>
                                                    @endif
                                                </small>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-comment-alt me-1"></i>
                                                {{ $forum->replies_count ?? $forum->replies()->count() }} Balasan
                                            </small>
                                        </div>
                                        <small class="text-success fw-bold">
                                            Lihat <i class="fas fa-arrow-right ms-1"></i>
                                        </small>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="p-3">
                            {{ $forums->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-comments text-muted" style="font-size: 48px; opacity: 0.2;"></i>
                            <p class="text-muted mt-3 mb-0">Belum ada diskusi di kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->


    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(url) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin menghapus diskusi ini?</p>
                        <div class="form-check">
                            <input class="form-check-input border border-secondary" type="checkbox" id="swal-hapus-terkait" value="1">
                            <label class="form-check-label text-danger small" for="swal-hapus-terkait">
                                Hapus juga diskusi ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                    </div>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return document.getElementById('swal-hapus-terkait').checked;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(method);

                    if (result.value) {
                        const hapusTerkait = document.createElement('input');
                        hapusTerkait.type = 'hidden';
                        hapusTerkait.name = 'hapus_terkait';
                        hapusTerkait.value = '1';
                        form.appendChild(hapusTerkait);
                    }

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmSyncForum(url, title) {
            Swal.fire({
                title: title,
                html: `
                    <div class="text-start">
                        <p class="mb-3">Apakah Anda yakin ingin melakukan tindakan ini?</p>
                        <div class="alert alert-info py-2 mb-0">
                            <div class="form-check mb-0">
                                <input class="form-check-input border border-primary border-2" type="checkbox" id="swal-sync-forum" checked>
                                <label class="form-check-label fw-bold text-primary" for="swal-sync-forum">
                                    Terapkan juga ke kelas lain?
                                </label>
                            </div>
                            <small class="d-block mt-1 text-muted">
                                Aksi akan diterapkan pada diskusi dengan judul yang sama di kelas yang Anda ampu (jika ada).
                            </small>
                        </div>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    return document.getElementById('swal-sync-forum').checked;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';
                    
                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const syncKelas = document.createElement('input');
                    syncKelas.type = 'hidden';
                    syncKelas.name = 'sync_kelas';
                    syncKelas.value = result.value ? '1' : '0';
                    form.appendChild(syncKelas);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
    @endpush
@endsection