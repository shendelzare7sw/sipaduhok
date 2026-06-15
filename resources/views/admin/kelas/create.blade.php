@extends('layouts.sneat')

@section('title', 'Tambah Kelas')

@section('page-title', 'Tambah Kelas Baru')
@section('page-subtitle', 'Buat kelas baru untuk tahun ajaran')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/kelas/form.css'])
@endsection

@section('content')
<div class="kelas-form-page is-create">
    <div class="kelas-breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.kelas.index') }}">Data Kelas</a>
        <span>/</span>
        <span class="current">Tambah Kelas</span>
    </div>

    <div class="info-box">
        <h6><i class="fas fa-lightbulb"></i> Panduan Penamaan Kelas</h6>
        <p>
            <strong>KB:</strong> KB1, KB2, KB3 (Kelompok Bermain)<br>
            <strong>TKA:</strong> TKA1, TKA2, TKA3 (Taman Kanak-Kanak A)<br>
            <strong>TKB:</strong> TKB1, TKB2, TKB3 (Taman Kanak-Kanak B)<br>
            <strong>SD:</strong> 1A, 1B, 2A, 2B, ... 6A, 6B<br>
            <strong>SMP:</strong> 7A, 7B, 8A, 8B, 9A, 9B<br>
            <strong>SMA:</strong> 10A, 10B, 11A, 11B, 12A, 12B
        </p>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-chalkboard"></i> Form Tambah Kelas</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.kelas.store') }}" method="POST" id="kelasForm">
                @csrf

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Informasi Dasar
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tahun_ajaran_id">Tahun Ajaran <span class="required">*</span></label>
                            <select class="form-control @error('tahun_ajaran_id') is-invalid @enderror"
                                    id="tahun_ajaran_id" name="tahun_ajaran_id" required>
                                <option value="">Pilih Tahun Ajaran</option>
                                @foreach($tahunAjarans as $ta)
                                    <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}
                                        data-tahun="{{ date('Y', strtotime($ta->tanggal_mulai)) }}">
                                        {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tahun_ajaran_id')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="cabang_id">Cabang <span class="required">*</span></label>
                            <select class="form-control @error('cabang_id') is-invalid @enderror"
                                    id="cabang_id" name="cabang_id" required>
                                <option value="">Pilih Cabang</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}" {{ old('cabang_id') == $c->id ? 'selected' : '' }}
                                        data-kode="{{ $c->kode_cabang }}">
                                        {{ $c->nama_cabang }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cabang_id')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="jenjang">Jenjang <span class="required">*</span></label>
                            <select class="form-control @error('jenjang') is-invalid @enderror"
                                    id="jenjang" name="jenjang" required>
                                <option value="">Pilih Jenjang</option>
                                @foreach($jenjangs as $j)
                                    <option value="{{ $j }}" {{ old('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                            @error('jenjang')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nama_kelas">Nama Kelas <span class="required">*</span></label>
                            <input type="text"
                                   class="form-control @error('nama_kelas') is-invalid @enderror"
                                   id="nama_kelas"
                                   name="nama_kelas"
                                   value="{{ old('nama_kelas') }}"
                                   placeholder="Contoh: 7A, KB1, SMP A"
                                   required>
                            <div class="form-hint">Gunakan penamaan sesuai jenjang</div>
                            @error('nama_kelas')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror

                            <div class="nama-kelas-suggestions" id="namaKelasSuggestions"></div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-cog"></i> Pengaturan Lainnya
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="kuota_siswa">Kuota Siswa <span class="required">*</span></label>
                            <input type="number"
                                   class="form-control @error('kuota_siswa') is-invalid @enderror"
                                   id="kuota_siswa"
                                   name="kuota_siswa"
                                   value="{{ old('kuota_siswa', 30) }}"
                                   min="1"
                                   max="100"
                                   required>
                            <div class="form-hint">Maksimal siswa yang dapat ditampung (1-100)</div>
                            @error('kuota_siswa')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="wali_kelas_id">Wali Kelas</label>
                            <input type="hidden" id="wali_kelas_id" name="wali_kelas_id" value="{{ old('wali_kelas_id') }}">
                            <div class="wali-kelas-display" data-open-wali-modal>
                                <div class="wali-placeholder">
                                    <i class="fas fa-user-plus"></i> Klik untuk memilih wali kelas
                                </div>
                            </div>
                            <div class="form-hint">Klik untuk memilih atau mengubah wali kelas</div>
                            @error('wali_kelas_id')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="preview-card">
                    <h6><i class="fas fa-eye"></i> Preview Kode Kelas</h6>
                    <div class="preview-kode" id="previewKode">-</div>
                    <p class="preview-note">
                        Kode kelas akan dibuat otomatis berdasarkan cabang, jenjang, nama kelas, dan tahun ajaran.
                    </p>
                </div>

                <div class="form-actions">
                    <div class="form-actions-left">
                        <a href="{{ route('admin.kelas.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                    <div class="form-actions-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Kelas
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade kelas-modal" id="waliKelasModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content kelas-modal-content">
            <div class="modal-header kelas-modal-header">
                <h5 class="modal-title kelas-modal-title">
                    <i class="fas fa-user-tie"></i>
                    Pilih Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body kelas-modal-body">
                <div class="mb-3">
                    <label class="form-label kelas-modal-label">
                        <i class="fas fa-search"></i>
                        Cari Wali Kelas
                    </label>
                    <input type="text" id="searchWaliKelas" class="form-control" placeholder="Ketik nama wali kelas...">
                </div>

                <div class="mb-3">
                    <label class="form-label kelas-modal-label">Pilih Wali Kelas</label>
                    <div id="waliKelasList" class="wali-kelas-list">
                        @foreach($waliKelasOptions as $wk)
                            @php
                                $assignedKelasList = $wk->waliKelasAssignments ?? collect();
                                $hasAssignments = $assignedKelasList->count() > 0;
                            @endphp
                            <div class="wali-option-item"
                                 data-id="{{ $wk->id }}"
                                 data-name="{{ strtolower($wk->nama_lengkap) }}"
                                 data-display-name="{{ $wk->nama_lengkap }}"
                                 data-cabang-name="{{ $wk->user->cabang->nama_cabang ?? '-' }}">
                                <div class="wali-avatar">
                                    {{ substr($wk->nama_lengkap, 0, 2) }}
                                </div>
                                <div class="wali-option-meta">
                                    <div class="wali-name">{{ $wk->nama_lengkap }}</div>
                                    <div class="wali-role">{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                    @if($hasAssignments)
                                        <div class="assigned-kelas-list">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                            @foreach($assignedKelasList as $assignment)
                                                <span class="assigned-kelas-badge">{{ $assignment->kelas->nama_kelas }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="modal-note">
                    <div class="modal-note-content">
                        <i class="fas fa-info-circle"></i>
                        <div class="modal-note-text">
                            <strong>Multi-Kelas:</strong> Satu wali kelas bisa ditugaskan ke lebih dari satu kelas.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer kelas-modal-footer">
                <button type="button" id="btnRemoveWaliKelas" class="btn btn-remove-wali">
                    <i class="fas fa-times"></i> Hapus Wali Kelas
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade kelas-modal" id="confirmRemoveWaliModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content kelas-modal-content">
            <div class="modal-header kelas-modal-header is-danger">
                <h5 class="modal-title kelas-modal-title is-danger">
                    <i class="fas fa-exclamation-triangle"></i>
                    Konfirmasi Hapus Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body kelas-modal-body">
                <p class="remove-confirm-text">
                    Apakah Anda yakin ingin menghapus wali kelas untuk kelas baru ini?
                </p>
                <div class="modal-note is-warning">
                    <div class="modal-note-content">
                        <i class="fas fa-info-circle"></i>
                        <div class="modal-note-text">
                            Kelas akan dibuat tanpa wali kelas. Anda dapat menambahkannya nanti.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer kelas-modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Batal
                </button>
                <button type="button" id="confirmRemoveBtn" class="btn btn-danger-confirm">
                    <i class="fas fa-trash"></i> Ya, Hapus Wali Kelas
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/kelas/form.js'])
@endsection
