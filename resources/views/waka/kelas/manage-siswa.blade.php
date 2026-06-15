@extends('layouts.sneat')

@section('title', 'Kelola Siswa - ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Siswa Kelas')
@section('page-subtitle', $kelas->nama_kelas . ' - ' . $kelas->tahunAjaran->nama_tahun_ajaran)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/kelas/manage-siswa.css'])
@endsection

@section('content')
<div id="manageSiswaConfig" class="d-none" data-sisa-kuota="{{ $sisaKuota }}"></div>

<div class="kelas-manage-page">
    <div class="kelas-breadcrumb">
        <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('waka.kelas.index') }}">Data Kelas</a>
        <span>/</span>
        <a href="{{ route('waka.kelas.show', $kelas) }}">{{ $kelas->nama_kelas }}</a>
        <span>/</span>
        <span class="current">Kelola Siswa</span>
    </div>

    <div class="kelas-info-banner">
        <div class="kelas-info-left">
            <div class="kelas-icon">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="kelas-title">
                <h2>{{ $kelas->nama_kelas }}</h2>
                <p><i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang }} &bull; <i class="fas fa-calendar"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</p>
            </div>
        </div>
        <div class="kelas-info-right">
            <div class="kelas-stat">
                <div class="kelas-stat-value">{{ $siswaInKelas->count() }}</div>
                <div class="kelas-stat-label">Siswa Saat Ini</div>
            </div>
            <div class="kelas-stat">
                <div class="kelas-stat-value">{{ $kelas->kuota_siswa }}</div>
                <div class="kelas-stat-label">Kuota</div>
            </div>
            <div class="kelas-stat">
                <div class="kelas-stat-value">{{ $sisaKuota }}</div>
                <div class="kelas-stat-label">Sisa Kuota</div>
            </div>
        </div>
    </div>

    @if($sisaKuota <= 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Kuota Penuh!</strong> Kelas ini sudah mencapai batas kuota maksimal. Tidak dapat menambahkan siswa baru.
        </div>
    @endif

    <div class="grid-2">
        <div class="card">
            <div class="card-header danger">
                <h5><i class="fas fa-users"></i> Siswa di Kelas Ini ({{ $siswaInKelas->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInKelas" placeholder="Cari siswa di kelas ini...">
                </div>

                @if($siswaInKelas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile" id="tableInKelas">
                            <thead>
                                <tr>
                                    <th width="50">No</th>
                                    <th>Siswa</th>
                                    <th>JK</th>
                                    <th width="80">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaInKelas as $index => $s)
                                <tr data-nama="{{ strtolower($s->nama_lengkap) }}">
                                    <td class="mobile-card-hide" data-label="No">{{ $index + 1 }}</td>
                                    <td class="mobile-card-head">
                                        <div class="user-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                        <div>
                                            <div class="user-name">{{ $s->nama_lengkap }}</div>
                                            <div class="user-nisn">{{ $s->nis }}</div>
                                        </div>
                                    </td>
                                    <td data-label="JK">
                                        <span class="badge {{ $s->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                            {{ $s->jenis_kelamin }}
                                        </span>
                                    </td>
                                    <td class="mobile-card-actions">
                                        <form action="{{ route('waka.kelas.remove-siswa', $kelas) }}" method="POST" id="deleteForm{{ $s->id }}">
                                            @csrf
                                            <input type="hidden" name="siswa_id" value="{{ $s->id }}">
                                            <button type="button"
                                                    class="btn btn-danger btn-sm"
                                                    title="Keluarkan dari kelas"
                                                    data-remove-siswa
                                                    data-form-id="{{ $s->id }}"
                                                    data-siswa-name="{{ $s->nama_lengkap }}">
                                                <i class="fas fa-user-minus"></i>
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

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-plus"></i> Siswa Tersedia ({{ $siswaAvailable->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchAvailable" placeholder="Cari siswa tersedia...">
                </div>

                @if($siswaAvailable->count() > 0 && $sisaKuota > 0)
                    <form action="{{ route('waka.kelas.add-siswa', $kelas) }}" method="POST" id="formAddSiswa">
                        @csrf
                        <div class="action-bar">
                            <div class="selected-count">
                                <span id="selectedCount">0</span> siswa dipilih
                            </div>
                            <button type="submit" class="btn btn-success" id="btnAddSiswa" disabled>
                                <i class="fas fa-plus"></i> Tambahkan ke Kelas
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-card-mobile" id="tableAvailable">
                                <thead>
                                    <tr class="select-all-row">
                                        <th width="40">
                                            <input type="checkbox" class="checkbox-custom" id="selectAll">
                                        </th>
                                        <th>Siswa</th>
                                        <th>JK</th>
                                        <th>Kelas Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaAvailable as $s)
                                    <tr data-nama="{{ strtolower($s->nama_lengkap) }}">
                                        <td class="mobile-card-checkbox">
                                            <input type="checkbox" class="checkbox-custom siswa-checkbox"
                                                   name="siswa_ids[]" value="{{ $s->id }}">
                                        </td>
                                        <td class="mobile-card-head">
                                            <div class="user-avatar blue">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                            <div>
                                                <div class="user-name">{{ $s->nama_lengkap }}</div>
                                                <div class="user-nisn">{{ $s->nis }}</div>
                                            </div>
                                        </td>
                                        <td data-label="JK">
                                            <span class="badge {{ $s->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                                {{ $s->jenis_kelamin }}
                                            </span>
                                        </td>
                                        <td data-label="Kelas">
                                            @if($s->kelas_id)
                                                <span class="badge badge-warning">{{ $s->kelas->nama_kelas ?? 'Ada kelas' }}</span>
                                            @else
                                                <span class="kelas-muted">Belum ada kelas</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @elseif($sisaKuota <= 0)
                    <div class="empty-state">
                        <i class="fas fa-ban"></i>
                        <p>Kuota kelas sudah penuh</p>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Semua siswa di cabang ini sudah terdaftar di kelas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="manage-footer-action">
        <a href="{{ route('waka.kelas.show', $kelas) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail Kelas
        </a>
    </div>
</div>

<div class="modal fade delete-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content delete-modal-content">
            <button type="button" class="delete-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body delete-modal-body">
                <div class="delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <h4 class="delete-modal-title">Keluarkan Siswa?</h4>

                <p class="delete-modal-text">
                    Apakah Anda yakin ingin mengeluarkan <br>
                    <strong id="siswaName"></strong><br>
                    dari <span class="fw-medium">Kelas {{ $kelas->nama_kelas }}</span>?
                </p>

                <div class="delete-info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        Siswa akan dikeluarkan dari kelas ini, namun data siswa tetap tersimpan dan dapat ditambahkan kembali kapan saja.
                    </p>
                </div>

                <div class="delete-modal-actions">
                    <button type="button" class="btn btn-cancel-delete" data-bs-dismiss="modal">
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
    @vite(['resources/js/waka/kelas/manage-siswa.js'])
@endsection
