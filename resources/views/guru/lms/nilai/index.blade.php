@extends('layouts.lms-guru')

@section('title', 'Rekap Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Daftar Nilai Siswa</h6>
                        <small class="text-muted">Tahun Ajaran Aktif</small>
                    </div>
                    <div class="d-flex gap-2">
                        <form action="{{ route('guru.lms.nilai.recalculate', [$kelas->id, $mapel->id]) }}" method="POST"
                            onsubmit="return confirm('Hitung ulang semua nilai berdasarkan Tugas dan Ujian yang ada?')">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-sync-alt me-1"></i> Hitung Ulang Otomatis
                            </button>
                        </form>
                        <button class="btn btn-sm btn-success" disabled title="Segera Hadir">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </button>
                    </div>
                </div>
                <div class="p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="text-center">No</th>
                                    <th width="20%">Nama Siswa</th>
                                    <th width="10%" class="text-center">Tugas (15%)</th>
                                    <th width="10%" class="text-center">Latihan (15%)</th>
                                    <th width="10%" class="text-center">UH (20%)</th>
                                    <th width="10%" class="text-center">PTS (20%)</th>
                                    <th width="10%" class="text-center">PAS (30%)</th>
                                    <th width="10%" class="text-center">Nilai Akhir</th>
                                    <th width="10%" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($nilaiList as $index => $nilai)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="fw-bold">{{ $nilai->siswa->nama_lengkap ?? '-' }}</div>
                                            <small
                                                class="text-muted">{{ $nilai->siswa->nis ?? $nilai->siswa->nisn ?? '-' }}</small>
                                        </td>
                                        <form action="{{ route('guru.lms.nilai.update', [$kelas->id, $mapel->id]) }}"
                                            method="POST">
                                            @csrf
                                            <input type="hidden" name="nilai_id" value="{{ $nilai->id }}">

                                            <td class="text-center">
                                                <input type="number" step="0.01" name="tugas_1"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ round($nilai->rata_tugas ?? $nilai->tugas_1, 2) }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" name="latihan_1"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ round($nilai->rata_latihan ?? $nilai->latihan_1, 2) }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" name="uh_1"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ round($nilai->rata_uh ?? $nilai->uh_1, 2) }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" name="pts"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ round($nilai->pts, 2) }}">
                                            </td>
                                            <td class="text-center">
                                                <input type="number" step="0.01" name="pas"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ round($nilai->pas, 2) }}">
                                            </td>
                                            <td class="text-center fw-bold text-primary">
                                                {{ round($nilai->nilai_akhir, 2) }}
                                            </td>
                                            <td class="text-center">
                                                <button type="submit" class="btn btn-sm btn-primary"
                                                    title="Simpan Perubahan Manual">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="alert alert-info mt-3 border-0 d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-2x"></i>
                <div>
                    <h6 class="fw-bold mb-1">Informasi Penilaian</h6>
                    <ul class="mb-0 small ps-3">
                        <li>Nilai Tugas, UTS, dan UAS dapat dihitung otomatis dari modul Tugas dan Ujian.</li>
                        <li>Anda juga dapat mengubah nilai secara manual melalui kolom input di atas lalu tekan tombol
                            Simpan (<i class="fas fa-save"></i>).</li>
                        <li>Nilai Akhir dihitung berdasarkan bobot: <strong>Tugas (30%), UTS (30%), UAS (40%)</strong>.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection