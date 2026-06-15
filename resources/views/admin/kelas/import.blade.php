@extends('layouts.sneat')

@section('title', 'Import Kelas')
@section('page-title', 'Import Kelas')
@section('page-subtitle', 'Import data kelas dari file Excel')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/kelas/import.css'])
@endsection

@section('content')
<div class="kelas-import-page">
    <div class="instructions">
        <h6><i class="fas fa-info-circle"></i> Petunjuk Import</h6>
        <ol>
            <li><strong>Download Template</strong> - Klik tombol "Download Template" untuk file Excel dengan format yang benar</li>
            <li><strong>Isi Data</strong> - Isi data kelas sesuai kolom. Pastikan nama cabang dan tahun ajaran sesuai dengan data di sistem</li>
            <li><strong>Upload File</strong> - Pilih file dan klik "Import"</li>
        </ol>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-file-import card-title-icon-primary"></i>Upload File Excel</h5>
        </div>
        <div class="card-body">
            <div class="template-box">
                <p>Template sudah berisi referensi cabang dan tahun ajaran yang tersedia:</p>
                <a href="{{ route('admin.kelas.template') }}" class="btn btn-success">
                    <i class="fas fa-download"></i> Download Template Excel
                </a>
            </div>

            <form action="{{ route('admin.kelas.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                @csrf
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon"><i class="fas fa-cloud-upload-alt"></i></div>
                    <div class="upload-title">Klik untuk memilih file atau drag & drop</div>
                    <div class="upload-hint">Format: .xlsx, .xls (Maks 5MB)</div>
                </div>
                <input type="file" name="file" id="fileInput" class="file-input" accept=".xlsx,.xls">
                <div class="file-selected" id="fileSelected">
                    <div class="selected-file-info">
                        <i class="fas fa-file-excel file-excel-icon"></i>
                        <div>
                            <div class="file-name" id="fileName">-</div>
                            <div class="file-size" id="fileSize">-</div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary btn-clear-file" data-clear-file>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                @error('file')
                    <div class="file-error"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                @enderror
                <div class="form-actions">
                    <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                        <i class="fas fa-upload"></i> Import Data
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-table card-title-icon-success"></i>Format Kolom</h5>
        </div>
        <div class="card-body">
            <div class="table-wrapper">
                <table class="format-table">
                    <thead>
                        <tr>
                            <th>Kolom</th>
                            <th>Wajib</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>nama_kelas</code></td>
                            <td><span class="required-column">Ya</span></td>
                            <td>Nama kelas (contoh: Kelas 1 SD)</td>
                        </tr>
                        <tr>
                            <td><code>jenjang</code></td>
                            <td><span class="required-column">Ya</span></td>
                            <td>KB, TKA, TKB, SD, SMP, SMA</td>
                        </tr>
                        <tr>
                            <td><code>nama_cabang</code></td>
                            <td><span class="optional-column">Opsional</span></td>
                            <td>Nama cabang (harus sesuai dengan data di sistem)</td>
                        </tr>
                        <tr>
                            <td><code>nama_tahun_ajaran</code></td>
                            <td><span class="optional-column">Opsional</span></td>
                            <td>Default: tahun ajaran aktif</td>
                        </tr>
                        <tr>
                            <td><code>kuota_siswa</code></td>
                            <td><span class="optional-column">Opsional</span></td>
                            <td>Default: 30</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/kelas/import.js'])
@endsection
