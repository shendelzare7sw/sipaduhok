{{--
    Satu balasan forum LMS (rekursif) — dipakai bersama Guru dan Siswa.
    Param: $reply, $level, $isClosed (bool), $forumUrls = [
        'reply' => URL kirim balasan,
        'update' => fn ($replyId) => URL ubah balasan,
        'destroy' => fn ($replyId) => URL hapus balasan,
    ]. Elemen berada di dalam x-data milik halaman yang menyediakan cocok($el).
--}}
@php
    $isTeacher = $reply->is_teacher_reply || $reply->isFromTeacher();
    $isMe = $reply->user_id == auth()->id();
    $indent = ['', 'sm:ml-8', 'sm:ml-14', 'sm:ml-20', 'sm:ml-24', 'sm:ml-28'][min($level, 5)];
    $textarea = 'block w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
    $fileInput = 'block w-full text-xs text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700';
    $adaLampiran = $reply->attachment && is_array($reply->attachment) && count($reply->attachment) > 0;
@endphp

<article data-post data-author="{{ $reply->user->name }}" data-role="{{ $isTeacher ? 'teacher' : 'student' }}" data-is-mine="{{ $isMe ? 'true' : 'false' }}"
         x-show="cocok($el)" x-data="{ balas: false, ubah: false, lampir: false, berkas: [] }"
         class="min-w-0 rounded-2xl border bg-white p-4 shadow-sm {{ $indent }} {{ $level > 0 ? 'border-l-4 border-slate-200 border-l-indigo-200' : 'border-slate-200' }}">
    <div class="flex items-start gap-3">
        <span class="flex h-9 w-9 shrink-0 items-center justify-center overflow-hidden rounded-full text-xs font-extrabold text-white {{ $isTeacher ? 'bg-emerald-600' : 'bg-indigo-600' }}">
            @if($reply->user && $reply->user->foto_profil)
                <img src="{{ asset('storage/' . $reply->user->foto_profil) }}" alt="{{ $reply->user->name }}" class="h-full w-full object-cover">
            @else
                {{ $isTeacher ? 'G' : substr($reply->user->name, 0, 2) }}
            @endif
        </span>
        <div class="min-w-0 flex-1">
            <p class="flex flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">
                {{ $reply->user->name }}
                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isTeacher ? 'bg-emerald-50 text-emerald-700' : 'bg-indigo-50 text-indigo-700' }}">{{ $isTeacher ? 'Guru' : 'Siswa' }}</span>
                @if($isMe)<span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">Anda</span>@endif
            </p>
            <p class="text-[11px] text-slate-500">{{ $reply->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}</p>
        </div>
        @if($isMe)
            <div class="flex shrink-0 gap-1.5">
                <button type="button" x-on:click="ubah = ! ubah" class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-xs text-amber-700 ring-1 ring-inset ring-amber-100 hover:bg-amber-100" title="Edit balasan" aria-label="Edit balasan"><i class="fa-solid fa-pen" aria-hidden="true"></i></button>
                <form method="POST" action="{{ $forumUrls['destroy']($reply->id) }}" data-confirm data-confirm-title="Hapus balasan?" data-confirm-message="Balasan ini akan dihapus permanen." data-confirm-text="Ya, hapus">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-xs text-rose-700 ring-1 ring-inset ring-rose-100 hover:bg-rose-100" title="Hapus balasan" aria-label="Hapus balasan"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
                </form>
            </div>
        @endif
    </div>

    <div data-post-content x-show="! ubah" class="mt-3 break-words text-sm leading-7 text-slate-700">
        <x-lms.rich-text :text="$reply->isi" />
        @if($adaLampiran)
            <div class="mt-3 flex flex-wrap gap-2 sm:gap-3">@foreach($reply->attachment as $attachment)<x-lms.media-display :file="$attachment" group="balasan-{{ $reply->id }}" />@endforeach</div>
        @endif
    </div>

    @if($isMe)
        <form x-cloak x-show="ubah" action="{{ $forumUrls['update']($reply->id) }}" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3 rounded-xl border border-amber-200 bg-amber-50/50 p-3">
            @csrf
            @method('PUT')
            <textarea name="isi" rows="3" required data-autogrow class="{{ $textarea }}">{{ $reply->isi }}</textarea>
            @if($adaLampiran)
                <div>
                    <p class="mb-2 text-[11px] font-bold text-slate-500">File yang sudah ada:</p>
                    <div class="flex flex-wrap gap-2">@foreach($reply->attachment as $attachment)<x-lms.media-display :file="$attachment" group="ubah-{{ $reply->id }}" />@endforeach</div>
                </div>
            @endif
            <label class="block text-xs font-bold text-slate-600">Tambah file
                <input type="file" name="attachment[]" multiple class="mt-1 {{ $fileInput }}">
            </label>
            <div class="flex justify-end gap-2">
                <button type="button" x-on:click="ubah = false" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-indigo-600 px-4 text-xs font-bold text-white hover:bg-indigo-700">Simpan perubahan</button>
            </div>
        </form>
    @endif

    @unless($isClosed)
        <button type="button" x-on:click="balas = ! balas" class="mt-3 inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-indigo-50 px-3 text-xs font-bold text-indigo-700 hover:bg-indigo-100"><i class="fa-solid fa-reply" aria-hidden="true"></i>Balas</button>
        <form x-cloak x-show="balas" action="{{ $forumUrls['reply'] }}" method="POST" enctype="multipart/form-data" class="mt-3 space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
            @csrf
            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
            <textarea name="isi" rows="3" placeholder="Tulis balasan Anda..." required data-autogrow class="{{ $textarea }}"></textarea>
            <button type="button" x-on:click="lampir = ! lampir" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-600 hover:text-indigo-700"><i class="fa-solid fa-paperclip" aria-hidden="true"></i>Lampirkan file</button>
            <div x-show="lampir" class="rounded-xl border border-dashed border-slate-300 bg-white p-3">
                <input type="file" name="attachment[]" multiple x-on:change="berkas = [...$event.target.files].map((f) => f.name)" class="{{ $fileInput }}">
                <ul class="mt-2 space-y-0.5 text-[11px] text-slate-500"><template x-for="nama in berkas" :key="nama"><li class="truncate" x-text="nama"></li></template></ul>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="inline-flex min-h-9 items-center rounded-lg bg-indigo-600 px-4 text-xs font-bold text-white hover:bg-indigo-700">Kirim balasan</button>
                <button type="button" x-on:click="balas = false" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
            </div>
        </form>
    @endunless
</article>

@if($reply->children && $reply->children->count() > 0)
    @foreach($reply->children as $child)
        @include('lms.forum.reply-item', ['reply' => $child, 'level' => $level + 1])
    @endforeach
@endif
