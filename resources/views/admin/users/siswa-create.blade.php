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

            .btn {
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
                        <input type="text" name="username" class="form-control" value="{{ old('username') }}"
                            placeholder="Masukkan username" required>
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

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Kelas <span style="color: #ef4444;">*</span></label>
                        <select name="kelas_id" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} ({{ $kelas->jenjang }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Cabang <span style="color: #ef4444;">*</span></label>
                        <select name="cabang_id" class="form-control" required>
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangList as $cabang)
                                <option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>
                            @endforeach
                        </select>
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
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin') == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin') == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
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
            // Toggle password visibility
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
                    // Reset search when opening
                    document.getElementById('searchParent').value = '';
                    filterParentList();
                } else if (option === 'new') {
                    newForm.style.display = 'block';
                }
            }

            // Search functionality for parent list
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

                    // Search match
                    const matchSearch = searchTerm === '' || name.includes(searchTerm) || username.includes(searchTerm);

                    // Status filter match
                    const matchStatus = statusFilter === '' || status === statusFilter;

                    if (matchSearch && matchStatus) {
                        option.style.display = 'flex';
                        visibleCount++;
                    } else {
                        option.style.display = 'none';
                    }
                });

                // Update count
                if (parentCount) {
                    parentCount.textContent = visibleCount;
                }

                // Show/hide no results message
                if (visibleCount === 0) {
                    noParentFound.style.display = 'block';
                } else {
                    noParentFound.style.display = 'none';
                }
            }

            // Toggle for existing parent relationship
            function toggleExistingRelationship() {
                const selectValue = document.getElementById('existing_relationship').value;
                const otherField = document.getElementById('existingRelationshipOtherField');

                if (selectValue === 'lainnya') {
                    otherField.style.display = 'block';
                } else {
                    otherField.style.display = 'none';
                }
            }

            // Toggle for new parent relationship
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