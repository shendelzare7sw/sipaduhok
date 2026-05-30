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
    <style>
        .btn-ai-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.4), 0 2px 4px -1px rgba(99, 102, 241, 0.2);
            transition: all 0.3s ease;
        }
        .btn-ai-gradient:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.5), 0 4px 6px -2px rgba(99, 102, 241, 0.3);
            color: white;
        }
        .btn-ai-gradient:active {
            transform: translateY(0);
        }
        .btn-ai-gradient:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }
        .btn-ai-loading {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%) !important;
            animation: pulse-glow 1.5s ease-in-out infinite;
            pointer-events: none;
        }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 2px 8px rgba(99, 102, 241, 0.4); }
            50% { box-shadow: 0 4px 20px rgba(139, 92, 246, 0.7); }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aiBtn = document.getElementById('aiAssistBtn');
            if (aiBtn) {
                const toastEl = document.getElementById('aiToast');
                const toast = new bootstrap.Toast(toastEl);
                const toastMsg = document.getElementById('aiToastMessage');
                let isProcessing = false; // Race condition protection

                aiBtn.addEventListener('click', function() {
                    // Prevent multiple simultaneous requests
                    if (isProcessing) return;

                    // Validate if student has submitted answer
                    const hasAnswer = {{ ($tugasSiswa->jawaban_text || $tugasSiswa->file_jawaban) ? 'true' : 'false' }};
                    if (!hasAnswer) {
                        toastMsg.innerHTML = '<i class="fas fa-exclamation-circle text-warning me-1"></i> Belum ada jawaban siswa untuk dianalisis.';
                        toast.show();
                        return;
                    }

                    isProcessing = true;

                    // UI Loading State with live timer
                    const originalContent = this.innerHTML;
                    let seconds = 0;
                    const btnRef = this;
                    btnRef.disabled = true;
                    btnRef.classList.add('btn-ai-loading');

                    const updateTimer = () => {
                        btnRef.innerHTML = `<i class="fas fa-spinner fa-spin me-1"></i> Menganalisis... <span class="badge bg-light text-dark ms-1">${seconds}s</span>`;
                        toastMsg.innerHTML = `<i class="fas fa-spinner fa-spin text-primary me-1"></i> Sedang menganalisis jawaban (Vision AI)... <strong>${seconds}s</strong>`;
                    };
                    updateTimer();
                    toast.show();
                    
                    const timerInterval = setInterval(() => {
                        seconds++;
                        updateTimer();
                    }, 1000);

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
                        // Visual Feedback
                        scoreInput.classList.add('bg-success', 'text-white', 'bg-opacity-25');
                        
                        feedbackInput.value = `[AI Suggestion] ${data.feedback}\n\n` + feedbackInput.value;
                        feedbackInput.classList.add('bg-info', 'text-white', 'bg-opacity-10');

                        setTimeout(() => {
                            scoreInput.classList.remove('bg-success', 'text-white', 'bg-opacity-25');
                            scoreInput.classList.add('transition-fade'); // smooth remove if added css for it
                            feedbackInput.classList.remove('bg-info', 'text-white', 'bg-opacity-10');
                        }, 2000);

                        toastMsg.innerHTML = `<i class="fas fa-check-circle text-success me-1"></i> Analisis selesai dalam <strong>${seconds}s</strong>! Saran skor: <strong>${data.score}</strong>`;
                    })
                    .catch(error => {
                        console.error(error);
                        toastMsg.innerHTML = `<i class="fas fa-exclamation-triangle text-danger me-1"></i> Gagal (${seconds}s): ${error.message}`;
                        toast.show();
                    })
                    .finally(() => {
                        clearInterval(timerInterval);
                        btnRef.innerHTML = originalContent;
                        btnRef.disabled = false;
                        btnRef.classList.remove('btn-ai-loading');
                        isProcessing = false;
                    });
                });
            }
        });
    </script>
    @endpush
@endsection
