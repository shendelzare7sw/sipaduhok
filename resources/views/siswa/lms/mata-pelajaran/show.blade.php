@extends('layouts.lms')

@section('title', $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Kelola Pertemuan')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .alert-warning-custom {
            background: #fff3cd;
            border-left: 4px solid #ff9800;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-size: 14px;
            color: #856404;
        }

        .pertemuan-bar {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            margin-bottom: 15px;
            background: white;
            overflow: hidden;
        }

        .pertemuan-header {
            padding: 15px 20px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background 0.2s;
        }

        .pertemuan-header:hover {
            background: #e9ecef;
        }

        .pertemuan-header.active {
            background: #e3f2fd;
            border-left: 4px solid #2196F3;
        }

        .pertemuan-title {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #333;
        }

        .toggle-icon {
            transition: transform 0.3s;
            font-size: 12px;
        }

        .toggle-icon.open {
            transform: rotate(90deg);
        }

        .pertemuan-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .pertemuan-content.open {
            max-height: 5000px;
        }

        .category-bar {
            border-bottom: 1px solid #f0f0f0;
        }

        .category-bar:last-child {
            border-bottom: none;
        }

        .category-header {
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .category-header:hover {
            background: #f8f9fa;
        }

        .category-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex: 1;
        }

        .category-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            flex-shrink: 0;
        }

        .icon-modul { background: #e3f2fd; color: #2196F3; }
        .icon-materi { background: #fce4ec; color: #e91e63; }
        .icon-meeting { background: #e8f5e9; color: #4caf50; }
        .icon-forum { background: #fff3e0; color: #ff9800; }
        .icon-kuis { background: #f3e5f5; color: #9c27b0; }
        .icon-tugas { background: #ffebee; color: #f44336; }

        .category-info {
            flex: 1;
        }

        .category-name {
            font-weight: 500;
            color: #333;
            margin-bottom: 2px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .category-desc {
            font-size: 12px;
            color: #666;
        }

        .toggle-category {
            font-size: 10px;
            color: #999;
            transition: transform 0.3s;
        }

        .toggle-category.open {
            transform: rotate(90deg);
        }

        .badge-count {
            background: #f44336;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-warning {
            background: #ff9800;
        }

        .category-items {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            background: #fafafa;
        }

        .category-items.open {
            max-height: 3000px;
        }

        .item-card {
            padding: 12px 20px 12px 64px;
            border-bottom: 1px solid #e8e8e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .item-card:last-child {
            border-bottom: none;
        }

        .item-card.from-previous {
            background: #fff9e6;
            border-left: 3px solid #ff9800;
        }

        .item-info h4 {
            font-size: 14px;
            color: #333;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .item-meta {
            font-size: 12px;
            color: #666;
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .previous-tag {
            background: #ff9800;
            color: white;
            padding: 3px 8px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 600;
        }

        .status-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 500;
        }

        .status-belum {
            background: #ffebee;
            color: #c62828;
        }

        .status-selesai {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .item-actions {
            display: flex;
            gap: 8px;
            flex-shrink: 0;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn-small {
            padding: 6px 12px;
            border: 1px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            color: #666;
            transition: all 0.2s;
            white-space: nowrap;
            text-decoration: none;
        }

        .btn-small:hover {
            background: #f5f5f5;
            border-color: #999;
            text-decoration: none;
        }

        .btn-primary-sm {
            background: #2196F3;
            color: white;
            border-color: #2196F3;
        }

        .btn-primary-sm:hover {
            background: #1976D2;
            color: white;
        }

        .no-content {
            padding: 20px;
            text-align: center;
            color: #999;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .pertemuan-header, .category-header, .item-card {
                padding: 12px 15px;
            }

            .item-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding-left: 50px;
            }

            .item-actions {
                width: 100%;
            }

            .btn-small {
                flex: 1;
            }
        }
    </style>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}">Dashboard LMS</a></li>
            <li class="breadcrumb-item active">{{ $mataPelajaran->nama_mapel }}</li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-white rounded shadow-sm">
        <div>
            <h4 class="mb-1"><i class="fas fa-book text-primary me-2"></i>{{ $mataPelajaran->nama_mapel }}</h4>
            <span class="text-muted">{{ optional($mataPelajaran->guruPengajar->first())->tenagaPendidik->nama ?? 'Guru Pengajar' }}</span>
        </div>
        <span class="text-muted">Semester {{ now()->month >= 7 ? 'Ganjil' : 'Genap' }} {{ now()->year }}/{{ now()->year + 1 }}</span>
    </div>

    @php
        // Calculate incomplete tasks and quizzes from previous weeks
        $incompleteTasks = collect();
        $incompleteQuizzes = collect();
        $latestPertemuan = $pertemuans->first();
        
        foreach($pertemuans->skip(1) as $prevPertemuan) {
            foreach($prevPertemuan->tugas as $tugas) {
                // Check if not submitted (you'd need logic for your actual submission check)
                $incompleteTasks->push([
                    'tugas' => $tugas,
                    'from_pekan' => $prevPertemuan->pekan ?? 'Sebelumnya',
                    'pertemuan_title' => $prevPertemuan->judul
                ]);
            }
            foreach($prevPertemuan->ujian as $ujian) {
                $incompleteQuizzes->push([
                    'ujian' => $ujian,
                    'from_pekan' => $prevPertemuan->pekan ?? 'Sebelumnya',
                    'pertemuan_title' => $prevPertemuan->judul
                ]);
            }
        }
        
        $totalIncomplete = $incompleteTasks->count() + $incompleteQuizzes->count();
    @endphp

    @if($totalIncomplete > 0)
        <div class="alert-warning-custom">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Perhatian:</strong> Anda memiliki {{ $incompleteTasks->count() }} tugas dan {{ $incompleteQuizzes->count() }} kuis yang belum dikerjakan dari pekan sebelumnya.
        </div>
    @endif

    <!-- Pertemuan List -->
    @forelse($pertemuans as $index => $pertemuan)
        <div class="pertemuan-bar">
            <div class="pertemuan-header {{ $index === 0 ? 'active' : '' }}" onclick="togglePertemuan(this)">
                <div class="pertemuan-title">
                    <i class="fas fa-chevron-right toggle-icon {{ $index === 0 ? 'open' : '' }}"></i>
                    <span>Pertemuan Pekan {{ $pertemuan->pekan ?? ($index + 1) }} - {{ $pertemuan->tanggal->translatedFormat('d F Y') }}</span>
                </div>
                <span class="text-muted" style="font-size: 13px;">{{ $pertemuan->judul }}</span>
            </div>
            <div class="pertemuan-content {{ $index === 0 ? 'open' : '' }}">
                
                @php
                    // Separate materi by kategori
                    $modulAjar = $pertemuan->materi->where('kategori', 'modul_ajar');
                    $materiPendukung = $pertemuan->materi->where('kategori', '!=', 'modul_ajar');
                @endphp

                <!-- Modul Ajar -->
                <div class="category-bar">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-left">
                            <i class="fas fa-chevron-right toggle-category {{ $modulAjar->count() > 0 ? 'open' : '' }}"></i>
                            <div class="category-icon icon-modul"><i class="fas fa-book-open"></i></div>
                            <div class="category-info">
                                <div class="category-name">
                                    Modul Ajar
                                    @if($modulAjar->count() > 0)
                                        <span class="badge-count">{{ $modulAjar->count() }}</span>
                                    @endif
                                </div>
                                <div class="category-desc">{{ $modulAjar->count() > 0 ? 'Klik untuk lihat detail modul' : 'Tidak ada modul' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="category-items {{ $modulAjar->count() > 0 ? 'open' : '' }}">
                        @forelse($modulAjar as $modul)
                            <div class="item-card">
                                <div class="item-info">
                                    <h4>{{ $modul->judul_materi }}</h4>
                                    <div class="item-meta">
                                        <span><i class="far fa-calendar me-1"></i>{{ $modul->tanggal_upload->translatedFormat('d M Y') }}</span>
                                        <span><i class="fas fa-file-alt me-1"></i>{{ strtoupper($modul->tipe_file) }}</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ route('siswa.lms.mapel.materi', [$mataPelajaran->id, $modul->id]) }}" class="btn-small">Lihat</a>
                                    @if($modul->file_materi)
                                        <a href="{{ asset('storage/' . $modul->file_materi) }}" class="btn-small btn-primary-sm" download>Download</a>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="no-content">Tidak ada modul ajar</div>
                        @endforelse
                    </div>
                </div>

                <!-- Materi Pendukung -->
                <div class="category-bar">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-left">
                            <i class="fas fa-chevron-right toggle-category {{ $materiPendukung->count() > 0 ? 'open' : '' }}"></i>
                            <div class="category-icon icon-materi"><i class="fas fa-file-alt"></i></div>
                            <div class="category-info">
                                <div class="category-name">
                                    Materi
                                    @if($materiPendukung->count() > 0)
                                        <span class="badge-count">{{ $materiPendukung->count() }}</span>
                                    @endif
                                </div>
                                <div class="category-desc">{{ $materiPendukung->count() > 0 ? 'Slide, video, dan dokumen pendukung' : 'Tidak ada materi' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="category-items {{ $materiPendukung->count() > 0 ? 'open' : '' }}">
                        @forelse($materiPendukung as $materi)
                            <div class="item-card">
                                <div class="item-info">
                                    <h4>{{ $materi->judul_materi }}</h4>
                                    <div class="item-meta">
                                        <span><i class="far fa-calendar me-1"></i>{{ $materi->tanggal_upload->translatedFormat('d M Y') }}</span>
                                        <span><i class="fas fa-file-alt me-1"></i>{{ strtoupper($materi->tipe_file) }}</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ route('siswa.lms.mapel.materi', [$mataPelajaran->id, $materi->id]) }}" class="btn-small btn-primary-sm">Lihat</a>
                                </div>
                            </div>
                        @empty
                            <div class="no-content">Tidak ada materi pendukung</div>
                        @endforelse
                    </div>
                </div>

                <!-- Meeting/Zoom -->
                <div class="category-bar">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-left">
                            <i class="fas fa-chevron-right toggle-category {{ $pertemuan->zoom_link ? 'open' : '' }}"></i>
                            <div class="category-icon icon-meeting"><i class="fas fa-video"></i></div>
                            <div class="category-info">
                                <div class="category-name">Meeting</div>
                                <div class="category-desc">{{ $pertemuan->zoom_link ? $pertemuan->tanggal->translatedFormat('l, d M Y - H:i') . ' WIB' : 'Tidak ada meeting terjadwal' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="category-items {{ $pertemuan->zoom_link ? 'open' : '' }}">
                        @if($pertemuan->zoom_link)
                            <div class="item-card">
                                <div class="item-info">
                                    <h4>Live Session: {{ $pertemuan->judul }}</h4>
                                    <div class="item-meta">
                                        @if($pertemuan->tanggal->isToday())
                                            <span style="color: #4caf50; font-weight: 600;"><i class="fas fa-circle text-danger me-1"></i>Hari Ini</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ $pertemuan->zoom_link }}" target="_blank" class="btn-small btn-primary-sm">
                                        <i class="fas fa-video me-1"></i>Join Zoom
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="no-content">Tidak ada meeting terjadwal</div>
                        @endif
                    </div>
                </div>

                <!-- Forum Diskusi -->
                <div class="category-bar">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-left">
                            <i class="fas fa-chevron-right toggle-category"></i>
                            <div class="category-icon icon-forum"><i class="fas fa-comments"></i></div>
                            <div class="category-info">
                                <div class="category-name">
                                    Forum Diskusi
                                    @if($pertemuan->forumDiskusi->count() > 0)
                                        <span class="badge-count badge-warning">{{ $pertemuan->forumDiskusi->count() }}</span>
                                    @endif
                                </div>
                                <div class="category-desc">{{ $pertemuan->forumDiskusi->count() > 0 ? $pertemuan->forumDiskusi->count() . ' diskusi' : 'Tidak ada diskusi' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="category-items">
                        @forelse($pertemuan->forumDiskusi as $forum)
                            <div class="item-card">
                                <div class="item-info">
                                    <h4>{{ $forum->judul }}</h4>
                                    <div class="item-meta">
                                        <span><i class="fas fa-user me-1"></i>{{ $forum->user->name ?? 'Anonim' }}</span>
                                        <span><i class="fas fa-comment me-1"></i>{{ $forum->replies_count ?? 0 }} balasan</span>
                                        <span><i class="far fa-clock me-1"></i>{{ $forum->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <a href="{{ route('siswa.lms.mapel.forum.show', [$mataPelajaran->id, $forum->id]) }}" class="btn-small">Buka</a>
                                </div>
                            </div>
                        @empty
                            <div class="no-content">Tidak ada diskusi</div>
                        @endforelse
                    </div>
                </div>

                <!-- Kuis/Ujian -->
                @php
                    $currentQuizzes = $pertemuan->ujian;
                    // Add carried forward quizzes for the latest pertemuan
                    $carriedQuizzes = ($index === 0) ? $incompleteQuizzes : collect();
                @endphp
                <div class="category-bar">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-left">
                            <i class="fas fa-chevron-right toggle-category {{ $currentQuizzes->count() > 0 || $carriedQuizzes->count() > 0 ? 'open' : '' }}"></i>
                            <div class="category-icon icon-kuis"><i class="fas fa-question-circle"></i></div>
                            <div class="category-info">
                                <div class="category-name">
                                    Kuis
                                    @if($currentQuizzes->count() + $carriedQuizzes->count() > 0)
                                        <span class="badge-count">{{ $currentQuizzes->count() + $carriedQuizzes->count() }}</span>
                                    @endif
                                    @if($carriedQuizzes->count() > 0)
                                        <span class="badge-count badge-warning">{{ $carriedQuizzes->count() }} dari pekan lalu</span>
                                    @endif
                                </div>
                                <div class="category-desc">{{ $currentQuizzes->count() + $carriedQuizzes->count() > 0 ? 'Klik untuk melihat kuis' : 'Tidak ada kuis' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="category-items {{ $currentQuizzes->count() > 0 || $carriedQuizzes->count() > 0 ? 'open' : '' }}">
                        @foreach($carriedQuizzes as $carried)
                            <div class="item-card from-previous">
                                <div class="item-info">
                                    <h4>
                                        <span class="previous-tag">PEKAN {{ $carried['from_pekan'] }}</span>
                                        {{ $carried['ujian']->judul_ujian }}
                                    </h4>
                                    <div class="item-meta">
                                        <span><i class="far fa-calendar me-1"></i>Deadline: {{ $carried['ujian']->tanggal_selesai->translatedFormat('d M Y') }}</span>
                                        @if($carried['ujian']->tanggal_selesai->isPast())
                                            <span style="color: #c62828; font-weight: 600;"><i class="fas fa-exclamation-triangle me-1"></i>TERLAMBAT</span>
                                        @endif
                                        <span><i class="fas fa-clock me-1"></i>{{ $carried['ujian']->durasi_menit }} menit</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <span class="status-badge status-belum">Belum Dikerjakan</span>
                                    <a href="{{ route('siswa.lms.mapel.ujian.show', [$mataPelajaran->id, $carried['ujian']->id]) }}" class="btn-small btn-primary-sm">Kerjakan</a>
                                </div>
                            </div>
                        @endforeach
                        @forelse($currentQuizzes as $ujian)
                            <div class="item-card">
                                <div class="item-info">
                                    <h4>{{ $ujian->judul_ujian }}</h4>
                                    <div class="item-meta">
                                        <span><i class="far fa-calendar me-1"></i>Deadline: {{ $ujian->tanggal_selesai->translatedFormat('d M Y') }}</span>
                                        @if($ujian->tanggal_selesai->isFuture())
                                            <span><i class="fas fa-clock me-1"></i>{{ $ujian->tanggal_selesai->diffForHumans() }}</span>
                                        @endif
                                        <span><i class="fas fa-stopwatch me-1"></i>{{ $ujian->durasi_menit }} menit</span>
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <span class="status-badge status-belum">Belum Dikerjakan</span>
                                    <a href="{{ route('siswa.lms.mapel.ujian.show', [$mataPelajaran->id, $ujian->id]) }}" class="btn-small btn-primary-sm">Kerjakan</a>
                                </div>
                            </div>
                        @empty
                            @if($carriedQuizzes->count() === 0)
                                <div class="no-content">Tidak ada kuis</div>
                            @endif
                        @endforelse
                    </div>
                </div>

                <!-- Tugas -->
                @php
                    $currentTasks = $pertemuan->tugas;
                    $carriedTasks = ($index === 0) ? $incompleteTasks : collect();
                @endphp
                <div class="category-bar">
                    <div class="category-header" onclick="toggleCategory(this)">
                        <div class="category-left">
                            <i class="fas fa-chevron-right toggle-category {{ $currentTasks->count() > 0 || $carriedTasks->count() > 0 ? 'open' : '' }}"></i>
                            <div class="category-icon icon-tugas"><i class="fas fa-tasks"></i></div>
                            <div class="category-info">
                                <div class="category-name">
                                    Tugas
                                    @if($currentTasks->count() + $carriedTasks->count() > 0)
                                        <span class="badge-count">{{ $currentTasks->count() + $carriedTasks->count() }}</span>
                                    @endif
                                    @if($carriedTasks->count() > 0)
                                        <span class="badge-count badge-warning">{{ $carriedTasks->count() }} dari pekan lalu</span>
                                    @endif
                                </div>
                                <div class="category-desc">{{ $currentTasks->count() + $carriedTasks->count() > 0 ? 'Klik untuk melihat tugas' : 'Tidak ada tugas' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="category-items {{ $currentTasks->count() > 0 || $carriedTasks->count() > 0 ? 'open' : '' }}">
                        @foreach($carriedTasks as $carried)
                            <div class="item-card from-previous">
                                <div class="item-info">
                                    <h4>
                                        <span class="previous-tag">PEKAN {{ $carried['from_pekan'] }}</span>
                                        {{ $carried['tugas']->judul_tugas }}
                                    </h4>
                                    <div class="item-meta">
                                        <span><i class="far fa-calendar me-1"></i>Deadline: {{ $carried['tugas']->tanggal_deadline->translatedFormat('d M Y') }}</span>
                                        @if($carried['tugas']->tanggal_deadline->isPast())
                                            <span style="color: #c62828; font-weight: 600;"><i class="fas fa-exclamation-triangle me-1"></i>TERLAMBAT {{ $carried['tugas']->tanggal_deadline->diffInDays(now()) }} hari</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <span class="status-badge status-belum">Belum Dikerjakan</span>
                                    <a href="{{ route('siswa.lms.mapel.tugas.show', [$mataPelajaran->id, $carried['tugas']->id]) }}" class="btn-small btn-primary-sm">Kerjakan</a>
                                </div>
                            </div>
                        @endforeach
                        @forelse($currentTasks as $tugas)
                            <div class="item-card">
                                <div class="item-info">
                                    <h4>{{ $tugas->judul_tugas }}</h4>
                                    <div class="item-meta">
                                        <span><i class="far fa-calendar me-1"></i>Deadline: {{ $tugas->tanggal_deadline->translatedFormat('d M Y') }}</span>
                                        @if($tugas->tanggal_deadline->isFuture())
                                            <span><i class="fas fa-clock me-1"></i>{{ $tugas->tanggal_deadline->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="item-actions">
                                    <span class="status-badge status-belum">Belum Dikerjakan</span>
                                    <a href="{{ route('siswa.lms.mapel.tugas.show', [$mataPelajaran->id, $tugas->id]) }}" class="btn-small btn-primary-sm">Kerjakan</a>
                                </div>
                            </div>
                        @empty
                            @if($carriedTasks->count() === 0)
                                <div class="no-content">Tidak ada tugas</div>
                            @endif
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    @empty
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
            <h5 class="text-muted">Belum ada pertemuan yang dijadwalkan</h5>
            <p class="text-muted small">Guru belum membuat pertemuan untuk mata pelajaran ini.</p>
        </div>
    @endforelse

    <script>
        function togglePertemuan(element) {
            const content = element.nextElementSibling;
            const icon = element.querySelector('.toggle-icon');
            
            // Close other pertemuans
            document.querySelectorAll('.pertemuan-content').forEach(item => {
                if (item !== content) {
                    item.classList.remove('open');
                    item.previousElementSibling.classList.remove('active');
                    item.previousElementSibling.querySelector('.toggle-icon').classList.remove('open');
                }
            });

            content.classList.toggle('open');
            element.classList.toggle('active');
            icon.classList.toggle('open');
        }

        function toggleCategory(element) {
            const toggleIcon = element.querySelector('.toggle-category');
            const items = element.nextElementSibling;
            
            items.classList.toggle('open');
            if (toggleIcon) {
                toggleIcon.classList.toggle('open');
            }
        }
    </script>
@endsection