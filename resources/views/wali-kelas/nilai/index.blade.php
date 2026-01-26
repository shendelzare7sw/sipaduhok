@extends('layouts.sneat')

@section('title', 'Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', isset($kelas) && $kelas ? 'Lihat nilai siswa kelas ' . $kelas->nama_kelas : 'Kelola nilai siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* Styling Tabel Nilai agar tetap Rapi */
    .table-nilai thead th {
        background-color: #f8f9fc;
        text-align: center;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        vertical-align: middle;
        color: #4e73df;
        border-bottom: 2px solid #e3e6f0;
    }
    .nilai-akhir {
        font-size: 1.05rem;
        font-weight: 800;
        color: #165fac;
    }
    .badge-predikat {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 800;
        color: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        font-size: 0.85rem;
    }
    .col-siswa {
        min-width: 200px;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
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
                        <a href="{{ route('wali.nilai.print', ['mata_pelajaran_id' => $selectedMapelId ?? '']) }}" target="_blank" class="btn btn-secondary btn-sm shadow-sm">
                            <i class="fas fa-print me-1"></i> Cetak Rekap Nilai
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER MATA PELAJARAN --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Pilih Mata Pelajaran</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('wali.nilai.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-9 mb-2 mb-md-0">
                            <select name="mata_pelajaran_id" class="form-select border-start border-primary border-4 shadow-sm" onchange="this.form.submit()">
                                <option value="">-- Lihat Semua (Ringkasan Siswa) --</option>
                                @foreach($mataPelajaranList as $mapel)
                                    <option value="{{ $mapel->id }}" {{ ($selectedMapelId ?? null) == $mapel->id ? 'selected' : '' }}>
                                        {{ $mapel->nama_mapel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
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

        {{-- TABEL UTAMA NILAI --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-table me-2"></i>Daftar Nilai {{ $selectedMapel ? ': ' . $selectedMapel->nama_mapel : '(Seluruh Siswa)' }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="50">NO</th>
                                <th width="120">NIS</th>
                                <th class="text-start col-siswa">NAMA LENGKAP SISWA</th>
                                @if(isset($selectedMapelId) && $selectedMapelId)
                                    <th>TUGAS</th>
                                    <th>LATIHAN</th>
                                    <th>UH</th>
                                    <th>PTS</th>
                                    <th>PAS</th>
                                    <th>N. AKHIR</th>
                                    <th>PRED.</th>
                                @endif
                                <th width="100">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($siswaList ?? [] as $index => $siswa)
                                @php
                                    $nilai = (isset($selectedMapelId) && $selectedMapelId) ? ($nilaiData[$siswa->id] ?? null) : null;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="text-center align-middle fw-bold text-gray-800">{{ $siswa->nis }}</td>
                                    <td class="align-middle col-siswa">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-muted">Kelas: {{ $kelas->nama_kelas }}</small>
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
                                        <td class="text-center align-middle">
                                            @if($nilai)
                                                @php
                                                    $predikat = $nilai->nilaiHuruf();
                                                    $colors = ['A'=>'bg-success', 'B'=>'bg-primary', 'C'=>'bg-warning', 'D'=>'bg-danger', 'E'=>'bg-dark'];
                                                @endphp
                                                <span class="badge-predikat {{ $colors[$predikat] ?? 'bg-secondary' }}">
                                                    {{ $predikat }}
                                                </span>
                                            @else
                                                <span class="text-muted small">N/A</span>
                                            @endif
                                        </td>
                                    @endif

                                    <td class="text-center align-middle">
                                        <a href="{{ route('wali.nilai.show', $siswa->id) }}" class="btn btn-info btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px;" title="Lihat Profil Nilai">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-5">
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
@endsection