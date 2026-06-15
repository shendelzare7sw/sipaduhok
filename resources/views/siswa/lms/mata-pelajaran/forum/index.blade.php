@extends('layouts.lms')

@section('title', 'Forum Diskusi - ' . $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Forum Diskusi')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/forum/index.css'])
@endpush

@section('content')
<div class="siswa-lms-mapel-forum-index-page">
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

    <!-- Header -->
    <div class="mb-4">
        <h4 class="forum-heading">
            <i class="fas fa-comments me-2"></i>Forum Diskusi
        </h4>
    </div>

    <!-- Forum List -->
    <div class="forum-card">
        @forelse($diskusi as $item)
            <div class="diskusi-item">
                <div class="diskusi-avatar">
                    {{ strtoupper(substr($item->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="diskusi-content">
                    <div class="diskusi-title">
                        @if($item->is_pinned)
                            <i class="fas fa-thumbtack text-warning" title="Disematkan"></i>
                        @endif
                        <a href="{{ route('siswa.lms.mapel.forum.show', [$mataPelajaran->id, $item->id]) }}">
                            {{ $item->judul }}
                        </a>
                        @if($item->is_closed)
                            <span class="badge bg-secondary">Ditutup</span>
                        @endif
                    </div>
                    <div class="diskusi-meta">
                        <span class="topik-badge topik-{{ $item->topik }}">{{ ucfirst($item->topik) }}</span>
                        <span class="ms-2">oleh <strong>{{ $item->user->name ?? 'Unknown' }}</strong></span>
                        <span class="ms-2">- {{ $item->created_at->copy()->locale('id')->diffForHumans() }}</span>
                    </div>
                    <div class="diskusi-excerpt">
                        {{ Str::limit($item->isi, 150) }}
                    </div>
                    <div class="diskusi-stats">
                        <span><i class="fas fa-reply text-primary"></i> {{ $item->replies_count ?? $item->replies->count() }}
                            balasan</span>
                        @if($item->replies->where('is_answer', true)->count() > 0)
                            <span class="text-success"><i class="fas fa-check-circle"></i> Terjawab</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-forum">
                <i class="fas fa-comments fa-3x mb-3 empty-forum-icon"></i>
                <h5>Belum Ada Diskusi</h5>
                <p>Guru belum memulai diskusi untuk mata pelajaran ini.</p>
            </div>
        @endforelse
    </div>

    @if($diskusi->hasPages())
        <div class="mt-4">
            {{ $diskusi->links() }}
        </div>
    @endif

</div>
@endsection