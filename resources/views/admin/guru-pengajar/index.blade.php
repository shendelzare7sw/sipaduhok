@extends('layouts.sneat')

@section('title', 'Data Guru Pengajar')
@section('page-title', 'Data Guru Pengajar')
@section('page-subtitle')
Kelola penugasan guru pengajar {{ $currentTahunAjaran ? '- ' . $currentTahunAjaran->nama_tahun_ajaran : '' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/guru-pengajar.css') }}">
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-primary">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalGuru'] }}</div>
                <div class="stat-label">Total Guru</div>
                <div class="stat-desc">Guru aktif</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['guruWithAssignment'] }}</div>
                <div class="stat-label">Sudah Ditugaskan</div>
                <div class="stat-desc">Guru dengan penugasan</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-teal">
                <i class="fas fa-tasks"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalPenugasan'] }}</div>
                <div class="stat-label">Total Penugasan</div>
                <div class="stat-desc">Guru-Kelas-Mapel</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon stat-icon-purple">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalMataPelajaran'] }}</div>
                <div class="stat-label">Mata Pelajaran</div>
                <div class="stat-desc">Mapel aktif</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="gp-card">
        <div class="gp-card-header">
            <div>
                <h5 class="gp-card-title">
                    <i class="fas fa-user-tie title-icon-teal"></i> Daftar Guru Pengajar
                </h5>
                <div class="gp-card-subtitle">Penugasan guru otomatis dari Jadwal Pelajaran</div>
            </div>
            <div class="header-actions d-flex gap-2">
                <button type="button" class="btn btn-warning text-white d-flex align-items-center gap-1 text-white" data-bs-toggle="modal" data-bs-target="#modalSinkronkan">
                    <i class="fas fa-sync-alt"></i> <span class="d-none d-sm-inline">Sinkronkan</span>
                </button>
                <a href="{{ route('admin.guru-pengajar.print', request()->query()) }}" class="btn btn-secondary text-white d-flex align-items-center gap-1" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form action="{{ route('admin.guru-pengajar.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama, NIP, atau email..." value="{{ request('search') }}">
                </div>

                <select name="tahun_ajaran_id" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Tahun Ajaran</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <select name="status" class="form-select filter-select" data-auto-submit>
                    <option value="">Status: Aktif</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                </select>

                <button type="submit" class="btn btn-secondary btn-sm px-3 rounded-md">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.guru-pengajar.index', ['tahun_ajaran_id' => request('tahun_ajaran_id')]) }}" class="btn btn-outline-danger btn-sm px-3 rounded-md">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        @if($guruList->count() > 0)
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead>
                        <tr>
                            <th>Guru Pengajar</th>
                            <th>Telepon</th>
                            <th>Status</th>
                            <th>Penugasan (Kelas - Mapel)</th>
                            <th class="text-end" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($guruList as $guru)
                        <tr>
                            <td class="mobile-card-head" data-label="Guru">
                                <div class="guru-info">
                                    <div class="guru-avatar">{{ strtoupper(substr($guru->nama_lengkap, 0, 1)) }}</div>
                                    <div class="guru-details">
                                        <span class="guru-name">{{ $guru->nama_lengkap }}</span>
                                        <span class="guru-nip">{{ $guru->nip ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td data-label="Telepon">{{ $guru->telepon ?? '-' }}</td>
                            <td data-label="Status">
                                @if($guru->user->is_active)
                                    <span class="badge bg-label-success px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-label-warning px-2 py-1">Tidak Aktif</span>
                                @endif
                            </td>
                            <td data-label="Penugasan" class="mobile-card-full">
                                @if($guru->guruKelas->count() > 0)
                                    <div class="assignment-list">
                                        @foreach($guru->guruKelas->take(3) as $assignment)
                                            <div class="assignment-chip">
                                                <span class="kelas">{{ $assignment->kelas->nama_kelas }}</span>
                                                <span>-</span>
                                                <span class="mapel">{{ $assignment->mataPelajaran->nama_mapel }}</span>
                                            </div>
                                        @endforeach
                                        @if($guru->guruKelas->count() > 3)
                                            <span class="more-assignments">+{{ $guru->guruKelas->count() - 3 }} lainnya</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="no-assignment">Belum ada penugasan</span>
                                @endif
                            </td>
                            <td class="td-actions text-end" data-label="Aksi">
                                <div class="d-flex justify-content-end gap-1 action-btns">
                                    <a href="{{ route('admin.guru-pengajar.show', ['guruPengajar' => $guru, 'tahun_ajaran_id' => request('tahun_ajaran_id')]) }}"
                                       class="btn btn-sm btn-info text-white px-2 py-1 rounded" title="Kelola Penugasan">
                                        <i class="fas fa-cog"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($guruList->hasPages())
            <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap">
                <span class="text-muted small">Menampilkan {{ $guruList->firstItem() }} - {{ $guruList->lastItem() }} dari {{ $guruList->total() }} guru</span>
                <div class="mt-2 mt-sm-0">
                    {{ $guruList->withQueryString()->links() }}
                </div>
            </div>
            @endif
        @else
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher"></i>
                <h3>Belum Ada Data Guru Pengajar</h3>
                <p>Penugasan guru pengajar dikelola otomatis dari <strong>Jadwal Pelajaran</strong>.<br>
                Pastikan jadwal pelajaran sudah dibuat dan guru sudah ditugaskan,<br>
                lalu jalankan sinkronisasi untuk memperbarui data.</p>
                <div class="d-flex gap-2 justify-content-center mt-3 flex-wrap">
                    <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-1"></i> Buka Jadwal Pelajaran
                    </a>
                    <button type="button" class="btn btn-warning text-white" data-bs-toggle="modal" data-bs-target="#modalSinkronkan">
                        <i class="fas fa-sync-alt me-1"></i> Sinkronkan dari Jadwal
                    </button>
                </div>
            </div>
        @endif
    </div>

{{-- Modal Konfirmasi Sinkronisasi --}}
<div class="modal fade" id="modalSinkronkan" tabindex="-1" aria-labelledby="modalSinkronkanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow sync-modal-content">
            <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top">
                <h5 class="modal-title fw-bold text-dark m-0 d-flex align-items-center gap-2" id="modalSinkronkanLabel">
                    <i class="fas fa-sync-alt text-warning"></i> Konfirmasi Sinkronisasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 py-4">
                <div class="d-flex gap-3 align-items-start">
                    <div class="flex-shrink-0">
                        <div class="rounded-circle bg-label-warning d-inline-flex align-items-center justify-content-center sync-icon-wrap">
                            <i class="fas fa-exclamation-triangle text-warning fs-5"></i>
                        </div>
                    </div>
                    <div>
                        <p class="mb-1 fw-semibold">Apakah Anda yakin ingin menyinkronkan ulang?</p>
                        <p class="text-muted mb-0 small">Tindakan ini akan memperbarui semua penugasan guru berdasarkan jadwal pelajaran yang aktif. Penugasan yang tidak terdapat di jadwal akan dihapus.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer px-4 py-3 bg-light border-top">
                <button type="button" class="btn btn-secondary fw-medium rounded-md" data-bs-dismiss="modal">
                    Batal
                </button>
                <form action="{{ route('admin.guru-pengajar.rebuild') }}" method="POST" class="inline-form">
                    @csrf
                    @if(request('tahun_ajaran_id'))
                        <input type="hidden" name="tahun_ajaran_id" value="{{ request('tahun_ajaran_id') }}">
                    @endif
                    <button type="submit" class="btn btn-warning text-white fw-medium d-flex align-items-center gap-2 text-white rounded-md">
                        <i class="fas fa-sync-alt"></i> Ya, Sinkronkan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite('resources/js/admin/guru-pengajar/index.js')
@endsection
