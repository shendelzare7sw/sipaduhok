@extends('layouts.lms-guru')

@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-edit me-2"></i>Edit Tugas
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.tugas.update', [$kelas->id, $mapel->id, $tugas->id]) }}" 
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                    <input type="text" name="judul_tugas" class="form-control" 
                           value="{{ old('judul_tugas', $tugas->judul_tugas) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi / Instruksi <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control" rows="5" required>{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai" class="form-control" 
                               value="{{ old('tanggal_mulai', $tugas->tanggal_mulai->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Deadline <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_deadline" class="form-control" 
                               value="{{ old('tanggal_deadline', $tugas->tanggal_deadline->format('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">File Tugas (Kosongkan jika tidak ingin mengubah)</label>
                    @if($tugas->file_tugas)
                        <div class="alert alert-info mb-2">
                            File saat ini: <strong>{{ basename($tugas->file_tugas) }}</strong>
                        </div>
                    @endif
                    <input type="file" name="file_tugas" class="form-control">
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Tugas
                    </button>
                    <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection