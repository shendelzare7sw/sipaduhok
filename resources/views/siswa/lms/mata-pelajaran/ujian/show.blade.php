@extends('layouts.lms')

@section('title', 'Ujian - ' . $ujian->judul_ujian)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Ujian ' . ucwords(str_replace('_', ' ', $ujian->tipe_ujian)))

@section('content')
<style>
    .ujian-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .timer-box {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin-bottom: 25px;
        position: sticky;
        top: 20px;
        z-index: 100;
    }
    .timer-box.warning {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }
    .timer-display {
        font-size: 48px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        margin: 10px 0;
    }
    .soal-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 20px;
        transition: all 0.3s;
    }
    .soal-card:hover {
        border-color: #165fac;
        box-shadow: 0 4px 12px rgba(22, 95, 172, 0.1);
    }
    .soal-number {
        background: linear-gradient(135deg, #165fac, #0d3f7a);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }
    .pilihan-label {
        display: flex;
        align-items: center;
        padding: 12px 15px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
        margin-bottom: 10px;
    }
    .pilihan-label:hover {
        border-color: #165fac;
        background: #f0f9ff;
    }
    .pilihan-label input[type="radio"] {
        margin-right: 12px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }
    .access-denied {
        background: linear-gradient(135deg, #fecaca, #fee2e2);
        border: 2px solid #ef4444;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
    }
</style>

<!-- Breadcrumb -->
<nav aria-label="breadcrumb" style="margin-bottom: 20px;">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('siswa.lms.dashboard') }}">Dashboard LMS</a></li>
        <li class="breadcrumb-item"><a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">{{ $mataPelajaran->nama_mapel }}</a></li>
        <li class="breadcrumb-item active">{{ $ujian->judul_ujian }}</li>
    </ol>
</nav>

@php
    $now = now();
    $isOngoing = $ujian->isOngoing();
    $needsValidation = in_array($ujian->tipe_ujian, ['uts', 'uas']);
    $hasAccess = !$needsValidation || ($siswa->validasi_ujian_bendahara && $siswa->validasi_ujian_wali);
@endphp

<!-- Cek Akses Validasi untuk UTS/UAS -->
@if($needsValidation && !$hasAccess)
<div class="access-denied">
    <i class="fas fa-lock fa-4x mb-3" style="color: #ef4444;"></i>
    <h3 style="color: #991b1b; margin-bottom: 15px;">
        Belum Memiliki Akses Ujian
    </h3>
    <p style="color: #666; font-size: 16px; margin-bottom: 20px;">
        Silakan Periksa Tagihan Anda dan Hubungi Wali Kelas
    </p>
    
    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px auto; max-width: 500px;">
        <h5 style="color: #165fac; margin-bottom: 15px;">Status Validasi:</h5>
        <div style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
            <span>Validasi Bendahara:</span>
            <span class="badge {{ $siswa->validasi_ujian_bendahara ? 'bg-success' : 'bg-danger' }}">
                {{ $siswa->validasi_ujian_bendahara ? 'Disetujui' : 'Belum Disetujui' }}
            </span>
        </div>
        <div style="display: flex; justify-content: space-between; padding: 10px 0;">
            <span>Validasi Wali Kelas:</span>
            <span class="badge {{ $siswa->validasi_ujian_wali ? 'bg-success' : 'bg-danger' }}">
                {{ $siswa->validasi_ujian_wali ? 'Disetujui' : 'Belum Disetujui' }}
            </span>
        </div>
    </div>

    <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-secondary mt-3">
        <i class="fas fa-arrow-left"></i> Kembali
    </a>
</div>

@elseif(!$isOngoing)
<!-- Ujian Belum/Sudah Berlangsung -->
<div class="ujian-card">
    <div style="text-align: center; padding: 40px;">
        @if($now->lt($ujian->tanggal_mulai))
            <i class="fas fa-clock fa-3x mb-3" style="color: #f59e0b;"></i>
            <h3 style="color: #92400e;">Ujian Belum Dimulai</h3>
            <p style="color: #666; margin: 15px 0;">
                Ujian akan dimulai pada:<br>
                <strong>{{ $ujian->tanggal_mulai->format('d F Y, H:i') }} WIB</strong>
            </p>
        @else
            <i class="fas fa-check-circle fa-3x mb-3" style="color: #10b981;"></i>
            <h3 style="color: #065f46;">Ujian Sudah Selesai</h3>
            <p style="color: #666; margin: 15px 0;">
                Periode ujian berakhir pada:<br>
                <strong>{{ $ujian->tanggal_selesai->format('d F Y, H:i') }} WIB</strong>
            </p>
        @endif
        
        <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-primary mt-3">
            <i class="fas fa-arrow-left"></i> Kembali ke Mata Pelajaran
        </a>
    </div>
</div>

@else
<!-- Ujian Sedang Berlangsung -->
<div class="ujian-card">
    <!-- Info Ujian -->
    <div style="background: #f0f9ff; padding: 20px; border-radius: 12px; margin-bottom: 25px; border-left: 4px solid #165fac;">
        <h3 style="color: #165fac; margin: 0 0 15px 0;">
            <i class="fas fa-file-signature"></i> {{ $ujian->judul_ujian }}
        </h3>
        <div style="display: flex; gap: 20px; flex-wrap: wrap; color: #666; font-size: 14px;">
            <div><i class="fas fa-stopwatch"></i> <strong>Durasi:</strong> {{ $ujian->durasi_menit }} menit</div>
            <div><i class="fas fa-list-ol"></i> <strong>Jumlah Soal:</strong> {{ $soalList->count() }} soal</div>
            <div><i class="fas fa-calendar"></i> <strong>Berlangsung:</strong> {{ $ujian->tanggal_mulai->format('d M Y, H:i') }} - {{ $ujian->tanggal_selesai->format('H:i') }}</div>
        </div>
    </div>

    @if($ujianSiswa && $ujianSiswa->status === 'selesai')
    <!-- Ujian Sudah Dikerjakan -->
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">
            <i class="fas fa-check-circle"></i> Ujian Sudah Selesai
        </h4>
        <p style="margin: 10px 0;">
            Anda sudah menyelesaikan ujian ini pada:<br>
            <strong>{{ $ujianSiswa->waktu_selesai->format('d F Y, H:i') }} WIB</strong>
        </p>
        @if($ujianSiswa->nilai !== null)
        <hr>
        <p style="margin: 10px 0 0 0;">
            <strong>Nilai Anda:</strong> <span style="font-size: 24px; color: #165fac;">{{ number_format($ujianSiswa->nilai, 1) }}</span>
        </p>
        @else
        <hr>
        <p style="margin: 10px 0 0 0;">
            <i class="fas fa-hourglass-half"></i> Nilai sedang diproses oleh guru
        </p>
        @endif
    </div>

    @elseif($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan')
    <!-- Timer Countdown -->
    <div class="timer-box" id="timerBox">
        <div style="font-size: 14px; opacity: 0.9;">
            <i class="fas fa-hourglass-half"></i> Sisa Waktu
        </div>
        <div class="timer-display" id="timerDisplay">00:00:00</div>
        <div style="font-size: 14px; opacity: 0.9;">
            Ujian akan otomatis tersubmit saat waktu habis
        </div>
    </div>

    <!-- Form Soal -->
    <form action="{{ route('siswa.lms.mapel.ujian.submit', [$mataPelajaran->id, $ujian->id]) }}" 
          method="POST" 
          id="ujianForm">
        @csrf

        @foreach($soalList as $index => $soal)
        <div class="soal-card">
            <div class="d-flex">
                <div class="soal-number">{{ $index + 1 }}</div>
                <div style="flex: 1;">
                    <h5 style="color: #1a1a1a; margin-bottom: 15px;">
                        {{ $soal->pertanyaan }}
                    </h5>

                    @if($soal->tipe_soal === 'pilihan_ganda')
                        @php $pilihan = json_decode($soal->pilihan_jawaban, true); @endphp
                        @foreach($pilihan as $key => $value)
                        <label class="pilihan-label">
                            <input type="radio" 
                                   name="jawaban[{{ $soal->id }}]" 
                                   value="{{ $key }}" 
                                   required>
                            <span>{{ $key }}. {{ $value }}</span>
                        </label>
                        @endforeach
                    @else
                        <textarea name="jawaban[{{ $soal->id }}]" 
                                  rows="5" 
                                  class="form-control" 
                                  placeholder="Tulis jawaban Anda di sini..."
                                  required></textarea>
                    @endif
                </div>
            </div>
        </div>
        @endforeach

        <div style="background: #fef3c7; padding: 20px; border-radius: 12px; margin-top: 30px; text-align: center;">
            <h5 style="color: #92400e; margin-bottom: 15px;">
                <i class="fas fa-exclamation-triangle"></i> Perhatian
            </h5>
            <p style="color: #666; margin-bottom: 20px;">
                Pastikan semua soal sudah dijawab sebelum submit. Setelah submit, jawaban tidak dapat diubah.
            </p>
            <button type="submit" class="btn btn-danger btn-lg" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan ujian? Jawaban tidak dapat diubah setelah dikumpulkan.')">
                <i class="fas fa-paper-plane"></i> Submit Ujian
            </button>
        </div>
    </form>

    @else
    <!-- Belum Mulai Ujian -->
    <div style="text-align: center; padding: 40px;">
        <i class="fas fa-play-circle fa-3x mb-3" style="color: #165fac;"></i>
        <h3 style="color: #165fac;">Siap Memulai Ujian?</h3>
        <p style="color: #666; margin: 15px 0 25px 0;">
            Klik tombol di bawah untuk memulai ujian. Timer akan berjalan setelah Anda klik tombol ini.
        </p>
        
        <form action="{{ route('siswa.lms.mapel.ujian.mulai', [$mataPelajaran->id, $ujian->id]) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-play"></i> Mulai Ujian
            </button>
        </form>
    </div>
    @endif
</div>
@endif

@if($ujianSiswa && $ujianSiswa->status === 'sedang_mengerjakan')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const waktuMulai = new Date("{{ $ujianSiswa->waktu_mulai }}").getTime();
    const durasiMenit = {{ $ujian->durasi_menit }};
    const waktuSelesai = waktuMulai + (durasiMenit * 60 * 1000);
    
    const timerDisplay = document.getElementById('timerDisplay');
    const timerBox = document.getElementById('timerBox');
    const form = document.getElementById('ujianForm');
    
    const countdown = setInterval(function() {
        const now = new Date().getTime();
        const distance = waktuSelesai - now;
        
        if (distance < 0) {
            clearInterval(countdown);
            timerDisplay.innerHTML = "00:00:00";
            alert("Waktu habis! Ujian akan otomatis disubmit.");
            form.submit();
            return;
        }
        
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        timerDisplay.innerHTML = 
            String(hours).padStart(2, '0') + ":" + 
            String(minutes).padStart(2, '0') + ":" + 
            String(seconds).padStart(2, '0');
        
        // Warning jika kurang dari 5 menit
        if (distance < 5 * 60 * 1000) {
            timerBox.classList.add('warning');
        }
    }, 1000);
});
</script>
@endif

@endsection