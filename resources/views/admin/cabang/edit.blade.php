@extends('layouts.sneat')

@section('title', 'Edit Cabang')

@section('page-title', 'Edit Cabang')
@section('page-subtitle')
Perbarui data cabang {{ $cabang->nama_cabang }}
@endsection

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
    color: #f59e0b;
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

/* Current Data Badge */
.current-data-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border: 1px solid #fcd34d;
    border-radius: 50px;
    font-size: 13px;
    color: #92400e;
    margin-bottom: 24px;
}

.current-data-badge i {
    color: #f59e0b;
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
    color: #f59e0b;
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
    border-color: #f59e0b;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
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
    accent-color: #f59e0b;
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

/* Warning Box */
.warning-box {
    padding: 16px 20px;
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border-left: 4px solid #f59e0b;
    border-radius: 0 10px 10px 0;
    margin-bottom: 24px;
}

.warning-box h6 {
    font-size: 14px;
    font-weight: 600;
    color: #92400e;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.warning-box p {
    font-size: 13px;
    color: #a16207;
    margin: 0;
    line-height: 1.6;
}

/* Buttons */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
    margin-top: 32px;
}

.form-actions-left {
    display: flex;
    gap: 12px;
}

.form-actions-right {
    display: flex;
    gap: 12px;
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
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 20px rgba(245, 158, 11, 0.5);
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

.btn-danger:hover {
    background: #fecaca;
    border-color: #f87171;
}

/* Stats Summary */
.stats-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 768px) {
    .stats-summary {
        grid-template-columns: 1fr;
    }

    .card-header {
        padding: 16px 20px;
    }

    .card-body {
        padding: 16px 20px;
    }

    .form-actions {
        flex-direction: column;
        gap: 12px;
    }

    .form-actions-left,
    .form-actions-right {
        width: 100%;
    }

    .form-actions-left {
        order: 2;
    }

    .form-actions-right {
        order: 1;
    }

    .form-actions .btn {
        flex: 1;
        justify-content: center;
        padding: 10px 16px;
        font-size: 13px;
    }

    .breadcrumb {
        font-size: 12px;
        flex-wrap: wrap;
    }

    .current-data-badge {
        font-size: 12px;
        padding: 6px 12px;
    }

    .warning-box {
        padding: 12px 16px;
    }

    .warning-box h6 {
        font-size: 13px;
    }

    .warning-box p {
        font-size: 12px;
    }
}

.stats-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 16px;
    text-align: center;
}

.stats-item .stats-number {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
}

.stats-item .stats-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-item.has-data .stats-number {
    color: #3b82f6;
}
</style>

<div style="max-width: 900px; margin: 0 auto;">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.cabang.index') }}">Manajemen Cabang</a>
        <span>/</span>
        <span class="current">Edit: {{ $cabang->nama_cabang }}</span>
    </div>

    {{-- Current Data Badge --}}
    <div class="current-data-badge">
        <i class="fas fa-edit"></i>
        Mengedit data cabang dengan kode: <strong>{{ $cabang->kode_cabang }}</strong>
    </div>

    {{-- Warning Box --}}
    <div class="warning-box">
        <h6><i class="fas fa-exclamation-triangle"></i> Perhatian</h6>
        <p>
            Perubahan pada data cabang akan mempengaruhi seluruh data terkait seperti siswa, kelas, dan tenaga pendidik yang terdaftar di cabang ini.
            Pastikan data yang dimasukkan sudah benar.
        </p>
    </div>

    {{-- Stats Summary --}}
    @php
        $siswaCount = \App\Models\Siswa::where('cabang_id', $cabang->id)->count();
        $kelasCount = \App\Models\Kelas::where('cabang_id', $cabang->id)->count();
        $userCount = \App\Models\User::where('cabang_id', $cabang->id)->where('role', '!=', 'siswa')->count();
    @endphp
    <div class="stats-summary">
        <div class="stats-item {{ $siswaCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $siswaCount }}</div>
            <div class="stats-label">Siswa Terdaftar</div>
        </div>
        <div class="stats-item {{ $kelasCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $kelasCount }}</div>
            <div class="stats-label">Kelas Aktif</div>
        </div>
        <div class="stats-item {{ $userCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $userCount }}</div>
            <div class="stats-label">Tenaga Pendidik</div>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-edit"></i> Form Edit Cabang</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.cabang.update', $cabang) }}" method="POST" id="cabangForm">
                @csrf
                @method('PUT')

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
                                   value="{{ old('kode_cabang', $cabang->kode_cabang) }}"
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
                                   value="{{ old('nama_cabang', $cabang->nama_cabang) }}"
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
                                      required>{{ old('alamat', $cabang->alamat) }}</textarea>
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
                                   value="{{ old('telepon', $cabang->telepon) }}"
                                   placeholder="Contoh: (021) 12345678">
                            <div class="form-hint">Opsional - nomor telepon kantor cabang</div>
                            @error('telepon')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label>Status Cabang</label>
                            <label class="form-check">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $cabang->is_active) ? 'checked' : '' }}>
                                <span class="form-check-label">
                                    <span class="label-title">Aktif</span>
                                    <span class="label-desc">Cabang beroperasi dan dapat menerima siswa baru</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <div class="form-actions-left">
                        <a href="{{ route('admin.cabang.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('admin.cabang.show', $cabang) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                    <div class="form-actions-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const kodeInput = document.getElementById('kode_cabang');

    // Auto uppercase for kode_cabang
    kodeInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
});
</script>
@endsection
