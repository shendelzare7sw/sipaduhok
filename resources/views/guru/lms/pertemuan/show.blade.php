@extends('layouts.lms-guru')

@section('title', 'Detail Pertemuan')
@section('page-title', $pertemuan->judul)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-9">
            {{-- Navigation --}}
            <a href="{{ route('guru.lms.pertemuan.index', [$kelas->id, $mapel->id]) }}"
                class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Feed
            </a>

            {{-- Main Post Card --}}
            <div class="card-custom mb-4 border-start border-4 border-primary shadow-sm">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <span class="badge bg-primary mb-2">Pekan Ke-{{ $pertemuan->pekan }}</span>
                            <h3 class="fw-bold mb-1">{{ $pertemuan->judul }}</h3>
                            <p class="text-muted"><i class="far fa-calendar me-1"></i>
                                {{ $pertemuan->tanggal->translatedFormat('l, d F Y') }}</p>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form
                                        action="{{ route('guru.lms.pertemuan.destroy', [$kelas->id, $mapel->id, $pertemuan->id]) }}"
                                        method="POST" onsubmit="return confirm('Hapus pertemuan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger" type="submit">
                                            <i class="fas fa-trash me-2"></i>Hapus Pertemuan
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    @if($pertemuan->deskripsi)
                        <div class="mt-3 text-secondary" style="white-space: pre-wrap;">{{ $pertemuan->deskripsi }}</div>
                    @endif

                    @if($pertemuan->zoom_link)
                        <div class="mt-4 p-3 bg-primary bg-opacity-10 border border-primary rounded d-flex align-items-center">
                            <i class="fas fa-video fa-2x text-primary me-3"></i>
                            <div>
                                <h6 class="fw-bold text-primary mb-1">Pertemuan Virtual (Zoom/GMeet)</h6>
                                <a href="{{ $pertemuan->zoom_link }}" target="_blank"
                                    class="text-primary text-decoration-underline">
                                    {{ $pertemuan->zoom_link }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Modul Ajar Section --}}
            <h5 class="fw-bold mb-3 text-secondary border-bottom pb-2"><i class="fas fa-book me-2"></i>Modul Ajar</h5>

            <div class="row g-3 mb-4">
                @forelse($pertemuan->materi->where('kategori', 'modul_ajar') as $modul)
                    <div class="col-md-6">
                        <div class="card-custom h-100 border-start border-4 border-purple hover-shadow">
                            <div class="p-3 d-flex justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-purple bg-opacity-10 text-purple rounded p-3 me-3">
                                        <i class="fas fa-book-open fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $modul->judul_materi }}</h6>
                                        <small class="text-muted text-uppercase"
                                            style="font-size: 10px;">{{ $modul->tipe_file }}</small>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ asset('storage/' . $modul->file_materi) }}"
                                                target="_blank"><i class="fas fa-download me-2"></i>Download</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('guru.lms.materi.edit', [$kelas->id, $mapel->id, $modul->id]) }}"><i
                                                    class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>
                                        <li>
                                            <form
                                                action="{{ route('guru.lms.materi.destroy', [$kelas->id, $mapel->id, $modul->id]) }}"
                                                method="POST" onsubmit="return confirm('Hapus modul ini?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit"><i
                                                        class="fas fa-trash me-2"></i>Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-light border-dashed text-center">
                            <p class="text-muted mb-2">Belum ada Modul Ajar yang diunggah.</p>
                            <a href="{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id, 'pertemuan_id' => $pertemuan->id, 'kategori' => 'modul_ajar']) }}"
                                class="btn btn-sm btn-outline-purple">
                                <i class="fas fa-plus me-1"></i> Upload Modul Ajar
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Other Activities --}}
            <h5 class="fw-bold mb-3 text-secondary border-bottom pb-2"><i class="fas fa-tasks me-2"></i>Aktivitas
                Pembelajaran</h5>

            <div class="d-flex flex-column gap-3">
                {{-- Combine all other items and sort/display --}}
                @php
                    $materiLain = $pertemuan->materi->where('kategori', '!=', 'modul_ajar')->map(function ($i) {
                        $i->type = 'materi';
                        return $i; });
                    $tugas = $pertemuan->tugas->map(function ($i) {
                        $i->type = 'tugas';
                        return $i; });
                    $ujian = $pertemuan->ujian->map(function ($i) {
                        $i->type = 'ujian';
                        return $i; });
                    $forum = $pertemuan->forumDiskusi->map(function ($i) {
                        $i->type = 'forum';
                        return $i; });

                    $allItems = $materiLain->concat($tugas)->concat($ujian)->concat($forum)->sortByDesc('created_at');
                @endphp

                @forelse($allItems as $item)
                    @if($item->type == 'materi')
                        <div class="card-custom border-start border-4 border-success hover-shadow">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 text-success rounded p-3 me-3">
                                        <i class="fas fa-file-alt fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->judul_materi }}</h6>
                                        <span class="badge bg-success bg-opacity-10 text-success">Materi Pendukung</span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="{{ asset('storage/' . $item->file_materi) }}"
                                                target="_blank"><i class="fas fa-eye me-2"></i>Lihat</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('guru.lms.materi.edit', [$kelas->id, $mapel->id, $item->id]) }}"><i
                                                    class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li>
                                            <form
                                                action="{{ route('guru.lms.materi.destroy', [$kelas->id, $mapel->id, $item->id]) }}"
                                                method="POST" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit"><i
                                                        class="fas fa-trash me-2"></i>Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @elseif($item->type == 'tugas')
                        <div class="card-custom border-start border-4 border-warning hover-shadow">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded p-3 me-3">
                                        <i class="fas fa-clipboard-list fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->judul_tugas }}</h6>
                                        <span class="badge bg-warning bg-opacity-10 text-warning mb-1">Tugas</span>
                                        <small class="text-muted d-block">Deadline:
                                            {{ $item->tanggal_deadline->format('d M Y, H:i') }}</small>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-eye me-2"></i>Lihat & Nilai</a></li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('guru.lms.tugas.edit', [$kelas->id, $mapel->id, $item->id]) }}"><i
                                                    class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li>
                                            <form
                                                action="{{ route('guru.lms.tugas.destroy', [$kelas->id, $mapel->id, $item->id]) }}"
                                                method="POST" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit"><i
                                                        class="fas fa-trash me-2"></i>Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @elseif($item->type == 'ujian')
                        <div class="card-custom border-start border-4 border-danger hover-shadow">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded p-3 me-3">
                                        <i class="fas fa-stopwatch fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->judul_ujian }}</h6>
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Ujian</span>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-link text-muted p-0" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li><a class="dropdown-item" href="#"><i class="fas fa-chart-bar me-2"></i>Monitoring</a>
                                        </li>
                                        <li><a class="dropdown-item"
                                                href="{{ route('guru.lms.ujian.edit', [$kelas->id, $mapel->id, $item->id]) }}"><i
                                                    class="fas fa-edit me-2"></i>Edit</a></li>
                                        <li>
                                            <form
                                                action="{{ route('guru.lms.ujian.destroy', [$kelas->id, $mapel->id, $item->id]) }}"
                                                method="POST" onsubmit="return confirm('Hapus?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger" type="submit"><i
                                                        class="fas fa-trash me-2"></i>Hapus</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @elseif($item->type == 'forum')
                        <div class="card-custom border-start border-4 border-info hover-shadow">
                            <div class="p-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-info bg-opacity-10 text-info rounded p-3 me-3">
                                        <i class="fas fa-comments fa-lg"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">{{ $item->judul }}</h6>
                                        <span class="badge bg-info bg-opacity-10 text-info">Forum Diskusi</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('guru.lms.forum.show', [$kelas->id, $mapel->id, $item->id]) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-4 bg-light rounded border-dashed">
                        <i class="far fa-folder-open mb-2 text-muted fa-2x"></i>
                        <p class="text-muted mb-0">Belum ada aktivitas pembelajaran lainnya.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Sidebar Action --}}
        <div class="col-md-3">
            <div class="card-custom shadow-sm mb-4">
                <div class="card-header-custom fw-bold bg-primary text-white">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Konten
                </div>
                <div class="p-3 d-grid gap-2">
                    <button class="btn btn-outline-purple text-start p-2"
                        onclick="window.location.href='{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id, 'pertemuan_id' => $pertemuan->id, 'kategori' => 'modul_ajar']) }}'">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-book fa-lg me-3"></i>
                            <div>
                                <div class="fw-bold">Upload Modul</div>
                                <small class="text-muted" style="font-size: 10px;">Bahan ajar utama (PDF, PPT)</small>
                            </div>
                        </div>
                    </button>

                    <button class="btn btn-outline-success text-start p-2"
                        onclick="window.location.href='{{ route('guru.lms.materi.create', [$kelas->id, $mapel->id, 'pertemuan_id' => $pertemuan->id, 'kategori' => 'materi']) }}'">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-file-alt fa-lg me-3"></i>
                            <div>
                                <div class="fw-bold">Materi Pendukung</div>
                                <small class="text-muted" style="font-size: 10px;">Artikel, Video, Link</small>
                            </div>
                        </div>
                    </button>

                    <button class="btn btn-outline-warning text-start p-2"
                        onclick="window.location.href='{{ route('guru.lms.tugas.create', [$kelas->id, $mapel->id, 'pertemuan_id' => $pertemuan->id]) }}'">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tasks fa-lg me-3"></i>
                            <div>
                                <div class="fw-bold">Buat Tugas</div>
                                <small class="text-muted" style="font-size: 10px;">Penugasan siswa</small>
                            </div>
                        </div>
                    </button>

                    <button class="btn btn-outline-danger text-start p-2"
                        onclick="window.location.href='{{ route('guru.lms.ujian.create', [$kelas->id, $mapel->id, 'pertemuan_id' => $pertemuan->id]) }}'">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-stopwatch fa-lg me-3"></i>
                            <div>
                                <div class="fw-bold">Buat Ujian</div>
                                <small class="text-muted" style="font-size: 10px;">Kuis atau Ulangan</small>
                            </div>
                        </div>
                    </button>

                    <button class="btn btn-outline-info text-start p-2"
                        onclick="window.location.href='{{ route('guru.lms.forum.create', [$kelas->id, $mapel->id, 'pertemuan_id' => $pertemuan->id]) }}'">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-comments fa-lg me-3"></i>
                            <div>
                                <div class="fw-bold">Forum Diskusi</div>
                                <small class="text-muted" style="font-size: 10px;">Ruang tanya jawab</small>
                            </div>
                        </div>
                    </button>
                </div>
            </div>

            <div class="alert alert-info small">
                <i class="fas fa-info-circle me-1"></i>
                Konten yang ditambahkan akan muncul secara otomatis di <strong>Pekan {{ $pertemuan->pekan }}</strong> pada
                dashboard siswa.
            </div>
        </div>
    </div>

    @push('scripts')
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

            .btn-outline-purple {
                color: #a855f7;
                border-color: #a855f7;
            }

            .btn-outline-purple:hover {
                background-color: #a855f7;
                color: white;
            }

            .hover-shadow:hover {
                transform: translateY(-2px);
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
                transition: all 0.3s;
            }

            .border-dashed {
                border-style: dashed !important;
            }
        </style>
    @endpush
@endsection