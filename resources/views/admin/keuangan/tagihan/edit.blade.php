@extends('layouts.sneat')

@section('title', 'Edit Tagihan - ' . $siswa->nama_lengkap)
@section('page-title', 'Edit Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .student-avatar {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="text-primary text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Tagihan
        </a>
    </div>

    {{-- Info Siswa Card --}}
    <div class="card shadow mb-4 border-start border-primary border-4">
        <div class="card-body bg-primary bg-opacity-10">
            <div class="d-flex gap-3 align-items-center">
                <div class="student-avatar">
                    {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-gray-800">{{ $siswa->nama_lengkap }}</h5>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-id-card me-1"></i> NISN: {{ $siswa->nisn }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-school me-1"></i> Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-building me-1"></i> {{ $siswa->cabang->nama_cabang ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Tagihan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-warning">
                <i class="fas fa-edit me-2"></i>Input/Edit Tagihan
            </h6>
        </div>
        <div class="card-body">
            {{-- Info Alert tentang SPP --}}
            <div class="alert alert-info border-start border-info border-4 mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle me-2 mt-1"></i>
                    <div>
                        <strong>Informasi Penting:</strong>
                        <p class="mb-0 mt-1">Untuk tagihan <strong>SPP Bulanan</strong>, silakan gunakan fitur <a href="{{ route('admin.keuangan.tagihan.generate-spp') }}" class="alert-link fw-bold">"Generate SPP"</a> yang akan membuat 12 tagihan SPP otomatis (Januari-Desember) dengan tanggal jatuh tempo yang lebih akurat.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.keuangan.tagihan.update', $siswa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th style="min-width: 200px;">Jenis Tagihan</th>
                                <th style="min-width: 200px;">Jumlah (Rp)</th>
                                <th style="min-width: 180px;">Jatuh Tempo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisTagihan as $key => $label)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $loop->iteration }}</td>
                                    <td class="align-middle">
                                        <strong>{{ $label }}</strong>
                                        @if($key === 'spp')
                                            <br><small class="text-muted">Tagihan bulanan</small>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        <div class="input-group" style="max-width: 250px;">
                                            <span class="input-group-text bg-white">Rp</span>
                                            <input type="text"
                                                   name="tagihan[{{ $key }}]"
                                                   class="form-control currency-input"
                                                   value="{{ old('tagihan.'.$key, number_format($tagihanExist[$key] ?? 0, 0, ',', '.')) }}"
                                                   placeholder="0">
                                        </div>
                                        @error('tagihan.'.$key)
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-middle">
                                        <input type="date"
                                               name="tanggal_jatuh_tempo[{{ $key }}]"
                                               class="form-control"
                                               value="{{ old('tanggal_jatuh_tempo.'.$key, now()->addMonth()->format('Y-m-d')) }}"
                                               style="max-width: 200px;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Masukkan nominal 0 jika siswa tidak memiliki tagihan untuk jenis tersebut.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary shadow-sm fw-bold">
                                <i class="fas fa-save me-1"></i> Simpan Tagihan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Warning Alert --}}
    <div class="alert alert-warning border-start border-warning border-4 shadow-sm">
        <div class="d-flex">
            <i class="fas fa-exclamation-triangle fa-lg me-2 mt-1"></i>
            <div>
                <strong>Catatan:</strong>
                <ul class="mb-0 mt-2">
                    <li>Tagihan yang sudah memiliki pembayaran tidak dapat dihapus, hanya dapat diubah nominalnya.</li>
                    <li>Perubahan tagihan akan mempengaruhi status pembayaran siswa.</li>
                    <li>Tahun ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong></li>
                </ul>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script>
    // Handle form submission - parse currency values before submit
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');

        form.addEventListener('submit', function(e) {
            // Parse all currency inputs before submission
            const currencyInputs = form.querySelectorAll('.currency-input');
            currencyInputs.forEach(input => {
                // Remove dots (thousand separators) before submit
                const rawValue = input.value.replace(/\./g, '');
                input.value = rawValue || '0';
            });
        });
    });
</script>
@endsection