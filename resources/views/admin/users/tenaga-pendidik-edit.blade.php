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

@section('styles')
    @vite(['resources/css/admin/users/tenaga-pendidik-edit.css'])
@endsection

@section('content')
{{-- Header with Back Button --}}
<div class="page-header">
    <a href="{{ url()->previous(route('admin.users.tenaga-pendidik')) }}" class="btn-back" title="Kembali">
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
        <ul class="validation-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Display Success Message --}}


<form action="{{ route('admin.users.update-tenaga-pendidik', $tenagaPendidik->user_id) }}" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="_return_url" value="{{ url()->previous(route('admin.users.tenaga-pendidik')) }}">

    {{-- Section 1: Account Information --}}
    <div class="card">
        <h5 class="form-title">
            <i class="fas fa-user-lock section-icon section-icon-primary"></i>
            1. Informasi Akun
        </h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Username <span class="required-mark">*</span></label>
                    <input type="text" name="username" class="form-control" value="{{ old('username', $tenagaPendidik->user->username) }}" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Email <span class="required-mark">*</span></label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $tenagaPendidik->user->email) }}" required>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin diubah)</small></label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru (opsional)">
                        <button type="button" class="toggle-password" data-toggle-password data-target="password">
                            <i class="fas fa-eye" id="password-icon"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Role / Jabatan <span class="required-mark">*</span></label>
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
                    <label class="form-label">Cabang Penempatan <span class="required-mark">*</span></label>
                    <select name="cabang_id" id="cabangSelect" class="form-control" required>
                        @foreach($cabangList as $cabang)
                            <option value="{{ $cabang->id }}" {{ $tenagaPendidik->user->cabang_id == $cabang->id ? 'selected' : '' }}>
                                {{ $cabang->nama_cabang }}
                            </option>
                        @endforeach
                    </select>
                    <input type="hidden" name="cabang_id_hidden" id="cabangHidden" value="" disabled>
                    <small id="cabangInfo" class="text-muted cabang-info d-none">
                        <i class="fas fa-info-circle"></i> Role ini bersifat fleksibel dan dapat mengakses semua cabang, sehingga penempatan otomatis di Gedung Utama.
                    </small>
                </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Status Akun <span class="required-mark">*</span></label>
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
            <i class="fas fa-id-card section-icon section-icon-success"></i>
            2. Biodata Pribadi
        </h5>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap (dengan Gelar) <span class="required-mark">*</span></label>
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
                    <label class="form-label">Jenis Kelamin <span class="required-mark">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="L" {{ $tenagaPendidik->jenis_kelamin == 'L' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="P" {{ $tenagaPendidik->jenis_kelamin == 'P' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">No. Telepon / WhatsApp <span class="required-mark">*</span></label>
                    <input type="text" name="telepon" class="form-control" value="{{ old('telepon', $tenagaPendidik->telepon) }}" placeholder="Contoh: 081234567890" required>
                </div>
            </div>
        </div>
        <div class="form-group">
                <label class="form-label">Email Pemulihan <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
            <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email', $tenagaPendidik->user->personal_email) }}" placeholder="contoh: nama@gmail.com">
            @error('personal_email') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tempat Lahir <span class="required-mark">*</span></label>
                    <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $tenagaPendidik->tempat_lahir) }}" placeholder="Contoh: Jakarta" required>
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label class="form-label">Tanggal Lahir <span class="required-mark">*</span></label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', \Carbon\Carbon::parse($tenagaPendidik->tanggal_lahir)->format('Y-m-d')) }}" required>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Pendidikan Terakhir <span class="required-mark">*</span></label>
            <input type="text" name="pendidikan_terakhir" class="form-control" value="{{ old('pendidikan_terakhir', $tenagaPendidik->pendidikan_terakhir) }}" placeholder="Contoh: S1 Pendidikan Guru Sekolah Dasar" required>
        </div>
        <div class="form-group">
            <label class="form-label">Alamat Lengkap <span class="required-mark">*</span></label>
            <textarea name="alamat" class="form-control" rows="3" placeholder="Masukkan alamat lengkap dengan RT/RW, Kelurahan, Kecamatan, Kota/Kabupaten, Provinsi" required>{{ old('alamat', $tenagaPendidik->alamat) }}</textarea>
        </div>
    </div>

    {{-- Form Actions --}}
    <div class="form-actions">
        <a href="{{ url()->previous(route('admin.users.tenaga-pendidik')) }}" class="btn btn-secondary">
            <i class="fas fa-times"></i>
            Batal
        </a>
        <button type="submit" class="btn btn-warning">
            <i class="fas fa-save"></i>
            Update Perubahan
        </button>
    </div>
</form>
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/tenaga-pendidik-form.js'])
@endsection
