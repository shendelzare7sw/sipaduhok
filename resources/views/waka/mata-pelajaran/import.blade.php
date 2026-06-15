@extends('layouts.sneat')

@section('title', 'Import Mata Pelajaran')
@section('page-title', 'Import Mata Pelajaran')
@section('page-subtitle', 'Import data mata pelajaran dari file Excel')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/mata-pelajaran/import.css'])
@endsection

@section('content')
    <div class="mp-import-page">
        <div class="instructions">
            <h6><i class="fas fa-info-circle"></i> Petunjuk Import</h6>
            <ol>
                <li><strong>Download Template</strong> - Klik tombol "Download Template" untuk mendapatkan file Excel dengan format yang benar</li>
                <li><strong>Isi Data</strong> - Buka file Excel dan isi data mata pelajaran sesuai kolom yang tersedia. Hapus baris contoh terlebih dahulu.</li>
                <li><strong>Upload File</strong> - Pilih file Excel yang sudah diisi dan klik tombol "Import"</li>
                <li><strong>Validasi Otomatis</strong> - Sistem akan memvalidasi data dan melewati baris yang sudah ada</li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-import icon-primary"></i>Upload File Excel</h5>
            </div>
            <div class="card-body">
                <div class="template-block">
                    <p>Pastikan file Excel Anda menggunakan format yang benar:</p>
                    <a href="{{ route('waka.mata-pelajaran.template') }}" class="btn btn-success">
                        <i class="fas fa-download"></i>
                        Download Template Excel
                    </a>
                </div>

                <form action="{{ route('waka.mata-pelajaran.import.store') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf

                    <div class="upload-area" id="uploadArea">
                        <div class="upload-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-text">
                            Klik untuk memilih file atau drag & drop file di sini
                        </div>
                        <div class="upload-hint">
                            Format yang didukung: .xlsx, .xls (Maksimal 5MB)
                        </div>
                    </div>

                    <input type="file" name="file" id="fileInput" class="file-input" accept=".xlsx,.xls">

                    <div class="file-selected" id="fileSelected">
                        <div class="file-selected-info">
                            <i class="fas fa-file-excel file-selected-icon"></i>
                            <div>
                                <div class="file-name" id="fileName">-</div>
                                <div class="file-size" id="fileSize">-</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary btn-compact" data-clear-file>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    @error('file')
                        <div class="error-text">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror

                    <div class="form-actions">
                        <a href="{{ route('waka.mata-pelajaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i>
                            Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-upload"></i>
                            Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-table icon-success"></i>Format Kolom</h5>
            </div>
            <div class="card-body">
                <div class="table-scroll">
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
                                <td><code class="format-code">kode_mapel</code></td>
                                <td><span class="text-optional">Opsional</span></td>
                                <td>Kode unik mata pelajaran (contoh: MTK-SD)</td>
                            </tr>
                            <tr>
                                <td><code class="format-code">nama_mapel</code></td>
                                <td><span class="text-required">Ya</span></td>
                                <td>Nama mata pelajaran</td>
                            </tr>
                            <tr>
                                <td><code class="format-code">jenjang</code></td>
                                <td><span class="text-required">Ya</span></td>
                                <td>Pilih salah satu: KB, TKA, TKB, SD, SMP, SMA</td>
                            </tr>
                            <tr>
                                <td><code class="format-code">deskripsi</code></td>
                                <td><span class="text-optional">Opsional</span></td>
                                <td>Deskripsi tambahan tentang mata pelajaran</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/waka/mata-pelajaran/import.js'])
@endsection
