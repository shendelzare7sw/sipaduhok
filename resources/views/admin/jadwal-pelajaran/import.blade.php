@extends('layouts.sneat')

@section('title', 'Import Jadwal Pelajaran')
@section('page-title', 'Import Jadwal Pelajaran')
@section('page-subtitle', 'Import jadwal pelajaran dari file Excel')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/jadwal-pelajaran/import.css'])
@endsection

@section('content')
    <div class="jp-import-page">
        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="instructions">
            <h6><i class="fas fa-info-circle me-2"></i> Petunjuk Import Jadwal Pelajaran</h6>
            <ol>
                <li>Pilih tahun ajaran target untuk import.</li>
                <li>Download template Excel dan <strong>hapus baris contoh</strong> berwarna kuning sebelum mengisi data.</li>
                <li><strong>WAJIB:</strong> nama_cabang, nama_kelas, nama_mapel, hari, jam_mulai, jam_selesai.</li>
                <li><strong>OPSIONAL:</strong> nama_guru, jika kosong jadwal dibuat dengan status "kosong", dan keterangan.</li>
                <li><strong>MULTI-KELAS:</strong> pisahkan nama kelas dengan koma, contoh: <code>X IPA 1, X IPA 2</code>.</li>
                <li><strong>BEDA JENJANG:</strong> sistem otomatis memisah jadwal per jenjang dengan mapel yang sesuai.</li>
                <li><span class="jp-warning-red">Jika kelas/mapel tidak ditemukan di database, baris akan dilewati.</span></li>
                <li><span class="jp-warning-orange">Jika guru tidak ditemukan, jadwal tetap dibuat dengan status "kosong".</span></li>
            </ol>
        </div>

        <div class="card jp-import-card">
            <div class="card-header">
                <h5><i class="fas fa-file-import jp-import-icon"></i>Upload File Excel</h5>
            </div>
            <div class="card-body">
                <div class="jp-template-actions">
                    <a href="{{ route('admin.jadwal-pelajaran.template') }}" class="btn btn-success">
                        <i class="fas fa-download"></i> Download Template
                    </a>
                </div>

                <form action="{{ route('admin.jadwal-pelajaran.import.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <label class="jp-form-label" for="tahunAjaranId">Tahun Ajaran Target:</label>
                    <select name="tahun_ajaran_id" id="tahunAjaranId" class="jp-import-select" required>
                        @foreach(\App\Models\TahunAjaran::orderBy('tanggal_mulai', 'desc')->get() as $ta)
                            <option value="{{ $ta->id }}" {{ $ta->is_active ? 'selected' : '' }}>
                                {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>

                    <label for="fileInput" class="upload-area" data-upload-area>
                        <div class="upload-area-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div class="upload-area-title">Klik untuk memilih file atau drag & drop</div>
                        <div class="upload-area-help">Format: .xlsx, .xls (Maks 5MB)</div>
                    </label>

                    <input type="file" name="file" id="fileInput" class="d-none" accept=".xlsx,.xls" required>

                    <div class="file-selected d-none" id="fileSelected">
                        <div class="file-selected-main">
                            <i class="fas fa-file-excel file-selected-icon"></i>
                            <div>
                                <strong id="fileName">-</strong>
                                <div class="file-selected-size" id="fileSize">-</div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-secondary jp-clear-file-btn" data-clear-file>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="jp-actions">
                        <a href="{{ route('admin.jadwal-pelajaran.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/jadwal-pelajaran/import.js'])
@endsection
