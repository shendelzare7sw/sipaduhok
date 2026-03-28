@php
    $isTeacher = $reply->is_teacher_reply;
    $isMe = $reply->user_id == auth()->id();
    $role = $isTeacher ? 'teacher' : 'student';
    $roleName = $isTeacher ? 'Guru' : 'Siswa';
    $badgeClass = $isTeacher ? 'badge-guru' : 'badge-siswa';
    $uniqueId = 'reply-' . $reply->id;
@endphp

<div class="post {{ $level > 0 ? 'reply-node' : '' }}" data-author="{{ $reply->user->name }}"
    data-role="{{ $role }}">

    <div class="post-header">
        <div class="avatar {{ $role }}">
            @if($reply->user && $reply->user->foto_profil)
                <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" alt="{{ $reply->user->name }}">
            @else
                {{ $isTeacher ? 'G' : substr($reply->user->name, 0, 2) }}
            @endif
        </div>
        <div class="post-info">
            <div>
                <span class="author-name">{{ $reply->user->name }}</span>
                <span class="badge-role {{ $badgeClass }}">{{ $roleName }}</span>
                @if($isMe)
                    <span class="badge bg-success" style="font-size: 10px;">Anda</span>
                @endif
            </div>
            <div class="post-date">
                {{ $reply->created_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
            </div>
        </div>
    </div>

    <!-- Content Display -->
    <div class="post-content" id="content-{{ $uniqueId }}">
        <div class="post-text mb-3">
            {!! nl2br(e($reply->content)) !!}
        </div>

        @if($reply->attachment)
            <div class="mt-3">
                @if(Str::startsWith($reply->attachment_type, 'image/'))
                    <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank">
                        <img src="{{ asset('storage/' . $reply->attachment) }}" alt="Attachment" class="img-fluid rounded"
                            style="max-height: 300px;">
                    </a>
                @elseif(Str::startsWith($reply->attachment_type, 'video/'))
                    <video controls class="w-100 rounded" style="max-height: 300px;">
                        <source src="{{ asset('storage/' . $reply->attachment) }}" type="{{ $reply->attachment_type }}">
                    </video>
                @elseif(Str::endsWith(strtolower($reply->attachment), '.pdf'))
                    <iframe src="{{ asset('storage/' . $reply->attachment) }}" width="100%" height="400px"
                        style="border: 1px solid #ddd; border-radius: 4px;"></iframe>
                @else
                    <div class="p-2 border rounded bg-light d-flex align-items-center">
                        <i class="fas fa-file me-2 text-secondary"></i>
                        <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank"
                            class="text-decoration-none text-dark">
                            {{ basename($reply->attachment) }}
                        </a>
                        <a href="{{ asset('storage/' . $reply->attachment) }}" download
                            class="ms-auto btn btn-sm btn-outline-secondary">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                @endif
            </div>
        @endif

        <!-- Actions (Edit/Delete) -->
        @if($isMe)
            <div class="d-flex gap-2" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #f0f0f0;">
                <button class="action-icon text-primary" onclick="toggleEditForm('{{ $uniqueId }}')" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="action-icon text-danger" title="Hapus"
                    onclick="confirmDeleteReply('{{ route('siswa.lms.mapel.forum.reply.destroy', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Edit Form (Hidden) -->
    @if($isMe)
        <div id="edit-{{ $uniqueId }}" class="edit-form" style="display: none;">
            <form action="{{ route('siswa.lms.mapel.forum.reply.update', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}"
                method="POST">
                @csrf @method('PUT')
                <div class="reply-form" style="display: block;">
                    <textarea name="isi" class="form-control" rows="3" required>{{ $reply->content }}</textarea>
                    <div class="d-flex justify-content-end gap-2 mt-2">
                        <button type="button" class="btn btn-secondary btn-sm"
                            onclick="toggleEditForm('{{ $uniqueId }}')">Batal</button>
                        <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>
    @endif

    @if(!$diskusi->is_closed)
        <button class="reply-btn" onclick="toggleReplyForm('form-{{ $uniqueId }}')">
            <i class="fas fa-reply me-1"></i> REPLY
        </button>
    @endif

    <!-- Reply Form -->
    <div id="form-{{ $uniqueId }}" class="reply-form">
        <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $diskusi->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
            <textarea name="isi" placeholder="Tulis balasan Anda..." required></textarea>

            <button type="button" class="attach-btn" onclick="toggleAttachArea(this)">
                <i class="fas fa-paperclip"></i> Lampirkan File
            </button>

            <div class="file-upload-area">
                <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                <p>Klik untuk memilih file</p>
                <input type="file" name="attachment" style="display: block; width: 100%;">
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-sm px-4">Kirim Balasan</button>
                <button type="button" class="btn btn-secondary btn-sm"
                    onclick="toggleReplyForm('form-{{ $uniqueId }}')">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Recursive Children --}}
@if($reply->replies && $reply->replies->count() > 0)
    @foreach($reply->replies as $child)
        @include('siswa.lms.forum.partials.reply-item', ['reply' => $child, 'level' => $level + 1])
    @endforeach
@endif
