@extends('layouts.sneat')

@section('title', 'Import Mata Pelajaran')
@section('page-title', 'Import Mata Pelajaran')
@section('page-subtitle', 'Import data mata pelajaran dari file Excel')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        /* Card */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 24px;
            border: none;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: white;
            border-radius: 12px 12px 0 0;
        }

        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827;
        }

        .card-body {
            padding: 24px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-success:hover {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
        }

        /* Upload Area */
        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: #f9fafb;
            transition: all 0.3s;
            cursor: pointer;
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-area.dragover {
            border-color: #3b82f6;
            background: #dbeafe;
        }

        .upload-icon {
            font-size: 48px;
            color: #9ca3af;
            margin-bottom: 16px;
        }

        .upload-text {
            font-size: 16px;
            color: #374151;
            margin-bottom: 8px;
        }

        .upload-hint {
            font-size: 13px;
            color: #6b7280;
        }

        .file-selected {
            display: none;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 16px;
        }

        .file-selected.show {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* Instructions */
        .instructions {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
        }

        .instructions h6 {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e;
        }

        .instructions li {
            margin-bottom: 8px;
        }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
        }

        .alert-warning {
            background: #fffbeb;
            border: 1px solid #fef3c7;
            color: #92400e;
        }

        /* Form */
        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }

            .form-actions .btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Hidden file input */
        .file-input {
            display: none;
        }
    </style>

    <div style="max-width: 800px; margin: 0 auto; padding: 0 1rem;">
        {{-- Alert Messages --}}
        {{-- Instructions Card --}}
        <div class="instructions">
            <h6><i class="fas fa-info-circle"></i> Petunjuk Import</h6>
            <ol>
                <li><strong>Download Template</strong> - Klik tombol "Download Template" untuk mendapatkan file Excel dengan
                    format yang benar</li>
                <li><strong>Isi Data</strong> - Buka file Excel dan isi data mata pelajaran sesuai kolom yang tersedia.
                    Hapus baris contoh terlebih dahulu.</li>
                <li><strong>Upload File</strong> - Pilih file Excel yang sudah diisi dan klik tombol "Import"</li>
                <li><strong>Validasi Otomatis</strong> - Sistem akan memvalidasi data dan melewati baris yang sudah ada</li>
            </ol>
        </div>

        {{-- Main Card --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-import" style="color: #3b82f6; margin-right: 10px;"></i>Upload File Excel</h5>
            </div>
            <div class="card-body">
                {{-- Download Template Button --}}
                <div style="margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid #e5e7eb;">
                    <p style="margin-bottom: 12px; color: #374151;">Pastikan file Excel Anda menggunakan format yang benar:
                    </p>
                    <a href="{{ route('admin.mata-pelajaran.template') }}" class="btn btn-success">
                        <i class="fas fa-download"></i>
                        Download Template Excel
                    </a>
                </div>

                {{-- Upload Form --}}
                <form action="{{ route('admin.mata-pelajaran.import.store') }}" method="POST" enctype="multipart/form-data"
                    id="importForm">
                    @csrf

                    {{-- Upload Area --}}
                    <div class="upload-area" id="uploadArea" onclick="document.getElementById('fileInput').click()">
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

                    {{-- File Selected Indicator --}}
                    <div class="file-selected" id="fileSelected">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-file-excel" style="color: #16a34a; font-size: 24px;"></i>
                            <div>
                                <div style="font-weight: 600; color: #111827;" id="fileName">-</div>
                                <div style="font-size: 12px; color: #6b7280;" id="fileSize">-</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="clearFile()" style="padding: 6px 12px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    @error('file')
                        <div style="color: #dc2626; font-size: 13px; margin-top: 8px;">
                            <i class="fas fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror

                    {{-- Form Actions --}}
                    <div class="form-actions">
                        <a href="{{ route('admin.mata-pelajaran.index') }}" class="btn btn-secondary">
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

        {{-- Info Table Format --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-table" style="color: #10b981; margin-right: 10px;"></i>Format Kolom</h5>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f9fafb;">
                            <th
                                style="padding: 12px 16px; text-align: left; border-bottom: 2px solid #e5e7eb; font-weight: 600; color: #374151;">
                                Kolom</th>
                            <th
                                style="padding: 12px 16px; text-align: left; border-bottom: 2px solid #e5e7eb; font-weight: 600; color: #374151;">
                                Wajib</th>
                            <th
                                style="padding: 12px 16px; text-align: left; border-bottom: 2px solid #e5e7eb; font-weight: 600; color: #374151;">
                                Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><code
                                    style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">kode_mapel</code>
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><span
                                    style="color: #6b7280;">Opsional</span></td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">Kode unik mata pelajaran
                                (contoh: MTK-SD)</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><code
                                    style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">nama_mapel</code>
                            </td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><span
                                    style="color: #dc2626; font-weight: 600;">Ya</span></td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">Nama mata pelajaran</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><code
                                    style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">jenjang</code></td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><span
                                    style="color: #dc2626; font-weight: 600;">Ya</span></td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">Pilih salah satu: KB, TKA,
                                TKB, SD, SMP, SMA</td>
                        </tr>
                        <tr>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><code
                                    style="background: #f3f4f6; padding: 2px 6px; border-radius: 4px;">deskripsi</code></td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;"><span
                                    style="color: #6b7280;">Opsional</span></td>
                            <td style="padding: 12px 16px; border-bottom: 1px solid #f3f4f6;">Deskripsi tambahan tentang
                                mata pelajaran</td>
                        </tr>
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        // File input handler
        const fileInput = document.getElementById('fileInput');
        const uploadArea = document.getElementById('uploadArea');
        const fileSelected = document.getElementById('fileSelected');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const submitBtn = document.getElementById('submitBtn');

        fileInput.addEventListener('change', function () {
            if (this.files.length > 0) {
                const file = this.files[0];
                fileName.textContent = file.name;
                fileSize.textContent = formatFileSize(file.size);
                fileSelected.classList.add('show');
                uploadArea.style.display = 'none';
                submitBtn.disabled = false;
            }
        });

        function clearFile() {
            fileInput.value = '';
            fileSelected.classList.remove('show');
            uploadArea.style.display = 'block';
            submitBtn.disabled = true;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Drag and drop
        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            this.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function (e) {
            e.preventDefault();
            this.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            this.classList.remove('dragover');

            if (e.dataTransfer.files.length > 0) {
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection