@extends('layouts.lms-guru')

@section('title', 'Buat Tugas')
@section('page-title', 'Buat Tugas Baru')
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
            <i class="fas fa-plus-circle me-2"></i>Form Buat Tugas
        </div>
        <div class="p-4">
            <form action="{{ route('guru.lms.tugas.store', [$kelas->id, $mapel->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Judul Tugas <span class="text-danger">*</span></label>
                    <input type="text" name="judul_tugas" class="form-control @error('judul_tugas') is-invalid @enderror"
                        value="{{ old('judul_tugas') }}" required>
                    @error('judul_tugas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi / Instruksi <span class="text-danger">*</span></label>
                    <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="5"
                        required>{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_mulai"
                            class="form-control @error('tanggal_mulai') is-invalid @enderror"
                            value="{{ old('tanggal_mulai', date('Y-m-d')) }}" required>
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Deadline <span class="text-danger">*</span></label>
                        <input type="date" name="tanggal_deadline"
                            class="form-control @error('tanggal_deadline') is-invalid @enderror"
                            value="{{ old('tanggal_deadline') }}" required>
                        @error('tanggal_deadline')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">File Tugas (Optional)</label>
                    <input type="file" name="file_tugas" class="form-control">
                    <small class="text-muted">Upload soal dalam bentuk file jika diperlukan (Max 10MB)</small>
                </div>

                <div class="mb-4">
                    <h5 class="form-label fw-bold border-bottom pb-2">Pengaturan Penilaian & Pengulangan</h5>
                    <div class="p-3 border rounded bg-light mb-3">
                        <!-- Tampilkan Nilai -->
                        <div class="form-check form-switch mb-1">
                            <input class="form-check-input" type="checkbox" role="switch" id="tampilkanNilai" name="tampilkan_nilai" value="1" checked>
                            <label class="form-check-label fw-bold" for="tampilkanNilai">Tampilkan Nilai ke Siswa</label>
                        </div>
                        <small class="text-muted d-block mt-1">Jika dinonaktifkan, siswa tidak akan bisa melihat nilai tugasnya meskipun sudah dinilai oleh guru.</small>
                    </div>

                    <div class="p-3 border rounded bg-light">
                        <!-- Pengulangan (Edit) -->
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" role="switch" id="bisaDiulang" name="bisa_diulang" value="1" onchange="toggleBatasPengulangan()">
                            <label class="form-check-label fw-bold" for="bisaDiulang">Izinkan Siswa Mengedit Jawaban</label>
                        </div>
                        <small class="text-muted d-block mb-3">Jika diaktifkan, siswa dapat mengubah jawabannya sebelum deadline.</small>

                        <div id="batasPengulanganContainer" style="display: none; padding-left: 2.5rem;">
                            <label for="batasPengulangan" class="form-label fw-semibold">Batas Edit (Kali)</label>
                            <input type="number" class="form-control" id="batasPengulangan" name="batas_pengulangan" min="0" placeholder="Kosongkan jika tak terbatas" style="max-width: 200px;">
                            <small class="text-muted d-block mt-1">Biarkan kosong agar siswa bisa mengedit tanpa batas (selama belum deadline).</small>
                        </div>
                    </div>
                </div>

                <script>
                    function toggleBatasPengulangan() {
                        const isChecked = document.getElementById('bisaDiulang').checked;
                        const container = document.getElementById('batasPengulanganContainer');
                        const input = document.getElementById('batasPengulangan');
                        
                        if (isChecked) {
                            container.style.display = 'block';
                            if (!input.value) {
                                input.value = 2; // Default 2 kali
                            }
                        } else {
                            container.style.display = 'none';
                        }
                    }
                    // Run on load to set initial state
                    document.addEventListener('DOMContentLoaded', toggleBatasPengulangan);
                </script>

                @include('guru.partials.multi-kelas-selector')

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Buat Tugas
                    </button>
                    <a href="{{ route('guru.lms.tugas.index', [$kelas->id, $mapel->id]) }}"
                        class="btn btn-secondary">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
