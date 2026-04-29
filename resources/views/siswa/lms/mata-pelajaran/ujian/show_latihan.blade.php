@php
    $layout = ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan') ? 'layouts.lms-latihan' : 'layouts.lms';
@endphp

@extends($layout)

@section('title', $ujian->judul_ujian)

{{-- Section for Standard Layout (Start/Result screens) --}}
@if(!isset($ujianSiswa) || $ujianSiswa->status !== 'sedang_mengerjakan')
    @section('page-title', $mataPelajaran->nama_mapel)
    @section('page-subtitle', ucwords(str_replace('_', ' ', $ujian->tipe_ujian)))
    @section('sidebar-menu')
        @include('siswa.partials.sidebar-lms')
    @endsection
@endif

@section('content')

@if(!$ujianSiswa || $ujianSiswa->status !== 'sedang_mengerjakan')
    {{-- LAYOUT 1: START SCREEN / RESULT SCREEN --}}
    <style>
        .ujian-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .info-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 20px;
            text-align: center;
        }

        .info-box i {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .info-box h5 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
    </style>

    <div class="container-fluid">


        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($ujianSiswa && $ujianSiswa->status === 'selesai')
                     <!-- RESULT SCREEN -->
                     <div class="ujian-card text-center">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h3 class="text-success mb-2">Latihan Selesai!</h3>
                        <p class="text-muted mb-4">Jawaban Anda telah tersimpan.</p>

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
                                    <form id="form-retake" action="{{ route('siswa.lms.mapel.latihan.retake', [$mataPelajaran->id, $ujian->id]) }}" method="POST" class="m-0">
                                        @csrf
                                        <button type="button" class="btn btn-warning px-4" onclick="confirmRetake()">
                                            <i class="fas fa-redo-alt me-2"></i> Kerjakan Ulang @if($sisaPengulangan !== null) (Sisa: {{ $sisaPengulangan }}) @endif
                                        </button>
                                    </form>
                                @endif
                            @endif
                            <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-primary px-4 m-0">
                                <i class="fas fa-arrow-left me-2"></i> Kembali
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
                                <form action="{{ route('siswa.lms.mapel.latihan.mulai', [$mataPelajaran->id, $ujian->id]) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-play me-2"></i> Mulai Latihan Sekarang
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
                                    <i class="fas fa-history me-2"></i> Sudah Berakhir
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($ujianSiswa && $ujianSiswa->status === 'selesai' && $ujian->bisa_diulang)
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmRetake() {
            Swal.fire({
                title: 'Kerjakan Ulang?',
                text: 'Jawaban dan nilai Anda sebelumnya akan diriset. Apakah Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kerjakan Ulang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-retake').submit();
                }
            });
        }
    </script>
    @endpush
    @endif

@else
    {{-- LAYOUT 2: LATIHAN INTERFACE (WORKSHEET MODE) --}}
    <style>
        body {
            background: #f3f4f6;
        }

        .latihan-header {
            background: white;
            padding: 15px 20px;
            border-bottom: 1px solid #e5e7eb;
            position: sticky;
            top: 60px; /* Offset for fixed exam-header (60px tall) */
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .latihan-header-left {
            flex: 1;
            min-width: 0; /* Allow truncation */
        }

        .latihan-header-left h5 {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .latihan-header-right {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .timer-badge {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.2rem;
            color: #dc3545;
            background: #fff;
            padding: 5px 12px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            white-space: nowrap;
        }

        .question-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
        }

        /* Mobile Responsive */
        @media (max-width: 575px) {
            .latihan-header {
                padding: 10px 12px;
                gap: 8px;
            }

            .latihan-header-left h5 {
                font-size: 0.85rem;
            }

            .latihan-header-left small {
                display: none;
            }

            .timer-badge {
                font-size: 0.95rem;
                padding: 4px 8px;
            }

            .latihan-header-right .btn {
                font-size: 12px;
                padding: 5px 10px;
            }

            .latihan-header-right .btn .me-1 + span,
            .latihan-header-right .btn i ~ * {
                /* Keep icon, text still shows */
            }

            .question-card {
                padding: 15px;
                margin-bottom: 14px;
            }

            .question-text {
                font-size: 0.95rem;
            }

            .option-item {
                padding: 10px 12px;
            }
        }

        .question-number {
            background: #165fac;
            color: white;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .question-text {
            font-size: 1.05rem;
            line-height: 1.6;
            color: #374151;
            margin-bottom: 20px;
        }

        .option-item {
            display: flex;
            align-items: flex-start;
            padding: 12px 15px;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            margin-bottom: 10px;
            cursor: pointer;
            background: #fff;
            transition: all 0.2s;
        }

        .option-item:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }

        .option-item input[type="radio"] {
            margin-right: 12px;
            margin-top: 4px;
            transform: scale(1.2);
        }

        .narasi-box {
            background: #f0f9ff;
            border-left: 4px solid #0ea5e9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 0.95rem;
        }
    </style>

    <form action="{{ route('siswa.lms.mapel.latihan.submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" id="examForm">
        @csrf
        
        <!-- Sticky Header -->
        <div class="latihan-header">
            <div class="latihan-header-left">
                <h5 class="mb-0 fw-bold">{{ $ujian->judul_ujian }}</h5>
                <small class="text-muted text-uppercase">{{ str_replace('_', ' ', $ujian->tipe_ujian) }}</small>
            </div>
            <div class="latihan-header-right">
                <i class="fas fa-clock text-secondary d-none d-sm-inline"></i>
                <div class="timer-badge" id="timer-display">00:00:00</div>
                <button type="button" class="btn btn-primary fw-bold" onclick="finishExam()">
                    <i class="fas fa-paper-plane me-1"></i><span class="d-none d-sm-inline"> SELESAI</span>
                </button>
            </div>
        </div>

        <div class="container py-4" style="max-width: 800px;">
            @if($soalList->count() > 0)
                @foreach($soalList as $index => $soal)
                    <div class="question-card">
                        <div class="d-flex align-items-start gap-3">
                            <div class="question-number">{{ $index + 1 }}</div>
                            <div class="w-100">
                                @if($soal->narasi)
                                    <div class="narasi-box">
                                        <div class="fw-bold mb-1 text-primary"><i class="fas fa-book-open me-1"></i> Bacaan</div>
                                        {!! nl2br(e($soal->narasi)) !!}
                                    </div>
                                @endif

                                @if($soal->image_path)
                                    <div class="soal-image-box mb-3">
                                        <div class="card border-0 shadow-sm">
                                            <div class="card-body p-2 text-center">
                                                <img src="{{ asset('storage/' . $soal->image_path) }}"
                                                     alt="Gambar Soal {{ $index + 1 }}"
                                                     class="img-fluid rounded"
                                                     style="max-height: 250px; cursor: pointer;"
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
                                                    <img src="{{ asset('storage/' . $soal->image_path) }}" class="img-fluid rounded shadow-lg" style="max-height: 80vh;">
                                                </div>
                                                <div class="modal-footer border-0 justify-content-center">
                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i>Tutup Gambar</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

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
                                                    <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" {{ isset($existingAnswers[$soal->id]) && $existingAnswers[$soal->id] == $key ? 'checked' : '' }}>
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
                                        <input type="hidden" name="jawaban[{{ $soal->id }}]" id="kompleks-hidden-{{ $soal->id }}" value="">
                                        @php
                                            $checkedKompleks = isset($existingAnswers[$soal->id]) ? explode(',', $existingAnswers[$soal->id]) : [];
                                        @endphp
                                        @if(is_array($pilihan))
                                            @foreach($pilihan as $key => $value)
                                                @if($key !== 'jawaban_benar')
                                                    <label class="option-item">
                                                        <input type="checkbox" class="kompleks-cb" data-soal-id="{{ $soal->id }}" value="{{ $key }}"
                                                            onchange="updateKompleks({{ $soal->id }})" style="margin-right: 12px; margin-top: 4px; transform: scale(1.2);" {{ in_array($key, $checkedKompleks) ? 'checked' : '' }}>
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
                                        <input type="hidden" name="jawaban[{{ $soal->id }}]" id="bs-hidden-{{ $soal->id }}" value="">
                                        @php
                                            $checkedBS = isset($existingAnswers[$soal->id]) ? json_decode($existingAnswers[$soal->id], true) : [];
                                        @endphp
                                        @foreach($pernyataanList as $pIdx => $item)
                                            <div class="mb-3 p-3 border rounded bg-light">
                                                <p class="mb-2 fw-bold">{{ $item['text'] ?? $item['pernyataan'] ?? '' }}</p>
                                                <div class="d-flex gap-3">
                                                    <label class="option-item mb-0 flex-fill text-center" style="justify-content: center;">
                                                        <input type="radio" name="bs_{{ $soal->id }}_{{ $pIdx }}" value="true"
                                                            onchange="updateBenarSalah({{ $soal->id }}, {{ count($pernyataanList) }})" style="margin-right: 8px;" {{ isset($checkedBS[$pIdx]) && $checkedBS[$pIdx] == 'true' ? 'checked' : '' }}>
                                                        <span><strong>BENAR</strong></span>
                                                    </label>
                                                    <label class="option-item mb-0 flex-fill text-center" style="justify-content: center;">
                                                        <input type="radio" name="bs_{{ $soal->id }}_{{ $pIdx }}" value="false"
                                                            onchange="updateBenarSalah({{ $soal->id }}, {{ count($pernyataanList) }})" style="margin-right: 8px;" {{ isset($checkedBS[$pIdx]) && $checkedBS[$pIdx] == 'false' ? 'checked' : '' }}>
                                                        <span><strong>SALAH</strong></span>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach

                                    @else
                                        <textarea name="jawaban[{{ $soal->id }}]" rows="4" class="form-control"
                                            placeholder="Tulis jawaban Anda disini...">{{ $existingAnswers[$soal->id] ?? '' }}</textarea>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>Belum ada soal untuk latihan ini.</h5>
                </div>
            @endif

            <div class="text-center mt-4 mb-5">
                <button type="button" class="btn btn-primary btn-lg px-5 shadow" onclick="finishExam()">
                    <i class="fas fa-check-circle me-2"></i> KIRIM JAWABAN
                </button>
            </div>
        </div>
    </form>

    <!-- JS Logic -->
    <script>
        // Timer Logic
        const durasiMenit = {{ $ujian->durasi_menit ?? 0 }};
        const startTime = new Date("{{ $ujianSiswa->waktu_mulai }}").getTime();
        
        // Jika durasi 0, berarti tanpa batas waktu
        const isUnlimited = (durasiMenit === 0);
        const endTime = isUnlimited ? null : startTime + (durasiMenit * 60 * 1000);

        function updateTimer() {
            if (isUnlimited) {
                document.getElementById("timer-display").innerHTML = "NO LIMIT";
                return;
            }

            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                document.getElementById("timer-display").innerHTML = "00:00:00";
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Latihan akan disubmit otomatis.',
                    icon: 'warning',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    document.getElementById('examForm').submit();
                });
                return;
            }

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const timerStr = 
                (hours < 10 ? "0" + hours : hours) + ":" + 
                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                (seconds < 10 ? "0" + seconds : seconds);
            
            document.getElementById("timer-display").innerHTML = timerStr;
        }

        setInterval(updateTimer, 1000);
        updateTimer();

        function updateKompleks(soalId) {
            const checkboxes = document.querySelectorAll(`.kompleks-cb[data-soal-id="${soalId}"]:checked`);
            const selected = Array.from(checkboxes).map(cb => cb.value);
            document.getElementById(`kompleks-hidden-${soalId}`).value = JSON.stringify(selected);
        }

        function updateBenarSalah(soalId, totalPernyataan) {
            const answers = [];
            for (let i = 0; i < totalPernyataan; i++) {
                const radio = document.querySelector(`input[name="bs_${soalId}_${i}"]:checked`);
                if (radio) {
                    answers.push(radio.value === 'true');
                } else {
                    answers.push(null);
                }
            }
            document.getElementById(`bs-hidden-${soalId}`).value = JSON.stringify(answers);
        }

        function finishExam() {
            Swal.fire({
                title: 'Kirim Jawaban?',
                text: 'Pastikan Anda sudah memeriksa semua jawaban.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kirim!',
                cancelButtonText: 'Periksa Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('examForm').submit();
                }
            });
        }

        function confirmRetake() {
            Swal.fire({
                title: 'Kerjakan Ulang?',
                text: 'Riwayat nilai sebelumnya akan dihapus. Anda yakin?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Kerjakan Ulang!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-retake').submit();
                }
            });
        }
    </script>
@endif

@endsection
