@extends('layouts.sneat')

@section('title', 'Tambah Tenaga Pendidik')
@section('page-title', 'Tambah Tenaga Pendidik')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/tenaga-pendidik-create.css'])
@endsection

@section('content')
<form action="{{ route('admin.users.store-tenaga-pendidik') }}" method="POST">
        @csrf

        {{-- CARD 1: INFORMASI AKUN --}}
        <div class="card">
            <h5 class="form-title">1. Informasi Akun (Login)</h5>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required-mark">*</span></label>
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}" required>
                        @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Email <span class="required-mark">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Password <span class="required-mark">*</span></label>
                        <div class="password-wrapper">
                            <input type="password" name="password" id="password" class="form-control" required>
                            <button type="button" class="toggle-password" data-toggle-password data-target="password">
                                <i class="fas fa-eye" id="password-icon"></i>
                            </button>
                        </div>
                        @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Role / Jabatan <span class="required-mark">*</span></label>
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
            <div class="form-group" id="cabangGroup">
                <label class="form-label">Cabang Penempatan <span class="required-mark">*</span></label>
                <select name="cabang_id" id="cabangSelect" class="form-control" required>
                    <option value="">-- Pilih Cabang --</option>
                    @foreach($cabangList as $cabang)
                        <option value="{{ $cabang->id }}" {{ old('cabang_id') == $cabang->id ? 'selected' : '' }}>
                            {{ $cabang->nama_cabang }}
                        </option>
                    @endforeach
                </select>
                <input type="hidden" name="cabang_id_hidden" id="cabangHidden" value="" disabled>
                <small id="cabangInfo" class="text-muted cabang-info d-none">
                    <i class="fas fa-info-circle"></i> Role ini bersifat fleksibel dan dapat mengakses semua cabang,
                    sehingga penempatan otomatis di Gedung Utama.
                </small>
                @error('cabang_id') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
        </div>

        {{-- CARD 2: BIODATA --}}
        <div class="card">
            <h5 class="form-title">2. Biodata Lengkap</h5>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap (Gelar) <span class="required-mark">*</span></label>
                        <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                            required>
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
                        <label class="form-label">Jenis Kelamin <span class="required-mark">*</span></label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">No. Telepon / WA <span class="required-mark">*</span></label>
                        <input type="text" name="telepon" class="form-control" value="{{ old('telepon') }}" required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Email Pribadi <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
                <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email') }}" placeholder="contoh: nama@gmail.com">
                @error('personal_email') <span class="text-danger">{{ $message }}</span> @enderror
            </div>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span class="required-mark">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}"
                            required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span class="required-mark">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}"
                            required>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Pendidikan Terakhir <span class="required-mark">*</span></label>
                <input type="text" name="pendidikan_terakhir" class="form-control"
                    placeholder="Contoh: S1 Pendidikan Matematika" value="{{ old('pendidikan_terakhir') }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Alamat Lengkap <span class="required-mark">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat') }}</textarea>
            </div>
        </div>

        <div class="tp-form-actions">
            <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn-secondary">Batal</a>
            <button type="submit" class="btn-primary">Simpan Data</button>
        </div>
    </form>
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/tenaga-pendidik-form.js'])
@endsection