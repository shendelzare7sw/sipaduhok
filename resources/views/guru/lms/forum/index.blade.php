@extends('layouts.lms-guru')

@section('title', 'Forum Diskusi')
@section('page-title', 'Diskusi Kelas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/forum/index.css'])
@endpush

@section('content')
<div class="guru-lms-forum-index-page" data-csrf-token="{{ csrf_token() }}">
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
                                            <small class="text-muted">{{ $forum->created_at->copy()->locale('id')->diffForHumans() }}</small>
                                            <div class="dropdown d-inline-block">
                                                <button class="btn btn-sm btn-link text-muted p-0 px-1" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <button class="dropdown-item" data-forum-sync-url="{{ route('guru.lms.forum.togglePin', [$kelas->id, $mapel->id, $forum->id]) }}" data-forum-sync-title="{{ $forum->is_pinned ? 'Unpin Diskusi' : 'Pin Diskusi' }}">
                                                            <i class="fas fa-thumbtack me-2 {{ $forum->is_pinned ? 'text-secondary' : 'text-warning' }}"></i>
                                                            {{ $forum->is_pinned ? 'Lepas Pin' : 'Pin Diskusi' }}
                                                        </button>
                                                    </li>
                                                    <li>
                                                        <button class="dropdown-item" data-forum-sync-url="{{ route('guru.lms.forum.toggleClose', [$kelas->id, $mapel->id, $forum->id]) }}" data-forum-sync-title="{{ $forum->is_closed ? 'Buka Diskusi' : 'Tutup Diskusi' }}">
                                                            <i class="fas fa-{{ $forum->is_closed ? 'lock-open' : 'lock' }} me-2 {{ $forum->is_closed ? 'text-success' : 'text-secondary' }}"></i>
                                                            {{ $forum->is_closed ? 'Buka Kembali' : 'Tutup Diskusi' }}
                                                        </button>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <button class="dropdown-item text-danger" data-forum-delete-url="{{ route('guru.lms.forum.destroy', [$kelas->id, $mapel->id, $forum->id]) }}">
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
                                                <div class="forum-avatar {{ $forum->isFromTeacher() ? 'bg-success' : 'bg-primary' }} text-white">
                                                    {{ substr($forum->user->name ?? 'U', 0, 1) }}
                                                </div>
                                                <small class="fw-semibold text-secondary">
                                                    {{ $forum->user->name ?? 'Unknown' }}
                                                    @if($forum->isFromTeacher())
                                                        <span class="badge bg-success ms-1 teacher-badge">Guru</span>
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
                            <i class="fas fa-comments text-muted empty-forum-icon"></i>
                            <p class="text-muted mt-3 mb-0">Belum ada diskusi di kelas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/forum/index.js'])
@endpush
