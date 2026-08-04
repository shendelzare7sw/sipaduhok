@extends('layouts.sneat')

@section('title', 'Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', isset($kelas) && $kelas ? 'Lihat nilai siswa kelas ' . $kelas->nama_kelas : 'Kelola nilai siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-kelas/nilai/index.css', 'resources/js/wali-kelas/nilai/index.js'])
@endsection

@section('content')
<div class="wk-page">
<div class="container-fluid px-0">
    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @else
        {{-- HEADER ACTIONS --}}
        <div class="card shadow mb-4">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="m-0 fw-bold text-primary">Rekapitulasi Nilai Akademik</h5>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('wali.nilai.print', ['mata_pelajaran_id' => $selectedMapelId ?? '', 'semester' => $semester ?? '']) }}" target="_blank" class="btn btn-secondary btn-sm shadow-sm">
                            <i class="fas fa-print me-1"></i> Cetak Rekap Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER SEMESTER & MATA PELAJARAN --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filter Nilai</h6>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-dark border"><i class="fas fa-calendar me-1"></i>Semester {{ ucfirst($semester ?? 'genap') }}</span>
                    @if(($semester ?? 'genap') == ($currentSemester ?? 'genap'))
                        <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('wali.nilai.index') }}" method="GET">
                    <div class="row align-items-end g-2">
                        {{-- Semester Selector --}}
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"><i class="fas fa-calendar-alt me-1"></i>Semester</label>
                            <select name="semester" class="form-select border-start border-success border-4 shadow-sm" data-auto-submit>
                                <option value="ganjil" {{ ($semester ?? 'genap') == 'ganjil' ? 'selected' : '' }}>Ganjil (Jul-Des)</option>
                                <option value="genap" {{ ($semester ?? 'genap') == 'genap' ? 'selected' : '' }}>Genap (Jan-Jun)</option>
                            </select>
                        </div>
                        {{-- Mata Pelajaran Selector --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold"><i class="fas fa-book me-1"></i>Mata Pelajaran</label>
                            <select name="mata_pelajaran_id" class="form-select border-start border-primary border-4 shadow-sm" data-auto-submit>
                                <option value="">-- Lihat Semua (Ringkasan Siswa) --</option>
                                @foreach($mataPelajaranList as $mapel)
                                    <option value="{{ $mapel->id }}" {{ ($selectedMapelId ?? null) == $mapel->id ? 'selected' : '' }}>
                                        {{ $mapel->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        {{-- Reset Button --}}
                        <div class="col-md-3">
                            <a href="{{ route('wali.nilai.index') }}" class="btn btn-light border w-100 fw-bold">
                                <i class="fas fa-sync-alt me-1"></i> Reset Filter
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- STATISTIK KELAS (Tampil jika Mapel tertentu dipilih) --}}
        @if(isset($selectedMapelId) && $selectedMapelId)
            <div class="row mb-2">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-start border-primary border-4 shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row g-0 align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs fw-bold text-primary text-uppercase mb-1">Rata-rata Kelas</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($rataRataKelas, 2) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-calculator fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-start border-success border-4 shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row g-0 align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs fw-bold text-success text-uppercase mb-1">Nilai Tertinggi</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($nilaiTertinggi, 2) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-trophy fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-start border-danger border-4 shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row g-0 align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs fw-bold text-danger text-uppercase mb-1">Nilai Terendah</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ number_format($nilaiTerendah, 2) }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-start border-info border-4 shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row g-0 align-items-center">
                                <div class="col me-2">
                                    <div class="text-xs fw-bold text-info text-uppercase mb-1">Ketuntasan Siswa</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800">{{ $jumlahTuntas }} / {{ $siswaList->count() }}</div>
                                </div>
                                <div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(isset($selectedMapelId) && $selectedMapelId && ($jumlahGuruUpdate ?? 0) > 0)
            <div class="alert alert-warning shadow-sm border-start border-warning border-4 mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-bell fa-lg me-3 mt-1 text-warning"></i>
                    <div>
                        <h6 class="alert-heading fw-bold mb-1">{{ $jumlahGuruUpdate }} siswa punya update nilai dari guru</h6>
                        <p class="small mb-0 text-muted">
                            Guru pengajar mapel ini sudah menyimpan nilai baru setelah Anda terakhir mengedit.
                            Klik tombol <span class="badge bg-warning text-dark"><i class="fas fa-bell"></i></span> di kolom AKSI siswa terkait untuk membuka halaman edit — di sana ada tombol <strong>Preview vs Guru</strong> & <strong>Sinkronisasi dari Guru</strong> per mata pelajaran.
                        </p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TABEL UTAMA NILAI --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-table me-2"></i>Daftar Nilai {{ $selectedMapel ? ': ' . $selectedMapel->nama_mapel : '(Seluruh Siswa)' }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover wk-card-table mb-0">
                        <thead>
                            <tr>
                                <th class="col-no">NO</th>
                                <th class="col-nis">NIS</th>
                                <th class="text-start col-siswa">NAMA LENGKAP SISWA</th>
                                @if(isset($selectedMapelId) && $selectedMapelId)
                                    <th>TUGAS</th>
                                    <th>LATIHAN</th>
                                    <th>UH</th>
                                    <th>PTS</th>
                                    <th>PAS</th>
                                    <th>N. AKHIR</th>
                                @endif
                                <th width="100">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaList ?? [] as $index => $siswa)
                                @php
                                    $nilai = (isset($selectedMapelId) && $selectedMapelId) ? ($nilaiData[$siswa->id] ?? null) : null;
                                    $guruBaruUpdate = $nilai
                                        && $nilai->hasGuruUpdate()
                                        && $nilai->guru_terakhir_simpan_at
                                        && (!$nilai->wali_terakhir_edit_at
                                            || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
                                    $waliEditBeda = $nilai
                                        && $nilai->wali_terakhir_edit_at
                                        && $nilai->hasGuruUpdate()
                                        && !$guruBaruUpdate;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600 col-no">{{ $loop->iteration }}</td>
                                    <td class="text-center align-middle fw-bold text-gray-800 col-nis">{{ $siswa->nis }}</td>
                                    <td class="align-middle col-siswa">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-muted">Kelas: {{ $kelas->nama_kelas }}</small>
                                        @if($guruBaruUpdate)
                                            <div class="mt-1">
                                                <span class="badge bg-warning text-dark" title="Guru pengajar sudah update nilai setelah Anda terakhir edit. Buka detail untuk preview diff & sync.">
                                                    <i class="fas fa-bell me-1"></i> Guru Update Baru
                                                </span>
                                            </div>
                                        @elseif($waliEditBeda)
                                            <div class="mt-1">
                                                <span class="badge bg-info text-white" title="Nilai saat ini berbeda dari snapshot guru karena Anda sudah mengedit.">
                                                    <i class="fas fa-user-edit me-1"></i> Edit Wali
                                                </span>
                                            </div>
                                        @endif
                                    </td>

                                    @if(isset($selectedMapelId) && $selectedMapelId)
                                        <td class="text-center align-middle fw-bold">{{ $nilai ? number_format($nilai->rata_tugas ?? 0, 1) : '-' }}</td>
                                        <td class="text-center align-middle fw-bold">{{ $nilai ? number_format($nilai->rata_latihan ?? 0, 1) : '-' }}</td>
                                        <td class="text-center align-middle fw-bold">{{ $nilai ? number_format($nilai->rata_uh ?? 0, 1) : '-' }}</td>
                                        <td class="text-center align-middle fw-bold">{{ $nilai ? number_format($nilai->pts ?? 0, 1) : '-' }}</td>
                                        <td class="text-center align-middle fw-bold">{{ $nilai ? number_format($nilai->pas ?? 0, 1) : '-' }}</td>
                                        <td class="text-center align-middle">
                                            <span class="nilai-akhir">{{ $nilai ? number_format($nilai->nilai_akhir ?? 0, 1) : '-' }}</span>
                                        </td>
                                    @endif

                                    <td class="text-center align-middle">
                                        <div class="d-flex gap-1 justify-content-center">
                                            <a href="{{ route('wali.nilai.show', $siswa->id) }}?semester={{ $semester }}" class="btn btn-info btn-sm rounded-circle shadow-sm nilai-action-btn" title="Lihat Profil Nilai">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('wali.nilai.edit', $siswa->id) }}?semester={{ $semester }}" class="btn {{ $guruBaruUpdate ? 'btn-warning' : 'btn-primary' }} btn-sm rounded-circle shadow-sm nilai-action-btn" title="{{ $guruBaruUpdate ? 'Ada update guru — buka untuk preview & sinkron' : 'Edit nilai semua mapel' }}">
                                                <i class="fas {{ $guruBaruUpdate ? 'fa-bell' : 'fa-edit' }}"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-5">
                                        <i class="fas fa-database fa-3x text-gray-200 mb-3"></i>
                                        <p class="text-gray-500 mb-0">Belum ada data siswa untuk ditampilkan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
</div>
</div>

@endsection
