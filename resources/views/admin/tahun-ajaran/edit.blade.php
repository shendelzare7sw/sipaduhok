@extends('layouts.sneat')

@section('title', 'Edit Tahun Ajaran')

@section('page-title', 'Edit Tahun Ajaran')
@section('page-subtitle', 'Form untuk mengubah data tahun ajaran')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Standard Styles (Sama dengan Index/Create) */
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
.card-header h5 { margin: 0; font-size: 16px; font-weight: 600; }
.card-body { padding: 20px; }
.card-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    background: white;
    border-radius: 0 0 12px 12px;
}

/* Grid System */
.row { display: flex; flex-wrap: wrap; margin: -12px; }
.col-md-8 { flex: 0 0 66.666667%; max-width: 66.666667%; padding: 12px; }
.col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 12px; }
.col-md-6 { flex: 0 0 50%; max-width: 50%; padding: 12px; }

@media (max-width: 767.98px) {
    .col-md-8, .col-md-4, .col-md-6 { flex: 0 0 100%; max-width: 100%; }
}

/* Form Elements */
.form-label { display: block; margin-bottom: 8px; font-weight: 500; font-size: 14px; color: #374151; }
.form-control {
    display: block; width: 100%; padding: 8px 12px; font-size: 14px; font-weight: 400;
    color: #212529; background-color: #fff; border: 1px solid #ced4da; border-radius: 6px;
    transition: border-color .15s ease-in-out; box-sizing: border-box;
}
.form-control:focus { border-color: #f59e0b; outline: 0; box-shadow: 0 0 0 0.25rem rgba(245, 158, 11, 0.25); } /* Focus warna orange utk edit */
.is-invalid { border-color: #dc3545; }

/* Buttons */
.btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 8px 16px; font-size: 14px; font-weight: 500; text-decoration: none;
    cursor: pointer; border-radius: 6px; border: 1px solid transparent; transition: all .15s;
}
.btn-warning { background-color: #f59e0b; color: #fff; border-color: #f59e0b; }
.btn-warning:hover { background-color: #d97706; border-color: #d97706; }
.btn-secondary { background: #6b7280; color: white; border-color: #6b7280; }
.btn-secondary:hover { background: #4b5563; }

/* Utils */
.d-flex { display: flex !important; }
.justify-content-between { justify-content: space-between !important; }
.align-items-center { align-items: center !important; }
.gap-2 { gap: 8px !important; }
.mb-0 { margin-bottom: 0; }
.mb-3 { margin-bottom: 16px; }
.text-muted { color: #6b7280; }

/* Info Box */
.info-box { padding: 16px; border-radius: 8px; margin-bottom: 16px; font-size: 14px; }
.info-box-blue { background: #eff6ff; border-left: 4px solid #3b82f6; color: #1e3a8a; }
.info-box-orange { background: #fff7ed; border-left: 4px solid #f97316; color: #9a3412; }
.info-title { font-weight: 600; margin-bottom: 8px; display: flex; align-items: center; gap: 6px; }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    
    <div class="row">
        {{-- Kolom Kiri: Form Edit --}}
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Tahun Ajaran</h5>
                    <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left" style="margin-right: 4px;"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('admin.tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        {{-- Nama Tahun Ajaran --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Tahun Ajaran <span style="color: #dc3545;">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('nama_tahun_ajaran') is-invalid @enderror" 
                                   name="nama_tahun_ajaran" 
                                   value="{{ old('nama_tahun_ajaran', $tahunAjaran->nama_tahun_ajaran) }}"
                                   placeholder="Contoh: 2024/2025"
                                   required>
                            @error('nama_tahun_ajaran')
                                <small style="color: #dc3545; display:block; margin-top:4px;">{{ $message }}</small>
                            @enderror
                        </div>

                        {{-- Row untuk Tanggal --}}
                        <div class="row" style="margin: 0 -12px;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Mulai <span style="color: #dc3545;">*</span>
                                    </label>
                                    <input type="date" 
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                           name="tanggal_mulai" 
                                           value="{{ old('tanggal_mulai', $tahunAjaran->tanggal_mulai) }}"
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
                                           value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai) }}"
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
                                       value="{{ old('tanggal_mulai_genap', $tahunAjaran->tanggal_mulai_genap ? $tahunAjaran->tanggal_mulai_genap->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_akhir_pts_ganjil', $tahunAjaran->tanggal_akhir_pts_ganjil ? $tahunAjaran->tanggal_akhir_pts_ganjil->format('Y-m-d') : '') }}">
                                @error('tanggal_akhir_pts_ganjil')
                                    <small style="color: #dc3545;">{{ $message }}</small>
                                @enderror
                                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">
                                    Contoh: 30 September. Setelah tanggal ini sampai sebelum semester genap mulai = periode PAS Ganjil.
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
                                       value="{{ old('tanggal_akhir_pts_genap', $tahunAjaran->tanggal_akhir_pts_genap ? $tahunAjaran->tanggal_akhir_pts_genap->format('Y-m-d') : '') }}">
                                @error('tanggal_akhir_pts_genap')
                                    <small style="color: #dc3545;">{{ $message }}</small>
                                @enderror
                                <small class="text-muted" style="display: block; margin-top: 4px; font-size: 12px;">
                                    Contoh: 31 Maret. Setelah tanggal ini sampai akhir tahun ajaran = periode PAS Genap.
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
                                       {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }}>
                                <div>
                                    <strong style="color: #374151;">Set sebagai Tahun Ajaran Aktif</strong>
                                    <small class="text-muted" style="display: block; line-height: 1.4;">
                                        Mengaktifkan tahun ajaran ini akan menonaktifkan tahun ajaran aktif lainnya.
                                    </small>
                                </div>
                            </label>
                        </div>

                    </div>
                    
                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kolom Kanan: Informasi & Warning --}}
        <div class="col-md-4">
            
            {{-- Warning Box jika data digunakan --}}
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Status Data</h5>
                </div>
                <div class="card-body">
                    
                    @if($tahunAjaran->kelas->count() > 0)
                        <div class="info-box info-box-orange">
                            <div class="info-title">
                                <i class="fas fa-exclamation-triangle"></i> Perhatian
                            </div>
                            <p style="margin: 0; line-height: 1.6;">
                                Tahun ajaran ini sedang digunakan oleh <strong>{{ $tahunAjaran->kelas->count() }} kelas</strong>.
                                <br><br>
                                Perubahan pada tanggal atau nama tahun ajaran akan berdampak pada laporan dan data kelas yang terkait.
                            </p>
                        </div>
                    @else
                        <div class="info-box info-box-blue">
                            <div class="info-title">
                                <i class="fas fa-check-circle"></i> Aman untuk diedit
                            </div>
                            <p style="margin: 0; line-height: 1.6;">
                                Belum ada kelas yang menggunakan tahun ajaran ini. Anda dapat mengubah data dengan aman.
                            </p>
                        </div>
                    @endif

                    <div style="margin-top: 16px;">
                        <small class="text-muted">
                            <i class="fas fa-clock"></i> Dibuat: {{ $tahunAjaran->created_at->format('d M Y') }}<br>
                            <i class="fas fa-history"></i> Update Terakhir: {{ $tahunAjaran->updated_at->format('d M Y') }}
                        </small>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-body">
                     <div class="info-title" style="color: #374151;">
                        <i class="fas fa-info-circle"></i> Catatan
                    </div>
                    <ul style="margin: 0; padding-left: 20px; line-height: 1.6; font-size: 14px; color: #6b7280;">
                        <li>Pastikan Tanggal Selesai lebih besar dari Tanggal Mulai.</li>
                        <li>Format nama disarankan konsisten (contoh: 2024/2025).</li>
                    </ul>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection