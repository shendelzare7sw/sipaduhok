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

        .student-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #eff6ff;
            color: #1e40af;
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
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

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        }

        .btn-warning:active {
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

            .student-badge {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    {{-- Header with Back Button --}}
    <div class="page-header">
        <a href="{{ route('admin.users.siswa') }}" class="btn-back" title="Kembali">
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
        <div>
            <strong>Catatan:</strong> Form ini untuk mengubah seluruh data siswa, termasuk biodata lengkap dan data akun.
        </div>
        </div>
    </div>

    <form action="{{ route('admin.users.update-siswa', $siswa->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Section 1: Account Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-user-lock" style="color: #3b82f6;"></i>
                Informasi Akun
            </h5>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
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
                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin
                        diubah)</small></label>
                <div style="position: relative;">
                    <input type="password" id="passwordField" name="password" class="form-control"
                        placeholder="Masukkan password baru (opsional)" style="padding-right: 45px;">
                    <button type="button" onclick="togglePassword('passwordField', 'togglePasswordIcon')"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 5px;">
                        <i id="togglePasswordIcon" class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Status Akun <span style="color: #ef4444;">*</span></label>
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
                <i class="fas fa-id-card" style="color: #10b981;"></i>
                Data Siswa
            </h5>

            <div class="form-group">
                <label class="form-label">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control"
                    value="{{ old('nama_lengkap', $siswa->nama_lengkap) }}" placeholder="Nama lengkap siswa" required>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NISN <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nisn" class="form-control" value="{{ old('nisn', $siswa->nisn) }}"
                            placeholder="Nomor Induk Siswa Nasional" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">NIS <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="nis" class="form-control" value="{{ old('nis', $siswa->nis) }}"
                            placeholder="Nomor Induk Siswa" required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin <span style="color: #ef4444;">*</span></label>
                        <select name="jenis_kelamin" class="form-control" required>
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="L" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="P" {{ old('jenis_kelamin', $siswa->jenis_kelamin) == 'P' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tempat Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="tempat_lahir" class="form-control" value="{{ old('tempat_lahir', $siswa->tempat_lahir) }}"
                            placeholder="Kota tempat lahir" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Tanggal Lahir <span style="color: #ef4444;">*</span></label>
                        <input type="date" name="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir', \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('Y-m-d')) }}" required>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Alamat Lengkap <span style="color: #ef4444;">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap tempat tinggal"
                    required>{{ old('alamat', $siswa->alamat) }}</textarea>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Agama <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="agama" class="form-control" value="{{ old('agama', $siswa->agama) }}"
                            placeholder="Contoh: Kristen, Islam, dll" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section 3: Academic Information --}}
        <div class="card">
            <h5 class="form-title">
                <i class="fas fa-school" style="color: #8b5cf6;"></i>
                Informasi Akademik
            </h5>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Cabang <span style="color: #ef4444;">*</span></label>
                        <select name="cabang_id" id="cabangSelectEdit" class="form-control" required onchange="loadKelasOptionsEdit()">
                            <option value="">-- Pilih Cabang --</option>
                            @foreach($cabangList as $cabang)
                                <option value="{{ $cabang->id }}" {{ old('cabang_id', $siswa->cabang_id) == $cabang->id ? 'selected' : '' }}>
                                    {{ $cabang->nama_cabang }}
                                </option>
                            @endforeach
                        </select>
                        @error('cabang_id')
                            <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col" id="kelasWrapperEdit">
                    <div class="form-group">
                        <label class="form-label">Kelas <span style="color: #ef4444;">*</span></label>
                        <select name="kelas_id" id="kelasSelectEdit" class="form-control" required>
                            <option value="">-- Pilih Kelas --</option>
                        </select>
                        @error('kelas_id')
                            <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Tanggal Masuk <span style="color: #ef4444;">*</span></label>
                <input type="date" name="tanggal_masuk" class="form-control" value="{{ old('tanggal_masuk', $siswa->tanggal_masuk ? $siswa->tanggal_masuk->format('Y-m-d') : '') }}" required>
            </div>

            <div class="form-group">
                <label class="form-label">Status Siswa <span style="color: #ef4444;">*</span></label>
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
                <i class="fas fa-users" style="color: #10b981;"></i>
                Data Orang Tua / Wali (Biodata)
            </h5>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
                <i class="fas fa-info-circle"></i>
                Informasi dasar orang tua/wali siswa untuk keperluan administrasi sekolah.
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
                <label class="form-label">No. Telepon Orang Tua (WA Aktif)</label>
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
                <i class="fas fa-user-friends" style="color: #f59e0b;"></i>
                Manajemen Akun Orang Tua (Login Sistem)
            </h5>
            <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
                <i class="fas fa-info-circle"></i>
                Kelola akun orang tua yang terhubung dengan siswa ini untuk akses ke sistem. Satu siswa bisa memiliki
                beberapa akun orang tua/wali.
            </p>

            {{-- Current Parents List --}}
            <div class="form-group">
                <label class="form-label">Orang Tua Terdaftar</label>
                @if($siswa->studentParents && $siswa->studentParents->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($siswa->studentParents as $sp)
                            <div class="parent-row"
                                style="display: flex; align-items: center; justify-content: space-between; padding: 12px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px;">
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #111827;">
                                        <i class="fas fa-user" style="color: #f59e0b;"></i>
                                        {{ $sp->parent->name }}
                                    </div>
                                    <small style="color: #64748b;">
                                        {{ ucwords(str_replace('_', ' ', $sp->relationship)) }} •
                                        Username: {{ $sp->parent->username }} •
                                        @if($sp->is_primary) <span style="color: #10b981;">Kontak Utama</span> @endif
                                        @if($sp->can_access_academic) <span style="color: #3b82f6;">Akses Akademik</span> @endif
                                    </small>
                                </div>
                                <input type="hidden" name="remove_parents[]" value="" id="remove_parent_{{ $sp->id }}">
                                <button type="button" class="btn btn-danger btn-sm" onclick="removeParent({{ $sp->id }}, this)"
                                    title="Hapus Hubungan">
                                    <i class="fas fa-unlink"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div
                        style="text-align: center; padding: 20px; background: #fef3c7; border: 1px dashed #f59e0b; border-radius: 8px;">
                        <i class="fas fa-exclamation-triangle" style="color: #f59e0b; font-size: 24px; margin-bottom: 8px;"></i>
                        <p style="margin: 0; color: #92400e;">Siswa ini belum memiliki akun orang tua terdaftar.</p>
                        <small style="color: #92400e;">Tambahkan orang tua di bawah untuk memberikan akses ke sistem.</small>
                    </div>
                @endif
            </div>

            {{-- Add New Parent --}}
            <div class="form-group" style="margin-top: 20px;">
                <label class="form-label">Tambah Orang Tua</label>
                <select name="add_parent_option" id="addParentOption" class="form-control" onchange="toggleAddParentForm()">
                    <option value="">-- Pilih Aksi --</option>
                    <option value="existing">Hubungkan dengan Orang Tua yang Sudah Ada</option>
                    <option value="new">Buat Akun Orang Tua Baru</option>
                </select>
            </div>

            {{-- Add Existing Parent Form --}}
            <div id="addExistingParentForm" style="display: none; margin-top: 12px;">
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
                        Pilih Orang Tua
                        <small class="text-muted" style="font-weight: normal; margin-left: 8px;">
                            (<span id="parentCount">{{ $orangTuaList->count() }}</span> tersedia)
                        </small>
                    </label>
                    <div id="parentListContainer"
                        style="max-height: 300px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px;">
                        @foreach($orangTuaList as $ortu)
                            @php
                                $hasChildren = $ortu->studentParents->count() > 0;
                                $isAlreadyLinked = $ortu->studentParents->where('siswa_id', $siswa->id)->count() > 0;
                            @endphp
                            <label class="parent-option" data-name="{{ strtolower($ortu->name) }}"
                                data-username="{{ strtolower($ortu->username) }}"
                                data-status="{{ $hasChildren ? 'has_children' : 'available' }}"
                                data-already-linked="{{ $isAlreadyLinked ? 'true' : 'false' }}"
                                style="display: {{ $isAlreadyLinked ? 'none' : 'flex' }}; align-items: center; padding: 12px; margin-bottom: 8px; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                <input type="radio" name="add_existing_parent_id" value="{{ $ortu->id }}"
                                    style="margin-right: 12px;" {{ $isAlreadyLinked ? 'disabled' : '' }}>
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
                    <label class="form-label">Hubungan</label>
                    <select name="add_existing_relationship" class="form-control" id="add_existing_relationship"
                        onchange="toggleAddExistingRelationship()">
                        <option value="ayah_kandung">Ayah Kandung</option>
                        <option value="ibu_kandung">Ibu Kandung</option>
                        <option value="wali">Wali</option>
                        <option value="ayah_tiri">Ayah Tiri</option>
                        <option value="ibu_tiri">Ibu Tiri</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div id="addExistingRelationshipOtherField" style="display: none;">
                    <div class="form-group">
                        <label class="form-label">Sebutkan Hubungan Keluarga Lainnya</label>
                        <input type="text" class="form-control" name="add_existing_relationship_lainnya"
                            placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll">
                    </div>
                </div>
            </div>

            {{-- Add New Parent Form --}}
            <div id="addNewParentForm" style="display: none; margin-top: 12px;">
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
                            <div style="position: relative;">
                                <input type="password" id="newParentPasswordField" name="add_new_parent_password"
                                    class="form-control" placeholder="Minimal 8 karakter" style="padding-right: 45px;">
                                <button type="button"
                                    onclick="togglePassword('newParentPasswordField', 'toggleNewParentPasswordIcon')"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b; padding: 5px;">
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
                            <select name="add_new_relationship" class="form-control" id="add_new_relationship"
                                onchange="toggleAddNewRelationship()">
                                <option value="ayah_kandung">Ayah Kandung</option>
                                <option value="ibu_kandung">Ibu Kandung</option>
                                <option value="wali">Wali</option>
                                <option value="ayah_tiri">Ayah Tiri</option>
                                <option value="ibu_tiri">Ibu Tiri</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div id="addNewRelationshipOtherField" style="display: none;">
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

        </div>

        {{-- Confirmation Modal --}}
        <style>
            /* Modal Styles */
            .modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 1050;
                display: none;
                align-items: center;
                justify-content: center;
                animation: fadeIn 0.2s ease-out;
            }

            .modal-container {
                background: white;
                padding: 0;
                border-radius: 12px;
                width: 90%;
                max-width: 400px;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
                transform: scale(0.95);
                animation: scaleIn 0.2s ease-out forwards;
                overflow: hidden;
            }

            .modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #e2e8f0;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .modal-title {
                font-size: 18px;
                font-weight: 700;
                color: #1e293b;
                margin: 0;
            }

            .modal-body {
                padding: 24px;
                color: #475569;
                font-size: 15px;
                line-height: 1.5;
            }

            .modal-footer {
                padding: 16px 24px;
                background: #f8fafc;
                border-top: 1px solid #e2e8f0;
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes scaleIn {
                from {
                    transform: scale(0.95);
                    opacity: 0;
                }

                to {
                    transform: scale(1);
                    opacity: 1;
                }
            }
        </style>
        <div id="confirmationModal" class="modal-overlay">
            <div class="modal-container">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button"
                        style="background: none; border: none; font-size: 24px; cursor: pointer; color: #94a3b8;"
                        onclick="closeModal()">&times;</button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus hubungan dengan orang tua ini?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Ya, Hapus</button>
                </div>
            </div>
        </div>

        <script>
            // Kelas data grouped by cabang_id (for dynamic filtering)
            const allKelasDataEdit = @json($kelasList->groupBy('cabang_id'));
            const currentKelasId = @json(old('kelas_id', $siswa->kelas_id));
            const currentCabangId = @json(old('cabang_id', $siswa->cabang_id));

            function loadKelasOptionsEdit() {
                const cabangId = document.getElementById('cabangSelectEdit').value;
                const kelasWrapper = document.getElementById('kelasWrapperEdit');
                const kelasSelect = document.getElementById('kelasSelectEdit');

                // Clear existing options
                kelasSelect.innerHTML = '<option value="">-- Pilih Kelas --</option>';

                if (!cabangId) {
                    kelasWrapper.style.display = 'none';
                    return;
                }

                const kelasList = allKelasDataEdit[cabangId] || [];

                if (kelasList.length === 0) {
                    kelasSelect.innerHTML = '<option value="">-- Tidak ada kelas tersedia --</option>';
                } else {
                    kelasList.forEach(function(kelas) {
                        const option = document.createElement('option');
                        option.value = kelas.id;
                        option.textContent = kelas.nama_kelas + ' (' + kelas.jenjang + ')';
                        // Select current kelas only if it belongs to the selected cabang
                        if (currentKelasId && kelas.id == currentKelasId && kelas.cabang_id == cabangId) {
                            option.selected = true;
                        }
                        kelasSelect.appendChild(option);
                    });
                }

                kelasWrapper.style.display = 'block';
            }

            // On page load: populate kelas based on current cabang
            document.addEventListener('DOMContentLoaded', function() {
                if (currentCabangId) {
                    loadKelasOptionsEdit();
                }
            });

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

            function toggleAddParentForm() {
                const option = document.getElementById('addParentOption').value;
                const existingForm = document.getElementById('addExistingParentForm');
                const newForm = document.getElementById('addNewParentForm');

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

            // Modal Logic
            let parentIdToDelete = null;
            let elementToDelete = null;

            function removeParent(parentId, btnElement) {
                parentIdToDelete = parentId;
                // Find the parent container
                elementToDelete = btnElement.closest('.parent-row');

                const modal = document.getElementById('confirmationModal');
                modal.style.display = 'flex';
            }

            function closeModal() {
                const modal = document.getElementById('confirmationModal');
                modal.style.display = 'none';
                parentIdToDelete = null;
                elementToDelete = null;
            }

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (parentIdToDelete && elementToDelete) {
                    // Mark for deletion
                    document.getElementById('remove_parent_' + parentIdToDelete).value = parentIdToDelete;

                    // Visually hide
                    elementToDelete.style.opacity = '0.5';
                    elementToDelete.style.pointerEvents = 'none';
                    elementToDelete.style.background = '#fee2e2';

                    closeModal();
                }
            });

            // Close modal when clicking outside
            document.getElementById('confirmationModal').addEventListener('click', function (e) {
                if (e.target === this) {
                    closeModal();
                }
            });

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
                    const alreadyLinked = option.getAttribute('data-already-linked');

                    // Skip already linked parents
                    if (alreadyLinked === 'true') {
                        option.style.display = 'none';
                        return;
                    }

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

            // Toggle for adding existing parent relationship
            function toggleAddExistingRelationship() {
                const selectValue = document.getElementById('add_existing_relationship').value;
                const otherField = document.getElementById('addExistingRelationshipOtherField');

                if (selectValue === 'lainnya') {
                    otherField.style.display = 'block';
                } else {
                    otherField.style.display = 'none';
                }
            }

            // Toggle for adding new parent relationship
            function toggleAddNewRelationship() {
                const selectValue = document.getElementById('add_new_relationship').value;
                const otherField = document.getElementById('addNewRelationshipOtherField');

                if (selectValue === 'lainnya') {
                    otherField.style.display = 'block';
                } else {
                    otherField.style.display = 'none';
                }
            }
        </script>

        {{-- Hidden Fields for Other Data --}}


        {{-- Form Actions --}}
        <div class="form-actions">
            <a href="{{ route('admin.users.siswa') }}" class="btn btn-secondary">
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