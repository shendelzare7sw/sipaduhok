@extends('layouts.sneat')
@section('title', 'Import Orang Tua')
@section('page-title', 'Import Orang Tua')
@section('page-subtitle', 'Import data orang tua dari file Excel')
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
            background: linear-gradient(135deg, #ea580c, #c2410c);
            color: #fff
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #d1d5db
        }

        .btn-success {
            background: linear-gradient(135deg, #ea580c, #c2410c);
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
            border-color: #ea580c;
            background: #fff7ed
        }

        .file-selected {
            display: none;
            background: #ffedd5;
            border: 1px solid #fed7aa;
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
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px
        }

        .instructions h6 {
            color: #c2410c;
            font-weight: 600;
            margin-bottom: 12px
        }

        .instructions ol {
            margin: 0;
            padding-left: 20px;
            color: #9a3412
        }

        .alert-danger {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px
        }
    </style>

    <div style="max-width:800px;margin:0 auto;padding:0 1rem">
        @if(session('error'))
        <div class="alert-danger"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>@endif

        <div class="instructions">
            <h6><i class="fas fa-info-circle"></i> Petunjuk Import Orang Tua</h6>
            <ol>
                <li>Nama orang tua WAJIB diisi</li>
                <li>Username & email opsional (auto-generate jika kosong)</li>
                <li>nis_anak: NIS siswa yang akan dihubungkan (pisah koma jika lebih dari satu)</li>
                <li>Password default: <code>password</code></li>
            </ol>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-file-import" style="color:#ea580c;margin-right:10px"></i>Upload File Excel</h5>
            </div>
            <div class="card-body">
                <div style="margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid #e5e7eb">
                    <a href="{{ route('admin.users.orang-tua-template') }}" class="btn btn-success"><i
                            class="fas fa-download"></i> Download Template</a>
                </div>
                <form action="{{ route('admin.users.import-orang-tua.store') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="upload-area" onclick="document.getElementById('fileInput').click()">
                        <div style="font-size:48px;color:#9ca3af;margin-bottom:16px"><i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div style="font-size:16px;color:#374151">Klik untuk memilih file atau drag & drop</div>
                        <div style="font-size:13px;color:#6b7280">Format: .xlsx, .xls (Maks 5MB)</div>
                    </div>
                    <input type="file" name="file" id="fileInput" style="display:none" accept=".xlsx,.xls">
                    <div class="file-selected" id="fileSelected">
                        <div style="display:flex;align-items:center;gap:10px">
                            <i class="fas fa-file-excel" style="color:#ea580c;font-size:24px"></i>
                            <div><strong id="fileName">-</strong>
                                <div style="font-size:12px;color:#6b7280" id="fileSize">-</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary" onclick="clearFile()" style="padding:6px 12px"><i
                                class="fas fa-times"></i></button>
                    </div>
                    <div style="display:flex;gap:12px;margin-top:24px">
                        <a href="{{ route('admin.users.orang-tua') }}" class="btn btn-secondary"><i
                                class="fas fa-arrow-left"></i> Kembali</a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled><i class="fas fa-upload"></i>
                            Import Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const fi = document.getElementById('fileInput'), fs = document.getElementById('fileSelected'), fn = document.getElementById('fileName'), fz = document.getElementById('fileSize'), sb = document.getElementById('submitBtn');
        fi.onchange = function () { if (this.files.length) { fn.textContent = this.files[0].name; fz.textContent = (this.files[0].size / 1024).toFixed(2) + ' KB'; fs.classList.add('show'); document.querySelector('.upload-area').style.display = 'none'; sb.disabled = false } };
        function clearFile() { fi.value = ''; fs.classList.remove('show'); document.querySelector('.upload-area').style.display = 'block'; sb.disabled = true }
    </script>
@endsection