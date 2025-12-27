@extends('layouts.sneat')

@section('title')
Edit Siswa - {{ $siswa->nama_lengkap ?? 'N/A' }}
@endsection

@section('page-title', 'Edit Data Siswa')

@section('page-subtitle')
Perbarui data {{ $siswa->nama_lengkap ?? 'N/A' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
    /* Card Styles */
    .card { 
        background: white; 
        border-radius: 12px; 
        box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
        margin-bottom: 24px; 
        padding: 24px; 
    }
    
    /* Header Section */
    .page-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 18px;
    }
    
    .btn-back:hover {
        background: #e2e8f0;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateX(-2px);
    }
    
    .page-header-title {
        flex: 1;
    }
    
    .page-header-title h2 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
    }
    
    .page-header-title p {
        margin: 4px 0 0 0;
        font-size: 14px;
        color: #64748b;
    }
    
    .student-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #eff6ff;
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }
    
    /* Form Styles */
    .form-title { 
        font-size: 16px; 
        font-weight: 700; 
        color: #1e293b; 
        margin-bottom: 20px; 
        padding-bottom: 10px; 
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .form-group { 
        margin-bottom: 16px; 
    }
    
    .form-label { 
        display: block; 
        margin-bottom: 6px; 
        font-weight: 500; 
        font-size: 14px; 
        color: #475569; 
    }
    
    .text-muted {
        color: #94a3b8;
        font-size: 12px;
    }
    
    .form-control { 
        width: 100%; 
        padding: 10px 12px; 
        border: 1px solid #cbd5e1; 
        border-radius: 8px; 
        font-size: 14px; 
        box-sizing: border-box;
        transition: all 0.2s ease;
        font-family: inherit;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    .form-control:hover {
        border-color: #94a3b8;
    }
    
    select.form-control {
        cursor: pointer;
    }
    
    /* Grid System */
    .row { 
        display: flex; 
        gap: 20px; 
        flex-wrap: wrap; 
    }
    
    .col { 
        flex: 1; 
        min-width: 250px; 
    }
    
    /* Button Styles */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-bottom: 40px;
        padding-top: 8px;
    }
    
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
    }
    
    .btn-warning {
        background: #f59e0b;
        color: white;
    }
    
    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    
    .btn-warning:active {
        transform: translateY(0);
    }
    
    .btn-secondary {
        background: white;
        border: 1px solid #cbd5e1;
        color: #475569;
    }
    
    .btn-secondary:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #1e293b;
    }
    
    .btn-secondary:active {
        background: #f1f5f9;
    }
    
    /* Alert/Error Messages */
    .alert {
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .alert-danger {
        background: #fee2e2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }
    
    .alert-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
    }
    
    /* Info Box */
    .info-box {
        background: #f0f9ff;
        border: 1px solid #bae6fd;
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
        font-size: 13px;
        color: #0c4a6e;
        display: flex;
        align-items: start;
        gap: 10px;
    }
    
    .info-box i {
        color: #0284c7;
        font-size: 16px;
        margin-top: 2px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .row {
            flex-direction: column;
        }
        
        .col {
            min-width: 100%;
        }
        
        .form-actions {
            flex-direction: column-reverse;
        }
        
        .btn {
            width: 100%;
        }
        
        .page-header {
            flex-wrap: wrap;
        }
        
        .page-header-title h2 {
            font-size: 20px;
        }
        
        .student-badge {
            width: 100%;
            justify-content: center;
        }
    }
</style>

{{-- Header with Back Button --}}
<div class="page-header">
    <a href="{{ route('admin.users.siswa') }}" class="btn-back" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div class="page-header-title">
        <h2>Edit Data Siswa</h2>
        <p>Perbarui informasi data siswa</p>
    </div>
    <div class="student-badge">
        <i class="fas fa-user-graduate"></i>
        {{ $siswa->nama_lengkap }}
    </div>
</div>

{{-- Display Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</strong>
        <ul style="margin: 8px 0 0 0; padding-left: 20px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Display Success Message --}}
@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

{{-- Info Box --}}
<div class="info-box">
    <i class="fas fa-info-circle"></i>
    <div>
        <strong>Catatan:</strong> Form ini hanya untuk mengubah data akun dan status siswa. Data lengkap biodata siswa lainnya tetap tersimpan di sistem.
    </div>
</div>

<form action="{{ route('admin.users.update-siswa', $siswa->id) }}" method="POST">
    @csrf
    @method('PUT')
    
    {{-- Section 1: Account Information --}}
    <div class="card">
        <h5 class="form-title">
            <i class="fas fa-user-lock" style="color: #3b82f6;"></i>
            Informasi Akun
        </h5>
        
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $siswa->user->username) }}" placeholder="Masukkan username" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Email <small class="text-muted">(Opsional)</small></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $siswa->user->email) }}" placeholder="contoh@email.com">
                </div>
            </div>
        </div>
        
        <div class="form-group">
            <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan password baru (opsional)">
        </div>

        <div class="form-group">
            <label class="form-label">Status Akun <span style="color: #ef4444;">*</span></label>
            <select name="is_active" class="form-control">
                <option value="1" {{ $siswa->user->is_active ? 'selected' : '' }}>✓ Aktif - Dapat Login</option>
                <option value="0" {{ !$siswa->user->is_active ? 'selected' : '' }}>✗ Non-Aktif - Tidak Dapat Login</option>
            </select>
        </div>
    </div>

    {{-- Section 2: Student Data --}}
    <div class="card">
        <h5 class="form-title">
            <i class="fas fa-id-card" style="color: #10b981;"></i>
            Data Siswa
        </h5>

        <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color: #ef4444;">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" placeholder="Nama lengkap siswa" required>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">NISN <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}" placeholder="Nomor Induk Siswa Nasional" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">NIS <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}" placeholder="Nomor Induk Siswa" required>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 3: Academic Information --}}
    <div class="card">
        <h5 class="form-title">
            <i class="fas fa-school" style="color: #8b5cf6;"></i>
            Informasi Akademik
        </h5>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Kelas <span style="color: #ef4444;">*</span></label>
                    <select name="kelas_id" class="form-control" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasList as $kelas)
                            <option value="{{ $kelas->id }}" {{ $siswa->kelas_id == $kelas->id ? 'selected' : '' }}>
                                {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Cabang <span style="color: #ef4444;">*</span></label>
                    <select name="cabang_id" class="form-control" required>
                        <option value="">-- Pilih Cabang --</option>
                        @foreach($cabangList as $cabang)
                            <option value="{{ $cabang->id }}" {{ $siswa->cabang_id == $cabang->id ? 'selected' : '' }}>
                                {{ $cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Status Siswa <span style="color: #ef4444;">*</span></label>
            <select name="status" class="form-control">
                <option value="aktif" {{ $siswa->status == 'aktif' ? 'selected' : '' }}>✓ Aktif - Sedang Belajar</option>
                <option value="lulus" {{ $siswa->status == 'lulus' ? 'selected' : '' }}>🎓 Lulus</option>
                <option value="pindah" {{ $siswa->status == 'pindah' ? 'selected' : '' }}>🔄 Pindah Sekolah</option>
                <option value="keluar" {{ $siswa->status == 'keluar' ? 'selected' : '' }}>✗ Keluar</option>
            </select>
        </div>
    </div>

    {{-- Hidden Fields for Other Data --}}
    <input type="hidden" name="jenis_kelamin" value="{{ $siswa->jenis_kelamin }}">
    <input type="hidden" name="tempat_lahir" value="{{ $siswa->tempat_lahir }}">
    <input type="hidden" name="tanggal_lahir" value="{{ $siswa->tanggal_lahir }}">
    <input type="hidden" name="alamat" value="{{ $siswa->alamat }}">
    <input type="hidden" name="nama_ayah" value="{{ $siswa->nama_ayah }}">
    <input type="hidden" name="nama_ibu" value="{{ $siswa->nama_ibu }}">
    <input type="hidden" name="telepon_orangtua" value="{{ $siswa->telepon_orangtua }}">
    <input type="hidden" name="tanggal_masuk" value="{{ $siswa->tanggal_masuk }}">

    {{-- Form Actions --}}
    <div class="form-actions">
        <a href="{{ route('admin.users.siswa') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Batal
        </a>
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i>
            Update Data
        </button>
    </div>
</form>
@endsection