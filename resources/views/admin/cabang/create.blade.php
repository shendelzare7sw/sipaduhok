@extends('layouts.sneat')

@section('title', 'Tambah Cabang')

@section('page-title', 'Tambah Cabang Baru')
@section('page-subtitle', 'Tambahkan lokasi/cabang baru PKBM House of Knowledge')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Card Styles */
.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    border: none;
    overflow: hidden;
}

.card-header {
    padding: 24px 28px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h5 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header h5 i {
    color: #3b82f6;
    font-size: 24px;
}

.card-body {
    padding: 28px;
}

/* Breadcrumb */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    font-size: 14px;
}

.breadcrumb a {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.2s;
}

.breadcrumb a:hover {
    color: #3b82f6;
}

.breadcrumb span {
    color: #9ca3af;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

/* Form Styles */
.form-section {
    margin-bottom: 32px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: #3b82f6;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 20px;
}

.form-row.single {
    grid-template-columns: 1fr;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }

    .form-actions {
        flex-direction: column;
    }

    .form-actions .btn {
        width: 100%;
        justify-content: center;
    }
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-group label .required {
    color: #ef4444;
    margin-left: 2px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    background: #fff;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}

.form-control::placeholder {
    color: #9ca3af;
}

.form-control.is-invalid {
    border-color: #ef4444;
}

textarea.form-control {
    min-height: 120px;
    resize: vertical;
}

.form-hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

.invalid-feedback {
    font-size: 13px;
    color: #ef4444;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.invalid-feedback i {
    font-size: 12px;
}

/* Checkbox / Toggle */
.form-check {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    background: #f9fafb;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s;
}

.form-check:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.form-check input[type="checkbox"] {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 2px solid #d1d5db;
    cursor: pointer;
    accent-color: #3b82f6;
}

.form-check-label {
    display: flex;
    flex-direction: column;
    gap: 2px;
    cursor: pointer;
}

.form-check-label .label-title {
    font-weight: 500;
    color: #111827;
    font-size: 14px;
}

.form-check-label .label-desc {
    font-size: 12px;
    color: #6b7280;
}

/* Info Box */
.info-box {
    padding: 16px 20px;
    background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
    border-left: 4px solid #3b82f6;
    border-radius: 0 10px 10px 0;
    margin-bottom: 24px;
}

.info-box h6 {
    font-size: 14px;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box p {
    font-size: 13px;
    color: #3b82f6;
    margin: 0;
    line-height: 1.6;
}

/* Buttons */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
    margin-top: 32px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
    border-color: #9ca3af;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(59, 130, 246, 0.5);
}

/* Preview Card */
.preview-card {
    background: linear-gradient(135deg, #fafafa 0%, #f3f4f6 100%);
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 24px;
    margin-top: 24px;
}

.preview-card h6 {
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.preview-content {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
}

@media (max-width: 576px) {
    .preview-content {
        grid-template-columns: 1fr;
    }
}

.preview-item {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.preview-item .label {
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #9ca3af;
    font-weight: 600;
}

.preview-item .value {
    font-size: 14px;
    color: #374151;
    font-weight: 500;
}

.preview-item .value.empty {
    color: #d1d5db;
    font-style: italic;
}
</style>

<div style="max-width: 900px; margin: 0 auto;">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.cabang.index') }}">Manajemen Cabang</a>
        <span>/</span>
        <span class="current">Tambah Cabang</span>
    </div>

    {{-- Info Box --}}
    <div class="info-box">
        <h6><i class="fas fa-lightbulb"></i> Informasi</h6>
        <p>
            PKBM House of Knowledge memiliki 3 lokasi cabang utama: Ruko (Gedung Utama), PAUD HOK, dan HOK Cimanggis.
            Setiap cabang dapat memiliki siswa, kelas, dan tenaga pendidik masing-masing.
        </p>
    </div>

    {{-- Form Card --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-building"></i> Form Tambah Cabang</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cabang.store') }}" method="POST" id="cabangForm">
                @csrf

                {{-- Basic Information --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Informasi Dasar
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="kode_cabang">Kode Cabang <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('kode_cabang') is-invalid @enderror" 
                                   id="kode_cabang" 
                                   name="kode_cabang" 
                                   value="{{ old('kode_cabang') }}"
                                   placeholder="Contoh: RUKO, PAUD, CMNGS"
                                   maxlength="10"
                                   style="text-transform: uppercase;"
                                   required>
                            <div class="form-hint">Maksimal 10 karakter, hanya huruf dan angka (akan otomatis uppercase)</div>
                            @error('kode_cabang')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_cabang">Nama Cabang <span class="required">*</span></label>
                            <input type="text" 
                                   class="form-control @error('nama_cabang') is-invalid @enderror" 
                                   id="nama_cabang" 
                                   name="nama_cabang" 
                                   value="{{ old('nama_cabang') }}"
                                   placeholder="Contoh: PKBM House of Knowledge - Ruko"
                                   required>
                            @error('nama_cabang')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Contact Information --}}
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-map-marker-alt"></i> Lokasi & Kontak
                    </div>

                    <div class="form-row single">
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                      id="alamat" 
                                      name="alamat" 
                                      rows="3"
                                      placeholder="Masukkan alamat lengkap cabang..."
                                      required>{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="telepon">Nomor Telepon</label>
                            <input type="text" 
                                   class="form-control @error('telepon') is-invalid @enderror" 
                                   id="telepon" 
                                   name="telepon" 
                                   value="{{ old('telepon') }}"
                                   placeholder="Contoh: (021) 12345678">
                            <div class="form-hint">Opsional - nomor telepon kantor cabang</div>
                            @error('telepon')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Status Cabang</label>
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    <span class="label-title">Aktif</span>
                                    <span class="label-desc">Cabang beroperasi dan dapat menerima siswa baru</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Preview --}}
                <div class="preview-card" id="previewCard">
                    <h6><i class="fas fa-eye"></i> Preview Data</h6>
                    <div class="preview-content">
                        <div class="preview-item">
                            <span class="label">Kode Cabang</span>
                            <span class="value" id="previewKode">-</span>
                        </div>
                        <div class="preview-item">
                            <span class="label">Nama Cabang</span>
                            <span class="value" id="previewNama">-</span>
                        </div>
                        <div class="preview-item">
                            <span class="label">Alamat</span>
                            <span class="value" id="previewAlamat">-</span>
                        </div>
                        <div class="preview-item">
                            <span class="label">Telepon</span>
                            <span class="value" id="previewTelepon">-</span>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('admin.cabang.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Cabang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeInput = document.getElementById('kode_cabang');
    const namaInput = document.getElementById('nama_cabang');
    const alamatInput = document.getElementById('alamat');
    const teleponInput = document.getElementById('telepon');
    
    const previewKode = document.getElementById('previewKode');
    const previewNama = document.getElementById('previewNama');
    const previewAlamat = document.getElementById('previewAlamat');
    const previewTelepon = document.getElementById('previewTelepon');

    function updatePreview() {
        previewKode.textContent = kodeInput.value.toUpperCase() || '-';
        previewKode.classList.toggle('empty', !kodeInput.value);
        
        previewNama.textContent = namaInput.value || '-';
        previewNama.classList.toggle('empty', !namaInput.value);
        
        previewAlamat.textContent = alamatInput.value || '-';
        previewAlamat.classList.toggle('empty', !alamatInput.value);
        
        previewTelepon.textContent = teleponInput.value || '-';
        previewTelepon.classList.toggle('empty', !teleponInput.value);
    }

    // Auto uppercase for kode_cabang
    kodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        updatePreview();
    });

    namaInput.addEventListener('input', updatePreview);
    alamatInput.addEventListener('input', updatePreview);
    teleponInput.addEventListener('input', updatePreview);

    // Initial preview
    updatePreview();
});
</script>
@endsection
