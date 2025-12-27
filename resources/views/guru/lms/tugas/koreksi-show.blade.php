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
                        <a href="{{ asset('storage/' . $tugas->file_tugas) }}" 
                           class="btn btn-sm btn-outline-primary" target="_blank">
                            <i class="fas fa-download me-1"></i>Download Soal
                        </a>
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
                        <div>
                            <strong>File Jawaban:</strong><br>
                            <a href="{{ asset('storage/' . $tugasSiswa->file_jawaban) }}" 
                               class="btn btn-sm btn-success mt-2" target="_blank">
                                <i class="fas fa-download me-1"></i>Download Jawaban Siswa
                            </a>
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
                <div class="card-header-custom">
                    <i class="fas fa-star me-2"></i>Berikan Nilai
                </div>
                <div class="p-3">
                    <form action="{{ route('guru.lms.tugas.koreksi.store', [$kelas->id, $mapel->id, $tugas->id, $tugasSiswa->id]) }}" 
                          method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Nilai (0-100) <span class="text-danger">*</span></label>
                            <input type="number" name="nilai" class="form-control @error('nilai') is-invalid @enderror" 
                                   value="{{ old('nilai', $tugasSiswa->nilai) }}" 
                                   min="0" max="100" required>
                            @error('nilai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Feedback untuk Siswa</label>
                            <textarea name="feedback_guru" class="form-control" rows="4">{{ old('feedback_guru', $tugasSiswa->feedback_guru) }}</textarea>
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
@endsection