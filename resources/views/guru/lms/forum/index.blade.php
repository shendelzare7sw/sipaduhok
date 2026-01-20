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
                                        <small class="text-muted">
                                            {{ $forum->created_at->diffForHumans() }}
                                        </small>
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
@endsection