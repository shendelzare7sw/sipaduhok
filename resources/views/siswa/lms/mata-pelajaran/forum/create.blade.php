@extends('layouts.lms')

@section('title', 'Buat Pertanyaan - ' . $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Buat Pertanyaan Baru')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .form-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }
    </style>

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" style="margin-bottom: 20px;">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}">Dashboard LMS</a></li>
            <li class="breadcrumb-item"><a
                    href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">{{ $mataPelajaran->nama_mapel }}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}">Forum</a>
            </li>
            <li class="breadcrumb-item active">Buat Pertanyaan</li>
        </ol>
    </nav>

    <div class="form-card">
        <h4 style="color: var(--primary); margin-bottom: 24px;">
            <i class="fas fa-question-circle me-2"></i>Buat Pertanyaan Baru
        </h4>

        <form action="{{ route('siswa.lms.mapel.forum.store', $mataPelajaran->id) }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="form-label fw-bold">Topik <span class="text-danger">*</span></label>
                <select name="topik" class="form-select @error('topik') is-invalid @enderror" required>
                    <option value="">-- Pilih Topik --</option>
                    <option value="materi" {{ old('topik') == 'materi' ? 'selected' : '' }}>📚 Materi Pembelajaran</option>
                    <option value="tugas" {{ old('topik') == 'tugas' ? 'selected' : '' }}>📝 Tugas & Latihan</option>
                    <option value="ujian" {{ old('topik') == 'ujian' ? 'selected' : '' }}>📋 Ujian</option>
                    <option value="konsultasi" {{ old('topik') == 'konsultasi' ? 'selected' : '' }}>💬 Konsultasi Umum
                    </option>
                    <option value="lainnya" {{ old('topik') == 'lainnya' ? 'selected' : '' }}>📌 Lainnya</option>
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

@endsection