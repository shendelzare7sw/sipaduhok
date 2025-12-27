@extends('layouts.sneat')

@section('title', isset($kalender) ? 'Edit Kegiatan' : 'Tambah Kegiatan')

@section('page-title', isset($kalender) ? 'Edit Kegiatan' : 'Tambah Kegiatan')
@section('page-subtitle', 'Kalender Akademik')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="row">
    <div class="col-lg-8">
        <div class="content-card">
            <form action="{{ isset($kalender) ? route('sekretaris.kalender.update', $kalender->id) : route('sekretaris.kalender.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($kalender))
                    @method('PUT')
                @endif

                <!-- Nama Kegiatan -->
                <div class="form-group">
                    <label for="nama_kegiatan" class="form-label">
                        Nama Kegiatan <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('nama_kegiatan') is-invalid @enderror" 
                           id="nama_kegiatan" 
                           name="nama_kegiatan" 
                           value="{{ old('nama_kegiatan', $kalender->nama_kegiatan ?? '') }}" 
                           placeholder="Contoh: Field Trip ke Museum"
                           required>
                    @error('nama_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Jenis Kegiatan -->
                <div class="form-group">
                    <label for="jenis_kegiatan" class="form-label">
                        Jenis Kegiatan <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('jenis_kegiatan') is-invalid @enderror" 
                            id="jenis_kegiatan" 
                            name="jenis_kegiatan" 
                            required>
                        <option value="">-- Pilih Jenis --</option>
                        <option value="field_trip" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'field_trip' ? 'selected' : '' }}>Field Trip</option>
                        <option value="outing" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'outing' ? 'selected' : '' }}>Outing</option>
                        <option value="live_in" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'live_in' ? 'selected' : '' }}>Live In</option>
                        <option value="hokfest" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'hokfest' ? 'selected' : '' }}>HOK Fest</option>
                        <option value="pts" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'pts' ? 'selected' : '' }}>PTS (Penilaian Tengah Semester)</option>
                        <option value="pas" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'pas' ? 'selected' : '' }}>PAS (Penilaian Akhir Semester)</option>
                        <option value="libur" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'libur' ? 'selected' : '' }}>Libur</option>
                        <option value="ujian" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'ujian' ? 'selected' : '' }}>Ujian</option>
                        <option value="acara_sekolah" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'acara_sekolah' ? 'selected' : '' }}>Acara Sekolah</option>
                        <option value="lainnya" {{ old('jenis_kegiatan', $kalender->jenis_kegiatan ?? '') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    @error('jenis_kegiatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tanggal -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_mulai" class="form-label">
                                Tanggal Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('tanggal_mulai') is-invalid @enderror" 
                                   id="tanggal_mulai" 
                                   name="tanggal_mulai" 
                                   value="{{ old('tanggal_mulai', isset($kalender) ? $kalender->tanggal_mulai->format('Y-m-d') : (request('tanggal') ?? '')) }}" 
                                   required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_selesai" class="form-label">
                                Tanggal Selesai <small class="text-muted">(Opsional untuk 1 hari)</small>
                            </label>
                            <input type="date" 
                                   class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                   id="tanggal_selesai" 
                                   name="tanggal_selesai" 
                                   value="{{ old('tanggal_selesai', isset($kalender) && $kalender->tanggal_selesai ? $kalender->tanggal_selesai->format('Y-m-d') : '') }}">
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Waktu -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="waktu_mulai" class="form-label">
                                Waktu Mulai <small class="text-muted">(Opsional)</small>
                            </label>
                            <input type="time" 
                                   class="form-control @error('waktu_mulai') is-invalid @enderror" 
                                   id="waktu_mulai" 
                                   name="waktu_mulai" 
                                   value="{{ old('waktu_mulai', $kalender->waktu_mulai ?? '') }}">
                            @error('waktu_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="waktu_selesai" class="form-label">
                                Waktu Selesai <small class="text-muted">(Opsional)</small>
                            </label>
                            <input type="time" 
                                   class="form-control @error('waktu_selesai') is-invalid @enderror" 
                                   id="waktu_selesai" 
                                   name="waktu_selesai" 
                                   value="{{ old('waktu_selesai', $kalender->waktu_selesai ?? '') }}">
                            @error('waktu_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Keterangan -->
                <div class="form-group">
                    <label for="keterangan" class="form-label">
                        Keterangan <small class="text-muted">(Opsional)</small>
                    </label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror" 
                              id="keterangan" 
                              name="keterangan" 
                              rows="3"
                              placeholder="Detail kegiatan, lokasi, atau informasi tambahan">{{ old('keterangan', $kalender->keterangan ?? '') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Lampiran Surat -->
                <div class="form-group">
                    <label for="lampiran_surat" class="form-label">
                        Lampiran Surat <small class="text-muted">(PDF, max 5MB)</small>
                    </label>
                    
                    @if(isset($kalender) && $kalender->lampiran_surat)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $kalender->lampiran_surat) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-pdf me-1"></i>Lihat Lampiran Saat Ini
                            </a>
                            <p class="small text-muted mt-2">Upload file baru untuk mengganti</p>
                        </div>
                    @endif

                    <input type="file" 
                           class="form-control @error('lampiran_surat') is-invalid @enderror" 
                           id="lampiran_surat" 
                           name="lampiran_surat" 
                           accept=".pdf">
                    @error('lampiran_surat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-group">
                    <label for="status" class="form-label">
                        Status <span class="text-danger">*</span>
                    </label>
                    <select class="form-control @error('status') is-invalid @enderror" 
                            id="status" 
                            name="status" 
                            required>
                        <option value="aktif" {{ old('status', $kalender->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ old('status', $kalender->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="selesai" {{ old('status', $kalender->status ?? '') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between" style="margin-top: 24px;">
                    <a href="{{ route('sekretaris.kalender.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ isset($kalender) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="col-lg-4">
        <div class="content-card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                <i class="fas fa-info-circle me-2" style="color: #165fac;"></i>Informasi
            </h3>
            
            <div style="font-size: 14px; color: #666; line-height: 1.6;">
                <p><strong>Cara Kerja:</strong></p>
                <ul style="padding-left: 20px; margin-bottom: 12px;">
                    <li>Kegiatan akan muncul di kalender dashboard</li>
                    <li>Pengumuman otomatis dibuat <strong>H-3</strong></li>
                    <li>Lampiran bisa PDF atau link URL</li>
                </ul>
                
                <hr style="margin: 16px 0;">
                
                <p><strong>Jenis Kegiatan:</strong></p>
                <ul style="padding-left: 20px; margin-bottom: 12px;">
                    <li><strong>Field Trip:</strong> Kunjungan edukatif</li>
                    <li><strong>Outing:</strong> Kegiatan di luar sekolah</li>
                    <li><strong>Live In:</strong> Menginap bersama</li>
                    <li><strong>HOK Fest:</strong> Festival sekolah</li>
                    <li><strong>PTS/PAS:</strong> Ujian</li>
                </ul>
                
                <hr style="margin: 16px 0;">
                
                <p style="margin-bottom: 0;">
                    <i class="fas fa-lightbulb" style="color: #ffc107;"></i>
                    <strong> Tips:</strong> Isi waktu untuk kegiatan yang punya jam spesifik. Kosongkan untuk kegiatan seharian.
                </p>
            </div>
        </div>
    </div>
</div>
</div>
@endsection