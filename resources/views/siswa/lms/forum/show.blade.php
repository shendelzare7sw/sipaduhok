@extends('layouts.lms')

@section('title', $forum->judul)
@section('page-title', 'Diskusi Forum')
@section('page-subtitle', $mataPelajaran->nama_mapel)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    {{-- Include Shared Styles --}}
    @include('lms.forum.styles')

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
            <!-- Main Topic Post (Guru/Creator) -->
            <div class="post" data-author="{{ $forum->user->name ?? 'User' }}"
                data-role="{{ $forum->isFromTeacher() ? 'teacher' : 'student' }}">
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
                        <div class="post-date">{{ $forum->created_at->translatedFormat('l, d F Y \p\u\k\u\l H:i') }}</div>
                    </div>
                </div>
                <div class="post-content">
                    {!! nl2br(e($forum->content ?? $forum->isi)) !!}

                    @if($forum->lampiran)
                        <div class="media-attachment mt-3">
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
                                    Browser Anda tidak mendukung tag video.
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

                        {{-- Attachment Dropzone --}}
                        <button type="button" class="attachment-toggle-btn" onclick="toggleAttachment('reply-form-main')">
                            <i class="fas fa-paperclip"></i> Lampirkan File
                        </button>
                        <div id="attachment-area-reply-form-main" class="attachment-area">
                            <div class="dropzone-wrapper">
                                <div class="dropzone-desc">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p class="mb-0">Drag & Drop file disini atau klik untuk memilih</p>
                                </div>
                                <input type="file" name="attachment" class="dropzone-file-input" onchange="updateFileName(this, 'reply-form-main')">
                            </div>
                            <div id="file-name-reply-form-main" class="file-preview-name"></div>
                        </div>
                        <div class="d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-secondary btn-sm"
                                onclick="toggleReplyForm('reply-form-main')">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm">Kirim Balasan</button>
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
    <button class="scroll-btn" onclick="scrollToTop()">↓</button>

    @push('scripts')
        <script>
            function toggleReplyForm(id) {
                const form = document.getElementById(id);
                if (!form) return;

                // Close others
                document.querySelectorAll('.reply-form').forEach(el => {
                    if (el.id !== id) el.style.display = 'none';
                });
                // Also close edit forms
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

                // Close others
                document.querySelectorAll('.edit-form').forEach(el => {
                    if (el.id !== 'edit-' + id) el.style.display = 'none';
                });
                document.querySelectorAll('.post-content').forEach(el => el.style.display = 'block'); // reset others
                document.querySelectorAll('.reply-form').forEach(el => el.style.display = 'none');

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
                    // Check post content (for root posts)
                    let contentText = '';
                    const contentEl = post.querySelector('.post-content');
                    if (contentEl) contentText = contentEl.innerText.toLowerCase();
                    // Check data attributes which might be set on parent .post divs in partials
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
                searchInput.value = '';
                filterRole.value = 'all';
                performSearch();
            }

            window.onscroll = function () {
                const btn = document.querySelector('.scroll-btn');
                if (!btn) return;
                if (window.scrollY > 300) btn.innerText = '↑';
                else btn.innerHTML = '↓';
            };

            function scrollToTop() {
                window.scrollTo({ top: window.scrollY > 300 ? 0 : document.body.scrollHeight, behavior: 'smooth' });
            }

            // Attachment Toggle & Dropzone Logic
            function toggleAttachment(id) {
                const area = document.getElementById('attachment-area-' + id);
                // Find button that triggered it
                const btn = document.querySelector(`.attachment-toggle-btn[onclick="toggleAttachment('${id}')"]`);

                if(area) {
                    if(window.getComputedStyle(area).display !== 'none') {
                         area.style.display = 'none';
                         if(btn) btn.classList.remove('active');
                    } else {
                         area.style.display = 'block';
                         if(btn) btn.classList.add('active');
                    }
                }
            }

            function updateFileName(input, id) {
                const nameDisplay = document.getElementById('file-name-' + id);
                if (input.files && input.files[0]) {
                    nameDisplay.textContent = 'File terpilih: ' + input.files[0].name;
                    nameDisplay.style.display = 'block';

                    // Add active class to wrapper
                    const wrapper = input.closest('.dropzone-wrapper');
                    if(wrapper) wrapper.style.borderColor = '#2196f3';
                } else {
                    nameDisplay.style.display = 'none';
                }
            }
        </script>
    @endpush
@endsection
