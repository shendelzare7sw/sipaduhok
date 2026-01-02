{{-- resources/views/ketua/catatan/create.blade.php --}}

@extends('layouts.sneat')

@section('title', 'Kirim Catatan Baru')

@section('page-title', 'Kirim Catatan Baru')
@section('page-subtitle', 'Kirim catatan atau pesan kepada pengguna')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #1a1a1a;
}

.card-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 14px;
}

.form-label .required {
    color: #ef4444;
}

.form-control {
    width: 100%;
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-control:focus {
    outline: none;
    border-color: #165fac;
    box-shadow: 0 0 0 3px rgba(22, 95, 172, 0.1);
}

textarea.form-control {
    min-height: 150px;
    resize: vertical;
}

.form-text {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

.invalid-feedback {
    color: #ef4444;
    font-size: 12px;
    margin-top: 6px;
}

.btn {
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
    font-size: 14px;
}

.btn-primary {
    background: #165fac;
    color: white;
}

.btn-primary:hover {
    background: #124a87;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(22, 95, 172, 0.3);
}

.btn-secondary {
    background: #e5e7eb;
    color: #374151;
}

.btn-secondary:hover {
    background: #d1d5db;
}

.form-actions {
    display: flex;
    gap: 12px;
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}

.radio-group {
    display: flex;
    gap: 16px;
}

.radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s;
}

.radio-option:hover {
    border-color: #165fac;
    background: #f9fafb;
}

.radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.radio-option input[type="radio"]:checked ~ label {
    color: #165fac;
    font-weight: 600;
}

.info-box {
    padding: 16px;
    background: #eff6ff;
    border-left: 4px solid #3b82f6;
    border-radius: 8px;
    margin-bottom: 24px;
}

.info-box strong {
    color: #1e40af;
}

.hidden {
    display: none;
}
</style>
@endsection

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 0 1rem;">
    <div class="info-box">
        <strong><i class="fas fa-info-circle"></i> Informasi:</strong>
        Catatan yang Anda kirim dapat dilihat oleh penerima sesuai dengan tipe penerima yang dipilih.
        Gunakan prioritas "Mendesak" untuk catatan yang memerlukan tindakan segera.
    </div>

    <form action="{{ route('admin.catatan.store') }}" method="POST">
        @csrf
        
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-edit"></i> Form Kirim Catatan</h5>
            </div>
            <div class="card-body">
                {{-- Judul --}}
                <div class="form-group">
                    <label class="form-label">
                        Judul Catatan <span class="required">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="judul" 
                        class="form-control @error('judul') is-invalid @enderror" 
                        value="{{ old('judul') }}"
                        placeholder="Masukkan judul catatan..."
                        required
                    >
                    @error('judul')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Buat judul yang jelas dan deskriptif</small>
                </div>

                {{-- Isi Catatan --}}
                <div class="form-group">
                    <label class="form-label">
                        Isi Catatan <span class="required">*</span>
                    </label>
                    <textarea 
                        name="isi_catatan" 
                        class="form-control @error('isi_catatan') is-invalid @enderror" 
                        placeholder="Tulis catatan Anda di sini..."
                        required
                    >{{ old('isi_catatan') }}</textarea>
                    @error('isi_catatan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tipe Penerima --}}
                <div class="form-group">
                    <label class="form-label">
                        Kirim Kepada <span class="required">*</span>
                    </label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input 
                                type="radio" 
                                name="tipe_penerima" 
                                value="semua" 
                                {{ old('tipe_penerima') === 'semua' ? 'checked' : '' }}
                                required
                                onchange="togglePenerimaFields()"
                            >
                            <span><i class="fas fa-bullhorn"></i> Semua Pengguna</span>
                        </label>
                        <label class="radio-option">
                            <input 
                                type="radio" 
                                name="tipe_penerima" 
                                value="role" 
                                {{ old('tipe_penerima') === 'role' ? 'checked' : '' }}
                                onchange="togglePenerimaFields()"
                            >
                            <span><i class="fas fa-users"></i> Per Role</span>
                        </label>
                        <label class="radio-option">
                            <input 
                                type="radio" 
                                name="tipe_penerima" 
                                value="individu" 
                                {{ old('tipe_penerima') === 'individu' ? 'checked' : '' }}
                                onchange="togglePenerimaFields()"
                            >
                            <span>👤 Individu</span>
                        </label>
                    </div>
                    @error('tipe_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Role Penerima (conditional) --}}
                <div class="form-group hidden" id="roleField">
                    <label class="form-label">
                        Pilih Role <span class="required">*</span>
                    </label>
                    <select 
                        name="role_penerima" 
                        class="form-control @error('role_penerima') is-invalid @enderror"
                    >
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('role_penerima') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_penerima')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Penerima Individu (conditional) --}}
                <div class="form-group hidden" id="individuField">
                    <label class="form-label">
                        Pilih Penerima <span class="required">*</span>
                    </label>
                    <select 
                        name="penerima_id" 
                        class="form-control @error('penerima_id') is-invalid @enderror"
                    >
                        <option value="">-- Pilih Penerima --</option>
                        {{-- Akan diisi via AJAX atau hardcode --}}
                    </select>
                    @error('penerima_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Pilih satu pengguna sebagai penerima</small>
                </div>

                {{-- Prioritas --}}
                <div class="form-group">
                    <label class="form-label">
                        Prioritas <span class="required">*</span>
                    </label>
                    <select 
                        name="prioritas" 
                        class="form-control @error('prioritas') is-invalid @enderror"
                        required
                    >
                        <option value="biasa" {{ old('prioritas') === 'biasa' ? 'selected' : '' }}>
                            <i class="fas fa-file-alt"></i> Biasa
                        </option>
                        <option value="penting" {{ old('prioritas') === 'penting' ? 'selected' : '' }}>
                            <i class="fas fa-exclamation-triangle"></i> Penting
                        </option>
                        <option value="mendesak" {{ old('prioritas') === 'mendesak' ? 'selected' : '' }}>
                            🚨 Mendesak
                        </option>
                    </select>
                    @error('prioritas')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Kirim Catatan
                    </button>
                    <a href="{{ route('admin.catatan.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function togglePenerimaFields() {
    const tipe = document.querySelector('input[name="tipe_penerima"]:checked')?.value;
    const roleField = document.getElementById('roleField');
    const individuField = document.getElementById('individuField');

    roleField.classList.add('hidden');
    individuField.classList.add('hidden');

    if (tipe === 'role') {
        roleField.classList.remove('hidden');
    } else if (tipe === 'individu') {
        individuField.classList.remove('hidden');
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    togglePenerimaFields();
});
</script>
@endsection