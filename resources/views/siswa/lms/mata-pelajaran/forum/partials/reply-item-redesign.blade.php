@php
    $isTeacher = $reply->is_teacher_reply || $reply->isFromTeacher();
    $isMe = $reply->user_id == auth()->id();
    $role = $isTeacher ? 'teacher' : 'student';
    $roleName = $isTeacher ? 'Guru' : 'Siswa';
    $badgeClass = $isTeacher ? 'badge-guru' : 'badge-siswa';
    $uniqueId = 'reply-' . $reply->id;
@endphp

<div class="post {{ $level > 0 ? 'reply-level-' . min($level, 5) : '' }}" data-author="{{ $reply->user->name }}"
    data-role="{{ $role }}" data-is-mine="{{ $isMe ? 'true' : 'false' }}">

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
                    <span class="badge bg-secondary own-reply-badge">Anda</span>
                @endif
            </div>
            <div class="post-date">
                {{ $reply->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
            </div>
        </div>
    </div>

    <!-- Content Display -->
    <div class="post-content" id="content-{{ $uniqueId }}">
        <div class="post-text mb-3">
            {!! nl2br(e($reply->isi)) !!}
        </div>

        @if($reply->attachment && is_array($reply->attachment) && count($reply->attachment) > 0)
            <div class="attachments-container mt-3 mb-3">
                @foreach($reply->attachment as $attachment)
                    <x-lms.media-display :file="$attachment" />
                @endforeach
            </div>
        @endif

        <!-- Actions (Edit/Delete) - Below Content -->
        @if($isMe)
            <div class="post-actions d-flex gap-2">
                <button class="action-icon text-primary" data-toggle-reply="edit-form-{{ $uniqueId }}" title="Edit">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button type="button" class="action-icon text-danger" title="Hapus"
                    data-delete-url="{{ route('siswa.lms.mapel.forum.reply.destroy', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        @endif
    </div>

    <!-- Edit Form (Hidden) -->
    @if($isMe)
        <div id="edit-form-{{ $uniqueId }}" class="reply-form">
            <form action="{{ route('siswa.lms.mapel.forum.reply.update', [$mataPelajaran->id, $diskusi->id, $reply->id]) }}"
                method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <textarea name="isi" class="form-control mb-2" rows="3" required>{{ $reply->isi }}</textarea>

                <!-- Show existing attachments -->
                @if($reply->attachment && is_array($reply->attachment) && count($reply->attachment) > 0)
                    <div class="mb-2">
                        <small class="text-muted d-block mb-2">File yang sudah ada:</small>
                        <div class="attachments-container">
                            @foreach($reply->attachment as $attachment)
                                <x-lms.media-display :file="$attachment" />
                            @endforeach
                        </div>
                    </div>
                @endif

                <button type="button" class="attach-btn btn-sm mb-2" data-toggle-attach>
                    <i class="fas fa-plus-circle"></i> Tambah File
                </button>

                <div class="file-upload-area">
                    <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                    <p>Klik untuk memilih file (bisa pilih multiple)</p>
                    <input type="file" name="attachment[]" multiple class="forum-file-input">
                </div>

                <div class="d-flex justify-content-end gap-2 mt-2">
                    <button type="button" class="btn btn-secondary btn-sm"
                        data-toggle-reply="edit-form-{{ $uniqueId }}">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    @endif

    @if(!$diskusi->is_closed)
        <button class="reply-btn" data-toggle-reply="form-{{ $uniqueId }}">
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

            <button type="button" class="attach-btn" data-toggle-attach>
                <i class="fas fa-paperclip"></i> Lampirkan File
            </button>

            <div class="file-upload-area">
                <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                <p>Klik untuk memilih file (bisa pilih multiple)</p>
                <input type="file" name="attachment[]" multiple class="forum-file-input">
            </div>

            <div class="d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-primary btn-sm px-4">Kirim Balasan</button>
                <button type="button" class="btn btn-secondary btn-sm"
                    data-toggle-reply="form-{{ $uniqueId }}">Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- Recursive Children --}}
@if($reply->children && $reply->children->count() > 0)
    @foreach($reply->children as $child)
        @include('siswa.lms.mata-pelajaran.forum.partials.reply-item-redesign', ['reply' => $child, 'level' => $level + 1])
    @endforeach
@endif
