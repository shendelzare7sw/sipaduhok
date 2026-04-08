@extends('layouts.sneat')

@section('title', 'Setup Google Sheets')

@section('page-title', 'Setup Sinkronisasi Google Sheets')
@section('page-subtitle', 'Konfigurasi kredensial dan integrasi dengan Google Sheets')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    body {
        background: #f9fafb;
    }

    .setup-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 24px;
    }

    .setup-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        padding: 32px;
        margin-bottom: 24px;
    }

    .setup-header {
        text-align: center;
        margin-bottom: 32px;
    }

    .setup-header h2 {
        font-size: 28px;
        font-weight: 700;
        color: #1a1a1a;
        margin: 0 0 8px 0;
    }

    .setup-header p {
        color: #6b7280;
        margin: 0;
        font-size: 14px;
    }

    .setup-step {
        margin-bottom: 32px;
    }

    .step-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: #3b82f6;
        color: white;
        border-radius: 50%;
        font-weight: 600;
        margin-right: 12px;
    }

    .step-title {
        display: inline-block;
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 16px;
    }

    .step-description {
        color: #6b7280;
        font-size: 13px;
        margin-bottom: 16px;
        line-height: 1.6;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #1a1a1a;
        font-size: 14px;
    }

    .form-group input[type="text"],
    .form-group input[type="file"],
    .form-group textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 13px;
        font-family: inherit;
        box-sizing: border-box;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
        font-family: 'Courier New', monospace;
    }

    .form-group input:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .form-help {
        display: block;
        font-size: 12px;
        color: #9ca3af;
        margin-top: 6px;
    }

    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 13px;
    }

    .alert-info {
        background: #eff6ff;
        border-left: 3px solid #3b82f6;
        color: #0c4a6e;
    }

    .alert-success {
        background: #f0fdf4;
        border-left: 3px solid #22c55e;
        color: #15803d;
    }

    .alert-danger {
        background: #fef2f2;
        border-left: 3px solid #ef4444;
        color: #991b1b;
    }

    .btn-group {
        display: flex;
        gap: 12px;
        justify-content: center;
        margin-top: 24px;
    }

    .btn {
        padding: 10px 24px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-primary {
        background: #3b82f6;
        color: white;
    }

    .btn-primary:hover {
        background: #2563eb;
    }

    .btn-secondary {
        background: #e5e7eb;
        color: #374151;
        border: 2px solid #d1d5db;
    }

    .btn-secondary:hover {
        background: #d1d5db;
        border-color: #9ca3af;
        color: #1f2937;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-secondary:active {
        background: #bfdbfe;
        border-color: #9ca3af;
        transform: scale(0.98);
    }

    .btn-success {
        background: #10b981;
        color: white;
    }

    .btn-success:hover {
        background: #059669;
    }

    .file-input-wrapper {
        position: relative;
        display: inline-block;
        width: 100%;
    }

    .file-input-display {
        display: flex;
        align-items: center;
        padding: 10px 12px;
        border: 2px dashed #d1d5db;
        border-radius: 8px;
        background: #f9fafb;
        cursor: pointer;
        transition: all 0.2s;
    }

    .file-input-wrapper input[type="file"] {
        display: none;
    }

    .file-input-display:hover {
        border-color: #3b82f6;
        background: #eff6ff;
    }

    .file-icon {
        font-size: 20px;
        margin-right: 10px;
    }

    .file-name {
        flex: 1;
        color: #6b7280;
        font-size: 13px;
    }

    .file-name.selected {
        color: #1a1a1a;
        font-weight: 600;
    }

    .code-block {
        background: #1f2937;
        color: #e5e7eb;
        padding: 12px;
        border-radius: 6px;
        font-family: 'Courier New', monospace;
        font-size: 12px;
        overflow-x: auto;
        margin: 12px 0;
    }

    .divider {
        height: 1px;
        background: #e5e7eb;
        margin: 24px 0;
    }

    .success-icon {
        color: #10b981;
        font-size: 24px;
        text-align: center;
        margin-bottom: 16px;
    }

    .error-details {
        background: #fef2f2;
        padding: 12px;
        border-radius: 6px;
        margin-top: 8px;
        font-size: 12px;
        color: #991b1b;
        font-family: 'Courier New', monospace;
    }

    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #e5e7eb;
        border-top-color: #3b82f6;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
        margin-right: 8px;
        vertical-align: middle;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endsection

@section('content')
<div class="setup-container">
    <div class="setup-card">
        <div class="setup-header">
            <h2><i class="fas fa-link" style="margin-right: 8px;"></i> Setup Sinkronisasi Google Sheets</h2>
            <p>Panduan langkah demi langkah untuk mengkonfigurasi integrasi</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Setup Gagal!</strong>
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <div class="success-icon">✓</div>
                {{ session('success') }}
                <div style="margin-top: 16px; text-align: center;">
                    <a href="{{ route('admin.google-sheets.index') }}" class="btn btn-success">
                        Kembali ke Dashboard
                    </a>
                </div>
            </div>
        @else
            <form method="POST" action="{{ route('admin.google-sheets.save-credential') }}" enctype="multipart/form-data" id="setupForm">
                @csrf

                <!-- Step 1: Overview -->
                <div class="setup-step">
                    <div>
                        <span class="step-number">1</span>
                        <span class="step-title">Siapkan Kredensial Anda</span>
                    </div>
                    <div class="step-description">
                        Untuk mengaktifkan integrasi Google Sheets, Anda memerlukan file kunci JSON Service Account dari Google Cloud.
                        Ini memberikan akses API tanpa memerlukan autentikasi manual setiap kali.
                    </div>
                    <div class="alert alert-info">
                        <strong><i class="fas fa-info-circle"></i> Panduan Cepat:</strong><br>
                        1. Buka <a href="https://console.cloud.google.com/" target="_blank" style="color: #0284c7;">Google Cloud Console</a><br>
                        2. Buat proyek baru atau pilih proyek yang ada<br>
                        3. Aktifkan Google Sheets API dan Google Drive API<br>
                        4. Buat Service Account (IAM → Service Accounts)<br>
                        5. Buat kunci JSON untuk akun tersebut<br>
                        6. Unduh file JSON dan unggah di bawah ini
                    </div>
                </div>

                <!-- Step 2: Upload JSON -->
                <div class="setup-step">
                    <div>
                        <span class="step-number">2</span>
                        <span class="step-title">Unggah File JSON Service Account</span>
                    </div>
                    <div class="step-description">
                        Pilih file kunci JSON Service Account Google yang telah diunduh.
                    </div>

                    <div class="form-group">
                        <label for="jsonFile">File JSON Service Account</label>
                        <div class="file-input-wrapper">
                            <div class="file-input-display" onclick="document.getElementById('jsonFile').click();">
                                <span class="file-icon"><i class="fas fa-file-pdf" style="font-size: 18px;"></i></span>
                                <span class="file-name" id="fileName">Klik untuk memilih atau seret file JSON</span>
                            </div>
                            <input type="file" id="jsonFile" name="json_file" accept=".json" required
                                   onchange="handleFileSelect(this)">
                        </div>
                        <span class="form-help">Format yang diterima: File kunci JSON dari Google Cloud Console</span>
                    </div>
                </div>

                <!-- Step 3: Configure Spreadsheet -->
                <div class="setup-step">
                    <div>
                        <span class="step-number">3</span>
                        <span class="step-title">Konfigurasi Spreadsheet</span>
                    </div>
                    <div class="step-description">
                        Berikan ID Google Spreadsheet tempat data akan disinkronkan.
                    </div>

                    <div class="form-group">
                        <label for="spreadsheetId">ID Spreadsheet</label>
                        <input type="text" id="spreadsheetId" name="spreadsheet_id"
                               placeholder="contoh: 1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p"
                               value="{{ old('spreadsheet_id', $currentSpreadsheetId ?? '') }}" required>
                        <span class="form-help">
                            Temukan ini di URL Spreadsheet: docs.google.com/spreadsheets/d/<strong>SPREADSHEET_ID</strong>/edit
                        </span>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Step 4: Test Connection -->
                <div class="setup-step">
                    <div>
                        <span class="step-number">4</span>
                        <span class="step-title">Uji Koneksi</span>
                    </div>
                    <div class="step-description">
                        Sebelum menyelesaikan, uji bahwa kredensial berfungsi dan memiliki akses ke Spreadsheet Anda.
                    </div>

                    <div style="margin: 16px 0; text-align: center;">
                        <button type="button" class="btn btn-primary" id="testBtn" onclick="testConnection()">
                            <i class="fas fa-plug"></i> Uji Koneksi
                        </button>
                        <div id="testResult"></div>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Step 5: Save -->
                <div class="btn-group">
                    <a href="{{ route('admin.google-sheets.index') }}" class="btn btn-secondary" style="text-decoration: none; display: inline-block;">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-success" id="submitBtn">
                        <i class="fas fa-save"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        @endif
    </div>

    <div style="text-align: center; color: #9ca3af; font-size: 12px; margin-top: 32px;">
        <p><i class="fas fa-lock"></i> Kredensial Anda disimpan dengan aman di storage/app/credentials/</p>
        <p><i class="fas fa-question-circle"></i> Perlu bantuan? Hubungi administrator sistem Anda</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function handleFileSelect(input) {
        const fileName = document.getElementById('fileName');
        if (input.files && input.files.length > 0) {
            fileName.textContent = input.files[0].name;
            fileName.classList.add('selected');
        }
    }

    function testConnection() {
        const jsonFile = document.getElementById('jsonFile').files[0];
        const spreadsheetId = document.getElementById('spreadsheetId').value;
        const testBtn = document.getElementById('testBtn');
        const testResult = document.getElementById('testResult');

        if (!jsonFile) {
            testResult.innerHTML = '<div class="alert alert-danger" style="margin-top: 12px;">Silakan pilih file JSON terlebih dahulu</div>';
            return;
        }

        if (!spreadsheetId) {
            testResult.innerHTML = '<div class="alert alert-danger" style="margin-top: 12px;">Silakan masukkan ID Spreadsheet terlebih dahulu</div>';
            return;
        }

        testBtn.disabled = true;
        testBtn.innerHTML = '<span class="loading"></span>Testing...';
        testResult.innerHTML = '';

        const formData = new FormData();
        formData.append('json_file', jsonFile);
        formData.append('spreadsheet_id', spreadsheetId);
        formData.append('_token', '{{ csrf_token() }}');

        fetch('{{ route("admin.google-sheets.test-connection") }}', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                testResult.innerHTML = `
                    <div class="alert alert-success" style="margin-top: 12px;">
                        <i class="fas fa-check-circle"></i> <strong>Koneksi Berhasil!</strong><br>
                        ${data.message}
                    </div>
                `;
                document.getElementById('submitBtn').disabled = false;
            } else {
                testResult.innerHTML = `
                    <div class="alert alert-danger" style="margin-top: 12px;">
                        <i class="fas fa-times-circle"></i> <strong>Koneksi Gagal!</strong><br>
                        ${data.message}
                        ${data.error ? '<div class="error-details">' + data.error + '</div>' : ''}
                    </div>
                `;
                document.getElementById('submitBtn').disabled = true;
            }
        })
        .catch(e => {
            testResult.innerHTML = `
                <div class="alert alert-danger" style="margin-top: 12px;">
                    <i class="fas fa-exclamation-triangle"></i> <strong>Kesalahan:</strong> ${e.message}
                </div>
            `;
            document.getElementById('submitBtn').disabled = true;
        })
        .finally(() => {
            testBtn.disabled = false;
            testBtn.innerHTML = '<i class="fas fa-plug"></i> Uji Koneksi';
        });
    }

    // Drag and drop support
    const fileWrapper = document.querySelector('.file-input-wrapper');
    if (fileWrapper) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileWrapper.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            fileWrapper.addEventListener(eventName, () => {
                fileWrapper.style.borderColor = '#3b82f6';
                fileWrapper.style.background = '#eff6ff';
            });
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileWrapper.addEventListener(eventName, () => {
                fileWrapper.style.borderColor = '#d1d5db';
                fileWrapper.style.background = '#f9fafb';
            });
        });

        fileWrapper.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            document.getElementById('jsonFile').files = files;
            handleFileSelect(document.getElementById('jsonFile'));
        });
    }
</script>
@endsection
