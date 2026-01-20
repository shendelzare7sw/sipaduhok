@extends('layouts.lms')

@section('title', 'Forum Diskusi - ' . $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Forum Diskusi')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .forum-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .diskusi-item {
            padding: 20px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            gap: 16px;
            transition: background 0.2s;
        }

        .diskusi-item:hover {
            background: #f9fafb;
        }

        .diskusi-item:last-child {
            border-bottom: none;
        }

        .diskusi-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #0d3f7a);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }

        .diskusi-content {
            flex: 1;
        }

        .diskusi-title {
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .diskusi-title a {
            color: inherit;
            text-decoration: none;
        }

        .diskusi-title a:hover {
            color: var(--primary);
        }

        .diskusi-meta {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 8px;
        }

        .diskusi-excerpt {
            font-size: 14px;
            color: #4b5563;
            line-height: 1.5;
        }

        .diskusi-stats {
            display: flex;
            gap: 16px;
            font-size: 13px;
            color: #6b7280;
            margin-top: 10px;
        }

        .topik-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
        }

        .topik-materi {
            background: #dbeafe;
            color: #1e40af;
        }

        .topik-tugas {
            background: #fef3c7;
            color: #92400e;
        }

        .topik-ujian {
            background: #fecaca;
            color: #991b1b;
        }

        .topik-konsultasi {
            background: #d1fae5;
            color: #065f46;
        }

        .topik-lainnya {
            background: #f3f4f6;
            color: #6b7280;
        }
    </style>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}">Dashboard LMS</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">{{ $mataPelajaran->nama_mapel }}</a></li>
            <li class="breadcrumb-item active">Forum Diskusi</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 style="color: var(--primary); margin: 0;">
            <i class="fas fa-comments me-2"></i>Forum Diskusi
        </h4>
        <a href="{{ route('siswa.lms.mapel.forum.create', $mataPelajaran->id) }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Buat Pertanyaan
        </a>
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
                        <span class="ms-2">• {{ $item->created_at->diffForHumans() }}</span>
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
            <div style="text-align: center; padding: 60px 20px; color: #9ca3af;">
                <i class="fas fa-comments fa-3x mb-3" style="opacity: 0.3;"></i>
                <h5>Belum Ada Diskusi</h5>
                <p>Jadilah yang pertama bertanya!</p>
                <a href="{{ route('siswa.lms.mapel.forum.create', $mataPelajaran->id) }}" class="btn btn-primary mt-2">
                    <i class="fas fa-plus me-2"></i>Buat Pertanyaan
                </a>
            </div>
        @endforelse
    </div>

    @if($diskusi->hasPages())
        <div class="mt-4">
            {{ $diskusi->links() }}
        </div>
    @endif

@endsection