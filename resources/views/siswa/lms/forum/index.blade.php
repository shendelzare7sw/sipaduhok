@extends('layouts.lms')

@section('title', 'Forum Diskusi')
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .forum-card {
            background: white;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 12px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            transition: all 0.2s;
            border-left: 4px solid transparent;
            text-decoration: none;
            color: inherit;
            display: block;
        }

        .forum-card:hover {
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transform: translateX(3px);
            text-decoration: none;
            color: inherit;
        }

        @media (max-width: 576px) {
            .forum-card { padding: 14px; border-radius: 8px; }
            .forum-title { font-size: 14px; }
        }

        .forum-card.pinned {
            border-left-color: #f59e0b;
            background: #fffbeb;
        }

        .forum-card.closed {
            opacity: 0.7;
            border-left-color: #dc2626;
        }

        .forum-title {
            font-weight: 600;
            font-size: 16px;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .forum-meta {
            font-size: 13px;
            color: #666;
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .reply-count {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #e3f2fd;
            color: #1565c0;
            padding: 4px 12px;
            border-radius: 16px;
            font-size: 13px;
            font-weight: 500;
        }
    </style>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard LMS
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">
                <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item active">
            <i class="fas fa-comments"></i> Forum Diskusi
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="fas fa-comments me-2"></i>Forum Diskusi</h5>
    </div>

    <!-- Forum List -->
    @forelse($forums as $forum)
        <a href="{{ route('siswa.lms.mapel.forum.show', [$mataPelajaran->id, $forum->id]) }}"
            class="forum-card {{ $forum->is_pinned ? 'pinned' : '' }} {{ $forum->is_closed ? 'closed' : '' }}">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="forum-title">
                        @if($forum->is_pinned)
                            <i class="fas fa-thumbtack text-warning me-2"></i>
                        @endif
                        @if($forum->is_closed)
                            <i class="fas fa-lock text-danger me-2"></i>
                        @endif
                        {{ $forum->judul }}
                    </div>
                    <div class="forum-meta">
                        <span><i class="fas fa-user me-1"></i>{{ $forum->user->name ?? 'Guru' }}</span>
                        <span><i class="far fa-clock me-1"></i>{{ $forum->created_at->diffForHumans() }}</span>
                    </div>
                </div>
                <div class="reply-count">
                    <i class="fas fa-comment"></i>
                    {{ $forum->replies->count() }}
                </div>
            </div>
        </a>
    @empty
        <div class="card-custom text-center py-5">
            <i class="fas fa-comments text-muted" style="font-size: 60px; opacity: 0.3;"></i>
            <h5 class="mt-3 mb-2">Belum ada diskusi</h5>
            <p class="text-muted">Guru belum memulai diskusi untuk mata pelajaran ini.</p>
        </div>
    @endforelse

    @if($forums->hasPages())
        <div class="mt-4">
            {{ $forums->links() }}
        </div>
    @endif
@endsection
