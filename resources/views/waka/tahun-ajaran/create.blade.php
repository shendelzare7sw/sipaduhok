@extends('layouts.sneat')

@section('title', 'Tambah Tahun Ajaran')

@section('page-title', 'Tambah Tahun Ajaran')
@section('page-subtitle', 'Form untuk menambah tahun ajaran baru')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/tahun-ajaran/form.css'])
@endsection

@section('content')
<div class="tahun-ajaran-form-page">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Tambah Tahun Ajaran</h5>
                    <a href="{{ route('waka.tahun-ajaran.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left btn-icon"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('waka.tahun-ajaran.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Nama Tahun Ajaran <span class="required-mark">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('nama_tahun_ajaran') is-invalid @enderror"
                                   name="nama_tahun_ajaran"
                                   value="{{ old('nama_tahun_ajaran') }}"
                                   placeholder="Contoh: 2024/2025"
                                   required>
                            @error('nama_tahun_ajaran')
                                <small class="form-error">{{ $message }}</small>
                            @enderror
                            <small class="form-hint">Format disarankan: YYYY/YYYY (Contoh: 2024/2025)</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Mulai <span class="required-mark">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('tanggal_mulai') is-invalid @enderror"
                                           name="tanggal_mulai"
                                           value="{{ old('tanggal_mulai') }}"
                                           required>
                                    @error('tanggal_mulai')
                                        <small class="form-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">
                                        Tanggal Selesai <span class="required-mark">*</span>
                                    </label>
                                    <input type="date"
                                           class="form-control @error('tanggal_selesai') is-invalid @enderror"
                                           name="tanggal_selesai"
                                           value="{{ old('tanggal_selesai') }}"
                                           required>
                                    @error('tanggal_selesai')
                                        <small class="form-error">{{ $message }}</small>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <hr class="ta-divider">

                        <div class="semester-settings">
                            <div class="setting-heading">
                                <i class="fas fa-calendar-alt"></i>
                                <strong>Pengaturan Periode Semester</strong>
                            </div>
                            <p class="setting-description">
                                Atur kapan semester genap dimulai. Semester ganjil: Tanggal Mulai sampai sebelum tanggal ini. Semester genap: Tanggal ini sampai Tanggal Selesai.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-play-circle label-icon-success"></i>
                                    Tanggal Mulai Semester Genap
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_mulai_genap') is-invalid @enderror"
                                       name="tanggal_mulai_genap"
                                       value="{{ old('tanggal_mulai_genap') }}"
                                       placeholder="Opsional">
                                @error('tanggal_mulai_genap')
                                    <small class="form-error">{{ $message }}</small>
                                @enderror
                                <small class="form-hint">
                                    Biasanya Januari atau Februari. Kosongkan untuk menggunakan perhitungan otomatis (Juli-Des = Ganjil, Jan-Jun = Genap).
                                </small>
                            </div>

                            <hr class="ta-divider is-dashed">
                            <div class="pts-heading">
                                <i class="fas fa-flag-checkered"></i>
                                <strong>Periode PTS (Penilaian Tengah Semester)</strong>
                            </div>
                            <p class="setting-description is-small">
                                Tanggal akhir PTS dipakai untuk menghitung kehadiran rapor PTS (sakit/izin/alpha). PAS otomatis pakai sisa semester sampai akhir. Kosongkan untuk pakai default 3 bulan pertama.
                            </p>
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-flag label-icon-info"></i>
                                    Tanggal Akhir PTS Ganjil
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_akhir_pts_ganjil') is-invalid @enderror"
                                       name="tanggal_akhir_pts_ganjil"
                                       value="{{ old('tanggal_akhir_pts_ganjil') }}">
                                @error('tanggal_akhir_pts_ganjil')
                                    <small class="form-error">{{ $message }}</small>
                                @enderror
                                <small class="form-hint">Contoh: 30 September.</small>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">
                                    <i class="fas fa-flag label-icon-info"></i>
                                    Tanggal Akhir PTS Genap
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_akhir_pts_genap') is-invalid @enderror"
                                       name="tanggal_akhir_pts_genap"
                                       value="{{ old('tanggal_akhir_pts_genap') }}">
                                @error('tanggal_akhir_pts_genap')
                                    <small class="form-error">{{ $message }}</small>
                                @enderror
                                <small class="form-hint">Contoh: 31 Maret.</small>
                            </div>
                        </div>

                        <hr class="ta-divider">

                        <div class="mb-3">
                            <label class="checkbox-row">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active') ? 'checked' : '' }}>
                                <div>
                                    <strong>Set sebagai Tahun Ajaran Aktif</strong>
                                    <small>
                                        Jika dicentang, tahun ajaran ini akan otomatis menjadi aktif dan tahun ajaran yang sedang aktif lainnya akan dinonaktifkan.
                                    </small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('waka.tahun-ajaran.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save btn-icon"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Pusat Bantuan</h5>
                </div>
                <div class="card-body">
                    <div class="info-box info-box-blue">
                        <div class="info-title">
                            <i class="fas fa-info-circle"></i> Informasi
                        </div>
                        <ul>
                            <li>Tahun ajaran menentukan periode akademik sistem.</li>
                            <li>Hanya boleh ada <strong>1 tahun ajaran aktif</strong> dalam satu waktu.</li>
                            <li>Pastikan tanggal selesai lebih besar dari tanggal mulai.</li>
                        </ul>
                    </div>

                    <div class="info-box info-box-yellow">
                        <div class="info-title">
                            <i class="fas fa-lightbulb"></i> Tips
                        </div>
                        <p>
                            Gunakan format penamaan yang konsisten seperti <strong>2024/2025</strong> atau <strong>Ganjil 2024</strong> untuk memudahkan pencarian data di kemudian hari.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
