@extends('layouts.sneat')

@section('title', 'Edit Kelas')

@section('page-title', 'Edit Kelas')
@section('page-subtitle')
Perbarui data kelas {{ $kelas->nama_kelas }}
@endsection

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    border: none;
    overflow: hidden;
}

.card-header {
    padding: 24px 28px;
    border-bottom: 1px solid #e5e7eb;
    background: linear-gradient(135deg, #f8fafc 0%, #fff 100%);
}

.card-header h5 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 12px;
}

.card-header h5 i {
    color: #f59e0b;
    font-size: 24px;
}

.card-body {
    padding: 28px;
}

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

.breadcrumb span {
    color: #9ca3af;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

.current-data-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
    border: 1px solid #fcd34d;
    border-radius: 50px;
    font-size: 13px;
    color: #92400e;
    margin-bottom: 24px;
}

.stats-summary {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}

@media (max-width: 768px) {
    .stats-summary {
        grid-template-columns: 1fr;
    }
}

.stats-item {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 16px;
    text-align: center;
}

.stats-item .stats-number {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
}

.stats-item .stats-label {
    font-size: 12px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stats-item.has-data .stats-number {
    color: #3b82f6;
}

.form-section {
    margin-bottom: 32px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 20px;
    padding-bottom: 12px;
    border-bottom: 2px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-section-title i {
    color: #f59e0b;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
}

.form-group {
    margin-bottom: 0;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    margin-bottom: 8px;
}

.form-group label .required {
    color: #ef4444;
    margin-left: 2px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #d1d5db;
    border-radius: 10px;
    font-size: 14px;
    color: #111827;
    background: #fff;
    transition: all 0.2s;
}

.form-control:focus {
    outline: none;
    border-color: #f59e0b;
    box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
}

.form-control.is-invalid {
    border-color: #ef4444;
}

.form-hint {
    font-size: 12px;
    color: #6b7280;
    margin-top: 6px;
}

.invalid-feedback {
    font-size: 13px;
    color: #ef4444;
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.preview-card {
    background: linear-gradient(135deg, #fafafa 0%, #f3f4f6 100%);
    border: 2px dashed #d1d5db;
    border-radius: 12px;
    padding: 24px;
    margin-top: 24px;
}

.preview-card h6 {
    font-size: 14px;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.preview-kode {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 18px;
    font-weight: 600;
    color: #f59e0b;
    background: #fffbeb;
    padding: 12px 20px;
    border-radius: 8px;
    display: inline-block;
}

.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 12px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
    margin-top: 32px;
}

.form-actions-left, .form-actions-right {
    display: flex;
    gap: 12px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 10px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-primary {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(245, 158, 11, 0.4);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
    transform: translateY(-1px);
}
</style>

<div style="max-width: 900px; margin: 0 auto;">
    <div class="breadcrumb">
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
                                <option value="">Pilih Cabang</option>
                                @foreach($cabangs as $c)
                                    <option value="{{ $c->id }}" 
                                        {{ old('cabang_id', $kelas->cabang_id) == $c->id ? 'selected' : '' }}
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
                            <div class="wali-kelas-display" onclick="openWaliKelasModal()" style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 10px; background: white; transition: all 0.2s;">
                                @if($kelas->waliKelas)
                                    <div class="wali-info" style="display: flex; align-items: center; gap: 12px;">
                                        <div class="wali-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                                            {{ substr($kelas->waliKelas->nama_lengkap, 0, 2) }}
                                        </div>
                                        <div class="wali-details">
                                            <div class="wali-name" style="font-weight: 600; color: #111827;">{{ $kelas->waliKelas->nama_lengkap }}</div>
                                            <div class="wali-role" style="font-size: 12px; color: #6b7280;">{{ $kelas->waliKelas->user->cabang->nama_cabang ?? '-' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div style="color: #f59e0b; font-style: italic;">
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
                    <div class="preview-kode" id="previewKode">{{ $kelas->kode_kelas }}</div>
                    <p style="margin-top: 12px; font-size: 13px; color: #6b7280;">
                        Kode kelas akan diperbarui jika ada perubahan pada cabang, jenjang, nama kelas, atau tahun ajaran.
                    </p>
                </div>

                <div class="form-actions">
                    <div class="form-actions-left">
                        <a href="{{ route('waka.kelas.index') }}" class="btn btn-secondary">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const cabangSelect = document.getElementById('cabang_id');
    const tahunAjaranSelect = document.getElementById('tahun_ajaran_id');
    const jenjangSelect = document.getElementById('jenjang');
    const namaKelasInput = document.getElementById('nama_kelas');
    const previewKode = document.getElementById('previewKode');

    function updatePreview() {
        const cabangOption = cabangSelect.options[cabangSelect.selectedIndex];
        const tahunOption = tahunAjaranSelect.options[tahunAjaranSelect.selectedIndex];
        const jenjang = jenjangSelect.value;
        const namaKelas = namaKelasInput.value;

        if (cabangOption && cabangOption.dataset.kode && jenjang && namaKelas && tahunOption && tahunOption.dataset.tahun) {
            const kode = cabangOption.dataset.kode + '-' + jenjang + '-' + 
                        namaKelas.toUpperCase().replace(/\s+/g, '') + '-' + tahunOption.dataset.tahun;
            previewKode.textContent = kode;
        }
    }

    cabangSelect.addEventListener('change', updatePreview);
    tahunAjaranSelect.addEventListener('change', updatePreview);
    jenjangSelect.addEventListener('change', updatePreview);
    namaKelasInput.addEventListener('input', updatePreview);
});
</script>

{{-- Modal Pilih Wali Kelas --}}
<div class="modal fade" id="waliKelasModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px;">
                <h5 class="modal-title" style="font-weight: 600; color: #111827;">
                    <i class="fas fa-user-tie" style="color: #8b5cf6; margin-right: 10px;"></i>
                    Pilih Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #374151;">
                        <i class="fas fa-search" style="color: #9ca3af; margin-right: 6px;"></i>
                        Cari Wali Kelas
                    </label>
                    <input type="text" id="searchWaliKelas" class="form-control" placeholder="Ketik nama wali kelas..." style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; margin-bottom: 12px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #374151;">Pilih Wali Kelas</label>
                    <div id="waliKelasList" style="max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                        @foreach($waliKelasOptions as $wk)
                            @php
                                $assignedKelasList = $wk->waliKelasAssignments->filter(function($a) use ($kelas) {
                                    return $a->kelas_id != $kelas->id;
                                }) ?? collect();
                                $hasAssignments = $assignedKelasList->count() > 0;
                            @endphp
                            <div class="wali-option-item"
                                 data-id="{{ $wk->id }}"
                                 data-name="{{ strtolower($wk->nama_lengkap) }}"
                                 onclick="selectWaliKelas({{ $wk->id }}, '{{ $wk->nama_lengkap }}', '{{ $wk->user->cabang->nama_cabang ?? '-' }}')"
                                 style="padding: 12px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; border: 1px solid transparent;">
                                <div class="wali-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                    {{ substr($wk->nama_lengkap, 0, 2) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #111827;">{{ $wk->nama_lengkap }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                    @if($hasAssignments)
                                        <div style="font-size: 11px; color: #6366f1; margin-top: 4px; display: flex; flex-wrap: wrap; gap: 4px;">
                                            <i class="fas fa-chalkboard-teacher"></i>
                                            @foreach($assignedKelasList as $assignment)
                                                <span style="background: #e0e7ff; padding: 2px 6px; border-radius: 4px;">{{ $assignment->kelas->nama_kelas }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="padding: 12px; background: #e0f2fe; border-radius: 8px; border-left: 4px solid #0284c7;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #0284c7; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #0369a1;">
                            <strong>Multi-Kelas:</strong> Satu wali kelas bisa ditugaskan ke lebih dari satu kelas.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                <button type="button" id="btnRemoveWaliKelas" class="btn" style="background: #fef2f2; color: #dc2626; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-times"></i> Hapus Wali Kelas
                </button>
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus Wali Kelas --}}
<div class="modal fade" id="confirmRemoveWaliModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px; background: #fef2f2;">
                <h5 class="modal-title" style="font-weight: 600; color: #dc2626;">
                    <i class="fas fa-exclamation-triangle" style="margin-right: 10px;"></i>
                    Konfirmasi Hapus Wali Kelas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="color: #374151; margin-bottom: 16px;">
                    Apakah Anda yakin ingin menghapus wali kelas dari kelas <strong>{{ $kelas->nama_kelas }}</strong>?
                </p>
                <div style="padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #f59e0b; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #92400e;">
                            Tindakan ini akan menghapus penugasan wali kelas. Kelas akan menjadi "Belum ada wali kelas".
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    Batal
                </button>
                <button type="button" id="confirmRemoveBtn" class="btn" style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-trash"></i> Ya, Hapus Wali Kelas
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Modal konfirmasi pindah dihapus - sekarang dukung multi-kelas --}}

<script>
function openWaliKelasModal() {
    const modal = new bootstrap.Modal(document.getElementById('waliKelasModal'));
    modal.show();
}

document.getElementById('searchWaliKelas').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const waliOptions = document.querySelectorAll('.wali-option-item');
    waliOptions.forEach(option => {
        const name = option.getAttribute('data-name');
        option.style.display = name.includes(searchTerm) ? 'flex' : 'none';
    });
});

document.querySelectorAll('.wali-option-item').forEach(option => {
    option.addEventListener('mouseenter', function() {
        this.style.background = '#f9fafb';
        this.style.borderColor = '#d1d5db';
    });
    option.addEventListener('mouseleave', function() {
        this.style.background = 'transparent';
        this.style.borderColor = 'transparent';
    });
});

function selectWaliKelas(id, name, cabang) {
    setWaliKelas(id, name, cabang);
    const modal = bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'));
    if (modal) modal.hide();
}

function setWaliKelas(id, name, cabang) {
    document.getElementById('wali_kelas_id').value = id;
    const display = document.querySelector('.wali-kelas-display');
    display.innerHTML = `
        <div class="wali-info" style="display: flex; align-items: center; gap: 12px;">
            <div class="wali-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                ${name.substring(0, 2)}
            </div>
            <div class="wali-details">
                <div class="wali-name" style="font-weight: 600; color: #111827;">${name}</div>
                <div class="wali-role" style="font-size: 12px; color: #6b7280;">${cabang}</div>
            </div>
        </div>
    `;
}

document.getElementById('btnRemoveWaliKelas').addEventListener('click', function() {
    const waliModal = bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'));
    if (waliModal) waliModal.hide();
    setTimeout(function() {
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmRemoveWaliModal'));
        confirmModal.show();
    }, 300);
});

document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
    document.getElementById('wali_kelas_id').value = '';
    const display = document.querySelector('.wali-kelas-display');
    display.innerHTML = `
        <div style="color: #f59e0b; font-style: italic;">
            <i class="fas fa-user-plus"></i> Klik untuk memilih wali kelas
        </div>
    `;
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmRemoveWaliModal'));
    if (confirmModal) confirmModal.hide();
});
</script>

<style>
#waliKelasList::-webkit-scrollbar {
    width: 8px;
}

#waliKelasList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#waliKelasList::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

#waliKelasList::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.wali-kelas-display:hover {
    border-color: #8b5cf6 !important;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
</style>
@endsection
