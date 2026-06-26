@extends('layouts.sneat')

@section('title')
    Edit Wali Siswa - {{ $orangTua->name ?? 'N/A' }}
@endsection

@section('page-title', 'Edit Data Wali Siswa')

@section('page-subtitle')
    Perbarui data {{ $orangTua->name ?? 'N/A' }}
@endsection

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/wali-siswa-edit.css'])
@endsection

@section('content')
{{-- Header with Back Button --}}
    <div class="page-header">
        <a href="{{ route('admin.users.show-wali-siswa', $orangTua->id) }}" class="btn-back" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="page-header-title">
            <h2>Edit Data Wali Siswa</h2>
            <p>Perbarui informasi akun wali siswa</p>
        </div>
        <div class="parent-badge">
            <i class="fas fa-user-friends"></i>
            {{ $orangTua->name }}
        </div>
    </div>

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</strong>
            <ul class="validation-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Display Success Message --}}


    {{-- Info Box --}}
    <div class="info-box">
        <i class="fas fa-info-circle"></i>
        <div>
            <strong>Catatan:</strong> Gunakan form ini untuk mengubah data akun wali siswa. Data siswa yang terhubung dengan
            wali siswa ini tidak akan terpengaruh.
        </div>
    </div>

    <form action="{{ route('admin.users.update-wali-siswa', $orangTua->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Section 1: Account Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-lock section-icon--account"></i>
                Informasi Akun
            </h5>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required-mark">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $orangTua->name) }}"
                            placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required-mark">*</span></label>
                        <input type="text" name="username" class="form-control"
                            value="{{ old('username', $orangTua->username) }}" placeholder="Masukkan username" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Email <small class="text-muted">(Opsional)</small></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $orangTua->email) }}"
                            placeholder="contoh@email.com">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">No. Telepon/WA <small class="text-muted">(Opsional)</small></label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $orangTua->phone) }}"
                            placeholder="Contoh: 08123456789">
                        <small class="text-muted">
                            Nomor telepon/WhatsApp untuk komunikasi dengan sekolah
                        </small>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Pribadi <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
                <input type="email" name="personal_email" class="form-control"
                    value="{{ old('personal_email', $orangTua->personal_email) }}" placeholder="contoh: nama@gmail.com">
                @error('personal_email')
                    <div class="text-danger field-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin
                        diubah)</small></label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password baru (opsional)">
            </div>

            <div class="form-group">
                <label class="form-label">Status Akun <span class="required-mark">*</span></label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $orangTua->is_active ? 'selected' : '' }}><i class="fas fa-check"></i> Aktif - Dapat Login</option>
                    <option value="0" {{ !$orangTua->is_active ? 'selected' : '' }}><i class="fas fa-times"></i> Non-Aktif - Tidak Dapat Login</option>
                </select>
            </div>
        </div>

        {{-- Section 2: Children Information with Editable Relationship --}}
        @if($orangTua->studentParents && $orangTua->studentParents->count() > 0)
            <div class="card">
                <h5 class="form-title">
                    <i class="fas fa-users section-icon--children"></i>
                    Data Anak Terdaftar & Hubungan Keluarga
                </h5>
                <p class="section-note">
                    <i class="fas fa-info-circle"></i>
                    Anda dapat mengubah hubungan keluarga untuk setiap siswa di bawah ini.
                </p>

                <div class="child-card-list">
                    @foreach($orangTua->studentParents as $sp)
                        <div class="child-card">
                            <div class="child-card-header">
                                <div class="child-card-title">
                                    <i class="fas fa-user-graduate"></i>
                                    {{ $sp->siswa->nama_lengkap }}
                                </div>
                                <small class="child-card-meta">
                                    NIS: {{ $sp->siswa->nis }} • NISN: {{ $sp->siswa->nisn }} •
                                    Kelas: {{ $sp->siswa->kelas->nama_kelas ?? '-' }}
                                </small>
                            </div>

                            <div class="row child-relationship-row">
                                <div class="col">
                                    <div class="form-group form-group-compact">
                                        <label class="form-label">Hubungan Keluarga</label>
                                        <select name="relationships[{{ $sp->id }}]" class="form-control"
                                            id="hubungan_keluarga_{{ $sp->id }}"
                                            data-relationship-edit-id="{{ $sp->id }}">
                                            <option value="ayah_kandung" {{ $sp->relationship == 'ayah_kandung' ? 'selected' : '' }}>
                                                Ayah Kandung</option>
                                            <option value="ibu_kandung" {{ $sp->relationship == 'ibu_kandung' ? 'selected' : '' }}>Ibu
                                                Kandung</option>
                                            <option value="wali" {{ $sp->relationship == 'wali' ? 'selected' : '' }}>Wali</option>
                                            <option value="ayah_tiri" {{ $sp->relationship == 'ayah_tiri' ? 'selected' : '' }}>Ayah
                                                Tiri</option>
                                            <option value="ibu_tiri" {{ $sp->relationship == 'ibu_tiri' ? 'selected' : '' }}>Ibu Tiri
                                            </option>
                                            <option value="lainnya" {{ $sp->relationship == 'lainnya' || (!in_array($sp->relationship, ['ayah_kandung', 'ibu_kandung', 'wali', 'ayah_tiri', 'ibu_tiri'])) ? 'selected' : '' }}>Lainnya</option>
                                        </select>
                                    </div>

                                    <div id="otherRelationshipEditField_{{ $sp->id }}"
                                        class="relationship-extra {{ $sp->relationship == 'lainnya' || (!in_array($sp->relationship, ['ayah_kandung', 'ibu_kandung', 'wali', 'ayah_tiri', 'ibu_tiri'])) ? '' : 'd-none' }}">
                                        <div class="form-group form-group-compact">
                                            <label class="form-label">Sebutkan Hubungan Keluarga Lainnya</label>
                                            <input type="text" class="form-control" name="relationships_lainnya[{{ $sp->id }}]"
                                                placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll"
                                                value="{{ (!in_array($sp->relationship, ['ayah_kandung', 'ibu_kandung', 'wali', 'ayah_tiri', 'ibu_tiri'])) ? $sp->relationship : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col child-status-column">
                                    <div class="child-status-list">
                                        @if($sp->is_primary)
                                            <span class="child-status-badge child-status-badge--primary">
                                                <i class="fas fa-star"></i> Kontak Utama
                                            </span>
                                        @endif
                                        @if($sp->can_access_academic)
                                            <span class="child-status-badge child-status-badge--academic">
                                                <i class="fas fa-check-circle"></i> Akses Akademik
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Form Actions --}}
        <div class="form-actions">
            <a href="{{ route('admin.users.show-wali-siswa', $orangTua->id) }}" class="btn btn-secondary">
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

@section('scripts')
    @vite(['resources/js/admin/users/wali-siswa-form.js'])
@endsection
