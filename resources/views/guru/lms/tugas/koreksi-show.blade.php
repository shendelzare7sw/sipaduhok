@extends('layouts.lms-guru')

@section('title', 'Koreksi Jawaban')
@section('page-title', 'Koreksi Jawaban Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/tugas/koreksi-show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/guru/lms/tugas/koreksi-show.js'])
@endpush

@section('content')
    <div class="guru-lms-tugas-koreksi-show-page"
        data-ai-suggest-url="{{ route('guru.lms.tugas.koreksi.ai-suggest', [$kelas->id, $mapel->id, $tugas->id, $tugasSiswa->id]) }}"
        data-csrf-token="{{ csrf_token() }}"
        data-has-answer="{{ ($tugasSiswa->jawaban_text || $tugasSiswa->file_jawaban) ? 'true' : 'false' }}">
    <div class="mb-3">
        <a href="{{ route('guru.lms.tugas.koreksi', [$kelas->id, $mapel->id, $tugasSiswa->tugas_id]) }}"
           class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar Koreksi
        </a>
    </div>



    <div class="row">
        <div class="col-lg-8">
            {{-- Info Tugas --}}
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <i class="fas fa-file-alt me-2"></i>Tugas: {{ $tugas->judul_tugas }}
                </div>
                <div class="p-3">
                    <p class="mb-2"><strong>Deskripsi:</strong></p>
                    <p class="text-muted">{{ $tugas->deskripsi }}</p>
                    @if($tugas->file_tugas)
                        <div class="mt-3">
                            <strong>File Soal:</strong>
                            <x-file-preview :path="$tugas->file_tugas" label="Lihat Soal" />
                        </div>
                    @endif
                </div>
            </div>

            {{-- Jawaban Siswa --}}
            <div class="card-custom">
                <div class="card-header-custom">
                    <i class="fas fa-pen me-2"></i>Jawaban Siswa
                </div>
                <div class="p-3">
                    @if($tugasSiswa->jawaban_text)
                        <div class="mb-3">
                            <strong>Jawaban Teks:</strong>
                            <div class="p-3 bg-light rounded mt-2 answer-text-box">{{ $tugasSiswa->jawaban_text }}</div>
                        </div>
                    @endif

                    @if($tugasSiswa->file_jawaban)
                        <div class="mt-3">
                            <strong>File Jawaban:</strong>
                            <x-file-preview :path="$tugasSiswa->file_jawaban" label="Lihat Jawaban Siswa" />
                        </div>
                    @endif

                    @if(!$tugasSiswa->jawaban_text && !$tugasSiswa->file_jawaban)
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-inbox empty-answer-icon"></i>
                            <p class="mt-2">Tidak ada jawaban</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Info Siswa --}}
            <div class="card-custom mb-3">
                <div class="card-header-custom">
                    <i class="fas fa-user me-2"></i>Info Siswa
                </div>
                <div class="p-3">
                    <p class="mb-1"><strong>Nama:</strong></p>
                    <p class="mb-2">{{ $tugasSiswa->siswa->nama_lengkap }}</p>
                    
                    <p class="mb-1"><strong>NISN:</strong></p>
                    <p class="mb-2">{{ $tugasSiswa->siswa->nisn }}</p>
                    
                    <p class="mb-1"><strong>Waktu Kumpul:</strong></p>
                    <p class="mb-2">
                        {{ $tugasSiswa->tanggal_submit ? $tugasSiswa->tanggal_submit->format('d M Y H:i') : '-' }}
                        @if($tugasSiswa->isLate())
                            <br><span class="badge bg-warning text-dark">Terlambat</span>
                        @endif
                    </p>
                    
                    <p class="mb-1"><strong>Status:</strong></p>
                    <p>
                        @if($tugasSiswa->status == 'dikerjakan')
                            <span class="badge bg-warning">Perlu Dinilai</span>
                        @elseif($tugasSiswa->status == 'dinilai')
                            <span class="badge bg-success">Sudah Dinilai</span>
                        @endif
                    </p>
                </div>
            </div>

            {{-- Form Penilaian --}}
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-star me-2"></i>Berikan Nilai
                    </div>
                    @if($tugasSiswa->file_jawaban || $tugasSiswa->jawaban_text)
                        <button type="button" class="btn btn-sm btn-ai-gradient" id="aiAssistBtn">
                            <i class="fas fa-robot me-1"></i> Analisis AI
                        </button>
                    @endif
                </div>
                @if($tugasSiswa->file_jawaban || $tugasSiswa->jawaban_text)
                    <div class="px-3 pt-2 pb-1 bg-light border-bottom">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            AI dapat menganalisis: <strong>Gambar (JPG/PNG)</strong>, <strong>PDF (Digital & Scan)</strong>, dan <strong>Teks</strong>
                        </small>
                    </div>
                @endif
                <div class="p-3">
                    <form action="{{ route('guru.lms.tugas.koreksi.store', [$kelas->id, $mapel->id, $tugas->id, $tugasSiswa->id]) }}" 
                          method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nilai (0-100) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" id="nilaiInput" class="form-control @error('nilai') is-invalid @enderror" 
                                   value="{{ old('nilai', $tugasSiswa->nilai) }}" 
                                   min="0" max="100" required>
                            @error('nilai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Feedback untuk Siswa</label>
                            <textarea name="feedback_guru" id="feedbackInput" class="form-control" rows="4">{{ old('feedback_guru', $tugasSiswa->feedback_guru) }}</textarea>
                            <small class="text-muted">Berikan komentar atau saran untuk siswa</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Simpan Nilai
                        </button>
                    </form>

                    @if($tugasSiswa->status == 'dinilai')
                        <div class="alert alert-success mt-3 mb-0">
                            <i class="fas fa-check-circle me-1"></i>
                            Tugas sudah dinilai
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toast for AI Result -->
    <div class="position-fixed bottom-0 end-0 p-3 ai-toast-container">
        <div id="aiToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-robot text-primary rounded me-2"></i>
                <strong class="me-auto">AI Assistant</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="aiToastMessage">
                Sedang menganalisis jawaban (Vision AI)...
            </div>
        </div>
    </div>
    </div>
@endsection
