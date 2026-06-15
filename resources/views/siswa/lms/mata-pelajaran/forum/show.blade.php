@extends('layouts.lms')

@section('title', $diskusi->judul)
@section('page-title', 'Diskusi Forum')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection


@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/forum/show.css'])
@endpush

@section('content')
<div class="siswa-lms-mapel-forum-show-page">
    <div class="container-fluid">
        <nav aria-label="breadcrumb" class="forum-breadcrumb">
            <ol class="breadcrumb mb-0 forum-breadcrumb-list">
                <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}"><i class="fas fa-home me-1"></i>Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">{{ $mataPelajaran->nama_mapel }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $diskusi->judul }}</li>
            </ol>
        </nav>

        <div class="forum-container">
            <h2 class="forum-heading">{{ $diskusi->judul }}</h2>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <div class="forum-filter-row">
                    <input type="text" id="searchInput" class="forum-search-input" placeholder="Cari pesan...">

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
                <div class="post" data-author="{{ $diskusi->user->name ?? 'User' }}"
                    data-role="{{ $diskusi->isFromTeacher() ? 'teacher' : 'student' }}"
                    data-is-mine="{{ $diskusi->user_id == auth()->id() ? 'true' : 'false' }}">

                    <div class="post-header">
                        <div class="avatar {{ $diskusi->isFromTeacher() ? 'teacher' : 'student' }}">
                            @if($diskusi->user && $diskusi->user->foto_profil)
                                <img src="{{ asset('storage/' . $diskusi->user->foto_profil) }}" alt="{{ $diskusi->user->name }}">
                            @else
                                {{ substr($diskusi->user->name ?? '?', 0, 2) }}
                            @endif
                        </div>
                        <div class="post-info">
                            <div>
                                <span class="author-name">{{ $diskusi->user->name ?? 'User' }}</span>
                                @if($diskusi->isFromTeacher())
                                    <span class="badge-role badge-guru">Guru (Penulis)</span>
                                @else
                                    <span class="badge-role badge-siswa">Siswa</span>
                                @endif
                            </div>
                            <div class="post-date">{{ $diskusi->created_at->locale('id')->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
                            </div>
                        </div>
                    </div>

                    <div class="post-content">
                        {!! nl2br(e($diskusi->isi)) !!}

                        @if($diskusi->lampiran && is_array($diskusi->lampiran) && count($diskusi->lampiran) > 0)
                            <div class="attachments-container mt-3">
                                @foreach($diskusi->lampiran as $lampiran)
                                    <x-lms.media-display :file="$lampiran" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(!$diskusi->is_closed)
                        <button class="reply-btn" data-toggle-reply="reply-form-main">
                            <i class="fas fa-reply me-1"></i> REPLY
                        </button>
                    @endif

                    <!-- Reply Form Main -->
                    <div id="reply-form-main" class="reply-form">
                        <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $diskusi->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
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
                                    data-toggle-reply="reply-form-main">Batal</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Replies Loop -->
                @foreach($diskusi->replies->whereNull('parent_id') as $reply)
                    @include('siswa.lms.mata-pelajaran.forum.partials.reply-item-redesign', ['reply' => $reply, 'level' => 0])
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @vite(['resources/js/siswa/lms/mata-pelajaran/forum-show.js'])
@endpush
