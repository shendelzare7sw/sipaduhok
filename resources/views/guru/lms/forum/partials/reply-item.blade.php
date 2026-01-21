@php
    $isTeacher = $reply->is_teacher_reply;
    $role = $isTeacher ? 'teacher' : 'student';
    $roleName = $isTeacher ? 'Guru' : 'Siswa';
    $badgeClass = $isTeacher ? 'badge-guru' : 'badge-siswa';
    $marginLeft = $level > 0 ? 'margin-left: 40px;' : '';
    $uniqueId = 'reply-' . $reply->id;
@endphp

<div class="post {{ $level > 0 ? 'reply-node' : '' }}" style="{{ $marginLeft }}" data-author="{{ $reply->user->name }}"
    data-role="{{ $role }}" data-content="{{ strtolower($reply->content) }}">

    <div class="post-header">
        <div class="avatar {{ $role }}">{{ substr($reply->user->name, 0, 2) }}</div>
        <div class="post-info">
            <div>
                <span class="author-name">{{ $reply->user->name }}</span>
                <span class="badge-role {{ $badgeClass }}">{{ $roleName }}</span>
                @if($level > 0)
                    <small class="text-muted"><i class="fas fa-reply"></i> membalas</small>
                @endif
            </div>
            <div class="post-date">{{ $reply->created_at->translatedFormat('l, d F Y pukul H:i') }}</div>
        </div>
    </div>

    <div class="post-content">
        {!! nl2br(e($reply->content)) !!}

        @if($reply->attachment)
            <div class="media-attachment">
                @if(Str::startsWith($reply->attachment_type, 'image/'))
                    <img src="{{ asset('storage/' . $reply->attachment) }}" alt="Attachment"
                        style="max-width: 100%; border-radius: 4px;">
                @else
                    <a href="{{ asset('storage/' . $reply->attachment) }}" target="_blank"
                        class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-download me-1"></i> {{ basename($reply->attachment) }}
                    </a>
                @endif
            </div>
        @endif
    </div>

    <!-- Actions -->
    <div class="role-controls">
        @if($isTeacher && auth()->user()->role === 'guru')
            <!-- Guru can specific actions if needed -->
        @endif
    </div>

    @if(!$forum->is_closed)
        <button class="reply-btn" onclick="toggleReplyForm('form-{{ $uniqueId }}')">REPLY</button>
    @endif

    <!-- Reply Form -->
    <div id="form-{{ $uniqueId }}" class="reply-form">
        <form action="{{ route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forum->id]) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
            <div class="form-group mb-3">
                <textarea name="content" class="form-control" rows="3" placeholder="Tulis balasan Anda..."
                    required></textarea>
            </div>
            <div class="form-group mb-3">
                <label class="small text-muted">Lampiran (Opsional)</label>
                <input type="file" name="attachment" class="form-control form-control-sm">
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Kirim Balasan</button>
                <button type="button" class="btn btn-secondary btn-sm"
                    onclick="toggleReplyForm('form-{{ $uniqueId }}')">Batal</button>
            </div>
        </form>
    </div>
</div>

<!-- Recursive Children -->
@if($reply->replies && $reply->replies->count() > 0)
    @foreach($reply->replies as $child)
        @include('guru.lms.forum.partials.reply-item', ['reply' => $child, 'level' => $level + 1])
    @endforeach
@endif