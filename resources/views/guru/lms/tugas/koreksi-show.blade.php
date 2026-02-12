@extends('layouts.lms-guru')

@section('title', 'Koreksi Jawaban')
@section('page-title', 'Koreksi Jawaban Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
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
                            <div class="p-3 bg-light rounded mt-2" style="white-space: pre-wrap;">{{ $tugasSiswa->jawaban_text }}</div>
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
                            <i class="fas fa-inbox" style="font-size: 48px; opacity: 0.2;"></i>
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
                    
                    <p class="mb-1"><strong>Waktu Submit:</strong></p>
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
                        <button type="button" class="btn btn-sm btn-outline-info" id="aiAssistBtn">
                            <i class="fas fa-robot me-1"></i> Analisis AI (Vision)
                        </button>
                    @endif
                </div>
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
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
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

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aiBtn = document.getElementById('aiAssistBtn');
            if (aiBtn) {
                const toastEl = document.getElementById('aiToast');
                const toast = new bootstrap.Toast(toastEl);
                const toastMsg = document.getElementById('aiToastMessage');

                aiBtn.addEventListener('click', function() {
                    // UI Loading State
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Analyzing...';
                    this.disabled = true;
                    toastMsg.textContent = "Sedang menganalisis jawaban (Vision AI)...";
                    toast.show();

                    // URL Construction
                    const url = "{{ route('guru.lms.tugas.koreksi.ai-suggest', [$kelas->id, $mapel->id, $tugas->id, $tugasSiswa->id]) }}";

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({}) // Empty body is fine, controller reads DB
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.feedback || 'Terjadi kesalahan pada AI.');
                        }

                        // Populate inputs
                        const scoreInput = document.getElementById('nilaiInput');
                        const feedbackInput = document.getElementById('feedbackInput');

                        scoreInput.value = data.score;
                        scoreInput.classList.add('bg-success', 'text-white', 'bg-opacity-25');
                        
                        feedbackInput.value = `[AI Vision] ${data.feedback}\n\n` + feedbackInput.value;
                        feedbackInput.classList.add('bg-info', 'text-white', 'bg-opacity-10');

                        setTimeout(() => {
                            scoreInput.classList.remove('bg-success', 'text-white', 'bg-opacity-25');
                            feedbackInput.classList.remove('bg-info', 'text-white', 'bg-opacity-10');
                        }, 2000);

                        toastMsg.textContent = `Analisis selesai! Saran skor: ${data.score}`;
                    })
                    .catch(error => {
                        console.error(error);
                        alert('Gagal mengambil analisis AI: ' + error.message);
                        toastMsg.textContent = "Gagal: " + error.message;
                    })
                    .finally(() => {
                        this.innerHTML = originalContent;
                        this.disabled = false;
                    });
                });
            }
        });
    </script>
    @endpush
@endsection