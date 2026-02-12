@extends('layouts.lms-guru')

@section('title', $forum->judul)
@section('page-title', 'Forum Diskusi')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection


@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection


@push('styles')
    <style>
        /* Soft UI Theme for Forum */
        .forum-container {
            max-width: 100%;
            margin: 0 auto;
            background: linear-gradient(135deg, #f8f9fa 0%, #f5f6f8 100%);
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        .post {
            border: 1px solid #e8eaed;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            background: white;
            position: relative;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .post:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            margin-right: 15px;
            flex-shrink: 0;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            overflow: hidden;
            border: 3px solid transparent;
        }

        .avatar.teacher {
            background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.3);
            border-color: #4caf50;
        }

        .avatar.student {
            background: linear-gradient(135deg, #00a8e8 0%, #0088b8 100%);
            box-shadow: 0 2px 8px rgba(0, 168, 232, 0.3);
            border-color: #00a8e8;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .post-info {
            flex-grow: 1;
        }

        .author-name {
            font-weight: 600;
            color: #1a1a1a;
            margin-right: 10px;
            font-size: 15px;
        }

        .badge-role {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            margin-left: 8px;
            letter-spacing: 0.5px;
        }

        .badge-siswa {
            background: linear-gradient(135deg, #e0f2f7 0%, #d4ebf7 100%);
            color: #0088b8;
            border: 1px solid rgba(0, 136, 184, 0.2);
        }

        .badge-guru {
            background: linear-gradient(135deg, #e8f5e9 0%, #d4edda 100%);
            color: #2e7d32;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .post-date {
            color: #999;
            font-size: 13px;
            margin-top: 4px;
        }

        .post-content {
            color: #333;
            font-size: 14px;
            margin-bottom: 15px;
            padding-left: 63px;
            line-height: 1.6;
        }

        .attachments-container {
            padding-left: 63px;
        }

        .reply-btn {
            position: absolute;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        .reply-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .reply-level-1 { margin-left: 40px; }
        .reply-level-2 { margin-left: 80px; }
        .reply-level-3 { margin-left: 120px; }
        .reply-level-4 { margin-left: 160px; }
        .reply-level-5 { margin-left: 200px; }

        @media (max-width: 768px) {
            .reply-level-1 { margin-left: 15px; }
            .reply-level-2 { margin-left: 30px; }
            .reply-level-3 { margin-left: 45px; }
            .reply-level-4 { margin-left: 60px; }
            .reply-level-5 { margin-left: 75px; }
        }

        .reply-form {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #f0f3f7 100%);
            border-radius: 8px;
            border: 1px solid #e8eaed;
        }

        .reply-form.active {
            display: block;
        }

        .reply-form textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: inherit;
            font-size: 14px;
            resize: vertical;
            min-height: 80px;
            margin-bottom: 10px;
            transition: all 0.2s ease;
        }

        .reply-form textarea:focus {
            border-color: #667eea;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .attach-btn {
            background: white;
            border: 1px solid #ddd;
            padding: 8px 14px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 13px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.3s ease;
            margin-bottom: 10px;
            color: #666;
        }

        .attach-btn:hover {
            background: #f5f7fa;
            border-color: #667eea;
            color: #667eea;
        }

        .attach-btn.active {
            background: linear-gradient(135deg, #e8ecff 0%, #f0f3ff 100%);
            border-color: #667eea;
            color: #667eea;
        }

        .file-upload-area {
            display: none;
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
        }

        .file-upload-area:hover {
            border-color: #667eea;
            background: linear-gradient(135deg, #f0f3ff 0%, #e8ecff 100%);
        }

        .file-upload-area.show {
            display: block;
        }

        .action-icon {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 5px;
            opacity: 0.5;
            transition: all 0.2s ease;
            color: #666;
        }

        .action-icon:hover {
            opacity: 1;
        }

        .action-icon.text-danger:hover {
            color: #dc3545;
        }

        .action-icon.text-primary:hover {
            color: #667eea;
        }

        /* Filter and Search Bar Soft Style */
        .search-filter-bar {
            background: white;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #e8eaed;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .search-filter-bar input,
        .search-filter-bar select {
            background: linear-gradient(135deg, #fafbfc 0%, #f5f7fa 100%);
            border: 1px solid #e8eaed !important;
            border-radius: 6px;
            padding: 10px 12px;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .search-filter-bar input:focus,
        .search-filter-bar select:focus {
            border-color: #667eea !important;
            outline: none;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .search-filter-bar button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .search-filter-bar button:hover {
            transform: translateY(-2px);
            box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
        }

        #searchResults {
            color: #666;
            font-size: 13px;
            margin-top: 8px;
        }

        .highlight {
            animation: highlightFade 2s ease-out;
        }

        @keyframes highlightFade {
            0% {
                background-color: #fff3cd;
            }
            100% {
                background-color: white;
            }
        }

        @media (max-width: 768px) {
            .reply-node {
                margin-left: 20px;
                padding-left: 15px;
            }

            .post-content {
                padding-left: 0;
            }

            .reply-btn {
                position: static;
                margin-top: 10px;
            }

            .attachments-container {
                padding-left: 0;
            }
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
                            <div class="post-date">{{ $forum->created_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}
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
    <!-- Delete Reply Modal -->
    <div class="modal fade" id="deleteReplyModal" tabindex="-1" aria-labelledby="deleteReplyLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteReplyLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus balasan ini?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form id="deleteReplyForm" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
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
            document.getElementById('deleteReplyForm').action = url;
            var modal = new bootstrap.Modal(document.getElementById('deleteReplyModal'));
            modal.show();
        }
    </script>
@endpush
