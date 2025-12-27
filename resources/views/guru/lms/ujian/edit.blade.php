@extends('layouts.lms-guru')

@section('title', 'Edit Ujian')
@section('page-title', 'Edit Ujian')
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
            <i class="fas fa-edit me-2"></i>Edit Ujian
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.ujian.update', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Judul Ujian <span class="text-danger">*</span></label>
                    <input type="text" name="judul_ujian" class="form-control" 
                           value="{{ old('judul_ujian', $ujian->judul_ujian) }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="3">{{ old('deskripsi', $ujian->deskripsi) }}</textarea>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tipe Ujian <span class="text-danger">*</span></label>
                        <select name="tipe_ujian" class="form-control" required>
                            <option value="harian" {{ $ujian->tipe_ujian == 'harian' ? 'selected' : '' }}>Ulangan Harian</option>
                            <option value="uts" {{ $ujian->tipe_ujian == 'uts' ? 'selected' : '' }}>UTS</option>
                            <option value="uas" {{ $ujian->tipe_ujian == 'uas' ? 'selected' : '' }}>UAS</option>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_mulai" class="form-control" 
                               value="{{ old('tanggal_mulai', $ujian->tanggal_mulai->format('Y-m-d\TH:i')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="tanggal_selesai" class="form-control" 
                               value="{{ old('tanggal_selesai', $ujian->tanggal_selesai->format('Y-m-d\TH:i')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Durasi Ujian (menit) <span class="text-danger">*</span></label>
                    <input type="number" name="durasi_menit" class="form-control" 
                           value="{{ old('durasi_menit', $ujian->durasi_menit) }}" min="1" required>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Update Ujian
                    </button>
                    <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" 
                       class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection