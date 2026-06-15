@extends('layouts.lms-guru')

@php
    $isLatihan = request()->routeIs('guru.lms.latihan.*');
    $tipeLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $aiRouteName = $isLatihan ? 'guru.lms.latihan.koreksi.ai-suggest' : 'guru.lms.ujian.koreksi.ai-suggest';
    $aiUrlTemplate = route($aiRouteName, [$kelas->id, $mapel->id, $ujian->id, 'SOAL_ID_PLACEHOLDER']);
@endphp

@section('title', 'Koreksi Jawaban Siswa')
@section('page-title', 'Koreksi Jawaban: ' . ($ujianSiswa->siswa->nama_lengkap ?? '-'))
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/ujian/koreksi.css'])
@endpush

@push('scripts')
    @vite(['resources/js/guru/lms/ujian/koreksi.js'])
@endpush

@section('content')
    <div class="guru-lms-ujian-koreksi-page"
        data-ai-url-template="{{ $aiUrlTemplate }}"
        data-csrf-token="{{ csrf_token() }}">
    <div class="mb-3">
        @php
            $backRoute = $isLatihan ? 'guru.lms.latihan.hasil' : 'guru.lms.ujian.hasil';
        @endphp
        <a href="{{ route($backRoute, [$kelas->id, $mapel->id, $ujian->id]) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Hasil
        </a>
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
                            <td class="text-muted">Judul {{ $tipeLabel }}</td>
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
                            <td class="fw-bold fs-5 text-primary">: {{ number_format($ujianSiswa->nilai ?? 0, 1) }}/100</td>
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
                    $cardClass = $isAutoGraded ? 'bg-light koreksi-question-card' : 'bg-white border-warning koreksi-question-card is-manual';
                    if (!$isAutoGraded && $jawaban && $jawaban->nilai_soal === null) {
                        $cardClass = 'bg-warning bg-opacity-10 border-warning koreksi-question-card is-manual';
                    }
                @endphp

                <div class="card mb-3 {{ $cardClass }}">
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
                            {{-- Tampilan Auto Graded (Bisa di-override guru) --}}
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
                                    @elseif($soal->tipe_soal == 'pilihan_ganda_kompleks')
                                        @php
                                            $ansArray = [];
                                            if (isset($jawaban->jawaban) && $jawaban->jawaban) {
                                                $ansArray = is_array($jawaban->jawaban) ? $jawaban->jawaban : (json_decode($jawaban->jawaban, true) ?? []);
                                            }
                                        @endphp
                                        @if(empty($ansArray))
                                            <span class="text-muted fst-italic">(Tidak dijawab)</span>
                                        @else
                                            <strong>{{ implode(', ', $ansArray) }}</strong>
                                        @endif
                                    @else
                                        {{-- Simplifikasi tampilan untuk tipe lain --}}
                                        @php
                                            $decoded = null;
                                            if (isset($jawaban->jawaban)) {
                                                $decoded = is_array($jawaban->jawaban) ? $jawaban->jawaban : json_decode($jawaban->jawaban, true);
                                            }
                                        @endphp
                                        {{ is_array($decoded) ? implode(', ', $decoded) : ($jawaban->jawaban ?? '-') }}
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small text-muted fw-bold">Nilai Otomatis (Bisa Diubah):</label>
                                    <input type="number" step="any" min="0" max="{{ $soal->bobot_nilai }}" name="nilai[{{ $soal->id }}]" class="form-control form-control-lg-custom border-primary" value="{{ $jawaban->nilai_soal ?? 0 }}">
                                </div>
                            </div>

                        @else
                            {{-- Tampilan Manual Grading --}}
                            <div class="mb-3">
                                <label class="small text-muted fw-bold mb-1">Jawaban Siswa:</label>
                                <div class="p-3 border rounded student-answer-box student-answer-box-min mb-3">
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
                                    <input type="number" step="any" min="0" max="{{ $soal->bobot_nilai }}" 
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
    <div class="position-fixed bottom-0 end-0 p-3 ai-toast-container">
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
    </div>
@endsection
