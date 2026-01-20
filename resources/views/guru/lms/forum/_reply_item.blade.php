<div class="card-custom border-0 shadow-sm {{ $reply->is_teacher_reply ? 'border-start border-4 border-warning bg-light' : '' }}"
    style="margin-left: {{ $level * 32 }}px">
    <div class="p-3">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <div class="d-flex align-items-center gap-2">
                <div class="avatar-circle-sm {{ $reply->is_teacher_reply ? 'bg-warning text-dark' : 'bg-secondary text-white' }}"
                    style="width: 32px; height: 32px; font-size: 12px; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                    {{ $reply->is_teacher_reply ? 'G' : substr($reply->user->name ?? 'U', 0, 1) }}
                </div>
                <div>
                    <h6 class="mb-0 fw-bold d-flex align-items-center gap-2">
                        {{ $reply->user->name ?? 'User' }}
                        @if($reply->is_teacher_reply)
                            <span class="badge bg-warning text-dark" style="font-size: 10px;">GURU</span>
                        @endif
                    </h6>
                    <small class="text-muted">{{ $reply->created_at->diffForHumans() }}</small>
                </div>
            </div>
            @if(!$forum->is_closed)
                <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="collapse"
                    data-bs-target="#replyForm{{ $reply->id }}">
                    <i class="fas fa-reply me-1"></i> Balas
                </button>
            @endif
        </div>

        <div class="text-dark mb-2" style="font-size: 14px; white-space: pre-wrap;">{{ $reply->content }}</div>

        {{-- Nested Reply Form --}}
        <div class="collapse mt-2" id="replyForm{{ $reply->id }}">
            <div class="card card-body bg-white border p-3">
                <form action="{{ route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forum->id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                    <div class="mb-2">
                        <textarea name="content" class="form-control form-control-sm" rows="2"
                            placeholder="Balas komentar ini..." required></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-sm btn-primary">Kirim</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Recursive Children --}}
@foreach($reply->replies as $child)
    @include('guru.lms.forum._reply_item', ['reply' => $child, 'level' => $level + 1])
@endforeach