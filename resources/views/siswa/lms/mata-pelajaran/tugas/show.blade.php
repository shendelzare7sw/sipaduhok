@extends('layouts.lms')

@section('title', 'Tugas - ' . $tugas->judul_tugas)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Pengerjaan Tugas')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .tugas-card {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .deadline-box {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 25px;
        }

        .deadline-box.expired {
            background: linear-gradient(135deg, #ef4444, #dc2626);
        }

        .deadline-box.safe {
            background: linear-gradient(135deg, #10b981, #059669);
        }

        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 10px;
        }

        .status-belum {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-dikerjakan {
            background: #fef3c7;
            color: #92400e;
        }

        .status-dinilai {
            background: #d1fae5;
            color: #065f46;
        }

        .status-terlambat {
            background: #fecaca;
            color: #7f1d1d;
        }


    </style>

    <!-- Breadcrumb -->
    <div class="page-breadcrumb">
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.dashboard') }}">
                <i class="fas fa-home"></i> Dashboard LMS
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item">
            <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}">
                <i class="fas fa-book"></i> {{ $mataPelajaran->nama_mapel }}
            </a>
        </div>
        <i class="fas fa-chevron-right page-breadcrumb-separator"></i>
        <div class="page-breadcrumb-item active">
            <i class="fas fa-tasks"></i> {{ Str::limit($tugas->judul_tugas, 30) }}
        </div>
    </div>

    <!-- Deadline Warning -->
    @php
    $now = now();
    $deadline = $tugas->tanggal_deadline;
    $isExpired = $now->gt($deadline);
    
    $diff = $now->diff($deadline);
    $timeStringParts = [];
    if($diff->days > 0) $timeStringParts[] = $diff->days . ' hari';
    if($diff->h > 0) $timeStringParts[] = $diff->h . ' jam';
    if($diff->i > 0) $timeStringParts[] = $diff->i . ' menit';
    
    $timeRemaining = empty($timeStringParts) ? 'Kurang dari 1 menit' : implode(' ', $timeStringParts);
@endphp

    <div class="deadline-box {{ $isExpired ? 'expired' : ($diff->days == 0 ? '' : 'safe') }}">
        <div style="font-size: 14px; opacity: 0.9; margin-bottom: 5px;">
            <i class="fas fa-clock"></i> Tenggat
        </div>
        <h3 style="margin: 0; font-size: 28px;">
            {{ $deadline->copy()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
        </h3>
        <div style="font-size: 16px; margin-top: 10px;">
            @if($isExpired)
                <i class="fas fa-exclamation-circle"></i> Waktu sudah habis!
            @elseif($diff->days == 0 && $diff->h < 24)
            <i class="fas fa-exclamation-triangle"></i> Segera dikumpulkan! ({{ $timeRemaining }} lagi)
        @else
            <i class="fas fa-check-circle"></i> Tersisa {{ $timeRemaining }} lagi
        @endif
        </div>
    </div>

    <div class="tugas-card">
        <!-- Header -->
        <div style="border-bottom: 2px solid #e5e7eb; padding-bottom: 20px; margin-bottom: 25px;">
            <h2 style="color: #165fac; margin: 0 0 15px 0;">
                <i class="fas fa-tasks"></i> {{ $tugas->judul_tugas }}
            </h2>

            <div style="display: flex; gap: 15px; flex-wrap: wrap; color: #666; font-size: 14px;">
                <div><i class="fas fa-user-tie"></i> <strong>Guru:</strong> {{ $tugas->guru->nama_lengkap }}</div>
                <div><i class="fas fa-calendar-plus"></i> <strong>Dibuka:</strong>
                    {{ $tugas->tanggal_mulai->copy()->locale('id')->translatedFormat('d M Y') }}</div>
                <div><i class="fas fa-calendar-times"></i> <strong>Ditutup:</strong>
                    {{ $tugas->tanggal_deadline->copy()->locale('id')->translatedFormat('d M Y, H:i') }}</div>
                @if($tugas->bisa_diulang)
                    <div><i class="fas fa-redo-alt"></i> <strong>Sisa Pengeditan:</strong> 
                        @if($tugas->batas_pengulangan)
                            {{ max(0, $tugas->batas_pengulangan - (($existingSubmission->pengulangan_ke ?? 1) - 1)) }} kali
                        @else
                            Tak Terbatas
                        @endif
                    </div>
                @else
                    <div><i class="fas fa-lock"></i> <strong>Batas Pengeditan:</strong> 1 kali</div>
                @endif
            </div>

            @if($existingSubmission)
                <span class="status-badge status-{{ $existingSubmission->status }}">
                    <i class="fas fa-info-circle"></i> Status: {{ ucwords(str_replace('_', ' ', $existingSubmission->status)) }}
                </span>
            @else
                <span class="status-badge status-belum">
                    <i class="fas fa-circle"></i> Belum Dikerjakan
                </span>
            @endif
        </div>

        <!-- Deskripsi Tugas -->
        <div
            style="background: #f0f9ff; padding: 20px; border-radius: 8px; border-left: 4px solid #165fac; margin-bottom: 25px;">
            <h4 style="color: #165fac; margin-bottom: 12px;">
                <i class="fas fa-file-alt"></i> Deskripsi Tugas
            </h4>
            <div style="color: #333; line-height: 1.8;">
                {{ $tugas->deskripsi }}
            </div>
        </div>

        <!-- File Tugas dari Guru -->
        @if($tugas->file_tugas)
            <div style="background: #fef3c7; padding: 15px; border-radius: 8px; margin-bottom: 25px;">
                <h5 style="color: #92400e; margin-bottom: 10px;">
                    <i class="fas fa-paperclip"></i> Lampiran dari Guru
                </h5>
                <x-file-preview :path="$tugas->file_tugas" label="Lihat Tugas" />
            </div>
        @endif

        <!-- Form Submit Tugas -->
        @if(!$isExpired || $existingSubmission)
            <div style="background: white; padding: 25px; border: 2px solid #e5e7eb; border-radius: 12px;">
                <h4 style="color: #165fac; margin-bottom: 20px;">
                    <i class="fas fa-pencil-alt"></i>
                    {{ $existingSubmission ? 'Edit Jawaban' : 'Kerjakan Tugas' }}
                </h4>

                @if($existingSubmission && $existingSubmission->status === 'dinilai')
                    <div class="alert alert-success" role="alert">
                        <h5 class="alert-heading">
                            <i class="fas fa-check-circle"></i> Tugas Sudah Dinilai
                        </h5>
                        <p style="margin: 10px 0 0 0;">
                            <strong>Nilai:</strong> {{ $existingSubmission->nilai }}<br>
                            @if($existingSubmission->feedback_guru)
                                <strong>Umpan Balik Guru:</strong> {{ $existingSubmission->feedback_guru }}
                            @endif
                        </p>
                    </div>
                @endif

                <form action="{{ route('siswa.lms.mapel.tugas.submit', [$mataPelajaran->id, $tugas->id]) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    @php
                        $canSubmit = true;
                        if ($existingSubmission) {
                            if (!$tugas->bisa_diulang) {
                                $canSubmit = false;
                            } elseif ($tugas->batas_pengulangan > 0 && $existingSubmission->pengulangan_ke > $tugas->batas_pengulangan) {
                                $canSubmit = false;
                            }
                        }
                        $isDisabled = $isExpired || !$canSubmit;
                    @endphp

                    <!-- Jawaban Text -->
                    <div class="form-group mb-3">
                        <label class="form-label">Jawaban (Teks)</label>
                        <textarea name="jawaban_text" rows="8" class="form-control @error('jawaban_text') is-invalid @enderror"
                            placeholder="Tulis jawaban Anda di sini..." {{ $isDisabled ? 'disabled' : '' }}>{{ old('jawaban_text', $existingSubmission->jawaban_text ?? '') }}</textarea>
                        @error('jawaban_text')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Upload File -->
                    <div class="form-group mb-3">
                        <label class="form-label">Unggah File Jawaban (Opsional)</label>
                        <input type="file" name="file_jawaban" class="form-control @error('file_jawaban') is-invalid @enderror"
                            accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.mp4" {{ $isDisabled ? 'disabled' : '' }}>
                        <small class="form-text text-muted">
                            Format: PDF, Word, Excel, PowerPoint, gambar (JPG/PNG), video (MP4). Maksimal 10MB.
                        </small>
                        @error('file_jawaban')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                            @if($existingSubmission && $existingSubmission->file_jawaban)
                                <div class="mt-3">
                                    <small class="text-muted">File sebelumnya: </small>
                                    <x-file-preview :path="$existingSubmission->file_jawaban" label="Lihat File" />
                                </div>
                            @endif
                    </div>

                    <!-- Submit Button -->
                    @if(!$isExpired)
                        @if($canSubmit)
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ $existingSubmission ? 'Perbarui Jawaban' : 'Kirim Jawaban' }}
                                </button>
                                <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        @else
                            <div class="alert alert-warning" role="alert">
                                <i class="fas fa-lock"></i> Batas maksimal pengeditan jawaban telah tercapai.
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('siswa.lms.mapel.show', $mataPelajaran->id) }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-lock"></i> Tenggat sudah lewat. Jawaban tidak dapat diubah.
                        </div>
                    @endif
                </form>

                @if($existingSubmission)
                    <div class="alert alert-info mt-3" role="alert">
                        <small>
                            <i class="fas fa-info-circle"></i>
                            Terakhir dikirim:
                            {{ $existingSubmission->tanggal_submit ? $existingSubmission->tanggal_submit->copy()->locale('id')->translatedFormat('d F Y, H:i') . ' WIB' : '-' }}
                            @if($existingSubmission->status === 'terlambat')
                                <span style="color: #dc2626; font-weight: bold;">(Terlambat)</span>
                            @endif
                        </small>
                    </div>
                @endif
            </div>
        @else
            <div class="alert alert-danger" role="alert">
                <h5 class="alert-heading">
                    <i class="fas fa-exclamation-triangle"></i> Tenggat Sudah Lewat
                </h5>
                <p style="margin: 0;">
                    Maaf, waktu pengerjaan tugas sudah habis. Anda tidak dapat mengirim jawaban.
                </p>
            </div>
        @endif
    </div>

@endsection
