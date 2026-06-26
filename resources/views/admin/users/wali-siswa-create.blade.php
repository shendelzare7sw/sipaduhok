@extends('layouts.sneat')

@section('title', 'Tambah Wali Siswa')
@section('page-title', 'Tambah Akun Wali Siswa Baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/users/wali-siswa-create.css'])
@endsection

@section('content')
    <div class="container-fluid">
{{-- Page Header --}}
        <div class="page-header">
            <a href="{{ route('admin.users.wali-siswa') }}" class="btn-back" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="page-header-title">
                <h2>Tambah Akun Wali Siswa Baru</h2>
                <p>Lengkapi form di bawah untuk membuat akun wali siswa baru</p>
            </div>
        </div>

        {{-- Alerts --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong><i class="fas fa-exclamation-circle"></i> Terjadi kesalahan:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        {{-- Info Box --}}
        <div class="info-box">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Panduan:</strong> Isi semua field yang bertanda <span class="required-mark">*</span> (wajib
                diisi). Anda dapat menghubungkan akun ini dengan siswa yang sudah ada (opsional).
            </div>
        </div>

        <form action="{{ route('admin.users.wali-siswa.store') }}" method="POST" id="orangTuaCreateForm"
            data-wali-siswa-create-form>
            @csrf

            {{-- Section 1: Informasi Akun --}}
            <div class="card">
                <h5 class="form-title">
                    <i class="fas fa-user-lock section-icon--account"></i>
                    1. Informasi Akun Login
                </h5>

                <div class="form-group">
                    <label for="username" class="form-label">
                        Username <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Masukkan username untuk login" required>
                    <small class="text-muted">Username digunakan untuk login ke sistem</small>
                    @error('username')
                        <div class="text-danger field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password <span class="required-mark">*</span>
                    </label>
                    <div class="password-field-wrap">
                        <input type="password" class="form-control password-input" id="passwordField" name="password"
                            placeholder="Masukkan password" required>
                        <button type="button" class="password-toggle-btn"
                            data-toggle-password data-field="passwordField" data-icon="togglePasswordIcon">
                            <i id="togglePasswordIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password <span class="required-mark">*</span>
                    </label>
                    <div class="password-field-wrap">
                        <input type="password" class="form-control password-input" id="passwordConfirmField" name="password_confirmation"
                            placeholder="Ketik ulang password" required>
                        <button type="button" class="password-toggle-btn"
                            data-toggle-password data-field="passwordConfirmField" data-icon="togglePasswordConfirmIcon">
                            <i id="togglePasswordConfirmIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Section 2: Biodata Wali Siswa --}}
            <div class="card">
                <h5 class="form-title">
                    <i class="fas fa-id-card section-icon--profile"></i>
                    2. Biodata Wali Siswa
                </h5>

                <div class="form-group">
                    <label for="name" class="form-label">
                        Nama Lengkap <span class="required-mark">*</span>
                    </label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap wali siswa" required>
                </div>

                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email
                            </label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                                placeholder="contoh@email.com">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                No. Telepon
                            </label>
                            <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}"
                                placeholder="08xxxxxxxxxx">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Pribadi <small class="text-muted">(Penting – untuk pemulihan akun)</small></label>
                    <input type="email" class="form-control" name="personal_email" value="{{ old('personal_email') }}"
                        placeholder="contoh: nama@gmail.com">
                    @error('personal_email')
                        <div class="text-danger field-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="address" class="form-label">
                        Alamat
                    </label>
                    <textarea class="form-control" id="address" name="address" rows="3"
                        placeholder="Masukkan alamat lengkap">{{ old('address') }}</textarea>
                </div>
            </div>

            {{-- Section 3: Hubungkan dengan Siswa (Optional) --}}
            <div class="card">
                <h5 class="form-title">
                    <i class="fas fa-link section-icon--link"></i>
                    3. Hubungkan dengan Siswa (Opsional)
                </h5>

                <div class="info-box info-box--warning">
                    <i class="fas fa-lightbulb"></i>
                    <div>
                        <strong>Info:</strong> Anda dapat menghubungkan akun wali siswa ini dengan siswa yang sudah
                        terdaftar. Jika tidak dihubungkan sekarang, Anda dapat menghubungkannya nanti melalui menu edit
                        siswa.
                    </div>
                </div>

                <div class="student-link-section">
                    {{-- Search Bar --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-search muted-label-icon"></i>
                            Cari Siswa
                        </label>
                        <input type="text" id="searchStudent" class="form-control"
                            placeholder="Ketik nama atau NISN siswa..." >
                    </div>

                    {{-- Filter Group --}}
                    <div class="filter-block">
                        <div class="filter-header">
                            <label class="form-label filter-title">
                                <i class="fas fa-filter muted-label-icon"></i>
                                Filter Siswa
                            </label>
                            <button type="button" class="btn-reset-filter" data-reset-student-filters>
                                <i class="fas fa-redo"></i>
                                Reset Filter
                            </button>
                        </div>
                        <div class="search-filter-group">
                            <div class="form-group">
                                <select id="filterJenjang" class="form-control" >
                                    <option value="">Semua Jenjang</option>
                                    @foreach($jenjangs as $jenjang)
                                        <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="filterCabang" class="form-control" >
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangList as $cabang)
                                        <option value="{{ $cabang->nama_cabang }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="filterKelas" class="form-control" >
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->nama_kelas }}">{{ $kelas->jenjang }} -
                                            {{ $kelas->nama_kelas }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="counter-badge">
                        <i class="fas fa-users"></i>
                        <span id="studentCount">{{ $siswaList->count() }}</span> siswa tersedia
                    </div>

                    <div class="student-list">
                        @forelse($siswaList as $siswa)
                            <label class="student-option"
                                data-name="{{ strtolower($siswa->user->name ?? $siswa->nama_lengkap) }}"
                                data-nisn="{{ strtolower($siswa->nisn) }}" data-jenjang="{{ $siswa->kelas->jenjang ?? '' }}"
                                data-cabang="{{ $siswa->cabang->nama_cabang ?? '' }}"
                                data-kelas="{{ $siswa->kelas->nama_kelas ?? '' }}">
                                <input type="checkbox" name="siswa_ids[]" value="{{ $siswa->id }}">
                                <div class="student-info">
                                    <div class="student-name">
                                        {{ $siswa->user->name ?? $siswa->nama_lengkap }}
                                        <span class="badge badge-jenjang">{{ $siswa->kelas->jenjang ?? '-' }}</span>
                                        <span class="badge badge-kelas">{{ $siswa->kelas->nama_kelas ?? '-' }}</span>
                                    </div>
                                    <div class="student-details">
                                        NISN: {{ $siswa->nisn }} | Cabang: {{ $siswa->cabang->nama_cabang ?? '-' }}
                                    </div>
                                </div>
                            </label>
                        @empty
                            <div class="empty-student-state">
                                <i class="fas fa-inbox"></i>
                                <p>Tidak ada siswa yang terdaftar</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="form-group link-relationship-group">
                        <label for="hubungan_keluarga" class="form-label">
                            Hubungan Keluarga
                        </label>
                        <select class="form-control" id="hubungan_keluarga" name="hubungan_keluarga"
                            >
                            <option value="">-- Pilih Hubungan (jika menghubungkan dengan siswa) --</option>
                            <option value="ayah_kandung">Ayah Kandung</option>
                            <option value="ibu_kandung">Ibu Kandung</option>
                            <option value="wali">Wali</option>
                            <option value="ayah_tiri">Ayah Tiri</option>
                            <option value="ibu_tiri">Ibu Tiri</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                        <small class="text-muted">Hubungan keluarga akan diterapkan untuk semua siswa yang dipilih</small>
                    </div>

                    <div id="otherRelationshipCreateField" class="relationship-extra d-none">
                        <div class="form-group">
                            <label for="hubungan_keluarga_lainnya" class="form-label">
                                Sebutkan Hubungan Keluarga Lainnya
                            </label>
                            <input type="text" class="form-control" id="hubungan_keluarga_lainnya"
                                name="hubungan_keluarga_lainnya" placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Actions --}}
            <div class="form-actions">
                <a href="{{ route('admin.users.wali-siswa') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Akun Wali Siswa
                </button>
            </div>
        </form>

        {{-- Custom Modal --}}
        <div id="relationshipModal" class="custom-modal-overlay">
            <div class="custom-modal">
                <div class="custom-modal-header">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h5>Siswa Belum Dipilih</h5>
                </div>
                <div class="custom-modal-body">
                    <p>Anda telah memilih <strong>Hubungan Keluarga</strong> tetapi belum memilih siswa.</p>
                    <p><strong>Hubungan keluarga harus dihubungkan dengan minimal 1 siswa.</strong></p>
                    <p>Silakan pilih salah satu:</p>
                    <ul>
                        <li><strong>Pilih Siswa Sekarang:</strong> Kembali untuk memilih siswa yang akan dihubungkan</li>
                        <li><strong>Batal Pilih Hubungan:</strong> Kosongkan pilihan hubungan keluarga jika tidak ingin
                            menghubungkan dengan siswa</li>
                    </ul>
                </div>
                <div class="custom-modal-footer">
                    <button type="button" class="modal-btn modal-btn-secondary" data-clear-relationship>
                        <i class="fas fa-times"></i>
                        Batal Pilih Hubungan
                    </button>
                    <button type="button" class="modal-btn modal-btn-primary" data-close-relationship-modal>
                        <i class="fas fa-user-check"></i>
                        Pilih Siswa Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/users/wali-siswa-form.js'])
@endsection
