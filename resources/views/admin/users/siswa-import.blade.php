@extends('layouts.sneat')
@section('title', 'Import Siswa')
@section('page-title', 'Import Siswa')
@section('page-subtitle', 'Import data siswa dari file Excel')
@section('sidebar-menu')@include('admin.partials.sneat-sidebar-menu')@endsection

@section('content')
    <style>
        .card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .1);
            margin-bottom: 24px;
            border: none
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: #fff;
            border-radius: 12px 12px 0 0
        }

        .card-header h5 {
            margin: 0;
            font-size: 18px;
            font-weight: 600;
            color: #111827
        }

        .card-body {
            padding: 24px
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: .2s
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: #fff
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: #fff
        }

        .upload-area {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            background: #f9fafb;
            cursor: pointer
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff
        }

        .file-selected {
            display: none;
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            border-radius: 8px;
            padding: 12px 16px;
            margin-top: 16px
        }

        .file-selected.show {
            display: flex;
            align-items: center;
            justify-content: space-between
        }

        .instructions {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px
        }

        .instructions h6 {
            color: #0369a1;
            font-weight: 600;
            margin-bottom: 12px
        }

        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #0c4a6e
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px
        }

        @media (max-width: 768px) {
            .card-header {
                padding: 16px;
            }

            .card-body {
                padding: 16px;
            }

            .instructions {
                padding: 14px;
            }

            .instructions ol {
                padding-left: 16px;
                font-size: 13px;
            }

            .upload-area {
                padding: 24px 16px;
            }

            .upload-area div[style*="font-size:48px"] {
                font-size: 36px !important;
            }

            .file-selected {
                flex-direction: row;
                gap: 8px;
            }

            .file-selected div[style*="display:flex"] {
                min-width: 0;
            }

            .file-selected strong {
                word-break: break-all;
                font-size: 13px;
            }

            .btn {
                padding: 10px 16px;
                font-size: 13px;
                width: 100%;
                justify-content: center;
            }

            div[style*="display:flex;gap:12px"] {
                flex-direction: column !important;
            }
        }
    </style>

    <div style="max-width:800px;margin:0 auto;padding:0 1rem">
            <div class="instructions">
                <h6><i class="fas fa-info-circle"></i> Petunjuk Import Siswa</h6>
                <ol>
                    <li>Download template Excel dengan format yang benar</li>
                    <li>Isi data siswa. Kolom <strong>nama_lengkap</strong> wajib diisi</li>
                    <li>nama_kelas harus sesuai dengan kelas yang ada di sistem</li>
                    <li>User account akan dibuat otomatis dengan password: <code>password</code></li>
                </ol>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-file-import" style="color:#3b82f6;margin-right:10px"></i>Upload File Excel</h5>
                </div>
                <div class="card-body">
                    <div style="margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid #e5e7eb">
                        <a href="{{ route('admin.users.siswa-template') }}" class="btn btn-success"><i
                                class="fas fa-download"></i> Download Template</a>
                    </div>
                    <form action="{{ route('admin.users.import-siswa.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                            <div style="font-size:48px;color:#9ca3af;margin-bottom:16px"><i
                                    class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div style="font-size:16px;color:#374151">Klik untuk memilih file atau drag & drop</div>
                            <div style="font-size:13px;color:#6b7280">Format: .xlsx, .xls (Maks 5MB)</div>
                        </div>
                        <input type="file" name="file" id="fileInput" style="display:none" accept=".xlsx,.xls">
                        <div class="file-selected" id="fileSelected">
                            <div style="display:flex;align-items:center;gap:10px">
                                <i class="fas fa-file-excel" style="color:#16a34a;font-size:24px"></i>
                                <div><strong id="fileName">-</strong>
                                    <div style="font-size:12px;color:#6b7280" id="fileSize">-</div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-secondary" onclick="clearFile()"
                                style="padding:6px 12px"><i class="fas fa-times"></i></button>
                        </div>
                        <div style="display:flex;gap:12px;margin-top:24px">
                            <a href="{{ route('admin.users.siswa') }}" class="btn btn-secondary"><i
                                    class="fas fa-arrow-left"></i> Kembali</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled><i
                                    class="fas fa-upload"></i>
                                Import Data</button>
                        </div>
                    </form>
                </div>
            </div>

        <script>
            const fi = document.getElementById('fileInput'), fs = document.getElementById('fileSelected'), fn = document.getElementById('fileName'), fz = document.getElementById('fileSize'), sb = document.getElementById('submitBtn');
            fi.onchange = function () { if (this.files.length) { fn.textContent = this.files[0].name; fz.textContent = (this.files[0].size / 1024).toFixed(2) + ' KB'; fs.classList.add('show'); document.querySelector('.upload-area').style.display = 'none'; sb.disabled = false } };
            function clearFile() { fi.value = ''; fs.classList.remove('show'); document.querySelector('.upload-area').style.display = 'block'; sb.disabled = true }
        </script>
@endsection