@extends('layouts.sneat')

@section('title', 'Pengaturan Akun')

@section('sidebar-menu')
    @php
        $roleName = auth()->user()->roleRelation ? auth()->user()->roleRelation->name : auth()->user()->role;
        $sidebarMap = [
            'admin' => 'admin.partials.sneat-sidebar-menu',
            'ketua_pkbm' => 'ketua.partials.sneat-sidebar-menu',
            'wakil_kepala_sekolah' => 'waka.partials.sneat-sidebar-menu',
            'sekretaris' => 'sekretaris.partials.sneat-sidebar-menu',
            'bendahara' => 'bendahara.partials.sneat-sidebar-menu',
            'guru_pengajar' => 'guru.partials.sneat-sidebar-menu',
            'wali_kelas' => 'wali-kelas.partials.sneat-sidebar-menu',
            'siswa' => 'siswa.partials.sneat-sidebar-sia',
            'orang_tua' => 'orang-tua.partials.sneat-sidebar-menu',
        ];
        $sidebarView = $sidebarMap[$roleName] ?? 'admin.partials.sneat-sidebar-menu';
    @endphp
    @include($sidebarView)
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengaturan Akun</li>
            </ol>
        </nav>
        <h4 class="fw-bold"><span class="text-muted fw-light">User /</span> Pengaturan Akun</h4>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <h5 class="card-header"><i class="fas fa-user-circle me-2 text-primary"></i>Informasi Profil</h5>
                <div class="card-body">
                    <form action="{{ route('account.update-settings') }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                            @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control bg-light" value="{{ $user->roleRelation ? $user->roleRelation->display_name : $user->role }}" readonly disabled>
                            <small class="text-muted">Role tidak dapat diubah oleh pengguna.</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <h5 class="card-header"><i class="fas fa-lock me-2 text-warning"></i>Keamanan Password</h5>
                <div class="card-body">
                    <form action="{{ route('account.change-password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('current_password') is-invalid @enderror"
                                    id="current_password" name="current_password" placeholder="············">
                                <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePasswordVisibility('current_password', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            @error('current_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('new_password') is-invalid @enderror"
                                    id="new_password" name="new_password" placeholder="············">
                                <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePasswordVisibility('new_password', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <small class="text-muted">Gunakan minimal 8 karakter.</small>
                            @error('new_password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <div class="input-group">
                                <input type="password" class="form-control"
                                    id="new_password_confirmation" name="new_password_confirmation" placeholder="············">
                                <button class="btn btn-outline-secondary toggle-password" type="button" onclick="togglePasswordVisibility('new_password_confirmation', this)">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 d-flex align-items-center mb-4 mt-2">
                            <i class="fas fa-info-circle me-2 fs-5"></i>
                            <div style="font-size: 0.85rem;">Sesi akan berakhir otomatis setelah update password.</div>
                        </div>

                        <button type="submit" class="btn btn-warning text-white w-100">
                            <i class="fas fa-key me-1"></i> Update Password Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, buttonElement) {
        const input = document.getElementById(inputId);
        const icon = buttonElement.querySelector('i');

        if (input.type === 'password') {
            input.type = 'text';
            // Ganti icon mata tertutup ke mata terbuka
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            input.type = 'password';
            // Ganti icon mata terbuka ke mata tertutup
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }
</script>

<style>
    .toggle-password {
        border-color: #d9dee3;
        min-width: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .toggle-password:hover {
        background-color: #f8f9fa;
        border-color: #adb5bd;
        color: #495057;
    }

    .toggle-password:active {
        background-color: #e9ecef;
    }

    .toggle-password i {
        font-size: 16px;
        display: inline-block;
        line-height: 1;
    }

    /* Prevent duplicate icons from browser extensions or other scripts */
    .toggle-password i:not(:first-child) {
        display: none !important;
    }

    /* Ensure only one icon is visible */
    .toggle-password::before,
    .toggle-password::after {
        display: none !important;
    }

    .input-group .form-control.is-invalid {
        z-index: 2;
        border-right: 1px solid #dc3545;
    }

    .input-group .btn {
        z-index: 3;
    }

    .input-group .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    /* Hide browser default password reveal button */
    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }

    input[type="password"]::-webkit-credentials-auto-fill-button,
    input[type="password"]::-webkit-contacts-auto-fill-button {
        display: none !important;
        visibility: hidden;
        pointer-events: none;
        position: absolute;
        right: 0;
    }
</style>
@endsection
