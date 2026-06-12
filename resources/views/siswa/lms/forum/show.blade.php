@extends('layouts.lms')

@section('title', $forum->judul)
@section('page-title', 'Diskusi Forum')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/forum/show.css'])
@endpush

@section('content')
<div class="siswa-lms-forum-show-page">
    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Topik
            </a>
        </div>

        <div class="forum-container">
            <h2 class="forum-heading">{{ $forum->judul }}</h2>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <div class="forum-filter-row">
                    <input type="text" id="searchInput" class="forum-search-input" placeholder="Cari pesan...">

                    <select id="filterRole">
                        <option value="all">Semua Peran</option>
                        <option value="student">Siswa</option>
                        <option value="teacher">Guru</option>
                    </select>

                    <button type="button" data-clear-filters>
                        <i class="fas fa-redo-alt me-1"></i> Reset Filter
                    </button>
                </div>
                <div id="searchResults"></div>
            </div>

            <div id="postsContainer">
                <!-- Main Topic Post -->
                <div class="post" data-author="{{ $forum->user->name ?? 'User' }}"
                    data-role="{{ $forum->isFromTeacher() ? 'teacher' : 'student' }}">
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
                            <div class="post-date">{{ $forum->created_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}</div>
                        </div>
                    </div>
                    <div class="post-content">
                        {!! nl2br(e($forum->content ?? $forum->isi)) !!}

                        @if($forum->lampiran)
                            <div class="mt-3">
                                @php
                                    $ext = strtolower(pathinfo($forum->lampiran, PATHINFO_EXTENSION));
                                    $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
                                    $isVideo = in_array($ext, ['mp4', 'avi', 'mov']);
                                    $isPdf = $ext === 'pdf';
                                @endphp

                                @if($isImage)
                                    <a href="{{ Storage::url($forum->lampiran) }}" target="_blank">
                                        <img src="{{ Storage::url($forum->lampiran) }}" alt="Lampiran"
                                            class="img-fluid rounded forum-attachment-preview">
                                    </a>
                                @elseif($isVideo)
                                    <video controls class="w-100 rounded forum-attachment-preview">
                                        <source src="{{ Storage::url($forum->lampiran) }}">
                                    </video>
                                @elseif($isPdf)
                                    <iframe src="{{ Storage::url($forum->lampiran) }}" width="100%" height="400px"
                                        class="forum-attachment-frame"></iframe>
                                @else
                                    <a href="{{ Storage::url($forum->lampiran) }}" target="_blank" class="btn btn-sm btn-light border">
                                        <i class="fas fa-paperclip me-1"></i> Lihat Lampiran
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!$forum->is_closed)
                        <button class="reply-btn" data-toggle-reply="reply-form-main">
                            <i class="fas fa-reply me-1"></i> REPLY
                        </button>
                    @endif

                    <!-- Reply Form Main -->
                    <div id="reply-form-main" class="reply-form">
                        <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $forum->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <textarea name="isi" placeholder="Tulis balasan Anda..." required></textarea>

                            <button type="button" class="attach-btn" data-toggle-attach>
                                <i class="fas fa-paperclip"></i> Lampirkan File
                            </button>

                            <div class="file-upload-area">
                                <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                                <p>Klik untuk memilih file</p>
                                <input type="file" name="attachment" class="forum-file-input">
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
                    @include('siswa.lms.forum.partials.reply-item', ['reply' => $reply, 'level' => 0])
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/siswa/lms/forum/show.js'])
@endpush
