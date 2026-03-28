@php
    $isTeacher = $reply->is_teacher_reply;
    $role = $isTeacher ? 'teacher' : 'student';
    $roleName = $isTeacher ? 'Guru' : 'Siswa';
    $badgeClass = $isTeacher ? 'badge-guru' : 'badge-siswa';
    $marginLeft = $level > 0 ? 'margin-left: 40px;' : ''; // Or use class logic if preferred, but recursion level is dynamic
@endphp

<div class="post {{ $level > 0 ? 'reply-node' : '' }}" style="{{ $level > 0 ? 'margin-left: 40px;' : '' }}"
    data-author="{{ $reply->user->name ?? 'User' }}" data-role="{{ $role }}">

    <div class="post-header">
        <div class="avatar {{ $role }}">
            {{ $isTeacher ? 'G' : substr($reply->user->name ?? 'U', 0, 2) }}
        </div>
        <div class="post-info">
            <div>
                <span class="author-name">{{ $reply->user->name ?? 'User' }}</span>
                <span class="badge-role {{ $badgeClass }}">{{ $roleName }}</span>
            </div>
            <div class="post-date">{{ $reply->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}</div>
        </div>
    </div>

    <div class="post-content">
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

    @if(!$forum->is_closed)
        <button class="reply-btn" onclick="toggleReplyForm('reply-{{ $reply->id }}')">REPLY</button>
    @endif

    {{-- Reply Form --}}
    <div id="reply-{{ $reply->id }}" class="reply-form">
        <form action="{{ route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forum->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
            <div class="form-group mb-2">
                <textarea name="isi" class="form-control" placeholder="Tulis balasan Anda..." rows="3"
                    required></textarea>
            </div>

            {{-- Attachment Dropzone --}}
            <button type="button" class="attachment-toggle-btn" onclick="toggleAttachment('reply-{{ $reply->id }}')">
                <i class="fas fa-paperclip"></i> Lampirkan File
            </button>
            <div id="attachment-area-reply-{{ $reply->id }}" class="attachment-area">
                <div class="dropzone-wrapper">
                    <div class="dropzone-desc">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p class="mb-0">Drag & Drop file disini atau klik untuk memilih</p>
                    </div>
                    <input type="file" name="attachment" class="dropzone-file-input"
                        onchange="updateFileName(this, 'reply-{{ $reply->id }}')">
                </div>
                <div id="file-name-reply-{{ $reply->id }}" class="file-preview-name"></div>
            </div>
            <div class="text-end">
                <button type="button" class="btn btn-secondary btn-sm me-2"
                    onclick="toggleReplyForm('reply-{{ $reply->id }}')">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm">Kirim</button>
            </div>
        </form>
    </div>
</div>

{{-- Recursive Children --}}
@foreach($reply->replies as $child)
    @include('guru.lms.forum._reply_item', ['reply' => $child, 'level' => $level + 1])
@endforeach