@extends('layouts.lms-guru')

@section('title', 'Buat Pertemuan Baru')
@section('page-title', 'Buat Pertemuan Baru')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Back Button -->
            <a href="{{ route('guru.lms.dashboard', [$kelas->id, $mapel->id]) }}"
                class="btn btn-outline-secondary btn-sm mb-3">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
            </a>

            <!-- Form Card -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-plus me-2"></i>Buat Pertemuan Baru
                    </h5>
                </div>
                <div class="p-4">
                    <form action="{{ route('guru.lms.pertemuan.store', [$kelas->id, $mapel->id]) }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Pekan Ke <span class="text-danger">*</span></label>
                            <select name="pekan" class="form-select @error('pekan') is-invalid @enderror" required>
                                @for($i = 1; $i <= 20; $i++)
                                    <option value="{{ $i }}" {{ old('pekan') == $i ? 'selected' : '' }}>
                                        Pekan {{ $i }}
                                    </option>
                                @endfor
                            </select>
                            @error('pekan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Judul Pertemuan <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror"
                                value="{{ old('judul') }}" placeholder="Contoh: Pengenalan Materi Dasar" required>
                            @error('judul')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tanggal Pertemuan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control @error('tanggal') is-invalid @enderror"
                                value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Deskripsi (Opsional)</label>
                            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                rows="4"
                                placeholder="Deskripsi singkat tentang pertemuan ini...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Link Meeting (Opsional)</label>
                            <input type="url" name="zoom_link" class="form-control @error('zoom_link') is-invalid @enderror"
                                value="{{ old('zoom_link') }}"
                                placeholder="https://zoom.us/j/... atau https://meet.google.com/...">
                            <small class="text-muted">Link Zoom, Google Meet, atau platform lainnya</small>
                            @error('zoom_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="fas fa-save me-2"></i>Simpan Pertemuan
                            </button>
                            <a href="{{ route('guru.lms.dashboard', [$kelas->id, $mapel->id]) }}"
                                class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Card -->
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Tips:</strong> Setelah pertemuan dibuat, Anda dapat menambahkan Modul, Materi, Tugas, Kuis, dan
                Forum di halaman detail pertemuan.
            </div>
        </div>
    </div>
@endsection