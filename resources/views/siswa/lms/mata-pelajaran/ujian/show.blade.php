@php
    $layout = ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan') ? 'layouts.lms-ujian' : 'layouts.lms';
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
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-lg-8">
                @if($ujianSiswa && $ujianSiswa->status === 'selesai')
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
                                    <h1 class="display-4 fw-bold text-primary">{{ number_format($ujianSiswa->nilai, 1) }}</h1>
                                    <span class="text-muted">Nilai Akhir</span>
                                </div>
                            @else
                                <div class="my-4">
                                    <i class="fas fa-hourglass-half fa-3x text-warning mb-2"></i>
                                    <h5 class="text-secondary">Menunggu Penilaian Guru</h5>
                                </div>
                            @endif
                        @else
                            <div class="my-4">
                                <i class="fas fa-lock fa-3x text-secondary mb-2"></i>
                                <h5 class="text-secondary">Nilai Tidak Ditampilkan</h5>

                            </div>
                        @endif

                        <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-primary mt-3">
                            <i class="fas fa-arrow-left me-2"></i> Kembali ke Mata Pelajaran
                        </a>
                     </div>
                @else
                    <!-- START SCREEN -->
                    <div class="ujian-card">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">{{ $ujian->judul_ujian }}</h3>
                            <span class="badge bg-secondary">{{ strtoupper(str_replace('_', ' ', $ujian->tipe_ujian)) }}</span>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="info-box">
                                    <i class="fas fa-clock text-warning"></i>
                                    <h5>{{ $ujian->durasi_menit == 0 ? 'Tanpa Batas' : $ujian->durasi_menit . ' Menit' }}</h5>
                                    <small class="text-muted">Durasi</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <i class="fas fa-list-ol text-info"></i>
                                    <h5>{{ $soalList->count() }} Soal</h5>
                                    <small class="text-muted">Jumlah Soal</small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-box">
                                    <i class="fas fa-calendar-alt text-success"></i>
                                    <h5>{{ $ujian->tanggal_mulai->format('d M') }} - {{ $ujian->tanggal_selesai->format('d M') }}</h5>
                                    <small class="text-muted">Periode</small>
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
                                <form action="{{ route('siswa.lms.mapel.ujian.mulai', [$mataPelajaran->id, $ujian->id]) }}" method="POST">
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

@else
    {{-- LAYOUT 2: EXAM INTERFACE (FOCUS MODE) - Simple CBT Style --}}
    <style>
        body {
            background: #e9ecef;
        }

        /* Remove default Bootstrap container padding and use custom */
        .container-fluid {
            max-width: 100% !important;
        }

        /* Question Card - Simple */
        .question-card {
            background: white;
            border-radius: 4px;
            padding: 25px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            min-height: 400px;
        }

        .question-header {
            background: #f8f9fa;
            padding: 12px 20px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: inline-block;
        }

        .question-text {
            font-size: 1rem;
            line-height: 1.6;
            color: #212529;
            margin-bottom: 20px;
        }

        /* Options - Simple */
        .option-item {
            display: flex;
            align-items: flex-start;
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 10px;
            cursor: pointer;
            background: #fff;
        }

        .option-item:hover {
            background: #f8f9fa;
        }

        .option-item input[type="radio"] {
            margin-right: 10px;
            margin-top: 3px;
            width: 18px;
            height: 18px;
        }

        /* Navigation Grid - Smaller */
        .q-nav-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
            margin-bottom: 15px;
        }

        .q-nav-item {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            background: #6c757d;
            color: white;
            border: none;
            transition: all 0.2s;
        }

        .q-nav-item:hover {
            opacity: 0.8;
        }

        .q-nav-item.active {
            background: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.3);
        }

        .q-nav-item.answered {
            background: #198754;
        }

        .q-nav-item.doubt {
            background: #fd7e14;
        }

        /* Sidebar - Simple */
        .exam-sidebar {
            background: white;
            border-radius: 4px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Timer - Simple */
        .timer-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            text-align: center;
            margin-bottom: 15px;
        }

        .timer-badge {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            font-size: 1.25rem;
            color: #dc3545;
        }

        /* Buttons */
        .btn-nav-q {
            min-width: 140px;
        }

        /* Legend */
        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 8px;
            font-size: 0.875rem;
        }

        .legend-box {
            width: 20px;
            height: 20px;
            border-radius: 3px;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .q-nav-grid {
                grid-template-columns: repeat(5, 1fr);
            }
        }
    </style>

    <form action="{{ route('siswa.lms.mapel.ujian.submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" id="examForm">
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
                                <small class="d-block text-muted" style="font-size: 0.75rem;">SISA WAKTU</small>
                                <span class="timer-badge mobile-timer">00:00:00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Question Card -->
                    <div class="question-card">
                        @if($soalList->count() > 0)
                            @foreach($soalList as $index => $soal)
                                <div class="question-item" id="q-item-{{ $index }}" style="display: {{ $index === 0 ? 'block' : 'none' }};">
                                    @if($soal->narasi)
                                        <div class="narasi-box mb-3" style="background: #f0f7ff; border-left: 4px solid #165fac; border-radius: 4px; padding: 15px;">
                                            <small class="text-muted fw-bold d-block mb-1"><i class="fas fa-book-open me-1"></i> Bacaan</small>
                                            <div style="font-size: 0.95rem; line-height: 1.7; color: #333;">
                                                {!! nl2br(e($soal->narasi)) !!}
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
                                                        <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}" onchange="selectOption({{ $index }}, '{{ $key }}')">
                                                        <span><strong>{{ $key }}.</strong> {{ $value }}</span>
                                                    </label>
                                                @endforeach
                                            @endif
                                        @elseif($soal->tipe_soal === 'benar_salah')
                                            <label class="option-item">
                                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="benar" onchange="selectOption({{ $index }}, 'benar')">
                                                <span><strong>BENAR</strong></span>
                                            </label>
                                            <label class="option-item">
                                                <input type="radio" name="jawaban[{{ $soal->id }}]" value="salah" onchange="selectOption({{ $index }}, 'salah')">
                                                <span><strong>SALAH</strong></span>
                                            </label>
                                        @else
                                            <textarea name="jawaban[{{ $soal->id }}]" rows="6" class="form-control"
                                                placeholder="Tulis jawaban Anda..." oninput="selectOption({{ $index }}, 'text')"></textarea>
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
                                    <form action="{{ route('siswa.lms.mapel.ujian.submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('Anda akan mengakhiri ujian tanpa menjawab soal. Lanjutkan?')">
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
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <button type="button" class="btn btn-primary btn-nav-q" id="btn-prev" onclick="prevQuestion()">
                            <i class="fas fa-chevron-left"></i> SOAL SEBELUMNYA
                        </button>

                        <button type="button" class="btn btn-warning text-white" id="btn-ragu" onclick="toggleRagu()">
                            <i class="fas fa-flag"></i> RAGU-RAGU
                        </button>

                        <button type="button" class="btn btn-primary btn-nav-q" id="btn-next" onclick="nextQuestion()">
                            SOAL SELANJUTNYA <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Right: Sidebar (30%) -->
                <div class="col-lg-3">
                    <div class="exam-sidebar">
                        <!-- Timer Desktop -->
                        <div class="timer-box">
                            <small class="d-block text-muted mb-1" style="font-size: 0.75rem;">SISA WAKTU</small>
                            <div class="timer-badge" id="timer-display-main">00:00:00</div>
                        </div>

                        <!-- Navigation Title -->
                        <h6 class="fw-bold mb-2 small">NOMOR SOAL</h6>

                        <!-- Navigation Grid -->
                        <div class="q-nav-grid mb-3">
                            @foreach($soalList as $index => $soal)
                                <div class="q-nav-item" id="nav-item-{{ $index }}" onclick="jumpToQuestion({{ $index }})">
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
                        <button type="button" class="btn btn-danger w-100 fw-bold" onclick="finishExam()">
                            HENTIKAN UJIAN
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- JS Logic -->
    <script>
        let currentIndex = 0;
        const totalQuestions = {{ $soalList->count() }};
        const answersState = new Array(totalQuestions).fill(false);
        const doubtState = new Array(totalQuestions).fill(false);

        // Timer
        const durasiMenit = {{ $ujian->durasi_menit ?? 0 }};
        const startTime = new Date("{{ $ujianSiswa->waktu_mulai }}").getTime();
        
        // Jika durasi 0, berarti tanpa batas waktu
        const isUnlimited = (durasiMenit === 0);
        const endTime = isUnlimited ? null : startTime + (durasiMenit * 60 * 1000);

        function updateTimer() {
            if (isUnlimited) {
                document.getElementById("timer-display-main").innerHTML = "NO LIMIT";
                 document.querySelectorAll(".mobile-timer").forEach(el => el.innerHTML = "NO LIMIT");
                return;
            }

            const now = new Date().getTime();
            const distance = endTime - now;

            if (distance < 0) {
                document.getElementById("timer-display-main").innerHTML = "00:00:00";
                document.querySelectorAll(".mobile-timer").forEach(el => el.innerHTML = "00:00:00");
                Swal.fire({
                    title: 'Waktu Habis!',
                    text: 'Ujian akan disubmit otomatis.',
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

            document.getElementById("timer-display-main").innerHTML = timerStr;
            document.querySelectorAll(".mobile-timer").forEach(el => el.innerHTML = timerStr);
        }

        setInterval(updateTimer, 1000);
        updateTimer();

        // Navigation
        function jumpToQuestion(index) {
            document.getElementById(`q-item-${currentIndex}`).style.display = 'none';
            document.getElementById(`q-item-${index}`).style.display = 'block';
            currentIndex = index;
            updateUI();
        }

        function nextQuestion() {
            if (currentIndex < totalQuestions - 1) {
                jumpToQuestion(currentIndex + 1);
            }
        }

        function prevQuestion() {
            if (currentIndex > 0) {
                jumpToQuestion(currentIndex - 1);
            }
        }

        function updateUI() {
            document.getElementById('q-no-display').innerText = currentIndex + 1;
            document.getElementById('btn-prev').disabled = (currentIndex === 0);
            document.getElementById('btn-next').disabled = (currentIndex === totalQuestions - 1);

            // Update ragu button style
            const btnRagu = document.getElementById('btn-ragu');
            if(doubtState[currentIndex]) {
                btnRagu.classList.remove('btn-warning');
                btnRagu.classList.add('btn-outline-warning');
            } else {
                btnRagu.classList.remove('btn-outline-warning');
                btnRagu.classList.add('btn-warning', 'text-white');
            }

            document.querySelectorAll('.q-nav-item').forEach((el, idx) => {
                if (idx === currentIndex) el.classList.add('active');
                else el.classList.remove('active');
            });
        }

        function selectOption(index, value) {
            if(value && value.trim() !== '') {
                answersState[index] = true;
            } else {
                answersState[index] = false;
            }
            updateNavColor(index);
        }

        function toggleRagu() {
            doubtState[currentIndex] = !doubtState[currentIndex];
            updateNavColor(currentIndex);
            updateUI();
        }

        function updateNavColor(index) {
            const navItem = document.getElementById(`nav-item-${index}`);
            navItem.classList.remove('answered', 'doubt');

            if (doubtState[index]) {
                navItem.classList.add('doubt');
            } else if (answersState[index]) {
                navItem.classList.add('answered');
            }
        }

        function finishExam() {
            const unanswered = answersState.filter(x => !x).length;
            const doubts = doubtState.filter(x => x).length;

            let msg = '';
            if (unanswered > 0) msg += `Masih ada ${unanswered} soal belum dijawab.\n`;
            if (doubts > 0) msg += `Masih ada ${doubts} soal ditandai ragu-ragu.\n`;
            msg += '\nApakah Anda yakin ingin menyelesaikan ujian ini?';

            Swal.fire({
                title: 'Konfirmasi Submit',
                text: msg,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Selesaikan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('examForm').submit();
                }
            });
        }

        // Initialize
        updateUI();

        // Prevent back
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
@endif

@endsection
