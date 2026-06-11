@extends('layouts.sneat')

@section('title', isset($berita) ? 'Edit Berita' : 'Tambah Berita')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/akademik/berita/form.css'])
@endsection

@section('content')
<div class="admin-news-form-page">
<div class="admin-news-form-inner">
    <div class="row-custom">
        
        {{-- KOLOM KIRI: FORM --}}
        <div class="col-main">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas {{ isset($berita) ? 'fa-edit' : 'fa-plus-circle' }} icon-primary"></i>
                        {{ isset($berita) ? 'Edit Data Berita' : 'Tambah Berita Baru' }}
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ isset($berita) ? route('admin.akademik.berita.update', $berita->id) : route('admin.akademik.berita.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($berita))
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label class="form-label">Judul Berita <span class="required">*</span></label>
                            <input type="text" 
                                   name="judul" 
                                   class="form-control @error('judul') is-invalid @enderror" 
                                   value="{{ old('judul', $berita->judul ?? '') }}" 
                                   placeholder="Masukkan judul berita..."
                                   required>
                            @error('judul') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row-custom compact">
                            <div class="form-row-field">
                                <div class="form-group">
                                    <label class="form-label">Kategori <span class="required">*</span></label>
                                    <select name="kategori" class="form-control @error('kategori') is-invalid @enderror" required>
                                        <option value="">-- Pilih --</option>
                                        @foreach(['Kegiatan', 'Prestasi', 'Pengumuman', 'Artikel', 'Ujian'] as $cat)
                                            <option value="{{ strtolower($cat) }}" {{ (old('kategori', $berita->kategori ?? '') == strtolower($cat)) ? 'selected' : '' }}>
                                                {{ $cat }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('kategori') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="form-row-field">
                                <div class="form-group">
                                    <label class="form-label">Tanggal Berita <span class="required">*</span></label>
                                    <input type="date" 
                                           name="tanggal_berita" 
                                           class="form-control @error('tanggal_berita') is-invalid @enderror"
                                           value="{{ old('tanggal_berita', isset($berita) ? $berita->tanggal_berita->format('Y-m-d') : date('Y-m-d')) }}" 
                                           required>
                                    @error('tanggal_berita') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Deskripsi Singkat (Preview) <span class="required">*</span></label>
                            <textarea name="deskripsi_singkat" 
                                      rows="3" 
                                      class="form-control @error('deskripsi_singkat') is-invalid @enderror" 
                                      required>{{ old('deskripsi_singkat', $berita->deskripsi_singkat ?? '') }}</textarea>
                            <div class="form-text">Ringkasan yang akan muncul di kartu berita halaman depan.</div>
                            @error('deskripsi_singkat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">URL Berita Lengkap <span class="required">*</span></label>
                            <div class="url-input-wrap">
                                <i class="fas fa-link url-input-icon"></i>
                                <input type="url" 
                                       name="url_berita" 
                                       class="form-control url-input @error('url_berita') is-invalid @enderror"
                                       value="{{ old('url_berita', $berita->url_berita ?? '') }}" 
                                       placeholder="https://website-luar.com/berita/..."
                                       required>
                            </div>
                            @error('url_berita') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Gambar Thumbnail <span class="required">*</span></label>
                            
                            {{-- Area Klik untuk Upload --}}
                            <div class="image-upload-container" data-upload-trigger role="button" tabindex="0">
                                
                                {{-- ID fileInput dipanggil oleh Javascript di bawah --}}
                                <input type="file" 
                                       name="gambar_thumbnail" 
                                       id="fileInput"
                                       accept="image/*"
                                       class="news-file-input">
                                
                                {{-- Placeholder --}}
                                <div id="uploadPlaceholder" class="{{ (isset($berita) && $berita->gambar_thumbnail) ? 'is-hidden' : '' }}">
                                    <i class="fas fa-cloud-upload-alt upload-placeholder-icon"></i>
                                    <p class="upload-title">Klik untuk upload gambar</p>
                                    <p class="upload-hint">Format: JPG, PNG (Max: 2MB)</p>
                                </div>

                                {{-- Preview Image Box --}}
                                <div id="imagePreviewBox" class="preview-box {{ (isset($berita) && $berita->gambar_thumbnail) ? 'is-visible' : '' }}">
                                    <img id="previewImg" 
                                         src="{{ (isset($berita) && $berita->gambar_thumbnail) ? $berita->gambar_url : '#' }}" 
                                         alt="Preview">
                                </div>
                            </div>
                            @if(isset($berita))
                                <div class="form-text text-center mt-2">Biarkan kosong jika tidak ingin mengubah gambar.</div>
                            @endif
                            @error('gambar_thumbnail') <div class="invalid-feedback invalid-feedback-visible">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group news-options-panel">
                            <div class="row-custom compact">
                                <div class="form-row-field">
                                    <label class="form-label">Status Publikasi</label>
                                    <select name="status" class="form-control">
                                        <option value="draft" {{ old('status', $berita->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="aktif" {{ old('status', $berita->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif (Tayang)</option>
                                        <option value="arsip" {{ old('status', $berita->status ?? '') == 'arsip' ? 'selected' : '' }}>Arsip</option>
                                    </select>
                                </div>
                                <div class="form-row-field">
                                    <label class="form-label">Urutan Tampil</label>
                                    <input type="number" name="urutan_tampil" class="form-control" value="{{ old('urutan_tampil', $berita->urutan_tampil ?? 999) }}">
                                </div>
                            </div>
                            <div class="featured-toggle-wrap">
                                <label class="featured-toggle-label">
                                    <input type="checkbox" name="is_featured" value="1" class="featured-toggle"
                                           {{ old('is_featured', $berita->is_featured ?? false) ? 'checked' : '' }}>
                                    <span class="featured-toggle-text">Jadikan Berita Utama (Unggulan)</span>
                                </label>
                            </div>
                        </div>

                        <div class="form-footer-actions">
                            <a href="{{ route('admin.akademik.berita.index') }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> {{ isset($berita) ? 'Update Perubahan' : 'Simpan Berita' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: INFO PANEL --}}
        <div class="col-side">
            <div class="card">
                <div class="card-header guide-card-header">
                    <h5 class="guide-card-title">
                        <i class="fas fa-info-circle"></i> Panduan
                    </h5>
                </div>
                <div class="card-body">
                    <p class="guide-intro">Cara mengisi berita:</p>
                    <ol class="guide-list">
                        <li><strong>Judul:</strong> Gunakan judul yang singkat dan menarik.</li>
                        <li><strong>Kategori:</strong> Pilih kelompok berita yang sesuai agar mudah dicari.</li>
                        <li><strong>Deskripsi Singkat:</strong> Tulis 1-2 kalimat teaser untuk menarik pembaca.</li>
                        <li><strong>URL:</strong> Masukkan link lengkap ke website sumber berita.</li>
                        <li><strong>Gambar:</strong> Upload gambar landscape (mendatar) agar tampilan rapi.</li>
                    </ol>

                    <div class="guide-divider"></div>

                    <p class="guide-intro">Status:</p>
                    <ul class="guide-list guide-list-disc">
                        <li><strong>Draft:</strong> Disimpan tapi belum muncul di web.</li>
                        <li><strong>Aktif:</strong> Langsung muncul di website.</li>
                        <li><strong>Unggulan:</strong> Muncul di slider/bagian atas halaman depan.</li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
</div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/admin/akademik/berita/form.js'])
@endsection
