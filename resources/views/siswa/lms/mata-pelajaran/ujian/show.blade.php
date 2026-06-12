@php
    $layout = ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan') ? 'layouts.lms-ujian' : 'layouts.lms';
    $isLatihan = request()->routeIs('siswa.lms.mapel.latihan.*') || (isset($ujian) && $ujian->tipe_ujian === 'latihan');
    $routePrefix = $isLatihan ? 'siswa.lms.mapel.latihan.' : 'siswa.lms.mapel.ujian.';
    $existingAnswers = $existingAnswers ?? [];
    $soalList = $soalList ?? collect();
    $questionMeta = $soalList->values()->map(fn ($soal, $index) => [
        'soal_id' => $soal->id,
        'nomor_soal' => $index + 1,
    ])->all();
    $answersState = $soalList->map(function ($soal) use ($existingAnswers) {
        if (!isset($existingAnswers[$soal->id])) {
            return false;
        }

        $answer = $existingAnswers[$soal->id];

        if (!is_string($answer)) {
            return false;
        }

        $decoded = json_decode($answer, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            if ($soal->tipe_soal === 'pilihan_ganda_kompleks') {
                return count($decoded) > 0;
            }

            if ($soal->tipe_soal === 'benar_salah') {
                return count($decoded) > 0 && !in_array(null, $decoded, true);
            }

            return count($decoded) > 0;
        }

        return trim($answer) !== '' && trim($answer) !== '-';
    })->values()->all();
    $encodedQuestionMeta = base64_encode(json_encode($questionMeta));
    $encodedAnswersState = base64_encode(json_encode($answersState));
@endphp

@extends($layout)

@section('title', $ujian->judul_ujian)

{{-- Section for Standard Layout (Start/Result screens) --}}
@if(!isset($ujianSiswa) || $ujianSiswa->status !== 'sedang_mengerjakan')
    @section('page-title', $mataPelajaran->nama_mapel)
    @section('page-subtitle', 'Ujian ' . ucwords(str_replace('_', ' ', $ujian->tipe_ujian)))
    @section('sidebar-menu')
        @include('siswa.partials.sidebar-lms')
    @endsection
@endif

@push('styles')
    @vite(['resources/css/siswa/lms/mata-pelajaran/ujian/show.css'])
@endpush

@push('scripts')
    @vite(['resources/js/siswa/lms/mata-pelajaran/ujian/show.js'])
@endpush

@section('content')

@if(!$ujianSiswa || $ujianSiswa->status !== 'sedang_mengerjakan')
    {{-- LAYOUT 1: START SCREEN / RESULT SCREEN --}}
    <div class="siswa-lms-ujian-show-page">
        <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($ujianSiswa && in_array($ujianSiswa->status, ['selesai', 'dinilai']))
                     <!-- RESULT SCREEN -->
                     <div class="ujian-card text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3 class="text-success mb-2">Ujian Selesai!</h3>
                        <p class="text-muted mb-4">Semua jawaban Anda telah tersimpan.</p>

                        <div class="alert alert-light border">
                            <p class="mb-1 small text-muted">Diselesaikan pada:</p>
                            <strong>{{ $ujianSiswa->waktu_selesai->format('d F Y, H:i') }} WIB</strong>
                        </div>

                        @if($ujian->tampilkan_nilai)
                            @if($ujianSiswa->nilai !== null)
                                <div class="my-4">
                                    <h1 class="display-4 fw-bold text-primary">{{ number_format($ujianSiswa->nilai_terbaik ?? $ujianSiswa->nilai, 1) }}/100</h1>
                                    <span class="text-muted">Nilai Terbaik Anda</span>
                                    
                                    @if(($ujianSiswa->pengulangan_ke ?? 1) > 1)
                                    <div class="mt-2 text-muted small">
                                        Nilai Percobaan Terakhir: {{ number_format($ujianSiswa->nilai, 1) }}
                                    </div>
                                    @endif
                                </div>
                            @else
                                <div class="my-4">
                                    <i class="fas fa-hourglass-half fa-3x text-warning mb-2"></i>
                                    <h5 class="text-secondary">Menunggu Penilaian Guru</h5>
                                </div>
                            @endif
                        @else
                            <div class="my-4">
                                <h5 class="text-secondary fw-medium">Terima Kasih Sudah Menyelesaikan {{ ucwords(str_replace('_', ' ', $ujian->tipe_ujian)) }}</h5>
                            </div>
                        @endif

                        <div class="d-flex justify-content-center align-items-center gap-2 mt-4 flex-wrap">
                            @if($ujian->bisa_diulang && $ujian->isOngoing())
                                @php
                                    $sisaPengulangan = $ujian->batas_pengulangan ? max(0, $ujian->batas_pengulangan - (($ujianSiswa->pengulangan_ke ?? 1) - 1)) : null;
                                @endphp
                                @if($sisaPengulangan === null || $sisaPengulangan > 0)
                                    <form id="form-retake" action="{{ route($routePrefix . 'retake', [$mataPelajaran->id, $ujian->id]) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="button" class="btn btn-warning px-4" data-confirm-retake>
                                            <i class="fas fa-redo-alt me-2"></i> Kerjakan Ulang @if($sisaPengulangan !== null) (Sisa: {{ $sisaPengulangan }}) @endif
                                        </button>
                                    </form>
                                @else
                                    <button type="button" class="btn btn-secondary px-4 m-0" disabled>
                                        <i class="fas fa-ban me-2"></i> Pengulangan Habis
                                    </button>
                                @endif
                            @endif
                            @if($ujian->tampilkan_riwayat)
                                <a href="{{ route($routePrefix . 'review', [$mataPelajaran->id, $ujian->id]) }}" class="btn btn-outline-primary px-4 m-0">
                                    <i class="fas fa-search me-2"></i> Lihat Pembahasan
                                </a>
                            @endif
                            <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-primary px-4 m-0">
                                <i class="fas fa-arrow-left me-2"></i> Kembali ke Mata Pelajaran
                            </a>
                        </div>
                     </div>
                @else
                    <!-- START SCREEN -->
                    <div class="ujian-card">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">{{ $ujian->judul_ujian }}</h3>
                            <span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $ujian->tipe_ujian)) }}</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="info-box">
                                    <i class="fas fa-clock text-warning"></i>
                                    <h5>{{ $ujian->durasi_menit == 0 ? 'Tanpa Batas' : $ujian->durasi_menit . ' Menit' }}</h5>
                                    <small class="text-muted">Durasi</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <i class="fas fa-list-ol text-info"></i>
                                    <h5>{{ $soalList->count() }} Soal</h5>
                                    <small class="text-muted">Jumlah Soal</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <i class="fas fa-calendar-alt text-success"></i>
                                    <h5>{{ $ujian->tanggal_mulai->format('d M') }}</h5>
                                    <small class="text-muted">Tanggal</small>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box">
                                    <i class="fas fa-redo-alt text-primary"></i>
                                    @if($ujian->bisa_diulang)
                                        @if($ujian->batas_pengulangan)
                                            <h5>{{ max(0, $ujian->batas_pengulangan - ($ujianSiswa->pengulangan_ke ?? 0)) }} Kali</h5>
                                        @else
                                            <h5>Tak Terbatas</h5>
                                        @endif
                                        <small class="text-muted">Sisa Pengulangan</small>
                                    @else
                                        <h5>1 Kali</h5>
                                        <small class="text-muted">Batas Ujian</small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle me-2"></i>Petunjuk:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Berdoalah sebelum mengerjakan</li>
                                <li>Waktu berjalan otomatis saat tombol "Mulai" diklik</li>
                                <li>Tidak dapat mengulang ujian yang sudah disubmit</li>
                                <li>Pastikan koneksi internet stabil</li>
                            </ul>
                        </div>

                        @if(!$ujian->is_active)
                            <div class="text-center mt-4">
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-lock me-2"></i> Belum Dirilis
                                </button>
                            </div>
                        @elseif($ujian->isOngoing())
                             <div class="text-center mt-4">
                                <form action="{{ route($routePrefix . 'mulai', [$mataPelajaran->id, $ujian->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-play me-2"></i> Mulai Ujian Sekarang
                                    </button>
                                </form>
                            </div>
                        @elseif($ujian->tanggal_mulai->isFuture())
                             <div class="text-center mt-4">
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-hourglass-start me-2"></i> Belum Dimulai
                                </button>
                            </div>
                        @else
                             <div class="text-center mt-4">
                                <button class="btn btn-secondary btn-lg" disabled>
                                    <i class="fas fa-history me-2"></i> Ujian Sudah Berakhir
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
        </div>
    </div>

@else
    {{-- LAYOUT 2: EXAM INTERFACE (FOCUS MODE) - Simple CBT Style --}}
    <div class="siswa-lms-ujian-work-page"
        data-exam-type="{{ $ujian->tipe_ujian }}"
        data-total-questions="{{ $soalList->count() }}"
        data-duration-minutes="{{ $ujian->durasi_menit ?? 0 }}"
        data-start-time="{{ $ujianSiswa->waktu_mulai->toIso8601String() }}"
        data-autosave-url="{{ route($routePrefix . 'autosave', [$mataPelajaran->id, $ujian->id]) }}"
        data-monitoring-url="{{ $isLatihan ? '' : route('siswa.lms.mapel.ujian.monitoring', [$mataPelajaran->id, $ujian->id]) }}"
        data-csrf-token="{{ csrf_token() }}"
        data-storage-key="doubtState_{{ $ujianSiswa->id }}"
        data-question-meta="{{ $encodedQuestionMeta }}"
        data-answers-state="{{ $encodedAnswersState }}">
    <form action="{{ route($routePrefix . 'submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" id="examForm">
        @csrf
        <div class="container-fluid px-0">
            <div class="row g-3 mx-0">
                <!-- Left: Question Area (70%) -->
                <div class="col-lg-9">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="question-header">
                            <strong>SOAL NO. <span id="q-no-display" class="badge bg-primary">1</span></strong>
                        </div>

                        <!-- Timer Mobile -->
                        <div class="d-lg-none">
                            <div class="timer-box d-inline-block px-3 py-2">
                                <small class="d-block text-muted timer-label">SISA WAKTU</small>
                                <span class="timer-badge mobile-timer">00:00:00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Question Card -->
                    <div class="question-card">
                        @if($soalList->count() > 0)
                            @foreach($soalList as $index => $soal)
                                <div class="question-item {{ $index === 0 ? 'is-active' : '' }}" id="q-item-{{ $index }}">
                                    @if($soal->narasi)
                                        <div class="narasi-box mb-3">
                                            <small class="text-muted fw-bold d-block mb-1"><i class="fas fa-book-open me-1"></i> Bacaan</small>
                                            <div class="narasi-content">
                                                {!! nl2br(e($soal->narasi)) !!}
                                            </div>
                                        </div>
                                    @endif

                                    @if($soal->image_path)
                                        <div class="soal-image-box mb-3">
                                            <div class="card border-0 shadow-sm">
                                                <div class="card-body p-2 text-center">
                                                    <img src="{{ asset('storage/' . $soal->image_path) }}"
                                                         alt="Gambar Soal {{ $index + 1 }}"
                                                         class="img-fluid rounded soal-image"
                                                         data-bs-toggle="modal" data-bs-target="#imageModal{{$index}}">
                                                    <small class="text-muted d-block mt-2">
                                                        <i class="fas fa-search-plus me-1"></i> Klik gambar untuk memperbesar
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Image Modal -->
                                        <div class="modal fade" id="imageModal{{$index}}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content bg-transparent border-0">
                                                    <div class="modal-body text-center pt-2 pb-0">
                                                        <img src="{{ asset('storage/' . $soal->image_path) }}" alt="Gambar Soal {{ $index + 1 }}" class="img-fluid rounded shadow-lg soal-modal-image">
                                                    </div>
                                                    <div class="modal-footer border-0 justify-content-center">
                                                        <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Tutup Gambar</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Question Text -->
                                    <div class="question-text">
                                        {!! nl2br(e($soal->pertanyaan)) !!}
                                    </div>

                                    <!-- Answers -->
                                    <div>
                                        @if($soal->tipe_soal === 'pilihan_ganda')
                                            @php
                                                $pilihan = is_array($soal->pilihan_jawaban)
                                                    ? $soal->pilihan_jawaban
                                                    : json_decode($soal->pilihan_jawaban, true);
                                            @endphp
                                            @if(is_array($pilihan))
                                                @foreach($pilihan as $key => $value)
                                                    <label class="option-item">
                                                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}"
                                                            data-answer-choice data-index="{{ $index }}" data-soal-id="{{ $soal->id }}"
                                                            {{ isset($existingAnswers[$soal->id]) && $existingAnswers[$soal->id] == $key ? 'checked' : '' }}>
                                                        <span><strong>{{ $key }}.</strong> {{ $value }}</span>
                                                    </label>
                                                @endforeach
                                            @endif

                                        @elseif($soal->tipe_soal === 'pilihan_ganda_kompleks')
                                            @php
                                                $pilihan = is_array($soal->pilihan_jawaban)
                                                    ? $soal->pilihan_jawaban
                                                    : json_decode($soal->pilihan_jawaban, true);
                                            @endphp
                                            <small class="text-muted mb-2 d-block"><i class="fas fa-info-circle me-1"></i>Pilih semua jawaban yang benar</small>
                                            <input type="hidden" name="jawaban[{{ $soal->id }}]" id="kompleks-hidden-{{ $soal->id }}" value="{{ $existingAnswers[$soal->id] ?? '' }}">
                                            @php
                                                $ansRaw = $existingAnswers[$soal->id] ?? '';
                                                $checkedKompleks = json_decode($ansRaw, true);
                                                if (!is_array($checkedKompleks)) {
                                                    $checkedKompleks = $ansRaw ? explode(',', $ansRaw) : [];
                                                }
                                            @endphp
                                            @if(is_array($pilihan))
                                                @foreach($pilihan as $key => $value)
                                                    @if($key !== 'jawaban_benar')
                                                        <label class="option-item">
                                                            <input type="checkbox" class="kompleks-cb" data-soal-id="{{ $soal->id }}" data-index="{{ $index }}" value="{{ $key }}"
                                                                {{ in_array($key, $checkedKompleks) ? 'checked' : '' }}>
                                                            <span><strong>{{ $key }}.</strong> {{ $value }}</span>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            @endif

                                        @elseif($soal->tipe_soal === 'benar_salah')
                                            @php
                                                $pilihanData = is_array($soal->pilihan_jawaban)
                                                    ? $soal->pilihan_jawaban
                                                    : json_decode($soal->pilihan_jawaban, true);
                                                $pernyataanList = $pilihanData['pernyataan'] ?? [];
                                            @endphp
                                            <input type="hidden" name="jawaban[{{ $soal->id }}]" id="bs-hidden-{{ $soal->id }}" value="{{ $existingAnswers[$soal->id] ?? '' }}">
                                            @php
                                                $checkedBS = isset($existingAnswers[$soal->id]) ? json_decode($existingAnswers[$soal->id], true) : [];
                                            @endphp
                                            @foreach($pernyataanList as $pIdx => $item)
                                                <div class="mb-3 p-3 border rounded bg-light">
                                                    <p class="mb-2 fw-bold">{{ $item['text'] ?? $item['pernyataan'] ?? '' }}</p>
                                                    <div class="d-flex gap-3">
                                                        <label class="option-item benar-salah-option mb-0 flex-fill text-center">
                                                            <input type="radio" name="bs_{{ $soal->id }}_{{ $pIdx }}" value="true"
                                                                data-benar-salah-answer data-soal-id="{{ $soal->id }}" data-total-pernyataan="{{ count($pernyataanList) }}" data-index="{{ $index }}" {{ isset($checkedBS[$pIdx]) && ($checkedBS[$pIdx] === true || $checkedBS[$pIdx] === 'true' || $checkedBS[$pIdx] === 1) ? 'checked' : '' }}>
                                                            <span><strong>BENAR</strong></span>
                                                        </label>
                                                        <label class="option-item benar-salah-option mb-0 flex-fill text-center">
                                                            <input type="radio" name="bs_{{ $soal->id }}_{{ $pIdx }}" value="false"
                                                                data-benar-salah-answer data-soal-id="{{ $soal->id }}" data-total-pernyataan="{{ count($pernyataanList) }}" data-index="{{ $index }}" {{ isset($checkedBS[$pIdx]) && ($checkedBS[$pIdx] === false || $checkedBS[$pIdx] === 'false' || $checkedBS[$pIdx] === 0) ? 'checked' : '' }}>
                                                            <span><strong>SALAH</strong></span>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach

                                        @else
                                            <textarea name="jawaban[{{ $soal->id }}]" rows="6" class="form-control"
                                                placeholder="Tulis jawaban Anda..." data-answer-text data-index="{{ $index }}" data-soal-id="{{ $soal->id }}">{{ $existingAnswers[$soal->id] ?? '' }}</textarea>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h5 class="text-muted">Soal tidak ditemukan!</h5>
                                <p class="text-muted small mt-2">
                                    ID Ujian: {{ $ujian->id }}<br>
                                    Mata Pelajaran: {{ $mataPelajaran->nama_mapel ?? 'N/A' }}<br>
                                    Jumlah Soal: {{ $soalList->count() ?? 0 }}
                                </p>

                                @if($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan')
                                <div class="mt-4">
                                    <p class="text-muted mb-3">Anda sedang dalam sesi ujian tanpa ada soal. Pilih aksi di bawah:</p>
                                    <form action="{{ route($routePrefix . 'submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" class="empty-submit-form">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" data-confirm-empty-submit>
                                            <i class="fas fa-times-circle"></i> Akhiri Ujian Sekarang
                                        </button>
                                    </form>
                                    <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-secondary ms-2">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Mata Pelajaran
                                    </a>
                                </div>
                                @else
                                <div class="mt-4">
                                    <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali ke Mata Pelajaran
                                    </a>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="d-flex justify-content-between align-items-center mt-3 gap-2 flex-nowrap exam-nav-actions">
                        <button type="button" class="btn btn-primary btn-nav-q flex-grow-1 text-nowrap" id="btn-prev" data-prev-question>
                            <i class="fas fa-chevron-left me-1"></i> <span class="d-none d-sm-inline">SOAL </span>SEBELUMNYA
                        </button>

                        <label class="btn btn-warning d-flex align-items-center justify-content-center m-0 flex-grow-1 text-nowrap nav-ragu-label" id="label-ragu">
                            <input type="checkbox" id="cb-ragu" class="ragu-checkbox" data-toggle-doubt>
                            <span class="fw-bold"><i class="fas fa-flag me-1"></i> RAGU-RAGU</span>
                        </label>

                        <button type="button" class="btn btn-primary btn-nav-q flex-grow-1 text-nowrap" id="btn-next" data-next-question>
                            <span class="d-none d-sm-inline">SOAL </span>SELANJUTNYA <i class="fas fa-chevron-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Right: Sidebar (30%) -->
                <div class="col-lg-3">
                    <div class="exam-sidebar">
                        <!-- Timer Desktop -->
                        <div class="timer-box">
                            <small class="d-block text-muted mb-1 timer-label">SISA WAKTU</small>
                            <div class="timer-badge" id="timer-display-main">00:00:00</div>
                        </div>

                        <!-- Navigation Title -->
                        <h6 class="fw-bold mb-2 small">NOMOR SOAL</h6>

                        <!-- Navigation Grid -->
                        <div class="q-nav-grid mb-3">
                            @foreach($soalList as $index => $soal)
                                <div class="q-nav-item" id="nav-item-{{ $index }}" data-jump-question="{{ $index }}">
                                    {{ $index + 1 }}
                                </div>
                            @endforeach
                        </div>

                        <!-- Legend -->
                        <div class="mb-3">
                            <div class="legend-item">
                                <div class="legend-box bg-success"></div>
                                <span>Hijau = Sudah dijawab</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-box bg-warning"></div>
                                <span>Orange = Ragu-ragu</span>
                            </div>
                            <div class="legend-item">
                                <div class="legend-box bg-secondary"></div>
                                <span>Abu-abu = Belum dijawab</span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="button" class="btn btn-danger w-100 fw-bold" data-finish-exam>
                            SELESAIKAN UJIAN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
@endif

@endsection
