@extends('layouts.lms-guru')

@section('title', 'Edit Materi')
@section('page-title', 'Edit Materi')
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
            <i class="fas fa-edit me-2"></i>Edit Materi
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.materi.update', [$kelas->id, $mapel->id, $materi->id]) }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul Materi <span class="text-danger">*</span></label>
                    <input type="text" name="judul_materi" class="form-control @error('judul_materi') is-invalid @enderror" 
                           value="{{ old('judul_materi', $materi->judul_materi) }}" required>
                    @error('judul_materi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4">{{ old('deskripsi', $materi->deskripsi) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tipe File <span class="text-danger">*</span></label>
                        <select name="tipe_file" class="form-control" required>
                            <option value="pdf" {{ $materi->tipe_file == 'pdf' ? 'selected' : '' }}>PDF</option>
                            <option value="video" {{ $materi->tipe_file == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="ppt" {{ $materi->tipe_file == 'ppt' ? 'selected' : '' }}>PowerPoint</option>
                            <option value="doc" {{ $materi->tipe_file == 'doc' ? 'selected' : '' }}>Document</option>
                            <option value="link" {{ $materi->tipe_file == 'link' ? 'selected' : '' }}>Link URL</option>
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Upload <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_upload" class="form-control" 
                               value="{{ old('tanggal_upload', $materi->tanggal_upload->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">File Materi (Kosongkan jika tidak ingin mengubah)</label>
                    @if($materi->file_materi)
                        <div class="alert alert-info mb-2">
                            File saat ini: <strong>{{ basename($materi->file_materi) }}</strong>
                        </div>
                    @endif
                    <input type="file" name="file_materi" class="form-control">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Materi
                    </button>
                    <a href="{{ route('guru.lms.materi.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection