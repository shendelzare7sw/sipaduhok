@php
    $layout = ($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan') ? 'layouts.lms-ujian' : 'layouts.lms';
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
                        <h3 class="text-success mb-2">Latihan Selesai!</h3>
                        <p class="text-muted mb-4">Jawaban Anda telah tersimpan.</p>

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
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
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
        }

        .question-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            border: 1px solid #e5e7eb;
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

    <form action="{{ route('siswa.lms.mapel.ujian.submit', [$mataPelajaran->id, $ujian->id]) }}" method="POST" id="examForm">
        @csrf
        
        <!-- Sticky Header -->
        <div class="latihan-header">
            <div>
                <h5 class="mb-0 fw-bold">{{ $ujian->judul_ujian }}</h5>
                <small class="text-muted text-uppercase">{{ str_replace('_', ' ', $ujian->tipe_ujian) }}</small>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-clock text-secondary"></i>
                    <div class="timer-badge" id="timer-display">00:00:00</div>
                </div>
                <button type="button" class="btn btn-primary fw-bold px-4" onclick="finishExam()">
                    <i class="fas fa-paper-plane me-1"></i> SELESAI
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
                                            <div class="card-body p-2">
                                                <img src="{{ asset('storage/' . $soal->image_path) }}"
                                                     alt="Gambar Soal {{ $index + 1 }}"
                                                     class="img-fluid rounded"
                                                     style="max-width: 100%; height: auto; cursor: pointer;"
                                                     onclick="this.requestFullscreen()">
                                                <small class="text-muted d-block mt-2 text-center">
                                                    <i class="fas fa-search-plus me-1"></i> Klik gambar untuk memperbesar
                                                </small>
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
                                                    <input type="radio" name="jawaban[{{ $soal->id }}]" value="{{ $key }}">
                                                    <span><strong>{{ $key }}.</strong> {{ $value }}</span>
                                                </label>
                                            @endforeach
                                        @endif
                                    @elseif($soal->tipe_soal === 'benar_salah')
                                        <label class="option-item">
                                            <input type="radio" name="jawaban[{{ $soal->id }}]" value="benar">
                                            <span><strong>BENAR</strong></span>
                                        </label>
                                        <label class="option-item">
                                            <input type="radio" name="jawaban[{{ $soal->id }}]" value="salah">
                                            <span><strong>SALAH</strong></span>
                                        </label>
                                    @else
                                        <textarea name="jawaban[{{ $soal->id }}]" rows="4" class="form-control" 
                                            placeholder="Tulis jawaban Anda disini..."></textarea>
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
    </script>
@endif

@endsection
