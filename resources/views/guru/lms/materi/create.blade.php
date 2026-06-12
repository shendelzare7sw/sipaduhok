@extends('layouts.lms-guru')

@section('title', 'Tambah Materi')
@section('page-title', 'Tambah Materi Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@php
    $selectedTipeFile = old('tipe_file');
@endphp

@section('content')
<div class="guru-lms-materi-create-page">
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
                        <select name="tipe_file" id="tipeFile" class="form-control @error('tipe_file') is-invalid @enderror" required data-file-type-toggle>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="pdf" {{ $selectedTipeFile == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="video" {{ $selectedTipeFile == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="ppt" {{ $selectedTipeFile == 'ppt' ? 'selected' : '' }}>PowerPoint</option>
                            <option value="doc" {{ $selectedTipeFile == 'doc' ? 'selected' : '' }}>Document</option>
                            <option value="link" {{ $selectedTipeFile == 'link' ? 'selected' : '' }}>Link URL</option>
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

                <div id="fileInputContainer" class="mb-3" data-file-input-container @if(!$selectedTipeFile || $selectedTipeFile === 'link') hidden @endif>
                    <label class="form-label">File Materi <span class="text-danger" id="fileRequired">{{ $selectedTipeFile && $selectedTipeFile !== 'link' ? '*' : '' }}</span></label>
                    <input type="file" name="file_materi" id="fileMateri" class="form-control @error('file_materi') is-invalid @enderror" data-file-input>
                    <small class="text-muted">Maximum 50MB</small>
                    @error('file_materi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div id="linkInputContainer" class="mb-3" data-link-input-container @if($selectedTipeFile !== 'link') hidden @endif>
                    <label class="form-label">URL Link <span class="text-danger">*</span></label>
                    <input type="url" name="url_materi" id="urlMateri" class="form-control" placeholder="https://example.com" data-url-input>
                    <small class="text-muted">Contoh: https://youtu.be/... atau link dokumentasi lainnya</small>
                    @error('url_materi')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
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

</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/materi/create.js'])
@endpush
