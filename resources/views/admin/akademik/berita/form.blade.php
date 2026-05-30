@extends('layouts.sneat')

@section('title', isset($berita) ? 'Edit Berita' : 'Tambah Berita')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* --- STYLE FORM KONSISTEN --- */
.row-custom { display: flex; flex-wrap: wrap; margin: -12px; }
.col-main { flex: 0 0 66.666%; max-width: 66.666%; padding: 12px; }
.col-side { flex: 0 0 33.333%; max-width: 33.333%; padding: 12px; }

@media (max-width: 992px) { 
    .col-main, .col-side { flex: 0 0 100%; max-width: 100%; } 
}

/* Card Styling */
.card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border: none; }
.card-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background: #fff; border-radius: 12px 12px 0 0; }
.card-header h5 { margin: 0; font-size: 18px; font-weight: 600; color: #111827; display: flex; align-items: center; gap: 10px; }
.card-body { padding: 24px; }

/* Form Elements */
.form-group { margin-bottom: 20px; }
.form-label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: #374151; }
.form-label span.required { color: #dc2626; margin-left: 2px; }
.form-text { font-size: 12px; color: #6b7280; margin-top: 6px; }

.form-control {
    width: 100%; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px;
    font-size: 14px; transition: all 0.3s; background: #fff; box-sizing: border-box;
}
.form-control:focus { outline: none; border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
.form-control.is-invalid { border-color: #dc2626; background-color: #fef2f2; }
.invalid-feedback { font-size: 12px; color: #dc2626; margin-top: 6px; }

/* Buttons */
.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; transition: all 0.2s; }
.btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
.btn-primary:hover { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4); }
.btn-secondary { background: #f3f4f6; color: #374151; border: 1px solid #d1d5db; }
.btn-secondary:hover { background: #e5e7eb; border-color: #9ca3af; }

/* Custom Upload Box */
.image-upload-container {
    border: 2px dashed #d1d5db; border-radius: 12px; padding: 20px; text-align: center;
    background: #f9fafb; cursor: pointer; transition: all 0.3s; position: relative;
}
.image-upload-container:hover { border-color: #3b82f6; background: #eff6ff; }
.preview-box { margin-top: 15px; display: flex; justify-content: center; }
.preview-box img { max-width: 100%; max-height: 250px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }

/* Guide Panel */
.guide-list { padding-left: 20px; font-size: 14px; color: #4b5563; line-height: 1.6; }
.guide-list li { margin-bottom: 8px; }
.guide-divider { border-top: 1px solid #e5e7eb; margin: 16px 0; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div style="max-width: 1200px; margin: 0 auto;">
    <div class="row-custom">
        
        {{-- KOLOM KIRI: FORM --}}
        <div class="col-main">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas {{ isset($berita) ? 'fa-edit' : 'fa-plus-circle' }}" style="color: #3b82f6;"></i>
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

                        <div class="row-custom" style="margin: 0 -8px;">
                            <div style="flex: 1; padding: 0 8px;">
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
                            <div style="flex: 1; padding: 0 8px;">
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
                            <div style="position: relative;">
                                <i class="fas fa-link" style="position: absolute; left: 14px; top: 12px; color: #9ca3af;"></i>
                                <input type="url" 
                                       name="url_berita" 
                                       class="form-control @error('url_berita') is-invalid @enderror"
                                       style="padding-left: 40px;"
                                       value="{{ old('url_berita', $berita->url_berita ?? '') }}" 
                                       placeholder="https://website-luar.com/berita/..."
                                       required>
                            </div>
                            @error('url_berita') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group">
                            <label class="form-label">Gambar Thumbnail <span class="required">*</span></label>
                            
                            {{-- Area Klik untuk Upload --}}
                            <div class="image-upload-container" onclick="document.getElementById('fileInput').click()">
                                
                                {{-- ID fileInput dipanggil oleh Javascript di bawah --}}
                                <input type="file" 
                                       name="gambar_thumbnail" 
                                       id="fileInput"
                                       accept="image/*"
                                       style="display: none;">
                                
                                {{-- Placeholder --}}
                                <div id="uploadPlaceholder" style="{{ (isset($berita) && $berita->gambar_thumbnail) ? 'display:none;' : '' }}">
                                    <i class="fas fa-cloud-upload-alt" style="font-size: 32px; color: #9ca3af; margin-bottom: 10px;"></i>
                                    <p style="margin: 0; color: #6b7280; font-weight: 500;">Klik untuk upload gambar</p>
                                    <p style="margin: 4px 0 0; font-size: 12px; color: #9ca3af;">Format: JPG, PNG (Max: 2MB)</p>
                                </div>

                                {{-- Preview Image Box --}}
                                <div id="imagePreviewBox" class="preview-box" style="{{ (isset($berita) && $berita->gambar_thumbnail) ? 'display:flex;' : 'display:none;' }}">
                                    <img id="previewImg" 
                                         src="{{ (isset($berita) && $berita->gambar_thumbnail) ? $berita->gambar_url : '#' }}" 
                                         alt="Preview">
                                </div>
                            </div>
                            @if(isset($berita))
                                <div class="form-text text-center mt-2">Biarkan kosong jika tidak ingin mengubah gambar.</div>
                            @endif
                            @error('gambar_thumbnail') <div class="invalid-feedback" style="display:block;">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-group" style="background: #f9fafb; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb;">
                            <div class="row-custom" style="margin: 0 -8px;">
                                <div style="flex: 1; padding: 0 8px;">
                                    <label class="form-label">Status Publikasi</label>
                                    <select name="status" class="form-control">
                                        <option value="draft" {{ old('status', $berita->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                                        <option value="aktif" {{ old('status', $berita->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif (Tayang)</option>
                                        <option value="arsip" {{ old('status', $berita->status ?? '') == 'arsip' ? 'selected' : '' }}>Arsip</option>
                                    </select>
                                </div>
                                <div style="flex: 1; padding: 0 8px;">
                                    <label class="form-label">Urutan Tampil</label>
                                    <input type="number" name="urutan_tampil" class="form-control" value="{{ old('urutan_tampil', $berita->urutan_tampil ?? 999) }}">
                                </div>
                            </div>
                            <div style="margin-top: 12px;">
                                <label style="display: flex; align-items: center; cursor: pointer;">
                                    <input type="checkbox" name="is_featured" value="1" style="width: 16px; height: 16px;"
                                           {{ old('is_featured', $berita->is_featured ?? false) ? 'checked' : '' }}>
                                    <span style="margin-left: 8px; font-size: 14px; color: #374151;">Jadikan Berita Utama (Unggulan)</span>
                                </label>
                            </div>
                        </div>

                        <div style="margin-top: 24px; padding-top: 20px; border-top: 1px solid #f3f4f6; display: flex; justify-content: flex-end; gap: 12px;">
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
                <div class="card-header" style="background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);">
                    <h5 style="color: #b91c1c;">
                        <i class="fas fa-info-circle"></i> Panduan
                    </h5>
                </div>
                <div class="card-body">
                    <p style="font-size: 14px; color: #374151; font-weight: 500;">Cara mengisi berita:</p>
                    <ol class="guide-list">
                        <li><strong>Judul:</strong> Gunakan judul yang singkat dan menarik.</li>
                        <li><strong>Kategori:</strong> Pilih kelompok berita yang sesuai agar mudah dicari.</li>
                        <li><strong>Deskripsi Singkat:</strong> Tulis 1-2 kalimat teaser untuk menarik pembaca.</li>
                        <li><strong>URL:</strong> Masukkan link lengkap ke website sumber berita.</li>
                        <li><strong>Gambar:</strong> Upload gambar landscape (mendatar) agar tampilan rapi.</li>
                    </ol>

                    <div class="guide-divider"></div>

                    <p style="font-size: 14px; color: #374151; font-weight: 500;">Status:</p>
                    <ul class="guide-list" style="list-style-type: disc;">
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

{{-- SCRIPT: Dipindah ke sini agar pasti terbaca --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil elemen
        const fileInput = document.getElementById('fileInput');
        const imgPreview = document.getElementById('previewImg');
        const placeholder = document.getElementById('uploadPlaceholder');
        const previewBox = document.getElementById('imagePreviewBox');

        // Pastikan elemen ada sebelum menjalankan script
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                
                if (file) {
                    // Validasi ukuran
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Ukuran file terlalu besar! Maksimal 2MB.');
                        e.target.value = ''; // Reset input
                        return;
                    }

                    const reader = new FileReader();
                    
                    reader.onload = function(event) {
                        // Update gambar
                        imgPreview.src = event.target.result;
                        
                        // Tampilkan preview, sembunyikan placeholder
                        placeholder.style.display = 'none';
                        previewBox.style.display = 'flex';
                    }
                    
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endsection
