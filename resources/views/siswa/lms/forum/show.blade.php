@extends('layouts.lms')

@section('title', $forum->judul)
@section('page-title', 'Diskusi Forum')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        /* Shared CSS from Prototype */
        .forum-container {
            max-width: 100%;
            margin: 0 auto;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .post {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            position: relative;
            transition: all 0.3s;
        }

        .post.hidden {
            display: none;
        }

        .post.highlight {
            animation: highlight 3s ease-out;
        }

        @keyframes highlight {
            0% {
                background-color: #e3f2fd;
            }

            100% {
                background-color: white;
            }
        }

        .post-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            margin-right: 15px;
            flex-shrink: 0;
            text-transform: uppercase;
        }

        .avatar.teacher {
            background: #1565c0;
        }

        .avatar.student {
            background: #10b981;
        }

        .post-info {
            flex-grow: 1;
        }

        .author-name {
            font-weight: 600;
            color: #2c5282;
            margin-right: 10px;
            font-size: 15px;
        }

        .badge-role {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 5px;
        }

        .badge-siswa {
            background: #00a8e8;
            color: white;
        }

        .badge-guru {
            background: #4caf50;
            color: white;
        }

        .post-date {
            color: #666;
            font-size: 13px;
            margin-top: 3px;
        }

        .post-content {
            color: #333;
            font-size: 14px;
            margin-bottom: 15px;
            padding-left: 63px;
            white-space: pre-wrap;
        }

        .reply-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: #4caf50;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: background 0.3s;
        }

        .reply-btn:hover {
            background: #45a049;
        }

        .reply-node {
            margin-left: 40px;
            border-left: 3px solid #e0e0e0;
            padding-left: 20px;
        }

        .reply-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-left: 63px;
        }

        .reply-form.active {
            display: block;
        }

        .edit-form {
            display: none;
        }

        .media-attachment {
            margin-top: 10px;
            padding: 10px;
            background: #f5f5f5;
            border-radius: 4px;
            border: 1px solid #e0e0e0;
        }

        .action-icons {
            position: absolute;
            top: 60px;
            right: 20px;
            display: flex;
            gap: 10px;
        }

        .action-icon {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 16px;
            opacity: 0.6;
            transition: opacity 0.3s;
        }

        .action-icon:hover {
            opacity: 1;
        }
    </style>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}">Dashboard LMS</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">{{ $mataPelajaran->nama_mapel }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}">Forum</a>
            </li>
            <li class="breadcrumb-item active">Diskusi</li>
        </ol>
    </nav>

    <div class="forum-container">
        <h2 style="margin-bottom: 20px; color: #2c5282;">{{ $forum->judul }}</h2>

        <!-- Search and Filter Bar -->
        <div
            style="background: #f9f9f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e0e0e0;">
            <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                <input type="text" id="searchInput" placeholder="🔍 Cari pesan..."
                    style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">

                <select id="filterRole" style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    <option value="all">Semua Peran</option>
                    <option value="student">Siswa</option>
                    <option value="teacher">Guru</option>
                </select>

                <button onclick="clearFilters()"
                    style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                    Reset Filter
                </button>
            </div>
            <div id="searchResults" style="margin-top: 10px; font-size: 13px; color: #666;"></div>
        </div>

        <div id="postsContainer">
            <!-- Main Topic Post (Guru) -->
            <div class="post" data-author="{{ $forum->user->name }}">
                <div class="post-header">
                    <div class="avatar teacher">{{ substr($forum->user->name, 0, 2) }}</div>
                    <div class="post-info">
                        <div>
                            <span class="author-name">{{ $forum->user->name }}</span>
                            <span class="badge-role badge-guru">Guru (Penulis)</span>
                        </div>
                        <div class="post-date">{{ $forum->created_at->translatedFormat('l, d F Y pukul H:i') }}</div>
                    </div>
                </div>
                <div class="post-content">
                    {!! nl2br(e($forum->content)) !!}
                </div>

                @if(!$forum->is_closed)
                    <button class="reply-btn" onclick="toggleReplyForm('reply-form-main')">REPLY</button>
                @endif

                <!-- Main Reply Form -->
                <div id="reply-form-main" class="reply-form">
                    <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $forum->id]) }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <textarea name="isi" class="form-control" rows="3" placeholder="Tulis balasan Anda..."
                                required></textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label class="small text-muted">Lampiran (Opsional)</label>
                            <input type="file" name="attachment" class="form-control form-control-sm">
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">Kirim Balasan</button>
                            <button type="button" class="btn btn-secondary btn-sm"
                                onclick="toggleReplyForm('reply-form-main')">Batal</button>
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

    <!-- Scroll to Top -->
    <button class="scroll-btn" onclick="scrollToTop()"
        style="position: fixed; bottom: 30px; right: 30px; width: 48px; height: 48px; border-radius: 50%; background: white; border: 2px solid #e0e0e0; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.15);">↓</button>

    <script>
        function toggleReplyForm(id) {
            const form = document.getElementById(id);
            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                document.querySelectorAll('.reply-form').forEach(el => el.style.display = 'none');
                document.querySelectorAll('.edit-form').forEach(el => el.style.display = 'none');
                form.style.display = 'block';
            }
        }

        function toggleEditForm(id) {
            const display = document.getElementById('content-' + id);
            const form = document.getElementById('edit-' + id);

            if (form.style.display === 'block') {
                form.style.display = 'none';
                display.style.display = 'block';
            } else {
                form.style.display = 'block';
                display.style.display = 'none';
            }
        }

        // Search Logic
        const searchInput = document.getElementById('searchInput');
        const filterRole = document.getElementById('filterRole');

        [searchInput, filterRole].forEach(el => el.addEventListener('input', performSearch));

        function performSearch() {
            const term = searchInput.value.toLowerCase();
            const role = filterRole.value;
            const posts = document.querySelectorAll('.post');

            let visible = 0;
            posts.forEach(post => {
                const content = post.dataset.content || '';
                const postRole = post.dataset.role || '';

                const matchTerm = term === '' || content.includes(term);
                const matchRole = role === 'all' || postRole === role;

                if (matchTerm && matchRole) {
                    post.classList.remove('hidden');
                    visible++;
                } else {
                    post.classList.add('hidden');
                }
            });

            document.getElementById('searchResults').innerText = term || role !== 'all' ? `Menampilkan ${visible} pesan` : '';
        }

        function clearFilters() {
            searchInput.value = '';
            filterRole.value = 'all';
            performSearch();
        }

        window.onscroll = function () {
            const btn = document.querySelector('.scroll-btn');
            if (window.scrollY > 300) btn.innerHTML = '↑';
            else btn.innerHTML = '↓';
        };

        function scrollToTop() {
            window.scrollTo({ top: window.scrollY > 300 ? 0 : document.body.scrollHeight, behavior: 'smooth' });
        }
    </script>
@endsection