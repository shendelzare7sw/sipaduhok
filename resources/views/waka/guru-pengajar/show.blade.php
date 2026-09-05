@extends('layouts.app')

@section('title', 'Detail Guru Pengajar - ' . $guruPengajar->nama_lengkap)

@section('page-title', 'Kelola Penugasan Guru')
@section('page-subtitle', $guruPengajar->nama_lengkap)


@section('styles')
    @vite('resources/css/waka/guru-pengajar/show.css')
@endsection

@section('content')
    <div class="page-shell">
        <div class="breadcrumb">
            <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
            <span>/</span>
            <a href="{{ route('waka.guru-pengajar.index') }}">Data Guru Pengajar</a>
            <span>/</span>
            <span class="current">{{ $guruPengajar->nama_lengkap }}</span>
        </div>

        <div class="header-card">
            <div class="header-content">
                <div class="header-top">
                    <div class="header-info">
                        <div class="header-avatar">{{ strtoupper(substr($guruPengajar->nama_lengkap, 0, 1)) }}</div>
                        <div class="header-text">
                            <h1>{{ $guruPengajar->nama_lengkap }}</h1>
                            <div class="header-meta">
                                <span class="header-badge">
                                    <i class="fas fa-id-badge"></i> {{ $guruPengajar->nip ?? 'NIP: -' }}
                                </span>
                                <span class="header-badge">
                                    <i class="fas fa-user-tag"></i>
                                    {{ ucfirst(str_replace('_', ' ', $guruPengajar->user->role ?? '-')) }}
                                </span>
                                <span class="header-badge">
                                    <i class="fas fa-circle status-dot {{ $guruPengajar->user->is_active ? 'status-dot-active' : 'status-dot-inactive' }}"></i>
                                    {{ $guruPengajar->user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('waka.guru-pengajar.index') }}" class="btn btn-white-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>

                <div class="header-stats">
                    <div class="header-stat">
                        <div class="header-stat-value">{{ $stats['totalKelas'] }}</div>
                        <div class="header-stat-label">Kelas Diajar</div>
                    </div>
                    <div class="header-stat">
                        <div class="header-stat-value">{{ $stats['totalMapel'] }}</div>
                        <div class="header-stat-label">Mata Pelajaran</div>
                    </div>
                    <div class="header-stat">
                        <div class="header-stat-value">{{ $stats['totalPenugasan'] }}</div>
                        <div class="header-stat-label">Total Penugasan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-2">
            {{-- Info Guru --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Informasi Guru</h5>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value">{{ $guruPengajar->nama_lengkap }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIP</span>
                            <span class="info-value monospace">{{ $guruPengajar->nip ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Email</span>
                            <span class="info-value">{{ $guruPengajar->user->email ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telepon</span>
                            <span class="info-value">{{ $guruPengajar->telepon ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Pendidikan Terakhir</span>
                            <span class="info-value">{{ $guruPengajar->pendidikan_terakhir ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Info Box: Penugasan Otomatis dari Jadwal --}}
            <div class="card info-card">
                <div class="card-body compact-card-body">
                    <div class="info-row">
                        <i class="fas fa-info-circle info-icon"></i>
                        <div>
                            <strong class="info-title">Penugasan Otomatis dari Jadwal Pelajaran</strong>
                            <p class="info-text">
                                Penugasan guru ke kelas dan mata pelajaran dikelola otomatis dari Jadwal Pelajaran.
                                Untuk menambah atau mengubah penugasan, buat atau edit jadwal di menu Jadwal Pelajaran.
                            </p>
                            <a href="{{ route('waka.jadwal-pelajaran.index') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-calendar-alt me-1"></i> Buka Jadwal Pelajaran
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Penugasan --}}
        <div class="card">
            <div class="card-header card-header-flex">
                <h5><i class="fas fa-tasks"></i> Daftar Penugasan</h5>
                <form action="" method="GET" class="filter-inline-form">
                    <select name="tahun_ajaran_id" class="filter-select-inline" data-auto-submit>
                        <option value="">Semua Tahun Ajaran</option>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                @if($guruPengajar->guruKelas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile">
                            <thead>
                                <tr>
                                    <th class="mobile-card-hide">No</th>
                                    <th class="mobile-card-head">Kelas</th>
                                    <th data-label="Jenjang">Jenjang</th>
                                    <th data-label="Mata Pelajaran">Mata Pelajaran</th>
                                    <th data-label="Cabang">Cabang</th>
                                    <th data-label="Tahun Ajaran">Tahun Ajaran</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($guruPengajar->guruKelas as $index => $assignment)
                                    <tr>
                                        <td class="mobile-card-hide">{{ $index + 1 }}</td>
                                        <td class="mobile-card-head"><strong>{{ $assignment->kelas->nama_kelas }}</strong></td>
                                        <td data-label="Jenjang"><span class="badge badge-blue">{{ $assignment->kelas->jenjang }}</span></td>
                                        <td data-label="Mata Pelajaran"><span class="badge badge-teal">{{ $assignment->mataPelajaran->nama_mapel }}</span></td>
                                        <td data-label="Cabang">{{ $assignment->kelas->cabang->nama_cabang ?? '-' }}</td>
                                        <td data-label="Tahun Ajaran">{{ $assignment->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Jadwal Terkait --}}
                    @if($jadwalList->count() > 0)
                        <h6 class="mt-4 mb-2"><i class="fas fa-calendar-alt"></i> Jadwal Mengajar</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-card-mobile">
                                <thead>
                                    <tr>
                                        <th class="mobile-card-head">Hari</th>
                                        <th data-label="Jam">Jam</th>
                                        <th data-label="Mata Pelajaran">Mata Pelajaran</th>
                                        <th data-label="Kelas">Kelas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($jadwalList as $jadwal)
                                        <tr>
                                            <td class="mobile-card-head">{{ $jadwal->hari }}</td>
                                            <td data-label="Jam">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}</td>
                                            <td data-label="Mata Pelajaran">{{ $jadwal->mataPelajaran->nama_mapel ?? '-' }}</td>
                                            <td data-label="Kelas">{{ $jadwal->kelas->pluck('nama_kelas')->join(', ') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <p>Belum ada penugasan untuk guru ini</p>
                        <small class="text-muted">Buat jadwal pelajaran dengan guru ini untuk menambahkan penugasan secara otomatis.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    @vite('resources/js/waka/guru-pengajar/show.js')
@endsection
