@extends('layouts.sneat')

@section('title', 'Ajukan Izin - ' . $siswa->nama_lengkap)
@section('page-title', 'Ajukan Izin')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/orang-tua/presensi/ajukan-izin.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y orang-tua-ajukan-izin-page">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <h4 class="fw-bold mb-1">Pengajuan Izin/Sakit</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
    </div>

    <!-- Student Info Card -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="avatar avatar-lg permission-avatar">
                        @if($siswa->user && $siswa->user->foto_profil)
                            <img src="{{ asset('storage/' . $siswa->user->foto_profil) }}" alt="avatar"
                                class="rounded-circle border border-2 border-white shadow-sm permission-avatar-image">
                        @elseif($siswa->foto)
                            <img src="{{ asset('storage/' . $siswa->foto) }}" alt="avatar"
                                class="rounded-circle border border-2 border-white shadow-sm permission-avatar-image">
                        @else
                            <span class="avatar-initial rounded-circle bg-primary text-white shadow-sm fw-bold border border-2 border-white d-flex align-items-center justify-content-center permission-avatar-initial">
                                {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="col">
                    <h5 class="mb-1">{{ $siswa->nama_lengkap }}</h5>
                    <div class="text-muted small">
                        <span class="me-3">
                            <i class="fas fa-id-card me-1"></i>NISN: {{ $siswa->nisn }}
                        </span>
                        <span class="me-3">
                            <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
                        </span>
                        <span>
                            <i class="fas fa-building me-1"></i>{{ $siswa->cabang->nama_cabang ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-file-medical me-2 text-primary"></i>
                        Form Pengajuan Izin/Sakit
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <div>
                            <strong>Informasi:</strong> Sebagai orang tua/wali, Anda mengajukan izin ketidakhadiran untuk anak Anda.
                            Pastikan melampirkan bukti yang valid seperti surat dokter atau keterangan izin.
                        </div>
                    </div>

                    <form action="{{ route('orang-tua.presensi.store-izin', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Tanggal -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   name="tanggal"
                                   class="form-control @error('tanggal') is-invalid @enderror"
                                   value="{{ old('tanggal') }}"
                                   required>
                            @error('tanggal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Pilih tanggal ketidakhadiran anak Anda</small>
                        </div>

                        <!-- Jenis -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Jenis Izin <span class="text-danger">*</span>
                            </label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Izin --</option>
                                <option value="sakit" {{ old('jenis') === 'sakit' ? 'selected' : '' }}>
                                    Sakit
                                </option>
                                <option value="izin" {{ old('jenis') === 'izin' ? 'selected' : '' }}>
                                    Izin
                                </option>
                            </select>
                            @error('jenis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Keterangan -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Keterangan <span class="text-danger">*</span>
                            </label>
                            <textarea name="keterangan"
                                      rows="4"
                                      class="form-control @error('keterangan') is-invalid @enderror"
                                      placeholder="Jelaskan alasan ketidakhadiran anak Anda secara detail..."
                                      required>{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>

                        <!-- Upload Bukti -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Upload Bukti</label>
                            <input type="file"
                                   name="bukti"
                                   class="form-control @error('bukti') is-invalid @enderror"
                                   accept=".jpg,.jpeg,.png,.pdf">
                            @error('bukti')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-paperclip me-1"></i>
                                Format: JPG, PNG, PDF. Maksimal 2MB.
                                <br>
                                Contoh: Surat dokter, surat keterangan, atau foto resep obat
                            </small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i>Ajukan Izin
                            </button>
                            <a href="{{ route('orang-tua.presensi.anak', $siswa->id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Info Box -->
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        Panduan Pengajuan Izin
                    </h6>
                    <ul class="mb-0 ps-3">
                        <li class="mb-2">Pengajuan izin akan divalidasi oleh Wali Kelas</li>
                        <li class="mb-2">Pastikan bukti yang dilampirkan jelas dan valid</li>
                        <li class="mb-2">Untuk sakit, lampirkan surat dokter atau foto resep</li>
                        <li class="mb-2">Untuk izin keperluan keluarga, berikan keterangan yang jelas</li>
                        <li class="mb-2">Ajukan izin maksimal H-1 atau pada hari yang sama jika mendadak</li>
                        <li>Pengajuan izin yang disetujui akan masuk ke rekap presensi anak</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
