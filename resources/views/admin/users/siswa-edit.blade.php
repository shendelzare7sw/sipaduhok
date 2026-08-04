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

@section('styles')
    @vite(['resources/css/admin/users/siswa-edit.css'])
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/siswa-edit.js'])
@endsection

@section('content')
{{-- Header with Back Button --}}
    <div class="page-header">
        <a href="{{ url()->previous(route('admin.users.siswa')) }}" class="btn-back" title="Kembali">
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
        <div>
            <strong>Catatan:</strong> Form ini untuk mengubah seluruh data siswa, termasuk biodata lengkap dan data akun.
        </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update-siswa', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')
        <input type="hidden" name="_return_url" value="{{ url()->previous(route('admin.users.siswa')) }}">

        {{-- Section 1: Account Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-lock section-icon--account"></i>
                Informasi Akun
            </h5>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required-mark">*</span></label>
                        <input type="text" name="username" class="form-control"
                            value="{{ old('username', $siswa->user->username) }}" placeholder="Masukkan username" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Email <small class="text-muted">(Opsional)</small></label>
                        <input type="email" name="email" class="form-control"
                            value="{{ old('email', $siswa->user->email) }}" placeholder="contoh@email.com">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email Pemulihan <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
                <input type="email" name="personal_email" class="form-control"
                    value="{{ old('personal_email', $siswa->user->personal_email) }}" placeholder="contoh: nama@gmail.com">
                @error('personal_email') <div class="text-danger field-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin
                        diubah)</small></label>
                <div class="password-wrap">
                    <input type="password" id="passwordField" name="password" class="form-control password-input"
                        placeholder="Masukkan password baru (opsional)">
                    <button type="button" class="password-toggle" data-toggle-password data-field="passwordField" data-icon="togglePasswordIcon">
                        <i id="togglePasswordIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status Akun <span class="required-mark">*</span></label>
                <select name="is_active" class="form-control">
                    <option value="1" {{ $siswa->user->is_active ? 'selected' : '' }}><i class="fas fa-check"></i> Aktif - Dapat Login</option>
                    <option value="0" {{ !$siswa->user->is_active ? 'selected' : '' }}><i class="fas fa-times"></i> Non-Aktif - Tidak Dapat Login
                    </option>
                </select>
            </div>
        </div>

        {{-- Section 2: Student Data --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-id-card section-icon--student"></i>
                Data Siswa
            </h5>

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required-mark">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control"
                    value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" placeholder="Nama lengkap siswa" required>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NISN <span class="required-mark">*</span></label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}"
                            placeholder="Nomor Induk Siswa Nasional" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NIS <span class="required-mark">*</span></label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}"
                            placeholder="Nomor Induk Siswa" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span class="required-mark">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'checked' : '' }} required>
                                <span class="radio-label-text">Laki-laki</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'checked' : '' }} required>
                                <span class="radio-label-text">Perempuan</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span class="required-mark">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                            placeholder="Kota tempat lahir" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span class="required-mark">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap <span class="required-mark">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap tempat tinggal"
                    required>{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Agama <span class="required-mark">*</span></label>
                        <input type="text" name="agama" class="form-control" value="{{ old('agama', $siswa->agama) }}"
                            placeholder="Contoh: Kristen, Islam, dll" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Academic Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-school section-icon--academic"></i>
                Informasi Akademik
            </h5>

            <div class="row stack-row">
                <div>
                    <div class="form-group">
                        <label class="form-label">Kelas <span class="required-mark">*</span></label>
                        
                        {{-- Hidden Select for Form Submission --}}
                        <select name="kelas_id" id="kelasSelectEdit" class="d-none" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" 
                                    data-jenjang="{{ $kls->jenjang }}" 
                                    data-cabang-id="{{ $kls->cabang_id }}"
                                    {{ old('kelas_id', $siswa->kelas_id) == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Trigger Box --}}
                        <div class="kelas-display-edit" data-open-kelas-modal-edit>
                            <div id="selectedKelasTextEdit" class="text-muted selected-kelas-placeholder">
                                <i class="fas fa-school me-2"></i> Klik untuk memilih kelas...
                            </div>
                            <div id="selectedKelasChipsEdit" class="d-flex flex-wrap gap-2 mt-1 d-none">
                            </div>
                        </div>
                        @error('kelas_id')
                            <div class="text-danger field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Masuk <span class="required-mark">*</span></label>
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('Y-m-d') : '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status Siswa <span class="required-mark">*</span></label>
                <select name="status" class="form-control">
                    <option value="aktif" {{ $siswa->status == 'aktif' ? 'selected' : '' }}><i class="fas fa-check"></i> Aktif - Sedang Belajar</option>
                    <option value="lulus" {{ $siswa->status == 'lulus' ? 'selected' : '' }}><i class="fas fa-graduation-cap"></i> Lulus</option>
                    <option value="pindah" {{ $siswa->status == 'pindah' ? 'selected' : '' }}><i class="fas fa-exchange-alt"></i> Pindah Sekolah</option>
                    <option value="keluar" {{ $siswa->status == 'keluar' ? 'selected' : '' }}><i class="fas fa-times"></i> Keluar</option>
                </select>
            </div>
        </div>

        {{-- Section 4: Parent Biodata --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-users section-icon--guardian"></i>
                Data Wali Siswa / Wali (Biodata)
            </h5>
            <p class="section-note">
                <i class="fas fa-info-circle"></i>
                Informasi dasar wali siswa/wali siswa untuk keperluan administrasi sekolah.
            </p>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control"
                            value="{{ old('nama_ayah', $siswa->nama_ayah) }}" placeholder="Masukkan nama ayah kandung">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control"
                            value="{{ old('nama_ibu', $siswa->nama_ibu) }}" placeholder="Masukkan nama ibu kandung">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">No. Telepon Wali Siswa (WA Aktif)</label>
                <input type="text" name="telepon_orangtua" class="form-control"
                    value="{{ old('telepon_orangtua', $siswa->telepon_orangtua) }}" placeholder="Contoh: 08123456789">
                <small class="text-muted">
                    Nomor telepon/WhatsApp yang dapat dihubungi untuk komunikasi sekolah
                </small>
            </div>
        </div>

        {{-- Section 5: Parent Management --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-friends section-icon--parent"></i>
                Manajemen Akun Wali Siswa (Login Sistem)
            </h5>
            <p class="section-note">
                <i class="fas fa-info-circle"></i>
                Kelola akun wali siswa yang terhubung dengan siswa ini untuk akses ke sistem. Satu siswa bisa memiliki
                beberapa akun wali siswa/wali.
            </p>

            {{-- Current Parents List --}}
            <div class="form-group">
                <label class="form-label">Wali Siswa Terdaftar</label>
                @if($siswa->studentParents && $siswa->studentParents->count() > 0)
                    <div class="parents-stack">
                        @foreach($siswa->studentParents as $sp)
                            <div class="parent-row">
                                <div class="parent-row-body">
                                    <div class="parent-row-title">
                                        <i class="fas fa-user parent-row-icon"></i>
                                        {{ $sp->parent->name }}
                                    </div>
                                    <small class="parent-row-meta">
                                        {{ ucwords(str_replace('_', ' ', $sp->relationship)) }} •
                                        Username: {{ $sp->parent->username }} •
                                        @if($sp->is_primary) <span class="parent-row-tag--primary">Kontak Utama</span> @endif
                                        @if($sp->can_access_academic) <span class="parent-row-tag--access">Akses Akademik</span> @endif
                                    </small>
                                </div>
                                <input type="hidden" name="remove_parents[]" value="" id="remove_parent_{{ $sp->id }}">
                                <button type="button" class="btn btn-danger btn-sm" data-remove-parent data-parent-id="{{ $sp->id }}"
                                    title="Hapus Hubungan">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-parent-state">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p>Siswa ini belum memiliki akun wali siswa terdaftar.</p>
                        <small>Tambahkan wali siswa di bawah untuk memberikan akses ke sistem.</small>
                    </div>
                @endif
            </div>

            {{-- Add New Parent --}}
            <div class="form-group parent-form-group">
                <label class="form-label">Tambah Wali Siswa</label>
                <select name="add_parent_option" id="addParentOption" class="form-control">
                    <option value="">-- Pilih Aksi --</option>
                    <option value="existing">Hubungkan dengan Wali Siswa yang Sudah Ada</option>
                    <option value="new">Buat Akun Wali Siswa Baru</option>
                </select>
            </div>

            {{-- Add Existing Parent Form --}}
            <div id="addExistingParentForm" class="parent-form-block d-none">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-search muted-label-icon"></i>
                                Cari Wali Siswa
                            </label>
                            <input type="text" id="searchParent" class="form-control"
                                placeholder="Ketik nama atau username wali siswa...">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-filter muted-label-icon"></i>
                                Filter Status
                            </label>
                            <select id="filterParentStatus" class="form-control">
                                <option value="">Semua Wali Siswa</option>
                                <option value="available">Belum Terhubung (Baru)</option>
                                <option value="has_children">Sudah Punya Anak Terdaftar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group parent-form-block">
                    <label class="form-label">
                        Pilih Wali Siswa
                        <small class="text-muted parent-count-hint">
                            (<span id="parentCount">{{ $orangTuaList->count() }}</span> tersedia)
                        </small>
                    </label>
                    <div id="parentListContainer" class="parent-list-block">
                        @foreach($orangTuaList as $ortu)
                            @php
                                $hasChildren = $ortu->studentParents->count() > 0;
                                $isAlreadyLinked = $ortu->studentParents->where('siswa_id', $siswa->id)->count() > 0;
                            @endphp
                            <label class="parent-option" data-name="{{ strtolower($ortu->name) }}"
                                data-username="{{ strtolower($ortu->username) }}"
                                data-status="{{ $hasChildren ? 'has_children' : 'available' }}"
                                data-already-linked="{{ $isAlreadyLinked ? 'true' : 'false' }}">
                                <input type="radio" name="add_existing_parent_id" value="{{ $ortu->id }}"
                                    {{ $isAlreadyLinked ? 'disabled' : '' }}>
                                <div class="parent-option-body">
                                    <div class="parent-option-title">
                                        <i class="fas fa-user parent-option-icon"></i>
                                        {{ $ortu->name }}
                                        @if(!$hasChildren)
                                            <span class="new-badge">BARU</span>
                                        @endif
                                    </div>
                                    <small class="parent-option-meta">
                                        Username: {{ $ortu->username }}
                                        @if($hasChildren)
                                            • <strong>Anak:</strong>
                                            {{ $ortu->studentParents->pluck('siswa.nama_lengkap')->join(', ') }}
                                        @else
                                            • <em>Belum memiliki anak terdaftar</em>
                                        @endif
                                    </small>
                                </div>
                            </label>
                        @endforeach
                        <div id="noParentFound" class="empty-list-message d-none">
                            <i class="fas fa-search"></i>
                            <p>Tidak ada wali siswa yang ditemukan</p>
                            <small>Coba ubah kata kunci atau filter pencarian</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Hubungan</label>
                    <select name="add_existing_relationship" class="form-control" id="add_existing_relationship">
                        <option value="ayah_kandung">Ayah Kandung</option>
                        <option value="ibu_kandung">Ibu Kandung</option>
                        <option value="wali">Wali</option>
                        <option value="ayah_tiri">Ayah Tiri</option>
                        <option value="ibu_tiri">Ibu Tiri</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div id="addExistingRelationshipOtherField" class="relationship-extra d-none">
                    <div class="form-group">
                        <label class="form-label">Sebutkan Hubungan Keluarga Lainnya</label>
                        <input type="text" class="form-control" name="add_existing_relationship_lainnya"
                            placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                    </div>
                </div>
            </div>

            {{-- Add New Parent Form --}}
            <div id="addNewParentForm" class="parent-form-block d-none">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="add_new_parent_name" class="form-control">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="add_new_parent_username" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="add_new_parent_email" class="form-control"
                                placeholder="contoh@email.com">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <div class="password-wrap">
                                <input type="password" id="newParentPasswordField" name="add_new_parent_password"
                                    class="form-control password-input" placeholder="Minimal 8 karakter">
                                <button type="button" class="password-toggle" data-toggle-password data-field="newParentPasswordField" data-icon="toggleNewParentPasswordIcon">
                                    <i id="toggleNewParentPasswordIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">No. Telepon/WA</label>
                            <input type="text" name="add_new_parent_phone" class="form-control"
                                placeholder="Contoh: 08123456789">
                            <small class="text-muted">
                                Nomor telepon/WhatsApp untuk komunikasi dengan sekolah
                            </small>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Hubungan dengan Siswa</label>
                            <select name="add_new_relationship" class="form-control" id="add_new_relationship">
                                <option value="ayah_kandung">Ayah Kandung</option>
                                <option value="ibu_kandung">Ibu Kandung</option>
                                <option value="wali">Wali</option>
                                <option value="ayah_tiri">Ayah Tiri</option>
                                <option value="ibu_tiri">Ibu Tiri</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div id="addNewRelationshipOtherField" class="relationship-extra d-none">
                            <div class="form-group">
                                <label class="form-label">Sebutkan Hubungan Keluarga Lainnya</label>
                                <input type="text" class="form-control" name="add_new_relationship_lainnya"
                                    placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Confirmation Modal --}}
        <div id="confirmationModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="modal-close-btn" data-close-confirmation>&times;</button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus hubungan dengan wali siswa ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-close-confirmation>Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
                </div>
            </div>
        </div>

        {{-- Kelas Modal for Edit --}}
        <div class="modal fade" id="kelasModalEdit" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-school me-2"></i>Pilih Kelas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Filter Section --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label"><small>Filter Cabang</small></label>
                                <select id="filterCabangEdit" class="form-control form-control-sm">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangList as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><small>Filter Jenjang</small></label>
                                <select id="filterJenjangEdit" class="form-control form-control-sm">
                                    <option value="">Semua Jenjang</option>
                                    <option value="KB">KB</option>
                                    <option value="TKA">TKA</option>
                                    <option value="TKB">TKB</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><small>Cari Kelas</small></label>
                                <input type="text" id="searchKelasEdit" class="form-control form-control-sm" 
                                       placeholder="Ketik nama kelas...">
                            </div>
                        </div>

                        {{-- Kelas List --}}
                        <div class="kelas-list">
                            @foreach($kelasList as $kls)
                                <div class="kelas-item-edit d-flex align-items-center p-2" 
                                     data-cabang-id="{{ $kls->cabang_id }}" 
                                     data-jenjang="{{ $kls->jenjang }}"
                                     data-name="{{ $kls->nama_kelas }}">
                                    <input type="radio" class="kelas-checkbox-edit" name="kelas_selected_edit" 
                                           value="{{ $kls->id }}" 
                                           data-name="{{ $kls->nama_kelas }}"
                                           data-jenjang="{{ $kls->jenjang }}"
                                           id="kelas_edit_{{ $kls->id }}">
                                    <label class="ms-2 mb-0 flex-grow-1 kelas-item-label" for="kelas_edit_{{ $kls->id }}">
                                        <strong>{{ $kls->nama_kelas }}</strong>
                                        <small class="text-muted d-block">{{ $kls->jenjang }}</small>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" data-confirm-kelas-selection-edit>
                            <i class="fas fa-check me-1"></i>Konfirmasi
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hidden Fields for Other Data --}}


        {{-- Form Actions --}}
        <div class="form-actions">
            <a href="{{ url()->previous(route('admin.users.siswa')) }}" class="btn btn-secondary">
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
