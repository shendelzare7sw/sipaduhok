@extends('layouts.sneat')

@section('title', 'Detail Siswa - ' . $siswa->nama_lengkap)

@section('page-title', 'Detail Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/manajemen-siswa/show.css'])
@endsection

@section('content')
    <div class="ms-show-page">
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
                        <a href="{{ route('waka.manajemen-siswa.print-kartu', $siswa) }}" class="btn btn-white" target="_blank">
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
                            <span class="info-value text-code-blue">{{ $siswa->nisn }}</span>
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
                            <span class="info-value">{{ $siswa->tempat_lahir }}, {{ $siswa->tanggal_lahir->format('d F Y') }}</span>
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
                            <span class="info-label">Telepon Wali Siswa</span>
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
                                <i class="fas fa-layer-group"></i> {{ $siswa->kelas->jenjang }} &bull;
                                <i class="fas fa-building"></i> {{ $siswa->kelas->cabang->nama_cabang ?? '-' }} &bull;
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
                            <label for="kelas_id">{{ $siswa->kelas ? 'Pindahkan ke Kelas Lain' : 'Tempatkan ke Kelas' }}</label>
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
                        <button type="submit" class="btn btn-primary btn-full">
                            <i class="fas fa-save"></i> Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header card-header-flex">
                    <h5><i class="fas fa-users"></i> Data Wali Siswa / Wali</h5>
                    <button type="button" class="btn btn-sm btn-primary add-parent-button" id="btnTambahOrangTua">
                        <i class="fas fa-plus"></i> Tambah Wali Siswa
                    </button>
                </div>
                <div class="card-body">
                    @php
                        $hasAyahKandung = $siswa->orangTua->contains(fn($p) => $p->pivot->relationship === 'ayah_kandung');
                        $hasIbuKandung = $siswa->orangTua->contains(fn($p) => $p->pivot->relationship === 'ibu_kandung');
                        $existingParentIds = $siswa->orangTua->pluck('id')->toArray();
                    @endphp

                    @if($siswa->orangTua && $siswa->orangTua->count() > 0)
                        <div class="info-grid">
                            @foreach($siswa->orangTua as $parent)
                                @php
                                    $relationshipLabel = ucwords(str_replace('_', ' ', $parent->pivot->relationship));
                                    $isCoreRelationship = in_array($parent->pivot->relationship, ['ayah_kandung', 'ibu_kandung']);
                                @endphp
                                <div class="info-item parent-linked-item">
                                    <div class="parent-linked-row">
                                        <div class="parent-linked-content">
                                            <div class="parent-linked-heading">
                                                <div class="parent-avatar parent-avatar-{{ $parent->pivot->relationship }}">
                                                    {{ strtoupper(substr($parent->name, 0, 1)) }}
                                                </div>
                                                <div class="parent-linked-text">
                                                    <div class="parent-name">{{ $parent->name }}</div>
                                                    <div class="parent-email-row">
                                                        <i class="fas fa-envelope parent-email-icon"></i>
                                                        <span>{{ $parent->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="parent-badges">
                                                <span class="badge {{ $isCoreRelationship ? 'relationship-badge-core' : 'relationship-badge-other' }}">
                                                    <i class="fas fa-user-friends"></i> {{ $relationshipLabel }}
                                                </span>
                                                @if($parent->pivot->is_primary)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-star"></i> Penanggung Jawab Utama
                                                    </span>
                                                @endif
                                                @if($parent->pivot->is_financial_responsible)
                                                    <span class="badge badge-financial">
                                                        <i class="fas fa-wallet"></i> Penanggung Jawab Keuangan
                                                    </span>
                                                @endif
                                                @if($parent->pivot->can_access_academic)
                                                    <span class="badge badge-academic">
                                                        <i class="fas fa-book"></i> Akses Akademik
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <form action="{{ route('waka.manajemen-siswa.detach-parent', [$siswa, $parent]) }}" method="POST" id="detachParentForm{{ $parent->id }}" class="detach-parent-form">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-detach-parent"
                                                data-detach-parent
                                                data-parent-id="{{ $parent->id }}"
                                                data-parent-name="{{ $parent->name }}"
                                                data-relationship="{{ $relationshipLabel }}"
                                            >
                                                <i class="fas fa-unlink"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-parent-empty">
                            <i class="fas fa-exclamation-triangle"></i>
                            Belum ada data wali siswa/wali yang terhubung dengan siswa ini.
                        </div>
                    @endif

                    <div id="addParentFormContainer" class="add-parent-form">
                        <div class="add-parent-header">
                            <h6 class="add-parent-title">
                                <i class="fas fa-user-plus title-icon-primary"></i> Tambah Wali Siswa / Wali
                            </h6>
                            <button type="button" class="btn btn-sm btn-subtle-icon" data-hide-parent-form>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        @if($hasAyahKandung && $hasIbuKandung)
                            <div class="alert alert-info-parent">
                                <i class="fas fa-info-circle"></i>
                                Siswa ini sudah memiliki Ayah Kandung dan Ibu Kandung. Anda masih dapat menambahkan wali/wali siswa dengan hubungan lain.
                            </div>
                        @elseif($hasAyahKandung)
                            <div class="alert alert-info-parent">
                                <i class="fas fa-info-circle"></i>
                                Siswa ini sudah memiliki Ayah Kandung. Opsi "Ayah Kandung" tidak tersedia.
                            </div>
                        @elseif($hasIbuKandung)
                            <div class="alert alert-info-parent">
                                <i class="fas fa-info-circle"></i>
                                Siswa ini sudah memiliki Ibu Kandung. Opsi "Ibu Kandung" tidak tersedia.
                            </div>
                        @endif

                        <div class="form-group">
                            <label class="form-label">Opsi Tambah Wali Siswa</label>
                            <select id="parentOptionSelect" class="form-control">
                                <option value="">-- Pilih Opsi --</option>
                                <option value="existing">Pilih Wali Siswa yang Sudah Ada</option>
                                <option value="new">Buat Akun Wali Siswa Baru</option>
                            </select>
                        </div>

                        <div id="existingParentForm" class="parent-form-panel">
                            <form action="{{ route('waka.manajemen-siswa.attach-parent', $siswa) }}" method="POST" id="attachParentForm">
                                @csrf

                                <div class="form-grid-2">
                                    <div class="form-group form-group-compact">
                                        <label class="form-label">
                                            <i class="fas fa-search form-icon-muted"></i> Cari Wali Siswa
                                        </label>
                                        <input type="text" id="searchParentInput" class="form-control" placeholder="Ketik nama atau email...">
                                    </div>
                                    <div class="form-group form-group-compact">
                                        <label class="form-label">
                                            <i class="fas fa-filter form-icon-muted"></i> Filter Status
                                        </label>
                                        <select id="filterParentStatus" class="form-control">
                                            <option value="">Semua Wali Siswa</option>
                                            <option value="available">Belum Punya Anak Terdaftar (Baru)</option>
                                            <option value="has_children">Sudah Punya Anak Terdaftar</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">
                                        Pilih Wali Siswa <span class="required-mark">*</span>
                                        <small class="label-note">(<span id="parentCount">{{ $availableParents->count() }}</span> tersedia)</small>
                                    </label>
                                    <div id="parentListContainer" class="parent-list-container">
                                        @forelse($availableParents as $p)
                                            @php
                                                $isAlreadyLinked = in_array($p->id, $existingParentIds);
                                                $hasChildren = $p->studentParents->count() > 0;
                                            @endphp
                                            <label
                                                class="parent-option {{ $isAlreadyLinked ? 'is-hidden' : '' }}"
                                                data-name="{{ strtolower($p->name) }}"
                                                data-email="{{ strtolower($p->email) }}"
                                                data-already-linked="{{ $isAlreadyLinked ? 'true' : 'false' }}"
                                                data-status="{{ $hasChildren ? 'has_children' : 'available' }}"
                                            >
                                                <input type="radio" name="parent_id" value="{{ $p->id }}" class="parent-radio" {{ $isAlreadyLinked ? 'disabled' : '' }} required>
                                                <div class="parent-option-body">
                                                    <div class="parent-option-name">
                                                        <i class="fas fa-user"></i>
                                                        <span class="parent-option-name-text">{{ $p->name }}</span>
                                                        @if(!$hasChildren)
                                                            <span class="new-badge">BARU</span>
                                                        @endif
                                                    </div>
                                                    <small class="parent-option-meta">
                                                        {{ $p->email }}
                                                        @if($hasChildren)
                                                            <br><strong>Anak:</strong>
                                                            {{ $p->studentParents->take(3)->pluck('siswa.nama_lengkap')->join(', ') }}{{ $p->studentParents->count() > 3 ? '...' : '' }}
                                                        @else
                                                            <br><em>Belum memiliki anak terdaftar</em>
                                                        @endif
                                                    </small>
                                                </div>
                                            </label>
                                        @empty
                                            <div class="empty-parent-state">
                                                <i class="fas fa-users-slash empty-state-icon"></i>
                                                <p>Tidak ada akun wali siswa tersedia.</p>
                                                <small>Silakan buat akun baru terlebih dahulu.</small>
                                            </div>
                                        @endforelse
                                        <div id="noParentFound" class="empty-parent-state no-parent-found">
                                            <i class="fas fa-search empty-state-icon"></i>
                                            <p><strong>Tidak ada wali siswa yang ditemukan</strong></p>
                                            <small>Coba ubah kata kunci pencarian</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Hubungan <span class="required-mark">*</span></label>
                                    <select name="relationship" id="relationshipSelect" class="form-control" required>
                                        <option value="">-- Pilih Hubungan --</option>
                                        <option value="ayah_kandung" {{ $hasAyahKandung ? 'disabled' : '' }}>Ayah Kandung {{ $hasAyahKandung ? '(Sudah Ada)' : '' }}</option>
                                        <option value="ibu_kandung" {{ $hasIbuKandung ? 'disabled' : '' }}>Ibu Kandung {{ $hasIbuKandung ? '(Sudah Ada)' : '' }}</option>
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

                                <div class="parent-checkbox-grid">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_primary" value="1">
                                            <span class="checkbox-label-text"><i class="fas fa-star icon-warning"></i> Penanggung Jawab Utama</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_financial_responsible" value="1" checked>
                                            <span class="checkbox-label-text"><i class="fas fa-wallet icon-success"></i> Penanggung Jawab Keuangan</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="can_access_academic" value="1" checked>
                                            <span class="checkbox-label-text"><i class="fas fa-book icon-primary"></i> Akses Data Akademik</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary form-action-submit">
                                        <i class="fas fa-link"></i> Hubungkan Wali Siswa
                                    </button>
                                    <button type="button" class="btn btn-secondary-action" data-hide-parent-form>
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div id="newParentForm" class="parent-form-panel">
                            <form action="{{ route('waka.manajemen-siswa.attach-parent', $siswa) }}" method="POST" id="createParentForm">
                                @csrf
                                <input type="hidden" name="create_new_parent" value="1">

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Nama Lengkap <span class="required-mark">*</span></label>
                                        <input type="text" name="new_parent_name" class="form-control" placeholder="Nama lengkap wali siswa" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Username <span class="required-mark">*</span></label>
                                        <input type="text" name="new_parent_username" class="form-control" placeholder="Username untuk login" required>
                                    </div>
                                </div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">Email <span class="required-mark">*</span></label>
                                        <input type="email" name="new_parent_email" class="form-control" placeholder="contoh@email.com" required>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Password <span class="required-mark">*</span></label>
                                        <div class="password-wrapper">
                                            <input type="password" name="new_parent_password" id="newParentPassword" class="form-control password-input" placeholder="Minimal 8 karakter" required>
                                            <button type="button" class="password-toggle" data-toggle-password data-target="newParentPassword" data-icon="toggleNewParentPwdIcon">
                                                <i id="toggleNewParentPwdIcon" class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-grid-2">
                                    <div class="form-group">
                                        <label class="form-label">No. Telepon/WA</label>
                                        <input type="text" name="new_parent_phone" class="form-control" placeholder="Contoh: 08123456789">
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label">Hubungan <span class="required-mark">*</span></label>
                                        <select name="relationship" id="newRelationshipSelect" class="form-control" required>
                                            <option value="">-- Pilih Hubungan --</option>
                                            <option value="ayah_kandung" {{ $hasAyahKandung ? 'disabled' : '' }}>Ayah Kandung {{ $hasAyahKandung ? '(Sudah Ada)' : '' }}</option>
                                            <option value="ibu_kandung" {{ $hasIbuKandung ? 'disabled' : '' }}>Ibu Kandung {{ $hasIbuKandung ? '(Sudah Ada)' : '' }}</option>
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

                                <div class="parent-checkbox-grid">
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_primary" value="1">
                                            <span class="checkbox-label-text"><i class="fas fa-star icon-warning"></i> Penanggung Jawab Utama</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="is_financial_responsible" value="1" checked>
                                            <span class="checkbox-label-text"><i class="fas fa-wallet icon-success"></i> Penanggung Jawab Keuangan</span>
                                        </label>
                                    </div>
                                    <div class="form-group">
                                        <label>
                                            <input type="checkbox" name="can_access_academic" value="1" checked>
                                            <span class="checkbox-label-text"><i class="fas fa-book icon-primary"></i> Akses Data Akademik</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-actions">
                                    <button type="submit" class="btn btn-primary form-action-submit">
                                        <i class="fas fa-user-plus"></i> Buat & Hubungkan Wali Siswa
                                    </button>
                                    <button type="button" class="btn btn-secondary-action" data-hide-parent-form>
                                        <i class="fas fa-times"></i> Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="detachParentModal" tabindex="-1" aria-labelledby="detachParentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content detach-modal-content">
                    <div class="modal-header d-flex justify-content-between align-items-center detach-modal-header">
                        <h5 class="modal-title detach-modal-title" id="detachParentModalLabel">
                            <i class="fas fa-unlink"></i>
                            Konfirmasi Hapus Hubungan
                        </h5>
                        <button type="button" class="detach-modal-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body detach-modal-body">
                        <p class="detach-modal-message">Apakah Anda yakin ingin menghapus hubungan dengan wali siswa berikut?</p>
                        <div class="detach-parent-summary">
                            <div class="detach-parent-name" id="detachParentName"></div>
                            <div class="detach-parent-relationship">Hubungan: <span id="detachParentRelationship"></span></div>
                        </div>
                        <p class="detach-modal-note">
                            <i class="fas fa-info-circle"></i> Hubungan akan dihapus. Wali siswa masih bisa dihubungkan kembali nanti.
                        </p>
                    </div>
                    <div class="modal-footer detach-modal-footer">
                        <button type="button" class="btn btn-modal-cancel" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Batal
                        </button>
                        <button type="button" class="btn btn-modal-confirm" id="submitDetachParentButton">
                            <i class="fas fa-unlink"></i> Ya, Hapus Hubungan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/waka/manajemen-siswa/show.js'])
@endsection
