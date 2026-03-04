@extends('layouts.sneat')

@section('title')
    Edit Orang Tua - {{ $orangTua->name ?? 'N/A' }}
@endsection

@section('page-title', 'Edit Data Orang Tua')

@section('page-subtitle')
    Perbarui data {{ $orangTua->name ?? 'N/A' }}
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

        .parent-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fffbeb;
            color: #92400e;
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

            .parent-badge {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    {{-- Header with Back Button --}}
    <div class="page-header">
        <a href="{{ route('admin.users.show-orang-tua', $orangTua->id) }}" class="btn-back" title="Kembali">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div class="page-header-title">
            <h2>Edit Data Orang Tua</h2>
            <p>Perbarui informasi akun orang tua</p>
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
            <strong>Catatan:</strong> Gunakan form ini untuk mengubah data akun orang tua. Data siswa yang terhubung dengan
            orang tua ini tidak akan terpengaruh.
        </div>
    </div>

    <form action="{{ route('admin.users.update-orang-tua', $orangTua->id) }}" method="POST">
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
                        <label class="form-label">Nama Lengkap <span style="color: #ef4444;">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $orangTua->name) }}"
                            placeholder="Masukkan nama lengkap" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label class="form-label">Username <span style="color: #ef4444;">*</span></label>
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
                    <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Password <small class="text-muted">(Kosongkan jika tidak ingin
                        diubah)</small></label>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password baru (opsional)">
            </div>

            <div class="form-group">
                <label class="form-label">Status Akun <span style="color: #ef4444;">*</span></label>
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
                    <i class="fas fa-users" style="color: #10b981;"></i>
                    Data Anak Terdaftar & Hubungan Keluarga
                </h5>
                <p style="color: #64748b; font-size: 13px; margin-bottom: 16px;">
                    <i class="fas fa-info-circle"></i>
                    Anda dapat mengubah hubungan keluarga untuk setiap siswa di bawah ini.
                </p>

                <div style="display: flex; flex-direction: column; gap: 12px;">
                    @foreach($orangTua->studentParents as $sp)
                        <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px;">
                            <div style="margin-bottom: 10px;">
                                <div style="font-weight: 600; color: #111827; margin-bottom: 4px;">
                                    <i class="fas fa-user-graduate" style="color: #3b82f6;"></i>
                                    {{ $sp->siswa->nama_lengkap }}
                                </div>
                                <small style="color: #64748b;">
                                    NIS: {{ $sp->siswa->nis }} • NISN: {{ $sp->siswa->nisn }} •
                                    Kelas: {{ $sp->siswa->kelas->nama_kelas ?? '-' }}
                                </small>
                            </div>

                            <div class="row" style="align-items: end;">
                                <div class="col">
                                    <div class="form-group" style="margin-bottom: 0;">
                                        <label class="form-label">Hubungan Keluarga</label>
                                        <select name="relationships[{{ $sp->id }}]" class="form-control"
                                            id="hubungan_keluarga_{{ $sp->id }}"
                                            onchange="toggleOtherRelationshipEdit({{ $sp->id }})">
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
                                        style="display: {{ $sp->relationship == 'lainnya' || (!in_array($sp->relationship, ['ayah_kandung', 'ibu_kandung', 'wali', 'ayah_tiri', 'ibu_tiri'])) ? 'block' : 'none' }}; margin-top: 12px;">
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <label class="form-label">Sebutkan Hubungan Keluarga Lainnya</label>
                                            <input type="text" class="form-control" name="relationships_lainnya[{{ $sp->id }}]"
                                                placeholder="Contoh: Kakek, Nenek, Paman, Bibi, dll"
                                                value="{{ (!in_array($sp->relationship, ['ayah_kandung', 'ibu_kandung', 'wali', 'ayah_tiri', 'ibu_tiri'])) ? $sp->relationship : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col" style="max-width: 200px;">
                                    <div style="display: flex; gap: 6px; font-size: 11px; flex-wrap: wrap;">
                                        @if($sp->is_primary)
                                            <span
                                                style="background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 6px; font-weight: 600;">
                                                <i class="fas fa-star" style="font-size: 9px;"></i> Kontak Utama
                                            </span>
                                        @endif
                                        @if($sp->can_access_academic)
                                            <span
                                                style="background: #e0f2fe; color: #0369a1; padding: 4px 10px; border-radius: 6px; font-weight: 600;">
                                                <i class="fas fa-check-circle" style="font-size: 9px;"></i> Akses Akademik
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
            <a href="{{ route('admin.users.show-orang-tua', $orangTua->id) }}" class="btn btn-secondary">
                <i class="fas fa-times"></i>
                Batal
            </a>
            <button type="submit" class="btn btn-warning">
                <i class="fas fa-save"></i>
                Update Data
            </button>
        </div>
    </form>

    <script>
        function toggleOtherRelationshipEdit(studentParentId) {
            const selectValue = document.getElementById('hubungan_keluarga_' + studentParentId).value;
            const otherField = document.getElementById('otherRelationshipEditField_' + studentParentId);

            if (selectValue === 'lainnya') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
            }
        }
    </script>
@endsection