@extends('layouts.app')

@section('title', 'Edit Kelas')

@section('page-title', 'Edit Kelas')
@section('page-subtitle')
Perbarui data kelas {{ $kelas->nama_kelas }}
@endsection


@section('styles')
    @vite(['resources/css/waka/kelas/form.css'])
@endsection

@section('content')
<div class="kelas-form-page is-edit">
    <div class="kelas-breadcrumb">
        <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('waka.kelas.index') }}">Data Kelas</a>
        <span>/</span>
        <span class="current">Edit: {{ $kelas->nama_kelas }}</span>
    </div>

    <div class="current-data-badge">
        <i class="fas fa-edit"></i>
        Mengedit kelas: <strong>{{ $kelas->kode_kelas }}</strong>
    </div>

    @php
        $siswaCount = \App\Models\Siswa::where('kelas_id', $kelas->id)->count();
    @endphp
    <div class="stats-summary">
        <div class="stats-item {{ $siswaCount > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $siswaCount }}</div>
            <div class="stats-label">Siswa Terdaftar</div>
        </div>
        <div class="stats-item">
            <div class="stats-number">{{ $kelas->kuota_siswa }}</div>
            <div class="stats-label">Kuota Maksimal</div>
        </div>
        <div class="stats-item {{ ($kelas->kuota_siswa - $siswaCount) > 0 ? 'has-data' : '' }}">
            <div class="stats-number">{{ $kelas->kuota_siswa - $siswaCount }}</div>
            <div class="stats-label">Sisa Kuota</div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-edit"></i> Form Edit Kelas</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('waka.kelas.update', $kelas) }}" method="POST" id="kelasForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="_return_url" value="{{ url()->previous(route('waka.kelas.index')) }}">

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
                                    <option value="{{ $ta->id }}"
                                        {{ old('tahun_ajaran_id', $kelas->tahun_ajaran_id) == $ta->id ? 'selected' : '' }}
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
                                <option value="{{ $kelas->cabang_id }}"
                                        data-kode="{{ $kelas->cabang->kode_cabang ?? '' }}"
                                        selected>
                                    {{ $kelas->cabang->nama_cabang ?? 'Cabang belum ditentukan' }}
                                </option>
                            </select>
                            <div class="form-hint">Cabang kelas dikunci dan tidak dapat dipindahkan oleh Waka.</div>
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
                                    <option value="{{ $j }}" {{ old('jenjang', $kelas->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
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
                                   value="{{ old('nama_kelas', $kelas->nama_kelas) }}"
                                   placeholder="Contoh: 7A, KB1, SMP A"
                                   required>
                            @error('nama_kelas')
                                <div class="invalid-feedback"><i class="fas fa-exclamation-circle"></i> {{ $message }}</div>
                            @enderror
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
                                   value="{{ old('kuota_siswa', $kelas->kuota_siswa) }}"
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
                            <input type="hidden" id="wali_kelas_id" name="wali_kelas_id" value="{{ old('wali_kelas_id', $kelas->wali_kelas_id) }}">
                            <div class="wali-kelas-display" data-open-wali-modal>
                                @if($kelas->waliKelas)
                                    <div class="wali-info">
                                        <div class="wali-avatar">
                                            {{ substr($kelas->waliKelas->nama_lengkap, 0, 2) }}
                                        </div>
                                        <div class="wali-details">
                                            <div class="wali-name">{{ $kelas->waliKelas->nama_lengkap }}</div>
                                            <div class="wali-role">{{ $kelas->waliKelas->user->cabang->nama_cabang ?? '-' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="wali-placeholder">
                                        <i class="fas fa-user-plus"></i> Klik untuk memilih wali kelas
                                    </div>
                                @endif
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
                    <div class="preview-kode" id="previewKode" data-keep-initial="true">{{ $kelas->kode_kelas }}</div>
                    <p class="preview-note">
                        Kode kelas akan diperbarui jika ada perubahan pada cabang, jenjang, nama kelas, atau tahun ajaran.
                    </p>
                </div>

                <div class="form-actions">
                    <div class="form-actions-left">
                        <a href="{{ url()->previous(route('waka.kelas.index')) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali
                        </a>
                        <a href="{{ route('waka.kelas.show', $kelas) }}" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Lihat Detail
                        </a>
                    </div>
                    <div class="form-actions-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Simpan Perubahan
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
                                $assignedKelasList = ($wk->waliKelasAssignments ?? collect())->filter(function ($assignment) use ($kelas) {
                                    return $assignment->kelas_id != $kelas->id;
                                });
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
                    Apakah Anda yakin ingin menghapus wali kelas dari kelas <strong>{{ $kelas->nama_kelas }}</strong>?
                </p>
                <div class="modal-note is-warning">
                    <div class="modal-note-content">
                        <i class="fas fa-info-circle"></i>
                        <div class="modal-note-text">
                            Tindakan ini akan menghapus penugasan wali kelas. Kelas akan menjadi "Belum ada wali kelas".
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
    @vite(['resources/js/waka/kelas/form.js'])
@endsection
