@extends('layouts.sneat')

@section('title', 'Pengaturan Akun')
@section('page-title', 'Pengaturan Akun')

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

@section('styles')
    @vite(['resources/css/account/settings.css'])
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y account-settings-page">

    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb breadcrumb-style1">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Pengaturan Akun</li>
            </ol>
        </nav>
        <h4 class="fw-bold">Pengaturan Akun</h4>
    </div>

    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
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
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username', $user->username) }}">
                            @error('username') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            
                            @if(auth()->user()->isAdmin())
                                {{-- Admin can edit full email --}}
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}">
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @else
                                {{-- Non-admin can only edit local part --}}
                                @php
                                    $emailParts = explode('@', $user->email);
                                    $localPart = $emailParts[0] ?? '';
                                    $domainPart = $emailParts[1] ?? '';
                                @endphp
                                <div class="input-group">
                                    <input type="text" name="email_local" class="form-control @error('email_local') is-invalid @enderror" value="{{ old('email_local', $localPart) }}">
                                    <span class="input-group-text">@ {{ $domainPart }}</span>
                                </div>
                                <small class="text-muted">Domain email tidak dapat diubah.</small>
                                @error('email_local') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Pribadi <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
                            <input type="email" class="form-control @error('personal_email') is-invalid @enderror" name="personal_email" value="{{ old('personal_email', $user->personal_email) }}">
                            @error('personal_email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="{{ $user->roleRelation ? $user->roleRelation->display_name : ucwords(str_replace('_', ' ', $user->role)) }}" disabled>
                            <small class="text-muted">Hubungi Administrator jika terdapat kesalahan role.</small>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
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
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-password-toggle data-target="current_password">
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
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-password-toggle data-target="new_password">
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
                                <button class="btn btn-outline-secondary toggle-password" type="button"
                                    data-password-toggle data-target="new_password_confirmation">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="alert alert-warning border-0 d-flex align-items-center mb-4 mt-2">
                            <i class="fas fa-info-circle me-2 fs-5"></i>
                            <div class="account-session-note">Sesi akan berakhir otomatis setelah update password.</div>
                        </div>

                        <button type="submit" class="btn btn-warning text-white w-100">
                            <i class="fas fa-key me-1"></i> Update Password Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        @if(auth()->user()->isAdmin() || auth()->user()->isKetuaPKBM())
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="card h-100 shadow-sm border-0">
                <h5 class="card-header"><i class="fas fa-shield-alt me-2 text-primary"></i>Pengaturan Keamanan Khusus</h5>
                <div class="card-body">
                    <p class="text-sm text-muted mb-4">Ubah Pertanyaan Keamanan dan PIN yang Anda gunakan untuk pemulihan akun darurat (Lupa Kredensial).</p>
                    <form action="{{ route('account.update-security') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Pilih Pertanyaan Keamanan Baru</label>
                            <select name="security_question" class="form-select @error('security_question') is-invalid @enderror" required>
                                <option value="" disabled selected>Pilih pertanyaan...</option>
                                <option value="Apa nama SD Anda?" {{ old('security_question', $user->security_question) == 'Apa nama SD Anda?' ? 'selected' : '' }}>Apa nama SD Anda?</option>
                                <option value="Siapa nama teman masa kecil Anda?" {{ old('security_question', $user->security_question) == 'Siapa nama teman masa kecil Anda?' ? 'selected' : '' }}>Siapa nama teman masa kecil Anda?</option>
                                <option value="Di kota mana Anda bertemu pasangan Anda?" {{ old('security_question', $user->security_question) == 'Di kota mana Anda bertemu pasangan Anda?' ? 'selected' : '' }}>Di kota mana Anda bertemu pasangan Anda?</option>
                                <option value="Apa nama hewan peliharaan pertama Anda?" {{ old('security_question', $user->security_question) == 'Apa nama hewan peliharaan pertama Anda?' ? 'selected' : '' }}>Apa nama hewan peliharaan pertama Anda?</option>
                                <option value="Apa judul film favorit Anda?" {{ old('security_question', $user->security_question) == 'Apa judul film favorit Anda?' ? 'selected' : '' }}>Apa judul film favorit Anda?</option>
                            </select>
                            @error('security_question') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Jawaban (Case-insensitive)</label>
                            <input type="text" name="security_answer" class="form-control @error('security_answer') is-invalid @enderror" placeholder="Jawaban baru Anda" required>
                            @error('security_answer') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">6-Digit PIN Keamanan Baru</label>
                                <div class="input-group">
                                    <input type="password" id="security_pin" name="security_pin" class="form-control @error('security_pin') is-invalid @enderror" minlength="6" maxlength="6" pattern="\d{6}" placeholder="••••••" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-password-toggle data-target="security_pin">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Hanya Angka (0-9).</small>
                                @error('security_pin') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi PIN</label>
                                <div class="input-group">
                                    <input type="password" id="security_pin_confirmation" name="security_pin_confirmation" class="form-control" minlength="6" maxlength="6" pattern="\d{6}" placeholder="••••••" required>
                                    <button class="btn btn-outline-secondary toggle-password" type="button"
                                        data-password-toggle data-target="security_pin_confirmation">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <div class="mb-3">
                            <label class="form-label text-danger">Otorisasi Pergantian Keamanan</label>
                            <div class="input-group">
                                <input type="password" id="current_password_security" name="current_password" class="form-control border-danger @error('current_password_security') is-invalid @enderror" placeholder="Masukkan Password Login Anda Saat Ini" required>
                                <button class="btn btn-outline-danger toggle-password" type="button"
                                    data-password-toggle data-target="current_password_security">
                                    <i class="fas fa-eye-slash"></i>
                                </button>
                            </div>
                            <small class="text-danger"><i class="fas fa-exclamation-triangle me-1"></i> Wajib memasukkan Password Saat Ini untuk mengubah keamanan.</small>
                            @if($errors->has('current_password_security'))
                                <div class="text-danger font-medium text-sm mt-1">{{ $errors->first('current_password_security') }}</div>
                            @endif
                        </div>

                        <button type="submit" class="btn btn-primary mt-2">
                            <i class="fas fa-user-shield me-1"></i> Perbarui Keamanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/account/settings.js'])
@endsection
