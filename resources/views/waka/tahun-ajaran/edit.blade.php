@extends('layouts.sneat')

@section('title', 'Edit Tahun Ajaran')

@section('page-title', 'Edit Tahun Ajaran')
@section('page-subtitle', 'Form untuk mengubah data tahun ajaran')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/tahun-ajaran/form.css'])
@endsection

@section('content')
<div class="tahun-ajaran-form-page is-edit">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Form Edit Tahun Ajaran</h5>
                    <a href="{{ route('waka.tahun-ajaran.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left btn-icon"></i> Kembali
                    </a>
                </div>

                <form action="{{ route('waka.tahun-ajaran.update', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">
                                Tahun Ajaran <span class="required-mark">*</span>
                            </label>
                            <input type="text"
                                   class="form-control @error('nama_tahun_ajaran') is-invalid @enderror"
                                   name="nama_tahun_ajaran"
                                   value="{{ old('nama_tahun_ajaran', $tahunAjaran->nama_tahun_ajaran) }}"
                                   placeholder="Contoh: 2024/2025"
                                   required>
                            @error('nama_tahun_ajaran')
                                <small class="form-error">{{ $message }}</small>
                            @enderror
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
                                           value="{{ old('tanggal_mulai', $tahunAjaran->tanggal_mulai) }}"
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
                                           value="{{ old('tanggal_selesai', $tahunAjaran->tanggal_selesai) }}"
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
                                       value="{{ old('tanggal_mulai_genap', $tahunAjaran->tanggal_mulai_genap ? $tahunAjaran->tanggal_mulai_genap->format('Y-m-d') : '') }}">
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
                                       value="{{ old('tanggal_akhir_pts_ganjil', $tahunAjaran->tanggal_akhir_pts_ganjil ? $tahunAjaran->tanggal_akhir_pts_ganjil->format('Y-m-d') : '') }}">
                                @error('tanggal_akhir_pts_ganjil')
                                    <small class="form-error">{{ $message }}</small>
                                @enderror
                                <small class="form-hint">
                                    Contoh: 30 September. Setelah tanggal ini sampai sebelum semester genap mulai = periode PAS Ganjil.
                                </small>
                            </div>
                            <div class="mb-0">
                                <label class="form-label">
                                    <i class="fas fa-flag label-icon-info"></i>
                                    Tanggal Akhir PTS Genap
                                </label>
                                <input type="date"
                                       class="form-control @error('tanggal_akhir_pts_genap') is-invalid @enderror"
                                       name="tanggal_akhir_pts_genap"
                                       value="{{ old('tanggal_akhir_pts_genap', $tahunAjaran->tanggal_akhir_pts_genap ? $tahunAjaran->tanggal_akhir_pts_genap->format('Y-m-d') : '') }}">
                                @error('tanggal_akhir_pts_genap')
                                    <small class="form-error">{{ $message }}</small>
                                @enderror
                                <small class="form-hint">
                                    Contoh: 31 Maret. Setelah tanggal ini sampai akhir tahun ajaran = periode PAS Genap.
                                </small>
                            </div>
                        </div>

                        <hr class="ta-divider">

                        <div class="mb-3">
                            <label class="checkbox-row">
                                <input type="checkbox"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $tahunAjaran->is_active) ? 'checked' : '' }}>
                                <div>
                                    <strong>Set sebagai Tahun Ajaran Aktif</strong>
                                    <small>Mengaktifkan tahun ajaran ini akan menonaktifkan tahun ajaran aktif lainnya.</small>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="card-footer d-flex justify-content-end gap-2">
                        <a href="{{ route('waka.tahun-ajaran.index') }}" class="btn btn-secondary">
                            Batal
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save btn-icon"></i> Update Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-md-4">
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
                            <p>
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
                            <p>
                                Belum ada kelas yang menggunakan tahun ajaran ini. Anda dapat mengubah data dengan aman.
                            </p>
                        </div>
                    @endif

                    <small class="meta-list">
                        <i class="fas fa-clock"></i> Dibuat: {{ $tahunAjaran->created_at->format('d M Y') }}<br>
                        <i class="fas fa-history"></i> Update Terakhir: {{ $tahunAjaran->updated_at->format('d M Y') }}
                    </small>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="info-title notes-title">
                        <i class="fas fa-info-circle"></i> Catatan
                    </div>
                    <ul class="notes-list text-muted">
                        <li>Pastikan Tanggal Selesai lebih besar dari Tanggal Mulai.</li>
                        <li>Format nama disarankan konsisten (contoh: 2024/2025).</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
