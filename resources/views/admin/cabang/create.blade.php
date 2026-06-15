@extends('layouts.sneat')

@section('title', 'Tambah Cabang')

@section('page-title', 'Tambah Cabang Baru')
@section('page-subtitle', 'Tambahkan lokasi/cabang baru PKBM House of Knowledge')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite('resources/css/admin/cabang/create.css')
@endsection

@section('content')

<div class="form-shell">
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
                                   class="form-control text-uppercase-input @error('kode_cabang') is-invalid @enderror"
                                   id="kode_cabang"
                                   name="kode_cabang"
                                   value="{{ old('kode_cabang') }}"
                                   placeholder="Contoh: RUKO, PAUD, CMNGS"
                                   maxlength="10"
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

@endsection

@section('scripts')
    @vite('resources/js/admin/cabang/create.js')
@endsection
