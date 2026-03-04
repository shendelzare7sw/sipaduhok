@extends('layouts.sneat')

@section('title', 'Tambah Orang Tua')
@section('page-title', 'Tambah Akun Orang Tua Baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <div class="container-fluid">
        <style>
            /* Card Styles */
            .card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                margin-bottom: 24px;
                padding: 24px;
                border: 1px solid #e2e8f0;
            }

            /* Page Header */
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
            }

            .btn-back:hover {
                background: #e2e8f0;
                color: #1e293b;
                text-decoration: none;
            }

            .page-header-title h2 {
                margin: 0;
                font-size: 24px;
                font-weight: 700;
                color: #111827;
            }

            .page-header-title p {
                margin: 4px 0 0 0;
                font-size: 14px;
                color: #64748b;
            }

            /* Form Styles */
            .form-title {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 16px;
                font-weight: 600;
                color: #1e293b;
                margin-bottom: 20px;
                padding-bottom: 10px;
                border-bottom: 2px solid #f1f5f9;
            }

            .form-title i {
                font-size: 18px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-label {
                display: block;
                font-weight: 600;
                color: #374151;
                margin-bottom: 8px;
                font-size: 14px;
            }

            .text-muted {
                color: #64748b;
                font-size: 13px;
                margin-top: 4px;
                display: block;
            }

            .form-control,
            .form-select {
                width: 100%;
                padding: 10px 14px;
                font-size: 14px;
                line-height: 1.5;
                color: #1e293b;
                background-color: #fff;
                border: 1px solid #cbd5e1;
                border-radius: 8px;
                transition: all 0.2s ease;
            }

            .form-control:focus,
            .form-select:focus {
                outline: none;
                border-color: #3b82f6;
                box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            }

            .form-control:hover,
            .form-select:hover {
                border-color: #94a3b8;
            }

            /* Password Toggle Button */
            .password-toggle-btn {
                position: absolute;
                right: 12px;
                top: 50%;
                transform: translateY(-50%);
                background: none;
                border: none;
                color: #64748b;
                cursor: pointer;
                padding: 4px 8px;
                transition: color 0.2s ease;
            }

            .password-toggle-btn:hover {
                color: #3b82f6;
            }

            /* Row and Column */
            .row {
                display: flex;
                gap: 16px;
                margin-bottom: 0;
            }

            .col {
                flex: 1;
                min-width: 0;
            }

            /* Buttons */
            .btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                font-size: 14px;
                font-weight: 600;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                text-decoration: none;
            }

            .btn-primary {
                background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
                color: white;
            }

            .btn-primary:hover {
                background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(37, 99, 235, 0.4);
                color: white;
                text-decoration: none;
            }

            .btn-secondary {
                background: #f1f5f9;
                color: #475569;
                border: 1px solid #e2e8f0;
            }

            .btn-secondary:hover {
                background: #e2e8f0;
                color: #1e293b;
                text-decoration: none;
            }

            /* Form Actions */
            .form-actions {
                display: flex;
                gap: 12px;
                justify-content: flex-end;
                margin-top: 32px;
                padding-top: 24px;
                border-top: 2px solid #f1f5f9;
            }

            /* Alert Styles */
            .alert {
                padding: 16px;
                border-radius: 8px;
                margin-bottom: 20px;
                border: 1px solid;
            }

            .alert-danger {
                background-color: #fef2f2;
                border-color: #fecaca;
                color: #991b1b;
            }

            .alert-danger strong {
                color: #7f1d1d;
            }

            .alert-danger ul {
                margin: 8px 0 0 20px;
                padding: 0;
            }

            .alert-danger li {
                margin: 4px 0;
            }

            .alert-success {
                background-color: #f0fdf4;
                border-color: #bbf7d0;
                color: #166534;
            }

            /* Info Box */
            .info-box {
                display: flex;
                gap: 12px;
                padding: 14px 16px;
                background: #eff6ff;
                border: 1px solid #bfdbfe;
                border-radius: 8px;
                color: #1e40af;
                font-size: 14px;
                margin-bottom: 24px;
            }

            .info-box i {
                font-size: 18px;
                flex-shrink: 0;
                margin-top: 2px;
            }

            /* Student Link Section */
            .student-link-section {
                background: #f9fafb;
                padding: 20px;
                border-radius: 8px;
                border: 1px dashed #d1d5db;
            }

            .student-option {
                display: flex;
                align-items: center;
                padding: 12px;
                border: 1px solid #e5e7eb;
                border-radius: 8px;
                margin-bottom: 8px;
                cursor: pointer;
                transition: all 0.2s ease;
                background: white;
            }

            .student-option:hover {
                border-color: #3b82f6;
                background: #f0f9ff;
            }

            .student-option input[type="checkbox"] {
                width: 18px;
                height: 18px;
                margin-right: 12px;
                cursor: pointer;
            }

            .student-info {
                flex: 1;
            }

            .student-name {
                font-weight: 600;
                color: #111827;
                font-size: 14px;
            }

            .student-details {
                font-size: 13px;
                color: #6b7280;
                margin-top: 2px;
            }

            .badge {
                display: inline-block;
                padding: 2px 8px;
                border-radius: 4px;
                font-size: 11px;
                font-weight: 600;
                margin-left: 8px;
            }

            .badge-jenjang {
                background: #dbeafe;
                color: #1e40af;
            }

            .badge-kelas {
                background: #e0e7ff;
                color: #4338ca;
            }

            /* Search and Filter */
            .search-filter-group {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 12px;
                margin-bottom: 16px;
            }

            .search-filter-group .form-group {
                margin-bottom: 0;
            }

            @media (max-width: 992px) {
                .search-filter-group {
                    grid-template-columns: 1fr;
                }
            }

            /* Counter */
            .counter-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 12px;
                background: #f0f9ff;
                border: 1px solid #bfdbfe;
                border-radius: 6px;
                color: #1e40af;
                font-size: 13px;
                font-weight: 600;
                margin-bottom: 12px;
            }

            /* Modal Styles */
            .custom-modal-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                animation: fadeIn 0.2s ease;
            }

            .custom-modal-overlay.show {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .custom-modal {
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                max-width: 480px;
                width: 90%;
                animation: slideIn 0.3s ease;
            }

            .custom-modal-header {
                padding: 20px 24px;
                border-bottom: 1px solid #e5e7eb;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .custom-modal-header i {
                font-size: 24px;
                color: #f59e0b;
            }

            .custom-modal-header h5 {
                margin: 0;
                font-size: 18px;
                font-weight: 600;
                color: #111827;
            }

            .custom-modal-body {
                padding: 24px;
            }

            .custom-modal-body p {
                margin: 0 0 16px 0;
                color: #374151;
                line-height: 1.6;
            }

            .custom-modal-body ul {
                margin: 0;
                padding-left: 20px;
                color: #374151;
            }

            .custom-modal-body ul li {
                margin-bottom: 8px;
            }

            .custom-modal-footer {
                padding: 16px 24px;
                border-top: 1px solid #e5e7eb;
                display: flex;
                gap: 12px;
                justify-content: flex-end;
            }

            .modal-btn {
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 14px;
                border: none;
                cursor: pointer;
                transition: all 0.2s ease;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

            .modal-btn-primary {
                background: #3b82f6;
                color: white;
            }

            .modal-btn-primary:hover {
                background: #2563eb;
            }

            .modal-btn-secondary {
                background: #6b7280;
                color: white;
            }

            .modal-btn-secondary:hover {
                background: #4b5563;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                }

                to {
                    opacity: 1;
                }
            }

            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: translateY(-20px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            /* Responsive Design */
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
                    justify-content: center;
                }

                .page-header {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .search-filter-group {
                    flex-direction: column;
                }
            }
        </style>

        {{-- Page Header --}}
        <div class="page-header">
            <a href="{{ route('admin.users.orang-tua') }}" class="btn-back" title="Kembali">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div class="page-header-title">
                <h2>Tambah Akun Orang Tua Baru</h2>
                <p>Lengkapi form di bawah untuk membuat akun orang tua baru</p>
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
                <strong>Panduan:</strong> Isi semua field yang bertanda <span style="color: #ef4444;">*</span> (wajib
                diisi). Anda dapat menghubungkan akun ini dengan siswa yang sudah ada (opsional).
            </div>
        </div>

        <form action="{{ route('admin.users.orang-tua.store') }}" method="POST" id="orangTuaCreateForm"
            onsubmit="return validateForm()">
            @csrf

            {{-- Section 1: Informasi Akun --}}
            <div class="card">
                <h5 class="form-title">
                    <i class="fas fa-user-lock" style="color: #3b82f6;"></i>
                    1. Informasi Akun Login
                </h5>

                <div class="form-group">
                    <label for="username" class="form-label">
                        Username <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username') }}"
                        placeholder="Masukkan username untuk login" required>
                    <small class="text-muted">Username digunakan untuk login ke sistem</small>
                    @error('username')
                        <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">
                        Password <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" class="form-control" id="passwordField" name="password"
                            placeholder="Masukkan password" style="padding-right: 45px;" required>
                        <button type="button" class="password-toggle-btn"
                            onclick="togglePassword('passwordField', 'togglePasswordIcon')">
                            <i id="togglePasswordIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                    <small class="text-muted">Minimal 6 karakter</small>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        Konfirmasi Password <span style="color: #ef4444;">*</span>
                    </label>
                    <div style="position: relative;">
                        <input type="password" class="form-control" id="passwordConfirmField" name="password_confirmation"
                            placeholder="Ketik ulang password" style="padding-right: 45px;" required>
                        <button type="button" class="password-toggle-btn"
                            onclick="togglePassword('passwordConfirmField', 'togglePasswordConfirmIcon')">
                            <i id="togglePasswordConfirmIcon" class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Section 2: Biodata Orang Tua --}}
            <div class="card">
                <h5 class="form-title">
                    <i class="fas fa-id-card" style="color: #10b981;"></i>
                    2. Biodata Orang Tua
                </h5>

                <div class="form-group">
                    <label for="name" class="form-label">
                        Nama Lengkap <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                        placeholder="Masukkan nama lengkap orang tua" required>
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
                        <div class="text-danger" style="font-size: 13px; margin-top: 4px;">{{ $message }}</div>
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
                    <i class="fas fa-link" style="color: #f59e0b;"></i>
                    3. Hubungkan dengan Siswa (Opsional)
                </h5>

                <div class="info-box" style="background: #fef3c7; border-color: #fde68a; color: #92400e;">
                    <i class="fas fa-lightbulb"></i>
                    <div>
                        <strong>Info:</strong> Anda dapat menghubungkan akun orang tua ini dengan siswa yang sudah
                        terdaftar. Jika tidak dihubungkan sekarang, Anda dapat menghubungkannya nanti melalui menu edit
                        siswa.
                    </div>
                </div>

                <div class="student-link-section">
                    {{-- Search Bar --}}
                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-search" style="color: #9ca3af; margin-right: 6px;"></i>
                            Cari Siswa
                        </label>
                        <input type="text" id="searchStudent" class="form-control"
                            placeholder="Ketik nama atau NISN siswa..." onkeyup="filterStudentList()">
                    </div>

                    {{-- Filter Group --}}
                    <div style="margin-bottom: 12px;">
                        <div
                            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <label class="form-label" style="margin: 0;">
                                <i class="fas fa-filter" style="color: #9ca3af; margin-right: 6px;"></i>
                                Filter Siswa
                            </label>
                            <button type="button" onclick="resetFilters()"
                                style="background: none; border: none; color: #3b82f6; font-size: 13px; cursor: pointer; padding: 4px 8px; display: inline-flex; align-items: center; gap: 4px;">
                                <i class="fas fa-redo"></i>
                                Reset Filter
                            </button>
                        </div>
                        <div class="search-filter-group">
                            <div class="form-group">
                                <select id="filterJenjang" class="form-control" onchange="filterStudentList()">
                                    <option value="">Semua Jenjang</option>
                                    @foreach($jenjangs as $jenjang)
                                        <option value="{{ $jenjang }}">{{ $jenjang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="filterCabang" class="form-control" onchange="filterStudentList()">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangList as $cabang)
                                        <option value="{{ $cabang->nama_cabang }}">{{ $cabang->nama_cabang }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <select id="filterKelas" class="form-control" onchange="filterStudentList()">
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

                    <div
                        style="max-height: 300px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; background: white;">
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
                            <div style="text-align: center; padding: 20px; color: #9ca3af;">
                                <i class="fas fa-inbox" style="font-size: 32px; margin-bottom: 8px;"></i>
                                <p>Tidak ada siswa yang terdaftar</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="form-group" style="margin-top: 16px;">
                        <label for="hubungan_keluarga" class="form-label">
                            Hubungan Keluarga
                        </label>
                        <select class="form-control" id="hubungan_keluarga" name="hubungan_keluarga"
                            onchange="toggleOtherRelationshipCreate()">
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

                    <div id="otherRelationshipCreateField" style="display: none; margin-top: 12px;">
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
                <a href="{{ route('admin.users.orang-tua') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Simpan Akun Orang Tua
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
                    <button type="button" class="modal-btn modal-btn-secondary" onclick="clearRelationshipOnly()">
                        <i class="fas fa-times"></i>
                        Batal Pilih Hubungan
                    </button>
                    <button type="button" class="modal-btn modal-btn-primary" onclick="closeModalAndSelectStudent()">
                        <i class="fas fa-user-check"></i>
                        Pilih Siswa Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle Password Visibility
        function togglePassword(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const toggleIcon = document.getElementById(iconId);

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Filter Student List
        function filterStudentList() {
            const searchTerm = document.getElementById('searchStudent').value.toLowerCase();
            const jenjangFilter = document.getElementById('filterJenjang').value;
            const cabangFilter = document.getElementById('filterCabang').value;
            const kelasFilter = document.getElementById('filterKelas').value;
            const studentOptions = document.querySelectorAll('.student-option');
            let visibleCount = 0;

            studentOptions.forEach(option => {
                const name = option.getAttribute('data-name');
                const nisn = option.getAttribute('data-nisn');
                const jenjang = option.getAttribute('data-jenjang');
                const cabang = option.getAttribute('data-cabang');
                const kelas = option.getAttribute('data-kelas');

                const matchSearch = searchTerm === '' || name.includes(searchTerm) || nisn.includes(searchTerm);
                const matchJenjang = jenjangFilter === '' || jenjang === jenjangFilter;
                const matchCabang = cabangFilter === '' || cabang === cabangFilter;
                const matchKelas = kelasFilter === '' || kelas === kelasFilter;

                if (matchSearch && matchJenjang && matchCabang && matchKelas) {
                    option.style.display = 'flex';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });

            document.getElementById('studentCount').textContent = visibleCount;
        }

        // Reset All Filters
        function resetFilters() {
            document.getElementById('searchStudent').value = '';
            document.getElementById('filterJenjang').value = '';
            document.getElementById('filterCabang').value = '';
            document.getElementById('filterKelas').value = '';
            filterStudentList();
        }

        // Toggle Other Relationship Field
        function toggleOtherRelationshipCreate() {
            const selectValue = document.getElementById('hubungan_keluarga').value;
            const otherField = document.getElementById('otherRelationshipCreateField');

            if (selectValue === 'lainnya') {
                otherField.style.display = 'block';
            } else {
                otherField.style.display = 'none';
            }
        }

        // Form Validation
        function validateForm() {
            // Get all checked siswa checkboxes
            const checkedSiswa = document.querySelectorAll('input[name="siswa_ids[]"]:checked');
            const hubunganKeluarga = document.getElementById('hubungan_keluarga').value;
            const hubunganKeluargaLainnya = document.getElementById('hubungan_keluarga_lainnya').value;

            // NEW: Check if relationship is selected but NO siswa
            if (hubunganKeluarga && checkedSiswa.length === 0) {
                // Show custom modal
                document.getElementById('relationshipModal').classList.add('show');
                return false; // Prevent form submission
            }

            // If siswa is selected, relationship must be filled
            if (checkedSiswa.length > 0) {
                if (!hubunganKeluarga) {
                    alert('Anda telah memilih siswa, harap pilih Hubungan Keluarga!');
                    document.getElementById('hubungan_keluarga').focus();
                    return false;
                }

                // If "lainnya" is selected, custom text must be filled
                if (hubunganKeluarga === 'lainnya' && !hubunganKeluargaLainnya.trim()) {
                    alert('Harap sebutkan hubungan keluarga lainnya!');
                    document.getElementById('hubungan_keluarga_lainnya').focus();
                    return false;
                }
            }

            return true;
        }

        // Clear relationship selection only (don't submit)
        function clearRelationshipOnly() {
            // Clear relationship selection
            document.getElementById('hubungan_keluarga').value = '';
            document.getElementById('hubungan_keluarga_lainnya').value = '';
            document.getElementById('otherRelationshipCreateField').style.display = 'none';

            // Close modal
            document.getElementById('relationshipModal').classList.remove('show');

            // Don't submit - user can continue filling the form
        }

        // Close modal and keep relationship (user will select student)
        function closeModalAndSelectStudent() {
            // Just close the modal
            document.getElementById('relationshipModal').classList.remove('show');

            // Scroll to student selection area
            const studentSection = document.querySelector('.card:nth-child(4)'); // Section 3
            if (studentSection) {
                studentSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Close modal when clicking outside
        document.getElementById('relationshipModal').addEventListener('click', function (e) {
            if (e.target === this) {
                this.classList.remove('show');
            }
        });
    </script>
@endsection