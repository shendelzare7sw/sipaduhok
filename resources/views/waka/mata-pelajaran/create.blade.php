@extends('layouts.sneat')

@section('title', 'Tambah Mata Pelajaran')
@section('page-title', 'Tambah Mata Pelajaran')
@section('page-subtitle', 'Tambah Mata Pelajaran Baru')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Form Tambah Mata Pelajaran
                </h5>
                <a href="{{ route('waka.mata-pelajaran.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('waka.mata-pelajaran.store') }}" method="POST">
                    @csrf

                    {{-- Nama Mata Pelajaran --}}
                    <div class="mb-3">
                        <label for="nama_mapel" class="form-label">
                            Nama Mata Pelajaran <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               class="form-control @error('nama_mapel') is-invalid @enderror"
                               id="nama_mapel"
                               name="nama_mapel"
                               value="{{ old('nama_mapel') }}"
                               placeholder="Contoh: Matematika, Bahasa Indonesia, dll"
                               required
                               autofocus>
                        @error('nama_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Jenjang --}}
                    <div class="mb-3">
                        <label for="jenjang" class="form-label">
                            Jenjang Pendidikan <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('jenjang') is-invalid @enderror"
                                id="jenjang"
                                name="jenjang"
                                required>
                            <option value="">-- Pilih Jenjang --</option>
                            @foreach($jenjangList as $jenjang)
                                <option value="{{ $jenjang }}" {{ old('jenjang') == $jenjang ? 'selected' : '' }}>
                                    {{ $jenjang }}
                                </option>
                            @endforeach
                        </select>
                        @error('jenjang')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Kode Mata Pelajaran --}}
                    <div class="mb-3">
                        <label for="kode_mapel" class="form-label">
                            Kode Mata Pelajaran <span class="text-muted">(Opsional)</span>
                        </label>
                        <input type="text"
                               class="form-control @error('kode_mapel') is-invalid @enderror"
                               id="kode_mapel"
                               name="kode_mapel"
                               value="{{ old('kode_mapel') }}"
                               placeholder="Contoh: MTK, IPA, BIN, dll">
                        <small class="form-text text-muted">
                            Kode unik untuk mata pelajaran (maksimal 20 karakter)
                        </small>
                        @error('kode_mapel')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-4">
                        <label for="deskripsi" class="form-label">
                            Deskripsi <span class="text-muted">(Opsional)</span>
                        </label>
                        <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                                  id="deskripsi"
                                  name="deskripsi"
                                  rows="4"
                                  placeholder="Deskripsi singkat tentang mata pelajaran ini">{{ old('deskripsi') }}</textarea>
                        @error('deskripsi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('waka.mata-pelajaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Card --}}
        <div class="card mt-4">
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-info-circle text-info me-2"></i>Informasi</h6>
                <ul class="mb-0">
                    <li>Field yang bertanda <span class="text-danger">*</span> wajib diisi</li>
                    <li>Nama mata pelajaran harus unik dan jelas</li>
                    <li>Kode mata pelajaran bersifat opsional, tetapi harus unik jika diisi</li>
                    <li>Pilih jenjang sesuai dengan kurikulum (SD, SMP, atau SMA)</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
