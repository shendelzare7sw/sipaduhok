@extends('layouts.sneat')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/siswa-create.css'])
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/siswa-create.js'])
@endsection

@section('content')
{{-- Header with Back Button --}}
    <div class="page-header">
        <a href="{{ route('admin.users.siswa') }}" class="btn-back" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="page-header-title">
            <h2>Tambah Siswa Baru</h2>
            <p>Lengkapi form di bawah untuk mendaftarkan siswa baru</p>
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
            <strong>Panduan:</strong> Isi semua field yang bertanda <span class="required-mark">*</span> (wajib diisi).
            Data siswa yang sudah tersimpan dapat diubah kapan saja melalui menu edit.
        </div>
    </div>

    <form action="{{ route('admin.users.store-siswa') }}" method="POST">
        @csrf

        {{-- Section 1: Account Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-lock section-icon--account"></i>
                1. Informasi Akun
            </h5>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required-mark">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                            placeholder="Masukkan username" required>
                        @error('username')
                            <div class="text-danger field-error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Password <span class="required-mark">*</span></label>
                        <div class="password-wrap">
                            <input type="password" id="passwordField" name="password" class="form-control password-input" required
                                placeholder="Minimal 8 karakter">
                            <button type="button" class="password-toggle" data-toggle-password data-field="passwordField" data-icon="togglePasswordIcon">
                                <i id="togglePasswordIcon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email <small class="text-muted">(Opsional)</small></label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                    placeholder="contoh@email.com">
            </div>
            <div class="form-group">
                <label class="form-label">Email Pribadi <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
                <input type="email" name="personal_email" class="form-control" value="{{ old('personal_email') }}"
                    placeholder="contoh: nama@gmail.com">
                @error('personal_email') <div class="text-danger field-error">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NISN <span class="required-mark">*</span></label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}"
                            placeholder="Nomor Induk Siswa Nasional" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NIS <span class="required-mark">*</span></label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis') }}"
                            placeholder="Nomor Induk Siswa" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Academic Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-school section-icon--academic"></i>
                2. Informasi Akademik
            </h5>

            <div class="row stack-row">
                <div>
                    <div class="form-group">
                        <label class="form-label">Kelas <span class="required-mark">*</span></label>
                        
                        {{-- Hidden Select for Form Submission --}}
                        <select name="kelas_id" id="kelasSelect" class="d-none" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" 
                                    data-jenjang="{{ $kls->jenjang }}" 
                                    data-cabang-id="{{ $kls->cabang_id }}"
                                    {{ old('kelas_id') == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Trigger Box --}}
                        <div class="kelas-display" data-open-kelas-modal>
                            <div id="selectedKelasText" class="text-muted selected-kelas-placeholder">
                                <i class="fas fa-school me-2"></i> Klik untuk memilih kelas...
                            </div>
                            <div id="selectedKelasChips" class="d-flex flex-wrap gap-2 mt-1 d-none">
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
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
            </div>
        </div>

        {{-- Section 3: Student Data --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-id-card section-icon--student"></i>
                3. Biodata Siswa
            </h5>

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span class="required-mark">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                    placeholder="Nama lengkap siswa" required>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span class="required-mark">*</span></label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required>
                                <span class="radio-label-text">Laki-laki</span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required>
                                <span class="radio-label-text">Perempuan</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span class="required-mark">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}"
                            placeholder="Kota tempat lahir" required>
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
                <label class="form-label">Alamat Lengkap <span class="required-mark">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap tempat tinggal"
                    required>{{ old('alamat') }}</textarea>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Agama <span class="required-mark">*</span></label>
                        <input type="text" name="agama" class="form-control" value="{{ old('agama') }}"
                            placeholder="Contoh: Kristen, Islam, dll" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Parent Biodata --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-users section-icon--guardian"></i>
                4. Data Orang Tua / Wali (Biodata)
            </h5>
            <p class="section-note">
                <i class="fas fa-info-circle"></i>
                Informasi dasar orang tua/wali siswa untuk keperluan administrasi sekolah.
            </p>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Ayah</label>
                        <input type="text" name="nama_ayah" class="form-control" value="{{ old('nama_ayah') }}"
                            placeholder="Masukkan nama ayah kandung">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Nama Ibu</label>
                        <input type="text" name="nama_ibu" class="form-control" value="{{ old('nama_ibu') }}"
                            placeholder="Masukkan nama ibu kandung">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">No. Telepon Orang Tua (WA Aktif)</label>
                <input type="text" name="telepon_orangtua" class="form-control" value="{{ old('telepon_orangtua') }}"
                    placeholder="Contoh: 08123456789">
                <small class="text-muted">
                    Nomor telepon/WhatsApp yang dapat dihubungi untuk komunikasi sekolah
                </small>
            </div>
        </div>

        {{-- Section 5: Parent Account --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-friends section-icon--parent"></i>
                5. Akun Orang Tua (Login Sistem)
            </h5>
            <p class="section-note">
                <i class="fas fa-info-circle"></i>
                Pilih orang tua yang sudah ada atau buat akun baru untuk memberikan akses login ke sistem.
            </p>

            <div class="form-group">
                <label class="form-label">Opsi Akun Orang Tua</label>
                <select name="parent_option" id="parentOption" class="form-control">
                    <option value="">-- Pilih Opsi --</option>
                    <option value="existing">Pilih Orang Tua yang Sudah Ada</option>
                    <option value="new">Buat Akun Orang Tua Baru</option>
                    <option value="none">Tidak Perlu Akun (Bisa Ditambahkan Nanti)</option>
                </select>
            </div>

            {{-- Existing Parent Selection --}}
            <div id="existingParentForm" class="d-none">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-search muted-label-icon"></i>
                                Cari Orang Tua
                            </label>
                            <input type="text" id="searchParent" class="form-control"
                                placeholder="Ketik nama atau username orang tua...">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-filter muted-label-icon"></i>
                                Filter Status
                            </label>
                            <select id="filterParentStatus" class="form-control">
                                <option value="">Semua Orang Tua</option>
                                <option value="available">Belum Terhubung (Baru)</option>
                                <option value="has_children">Sudah Punya Anak Terdaftar</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="form-group parent-form-block">
                    <label class="form-label">
                        Pilih Orang Tua <span class="required-mark">*</span>
                        <small class="text-muted parent-count-hint">
                            (<span id="parentCount">{{ $orangTuaList->count() }}</span> tersedia)
                        </small>
                    </label>
                    <div id="parentListContainer" class="parent-list-block">
                        @foreach($orangTuaList as $ortu)
                            @php
                                $hasChildren = $ortu->studentParents->count() > 0;
                            @endphp
                            <label class="parent-option" data-name="{{ strtolower($ortu->name) }}"
                                data-username="{{ strtolower($ortu->username) }}"
                                data-status="{{ $hasChildren ? 'has_children' : 'available' }}">
                                <input type="radio" name="parent_id" value="{{ $ortu->id }}">
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
                            <p>Tidak ada orang tua yang ditemukan</p>
                            <small>Coba ubah kata kunci atau filter pencarian</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Hubungan <span class="required-mark">*</span></label>
                    <select name="existing_relationship" class="form-control" id="existing_relationship">
                        <option value="ayah_kandung">Ayah Kandung</option>
                        <option value="ibu_kandung">Ibu Kandung</option>
                        <option value="wali">Wali</option>
                        <option value="ayah_tiri">Ayah Tiri</option>
                        <option value="ibu_tiri">Ibu Tiri</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div id="existingRelationshipOtherField" class="relationship-extra d-none">
                    <div class="form-group">
                        <label class="form-label">Sebutkan Hubungan Keluarga Lainnya <span class="required-mark">*</span></label>
                        <input type="text" class="form-control" name="existing_relationship_lainnya"
                            placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                    </div>
                </div>
            </div>

            {{-- New Parent Form --}}
            <div id="newParentForm" class="d-none">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap Orang Tua <span class="required-mark">*</span></label>
                            <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}"
                                placeholder="Nama lengkap orang tua">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Username Orang Tua <span class="required-mark">*</span></label>
                            <input type="text" name="parent_username" class="form-control"
                                value="{{ old('parent_username') }}" placeholder="Username untuk login">
                            <small class="text-muted">Untuk login ke sistem</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Email Orang Tua <span class="required-mark">*</span></label>
                            <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email') }}"
                                placeholder="contoh@email.com">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Password <span class="required-mark">*</span></label>
                            <div class="password-wrap">
                                <input type="password" id="parentPasswordField" name="parent_password" class="form-control"
                                    class="password-input" placeholder="Minimal 8 karakter">
                                <button type="button" class="password-toggle" data-toggle-password data-field="parentPasswordField" data-icon="toggleParentPasswordIcon">
                                    <i id="toggleParentPasswordIcon" class="fas fa-eye"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Hubungan dengan Siswa <span class="required-mark">*</span></label>
                            <select name="new_relationship" class="form-control" id="new_relationship">
                                <option value="ayah_kandung">Ayah Kandung</option>
                                <option value="ibu_kandung">Ibu Kandung</option>
                                <option value="wali">Wali</option>
                                <option value="ayah_tiri">Ayah Tiri</option>
                                <option value="ibu_tiri">Ibu Tiri</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div id="newRelationshipOtherField" class="relationship-extra d-none">
                            <div class="form-group">
                                <label class="form-label">Sebutkan Hubungan Keluarga Lainnya <span class="required-mark">*</span></label>
                                <input type="text" class="form-control" name="new_relationship_lainnya"
                                    placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">No. Telepon/WA <span class="required-mark">*</span></label>
                            <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}"
                                placeholder="Contoh: 08123456789">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_primary" value="1" checked>
                        <span class="checkbox-label-text">Jadikan sebagai kontak utama</span>
                    </label>
                </div>
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="can_access_academic" value="1" checked>
                        <span class="checkbox-label-text">Dapat mengakses data akademik siswa</span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Modal Pilih Kelas --}}
        <div class="modal fade" id="kelasModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content class-modal-content">
                    <div class="modal-header class-modal-header">
                        <h5 class="modal-title class-modal-title">
                            <i class="fas fa-school class-modal-icon"></i>
                            Pilih Kelas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body class-modal-body">
                        {{-- Filter Section --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label modal-filter-label">
                                    <i class="fas fa-building me-1"></i>Cabang
                                </label>
                                <select id="filterCabang" class="form-select">
                                    <option value="">-- Semua Cabang --</option>
                                    @foreach($cabangList as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label modal-filter-label">
                                    <i class="fas fa-layer-group me-1"></i>Jenjang
                                </label>
                                <select id="filterJenjang" class="form-select">
                                    <option value="">-- Semua Jenjang --</option>
                                    <option value="KB">KB</option>
                                    <option value="TKA">TKA</option>
                                    <option value="TKB">TKB</option>
                                    <option value="SD">SD</option>
                                    <option value="SMP">SMP</option>
                                    <option value="SMA">SMA</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label modal-filter-label">
                                    <i class="fas fa-search me-1"></i>Cari
                                </label>
                                <input type="text" id="searchKelas" class="form-control" placeholder="Nama kelas...">
                            </div>
                        </div>

                        {{-- Kelas List --}}
                        <div class="kelas-list">
                            @foreach($kelasList as $kls)
                                <label class="kelas-item" data-name="{{ strtolower($kls->nama_kelas) }}" data-jenjang="{{ $kls->jenjang }}" data-cabang-id="{{ $kls->cabang_id }}"
                                    >
                                    <input type="radio" class="kelas-checkbox" name="kelas_radio" value="{{ $kls->id }}" data-name="{{ $kls->nama_kelas }}" data-jenjang="{{ $kls->jenjang }}">
                                    <div class="kelas-item-body">
                                        <div class="kelas-item-title">{{ $kls->nama_kelas }}</div>
                                        <small class="kelas-item-meta">
                                            <i class="fas fa-layer-group me-1"></i>{{ $kls->jenjang }} 
                                            • 
                                            <i class="fas fa-building me-1"></i>{{ $kls->cabang->nama_cabang ?? '-' }}
                                        </small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer class-modal-footer">
                        <span class="text-muted selected-count-label">Dipilih: <strong><span id="selectedCount">0</span></strong></span>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary btn-sm" data-confirm-kelas-selection>Pilih Kelas</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div class="form-actions">
            <a href="{{ route('admin.users.siswa') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Batal
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                Simpan Data Siswa
            </button>
        </div>
    </form>
@endsection
