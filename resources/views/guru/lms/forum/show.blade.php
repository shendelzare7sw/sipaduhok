@extends('layouts.lms-guru')

@section('title', $forum->judul)
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
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
            overflow: hidden;
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
        .attachments-container { padding-left: 56px; }

        .reply-btn {
            position: absolute; top: 18px; right: 16px;
            background: #16a34a; color: white; border: none;
            padding: 6px 16px; border-radius: 6px; cursor: pointer;
            font-size: 13px; font-weight: 600;
            display: flex; align-items: center; gap: 5px;
            transition: background 0.2s;
        }
        .reply-btn:hover { background: #15803d; }

        .reply-level-1 { margin-left: 36px; }
        .reply-level-2 { margin-left: 72px; }
        .reply-level-3 { margin-left: 108px; }
        .reply-level-4 { margin-left: 144px; }
        .reply-level-5 { margin-left: 180px; }

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
        .action-icon.text-primary:hover { color: #16a34a !important; }

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

        @keyframes highlightFade {
            0%   { background-color: #fef9c3; }
            100% { background-color: white; }
        }
        .highlight { animation: highlightFade 2s ease-out; }

        @media (max-width: 768px) {
            .forum-container { padding: 12px; }
            .post { padding: 14px; }
            .post-content, .attachments-container { padding-left: 0; }
            .reply-btn { position: static; margin-top: 10px; }
            .reply-level-1 { margin-left: 14px; }
            .reply-level-2 { margin-left: 28px; }
            .reply-level-3 { margin-left: 40px; }
            .reply-level-4 { margin-left: 52px; }
            .reply-level-5 { margin-left: 60px; }
            .search-filter-bar { padding: 12px; }
        }

        @media (max-width: 480px) {
            .reply-level-1 { margin-left: 8px; }
            .reply-level-2 { margin-left: 12px; }
            .reply-level-3 { margin-left: 16px; }
            .reply-level-4 { margin-left: 18px; }
            .reply-level-5 { margin-left: 20px; }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid">
        <div class="mb-3">
            <a href="{{ route('guru.lms.forum.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
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

                    <select id="filterAuthor">
                        <option value="all">Semua Pengirim</option>
                        <option value="my">Pesan Saya</option>
                    </select>

                    <button onclick="clearFilters()">
                        <i class="fas fa-redo-alt me-1"></i> Reset Filter
                    </button>
                </div>
                <div id="searchResults" style="margin-top: 10px; font-size: 13px; color: #666;"></div>
            </div>

            <div id="postsContainer">
                <!-- Main Topic Post (Guru/Creator) -->
                <div class="post" data-author="{{ $forum->user->name ?? 'User' }}"
                    data-role="{{ $forum->isFromTeacher() ? 'teacher' : 'student' }}"
                    data-is-mine="{{ $forum->user_id == auth()->id() ? 'true' : 'false' }}">

                    <div class="post-header">
                        <div class="avatar {{ $forum->isFromTeacher() ? 'teacher' : 'student' }}">
                            @if($forum->user && $forum->user->foto_profil)
                                <img src="{{ asset('storage/' . $forum->user->foto_profil) }}" alt="{{ $forum->user->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
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
                        <button class="reply-btn" onclick="toggleReplyForm('reply-form-main')">
                            <i class="fas fa-reply me-1"></i> REPLY
                        </button>
                    @endif

                    <!-- Reply Form Main -->
                    <div id="reply-form-main" class="reply-form">
                        <form action="{{ route('guru.lms.forum.reply', [$kelas->id, $mapel->id, $forum->id]) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <textarea name="isi" placeholder="Tulis balasan Anda..." required></textarea>

                            <button type="button" class="attach-btn" onclick="toggleAttachArea(this)">
                                <i class="fas fa-paperclip"></i> Lampirkan File
                            </button>

                            <div class="file-upload-area">
                                <p class="mb-1"><i class="fas fa-cloud-upload-alt fa-2x text-muted"></i></p>
                                <p>Klik untuk memilih file (bisa pilih multiple)</p>
                                <input type="file" name="attachment[]" multiple style="display: block; width: 100%;">
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
                    @include('guru.lms.forum.partials.reply-item-redesign', ['reply' => $reply, 'level' => 0])
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function toggleReplyForm(id) {
            const form = document.getElementById(id);
            if (form) {
                if (form.style.display === 'block') {
                    form.style.display = 'none';
                } else {
                    form.style.display = 'block';
                }
            }
        }
        // ... func continues


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

        // Search and Filter Functions
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const filterRole = document.getElementById('filterRole');
            const filterAuthor = document.getElementById('filterAuthor');

            if (searchInput) {
                searchInput.addEventListener('input', performSearch);
                filterRole.addEventListener('change', performSearch);
                filterAuthor.addEventListener('change', performSearch);
            }
        });

        function handleFileSelect(event) {
            const file = event.target.files[0];
            const uploadArea = event.target.closest('.file-upload-area');
            if (!uploadArea) return;

            // Remove existing preview
            const existingPreview = uploadArea.querySelector('.file-preview');
            if (existingPreview) existingPreview.remove();

            if (file) {
                const reader = new FileReader();
                const previewDiv = document.createElement('div');
                previewDiv.className = 'file-preview mt-3 text-center';

                reader.onload = function (e) {
                    if (file.type.startsWith('image/')) {
                        previewDiv.innerHTML = `
                            <img src="${e.target.result}" class="img-fluid rounded border" style="max-height: 200px;">
                            <p class="small text-muted mt-1">${file.name}</p>
                        `;
                    } else if (file.type.startsWith('video/')) {
                        previewDiv.innerHTML = `
                            <video controls class="w-100 rounded border" style="max-height: 200px;">
                                <source src="${e.target.result}" type="${file.type}">
                                Browser Anda tidak mendukung preview video.
                            </video>
                             <p class="small text-muted mt-1">${file.name}</p>
                        `;
                    } else {
                        previewDiv.innerHTML = `
                            <div class="p-3 border rounded bg-light">
                                <i class="fas fa-file-alt fa-2x text-primary mb-2"></i>
                                <p class="mb-0 text-truncate">${file.name}</p>
                            </div>
                        `;
                    }
                }
                reader.readAsDataURL(file);
                uploadArea.appendChild(previewDiv);
            }
        }

        document.addEventListener('change', function (e) {
            if (e.target && e.target.type === 'file' && e.target.name === 'attachment') {
                handleFileSelect(e);
            }
        });

        function performSearch() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('filterRole').value;
            const authorFilter = document.getElementById('filterAuthor').value;

            const posts = document.querySelectorAll('.post');
            let visibleCount = 0;

            posts.forEach(post => {
                // Skip main post if you only want to search replies, but typically we search all
                const content = post.querySelector('.post-content').textContent.toLowerCase();
                const author = (post.dataset.author || '').toLowerCase();
                const role = post.dataset.role || '';
                const isMine = post.dataset.isMine === 'true';

                let matchSearch = searchTerm === '' || content.includes(searchTerm) || author.includes(searchTerm);
                let matchRole = roleFilter === 'all' || role === roleFilter;
                let matchAuthor = authorFilter === 'all' || (authorFilter === 'my' && isMine);

                if (matchSearch && matchRole && matchAuthor) {
                    post.classList.remove('hidden');
                    visibleCount++;
                } else {
                    post.classList.add('hidden');
                }
            });

            const resultsDiv = document.getElementById('searchResults');
            if (searchTerm !== '' || roleFilter !== 'all' || authorFilter !== 'all') {
                resultsDiv.textContent = `Menampilkan ${visibleCount} dari ${posts.length} pesan`;
            } else {
                resultsDiv.textContent = '';
            }
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('filterRole').value = 'all';
            document.getElementById('filterAuthor').value = 'all';
            performSearch();
        }

        function confirmDeleteReply(url) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: "Apakah Anda yakin ingin menghapus balasan ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
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
    </script>
@endpush
