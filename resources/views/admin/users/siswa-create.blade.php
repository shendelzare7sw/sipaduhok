@extends('layouts.sneat')

@section('title', 'Tambah Siswa')
@section('page-title', 'Tambah Siswa Baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        /* Card Styles */
        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
            display: flex;
            align-items: center;
            gap: 8px;
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

        select.form-control {
            cursor: pointer;
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

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-primary:active {
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

        /* Info Box */
        .info-box {
            background: #f0f9ff;
            border: 1px solid #bae6fd;
            border-radius: 8px;
            padding: 12px 16px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #0c4a6e;
            display: flex;
            align-items: start;
            gap: 10px;
        }

        .info-box i {
            color: #0284c7;
            font-size: 16px;
            margin-top: 2px;
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

            .form-actions .btn {
                width: 100%;
            }

            .page-header {
                flex-wrap: wrap;
            }

            .page-header-title h2 {
                font-size: 20px;
            }
        }
    </style>

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
            <ul style="margin: 8px 0 0 0; padding-left: 20px;">
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
            <strong>Panduan:</strong> Isi semua field yang bertanda <span style="color: #ef4444;">*</span> (wajib diisi).
            Data siswa yang sudah tersimpan dapat diubah kapan saja melalui menu edit.
        </div>
    </div>

    <form action="{{ route('admin.users.store-siswa') }}" method="POST">
        @csrf

        {{-- Section 1: Account Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-lock" style="color: #3b82f6;"></i>
                1. Informasi Akun
            </h5>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror" value="{{ old('username') }}"
                            placeholder="Masukkan username" required>
                        @error('username')
                            <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Password <span style="color: #ef4444;">*</span></label>
                        <div style="position: relative;">
                            <input type="password" id="passwordField" name="password" class="form-control" required
                                style="padding-right: 45px;" placeholder="Minimal 8 karakter">
                            <button type="button" onclick="togglePassword('passwordField', 'togglePasswordIcon')"
                                style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 5px;">
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
                @error('personal_email') <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div> @enderror
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NISN <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn') }}"
                            placeholder="Nomor Induk Siswa Nasional" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NIS <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis') }}"
                            placeholder="Nomor Induk Siswa" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 2: Academic Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-school" style="color: #8b5cf6;"></i>
                2. Informasi Akademik
            </h5>

            <div class="row" style="flex-direction: column; gap: 16px;">
                <div>
                    <div class="form-group">
                        <label class="form-label">Kelas <span style="color: #ef4444;">*</span></label>
                        
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
                        <div class="kelas-display" onclick="openKelasModal()"
                             style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 8px; background: white; min-height: 50px; transition: all 0.2s;">
                            <div id="selectedKelasText" class="text-muted" style="font-style: italic;">
                                <i class="fas fa-school me-2"></i> Klik untuk memilih kelas...
                            </div>
                            <div id="selectedKelasChips" class="d-flex flex-wrap gap-2 mt-1" style="display: none !important;">
                            </div>
                        </div>
                        @error('kelas_id')
                            <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Masuk <span style="color: #ef4444;">*</span></label>
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk') }}" required>
            </div>
        </div>

        {{-- Section 3: Student Data --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-id-card" style="color: #10b981;"></i>
                3. Biodata Siswa
            </h5>

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}"
                    placeholder="Nama lengkap siswa" required>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                        <div style="display: flex; gap: 20px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="jenis_kelamin" value="L" {{ old('jenis_kelamin') == 'L' ? 'checked' : '' }} required style="cursor: pointer;">
                                <span style="font-weight: 500; color: #475569;">Laki-laki</span>
                            </label>
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" name="jenis_kelamin" value="P" {{ old('jenis_kelamin') == 'P' ? 'checked' : '' }} required style="cursor: pointer;">
                                <span style="font-weight: 500; color: #475569;">Perempuan</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir') }}"
                            placeholder="Kota tempat lahir" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}"
                            required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap <span style="color: #ef4444;">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap tempat tinggal"
                    required>{{ old('alamat') }}</textarea>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Agama <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="agama" class="form-control" value="{{ old('agama') }}"
                            placeholder="Contoh: Kristen, Islam, dll" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 4: Parent Biodata --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-users" style="color: #10b981;"></i>
                4. Data Orang Tua / Wali (Biodata)
            </h5>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
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
                <i class="fas fa-user-friends" style="color: #f59e0b;"></i>
                5. Akun Orang Tua (Login Sistem)
            </h5>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
                <i class="fas fa-info-circle"></i>
                Pilih orang tua yang sudah ada atau buat akun baru untuk memberikan akses login ke sistem.
            </p>

            <div class="form-group">
                <label class="form-label">Opsi Akun Orang Tua</label>
                <select name="parent_option" id="parentOption" class="form-control" onchange="toggleParentForm()">
                    <option value="">-- Pilih Opsi --</option>
                    <option value="existing">Pilih Orang Tua yang Sudah Ada</option>
                    <option value="new">Buat Akun Orang Tua Baru</option>
                    <option value="none">Tidak Perlu Akun (Bisa Ditambahkan Nanti)</option>
                </select>
            </div>

            {{-- Existing Parent Selection --}}
            <div id="existingParentForm" style="display: none;">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-search" style="color: #9ca3af; margin-right: 6px;"></i>
                                Cari Orang Tua
                            </label>
                            <input type="text" id="searchParent" class="form-control"
                                placeholder="Ketik nama atau username orang tua...">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-filter" style="color: #9ca3af; margin-right: 6px;"></i>
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

                <div class="form-group" style="margin-top: 12px;">
                    <label class="form-label">
                        Pilih Orang Tua <span style="color: #ef4444;">*</span>
                        <small class="text-muted" style="font-weight: normal; margin-left: 8px;">
                            (<span id="parentCount">{{ $orangTuaList->count() }}</span> tersedia)
                        </small>
                    </label>
                    <div id="parentListContainer"
                        style="max-height: 300px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px;">
                        @foreach($orangTuaList as $ortu)
                            @php
                                $hasChildren = $ortu->studentParents->count() > 0;
                            @endphp
                            <label class="parent-option" data-name="{{ strtolower($ortu->name) }}"
                                data-username="{{ strtolower($ortu->username) }}"
                                data-status="{{ $hasChildren ? 'has_children' : 'available' }}"
                                style="display: flex; align-items: center; padding: 12px; margin-bottom: 8px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="parent_id" value="{{ $ortu->id }}" style="margin-right: 12px;">
                                <div style="flex: 1;">
                                    <div
                                        style="font-weight: 600; color: #111827; display: flex; align-items: center; gap: 8px;">
                                        <i class="fas fa-user" style="color: #f59e0b;"></i>
                                        {{ $ortu->name }}
                                        @if(!$hasChildren)
                                            <span
                                                style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">BARU</span>
                                        @endif
                                    </div>
                                    <small style="color: #64748b;">
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
                        <div id="noParentFound" style="display: none; text-align: center; padding: 20px; color: #64748b;">
                            <i class="fas fa-search" style="font-size: 24px; margin-bottom: 8px;"></i>
                            <p style="margin: 0; font-weight: 600;">Tidak ada orang tua yang ditemukan</p>
                            <small>Coba ubah kata kunci atau filter pencarian</small>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Hubungan <span style="color: #ef4444;">*</span></label>
                    <select name="existing_relationship" class="form-control" id="existing_relationship"
                        onchange="toggleExistingRelationship()">
                        <option value="ayah_kandung">Ayah Kandung</option>
                        <option value="ibu_kandung">Ibu Kandung</option>
                        <option value="wali">Wali</option>
                        <option value="ayah_tiri">Ayah Tiri</option>
                        <option value="ibu_tiri">Ibu Tiri</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div id="existingRelationshipOtherField" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Sebutkan Hubungan Keluarga Lainnya <span
                                style="color: #ef4444;">*</span></label>
                        <input type="text" class="form-control" name="existing_relationship_lainnya"
                            placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                    </div>
                </div>
            </div>

            {{-- New Parent Form --}}
            <div id="newParentForm" style="display: none;">
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap Orang Tua <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="parent_name" class="form-control" value="{{ old('parent_name') }}"
                                placeholder="Nama lengkap orang tua">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Username Orang Tua <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="parent_username" class="form-control"
                                value="{{ old('parent_username') }}" placeholder="Username untuk login">
                            <small class="text-muted">Untuk login ke sistem</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Email Orang Tua <span style="color: #ef4444;">*</span></label>
                            <input type="email" name="parent_email" class="form-control" value="{{ old('parent_email') }}"
                                placeholder="contoh@email.com">
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">Password <span style="color: #ef4444;">*</span></label>
                            <div style="position: relative;">
                                <input type="password" id="parentPasswordField" name="parent_password" class="form-control"
                                    style="padding-right: 45px;" placeholder="Minimal 8 karakter">
                                <button type="button"
                                    onclick="togglePassword('parentPasswordField', 'toggleParentPasswordIcon')"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 5px;">
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
                            <label class="form-label">Hubungan dengan Siswa <span style="color: #ef4444;">*</span></label>
                            <select name="new_relationship" class="form-control" id="new_relationship"
                                onchange="toggleNewRelationship()">
                                <option value="ayah_kandung">Ayah Kandung</option>
                                <option value="ibu_kandung">Ibu Kandung</option>
                                <option value="wali">Wali</option>
                                <option value="ayah_tiri">Ayah Tiri</option>
                                <option value="ibu_tiri">Ibu Tiri</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div id="newRelationshipOtherField" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Sebutkan Hubungan Keluarga Lainnya <span
                                        style="color: #ef4444;">*</span></label>
                                <input type="text" class="form-control" name="new_relationship_lainnya"
                                    placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                            </div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label class="form-label">No. Telepon/WA <span style="color: #ef4444;">*</span></label>
                            <input type="text" name="parent_phone" class="form-control" value="{{ old('parent_phone') }}"
                                placeholder="Contoh: 08123456789">
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="is_primary" value="1" checked>
                        <span style="font-weight: 500; font-size: 14px;">Jadikan sebagai kontak utama</span>
                    </label>
                </div>
                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 8px;">
                        <input type="checkbox" name="can_access_academic" value="1" checked>
                        <span style="font-weight: 500; font-size: 14px;">Dapat mengakses data akademik siswa</span>
                    </label>
                </div>
            </div>
        </div>

        <script>
            // Kelas data for modal
            const allKelasData = @json($kelasList);
            const cabangList = @json($cabangList);
            const oldKelasId = @json(old('kelas_id'));
            const oldCabangId = @json(old('cabang_id'));

            document.addEventListener('DOMContentLoaded', function() {
                // Initialize dan update UI jika ada old values
                if (oldKelasId) {
                    const selectedOption = document.querySelector(`option[value="${oldKelasId}"]`);
                    if (selectedOption) {
                        updateSelectedKelasUI([{
                            id: oldKelasId,
                            name: selectedOption.text.trim(),
                            jenjang: selectedOption.getAttribute('data-jenjang')
                        }]);
                    }
                }

                // Cabang select untuk filter
                document.getElementById('cabangSelect').addEventListener('change', function() {
                    // Clear kelas select jika cabang berubah
                    document.getElementById('kelasSelect').value = '';
                    updateSelectedKelasUI([]);
                });
            });

            // Modal Picker Functions
            function openKelasModal() {
                const kelasSelect = document.getElementById('kelasSelect');
                const selectedValue = kelasSelect.value;
                
                // Sync checkbox dengan current select value
                document.querySelectorAll('.kelas-checkbox').forEach(cb => {
                    cb.checked = (cb.value === selectedValue) && selectedValue !== '';
                });
                
                updateTempSelection();
                
                const modal = new bootstrap.Modal(document.getElementById('kelasModal'));
                modal.show();
            }

            function filterKelasList() {
                const cabangFilter = document.getElementById('filterCabang').value || document.getElementById('cabangSelect').value;
                const jenjangFilter = document.getElementById('filterJenjang').value;
                const searchText = document.getElementById('searchKelas').value.toLowerCase();
                
                document.querySelectorAll('.kelas-item').forEach(item => {
                    const itemCabang = item.getAttribute('data-cabang-id');
                    const itemJenjang = item.getAttribute('data-jenjang');
                    const itemName = item.getAttribute('data-name');
                    
                    let visible = true;
                    
                    if (cabangFilter && itemCabang !== cabangFilter) visible = false;
                    if (jenjangFilter && itemJenjang !== jenjangFilter) visible = false;
                    if (searchText && !itemName.toLowerCase().includes(searchText)) visible = false;
                    
                    item.style.display = visible ? 'block' : 'none';
                });
            }

            function updateTempSelection() {
                const count = document.querySelectorAll('.kelas-checkbox:checked').length;
                document.getElementById('selectedCount').textContent = count;
            }

            function confirmKelasSelection() {
                const checkbox = document.querySelector('.kelas-checkbox:checked');
                const select = document.getElementById('kelasSelect');
                
                if (checkbox) {
                    // Set select value
                    select.value = checkbox.value;
                    
                    // Update UI
                    updateSelectedKelasUI([{
                        id: checkbox.value,
                        name: checkbox.getAttribute('data-name'),
                        jenjang: checkbox.getAttribute('data-jenjang')
                    }]);
                } else {
                    select.value = '';
                    updateSelectedKelasUI([]);
                }
                
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('kelasModal')).hide();
            }

            function updateSelectedKelasUI(data) {
                const textPlaceholder = document.getElementById('selectedKelasText');
                const chipsContainer = document.getElementById('selectedKelasChips');

                if (data.length === 0) {
                    textPlaceholder.style.display = 'block';
                    chipsContainer.innerHTML = '';
                    chipsContainer.style.display = 'none';
                } else {
                    textPlaceholder.style.display = 'none';
                    chipsContainer.innerHTML = '';

                    data.forEach(item => {
                        const chip = document.createElement('div');
                        chip.className = 'badge bg-primary d-flex align-items-center p-2';
                        chip.style.cssText = 'font-size: 12px; max-width: 100%;';
                        chip.innerHTML = `
                            <i class="fas fa-school me-2"></i>
                            <span style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.name}</span>
                            <span class="ms-2 badge bg-white text-primary" style="font-size: 10px; white-space: nowrap;">${item.jenjang}</span>
                        `;
                        chipsContainer.appendChild(chip);
                    });
                    chipsContainer.style.display = 'flex';
                }
            }

            function togglePassword(fieldId, iconId) {
                const field = document.getElementById(fieldId);
                const icon = document.getElementById(iconId);

                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            }

            function toggleParentForm() {
                const option = document.getElementById('parentOption').value;
                const existingForm = document.getElementById('existingParentForm');
                const newForm = document.getElementById('newParentForm');

                existingForm.style.display = 'none';
                newForm.style.display = 'none';

                if (option === 'existing') {
                    existingForm.style.display = 'block';
                    document.getElementById('searchParent').value = '';
                    filterParentList();
                } else if (option === 'new') {
                    newForm.style.display = 'block';
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                const searchInput = document.getElementById('searchParent');
                const filterStatus = document.getElementById('filterParentStatus');

                if (searchInput) {
                    searchInput.addEventListener('input', filterParentList);
                }
                if (filterStatus) {
                    filterStatus.addEventListener('change', filterParentList);
                }
            });

            function filterParentList() {
                const searchTerm = document.getElementById('searchParent').value.toLowerCase();
                const statusFilter = document.getElementById('filterParentStatus').value;
                const parentOptions = document.querySelectorAll('.parent-option');
                const noParentFound = document.getElementById('noParentFound');
                const parentCount = document.getElementById('parentCount');
                let visibleCount = 0;

                parentOptions.forEach(option => {
                    const name = option.getAttribute('data-name');
                    const username = option.getAttribute('data-username');
                    const status = option.getAttribute('data-status');

                    const matchSearch = searchTerm === '' || name.includes(searchTerm) || username.includes(searchTerm);
                    const matchStatus = statusFilter === '' || status === statusFilter;

                    if (matchSearch && matchStatus) {
                        option.style.display = 'flex';
                        visibleCount++;
                    } else {
                        option.style.display = 'none';
                    }
                });

                if (parentCount) {
                    parentCount.textContent = visibleCount;
                }

                if (visibleCount === 0) {
                    noParentFound.style.display = 'block';
                } else {
                    noParentFound.style.display = 'none';
                }
            }

            function toggleExistingRelationship() {
                const selectValue = document.getElementById('existing_relationship').value;
                const otherField = document.getElementById('existingRelationshipOtherField');

                if (selectValue === 'lainnya') {
                    otherField.style.display = 'block';
                } else {
                    otherField.style.display = 'none';
                }
            }

            function toggleNewRelationship() {
                const selectValue = document.getElementById('new_relationship').value;
                const otherField = document.getElementById('newRelationshipOtherField');

                if (selectValue === 'lainnya') {
                    otherField.style.display = 'block';
                } else {
                    otherField.style.display = 'none';
                }
            }
        </script>

        {{-- Modal Pilih Kelas --}}
        <div class="modal fade" id="kelasModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content" style="border-radius: 16px; border: none;">
                    <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px;">
                        <h5 class="modal-title" style="font-weight: 600; color: #111827;">
                            <i class="fas fa-school" style="color: #8b5cf6; margin-right: 10px;"></i>
                            Pilih Kelas
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" style="padding: 24px;">
                        {{-- Filter Section --}}
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label" style="font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                    <i class="fas fa-building me-1"></i>Cabang
                                </label>
                                <select id="filterCabang" class="form-select" onchange="filterKelasList()">
                                    <option value="">-- Semua Cabang --</option>
                                    @foreach($cabangList as $cabang)
                                        <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" style="font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                    <i class="fas fa-layer-group me-1"></i>Jenjang
                                </label>
                                <select id="filterJenjang" class="form-select" onchange="filterKelasList()">
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
                                <label class="form-label" style="font-weight: 600; color: #374151; font-size: 12px; text-transform: uppercase;">
                                    <i class="fas fa-search me-1"></i>Cari
                                </label>
                                <input type="text" id="searchKelas" class="form-control" placeholder="Nama kelas..." onkeyup="filterKelasList()">
                            </div>
                        </div>

                        {{-- Kelas List --}}
                        <div style="max-height: 400px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px;">
                            @foreach($kelasList as $kls)
                                <label class="kelas-item" data-name="{{ strtolower($kls->nama_kelas) }}" data-jenjang="{{ $kls->jenjang }}" data-cabang-id="{{ $kls->cabang_id }}"
                                    style="display: flex; align-items: center; padding: 12px; margin-bottom: 8px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                    <input type="radio" class="kelas-checkbox" name="kelas_radio" value="{{ $kls->id }}" data-name="{{ $kls->nama_kelas }}" data-jenjang="{{ $kls->jenjang }}" style="margin-right: 12px;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: #111827;">{{ $kls->nama_kelas }}</div>
                                        <small style="color: #64748b;">
                                            <i class="fas fa-layer-group me-1"></i>{{ $kls->jenjang }} 
                                            • 
                                            <i class="fas fa-building me-1"></i>{{ $kls->cabang->nama_cabang ?? '-' }}
                                        </small>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 16px 24px;">
                        <span class="text-muted" style="font-size: 12px;">Dipilih: <strong><span id="selectedCount">0</span></strong></span>
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary btn-sm" onclick="confirmKelasSelection()">Pilih Kelas</button>
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