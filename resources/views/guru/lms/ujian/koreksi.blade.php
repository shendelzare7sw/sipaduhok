@extends('layouts.lms-guru')

@section('title', 'Koreksi Jawaban Siswa')
@section('page-title', 'Koreksi Jawaban: ' . ($ujianSiswa->siswa->nama_lengkap ?? '-'))
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
    <div class="mb-3">
        @php
            $isLatihan = request()->routeIs('guru.lms.latihan.*');
            $backRoute = $isLatihan ? 'guru.lms.latihan.hasil' : 'guru.lms.ujian.hasil';
        @endphp
        <a href="{{ route($backRoute, [$kelas->id, $mapel->id, $ujian->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Hasil
        </a>
    </div>

    {{-- AI Provider Info Badge --}}
    @php
        $currentProvider = \App\Models\AppSetting::where('key', 'ai_provider')->first()?->value ?? 'groq';
        $providerName = $currentProvider === 'groq' ? 'Groq Cloud' : 'Google Gemini';
        $providerIcon = $currentProvider === 'groq' ? 'fa-bolt' : 'fa-google';
        $providerColor = $currentProvider === 'groq' ? 'primary' : 'success';
    @endphp
    <div class="alert alert-{{$providerColor}} alert-dismissible fade show mb-3" role="alert">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <i class="fas fa-robot me-2"></i>
                <strong>AI Grading Assistant:</strong> Menggunakan <span class="fw-bold">{{ $providerName }}</span>
            </div>
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.ai-settings.index') }}" class="btn btn-sm btn-outline-{{$providerColor}}">
                <i class="fas fa-cog me-1"></i> Ubah Provider
            </a>
            @endif
        </div>
    </div>

    <div class="card-custom mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td width="150" class="text-muted">Nama Siswa</td>
                            <td class="fw-bold">: {{ $ujianSiswa->siswa->nama_lengkap }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Judul Ujian</td>
                            <td class="fw-bold">: {{ $ujian->judul_ujian }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless table-sm mb-0">
                        <tr>
                            <td width="150" class="text-muted">Status</td>
                            <td>: 
                                @if($ujianSiswa->status == 'selesai')
                                    <span class="badge bg-success">Selesai</span>
                                @elseif($ujianSiswa->status == 'dinilai')
                                    <span class="badge bg-primary">Sudah Dinilai</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($ujianSiswa->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Nilai Saat Ini</td>
                            <td class="fw-bold fs-5 text-primary">: {{ number_format($ujianSiswa->nilai ?? 0, 1) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route($isLatihan ? 'guru.lms.latihan.koreksi.store' : 'guru.lms.ujian.koreksi.store', [$kelas->id, $mapel->id, $ujian->id, $ujianSiswa->id]) }}" method="POST">
        @csrf
        
        <div class="accordion" id="soalAccordion">
            @foreach($soalList as $index => $soal)
                @php
                    $jawaban = $ujianSiswa->jawabanSiswa->where('soal_ujian_id', $soal->id)->first();
                    $isAutoGraded = !in_array($soal->tipe_soal, ['uraian', 'essay', 'isian_singkat']);
                    $bgColor = $isAutoGraded ? 'bg-light' : 'bg-white border-warning';
                    if (!$isAutoGraded && $jawaban && $jawaban->nilai_soal === null) {
                        $bgColor = 'bg-warning bg-opacity-10 border-warning'; // Highlight un-graded manual questions
                    }
                @endphp

                <div class="card mb-3 {{ $bgColor }}" style="border-left: 4px solid {{ $isAutoGraded ? '#165fac' : '#f59e0b' }};">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold">Soal No. {{ $index + 1 }} <span class="badge bg-secondary ms-2">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span></h6>
                            <span class="badge bg-info text-dark">Bobot: {{ $soal->bobot_nilai }}</span>
                        </div>
                        
                            @if($soal->narasi)
                                <div class="alert alert-secondary mb-3">
                                    <strong class="d-block mb-1"><i class="fas fa-book-open me-2"></i>Narasi / Konteks:</strong>
                                    <div class="fst-italic text-dark">{!! nl2br(e($soal->narasi)) !!}</div>
                                </div>
                            @endif

                            <div class="mb-3 p-3 bg-white border rounded question-text">
                                {!! nl2br(e($soal->pertanyaan)) !!}
                            </div>

                        @if($isAutoGraded)
                            {{-- Tampilan Auto Graded (Read Only) --}}
                            <div class="mb-3">
                                <label class="small text-muted fw-bold mb-1">Jawaban Siswa:</label>
                                <div class="p-3 border rounded student-answer-box">
                                    @if($soal->tipe_soal == 'pilihan_ganda')
                                        {{ $jawaban->jawaban ?? '-' }} 
                                        @if(isset($jawaban) && $soal->checkAnswer($jawaban->jawaban))
                                            <i class="fas fa-check-circle text-success ms-2"></i>
                                        @elseif(isset($jawaban))
                                            <i class="fas fa-times-circle text-danger ms-2"></i> (Kunci: {{ $soal->jawaban_benar }})
                                        @else
                                            <span class="text-muted fst-italic">(Tidak dijawab)</span>
                                        @endif
                                    @else
                                        {{-- Simplifikasi tampilan untuk tipe lain --}}
                                        {{ is_array($jawaban->jawaban ?? null) ? json_encode($jawaban->jawaban) : ($jawaban->jawaban ?? '-') }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="small text-muted fw-bold">Nilai Otomatis:</label>
                                    <input type="text" class="form-control form-control-lg-custom" value="{{ $jawaban->nilai_soal ?? 0 }}" readonly>
                                </div>
                            </div>

                        @else
                            {{-- Tampilan Manual Grading --}}
                            <div class="mb-3">
                                <label class="small text-muted fw-bold mb-1">Jawaban Siswa:</label>
                                <div class="p-3 border rounded student-answer-box mb-3" style="min-height: 80px;">
                                    @if(isset($jawaban->jawaban) && $jawaban->jawaban)
                                        {!! nl2br(e($jawaban->jawaban)) !!}
                                    @else
                                        <span class="text-muted fst-italic">Siswa tidak menjawab soal ini.</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold text-primary">Berikan Nilai (Max: {{ $soal->bobot_nilai }})</label>
                                    <input type="number" step="0.1" min="0" max="{{ $soal->bobot_nilai }}" 
                                        name="nilai[{{ $soal->id }}]" 
                                        id="nilai_{{ $soal->id }}"
                                        class="form-control form-control-lg-custom" 
                                        value="{{ $jawaban->nilai_soal ?? 0 }}" required>
                                </div>
                                <div class="col-md-9">
                                    <div class="d-flex justify-content-between mb-2">
                                        <label class="form-label fw-bold text-secondary align-self-end">Feedback / Komentar Guru (Opsional)</label>
                                        <button type="button" class="btn btn-ai-gradient ai-assist-btn px-4" 
                                            data-soal-id="{{ $soal->id }}" 
                                            data-answer="{{ $jawaban->jawaban ?? '' }}"
                                            data-max-score="{{ $soal->bobot_nilai }}">
                                            <i class="fas fa-robot me-2"></i> Analisis AI Assistant
                                        </button>
                                    </div>
                                    <textarea name="feedback[{{ $soal->id }}]" 
                                        id="feedback_{{ $soal->id }}"
                                        class="form-control form-control-lg-custom" 
                                        rows="3"
                                        placeholder="Berikan catatan koreksi...">{{ $jawaban->feedback ?? '' }}</textarea>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="card-custom sticky-bottom bg-white border-top shadow-lg p-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted small">Pastikan semua soal uraian/essay telah dinilai sebelum menyimpan.</span>
                </div>
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i> Simpan Hasil Koreksi
                </button>
            </div>
        </div>
    </form>

    <!-- Toast for AI Result -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
        <div id="aiToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-robot text-primary rounded me-2"></i>
                <strong class="me-auto">AI Assistant</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="aiToastMessage">
                Sedang menganalisis jawaban...
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .question-text {
            font-size: 1.1rem;
            line-height: 1.6;
            color: #2c3e50;
        }
        .student-answer-box {
            font-size: 1.05rem;
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
        }
        .btn-ai-gradient {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            border: none;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3);
            transition: all 0.3s ease;
        }
        .btn-ai-gradient:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 6px rgba(99, 102, 241, 0.4);
            color: white;
        }
        .form-control-lg-custom {
            font-size: 1.1rem;
            padding: 0.6rem 1rem;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const aiButtons = document.querySelectorAll('.ai-assist-btn');
            const toastEl = document.getElementById('aiToast');
            const toast = new bootstrap.Toast(toastEl);
            const toastMsg = document.getElementById('aiToastMessage');
            const processingButtons = new Set(); // Race condition protection

            aiButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    const soalId = this.dataset.soalId;

                    // Prevent multiple simultaneous requests for same button
                    if (processingButtons.has(soalId)) return;

                    const answer = this.dataset.answer;
                    const maxScore = parseFloat(this.dataset.maxScore);

                    if (!answer || answer === '-') {
                        toastMsg.textContent = 'Belum ada jawaban siswa untuk dianalisis.';
                        toast.show();
                        return;
                    }

                    processingButtons.add(soalId);

                    // UI Loading State
                    const originalContent = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengolah...';
                    this.disabled = true;

                    // Determine route based on context
                    const isLatihan = {{ request()->routeIs('guru.lms.latihan.*') ? 'true' : 'false' }};

                    // Use route helper with placeholder for dynamic soalId
                    @php
                        $routeName = request()->routeIs('guru.lms.latihan.*') ? 'guru.lms.latihan.koreksi.ai-suggest' : 'guru.lms.ujian.koreksi.ai-suggest';
                        $urlTemplate = route($routeName, [$kelas->id, $mapel->id, $ujian->id, 'SOAL_ID_PLACEHOLDER']);
                    @endphp
                    const url = "{{ $urlTemplate }}".replace('SOAL_ID_PLACEHOLDER', soalId);

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ answer: answer })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            throw new Error(data.feedback || 'Terjadi kesalahan pada AI.');
                        }

                        // Populate inputs
                        const scoreInput = document.getElementById(`nilai_${soalId}`);
                        const feedbackInput = document.getElementById(`feedback_${soalId}`);

                        // Animate changed values
                        scoreInput.value = data.score;
                        scoreInput.classList.add('bg-success', 'text-white', 'bg-opacity-25');

                        // Prepend AI feedback (consistent with tugas behavior)
                        const existingFeedback = feedbackInput.value.trim();
                        feedbackInput.value = `[AI Suggestion] ${data.feedback}${existingFeedback ? '\n\n' + existingFeedback : ''}`;
                        feedbackInput.classList.add('bg-info', 'text-white', 'bg-opacity-10');

                        setTimeout(() => {
                            scoreInput.classList.remove('bg-success', 'text-white', 'bg-opacity-25');
                            feedbackInput.classList.remove('bg-info', 'text-white', 'bg-opacity-10');
                        }, 2000);

                        toastMsg.textContent = `Analisis selesai! Saran skor: ${data.score}`;
                        toast.show();
                    })
                    .catch(error => {
                        console.error(error);
                        toastMsg.textContent = "Gagal: " + error.message;
                        toast.show();
                    })
                    .finally(() => {
                        this.innerHTML = originalContent;
                        this.disabled = false;
                        processingButtons.delete(soalId);
                    });
                });
            });
        });
    </script>
    @endpush
@endsection
