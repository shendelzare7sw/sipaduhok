@extends('layouts.sneat')

@section('title')
Edit Tenaga Pendidik - {{ $tenagaPendidik->nama_lengkap ?? 'N/A' }}
@endsection

@section('page-title', 'Edit Tenaga Pendidik')

@section('page-subtitle')
Perbarui data {{ $tenagaPendidik->nama_lengkap ?? 'N/A' }}
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

    /* Form Styles */
    .form-title {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e2e8f0;
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

    textarea.form-control {
        resize: vertical;
        min-height: 80px;
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

    /* Password Field Styles */
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }
    .password-wrapper input {
        padding-right: 45px;
    }
    .toggle-password {
        position: absolute;
        right: 12px;
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: color 0.2s ease;
    }
    .toggle-password:hover {
        color: #3b82f6;
    }
    .toggle-password i {
        font-size: 16px;
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

        .page-header-title h2 {
            font-size: 20px;
        }
    }
</style>

{{-- Header with Back Button --}}
<div class="page-header">
    <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn-back" title="Kembali">
        <i class="fas fa-arrow-left"></i>
    </a>
    <div class="page-header-title">
        <h2>Edit Data Tenaga Pendidik</h2>
        <p>Perbarui informasi tenaga pendidik</p>
    </div>
</div>

{{-- Display Validation Errors --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Terjadi kesalahan:</strong>
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

<form action="{{ route('admin.users.update-tenaga-pendidik', $tenagaPendidik->id) }}" method="POST">
    @csrf
    @method('PUT')

    {{-- Section 1: Account Information --}}
    <div class="card">
        <h5 class="form-title">
            <i class="fas fa-user-lock" style="color: #3b82f6; margin-right: 8px;"></i>
            1. Informasi Akun
        </h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $tenagaPendidik->user->username) }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Email <span style="color: #ef4444;">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $tenagaPendidik->email) }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru (opsional)">
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Role / Jabatan <span style="color: #ef4444;">*</span></label>
                    <select name="role" class="form-control" required>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ $tenagaPendidik->user->role == $role ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group" id="cabangGroup">
                    <label class="form-label">Cabang Penempatan <span style="color: #ef4444;">*</span></label>
                    <select name="cabang_id" id="cabangSelect" class="form-control" required>
                        @foreach($cabangList as $cabang)
                            <option value="{{ $cabang->id }}" {{ $tenagaPendidik->user->cabang_id == $cabang->id ? 'selected' : '' }}>
                                {{ $cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="cabang_id_hidden" id="cabangHidden" value="" disabled>
                    <small id="cabangInfo" class="text-muted" style="display: none; margin-top: 6px;">
                        <i class="fas fa-info-circle"></i> Role ini bersifat fleksibel dan dapat mengakses semua cabang, sehingga penempatan otomatis di Gedung Utama.
                    </small>
                </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Status Akun <span style="color: #ef4444;">*</span></label>
                    <select name="is_active" class="form-control">
                        <option value="1" {{ $tenagaPendidik->user->is_active ? 'selected' : '' }}>Aktif</option>
                        <option value="0" {{ !$tenagaPendidik->user->is_active ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Section 2: Personal Data --}}
    <div class="card">
        <h5 class="form-title">
            <i class="fas fa-id-card" style="color: #10b981; margin-right: 8px;"></i>
            2. Biodata Pribadi
        </h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap (dengan Gelar) <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $tenagaPendidik->nama_lengkap) }}" placeholder="Contoh: Dr. Ahmad Hidayat, S.Pd., M.Pd." required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">NIP <small class="text-muted">(Opsional)</small></label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip', $tenagaPendidik->nip) }}" placeholder="Nomor Induk Pegawai">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ $tenagaPendidik->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $tenagaPendidik->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $tenagaPendidik->telepon) }}" placeholder="Contoh: 081234567890" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $tenagaPendidik->tempat_lahir) }}" placeholder="Contoh: Jakarta" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', $tenagaPendidik->tanggal_lahir) }}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Pendidikan Terakhir <span style="color: #ef4444;">*</span></label>
            <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ old('pendidikan_terakhir', $tenagaPendidik->pendidikan_terakhir) }}" placeholder="Contoh: S1 Pendidikan Guru Sekolah Dasar" required>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat Lengkap <span style="color: #ef4444;">*</span></label>
            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap dengan RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi" required>{{ old('alamat', $tenagaPendidik->alamat) }}</textarea>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="form-actions">
        <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Batal
        </a>
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i>
            Update Perubahan
        </button>
    </div>
</form>

<script>
// Flexible roles that should have locked cabang
const flexibleRoles = ['ketua_pkbm', 'sekretaris', 'bendahara', 'wakil_kepala_sekolah'];
const defaultCabangId = '1'; // PKBM House Of Knowledge (Gedung Utama)

const roleSelect = document.querySelector('select[name="role"]');
const cabangSelect = document.getElementById('cabangSelect');
const cabangHidden = document.getElementById('cabangHidden');
const cabangInfo = document.getElementById('cabangInfo');

function handleRoleChange() {
    const selectedRole = roleSelect.value;
    const isFlexible = flexibleRoles.includes(selectedRole);
    
    if (isFlexible) {
        // Lock cabang to Gedung Utama
        cabangSelect.value = defaultCabangId;
        cabangSelect.disabled = true;
        cabangSelect.removeAttribute('name');
        cabangHidden.value = defaultCabangId;
        cabangHidden.name = 'cabang_id';
        cabangHidden.disabled = false;
        cabangInfo.style.display = 'block';
    } else {
        // Unlock cabang selection
        cabangSelect.disabled = false;
        cabangSelect.name = 'cabang_id';
        cabangHidden.disabled = true;
        cabangHidden.removeAttribute('name');
        cabangInfo.style.display = 'none';
    }
}

// Trigger on page load and role change
roleSelect.addEventListener('change', handleRoleChange);
document.addEventListener('DOMContentLoaded', handleRoleChange);

function togglePassword(inputId) {
    const passwordInput = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>
@endsection
