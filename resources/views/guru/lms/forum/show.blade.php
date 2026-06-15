@extends('layouts.lms-guru')

@section('title', $forum->judul)
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection


@push('styles')
    @vite(['resources/css/guru/lms/forum/show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/guru/lms/forum/show.js'])
@endpush

@section('content')
    <div class="container-fluid guru-lms-forum-show-page" data-csrf-token="{{ csrf_token() }}">
        <div class="mb-3">
            <a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Topik
            </a>
        </div>

        <div class="forum-container">
            <h2 class="forum-title">{{ $forum->judul }}</h2>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <div class="filter-controls">
                    <input type="text" id="searchInput" class="search-input" placeholder="Cari pesan...">

                    <select id="filterRole">
                        <option value="all">Semua Peran</option>
                        <option value="student">Siswa</option>
                        <option value="teacher">Guru</option>
                    </select>

                    <select id="filterAuthor">
                        <option value="all">Semua Pengirim</option>
                        <option value="my">Pesan Saya</option>
                    </select>

                    <button type="button" data-clear-filters>
                        <i class="fas fa-redo-alt me-1"></i> Reset Filter
                    </button>
                </div>
                <div id="searchResults"></div>
            </div>

            <div id="postsContainer">
                <!-- Main Topic Post (Guru/Creator) -->
                <div class="post" data-author="{{ $forum->user->name ?? 'User' }}"
                    data-role="{{ $forum->isFromTeacher() ? 'teacher' : 'student' }}"
                    data-is-mine="{{ $forum->user_id == auth()->id() ? 'true' : 'false' }}">

                    <div class="post-header">
                        <div class="avatar {{ $forum->isFromTeacher() ? 'teacher' : 'student' }}">
                            @if($forum->user && $forum->user->foto_profil)
                                <img src="{{ asset('storage/' . $forum->user->foto_profil) }}" alt="{{ $forum->user->name }}">
                            @else
                                {{ substr($forum->user->name ?? '?', 0, 2) }}
                            @endif
                        </div>
                        <div class="post-info">
                            <div>
                                <span class="author-name">{{ $forum->user->name ?? 'User' }}</span>
                                @if($forum->isFromTeacher())
                                    <span class="badge-role badge-guru">Guru (Penulis)</span>
                                @else
                                    <span class="badge-role badge-siswa">Siswa</span>
                                @endif
                            </div>
                            <div class="post-date">{{ $forum->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="post-content">
                        {!! nl2br(e($forum->isi)) !!}

                        @if($forum->lampiran && is_array($forum->lampiran) && count($forum->lampiran) > 0)
                            <div class="attachments-container mt-3">
                                @foreach($forum->lampiran as $lampiran)
                                    <x-lms.media-display :file="$lampiran" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(!$forum->is_closed)
                        <button type="button" class="reply-btn" data-toggle-reply="reply-form-main">
                            <i class="fas fa-reply me-1"></i> REPLY
                        </button>
                    @endif

                    <!-- Reply Form Main -->
                    <div id="reply-form-main" class="reply-form">
                        <form action="{{ route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forum->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <textarea name="isi" placeholder="Tulis balasan Anda..." required></textarea>

                            <button type="button" class="attach-btn" data-toggle-attach>
                                <i class="fas fa-paperclip"></i> Lampirkan File
                            </button>

                            <div class="file-upload-area">
                                <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                                <p>Klik untuk memilih file (bisa pilih multiple)</p>
                                <input type="file" name="attachment[]" class="forum-file-input" multiple>
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary btn-sm px-4">Kirim Balasan</button>
                                <button type="button" class="btn btn-secondary btn-sm"
                                    data-toggle-reply="reply-form-main">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Replies Loop -->
                @foreach($forum->replies->whereNull('parent_id') as $reply)
                    @include('guru.lms.forum.partials.reply-item-redesign', ['reply' => $reply, 'level' => 0])
                @endforeach
            </div>
        </div>
    </div>

@endsection
