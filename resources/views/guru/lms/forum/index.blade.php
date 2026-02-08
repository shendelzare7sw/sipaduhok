@extends('layouts.lms-guru')

@section('title', 'Forum Diskusi')
@section('page-title', 'Diskusi Kelas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i>Daftar Diskusi Kelas</h6>
                    <a href="{{ route('guru.lms.forum.create', [$kelas->id, $mapel->id]) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Buat Diskusi
                    </a>
                </div>
                <div class="p-0">
                    @if($forums->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($forums as $forum)
                                <a href="{{ route('guru.lms.forum.show', [$kelas->id, $mapel->id, $forum->id]) }}"
                                    class="list-group-item list-group-item-action p-4 {{ $forum->is_pinned ? 'bg-light' : '' }}">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="d-flex align-items-center gap-2">
                                            @if($forum->is_pinned)
                                                <span class="badge bg-warning text-dark"><i
                                                        class="fas fa-thumbtack me-1"></i>Pinned</span>
                                            @endif
                                            @if($forum->is_closed)
                                                <span class="badge bg-secondary"><i class="fas fa-lock me-1"></i>Closed</span>
                                            @endif
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                                {{ ucfirst($forum->topik) }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <small class="text-muted">{{ $forum->created_at->diffForHumans() }}</small>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-link text-muted p-0 px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
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

                                    <h5 class="fw-bold text-dark mb-2">
                                        {{ $forum->judul }}
                                    </h5>

                                    <p class="text-muted mb-3" style="font-size: 14px; line-height: 1.6;">
                                        {{ Str::limit(strip_tags($forum->isi), 150) }}
                                    </p>

                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle-sm {{ $forum->isFromTeacher() ? 'bg-success' : 'bg-primary' }} text-white"
                                                    style="width: 24px; height: 24px; font-size: 10px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                    {{ substr($forum->user->name ?? 'U', 0, 1) }}
                                                </div>
                                                <small class="fw-semibold text-secondary">
                                                    {{ $forum->user->name ?? 'Unknown' }}
                                                    @if($forum->isFromTeacher())
                                                        <span class="badge bg-success ms-1" style="font-size: 8px;">Guru</span>
                                                    @endif
                                                </small>
                                            </div>

                                            <div class="d-flex align-items-center gap-2 text-muted small">
                                                <i class="fas fa-comment-alt"></i>
                                                {{ $forum->replies_count ?? $forum->replies()->count() }} Balasan
                                            </div>
                                        </div>

                                        <div class="text-primary small fw-bold">
                                            Lihat Diskusi <i class="fas fa-arrow-right ms-1"></i>
                                        </div>
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
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus diskusi ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <div class="form-check mb-3 text-start">
                            <input class="form-check-input" type="checkbox" name="hapus_terkait" value="1" id="hapusTerkaitCheck">
                            <label class="form-check-label small text-danger" for="hapusTerkaitCheck">
                                Hapus juga diskusi ini dari kelas lain? (Jika ada duplikat)
                            </label>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Sync Confirmation Modal -->
    <div class="modal fade" id="syncForumModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="syncForumTitle">Konfirmasi Aksi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin melakukan tindakan ini?</p>
                    <div class="alert alert-info py-2 mb-0">
                        <div class="form-check mb-0">
                            <input class="form-check-input" type="checkbox" id="syncForumCheck" checked>
                            <label class="form-check-label fw-bold" for="syncForumCheck">
                                Terapkan juga ke kelas lain?
                            </label>
                        </div>
                        <small class="d-block mt-1 text-muted">
                            Aksi akan diterapkan pada diskusi dengan judul yang sama di kelas yang Anda ampu (jika ada).
                        </small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="syncForumForm" method="POST">
                        @csrf
                        {{-- Method PUT/PATCH/POST handled by route mostly, but toggle is usually POST --}}
                        <input type="hidden" name="sync_kelas" id="syncForumInput" value="1">
                        <button type="submit" class="btn btn-primary">Ya, Lanjutkan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function confirmDelete(url) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('hapusTerkaitCheck').checked = false;
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        function confirmSyncForum(url, title) {
            document.getElementById('syncForumForm').action = url;
            document.getElementById('syncForumTitle').innerText = title;
            
            // Default checked
            document.getElementById('syncForumCheck').checked = true;
            document.getElementById('syncForumInput').value = '1';

            var modal = new bootstrap.Modal(document.getElementById('syncForumModal'));
            modal.show();
        }

        // Handle checkbox change in modal
        document.getElementById('syncForumCheck').addEventListener('change', function() {
            document.getElementById('syncForumInput').value = this.checked ? '1' : '0';
        });
    </script>
    @endpush
@endsection