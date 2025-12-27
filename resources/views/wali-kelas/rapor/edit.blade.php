@extends('layouts.sneat')

@section('title', 'Edit Rapor')
@section('page-title', 'Edit Rapor')
@section('page-subtitle', 'Edit catatan dan kelengkapan rapor siswa')

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
    .total-box {
        padding: 10px;
        background: #f0f9ff;
        border-radius: 8px;
        margin-top: 26px;
        text-align: center;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold text-gray-900 mb-1">Edit Rapor - {{ $rapor->siswa->nama_lengkap }}</h4>
                    <p class="text-muted mb-0">
                        Semester: {{ ucfirst($rapor->semester) }} |
                        Kelas: {{ $rapor->kelas->nama_kelas }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank">
                        <i class="fas fa-eye me-1"></i>Preview
                    </a>
                    <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('wali.rapor.update', $rapor->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Data Kehadiran --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-calendar-check me-2"></i>Data Kehadiran
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Sakit (hari)</label>
                            <input type="number" name="jumlah_sakit" class="form-control"
                                   value="{{ old('jumlah_sakit', $rapor->jumlah_sakit) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Izin (hari)</label>
                            <input type="number" name="jumlah_izin" class="form-control"
                                   value="{{ old('jumlah_izin', $rapor->jumlah_izin) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alpha (hari)</label>
                            <input type="number" name="jumlah_alpha" class="form-control"
                                   value="{{ old('jumlah_alpha', $rapor->jumlah_alpha) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="total-box">
                            <small class="text-muted">Total Ketidakhadiran</small>
                            <div class="h3 mb-0 fw-bold text-primary" id="totalKetidakhadiran">
                                {{ $rapor->jumlah_sakit + $rapor->jumlah_izin + $rapor->jumlah_alpha }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Wali Kelas --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-comment-alt me-2"></i>Catatan Wali Kelas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan / Komentar untuk Siswa</label>
                    <textarea name="catatan_wali_kelas" class="form-control" rows="5"
                              placeholder="Contoh: Siswa menunjukkan peningkatan yang baik dalam...">{{ old('catatan_wali_kelas', $rapor->catatan_wali_kelas) }}</textarea>
                    <small class="text-muted">Berikan catatan positif dan saran untuk perkembangan siswa</small>
                </div>
            </div>
        </div>

        {{-- Daftar Nilai --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Daftar Nilai Mata Pelajaran
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th width="50">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center" width="120">Nilai Angka</th>
                                <th class="text-center" width="100">Nilai Huruf</th>
                                <th class="text-center" width="150">Predikat</th>
                                <th>Deskripsi Capaian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalNilai = 0; $jumlahMapel = 0; @endphp
                            @foreach($rapor->raporNilai as $index => $raporNilai)
                                @php
                                    $totalNilai += $raporNilai->nilai_angka;
                                    $jumlahMapel++;
                                @endphp
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle"><strong>{{ $raporNilai->mataPelajaran->nama_mapel }}</strong></td>
                                    <td class="text-center align-middle" style="background: #f0f9ff;">
                                        <strong class="text-primary" style="font-size: 16px;">
                                            {{ number_format($raporNilai->nilai_angka, 2) }}
                                        </strong>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-primary" style="font-size: 14px;">
                                            {{ $raporNilai->nilai_huruf }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle">
                                        {{ $raporNilai->nilai->predikat() }}
                                    </td>
                                    <td class="align-middle">
                                        <input type="text"
                                               name="deskripsi[{{ $raporNilai->id }}]"
                                               class="form-control form-control-sm"
                                               value="{{ old('deskripsi.' . $raporNilai->id, $raporNilai->deskripsi) }}"
                                               placeholder="Deskripsi capaian kompetensi...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background: #f3f4f6;">
                                <td colspan="2" class="text-end fw-bold align-middle" style="padding: 16px;">RATA-RATA:</td>
                                <td class="text-center fw-bold align-middle text-primary" style="font-size: 18px;">
                                    {{ $jumlahMapel > 0 ? number_format($totalNilai / $jumlahMapel, 2) : '0.00' }}
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($rapor->raporNilai->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-gray-200 mb-3"></i>
                        <p class="text-muted">Belum ada data nilai</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="card shadow mb-5">
            <div class="card-body text-end">
                <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
</div>
@endsection

@section('scripts')
<script>
    // Auto calculate total ketidakhadiran
    document.querySelectorAll('input[name="jumlah_sakit"], input[name="jumlah_izin"], input[name="jumlah_alpha"]').forEach(input => {
        input.addEventListener('input', function() {
            const sakit = parseInt(document.querySelector('input[name="jumlah_sakit"]').value) || 0;
            const izin = parseInt(document.querySelector('input[name="jumlah_izin"]').value) || 0;
            const alpha = parseInt(document.querySelector('input[name="jumlah_alpha"]').value) || 0;
            document.getElementById('totalKetidakhadiran').textContent = sakit + izin + alpha;
        });
    });
</script>
@endsection
