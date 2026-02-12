@extends('layouts.lms-guru')

@section('title', 'Tambah Materi')
@section('page-title', 'Tambah Materi Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-plus-circle me-2"></i>Form Tambah Materi
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.materi.store', [$kelas->id, $mapel->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                        <input type="text" name="judul_materi"
                            class="form-control @error('judul_materi') is-invalid @enderror"
                            value="{{ old('judul_materi') }}" required>
                        @error('judul_materi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori <span class="text-danger">*</span></label>
                        <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                            <option value="materi" {{ (old('kategori') ?? $kategori ?? '') == 'materi' ? 'selected' : '' }}>
                                Materi Pendukung</option>
                            <option value="modul_ajar" {{ (old('kategori') ?? $kategori ?? '') == 'modul_ajar' ? 'selected' : '' }}>Modul Ajar (Utama)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe File <span class="text-danger">*</span></label>
                        <select name="tipe_file" class="form-control @error('tipe_file') is-invalid @enderror" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="pdf">PDF</option>
                            <option value="video">Video</option>
                            <option value="ppt">PowerPoint</option>
                            <option value="doc">Document</option>
                            <option value="link">Link URL</option>
                        </select>
                        @error('tipe_file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Upload <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_upload" class="form-control"
                            value="{{ old('tanggal_upload', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">File Materi (Max 50MB)</label>
                    <input type="file" name="file_materi" class="form-control">
                    <small class="text-muted">Kosongkan jika tipe Link URL</small>
                </div>

                @include('guru.partials.multi-kelas-selector')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Simpan Materi
                    </button>
                    <a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
