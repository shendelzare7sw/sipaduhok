@extends('layouts.lms')

@section('title', 'Buat Pertanyaan - ' . $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Buat Pertanyaan Baru')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/forum/create.css'])
@endpush

@section('content')
<div class="siswa-lms-mapel-forum-create-page">
<!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard LMS
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">
                <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}">
                <i class="fas fa-comments"></i> Forum
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item active">
            <i class="fas fa-edit"></i> Buat Pertanyaan
        </div>
    </div>

    <div class="form-card">
        <h4 class="form-heading">
            <i class="fas fa-question-circle me-2"></i>Buat Pertanyaan Baru
        </h4>

        <form action="{{ route('siswa.lms.mapel.forum.store', $mataPelajaran->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">Topik <span class="text-danger">*</span></label>
                <select name="topik" class="form-select @error('topik') is-invalid @enderror" required>
                    <option value="">-- Pilih Topik --</option>
                    <option value="materi" {{ old('topik') == 'materi' ? 'selected' : '' }}>Materi Pembelajaran</option>
                    <option value="tugas" {{ old('topik') == 'tugas' ? 'selected' : '' }}>Tugas & Latihan</option>
                    <option value="ujian" {{ old('topik') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                    <option value="konsultasi" {{ old('topik') == 'konsultasi' ? 'selected' : '' }}>Konsultasi Umum</option>
                    <option value="lainnya" {{ old('topik') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('topik')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Judul Pertanyaan <span class="text-danger">*</span></label>
                <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                    value="{{ old('judul') }}" placeholder="Tulis judul pertanyaan yang jelas dan spesifik..." required>
                @error('judul')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Isi Pertanyaan <span class="text-danger">*</span></label>
                <textarea name="isi" class="form-control @error('isi') is-invalid @enderror" rows="8"
                    placeholder="Jelaskan pertanyaan Anda secara detail. Semakin jelas pertanyaannya, semakin mudah guru menjawab..."
                    required>{{ old('isi') }}</textarea>
                @error('isi')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="form-text text-muted">Minimal 10 karakter</small>
            </div>

            <div class="alert alert-info" role="alert">
                <i class="fas fa-lightbulb me-2"></i>
                <strong>Tips:</strong> Pertanyaan yang baik adalah pertanyaan yang spesifik dan jelas.
                Sertakan konteks seperti bab/materi yang dimaksud agar guru dapat menjawab dengan tepat.
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Pertanyaan
                </button>
                <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Kembali
                </a>
            </div>
        </form>
    </div>

</div>
@endsection