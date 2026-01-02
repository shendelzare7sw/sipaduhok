@extends('layouts.sneat')

@section('title', 'Edit Pengajuan Izin')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div class="mb-3 mb-md-0">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('orang-tua.presensi.riwayat-izin', $presensi->siswa_id) }}">Riwayat Izin</a></li>
                    <li class="breadcrumb-item active">Edit Pengajuan</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1">Edit Pengajuan Izin</h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $presensi->siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $presensi->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
    </div>

    @php
        // Extract existing data
        $keteranganText = preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan);
        $keteranganText = preg_replace('/\s*-\s*Diajukan oleh orang tua.+/', '', $keteranganText);

        $buktiPath = null;
        if (preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan, $matches)) {
            $buktiPath = $matches[1];
        }

        $extension = $buktiPath ? pathinfo($buktiPath, PATHINFO_EXTENSION) : '';
        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $isPdf = strtolower($extension) === 'pdf';
    @endphp

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Form Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2 text-warning"></i>
                        Edit Pengajuan Izin/Sakit
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info d-flex align-items-start mb-4">
                        <i class="fas fa-info-circle me-2 mt-1"></i>
                        <div>
                            <strong>Perhatian:</strong> Anda dapat mengedit pengajuan izin yang belum divalidasi oleh wali kelas.
                            Pastikan data yang dimasukkan sudah benar.
                        </div>
                    </div>

                    <form action="{{ route('orang-tua.presensi.update-izin', $presensi->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Tanggal (Read Only) -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Tanggal
                            </label>
                            <input type="text"
                                   class="form-control"
                                   value="{{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}"
                                   readonly>
                            <small class="text-muted">Tanggal tidak dapat diubah</small>
                        </div>

                        <!-- Jenis -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                Jenis Izin <span class="text-danger">*</span>
                            </label>
                            <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                                <option value="">-- Pilih Jenis Izin --</option>
                                <option value="sakit" {{ old('jenis', $presensi->status) === 'sakit' ? 'selected' : '' }}>
                                    🏥 Sakit
                                </option>
                                <option value="izin" {{ old('jenis', $presensi->status) === 'izin' ? 'selected' : '' }}>
                                    <i class="fas fa-file-alt"></i> Izin
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
                                      required>{{ old('keterangan', trim($keteranganText)) }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maksimal 500 karakter</small>
                        </div>

                        <!-- Bukti Lama -->
                        @if($buktiPath)
                            <div class="mb-3">
                                <label class="form-label fw-bold">Bukti Saat Ini</label>
                                <div class="card border-primary">
                                    <div class="card-body p-3">
                                        @if($isImage)
                                            <div class="text-center mb-2">
                                                <img src="{{ asset('storage/' . $buktiPath) }}"
                                                     alt="Bukti"
                                                     class="img-fluid rounded"
                                                     style="max-height: 150px;">
                                            </div>
                                        @elseif($isPdf)
                                            <div class="text-center mb-2">
                                                <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                <p class="mt-2 mb-0"><small>File PDF</small></p>
                                            </div>
                                        @else
                                            <div class="text-center mb-2">
                                                <i class="fas fa-file fa-3x text-secondary"></i>
                                                <p class="mt-2 mb-0"><small>File {{ strtoupper($extension) }}</small></p>
                                            </div>
                                        @endif

                                        <div class="d-grid gap-2 mt-2">
                                            <a href="{{ asset('storage/' . $buktiPath) }}"
                                               target="_blank"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye me-1"></i>Lihat Bukti
                                            </a>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="hapus_bukti" value="1" id="hapus_bukti">
                                                <label class="form-check-label text-danger" for="hapus_bukti">
                                                    <i class="fas fa-trash me-1"></i>Hapus bukti ini
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Upload Bukti Baru -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ $buktiPath ? 'Ganti Bukti Baru' : 'Upload Bukti' }}</label>
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
                                @if($buktiPath)
                                    <br>Upload file baru akan mengganti bukti lama.
                                @endif
                            </small>
                        </div>

                        <!-- Buttons -->
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('orang-tua.presensi.riwayat-izin', $presensi->siswa_id) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-1"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
