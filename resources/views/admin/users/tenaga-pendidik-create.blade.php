@extends('layouts.sneat')

@section('title', 'Tambah Tenaga Pendidik')
@section('page-title', 'Tambah Tenaga Pendidik')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
    .card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; padding: 24px; }
    .form-title { font-size: 16px; font-weight: 700; color: #1e293b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #e2e8f0; }
    .form-group { margin-bottom: 16px; }
    .form-label { display: block; margin-bottom: 6px; font-weight: 500; font-size: 14px; color: #475569; }
    .form-control { width: 100%; padding: 10px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 14px; box-sizing: border-box; }
    .form-control:focus { border-color: #3b82f6; outline: none; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1); }
    .row { display: flex; gap: 20px; flex-wrap: wrap; }
    .col { flex: 1; min-width: 250px; }
    .btn-primary { background: #2563eb; color: white; padding: 10px 24px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;}
    .btn-secondary { background: white; border: 1px solid #cbd5e1; color: #475569; padding: 10px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;}
    .text-danger { color: #ef4444; font-size: 12px; margin-top: 4px; }

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
</style>

<form action="{{ route('admin.users.store-tenaga-pendidik') }}" method="POST">
    @csrf

    {{-- CARD 1: INFORMASI AKUN --}}
    <div class="card">
        <h5 class="form-title">1. Informasi Akun (Login)</h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Username <span style="color:red">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                    @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Email <span style="color:red">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Password <span style="color:red">*</span></label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control" required>
                        <button type="button" class="toggle-password" onclick="togglePassword('password')">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Role / Jabatan <span style="color:red">*</span></label>
                    <select name="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ old('role') == $role ? 'selected' : '' }}>
                                {{ ucwords(str_replace('_', ' ', $role)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('role') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Cabang Penempatan <span style="color:red">*</span></label>
            <select name="cabang_id" class="form-control" required>
                <option value="">-- Pilih Cabang --</option>
                @foreach($cabangList as $cabang)
                    <option value="{{ $cabang->id }}" {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                        {{ $cabang->nama_cabang }}
                    </option>
                @endforeach
            </select>
            @error('cabang_id') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
    </div>

    {{-- CARD 2: BIODATA --}}
    <div class="card">
        <h5 class="form-title">2. Biodata Lengkap</h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap (Gelar) <span style="color:red">*</span></label>
                    <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
                    @error('nama_lengkap') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">NIP (Opsional)</label>
                    <input type="text" name="nip" class="form-control" value="{{ old('nip') }}">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Jenis Kelamin <span style="color:red">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">No. Telepon / WA <span style="color:red">*</span></label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir <span style="color:red">*</span></label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span style="color:red">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Pendidikan Terakhir <span style="color:red">*</span></label>
            <input type="text" name="pendidikan_terakhir" class="form-control" placeholder="Contoh: S1 Pendidikan Matematika" value="{{ old('pendidikan_terakhir') }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat Lengkap <span style="color:red">*</span></label>
            <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
        </div>
    </div>

    <div style="display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 40px;">
        <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn-secondary">Batal</a>
        <button type="submit" class="btn-primary">Simpan Data</button>
    </div>
</form>

<script>
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
