@extends('layouts.sneat')

@section('title', 'Detail Siswa - ' . $siswa->nama_lengkap)

@section('page-title', 'Detail Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #3b82f6;
        }

        .breadcrumb .current {
            color: #111827;
            font-weight: 500;
        }

        .header-card {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 20px;
            padding: 32px;
            color: white;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }

        .header-card.female {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        }

        .header-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .header-avatar {
            width: 80px;
            height: 80px;
            min-width: 80px;
            min-height: 80px;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: 700;
        }

        .header-text h1 {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .header-meta {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .header-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            font-size: 13px;
            font-weight: 500;
        }

        .header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-white {
            background: white;
            color: #2563eb;
        }

        .btn-white:hover {
            background: #f0f9ff;
        }

        .btn-white-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
        }

        .btn-white-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-sm {
            padding: 8px 14px;
            font-size: 13px;
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
        }

        .card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h5 i {
            color: #3b82f6;
        }

        .card-body {
            padding: 24px;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        @media (max-width: 992px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 576px) {
            .info-grid {
                grid-template-columns: 1fr;
            }
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6b7280;
            font-weight: 600;
        }

        .info-value {
            font-size: 15px;
            color: #111827;
        }

        .kelas-current {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            border: 2px solid #bbf7d0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }

        .kelas-current.no-kelas {
            background: linear-gradient(135deg, #fefce8 0%, #fef9c3 100%);
            border-color: #fde047;
        }

        .kelas-current h4 {
            font-size: 14px;
            color: #166534;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .kelas-current.no-kelas h4 {
            color: #a16207;
        }

        .kelas-current .kelas-name {
            font-size: 24px;
            font-weight: 700;
            color: #15803d;
            margin-bottom: 8px;
        }

        .kelas-current.no-kelas .kelas-name {
            font-size: 18px;
            color: #ca8a04;
        }

        .kelas-current .kelas-meta {
            font-size: 13px;
            color: #16a34a;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
        }

        .form-group select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 14px;
        }

        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .parent-checkbox-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 16px;
        }

        .parent-checkbox-grid .form-group { margin-bottom: 0; }

        .parent-checkbox-grid label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 10px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 13px;
            white-space: nowrap;
        }

        @media (max-width: 767.98px) {
            .header-card {
                padding: 20px;
            }
            .header-text h1 {
                font-size: 18px;
            }
            .header-top {
                flex-direction: column;
                gap: 12px;
            }
            .header-actions {
                width: 100%;
            }
            .header-actions .btn {
                flex: 1;
                justify-content: center;
            }
            .parent-checkbox-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="breadcrumb">
            <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
            <span>/</span>
            <a href="{{ route('waka.manajemen-siswa.index') }}">Manajemen Siswa</a>
            <span>/</span>
            <span class="current">{{ $siswa->nama_lengkap }}</span>
        </div>

        <div class="header-card {{ $siswa->jenis_kelamin == 'P' ? 'female' : '' }}">
            <div class="header-content">
                <div class="header-top">
                    <div class="header-info">
                        <div class="header-avatar">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</div>
                        <div class="header-text">
                            <h1>{{ $siswa->nama_lengkap }}</h1>
                            <div class="header-meta">
                                <span class="header-badge">
                                    <i class="fas fa-id-card"></i> NISN: {{ $siswa->nisn }}
                                </span>
                                @if($siswa->nis)
                                    <span class="header-badge">
                                        <i class="fas fa-hashtag"></i> NIS: {{ $siswa->nis }}
                                    </span>
                                @endif
                                <span class="header-badge">
                                    <i class="fas fa-{{ $siswa->jenis_kelamin == 'L' ? 'mars' : 'venus' }}"></i>
                                    {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        <a href="{{ route('waka.manajemen-siswa.print-kartu', $siswa) }}" class="btn btn-white"
                            target="_blank">
                            <i class="fas fa-id-card"></i> Cetak Kartu
                        </a>
                        <a href="{{ route('waka.manajemen-siswa.index') }}" class="btn btn-white-outline">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid-2">
            {{-- Data Pribadi --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user"></i> Data Pribadi</h5>
                </div>
                <div class="card-body">
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Nama Lengkap</span>
                            <span class="info-value">{{ $siswa->nama_lengkap }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NISN</span>
                            <span class="info-value"
                                style="font-family: monospace; color: #3b82f6;">{{ $siswa->nisn }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">NIS</span>
                            <span class="info-value">{{ $siswa->nis ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Jenis Kelamin</span>
                            <span class="info-value">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tempat, Tanggal Lahir</span>
                            <span class="info-value">{{ $siswa->tempat_lahir }},
                                {{ $siswa->tanggal_lahir->format('d F Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Alamat</span>
                            <span class="info-value">{{ $siswa->alamat }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nama Ayah</span>
                            <span class="info-value">{{ $siswa->nama_ayah ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Nama Ibu</span>
                            <span class="info-value">{{ $siswa->nama_ibu ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Telepon Orang Tua</span>
                            <span class="info-value">{{ $siswa->telepon_orangtua ?? '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Tanggal Masuk</span>
                            <span class="info-value">{{ $siswa->tanggal_masuk->format('d F Y') }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Status</span>
                            <span class="info-value">
                                <span class="badge {{ $siswa->status == 'aktif' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($siswa->status) }}
                                </span>
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Cabang</span>
                            <span class="info-value">{{ $siswa->cabang->nama_cabang ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kelas & Penempatan --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-graduation-cap"></i> Kelas & Penempatan</h5>
                </div>
                <div class="card-body">
                    <div class="kelas-current {{ !$siswa->kelas ? 'no-kelas' : '' }}">
                        @if($siswa->kelas)
                            <h4>Kelas Saat Ini</h4>
                            <div class="kelas-name">{{ $siswa->kelas->nama_kelas }}</div>
                            <div class="kelas-meta">
                                <i class="fas fa-layer-group"></i> {{ $siswa->kelas->jenjang }} •
                                <i class="fas fa-building"></i> {{ $siswa->kelas->cabang->nama_cabang ?? '-' }} •
                                <i class="fas fa-calendar"></i> {{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                                @if($siswa->kelas->waliKelas)
                                    <br><i class="fas fa-user-tie"></i> Wali Kelas: {{ $siswa->kelas->waliKelas->nama_lengkap }}
                                @endif
                            </div>
                        @else
                            <h4><i class="fas fa-exclamation-triangle"></i> Belum Ada Kelas</h4>
                            <div class="kelas-name">Siswa ini belum ditempatkan di kelas manapun</div>
                        @endif
                    </div>

                    <form action="{{ route('waka.manajemen-siswa.assign-kelas', $siswa) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label
                                for="kelas_id">{{ $siswa->kelas ? 'Pindahkan ke Kelas Lain' : 'Tempatkan ke Kelas' }}</label>
                            <select name="kelas_id" id="kelas_id">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelasList->groupBy('jenjang') as $jenjang => $kelasGroup)
                                    <optgroup label="{{ $jenjang }}">
                                        @foreach($kelasGroup as $k)
                                            @php
                                                $sisaKuota = $k->kuota_siswa - $k->siswa_count;
                                            @endphp
                                            <option value="{{ $k->id }}" {{ $siswa->kelas_id == $k->id ? 'selected' : '' }} {{ $sisaKuota <= 0 && $siswa->kelas_id != $k->id ? 'disabled' : '' }}>
                                                {{ $k->nama_kelas }} - {{ $k->cabang->nama_cabang ?? '' }} (Sisa: {{ $sisaKuota }})
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            {{-- Data Orang Tua / Wali --}}
            <div class="card">
                <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <h5><i class="fas fa-users"></i> Data Orang Tua / Wali</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="btnTambahOrangTua" style="z-index: 10;">
                        <i class="fas fa-plus"></i> Tambah Orang Tua
                    </button>
                </div>
                <div class="card-body">
                    <div class="card-body">
                        @php
                            $hasAyahKandung = $siswa->orangTua->contains(fn($p) => $p->pivot->relationship === 'ayah_kandung');
                            $hasIbuKandung = $siswa->orangTua->contains(fn($p) => $p->pivot->relationship === 'ibu_kandung');
                            $existingParentIds = $siswa->orangTua->pluck('id')->toArray();
                        @endphp

                        @if($siswa->orangTua && $siswa->orangTua->count() > 0)
                            <div class="info-grid">
                                @foreach($siswa->orangTua as $parent)
                                    <div class="info-item"
                                        style="grid-column: 1 / -1; padding: 16px; background: #f9fafb; border-radius: 8px; margin-bottom: 12px;">
                                        <div style="display: flex; justify-content: space-between; align-items: start;">
                                            <div style="flex: 1;">
                                                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 8px;">
                                                    <div
                                                        style="width: 40px; height: 40px; background: {{ $parent->pivot->relationship === 'ayah_kandung' ? '#3b82f6' : ($parent->pivot->relationship === 'ibu_kandung' ? '#ec4899' : '#6b7280') }}; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                        {{ strtoupper(substr($parent->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div style="font-weight: 600; font-size: 16px;">{{ $parent->name }}</div>
                                                        <div style="font-size: 13px; color: #6b7280;">
                                                            <i class="fas fa-envelope"></i> {{ $parent->email }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <div
                                                    style="display: flex; gap: 8px; flex-wrap: wrap; margin-top: 8px; font-size: 13px;">
                                                    <span class="badge"
                                                        style="background: {{ in_array($parent->pivot->relationship, ['ayah_kandung', 'ibu_kandung']) ? '#dbeafe' : '#f3f4f6' }}; color: {{ in_array($parent->pivot->relationship, ['ayah_kandung', 'ibu_kandung']) ? '#1e40af' : '#374151' }};">
                                                        <i class="fas fa-user-friends"></i>
                                                        {{ ucwords(str_replace('_', ' ', $parent->pivot->relationship)) }}
                                                    </span>
                                                    @if($parent->pivot->is_primary)
                                                        <span class="badge badge-success">
                                                            <i class="fas fa-star"></i> Penanggung Jawab Utama
                                                        </span>
                                                    @endif
                                                    @if($parent->pivot->is_financial_responsible)
                                                        <span class="badge" style="background: #dcfce7; color: #166534;">
                                                            <i class="fas fa-wallet"></i> Penanggung Jawab Keuangan
                                                        </span>
                                                    @endif
                                                    @if($parent->pivot->can_access_academic)
                                                        <span class="badge" style="background: #e0e7ff; color: #3730a3;">
                                                            <i class="fas fa-book"></i> Akses Akademik
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <form action="{{ route('waka.manajemen-siswa.detach-parent', [$siswa, $parent]) }}"
                                                method="POST" id="detachParentForm{{ $parent->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm"
                                                    style="background: #fee2e2; color: #dc2626; border: none;"
                                                    onclick="confirmDetachParent('{{ $parent->id }}', '{{ $parent->name }}', '{{ ucwords(str_replace('_', ' ', $parent->pivot->relationship)) }}')">
                                                    <i class="fas fa-unlink"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert"
                                style="background: #fef3c7; border: 1px solid #fde047; color: #92400e; margin: 0;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Belum ada data orang tua/wali yang terhubung dengan siswa ini.
                            </div>
                        @endif

                        {{-- Form Tambah Orang Tua (Hidden by default) --}}
                        <div id="addParentFormContainer"
                            style="display: none; margin-top: 20px; padding: 20px; border: 2px solid #e5e7eb; border-radius: 12px; background: #fafafa;">
                            <div
                                style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                                <h6 style="margin: 0; font-size: 16px; font-weight: 600;"><i class="fas fa-user-plus"
                                        style="color: #3b82f6;"></i> Tambah Orang Tua / Wali</h6>
                                <button type="button" class="btn btn-sm" style="background: #f3f4f6; border: none;"
                                    onclick="hideAddParentForm()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            {{-- Info validation --}}
                            @if($hasAyahKandung && $hasIbuKandung)
                                <div class="alert"
                                    style="background: #dbeafe; border: 1px solid #93c5fd; color: #1e40af; margin-bottom: 16px;">
                                    <i class="fas fa-info-circle"></i>
                                    Siswa ini sudah memiliki Ayah Kandung dan Ibu Kandung. Anda masih dapat menambahkan
                                    wali/orang
                                    tua dengan hubungan lain.
                                </div>
                            @elseif($hasAyahKandung)
                                <div class="alert"
                                    style="background: #dbeafe; border: 1px solid #93c5fd; color: #1e40af; margin-bottom: 16px;">
                                    <i class="fas fa-info-circle"></i>
                                    Siswa ini sudah memiliki Ayah Kandung. Opsi "Ayah Kandung" tidak tersedia.
                                </div>
                            @elseif($hasIbuKandung)
                                <div class="alert"
                                    style="background: #dbeafe; border: 1px solid #93c5fd; color: #1e40af; margin-bottom: 16px;">
                                    <i class="fas fa-info-circle"></i>
                                    Siswa ini sudah memiliki Ibu Kandung. Opsi "Ibu Kandung" tidak tersedia.
                                </div>
                            @endif

                            {{-- Option Selector --}}
                            <div class="form-group">
                                <label class="form-label">Opsi Tambah Orang Tua</label>
                                <select id="parentOptionSelect" class="form-control" onchange="toggleParentOption()">
                                    <option value="">-- Pilih Opsi --</option>
                                    <option value="existing">Pilih Orang Tua yang Sudah Ada</option>
                                    <option value="new">Buat Akun Orang Tua Baru</option>
                                </select>
                            </div>

                            {{-- Existing Parent Selection Form --}}
                            <div id="existingParentForm" style="display: none;">
                                <form action="{{ route('waka.manajemen-siswa.attach-parent', $siswa) }}" method="POST"
                                    id="attachParentForm" onsubmit="return validateAttachParentForm()">
                                    @csrf

                                    {{-- Search and Filter --}}
                                    <div
                                        style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;">
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <label class="form-label"><i class="fas fa-search" style="color: #9ca3af;"></i>
                                                Cari
                                                Orang Tua</label>
                                            <input type="text" id="searchParentInput" class="form-control"
                                                placeholder="Ketik nama atau email..." oninput="filterParentList()">
                                        </div>
                                        <div class="form-group" style="margin-bottom: 0;">
                                            <label class="form-label"><i class="fas fa-filter" style="color: #9ca3af;"></i>
                                                Filter Status</label>
                                            <select id="filterParentStatus" class="form-control"
                                                onchange="filterParentList()">
                                                <option value="">Semua Orang Tua</option>
                                                <option value="available">Belum Punya Anak Terdaftar (Baru)</option>
                                                <option value="has_children">Sudah Punya Anak Terdaftar</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Parent List --}}
                                    <div class="form-group">
                                        <label class="form-label">Pilih Orang Tua <span style="color: #ef4444;">*</span>
                                            <small style="color: #6b7280; font-weight: normal;">(<span
                                                    id="parentCount">{{ $availableParents->count() }}</span>
                                                tersedia)</small></label>
                                        <div id="parentListContainer"
                                            style="max-height: 250px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 8px; padding: 8px; background: white;">
                                            @forelse($availableParents as $p)
                                                @php
                                                    $isAlreadyLinked = in_array($p->id, $existingParentIds);
                                                    $hasChildren = $p->studentParents->count() > 0;
                                                @endphp
                                                <label class="parent-option" data-name="{{ strtolower($p->name) }}"
                                                    data-email="{{ strtolower($p->email) }}"
                                                    data-already-linked="{{ $isAlreadyLinked ? 'true' : 'false' }}"
                                                    data-status="{{ $hasChildren ? 'has_children' : 'available' }}"
                                                    style="display: {{ $isAlreadyLinked ? 'none' : 'flex' }}; align-items: center; padding: 12px; margin-bottom: 8px; background: #f9fafb; border: 2px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.2s;">
                                                    <input type="radio" name="parent_id" value="{{ $p->id }}"
                                                        style="margin-right: 12px;" {{ $isAlreadyLinked ? 'disabled' : '' }}
                                                        required>
                                                    <div style="flex: 1;">
                                                        <div
                                                            style="font-weight: 600; color: #111827; display: flex; align-items: center; gap: 8px;">
                                                            <i class="fas fa-user" style="color: #f59e0b;"></i>
                                                            {{ $p->name }}
                                                            @if(!$hasChildren)
                                                                <span
                                                                    style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 4px; font-size: 11px; font-weight: 600;">BARU</span>
                                                            @endif
                                                        </div>
                                                        <small style="color: #64748b;">
                                                            {{ $p->email }}
                                                            @if($hasChildren)
                                                                • <strong>Anak:</strong>
                                                                {{ $p->studentParents->take(3)->pluck('siswa.nama_lengkap')->join(', ') }}{{ $p->studentParents->count() > 3 ? '...' : '' }}
                                                            @else
                                                                • <em>Belum memiliki anak terdaftar</em>
                                                            @endif
                                                        </small>
                                                    </div>
                                                </label>
                                            @empty
                                                <div style="text-align: center; padding: 20px; color: #64748b;">
                                                    <i class="fas fa-users-slash"
                                                        style="font-size: 24px; margin-bottom: 8px;"></i>
                                                    <p style="margin: 0;">Tidak ada akun orang tua tersedia.</p>
                                                    <small>Silakan buat akun baru terlebih dahulu.</small>
                                                </div>
                                            @endforelse
                                            <div id="noParentFound"
                                                style="display: none; text-align: center; padding: 20px; color: #64748b;">
                                                <i class="fas fa-search" style="font-size: 24px; margin-bottom: 8px;"></i>
                                                <p style="margin: 0; font-weight: 600;">Tidak ada orang tua yang ditemukan
                                                </p>
                                                <small>Coba ubah kata kunci pencarian</small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Relationship Selection --}}
                                    <div class="form-group">
                                        <label class="form-label">Hubungan <span style="color: #ef4444;">*</span></label>
                                        <select name="relationship" id="relationshipSelect" class="form-control" required
                                            onchange="checkRelationshipValidation()">
                                            <option value="">-- Pilih Hubungan --</option>
                                            <option value="ayah_kandung" {{ $hasAyahKandung ? 'disabled' : '' }}>Ayah
                                                Kandung
                                                {{ $hasAyahKandung ? '(Sudah Ada)' : '' }}
                                            </option>
                                            <option value="ibu_kandung" {{ $hasIbuKandung ? 'disabled' : '' }}>Ibu Kandung
                                                {{ $hasIbuKandung ? '(Sudah Ada)' : '' }}
                                            </option>
                                            <option value="ayah_tiri">Ayah Tiri</option>
                                            <option value="ibu_tiri">Ibu Tiri</option>
                                            <option value="kakek">Kakek</option>
                                            <option value="nenek">Nenek</option>
                                            <option value="paman">Paman</option>
                                            <option value="bibi">Bibi</option>
                                            <option value="wali">Wali</option>
                                            <option value="lainnya">Lainnya</option>
                                        </select>
                                    </div>

                                    {{-- Checkboxes --}}
                                    <div class="parent-checkbox-grid">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_primary" value="1">
                                                <span style="font-size: 13px;"><i class="fas fa-star"
                                                        style="color: #f59e0b;"></i> Penanggung Jawab Utama</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_financial_responsible" value="1" checked>
                                                <span style="font-size: 13px;"><i class="fas fa-wallet"
                                                        style="color: #10b981;"></i> Penanggung Jawab Keuangan</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="can_access_academic" value="1" checked>
                                                <span style="font-size: 13px;"><i class="fas fa-book"
                                                        style="color: #3b82f6;"></i> Akses Data Akademik</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Submit Buttons --}}
                                    <div style="display: flex; gap: 8px;">
                                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                                            <i class="fas fa-link"></i> Hubungkan Orang Tua
                                        </button>
                                        <button type="button" class="btn"
                                            style="flex: 1; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;"
                                            onclick="hideAddParentForm()">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- New Parent Creation Form --}}
                            <div id="newParentForm" style="display: none;">
                                <form action="{{ route('waka.manajemen-siswa.attach-parent', $siswa) }}" method="POST"
                                    id="createParentForm" onsubmit="return validateCreateParentForm()">
                                    @csrf
                                    <input type="hidden" name="create_new_parent" value="1">

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                        <div class="form-group">
                                            <label class="form-label">Nama Lengkap <span
                                                    style="color: #ef4444;">*</span></label>
                                            <input type="text" name="new_parent_name" class="form-control"
                                                placeholder="Nama lengkap orang tua" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Username <span
                                                    style="color: #ef4444;">*</span></label>
                                            <input type="text" name="new_parent_username" class="form-control"
                                                placeholder="Username untuk login" required>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                        <div class="form-group">
                                            <label class="form-label">Email <span style="color: #ef4444;">*</span></label>
                                            <input type="email" name="new_parent_email" class="form-control"
                                                placeholder="contoh@email.com" required>
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Password <span
                                                    style="color: #ef4444;">*</span></label>
                                            <div style="position: relative;">
                                                <input type="password" name="new_parent_password" id="newParentPassword"
                                                    class="form-control" placeholder="Minimal 8 karakter"
                                                    style="padding-right: 40px;" required>
                                                <button type="button"
                                                    onclick="togglePasswordVisibility('newParentPassword', 'toggleNewParentPwdIcon')"
                                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: #64748b;">
                                                    <i id="toggleNewParentPwdIcon" class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                        <div class="form-group">
                                            <label class="form-label">No. Telepon/WA</label>
                                            <input type="text" name="new_parent_phone" class="form-control"
                                                placeholder="Contoh: 08123456789">
                                        </div>
                                        <div class="form-group">
                                            <label class="form-label">Hubungan <span
                                                    style="color: #ef4444;">*</span></label>
                                            <select name="relationship" id="newRelationshipSelect" class="form-control"
                                                required>
                                                <option value="">-- Pilih Hubungan --</option>
                                                <option value="ayah_kandung" {{ $hasAyahKandung ? 'disabled' : '' }}>Ayah
                                                    Kandung {{ $hasAyahKandung ? '(Sudah Ada)' : '' }}</option>
                                                <option value="ibu_kandung" {{ $hasIbuKandung ? 'disabled' : '' }}>Ibu
                                                    Kandung
                                                    {{ $hasIbuKandung ? '(Sudah Ada)' : '' }}
                                                </option>
                                                <option value="ayah_tiri">Ayah Tiri</option>
                                                <option value="ibu_tiri">Ibu Tiri</option>
                                                <option value="kakek">Kakek</option>
                                                <option value="nenek">Nenek</option>
                                                <option value="paman">Paman</option>
                                                <option value="bibi">Bibi</option>
                                                <option value="wali">Wali</option>
                                                <option value="lainnya">Lainnya</option>
                                            </select>
                                        </div>
                                    </div>

                                    {{-- Checkboxes --}}
                                    <div class="parent-checkbox-grid">
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_primary" value="1">
                                                <span style="font-size: 13px;"><i class="fas fa-star"
                                                        style="color: #f59e0b;"></i> Penanggung Jawab Utama</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="is_financial_responsible" value="1" checked>
                                                <span style="font-size: 13px;"><i class="fas fa-wallet"
                                                        style="color: #10b981;"></i> Penanggung Jawab Keuangan</span>
                                            </label>
                                        </div>
                                        <div class="form-group">
                                            <label>
                                                <input type="checkbox" name="can_access_academic" value="1" checked>
                                                <span style="font-size: 13px;"><i class="fas fa-book"
                                                        style="color: #3b82f6;"></i> Akses Data Akademik</span>
                                            </label>
                                        </div>
                                    </div>

                                    {{-- Submit Buttons --}}
                                    <div style="display: flex; gap: 8px;">
                                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                                            <i class="fas fa-user-plus"></i> Buat & Hubungkan Orang Tua
                                        </button>
                                        <button type="button" class="btn"
                                            style="flex: 1; background: #f3f4f6; color: #374151; border: 1px solid #d1d5db;"
                                            onclick="hideAddParentForm()">
                                            <i class="fas fa-times"></i> Batal
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Show/hide add parent form
            document.getElementById('btnTambahOrangTua').addEventListener('click', function () {
                document.getElementById('addParentFormContainer').style.display = 'block';
                document.getElementById('parentOptionSelect').value = '';
                document.getElementById('existingParentForm').style.display = 'none';
                document.getElementById('newParentForm').style.display = 'none';
            });

            function hideAddParentForm() {
                document.getElementById('addParentFormContainer').style.display = 'none';
                document.getElementById('parentOptionSelect').value = '';
                document.getElementById('existingParentForm').style.display = 'none';
                document.getElementById('newParentForm').style.display = 'none';
            }

            function toggleParentOption() {
                const option = document.getElementById('parentOptionSelect').value;
                document.getElementById('existingParentForm').style.display = option === 'existing' ? 'block' : 'none';
                document.getElementById('newParentForm').style.display = option === 'new' ? 'block' : 'none';

                if (option === 'existing') {
                    document.getElementById('searchParentInput').value = '';
                    filterParentList();
                }
            }

            function filterParentList() {
                const searchTerm = document.getElementById('searchParentInput').value.toLowerCase();
                const statusFilter = document.getElementById('filterParentStatus').value;
                const parentOptions = document.querySelectorAll('.parent-option');
                const noParentFound = document.getElementById('noParentFound');
                const parentCount = document.getElementById('parentCount');
                let visibleCount = 0;

                parentOptions.forEach(option => {
                    const name = option.getAttribute('data-name');
                    const email = option.getAttribute('data-email');
                    const alreadyLinked = option.getAttribute('data-already-linked');
                    const status = option.getAttribute('data-status');

                    // Always hide parents already linked to this student
                    if (alreadyLinked === 'true') {
                        option.style.display = 'none';
                        return;
                    }

                    // Search filter
                    const matchSearch = searchTerm === '' || name.includes(searchTerm) || email.includes(searchTerm);

                    // Status filter
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

                if (noParentFound) {
                    noParentFound.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            }

            function validateAttachParentForm() {
                const parentSelected = document.querySelector('input[name="parent_id"]:checked');
                const relationship = document.getElementById('relationshipSelect').value;

                if (!parentSelected) {
                    alert('Silakan pilih orang tua terlebih dahulu!');
                    return false;
                }

                if (!relationship) {
                    alert('Silakan pilih hubungan dengan siswa!');
                    return false;
                }

                return true;
            }

            function validateCreateParentForm() {
                const name = document.querySelector('input[name="new_parent_name"]').value;
                const username = document.querySelector('input[name="new_parent_username"]').value;
                const email = document.querySelector('input[name="new_parent_email"]').value;
                const password = document.querySelector('input[name="new_parent_password"]').value;
                const relationship = document.getElementById('newRelationshipSelect').value;

                if (!name || !username || !email || !password) {
                    alert('Silakan lengkapi semua field yang wajib diisi!');
                    return false;
                }

                if (password.length < 8) {
                    alert('Password minimal 8 karakter!');
                    return false;
                }

                if (!relationship) {
                    alert('Silakan pilih hubungan dengan siswa!');
                    return false;
                }

                return true;
            }

            function togglePasswordVisibility(fieldId, iconId) {
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

            // Highlight selected parent option
            document.querySelectorAll('.parent-option').forEach(option => {
                option.addEventListener('click', function () {
                    document.querySelectorAll('.parent-option').forEach(o => {
                        o.style.borderColor = '#e5e7eb';
                        o.style.background = '#f9fafb';
                    });
                    this.style.borderColor = '#3b82f6';
                    this.style.background = '#eff6ff';
                });
            });

            // Detach Parent Modal Confirmation
            let detachParentId = null;

            function confirmDetachParent(parentId, parentName, relationship) {
                detachParentId = parentId;
                document.getElementById('detachParentName').textContent = parentName;
                document.getElementById('detachParentRelationship').textContent = relationship;

                const modal = new bootstrap.Modal(document.getElementById('detachParentModal'));
                modal.show();
            }

            function submitDetachParentForm() {
                if (detachParentId) {
                    document.getElementById('detachParentForm' + detachParentId).submit();
                }
            }
        </script>

        <!-- Detach Parent Confirmation Modal -->
        <div class="modal fade" id="detachParentModal" tabindex="-1" aria-labelledby="detachParentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden;">
                    <div class="modal-header d-flex justify-content-between align-items-center"
                        style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; padding: 20px 24px;">
                        <h5 class="modal-title" id="detachParentModalLabel"
                            style="display: flex; align-items: center; gap: 10px; margin: 0; font-weight: 600;">
                            <i class="fas fa-unlink"></i>
                            Konfirmasi Hapus Hubungan
                        </h5>
                        <button type="button" data-bs-dismiss="modal" aria-label="Close"
                            style="background: transparent; border: none; color: white; font-size: 1.25rem; opacity: 0.9; padding: 0; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; transition: background 0.2s;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body" style="padding: 24px;">
                        <p style="margin-bottom: 16px; color: #374151; font-size: 15px;">Apakah Anda yakin ingin menghapus
                            hubungan dengan orang tua berikut?</p>
                        <div
                            style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                            <div style="font-weight: 600; color: #111827; margin-bottom: 4px;" id="detachParentName"></div>
                            <div style="font-size: 14px; color: #6b7280;">Hubungan: <span
                                    id="detachParentRelationship"></span>
                            </div>
                        </div>
                        <p style="color: #6b7280; font-size: 14px; margin: 0;">
                            <i class="fas fa-info-circle"></i> Hubungan akan dihapus. Orang tua masih bisa dihubungkan
                            kembali
                            nanti.
                        </p>
                    </div>
                    <div class="modal-footer" style="border: none; padding: 16px 24px; background: #f9fafb; gap: 10px;">
                        <button type="button" class="btn"
                            style="flex: 1; background: white; border: 1px solid #d1d5db; color: #374151;"
                            data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="button" class="btn" onclick="submitDetachParentForm()"
                            style="flex: 1; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none;">
                            <i class="fas fa-unlink"></i> Ya, Hapus Hubungan
                        </button>
                    </div>
                </div>
            </div>
        </div>
@endsection