@php
    $isTeacher = $reply->is_teacher_reply;
    $isMe = $reply->user_id == auth()->id();
    $role = $isTeacher ? 'teacher' : 'student';
    $roleName = $isTeacher ? 'Guru' : 'Siswa';
    $badgeClass = $isTeacher ? 'badge-guru' : 'badge-siswa';
    $uniqueId = 'reply-' . $reply->id;
@endphp

<div class="post {{ $level > 0 ? 'reply-node' : '' }}" style="{{ $level > 0 ? 'margin-left: 40px;' : '' }}"
    data-author="{{ $reply->user->name }}" data-role="{{ $role }}">

    <div class="post-header">
        <div class="avatar {{ $role }}">
            @if($reply->user && $reply->user->foto_profil)
                <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" alt="{{ $reply->user->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
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
                @if($level > 0)
                    {{-- <small class="text-muted"><i class="fas fa-reply"></i> membalas</small> --}}
                @endif
            </div>
            <div class="post-date">
                {{ $reply->created_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
            </div>
        </div>
    </div>

    <div class="post-content" id="content-{{ $uniqueId }}">
        {!! nl2br(e($reply->content)) !!}

        @if($reply->attachment)
            <div class="media-attachment mt-2">
                @if(Str::startsWith($reply->attachment_type, 'image/'))
                    <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank">
                        <img src="{{ asset('storage/' . $reply->attachment) }}" alt="Attachment" class="img-fluid rounded"
                            style="max-height: 300px;">
                    </a>
                @elseif(Str::startsWith($reply->attachment_type, 'video/'))
                    <video controls class="w-100 rounded" style="max-height: 300px;">
                        <source src="{{ asset('storage/' . $reply->attachment) }}" type="{{ $reply->attachment_type }}">
                        Browser Anda tidak mendukung tag video.
                    </video>
                @elseif(Str::endsWith(strtolower($reply->attachment), '.pdf'))
                    <iframe src="{{ asset('storage/' . $reply->attachment) }}" width="100%" height="400px"
                        style="border: 1px solid #ddd; border-radius: 4px;"></iframe>
                    <div class="mt-1">
                        <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank"
                            class="small text-decoration-none">
                            <i class="fas fa-external-link-alt"></i> Buka PDF di tab baru
                        </a>
                    </div>
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
    </div>

    {{-- Edit Form (Hidden by default) --}}
    @if($isMe)
        <div id="edit-{{ $uniqueId }}" class="edit-form mb-3">
            <form action="{{ route('siswa.lms.mapel.forum.reply.update', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}"
                method="POST">
                @csrf @method('PUT')
                <div class="form-group mb-2">
                    <textarea name="isi" class="form-control" rows="3" required>{{ $reply->content }}</textarea>
                </div>
                <div class="d-flex gap-2 justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm"
                        onclick="toggleEditForm('{{ $uniqueId }}')">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan</button>
                </div>
            </form>
        </div>
    @endif

    <div class="action-icons">
        @if($isMe)
            <button class="action-icon" onclick="toggleEditForm('{{ $uniqueId }}')" title="Edit">✏️</button>
            <form
                action="{{ route('siswa.lms.mapel.forum.reply.destroy', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}"
                method="POST" style="display:inline;">
                @csrf @method('DELETE')
                <button type="submit" class="action-icon text-danger" onclick="return confirm('Hapus balasan ini?')"
                    title="Hapus">🗑️</button>
            </form>
        @endif
    </div>

    @if(!$diskusi->is_closed)
        <button class="reply-btn" onclick="toggleReplyForm('form-{{ $uniqueId }}')">REPLY</button>
    @endif

    {{-- Reply Form --}}
    <div id="form-{{ $uniqueId }}" class="reply-form">
        <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $diskusi->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
            <div class="form-group mb-3">
                <textarea name="isi" class="form-control" rows="3" placeholder="Tulis balasan Anda..."
                    required></textarea>
            </div>

            {{-- Attachment Dropzone --}}
            <button type="button" class="attachment-toggle-btn" onclick="toggleAttachment('form-{{ $uniqueId }}')">
                <i class="fas fa-paperclip"></i> Lampirkan File
            </button>
            <div id="attachment-area-form-{{ $uniqueId }}" class="attachment-area">
                <div class="dropzone-wrapper">
                    <div class="dropzone-desc">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="mb-0">Drag & Drop file disini atau klik untuk memilih</p>
                    </div>
                    <input type="file" name="attachment" class="dropzone-file-input"
                        onchange="updateFileName(this, 'form-{{ $uniqueId }}')">
                </div>
                <div id="file-name-form-{{ $uniqueId }}" class="file-preview-name"></div>
            </div>
            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-secondary btn-sm"
                    onclick="toggleReplyForm('form-{{ $uniqueId }}')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Kirim Balasan</button>
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
