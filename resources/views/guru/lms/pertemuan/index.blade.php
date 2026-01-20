@extends('layouts.lms-guru')

@section('title', 'Feed Pembelajaran')
@section('page-title', 'Feed Pembelajaran')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row">
        {{-- Main Feed --}}
        <div class="col-md-8">
            {{-- Action and Filter Bar --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="dropdown">
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-filter me-2"></i>
                        {{ $currentPekan == 'all' ? 'Semua Pekan' : 'Pekan Ke-' . $currentPekan }}
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item"
                                href="{{ route('guru.lms.pertemuan.index', [$kelas->id, $mapel->id, 'pekan' => 'all']) }}">Tampilkan
                                Semua</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        @foreach($availableWeeks as $week)
                            <li><a class="dropdown-item"
                                    href="{{ route('guru.lms.pertemuan.index', [$kelas->id, $mapel->id, 'pekan' => $week]) }}">Pekan
                                    Ke-{{ $week }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPertemuanModal">
                    <i class="fas fa-plus-circle me-2"></i>Buat Pertemuan Baru
                </button>
            </div>

            {{-- Feed Loop --}}
            @forelse($pertemuanList as $pekan => $pertemuans)
                <div class="mb-5">
                    <h6 class="fw-bold text-secondary text-uppercase mb-3 ps-2 border-start border-4 border-primary">
                        Pekan Ke-{{ $pekan }}
                    </h6>

                    @foreach($pertemuans as $p)
                        <div class="card-custom mb-3 border-0 shadow-sm hover-shadow transition-all">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">
                                            <a href="{{ route('guru.lms.pertemuan.show', [$kelas->id, $mapel->id, $p->id]) }}"
                                                class="text-decoration-none text-dark">
                                                {{ $p->judul }}
                                            </a>
                                        </h5>
                                        <div class="text-muted small">
                                            <i class="far fa-calendar-alt me-1"></i> {{ $p->tanggal->translatedFormat('l, d F Y') }}
                                            @if($p->zoom_link)
                                                <span class="mx-2">&bull;</span>
                                                <a href="{{ $p->zoom_link }}" target="_blank" class="text-primary text-decoration-none">
                                                    <i class="fas fa-video me-1"></i> Zoom Meeting
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dropdown">
                                        <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a class="dropdown-item"
                                                    href="{{ route('guru.lms.pertemuan.show', [$kelas->id, $mapel->id, $p->id]) }}">
                                                    <i class="fas fa-eye me-2"></i>Kelola Pertemuan
                                                </a>
                                            </li>
                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>
                                            <li>
                                                <form
                                                    action="{{ route('guru.lms.pertemuan.destroy', [$kelas->id, $mapel->id, $p->id]) }}"
                                                    method="POST" onsubmit="return confirm('Hapus pertemuan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item text-danger" type="submit">
                                                        <i class="fas fa-trash me-2"></i>Hapus
                                                    </button>
                                                </form>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                @if($p->deskripsi)
                                    <p class="text-secondary mb-3">{{ Str::limit($p->deskripsi, 150) }}</p>
                                @endif

                                {{-- Resource Badges --}}
                                <div class="d-flex gap-2 flex-wrap">
                                    @foreach($p->materi->where('kategori', 'modul_ajar') as $m)
                                        <span class="badge bg-purple bg-opacity-10 text-purple border border-purple">
                                            <i class="fas fa-book me-1"></i> Modul
                                        </span>
                                    @endforeach
                                    @php
                                        $materiCount = $p->materi->where('kategori', '!=', 'modul_ajar')->count();
                                        $tugasCount = $p->tugas->count();
                                        $ujianCount = $p->ujian->count();
                                        $forumCount = $p->forumDiskusi->count();
                                    @endphp

                                    @if($materiCount > 0)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success">
                                            <i class="fas fa-file-alt me-1"></i> {{ $materiCount }} Materi
                                        </span>
                                    @endif
                                    @if($tugasCount > 0)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning">
                                            <i class="fas fa-tasks me-1"></i> {{ $tugasCount }} Tugas
                                        </span>
                                    @endif
                                    @if($ujianCount > 0)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger">
                                            <i class="fas fa-stopwatch me-1"></i> {{ $ujianCount }} Ujian
                                        </span>
                                    @endif
                                    @if($forumCount > 0)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info">
                                            <i class="fas fa-comments me-1"></i> {{ $forumCount }} Diskusi
                                        </span>
                                    @endif

                                    @if($materiCount + $tugasCount + $ujianCount + $forumCount == 0 && $p->materi->where('kategori', 'modul_ajar')->isEmpty())
                                        <small class="text-muted fst-italic">Belum ada resource.</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <div class="text-center py-5">
                    <img src="{{ asset('img/empty-state.svg') }}" alt="Empty"
                        style="width: 150px; opacity: 0.5; margin-bottom: 24px;">
                    <h5 class="text-muted">Belum ada pertemuan di pekan ini.</h5>
                    <button class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#createPertemuanModal">
                        Buat Pertemuan Sekarang
                    </button>
                </div>
            @endforelse
        </div>

        {{-- Sidebar (Calendar & Info) --}}
        <div class="col-md-4">
            <div class="card-custom mb-4">
                <div class="card-header-custom">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-calendar me-2"></i>Kalender</h6>
                </div>
                <div class="p-3">
                    <div id='calendar'></div>
                </div>
            </div>

            <div class="card-custom bg-light border-0">
                <div class="p-4">
                    <h6 class="fw-bold mb-2">Tips Pengelolaan</h6>
                    <ul class="d-flex flex-column gap-2 ps-3 mb-0 small text-secondary">
                        <li>Gunakan <strong>Pekan</strong> untuk mengelompokkan materi yang relevan.</li>
                        <li>Upload <strong>Modul Ajar</strong> untuk referensi utama siswa.</li>
                        <li>Sematkan <strong>Link Zoom</strong> jika ada kelas virtual.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    {{-- Create Modal --}}
    <div class="modal fade" id="createPertemuanModal" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('guru.lms.pertemuan.store', [$kelas->id, $mapel->id]) }}" method="POST">
                @csrf
                <div class="modal-content">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold">Buat Pertemuan Baru</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Judul Pertemuan</label>
                                <input type="text" name="judul" class="form-control"
                                    placeholder="Contoh: Pengenalan Algoritma" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Pekan Ke-</label>
                                <input type="number" name="pekan" class="form-control"
                                    value="{{ $currentPekan == 'all' ? 1 : $currentPekan }}" min="1" max="20" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Link Zoom (Opsional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-video"></i></span>
                                <input type="url" name="zoom_link" class="form-control" placeholder="https://zoom.us/j/...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi Singkat</label>
                            <textarea name="deskripsi" class="form-control" rows="3"
                                placeholder="Jelaskan secara singkat topik yang dibahas..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Posting Pertemuan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var calendarEl = document.getElementById('calendar');
                var calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    themeSystem: 'bootstrap5',
                    headerToolbar: {
                        left: 'prev,next',
                        center: 'title',
                        right: ''
                    },
                    height: 300,
                    events: @json($events),
                    dateClick: function (info) {
                        var modal = new bootstrap.Modal(document.getElementById('createPertemuanModal'));
                        document.querySelector('input[name="tanggal"]').value = info.dateStr;
                        modal.show();
                    }
                });
                calendar.render();
            });
        </script>
        <style>
            .bg-purple {
                background-color: #a855f7;
            }

            .text-purple {
                color: #a855f7;
            }

            .border-purple {
                border-color: #a855f7 !important;
            }

            .hover-shadow:hover {
                transform: translateY(-2px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            }

            .transition-all {
                transition: all 0.3s ease;
            }
        </style>
    @endpush
@endsection