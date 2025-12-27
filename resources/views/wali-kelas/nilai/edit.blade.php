@extends('layouts.sneat')

@section('title', 'Edit Nilai Siswa')
@section('page-title', 'Edit Nilai Siswa')
@section('page-subtitle', 'Perbarui nilai per mata pelajaran')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }
    .nilai-akhir-display {
        padding: 8px;
        background: #f3f4f6;
        border-radius: 6px;
        font-weight: 700;
        color: #4e73df;
    }
    .student-info-card {
        background: white;
        border-radius: 15px;
        border-left: 5px solid #4e73df;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Back Button --}}
    <div class="mb-4 no-print">
        <a href="{{ route('wali.nilai.index') }}" class="btn btn-light btn-sm fw-bold shadow-sm border text-gray-700">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Nilai
        </a>
    </div>

    {{-- Student Info Card --}}
    <div class="card student-info-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-gray-900 mb-2">Edit Nilai - {{ $siswa->nama_lengkap }}</h4>
            <div class="row">
                <div class="col-md-4">
                    <div class="small text-muted fw-bold text-uppercase">NIS</div>
                    <div class="fw-bold text-dark">{{ $siswa->nis }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted fw-bold text-uppercase">NISN</div>
                    <div class="fw-bold text-dark">{{ $siswa->nisn }}</div>
                </div>
                <div class="col-md-4">
                    <div class="small text-muted fw-bold text-uppercase">Kelas</div>
                    <div class="fw-bold text-dark">{{ $kelas->nama_kelas }}</div>
                </div>
            </div>
            <div class="mt-3">
                <div class="small text-muted fw-bold text-uppercase">Tahun Ajaran</div>
                <div class="fw-bold text-dark">{{ $kelas->tahunAjaran->nama_tahun_ajaran }}</div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-start border-danger border-4 mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Ada kesalahan dalam input data:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form --}}
    <form action="{{ route('wali.nilai.update', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-table me-2"></i>Input Nilai Per Mata Pelajaran
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="text-center" width="50">NO</th>
                                <th>MATA PELAJARAN</th>
                                <th class="text-center" width="120">TUGAS (30%)</th>
                                <th class="text-center" width="120">UTS (30%)</th>
                                <th class="text-center" width="120">UAS (40%)</th>
                                <th class="text-center" width="130">NILAI AKHIR</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mataPelajaranList as $index => $mapel)
                                @php
                                    $nilai = $nilaiData[$mapel->id] ?? null;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $mapel->nama_mapel }}</div>
                                        <small class="text-muted text-uppercase">{{ $mapel->kode_mapel }}</small>
                                    </td>

                                    {{-- Nilai Tugas Input --}}
                                    <td class="text-center align-middle">
                                        <input
                                            type="number"
                                            name="nilai[{{ $mapel->id }}][nilai_tugas]"
                                            class="form-control text-center fw-bold"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            value="{{ $nilai && $nilai->nilai_tugas ? $nilai->nilai_tugas : '' }}"
                                            placeholder="0"
                                        >
                                        <input type="hidden" name="nilai[{{ $mapel->id }}][mata_pelajaran_id]" value="{{ $mapel->id }}">
                                    </td>

                                    {{-- Nilai UTS Input --}}
                                    <td class="text-center align-middle">
                                        <input
                                            type="number"
                                            name="nilai[{{ $mapel->id }}][nilai_uts]"
                                            class="form-control text-center fw-bold"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            value="{{ $nilai && $nilai->nilai_uts ? $nilai->nilai_uts : '' }}"
                                            placeholder="0"
                                        >
                                    </td>

                                    {{-- Nilai UAS Input --}}
                                    <td class="text-center align-middle">
                                        <input
                                            type="number"
                                            name="nilai[{{ $mapel->id }}][nilai_uas]"
                                            class="form-control text-center fw-bold"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            value="{{ $nilai && $nilai->nilai_uas ? $nilai->nilai_uas : '' }}"
                                            placeholder="0"
                                        >
                                    </td>

                                    {{-- Nilai Akhir (Read-only) --}}
                                    <td class="text-center align-middle">
                                        <div class="nilai-akhir-display">
                                            @php
                                                $nilaiAkhir = '-';
                                                if ($nilai && ($nilai->nilai_tugas || $nilai->nilai_uts || $nilai->nilai_uas)) {
                                                    $tugas = $nilai->nilai_tugas ?? 0;
                                                    $uts = $nilai->nilai_uts ?? 0;
                                                    $uas = $nilai->nilai_uas ?? 0;
                                                    $nilaiAkhir = number_format(($tugas * 0.3) + ($uts * 0.3) + ($uas * 0.4), 2);
                                                }
                                            @endphp
                                            <span id="nilai_akhir_{{ $mapel->id }}">{{ $nilaiAkhir }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <i class="fas fa-inbox fa-3x text-gray-200 mb-3 d-block opacity-50"></i>
                                        <p class="text-gray-500 mb-0">Tidak ada mata pelajaran</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Information Box --}}
            <div class="card-footer bg-light border-top py-3">
                <div class="row align-items-center">
                    <div class="col-md-1 text-center d-none d-md-block">
                        <i class="fas fa-info-circle fa-2x text-info opacity-50"></i>
                    </div>
                    <div class="col-md-11">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Metode Perhitungan Nilai Akhir</div>
                        <p class="mb-0 small text-gray-700">
                            Nilai Akhir dihitung secara otomatis dengan bobot:
                            <strong>Tugas (30%)</strong>, <strong>UTS (30%)</strong>, dan <strong>UAS (40%)</strong>.
                            Nilai akan otomatis terupdate saat Anda mengetik.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan / Notes Section --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-comment-alt me-2"></i>Catatan Tambahan (Opsional)
                </h6>
            </div>
            <div class="card-body">
                <textarea
                    name="catatan"
                    id="catatan"
                    class="form-control"
                    rows="4"
                    placeholder="Tambahkan catatan atau komentar tentang nilai siswa..."
                >{{ $nilai->catatan ?? '' }}</textarea>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end mb-5">
            <a href="{{ route('wali.nilai.show', $siswa->id) }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-times me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                <i class="fas fa-save me-1"></i> Simpan Perubahan
            </button>
        </div>
    </form>

</div>
</div>
@endsection

@section('scripts')
<script>
    // Calculate nilai akhir real-time for each mapel
    document.addEventListener('DOMContentLoaded', function() {
        // Find all input groups by mapel_id
        @foreach($mataPelajaranList as $mapel)
            const mapelId = {{ $mapel->id }};
            const tugas = document.querySelector(`input[name="nilai[${mapelId}][nilai_tugas]"]`);
            const uts = document.querySelector(`input[name="nilai[${mapelId}][nilai_uts]"]`);
            const uas = document.querySelector(`input[name="nilai[${mapelId}][nilai_uas]"]`);
            const display = document.querySelector(`#nilai_akhir_${mapelId}`);

            const updateNilaiAkhir = () => {
                const t = parseFloat(tugas.value) || 0;
                const u = parseFloat(uts.value) || 0;
                const a = parseFloat(uas.value) || 0;

                if (t || u || a) {
                    const hasil = (t * 0.3) + (u * 0.3) + (a * 0.4);
                    display.textContent = hasil.toFixed(2);
                } else {
                    display.textContent = '-';
                }
            };

            tugas.addEventListener('input', updateNilaiAkhir);
            uts.addEventListener('input', updateNilaiAkhir);
            uas.addEventListener('input', updateNilaiAkhir);
        @endforeach
    });
</script>
@endsection
