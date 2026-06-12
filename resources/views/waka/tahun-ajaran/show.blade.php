@extends('layouts.sneat')

@section('title', 'Detail Tahun Ajaran')

@section('page-title', 'Detail Tahun Ajaran')
@section('page-subtitle', 'Informasi lengkap tahun ajaran')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/tahun-ajaran/show.css'])
@endsection

@section('content')
<div class="tahun-ajaran-show-page">
    <div class="d-flex justify-content-between align-items-center mb-3 show-header-action">
        <h5 class="mb-0 show-title">
            Detail: {{ $tahunAjaran->nama_tahun_ajaran }}
        </h5>
        <a href="{{ route('waka.tahun-ajaran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left btn-icon"></i> Kembali
        </a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-muted btn-icon"></i> Informasi Umum</h5>
                </div>
                <div class="card-body">
                    <table class="table-detail">
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td class="detail-strong"><strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($tahunAjaran->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>{{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>{{ \Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>
                                {{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)) }} hari
                                <span class="text-muted duration-note">
                                    ({{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->diffInMonths(\Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)) }} bulan)
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $tahunAjaran->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header semester-header">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt text-success btn-icon"></i> Periode Semester</h5>
                </div>
                <div class="card-body">
                    @php
                        $periods = $tahunAjaran->getSemesterPeriods();
                        $currentSemester = \App\Models\TahunAjaran::getCurrentSemester();
                    @endphp

                    <div class="semester-grid">
                        <div class="semester-card is-ganjil">
                            <div class="semester-card-title is-ganjil">
                                <i class="fas fa-sun"></i>
                                <strong>Semester Ganjil</strong>
                                @if($tahunAjaran->is_active && $currentSemester == 'ganjil')
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </div>
                            <div class="semester-card-body is-ganjil">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $periods['ganjil']['start']->format('d M Y') }} - {{ $periods['ganjil']['end']->format('d M Y') }}
                            </div>
                        </div>

                        <div class="semester-card is-genap">
                            <div class="semester-card-title is-genap">
                                <i class="fas fa-snowflake"></i>
                                <strong>Semester Genap</strong>
                                @if($tahunAjaran->is_active && $currentSemester == 'genap')
                                    <span class="badge bg-success">Aktif</span>
                                @endif
                            </div>
                            <div class="semester-card-body is-genap">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $periods['genap']['start']->format('d M Y') }} - {{ $periods['genap']['end']->format('d M Y') }}
                            </div>
                        </div>
                    </div>

                    @if(!$tahunAjaran->tanggal_mulai_genap)
                        <div class="semester-auto-note">
                            <i class="fas fa-info-circle me-1"></i> Periode semester menggunakan perhitungan otomatis (Juli-Des = Ganjil, Jan-Jun = Genap).
                            <a href="{{ route('waka.tahun-ajaran.edit', $tahunAjaran->id) }}">Atur periode kustom</a>
                        </div>
                    @endif

                    <hr class="report-divider">
                    <div class="report-heading">
                        <i class="fas fa-flag-checkered"></i>
                        <strong>Periode Rapor (PTS vs PAS)</strong>
                    </div>
                    @php
                        $ptsGanjil = $tahunAjaran->getRaporPeriod('ganjil', 'tengah_semester');
                        $pasGanjil = $tahunAjaran->getRaporPeriod('ganjil', 'akhir_semester');
                        $ptsGenap  = $tahunAjaran->getRaporPeriod('genap', 'tengah_semester');
                        $pasGenap  = $tahunAjaran->getRaporPeriod('genap', 'akhir_semester');
                    @endphp
                    <div class="report-grid">
                        <div class="report-card is-pts">
                            <div class="report-title"><i class="fas fa-clipboard-list me-1"></i> PTS Ganjil</div>
                            <div>{{ $ptsGanjil['start']->format('d M Y') }} - {{ $ptsGanjil['end']->format('d M Y') }}</div>
                            @if(!$tahunAjaran->tanggal_akhir_pts_ganjil)
                                <small class="report-muted">(default: 3 bulan)</small>
                            @endif
                        </div>
                        <div class="report-card is-pas-ganjil">
                            <div class="report-title"><i class="fas fa-chart-bar me-1"></i> PAS Ganjil</div>
                            <div>{{ $pasGanjil['start']->format('d M Y') }} - {{ $pasGanjil['end']->format('d M Y') }}</div>
                        </div>
                        <div class="report-card is-pts">
                            <div class="report-title"><i class="fas fa-clipboard-list me-1"></i> PTS Genap</div>
                            <div>{{ $ptsGenap['start']->format('d M Y') }} - {{ $ptsGenap['end']->format('d M Y') }}</div>
                            @if(!$tahunAjaran->tanggal_akhir_pts_genap)
                                <small class="report-muted">(default: 3 bulan)</small>
                            @endif
                        </div>
                        <div class="report-card is-pas-genap">
                            <div class="report-title"><i class="fas fa-chart-bar me-1"></i> PAS Genap</div>
                            <div>{{ $pasGenap['start']->format('d M Y') }} - {{ $pasGenap['end']->format('d M Y') }}</div>
                        </div>
                    </div>
                    <small class="report-footer-note">
                        <i class="fas fa-info-circle"></i> Periode ini dipakai untuk auto-fill kehadiran rapor (sakit/izin/alpha) dari menu Presensi.
                        @if(!$tahunAjaran->tanggal_akhir_pts_ganjil || !$tahunAjaran->tanggal_akhir_pts_genap)
                            <a href="{{ route('waka.tahun-ajaran.edit', $tahunAjaran->id) }}">Atur tanggal akhir PTS</a>
                        @endif
                    </small>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chalkboard text-muted btn-icon"></i> Data Kelas Terkait</h5>
                    <span class="badge bg-secondary kelas-count-badge">{{ $tahunAjaran->kelas->count() }} Kelas</span>
                </div>
                <div class="card-body p-0">
                    @if($tahunAjaran->kelas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 related-table">
                                <thead>
                                    <tr>
                                        <th>Nama Kelas</th>
                                        <th>Tingkat</th>
                                        <th>Cabang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tahunAjaran->kelas as $kelas)
                                    <tr>
                                        <td><strong>{{ $kelas->nama_kelas }}</strong></td>
                                        <td>{{ $kelas->jenjang ?? '-' }}</td>
                                        <td>{{ $kelas->cabang->nama_cabang ?? '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-related">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p class="mb-0">Belum ada kelas yang terkait.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body action-grid">
                    <a href="{{ route('waka.tahun-ajaran.edit', $tahunAjaran->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit btn-icon"></i> Edit Data
                    </a>

                    @if(!$tahunAjaran->is_active)
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#activateModal">
                            <i class="fas fa-check btn-icon"></i> Aktifkan
                        </button>
                    @endif
                </div>
            </div>

            <div class="card danger-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> Peringatan</h5>
                </div>
                <div class="card-body">
                    <p>
                        Menghapus tahun ajaran bersifat permanen. Data yang dihapus tidak dapat dikembalikan.
                    </p>
                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash btn-icon"></i> Hapus Tahun Ajaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@if(!$tahunAjaran->is_active)
<div class="modal fade" id="activateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Aktifkan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Aktifkan tahun ajaran <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>?
                <div class="modal-note is-warning">
                    <i class="fas fa-exclamation-triangle"></i> Tahun aktif saat ini akan otomatis dinonaktifkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('waka.tahun-ajaran.activate', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus tahun ajaran <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>?
                <div class="modal-note is-danger">
                    <i class="fas fa-exclamation-circle"></i> Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('waka.tahun-ajaran.destroy', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
