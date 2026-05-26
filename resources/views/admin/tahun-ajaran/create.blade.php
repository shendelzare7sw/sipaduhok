@extends('layouts.sneat')

@section('title', 'Tambah Tahun Ajaran')

@section('page-title', 'Tambah Tahun Ajaran')
@section('page-subtitle', 'Form untuk menambah tahun ajaran baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Additional Bootstrap-like styles copied from Index */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 12px 12px 0 0;
}

.card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

.card-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    background: white;
    border-radius: 0 0 12px 12px;
}

.text-muted { color: #6b7280; }
.bg-light { background: #f9fafb; }
.bg-white { background: white; }

/* Grid System */
.row {
    display: flex;
    flex-wrap: wrap;
    margin: -12px;
}

.col-md-8 {
    flex: 0 0 66.666667%;
    max-width: 66.666667%;
    padding: 12px;
}

.col-md-4 {
    flex: 0 0 33.333333%;
    max-width: 33.333333%;
    padding: 12px;
}

.col-md-6 {
    flex: 0 0 50%;
    max-width: 50%;
    padding: 12px; /* Adjusted padding for nested grid */
}

@media (max-width: 767.98px) {
    .col-md-8, .col-md-4, .col-md-6 { flex: 0 0 100%; max-width: 100%; }
}

/* Utilities */
.d-flex { display: flex !important; }
.justify-content-between { justify-content: space-between !important; }
.align-items-center { align-items: center !important; }
.gap-2 { gap: 8px !important; }
.mb-0 { margin-bottom: 0; }
.mb-3 { margin-bottom: 16px; }

/* Form Control Styling */
.form-label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    font-size: 14px;
    color: #374151;
}

.form-control {
    display: block;
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #fff;
    border: 1px solid #ced4da;
    border-radius: 6px;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    box-sizing: border-box; /* Important for width: 100% */
}

.form-control:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.is-invalid {
    border-color: #dc3545;
}

/* Button Styling */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    font-size: 14px;
    font-weight: 500;
    line-height: 1.5;
    text-align: center;
    text-decoration: none;
    cursor: pointer;
    border: 1px solid transparent;
    border-radius: 6px;
    transition: all .15s ease-in-out;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}
.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.btn-secondary {
    background: #6b7280;
    color: white;
    border-color: #6b7280;
}
.btn-secondary:hover {
    background: #4b5563;
    border-color: #4b5563;
}

/* Info Box Styling */
.info-box {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
}
.info-box-blue {
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    color: #1e3a8a;
}
.info-box-yellow {
    background: #fffbeb;
    border-left: 4px solid #f59e0b;
    color: #92400e;
}
.info-title {
    font-weight: 600;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 6px;
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    
    <div class="row">
        {{-- Kolom Kiri: Form --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Tahun Ajaran</h5>
                    <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        
                        {{-- Nama Tahun Ajaran --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Nama Tahun Ajaran <span style="color: #dc3545;">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_tahun_ajaran') is-invalid @enderror" 
                                   name="nama_tahun_ajaran" 
                                   value="{{ old('nama_tahun_ajaran') }}"
                                   placeholder="Contoh: 2024/2025"
                                   required>
                            @error('nama_tahun_ajaran')
                                <small style="color: #dc3545; display:block; margin-top:4px;">{{ $message }}</small>
                            @enderror
                            <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">Format disarankan: YYYY/YYYY (Contoh: 2024/2025)</small>
                        </div>

                        {{-- Row untuk Tanggal --}}
                        <div class="row" style="margin: 0 -12px;"> {{-- Reset margin for nested row --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Mulai <span style="color: #dc3545;">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                           name="tanggal_mulai" 
                                           value="{{ old('tanggal_mulai') }}"
                                           required>
                                    @error('tanggal_mulai')
                                        <small style="color: #dc3545;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Selesai <span style="color: #dc3545;">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                           name="tanggal_selesai" 
                                           value="{{ old('tanggal_selesai') }}"
                                           required>
                                    @error('tanggal_selesai')
                                        <small style="color: #dc3545;">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr style="border-top: 1px solid #e5e7eb; margin: 16px 0;">

                        {{-- Pengaturan Semester --}}
                        <div style="background: #f0fdf4; border-left: 4px solid #22c55e; padding: 16px; border-radius: 0 8px 8px 0; margin-bottom: 16px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                                <i class="fas fa-calendar-alt" style="color: #16a34a;"></i>
                                <strong style="color: #166534;">Pengaturan Periode Semester</strong>
                            </div>
                            <p class="text-muted" style="font-size: 13px; margin-bottom: 12px;">
                                Atur kapan semester genap dimulai. Semester ganjil: Tanggal Mulai → sebelum tanggal ini. Semester genap: Tanggal ini → Tanggal Selesai.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-play-circle" style="color: #16a34a; margin-right: 4px;"></i>
                                    Tanggal Mulai Semester Genap
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_mulai_genap') is-invalid @enderror"
                                       name="tanggal_mulai_genap"
                                       value="{{ old('tanggal_mulai_genap') }}"
                                       placeholder="Opsional">
                                @error('tanggal_mulai_genap')
                                    <small style="color: #dc3545;">{{ $message }}</small>
                                @enderror
                                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">
                                    Biasanya Januari atau Februari. Kosongkan untuk menggunakan perhitungan otomatis (Juli-Des = Ganjil, Jan-Jun = Genap).
                                </small>
                            </div>

                            <hr style="border-top: 1px dashed #d1d5db; margin: 14px 0;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 6px;">
                                <i class="fas fa-flag-checkered" style="color: #0891b2;"></i>
                                <strong style="color: #155e75; font-size: 13px;">Periode PTS (Penilaian Tengah Semester)</strong>
                            </div>
                            <p class="text-muted" style="font-size: 12px; margin-bottom: 12px;">
                                Tanggal akhir PTS dipakai untuk menghitung kehadiran rapor PTS (sakit/izin/alpha). PAS otomatis pakai sisa semester sampai akhir. Kosongkan untuk pakai default 3 bulan pertama.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flag" style="color: #0891b2; margin-right: 4px;"></i>
                                    Tanggal Akhir PTS Ganjil
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_akhir_pts_ganjil') is-invalid @enderror"
                                       name="tanggal_akhir_pts_ganjil"
                                       value="{{ old('tanggal_akhir_pts_ganjil') }}">
                                @error('tanggal_akhir_pts_ganjil')
                                    <small style="color: #dc3545;">{{ $message }}</small>
                                @enderror
                                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">
                                    Contoh: 30 September.
                                </small>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">
                                    <i class="fas fa-flag" style="color: #0891b2; margin-right: 4px;"></i>
                                    Tanggal Akhir PTS Genap
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_akhir_pts_genap') is-invalid @enderror"
                                       name="tanggal_akhir_pts_genap"
                                       value="{{ old('tanggal_akhir_pts_genap') }}">
                                @error('tanggal_akhir_pts_genap')
                                    <small style="color: #dc3545;">{{ $message }}</small>
                                @enderror
                                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">
                                    Contoh: 31 Maret.
                                </small>
                            </div>
                        </div>

                        <hr style="border-top: 1px solid #e5e7eb; margin: 16px 0;">

                        {{-- Checkbox Active --}}
                        <div class="mb-3">
                            <label style="display: flex; align-items: flex-start; gap: 10px; cursor: pointer;">
                                <input type="checkbox" 
                                       name="is_active" 
                                       value="1"
                                       style="width: 18px; height: 18px; margin-top: 2px;"
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <div>
                                    <strong style="color: #374151;">Set sebagai Tahun Ajaran Aktif</strong>
                                    <small class="text-muted" style="display: block; line-height: 1.4;">
                                        Jika dicentang, tahun ajaran ini akan otomatis menjadi aktif dan tahun ajaran yang sedang aktif lainnya akan dinonaktifkan.
                                    </small>
                                </div>
                            </label>
                        </div>

                    </div>
                    
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Informasi --}}
        <div class="col-md-4">
            {{-- Info Box --}}
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pusat Bantuan</h5>
                </div>
                <div class="card-body">
                    <div class="info-box info-box-blue">
                        <div class="info-title">
                            <i class="fas fa-info-circle"></i> Informasi
                        </div>
                        <ul style="margin: 0; padding-left: 20px; line-height: 1.6;">
                            <li>Tahun ajaran menentukan periode akademik sistem.</li>
                            <li>Hanya boleh ada <strong>1 tahun ajaran aktif</strong> dalam satu waktu.</li>
                            <li>Pastikan tanggal selesai lebih besar dari tanggal mulai.</li>
                        </ul>
                    </div>

                    <div class="info-box info-box-yellow">
                        <div class="info-title">
                            <i class="fas fa-lightbulb"></i> Tips
                        </div>
                        <p style="margin: 0; line-height: 1.6;">
                            Gunakan format penamaan yang konsisten seperti <strong>2024/2025</strong> atau <strong>Ganjil 2024</strong> untuk memudahkan pencarian data di kemudian hari.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection