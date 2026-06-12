@extends('layouts.sneat')

@section('title', 'Siswa Kelas ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Siswa Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas . ' - ' . ($kelas->tahunAjaran->nama_tahun_ajaran ?? ''))

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/manajemen-siswa/per-kelas.css'])
@endsection

@section('content')
<div class="ms-per-kelas-page">
    <div class="ms-breadcrumb">
        <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('waka.manajemen-siswa.index') }}">Manajemen Siswa</a>
        <span>/</span>
        <span class="current">Kelas {{ $kelas->nama_kelas }}</span>
    </div>

    <div class="info-banner">
        <div>
            <h2>Kelas {{ $kelas->nama_kelas }}</h2>
            <div class="info-banner-meta">
                <div class="info-banner-item"><i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}</div>
                <div class="info-banner-item"><i class="fas fa-layer-group"></i> {{ $kelas->jenjang }}</div>
                <div class="info-banner-item"><i class="fas fa-calendar"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                @if($kelas->waliKelas)
                    <div class="info-banner-item"><i class="fas fa-user-tie"></i> {{ $kelas->waliKelas->nama_lengkap }}</div>
                @endif
            </div>
        </div>
        <div class="info-banner-stats">
            <div class="info-banner-stat">
                <div class="info-banner-stat-value">{{ $stats['totalSiswa'] }}</div>
                <div class="info-banner-stat-label">Total Siswa</div>
            </div>
            <div class="info-banner-stat">
                <div class="info-banner-stat-value">{{ $stats['siswaLaki'] }}</div>
                <div class="info-banner-stat-label">Laki-laki</div>
            </div>
            <div class="info-banner-stat">
                <div class="info-banner-stat-value">{{ $stats['siswaPerempuan'] }}</div>
                <div class="info-banner-stat-label">Perempuan</div>
            </div>
            <div class="info-banner-stat">
                <div class="info-banner-stat-value">{{ $stats['sisaKuota'] }}</div>
                <div class="info-banner-stat-label">Sisa Kuota</div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle"></i> Tambah Siswa</h5>
            </div>
            <div class="card-body">
                @if($stats['sisaKuota'] <= 0)
                    <div class="kuota-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Kuota kelas sudah penuh! Tidak bisa menambah siswa.
                    </div>
                @endif

                @if($availableSiswa->count() > 0 && $stats['sisaKuota'] > 0)
                    <form action="{{ route('waka.manajemen-siswa.add-to-kelas', $kelas) }}" method="POST">
                        @csrf
                        <div class="form-row">
                            <select name="siswa_id" required>
                                <option value="">-- Pilih Siswa --</option>
                                @foreach($availableSiswa as $s)
                                    <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->nisn }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah
                            </button>
                        </div>
                    </form>
                    <small class="helper-text">Menampilkan siswa tanpa kelas dari cabang {{ $kelas->cabang->nama_cabang ?? '' }}</small>
                @elseif($stats['sisaKuota'] > 0)
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Semua siswa di cabang ini sudah memiliki kelas</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Daftar Siswa ({{ $stats['totalSiswa'] }})</h5>
            </div>
            <div class="card-body is-table">
                @if($siswaList->count() > 0)
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    <th>JK</th>
                                    <th class="col-actions"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaList as $index => $siswa)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="siswa-info">
                                                <div class="siswa-avatar {{ $siswa->jenis_kelamin == 'P' ? 'female' : '' }}">
                                                    {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                                                </div>
                                                <span>{{ $siswa->nama_lengkap }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-l' : 'badge-p' }}">
                                                {{ $siswa->jenis_kelamin }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('waka.manajemen-siswa.remove-from-kelas', $kelas) }}"
                                                method="POST" id="deleteForm{{ $siswa->id }}">
                                                @csrf
                                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                                <button type="button"
                                                        class="btn btn-danger btn-sm"
                                                        title="Keluarkan"
                                                        data-remove-siswa
                                                        data-form-id="{{ $siswa->id }}"
                                                        data-siswa-name="{{ $siswa->nama_lengkap }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Belum ada siswa di kelas ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="footer-actions">
        <a href="{{ route('waka.manajemen-siswa.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Siswa
        </a>
        <a href="{{ route('waka.manajemen-siswa.print', ['kelas_id' => $kelas->id]) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-print"></i> Cetak Daftar Kelas
        </a>
    </div>
</div>

<div class="modal fade remove-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content remove-modal-content">
            <button type="button" class="remove-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body remove-modal-body">
                <div class="remove-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <h4 class="remove-modal-title">Keluarkan Siswa?</h4>

                <p class="remove-modal-text">
                    Apakah Anda yakin ingin mengeluarkan <br>
                    <strong id="siswaName"></strong><br>
                    dari <span class="fw-medium">Kelas {{ $kelas->nama_kelas }}</span>?
                </p>

                <div class="remove-info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        Siswa akan dikeluarkan dari kelas ini, namun data siswa tetap tersimpan dan dapat ditambahkan kembali kapan saja.
                    </p>
                </div>

                <div class="remove-modal-actions">
                    <button type="button" class="btn btn-outline" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                        <i class="fas fa-user-minus me-2"></i> Ya, Keluarkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/waka/manajemen-siswa/per-kelas.js'])
@endsection
