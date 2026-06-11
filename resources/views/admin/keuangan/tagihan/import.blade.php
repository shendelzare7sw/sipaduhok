@extends('layouts.sneat')

@section('title', 'Import Tagihan')
@section('page-title', 'Import Tagihan')
@section('page-subtitle', 'Import data tagihan dari file Excel')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/keuangan/tagihan/import.css'])
@endsection

@section('content')
    <div class="import-tagihan-page">
        <div class="instructions">
            <h6><i class="fas fa-info-circle"></i> Petunjuk Import Tagihan</h6>
            <ol>
                <li>Pilih tahun ajaran untuk tagihan yang akan dibuat</li>
                <li>Isi NIS atau NISN atau nama_siswa (salah satu wajib)</li>
                <li>jenis_tagihan dan jumlah WAJIB diisi</li>
                <li>Status tagihan otomatis: belum_bayar</li>
            </ol>
        </div>

        <div class="import-card">
            <div class="import-card-header">
                <h5><i class="fas fa-file-import import-title-icon"></i>Upload File Excel</h5>
            </div>
            <div class="import-card-body">
                <div class="template-section">
                    <a href="{{ route('admin.keuangan.tagihan.template') }}" class="import-btn import-btn-success">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>

                <form action="{{ route('admin.keuangan.tagihan.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="import-label">Tahun Ajaran:</label>
                    <select name="tahun_ajaran_id" class="form-select" required>
                        @foreach($tahunAjarans as $ta)
                            <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }}
                                {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <div class="upload-area" role="button" tabindex="0" data-upload-trigger>
                        <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                        <div class="upload-title">Klik untuk memilih file atau drag & drop</div>
                        <div class="upload-subtitle">Format: .xlsx, .xls (Maks 5MB)</div>
                    </div>

                    <input type="file" name="file" id="fileInput" class="file-input" accept=".xlsx,.xls">

                    <div class="file-selected" id="fileSelected">
                        <div class="selected-file-main">
                            <i class="fas fa-file-excel selected-file-icon"></i>
                            <div>
                                <strong id="fileName">-</strong>
                                <div class="file-size" id="fileSize">-</div>
                            </div>
                        </div>
                        <button type="button" class="import-btn import-btn-secondary clear-file-button" data-clear-file>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="import-actions">
                        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="import-btn import-btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="import-btn import-btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/keuangan/tagihan/import.js'])
@endsection
