@extends('layouts.lms')

@section('title', $forum->judul)
@section('page-title', 'Diskusi Forum')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    <style>
        .forum-container {
            max-width: 100%;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }

        .post {
            border: 1px solid #e8eaed;
            border-radius: 10px;
            padding: 18px;
            margin-bottom: 14px;
            background: white;
            position: relative;
            box-shadow: 0 1px 4px rgba(0,0,0,.06);
        }

        .post:hover { box-shadow: 0 3px 10px rgba(0,0,0,.09); }

        .post-header { display: flex; align-items: center; margin-bottom: 12px; }

        .avatar {
            width: 44px; height: 44px; border-radius: 50%;
            background: #16a34a;
            color: white; display: flex; align-items: center; justify-content: center;
            font-weight: bold; font-size: 15px; margin-right: 12px; flex-shrink: 0;
            overflow: hidden; text-transform: uppercase;
        }
        .avatar.teacher { background: #16a34a; }
        .avatar.student { background: #0ea5e9; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }

        .post-info { flex-grow: 1; min-width: 0; }
        .author-name { font-weight: 600; color: #1a1a1a; font-size: 14px; }

        .badge-role {
            display: inline-block; padding: 2px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600; margin-left: 6px;
        }
        .badge-siswa { background: #e0f2fe; color: #0369a1; border: 1px solid #bae6fd; }
        .badge-guru  { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }

        .post-date { color: #999; font-size: 12px; margin-top: 3px; }

        .post-content {
            color: #333; font-size: 14px; margin-bottom: 12px;
            padding-left: 56px; line-height: 1.6;
        }

        .reply-btn {
            position: absolute; top: 18px; right: 16px;
            background: #16a34a; color: white; border: none;
            padding: 6px 16px; border-radius: 6px; cursor: pointer;
            font-size: 13px; font-weight: 600;
            display: flex; align-items: center; gap: 5px;
            transition: background 0.2s;
        }
        .reply-btn:hover { background: #15803d; }

        .reply-node { margin-left: 36px; border-left: 3px solid #e0e0e0; padding-left: 16px; }

        .reply-form {
            display: none; margin-top: 14px; padding: 14px;
            background: #f8f9fa; border-radius: 8px; border: 1px solid #e8eaed;
        }
        .reply-form.active { display: block; }

        .reply-form textarea {
            width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;
            font-family: inherit; font-size: 14px; resize: vertical;
            min-height: 80px; margin-bottom: 10px;
        }
        .reply-form textarea:focus {
            border-color: #16a34a; outline: none;
            box-shadow: 0 0 0 3px rgba(22,163,74,.12);
        }

        .attach-btn {
            background: white; border: 1px solid #ddd; padding: 7px 12px;
            border-radius: 6px; cursor: pointer; font-size: 13px;
            display: inline-flex; align-items: center; gap: 5px;
            transition: all 0.2s; margin-bottom: 10px; color: #555;
        }
        .attach-btn:hover, .attach-btn.active {
            border-color: #16a34a; color: #16a34a; background: #f0fdf4;
        }

        .file-upload-area {
            display: none; border: 2px dashed #ddd; border-radius: 8px;
            padding: 18px; text-align: center; margin-bottom: 10px;
            cursor: pointer; transition: all 0.2s; background: #fafafa;
        }
        .file-upload-area:hover { border-color: #16a34a; background: #f0fdf4; }
        .file-upload-area.show { display: block; }

        .action-icon {
            background: none; border: none; cursor: pointer;
            font-size: 1rem; padding: 4px; opacity: 0.45;
            transition: opacity 0.2s; color: #555;
        }
        .action-icon:hover { opacity: 1; }

        .search-filter-bar {
            background: white; padding: 14px; border-radius: 10px;
            margin-bottom: 18px; border: 1px solid #e8eaed;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }
        .search-filter-bar input,
        .search-filter-bar select {
            background: #f9fafb; border: 1px solid #e8eaed !important;
            border-radius: 6px; padding: 9px 12px; font-size: 14px;
        }
        .search-filter-bar input:focus,
        .search-filter-bar select:focus {
            border-color: #16a34a !important; outline: none;
            box-shadow: 0 0 0 3px rgba(22,163,74,.12);
        }
        .search-filter-bar button {
            background: #16a34a; color: white; border: none;
            border-radius: 6px; padding: 9px 18px;
            cursor: pointer; font-size: 13px; font-weight: 600;
            transition: background 0.2s;
        }
        .search-filter-bar button:hover { background: #15803d; }

        #searchResults { color: #666; font-size: 13px; margin-top: 8px; }

        .hidden { display: none !important; }

        @keyframes highlightFade {
            0%   { background-color: #fef9c3; }
            100% { background-color: white; }
        }
        .highlight { animation: highlightFade 2s ease-out; }

        @media (max-width: 768px) {
            .forum-container { padding: 12px; }
            .post { padding: 14px; }
            .post-content { padding-left: 0; }
            .reply-btn { position: static; margin-top: 10px; }
            .reply-node { margin-left: 14px; padding-left: 12px; }
            .search-filter-bar { padding: 12px; }
        }

        @media (max-width: 480px) {
            .reply-node { margin-left: 8px; padding-left: 8px; }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Kembali ke Daftar Topik
            </a>
        </div>

        <div class="forum-container">
            <h2 style="margin-bottom: 20px; color: #2c5282;">{{ $forum->judul }}</h2>

            <!-- Search and Filter Bar -->
            <div class="search-filter-bar">
                <div style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center;">
                    <input type="text" id="searchInput" placeholder="🔍 Cari pesan..."
                        style="flex: 1; min-width: 200px;">

                    <select id="filterRole">
                        <option value="all">Semua Peran</option>
                        <option value="student">Siswa</option>
                        <option value="teacher">Guru</option>
                    </select>

                    <button onclick="clearFilters()">
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
                                        <img src="{{ Storage::url($forum->lampiran) }}" alt="Lampiran" class="img-fluid rounded"
                                            style="max-height: 300px;">
                                    </a>
                                @elseif($isVideo)
                                    <video controls class="w-100 rounded" style="max-height: 300px;">
                                        <source src="{{ Storage::url($forum->lampiran) }}">
                                    </video>
                                @elseif($isPdf)
                                    <iframe src="{{ Storage::url($forum->lampiran) }}" width="100%" height="400px"
                                        style="border: 1px solid #ddd; border-radius: 4px;"></iframe>
                                @else
                                    <a href="{{ Storage::url($forum->lampiran) }}" target="_blank" class="btn btn-sm btn-light border">
                                        <i class="fas fa-paperclip me-1"></i> Lihat Lampiran
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!$forum->is_closed)
                        <button class="reply-btn" onclick="toggleReplyForm('reply-form-main')">
                            <i class="fas fa-reply me-1"></i> REPLY
                        </button>
                    @endif

                    <!-- Reply Form Main -->
                    <div id="reply-form-main" class="reply-form">
                        <form action="{{ route('siswa.lms.mapel.forum.reply', [$mataPelajaran->id, $forum->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <textarea name="isi" placeholder="Tulis balasan Anda..." required></textarea>

                            <button type="button" class="attach-btn" onclick="toggleAttachArea(this)">
                                <i class="fas fa-paperclip"></i> Lampirkan File
                            </button>

                            <div class="file-upload-area">
                                <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                                <p>Klik untuk memilih file</p>
                                <input type="file" name="attachment" style="display: block; width: 100%;">
                            </div>

                            <div class="d-flex gap-2 mt-3">
                                <button type="submit" class="btn btn-primary btn-sm px-4">Kirim Balasan</button>
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
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDeleteReply(url) {
            Swal.fire({
                title: 'Hapus Balasan?',
                text: "Balasan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;
                    form.style.display = 'none';

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';
                    form.appendChild(csrf);

                    const method = document.createElement('input');
                    method.type = 'hidden';
                    method.name = '_method';
                    method.value = 'DELETE';
                    form.appendChild(method);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function toggleReplyForm(id) {
            const form = document.getElementById(id);
            if (!form) return;

            // Close other forms
            document.querySelectorAll('.reply-form').forEach(el => {
                if (el.id !== id) el.style.display = 'none';
            });
            document.querySelectorAll('.edit-form').forEach(el => el.style.display = 'none');
            document.querySelectorAll('.post-content').forEach(el => el.style.display = 'block');

            if (form.style.display === 'block') {
                form.style.display = 'none';
            } else {
                form.style.display = 'block';
            }
        }

        function toggleEditForm(id) {
            const display = document.getElementById('content-' + id);
            const form = document.getElementById('edit-' + id);
            if (!form || !display) return;

            document.querySelectorAll('.edit-form').forEach(el => {
                if (el.id !== 'edit-' + id) el.style.display = 'none';
            });
            document.querySelectorAll('.post-content').forEach(el => el.style.display = 'block');
            document.querySelectorAll('.reply-form').forEach(el => el.style.display = 'none');

            if (form.style.display === 'block') {
                form.style.display = 'none';
                display.style.display = 'block';
            } else {
                form.style.display = 'block';
                display.style.display = 'none';
            }
        }

        function toggleAttachArea(btn) {
            const area = btn.nextElementSibling;
            if (area.style.display === 'block') {
                area.style.display = 'none';
                btn.classList.remove('active');
            } else {
                area.style.display = 'block';
                btn.classList.add('active');
            }
        }

        // Search and Filter
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterRole = document.getElementById('filterRole');

            if (searchInput) {
                searchInput.addEventListener('input', performSearch);
                filterRole.addEventListener('change', performSearch);
            }
        });

        function performSearch() {
            const term = document.getElementById('searchInput').value.toLowerCase();
            const role = document.getElementById('filterRole').value;
            const posts = document.querySelectorAll('.post');

            let visible = 0;
            posts.forEach(post => {
                let contentText = '';
                const contentEl = post.querySelector('.post-content');
                if (contentEl) contentText = contentEl.innerText.toLowerCase();
                const author = (post.getAttribute('data-author') || '').toLowerCase();
                const postRole = post.getAttribute('data-role') || '';

                const matchTerm = term === '' || contentText.includes(term) || author.includes(term);
                const matchRole = role === 'all' || postRole === role;

                if (matchTerm && matchRole) {
                    post.classList.remove('hidden');
                    visible++;
                } else {
                    post.classList.add('hidden');
                }
            });

            const resDiv = document.getElementById('searchResults');
            if (term || role !== 'all') {
                resDiv.innerText = `Menampilkan ${visible} pesan`;
            } else {
                resDiv.innerText = '';
            }
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterRole').value = 'all';
            performSearch();
        }
    </script>
@endpush
