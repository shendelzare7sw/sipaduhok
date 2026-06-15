@extends('layouts.sneat')

@section('title', 'Tambah Mata Pelajaran')
@section('page-title', 'Tambah Mata Pelajaran')
@section('page-subtitle', 'Tambah Mata Pelajaran Baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle text-primary me-2"></i>Form Tambah Mata Pelajaran
                </h5>
                <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.mata-pelajaran.store') }}" method="POST">
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

                    {{-- Kelompok --}}
                    <div class="mb-3">
                        <label for="kelompok" class="form-label">
                            Kelompok <span class="text-muted">(Opsional)</span>
                        </label>
                        <select name="kelompok" id="kelompok" class="form-select @error('kelompok') is-invalid @enderror">
                            <option value="">-- Pilih Kelompok --</option>
                            <option value="A" {{ old('kelompok') === 'A' ? 'selected' : '' }}>
                                A (Mata Pelajaran Umum)
                            </option>
                            <option value="B" {{ old('kelompok') === 'B' ? 'selected' : '' }}>
                                B (Mata Pelajaran Pilihan/Muatan Lokal)
                            </option>
                        </select>
                        @error('kelompok')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Kelompok A: Pendidikan Agama, PKN, B.Indonesia, Matematika, IPA, IPS, B.Inggris, dll.<br>
                            Kelompok B: Prakarya, Seni Budaya, Muatan Lokal, dll.
                        </small>
                    </div>

                    {{-- Kode Mata Pelajaran --}}
                    <div class="mb-3">
                        <label for="kode_mapel" class="form-label">
                            Kode Mata Pelajaran <span class="text-muted">(Opsional)</span>
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control @error('kode_mapel') is-invalid @enderror"
                                   id="kode_mapel"
                                   name="kode_mapel"
                                   value="{{ old('kode_mapel') }}"
                                   placeholder="Contoh: MTK, IPA, BIN, dll">
                            <button class="btn btn-outline-primary" type="button" id="btnAutoGenerate">
                                <i class="fas fa-magic me-1"></i> Generate
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            Kode unik untuk mata pelajaran (maksimal 20 karakter). Klik "Generate" untuk melihat saran.
                        </small>
                        @error('kode_mapel')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                        
                        {{-- Suggestions Dropdown --}}
                        <div id="suggestionsContainer" class="mt-2 d-none" data-suggest-url="{{ route('admin.mata-pelajaran.suggest-kode') }}">
                            <div class="card shadow-sm">
                                <div class="card-header bg-light py-2">
                                    <small class="text-muted fw-bold">
                                        <i class="fas fa-lightbulb me-1"></i> Saran Kode (belum digunakan di jenjang ini)
                                    </small>
                                </div>
                                <div class="card-body p-2">
                                    <div id="suggestionsList" class="d-flex flex-wrap gap-2"></div>
                                    <div id="suggestionsLoading" class="text-center py-3 d-none">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                                            <span class="visually-hidden">Memuat...</span>
                                        </div>
                                        <small class="d-block mt-2 text-muted">Mencari kode yang tersedia...</small>
                                    </div>
                                    <div id="suggestionsEmpty" class="text-center py-2 d-none">
                                        <small class="text-muted">
                                            <i class="fas fa-info-circle me-1"></i> Pilih jenjang terlebih dahulu
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                        <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-secondary">
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

@section('scripts')
    @vite(['resources/js/admin/mata-pelajaran/form.js'])
@endsection
