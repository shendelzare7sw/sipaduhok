@extends('layouts.sneat')

@section('title', 'Ajukan Izin')
@section('page-title', 'Pengajuan Izin/Sakit')
@section('page-subtitle', 'Upload bukti izin atau surat sakit')

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@section('styles')
<style>
    .content-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    .content-card h2 {
        color: #4e73df;
        font-weight: 700;
        margin-bottom: 10px;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="content-card">
            <h2><i class="fas fa-file-medical"></i> Form Pengajuan Izin/Sakit</h2>
            <p style="color: #666; margin-bottom: 24px;">
                Silakan isi form di bawah ini untuk mengajukan izin ketidakhadiran.
                Pastikan melampirkan bukti yang valid.
            </p>

            <form action="{{ route('siswa.sia.presensi.store-izin') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Tanggal -->
                <div class="form-group mb-3">
                    <label class="form-label">Tanggal <span style="color: red;">*</span></label>
                    <input type="date"
                           name="tanggal"
                           class="form-control @error('tanggal') is-invalid @enderror"
                           value="{{ old('tanggal') }}"
                           required>
                    @error('tanggal')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jenis -->
                <div class="form-group mb-3">
                    <label class="form-label">Jenis <span style="color: red;">*</span></label>
                    <select name="jenis" class="form-select @error('jenis') is-invalid @enderror" required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="sakit" {{ old('jenis') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="izin" {{ old('jenis') === 'izin' ? 'selected' : '' }}>Izin</option>
                    </select>
                    @error('jenis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Keterangan -->
                <div class="form-group mb-3">
                    <label class="form-label">Keterangan <span style="color: red;">*</span></label>
                    <textarea name="keterangan"
                              rows="4"
                              class="form-control @error('keterangan') is-invalid @enderror"
                              placeholder="Jelaskan alasan ketidakhadiran..."
                              required>{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Upload Bukti -->
                <div class="form-group mb-4">
                    <label class="form-label">Upload Bukti (Surat Dokter/Surat Izin)</label>
                    <input type="file"
                           name="bukti"
                           class="form-control @error('bukti') is-invalid @enderror"
                           accept=".jpg,.jpeg,.png,.pdf">
                    <small class="form-text text-muted">
                        Format: JPG, PNG, PDF. Maksimal 2MB.
                        <br>Bisa berupa surat dokter atau surat izin yang ditandatangani orang tua.
                    </small>
                    @error('bukti')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Ajukan Izin
                    </button>
                    <a href="{{ route('siswa.sia.presensi.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>

        <!-- Info Box -->
        <div class="alert alert-info mt-4" role="alert">
            <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Informasi Penting</h5>
            <ul style="margin-bottom: 0; padding-left: 20px;">
                <li>Pengajuan izin akan divalidasi oleh Wali Kelas</li>
                <li>Pastikan bukti yang dilampirkan jelas dan valid</li>
                <li>Surat izin harus ditandatangani oleh orang tua</li>
                <li>Untuk sakit lebih dari 3 hari, wajib melampirkan surat dokter</li>
            </ul>
        </div>
    </div>
</div>
</div>

@endsection
