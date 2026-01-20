@extends('layouts.lms')

@section('title', $diskusi->judul . ' - Forum')
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Forum Diskusi')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .discussion-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 24px;
        }

        .discussion-header {
            display: flex;
            gap: 16px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }

        .discussion-avatar {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #0d3f7a);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 22px;
            flex-shrink: 0;
        }

        .discussion-avatar.teacher {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .discussion-body {
            padding: 20px 0;
            line-height: 1.8;
            color: #374151;
            white-space: pre-wrap;
        }

        .reply-card {
            background: #f9fafb;
            border-radius: 10px;
            padding: 16px;
            margin-bottom: 16px;
            margin-left: 72px;
            border-left: 3px solid #e5e7eb;
        }

        .reply-card.teacher-reply {
            background: #ecfdf5;
            border-left-color: #10b981;
        }

        .reply-card.is-answer {
            background: #fef3c7;
            border-left-color: #f59e0b;
        }

        .nested-reply {
            margin-left: 40px;
            margin-top: 12px;
        }

        .reply-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }

        .reply-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #9ca3af;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }

        .reply-avatar.teacher {
            background: #10b981;
        }

        .reply-meta {
            font-size: 13px;
            color: #6b7280;
        }

        .reply-content {
            color: #374151;
            line-height: 1.6;
            white-space: pre-wrap;
        }

        .reply-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        .topik-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
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
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}">Forum</a>
            </li>
            <li class="breadcrumb-item active">{{ Str::limit($diskusi->judul, 30) }}</li>
        </ol>
    </nav>

    <!-- Main Discussion -->
    <div class="discussion-card">
        <div class="discussion-header">
            <div class="discussion-avatar {{ $diskusi->isFromTeacher() ? 'teacher' : '' }}">
                {{ strtoupper(substr($diskusi->user->name ?? 'U', 0, 1)) }}
            </div>
            <div style="flex: 1;">
                <h4 style="margin: 0 0 8px 0; color: #1f2937;">
                    @if($diskusi->is_pinned)
                        <i class="fas fa-thumbtack text-warning me-2" title="Disematkan"></i>
                    @endif
                    {{ $diskusi->judul }}
                </h4>
                <div style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
                    <span class="topik-badge topik-{{ $diskusi->topik }}">{{ ucfirst($diskusi->topik) }}</span>
                    <span style="color: #6b7280; font-size: 14px;">
                        oleh <strong style="color: {{ $diskusi->isFromTeacher() ? '#10b981' : '#1f2937' }}">
                            {{ $diskusi->user->name ?? 'Unknown' }}
                            @if($diskusi->isFromTeacher())
                                <i class="fas fa-chalkboard-teacher ms-1"></i>
                            @endif
                        </strong>
                    </span>
                    <span style="color: #9ca3af; font-size: 13px;">
                        {{ $diskusi->created_at->format('d M Y, H:i') }}
                    </span>
                    @if($diskusi->is_closed)
                        <span class="badge bg-secondary">Ditutup</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="discussion-body">
            {{ $diskusi->isi }}
        </div>
    </div>

    <!-- Replies -->
    <h5 style="margin-bottom: 16px; color: #374151;">
        <i class="fas fa-comments me-2 text-primary"></i>
        {{ $replies->count() }} Balasan
    </h5>

    @forelse($replies as $reply)
        <div class="reply-card {{ $reply->isFromTeacher() ? 'teacher-reply' : '' }} {{ $reply->is_answer ? 'is-answer' : '' }}">
            <div class="reply-header">
                <div class="reply-avatar {{ $reply->isFromTeacher() ? 'teacher' : '' }}">
                    {{ strtoupper(substr($reply->user->name ?? 'U', 0, 1)) }}
                </div>
                <div class="reply-meta" style="flex: 1;">
                    <strong style="color: {{ $reply->isFromTeacher() ? '#10b981' : '#1f2937' }}">
                        {{ $reply->user->name ?? 'Unknown' }}
                        @if($reply->isFromTeacher())
                            <span class="badge bg-success ms-1">Guru</span>
                        @endif
                    </strong>
                    @if($reply->is_answer)
                        <span class="badge bg-warning ms-1"><i class="fas fa-check"></i> Jawaban Terbaik</span>
                    @endif
                    <span class="ms-2">{{ $reply->created_at->diffForHumans() }}</span>
                </div>

                @if(Auth::id() == $reply->user_id && $reply->can_edit)
                    <div class="dropdown">
                        <button class="btn btn-link btn-sm text-muted p-0" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button type="button" class="dropdown-item"
                                    onclick="editReply('{{ $reply->id }}', `{{ $reply->isi }}`)">
                                    <i class="fas fa-edit me-2 text-primary"></i> Edit
                                </button>
                            </li>
                            <li>
                                <form
                                    action="{{ route('siswa.lms.mapel.forum.reply.destroy', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}"
                                    method="POST" onsubmit="return confirm('Hapus balasan ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i> Hapus
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
            <div class="reply-content">
                {{ $reply->isi }}
            </div>

            {{-- Nested Replies --}}
            @foreach($reply->children as $child)
                <div class="nested-reply">
                    <div class="reply-card" style="margin-left: 0;">
                        <div class="reply-header">
                            <div class="reply-avatar {{ $child->isFromTeacher() ? 'teacher' : '' }}"
                                style="width: 28px; height: 28px; font-size: 12px;">
                                {{ strtoupper(substr($child->user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="reply-meta">
                                <strong style="color: {{ $child->isFromTeacher() ? '#10b981' : '#1f2937' }}">
                                    {{ $child->user->name ?? 'Unknown' }}
                                </strong>
                                <span class="ms-2">{{ $child->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="reply-content">
                            {{ $child->isi }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @empty
        <div class="discussion-card" style="text-align: center; padding: 40px; color: #9ca3af;">
            <i class="fas fa-comments fa-2x mb-3" style="opacity: 0.3;"></i>
            <p>Belum ada balasan. Jadilah yang pertama!</p>
        </div>
    @endforelse

    <!-- Reply Form -->
    @if(!$diskusi->is_closed)
        <div class="discussion-card">
            <h5 style="margin-bottom: 16px; color: #1f2937;">
                <i class="fas fa-reply me-2 text-primary"></i>Tulis Balasan
            </h5>

            <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $diskusi->id]) }}" method="POST"
                class="reply-form">
                @csrf

                <div class="mb-3">
                    <textarea name="isi" class="form-control @error('isi') is-invalid @enderror" rows="4"
                        placeholder="Tulis balasan Anda..." required>{{ old('isi') }}</textarea>
                    @error('isi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Balasan
                </button>
            </form>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-lock me-2"></i>
            Diskusi ini sudah ditutup dan tidak dapat menerima balasan baru.
        </div>
    @endif

    <!-- Edit Reply Modal -->
    <div class="modal fade" id="editReplyModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Balasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editReplyForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Isi Balasan</label>
                            <textarea name="isi" id="editReplyContent" class="form-control" rows="4" required></textarea>
                            <div class="form-text text-warning">
                                <i class="fas fa-exclamation-triangle"></i> Anda hanya dapat mengedit balasan dalam waktu 1
                                jam setelah posting.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function editReply(id, content) {
            let url = "{{ route('siswa.lms.mapel.forum.reply.update', [$mataPelajaran->id, $diskusi->id, ':id']) }}";
            url = url.replace(':id', id);

            document.getElementById('editReplyForm').action = url;
            document.getElementById('editReplyContent').value = content;

            var modal = new bootstrap.Modal(document.getElementById('editReplyModal'));
            modal.show();
        }
    </script>

@endsection