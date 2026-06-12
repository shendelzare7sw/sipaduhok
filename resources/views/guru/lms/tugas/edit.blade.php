@extends('layouts.lms-guru')

@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@php
    $bisaDiulang = old('bisa_diulang', $tugas->bisa_diulang);
@endphp

@push('styles')
    @vite(['resources/css/guru/lms/tugas/edit.css'])
@endpush

@section('content')
<div class="guru-lms-tugas-edit-page">
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
                        <div class="mb-2">
                            <strong>File saat ini:</strong>
                            <x-file-preview :path="$tugas->file_tugas" label="Lihat File Saat Ini" />
                        </div>
                    @endif
                    <input type="file" name="file_tugas" class="form-control">
                </div>

                <div class="mb-4">
                    <h5 class="form-label fw-bold border-bottom pb-2">Pengaturan Penilaian & Pengulangan</h5>
                    <div class="p-3 border rounded bg-light mb-3">
                        <!-- Tampilkan Nilai -->
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanNilai" name="tampilkan_nilai" value="1" {{ old('tampilkan_nilai', $tugas->tampilkan_nilai) ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="tampilkanNilai">Tampilkan Nilai ke Siswa</label>
                        </div>
                        <small class="text-muted d-block mt-1">Jika dinonaktifkan, siswa tidak akan bisa melihat nilai tugasnya meskipun sudah dinilai oleh guru.</small>
                    </div>

                    <div class="p-3 border rounded bg-light">
                        <!-- Pengulangan (Edit) -->
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="bisaDiulang" name="bisa_diulang" value="1" data-repeat-toggle {{ $bisaDiulang ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="bisaDiulang">Izinkan Siswa Mengedit Jawaban</label>
                        </div>
                        <small class="text-muted d-block mb-3">Jika diaktifkan, siswa dapat mengubah jawabannya sebelum deadline.</small>

                        <div id="batasPengulanganContainer" class="repeat-limit-container" data-repeat-container @if(!$bisaDiulang) hidden @endif>
                            <label for="batasPengulangan" class="form-label fw-semibold">Batas Edit (Kali)</label>
                            <input type="number" class="form-control repeat-limit-input" id="batasPengulangan" name="batas_pengulangan" min="0" placeholder="Kosongkan jika tak terbatas" value="{{ old('batas_pengulangan', $tugas->batas_pengulangan) }}" data-repeat-input>
                            <small class="text-muted d-block mt-1">Biarkan kosong agar siswa bisa mengedit tanpa batas (selama belum deadline).</small>
                        </div>
                    </div>
                </div>

                @include('guru.partials.multi-kelas-selector')

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
</div>
@endsection

@push('scripts')
    @vite(['resources/js/guru/lms/tugas/edit.js'])
@endpush
