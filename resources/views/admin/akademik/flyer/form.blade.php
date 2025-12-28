@extends('layouts.sneat')

@section('title', isset($flyer) ? 'Edit Flyer' : 'Tambah Flyer')

@section('page-title', isset($flyer) ? 'Edit Flyer' : 'Tambah Flyer')
@section('page-subtitle', 'Pop-up iklan untuk siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.content-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    padding: 24px;
    margin-bottom: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.invalid-feedback {
    color: #ef4444;
    font-size: 13px;
    margin-top: 4px;
    display: block;
}

.btn {
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #4b5563;
}

.img-thumbnail {
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 4px;
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="row">
    <div class="col-lg-8">
        <div class="content-card">
            <form action="{{ isset($flyer) ? route('admin.akademik.flyer.update', $flyer->id) : route('admin.akademik.flyer.store') }}" 
                  method="POST" 
                  enctype="multipart/form-data">
                @csrf
                @if(isset($flyer))
                    @method('PUT')
                @endif

                <!-- Judul -->
                <div class="form-group">
                    <label for="judul" class="form-label">
                        Judul Flyer <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('judul') is-invalid @enderror" 
                           id="judul" 
                           name="judul" 
                           value="{{ old('judul', $flyer->judul ?? '') }}" 
                           placeholder="Contoh: Hear For You - Konseling Gratis"
                           required>
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Deskripsi -->
                <div class="form-group">
                    <label for="deskripsi" class="form-label">
                        Deskripsi <small class="text-muted">(Opsional)</small>
                    </label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror" 
                              id="deskripsi" 
                              name="deskripsi" 
                              rows="3"
                              placeholder="Deskripsi singkat tentang flyer ini">{{ old('deskripsi', $flyer->deskripsi ?? '') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Gambar Flyer -->
                <div class="form-group">
                    <label for="gambar_flyer" class="form-label">
                        Gambar Flyer <span class="text-danger">*</span>
                        <small class="text-muted">(JPG, PNG, max 2MB)</small>
                    </label>

                    <div class="mb-3" id="imagePreviewContainer" style="{{ isset($flyer) && $flyer->gambar_flyer ? '' : 'display: none;' }}">
                        <img id="imagePreview"
                             src="{{ isset($flyer) && $flyer->gambar_flyer ? $flyer->gambar_url : '' }}"
                             alt="Preview"
                             class="img-thumbnail"
                             style="max-height: 250px; display: block;">
                        <p class="small text-muted mt-2">
                            <span id="previewLabel">{{ isset($flyer) ? 'Upload gambar baru untuk mengganti' : 'Preview gambar yang akan diupload' }}</span>
                        </p>
                    </div>

                    <input type="file"
                           class="form-control @error('gambar_flyer') is-invalid @enderror"
                           id="gambar_flyer"
                           name="gambar_flyer"
                           accept="image/*"
                           onchange="previewImage(this)"
                           {{ isset($flyer) ? '' : 'required' }}>
                    @error('gambar_flyer')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Link URL -->
                <div class="form-group">
                    <label for="link_url" class="form-label">
                        Link URL <small class="text-muted">(Opsional)</small>
                    </label>
                    <input type="url" 
                           class="form-control @error('link_url') is-invalid @enderror" 
                           id="link_url" 
                           name="link_url" 
                           value="{{ old('link_url', $flyer->link_url ?? '') }}" 
                           placeholder="https://example.com">
                    <small class="text-muted">Link untuk "Selengkapnya" di pop-up</small>
                    @error('link_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Periode Tampil -->
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
                                   value="{{ old('tanggal_mulai', isset($flyer) ? $flyer->tanggal_mulai->format('Y-m-d') : now()->format('Y-m-d')) }}" 
                                   required>
                            @error('tanggal_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="tanggal_selesai" class="form-label">
                                Tanggal Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('tanggal_selesai') is-invalid @enderror" 
                                   id="tanggal_selesai" 
                                   name="tanggal_selesai" 
                                   value="{{ old('tanggal_selesai', isset($flyer) ? $flyer->tanggal_selesai->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}" 
                                   required>
                            @error('tanggal_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Target & Urutan -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="target_audience" class="form-label">
                                Target Audience <span class="text-danger">*</span>
                            </label>
                            <select class="form-control @error('target_audience') is-invalid @enderror" 
                                    id="target_audience" 
                                    name="target_audience" 
                                    required>
                                <option value="siswa" {{ old('target_audience', $flyer->target_audience ?? 'siswa') == 'siswa' ? 'selected' : '' }}>Siswa</option>
                                <option value="guru" {{ old('target_audience', $flyer->target_audience ?? '') == 'guru' ? 'selected' : '' }}>Guru</option>
                                <option value="wali_kelas" {{ old('target_audience', $flyer->target_audience ?? '') == 'wali_kelas' ? 'selected' : '' }}>Wali Kelas</option>
                                <option value="orang_tua" {{ old('target_audience', $flyer->target_audience ?? '') == 'orang_tua' ? 'selected' : '' }}>Orang Tua</option>
                                <option value="semua" {{ old('target_audience', $flyer->target_audience ?? '') == 'semua' ? 'selected' : '' }}>Semua</option>
                            </select>
                            @error('target_audience')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="urutan_tampil" class="form-label">
                                Urutan Tampil <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('urutan_tampil') is-invalid @enderror" 
                                   id="urutan_tampil" 
                                   name="urutan_tampil" 
                                   value="{{ old('urutan_tampil', $flyer->urutan_tampil ?? 1) }}" 
                                   min="1"
                                   required>
                            <small class="text-muted">1 = tampil pertama, 2 = kedua, dst.</small>
                            @error('urutan_tampil')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
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
                        <option value="aktif" {{ old('status', $flyer->status ?? 'aktif') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                        <option value="draft" {{ old('status', $flyer->status ?? '') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="nonaktif" {{ old('status', $flyer->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="d-flex justify-content-between" style="margin-top: 24px;">
                    <a href="{{ route('admin.akademik.flyer.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ isset($flyer) ? 'Update' : 'Simpan' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="col-lg-4">
        <div class="content-card">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                <i class="fas fa-info-circle me-2" style="color: #165fac;"></i>Panduan
            </h3>
            
            <div style="font-size: 14px; color: #666; line-height: 1.6;">
                <p><strong>Organisasi di PKBM HOK:</strong></p>
                <ul style="padding-left: 20px; margin-bottom: 12px;">
                    <li>Hear For You</li>
                    <li>HOK EduLab Cafe</li>
                    <li>Taman Bacaan Masyarakat</li>
                </ul>
                
                <hr style="margin: 16px 0;">
                
                <p><strong>Cara Kerja:</strong></p>
                <ul style="padding-left: 20px; margin-bottom: 12px;">
                    <li>Flyer tampil sebagai <strong>pop-up</strong> saat siswa login</li>
                    <li>Tampil bergantian sesuai urutan</li>
                    <li>Hanya flyer aktif & dalam periode yang tampil</li>
                </ul>
                
                <hr style="margin: 16px 0;">
                
                <p style="margin-bottom: 0;">
                    <i class="fas fa-lightbulb" style="color: #ffc107;"></i>
                    <strong> Tips:</strong> Buat gambar dengan rasio 16:9 atau 4:3 untuk hasil terbaik.
                </p>
            </div>
        </div>

        <div class="content-card" id="sidePreviewCard" style="margin-top: 16px; {{ isset($flyer) && $flyer->gambar_flyer ? '' : 'display: none;' }}">
            <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">
                <i class="fas fa-eye me-2" style="color: #28a745;"></i>Preview
            </h3>
            <img id="sidePreviewImage"
                 src="{{ isset($flyer) && $flyer->gambar_flyer ? $flyer->gambar_url : '' }}"
                 alt="Preview"
                 style="width: 100%; border-radius: 8px;">
        </div>
    </div>
</div>
</div>

<script>
function previewImage(input) {
    const previewContainer = document.getElementById('imagePreviewContainer');
    const previewImage = document.getElementById('imagePreview');
    const previewLabel = document.getElementById('previewLabel');
    const sidePreviewCard = document.getElementById('sidePreviewCard');
    const sidePreviewImage = document.getElementById('sidePreviewImage');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            // Update main preview
            previewImage.src = e.target.result;
            previewContainer.style.display = 'block';
            previewLabel.textContent = 'Preview gambar yang akan diupload';

            // Update side preview
            sidePreviewImage.src = e.target.result;
            sidePreviewCard.style.display = 'block';
        }

        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection