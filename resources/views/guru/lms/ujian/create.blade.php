@extends('layouts.lms-guru')

@section('title', 'Buat Ujian')
@section('page-title', 'Buat Ujian Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-custom">
            <i class="fas fa-plus-circle me-2"></i>Form Buat Ujian
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.ujian.store', [$kelas->id, $mapel->id]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul Ujian <span class="text-danger">*</span></label>
                    <input type="text" name="judul_ujian" class="form-control @error('judul_ujian') is-invalid @enderror" 
                           value="{{ old('judul_ujian') }}" required>
                    @error('judul_ujian')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi') }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe Ujian <span class="text-danger">*</span></label>
                        <select name="tipe_ujian" class="form-control @error('tipe_ujian') is-invalid @enderror" required>
                            <option value="">-- Pilih Tipe --</option>
                            <option value="harian" {{ old('tipe_ujian') == 'harian' ? 'selected' : '' }}>Ulangan Harian</option>
                            <option value="uts" {{ old('tipe_ujian') == 'uts' ? 'selected' : '' }}>UTS</option>
                            <option value="uas" {{ old('tipe_ujian') == 'uas' ? 'selected' : '' }}>UAS</option>
                        </select>
                        @error('tipe_ujian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" 
                               class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                               value="{{ old('tanggal_mulai') }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_selesai" 
                               class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                               value="{{ old('tanggal_selesai') }}" required>
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Durasi Ujian (menit) <span class="text-danger">*</span></label>
                    <input type="number" name="durasi_menit" class="form-control @error('durasi_menit') is-invalid @enderror" 
                           value="{{ old('durasi_menit', 90) }}" min="1" required>
                    @error('durasi_menit')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Catatan:</strong> Setelah ujian dibuat, Anda dapat menambahkan soal ujian di halaman edit.
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Buat Ujian
                    </button>
                    <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection