@extends('layouts.sneat')

@section('title', isset($pengumuman) ? 'Edit Pengumuman' : 'Tambah Pengumuman')

@section('page-title', isset($pengumuman) ? 'Edit Pengumuman' : 'Tambah Pengumuman')
@section('page-subtitle', 'Kelola pengumuman sekolah')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/akademik/pengumuman/form.css'])
@endsection

@section('content')
<div class="admin-announcement-form-page">
<div class="row">
    <div class="col-lg-8">
        <div class="content-card">
            <form action="{{ isset($pengumuman) ? route('admin.akademik.pengumuman.update', $pengumuman->id) : route('admin.akademik.pengumuman.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($pengumuman))
                    @method('PUT')
                @endif
                <input type="hidden" name="_return_url" value="{{ url()->previous(route('admin.akademik.pengumuman.index')) }}">

                <!-- Link ke Kalender (Opsional) -->
                <div class="form-group">
                    <label for="kalender_akademik_id" class="form-label">
                        Link ke Kalender Akademik <small class="text-muted">(Opsional)</small>
                    </label>
                    <select class="form-control @error('kalender_akademik_id') is-invalid @enderror" 
                            id="kalender_akademik_id" 
                            name="kalender_akademik_id">
                        <option value="">-- Pilih Kegiatan (Jika Ada) --</option>
                        @foreach($kalender as $k)
                            <option value="{{ $k->id }}" {{ old('kalender_akademik_id', $pengumuman->kalender_akademik_id ?? '') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kegiatan }} ({{ $k->tanggal_mulai->format('d M Y') }})
                            </option>
                        @endforeach
                    </select>
                    <small class="text-muted">Pilih jika pengumuman terkait kegiatan di kalender</small>
                    @error('kalender_akademik_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Judul -->
                <div class="form-group">
                    <label for="judul" class="form-label">
                        Judul Pengumuman <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('judul') is-invalid @enderror" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul', $pengumuman->judul ?? '') }}" 
                           placeholder="Contoh: Pengumuman Libur Semester"
                           required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Isi Pengumuman -->
                <div class="form-group">
                    <label for="isi_pengumuman" class="form-label">
                        Isi Pengumuman <span class="text-danger">*</span>
                    </label>
                    <textarea class="form-control @error('isi_pengumuman') is-invalid @enderror" 
                              id="isi_pengumuman" 
                              name="isi_pengumuman" 
                              rows="5"
                              required>{{ old('isi_pengumuman', $pengumuman->isi_pengumuman ?? '') }}</textarea>
                    @error('isi_pengumuman')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Tanggal & Prioritas -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_pengumuman" class="form-label">
                                Tanggal Pengumuman <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('tanggal_pengumuman') is-invalid @enderror" 
                                   id="tanggal_pengumuman" 
                                   name="tanggal_pengumuman" 
                                   value="{{ old('tanggal_pengumuman', isset($pengumuman) ? $pengumuman->tanggal_pengumuman->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                                   required>
                            @error('tanggal_pengumuman')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="prioritas" class="form-label">
                                Prioritas <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('prioritas') is-invalid @enderror" 
                                    id="prioritas" 
                                    name="prioritas" 
                                    required>
                                <option value="biasa" {{ old('prioritas', $pengumuman->prioritas ?? 'biasa') == 'biasa' ? 'selected' : '' }}>Biasa</option>
                                <option value="penting" {{ old('prioritas', $pengumuman->prioritas ?? '') == 'penting' ? 'selected' : '' }}>Penting</option>
                                <option value="mendesak" {{ old('prioritas', $pengumuman->prioritas ?? '') == 'mendesak' ? 'selected' : '' }}>Mendesak</option>
                            </select>
                            @error('prioritas')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Lampiran -->
                <div class="form-group">
                    <label for="lampiran_surat" class="form-label">
                        Lampiran Surat <small class="text-muted">(PDF, max 5MB)</small>
                    </label>
                    
                    @if(isset($pengumuman) && $pengumuman->lampiran_surat)
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $pengumuman->lampiran_surat) }}" target="_blank" class="btn btn-sm btn-info">
                                <i class="fas fa-file-pdf me-1"></i>Lihat Lampiran
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
                        <option value="aktif" {{ old('status', $pengumuman->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ old('status', $pengumuman->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="arsip" {{ old('status', $pengumuman->status ?? '') == 'arsip' ? 'selected' : '' }}>Arsip</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between announcement-form-actions">
                    <a href="{{ url()->previous(route('admin.akademik.pengumuman.index')) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ isset($pengumuman) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="col-lg-4">
        <div class="content-card">
            <h3 class="announcement-info-title">
                <i class="fas fa-info-circle me-2 announcement-info-icon"></i>Informasi
            </h3>
            
            <div class="announcement-info-content">
                <p><strong>Auto-Generate:</strong></p>
                <p>Pengumuman otomatis dibuat <strong>3 hari sebelum</strong> kegiatan di kalender akademik.</p>
                
                <hr>
                
                <p><strong>Sinkronisasi:</strong></p>
                <ul>
                    <li>Kalender → Pengumuman: <strong>Sinkron</strong></li>
                    <li>Pengumuman → Kalender: <strong>Tidak sinkron</strong></li>
                </ul>
                <p class="small text-muted">Ubah pengumuman tidak mengubah kalender</p>
                
                <hr>
                
                <p><strong>Prioritas:</strong></p>
                <ul class="announcement-priority-list">
                    <li><strong>Biasa:</strong> Info umum</li>
                    <li><strong>Penting:</strong> Perlu perhatian</li>
                    <li><strong>Mendesak:</strong> Segera dibaca</li>
                </ul>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
