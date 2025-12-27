@extends('layouts.sneat')

@section('title', 'Buat Tagihan Massal')
@section('page-title', 'Buat Tagihan Massal')
@section('page-subtitle', 'Buat tagihan untuk seluruh siswa dalam satu kelas')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('bendahara.tagihan.index') }}" class="text-primary text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
        </a>
    </div>

    {{-- Info Alert --}}
    <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4">
        <div class="d-flex">
            <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
            <div>
                <strong>Informasi:</strong>
                <p class="mb-0 mt-2">
                    Fitur ini akan membuat tagihan untuk <strong>seluruh siswa aktif</strong> dalam kelas yang dipilih.
                    Jika siswa sudah memiliki tagihan dengan jenis yang sama, maka tagihan tersebut akan <strong>diperbarui</strong>.
                </p>
            </div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-success">
                <i class="fas fa-plus-circle me-2"></i>Form Tagihan Massal
            </h6>
        </div>
        <div class="card-body">
            <form action="{{ route('bendahara.tagihan.bulk-create') }}" method="POST">
                @csrf

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Pilih Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" class="form-control border-start border-primary border-3 shadow-sm" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ old('kelas_id') == $kelas->id ? 'selected' : '' }}>
                                    {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                                </option>
                            @endforeach
                        </select>
                        @error('kelas_id')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Jatuh Tempo <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_jatuh_tempo" class="form-control border-start border-primary border-3 shadow-sm"
                               value="{{ old('tanggal_jatuh_tempo', now()->addMonth()->format('Y-m-d')) }}"
                               required>
                        @error('tanggal_jatuh_tempo')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="mb-3 text-gray-800">
                    <i class="fas fa-money-bill-wave text-warning me-2"></i>Nominal Tagihan
                </h5>
                <p class="text-muted mb-3 small">
                    Masukkan nominal untuk setiap jenis tagihan. Kosongkan atau isi 0 jika tidak ingin membuat tagihan jenis tersebut.
                </p>

                <div class="row g-3 mb-4">
                    @foreach($jenisTagihan as $key => $label)
                        <div class="col-md-6 col-lg-4">
                            <div class="p-3 bg-light rounded shadow-sm">
                                <label class="form-label fw-bold small mb-2">{{ $label }}</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white">Rp</span>
                                    <input type="number"
                                           name="tagihan[{{ $key }}]"
                                           class="form-control"
                                           value="{{ old('tagihan.'.$key, 0) }}"
                                           min="0"
                                           step="1000"
                                           placeholder="0">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-secondary shadow-sm">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-success shadow-sm fw-bold">
                        <i class="fas fa-check me-1"></i> Buat Tagihan Massal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Warning Alert --}}
    <div class="alert alert-warning border-start border-warning border-4 shadow-sm">
        <div class="d-flex">
            <i class="fas fa-exclamation-triangle fa-lg me-2 mt-1"></i>
            <div>
                <strong>Perhatian:</strong>
                <ul class="mb-0 mt-2">
                    <li>Pastikan kelas yang dipilih sudah benar sebelum menyimpan.</li>
                    <li>Tagihan akan dibuat untuk tahun ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong></li>
                    <li>Proses ini tidak dapat dibatalkan. Jika terjadi kesalahan, Anda harus mengedit tagihan satu per satu.</li>
                </ul>
            </div>
        </div>
    </div>

</div>
</div>
@endsection