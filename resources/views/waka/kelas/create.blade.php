@extends('layouts.sneat')

@section('title', 'Tambah Kelas')

@section('page-title', 'Tambah Kelas Baru')
@section('page-subtitle', 'Buat kelas baru untuk tahun ajaran')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Card Styles */
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
    color: #3b82f6;
    font-size: 24px;
}

.card-body {
    padding: 28px;
}

/* Breadcrumb */
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

/* Info Box */
.info-box {
    padding: 16px 20px;
    background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 100%);
    border-left: 4px solid #3b82f6;
    border-radius: 0 10px 10px 0;
    margin-bottom: 24px;
}

.info-box h6 {
    font-size: 14px;
    font-weight: 600;
    color: #1e40af;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-box p {
    font-size: 13px;
    color: #3b82f6;
    margin: 0;
    line-height: 1.6;
}

/* Form Styles */
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
    color: #3b82f6;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
    margin-bottom: 20px;
}

.form-row.single {
    grid-template-columns: 1fr;
}

.form-row.three {
    grid-template-columns: repeat(3, 1fr);
}

@media (max-width: 768px) {
    .form-row, .form-row.three {
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
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
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

/* Nama Kelas Suggestions */
.nama-kelas-suggestions {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 10px;
}

.nama-kelas-suggestion {
    padding: 6px 14px;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    font-size: 13px;
    color: #4b5563;
    cursor: pointer;
    transition: all 0.2s;
}

.nama-kelas-suggestion:hover {
    background: #e0f2fe;
    border-color: #3b82f6;
    color: #1d4ed8;
}

/* Preview */
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
    color: #3b82f6;
    background: #eff6ff;
    padding: 12px 20px;
    border-radius: 8px;
    display: inline-block;
}

/* Buttons */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    padding-top: 24px;
    border-top: 1px solid #e5e7eb;
    margin-top: 32px;
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
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    box-shadow: 0 4px 14px rgba(59, 130, 246, 0.4);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
}
</style>

<div style="max-width: 900px; margin: 0 auto;">
    {{-- Breadcrumb --}}
    <div class="breadcrumb">
        <a href="{{ route('waka.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('waka.kelas.index') }}">Data Kelas</a>
        <span>/</span>
        <span class="current">Tambah Kelas</span>
    </div>

    {{-- Info Box --}}
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

    {{-- Form Card --}}
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-chalkboard"></i> Form Tambah Kelas</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('waka.kelas.store') }}" method="POST" id="kelasForm">
                @csrf

                {{-- Basic Information --}}
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
                            
                            {{-- Dynamic Suggestions --}}
                            <div class="nama-kelas-suggestions" id="namaKelasSuggestions"></div>
                        </div>
                    </div>
                </div>

                {{-- Additional Settings --}}
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
                            <div class="wali-kelas-display" onclick="openWaliKelasModal()" style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 10px; background: white; transition: all 0.2s;">
                                <div style="color: #f59e0b; font-style: italic;">
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

                {{-- Preview --}}
                <div class="preview-card">
                    <h6><i class="fas fa-eye"></i> Preview Kode Kelas</h6>
                    <div class="preview-kode" id="previewKode">-</div>
                    <p style="margin-top: 12px; font-size: 13px; color: #6b7280;">
                        Kode kelas akan dibuat otomatis berdasarkan cabang, jenjang, nama kelas, dan tahun ajaran.
                    </p>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('waka.kelas.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Simpan Kelas
                    </button>
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
    const suggestionsContainer = document.getElementById('namaKelasSuggestions');

    // Suggestions based on jenjang
    const suggestions = {
        'KB': ['KB1', 'KB2', 'KB3'],
        'TKA': ['TKA1', 'TKA2', 'TKA3'],
        'TKB': ['TKB1', 'TKB2', 'TKB3'],
        'SD': ['1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'],
        'SMP': ['7A', '7B', '8A', '8B', '9A', '9B'],
        'SMA': ['10A', '10B', '11A', '11B', '12A', '12B']
    };

    function updatePreview() {
        const cabangOption = cabangSelect.options[cabangSelect.selectedIndex];
        const tahunOption = tahunAjaranSelect.options[tahunAjaranSelect.selectedIndex];
        const jenjang = jenjangSelect.value;
        const namaKelas = namaKelasInput.value;

        if (cabangOption && cabangOption.dataset.kode && jenjang && namaKelas && tahunOption && tahunOption.dataset.tahun) {
            const kode = cabangOption.dataset.kode + '-' + jenjang + '-' + 
                        namaKelas.toUpperCase().replace(/\s+/g, '') + '-' + tahunOption.dataset.tahun;
            previewKode.textContent = kode;
        } else {
            previewKode.textContent = '-';
        }
    }

    function updateSuggestions() {
        const jenjang = jenjangSelect.value;
        suggestionsContainer.innerHTML = '';
        
        if (jenjang && suggestions[jenjang]) {
            suggestions[jenjang].forEach(function(s) {
                const btn = document.createElement('span');
                btn.className = 'nama-kelas-suggestion';
                btn.textContent = s;
                btn.onclick = function() {
                    namaKelasInput.value = s;
                    updatePreview();
                };
                suggestionsContainer.appendChild(btn);
            });
        }
    }

    cabangSelect.addEventListener('change', updatePreview);
    tahunAjaranSelect.addEventListener('change', updatePreview);
    jenjangSelect.addEventListener('change', function() {
        updateSuggestions();
        updatePreview();
    });
    namaKelasInput.addEventListener('input', updatePreview);

    // Initial
    updateSuggestions();
    updatePreview();
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
                                $assignedKelas = \App\Models\Kelas::where('wali_kelas_id', $wk->id)
                                    ->with(['cabang', 'tahunAjaran'])
                                    ->first();
                                $hasAssignment = $assignedKelas !== null;
                            @endphp
                            <div class="wali-option-item {{ $hasAssignment ? 'has-assignment' : '' }}"
                                 data-id="{{ $wk->id }}"
                                 data-name="{{ strtolower($wk->nama_lengkap) }}"
                                 data-has-assignment="{{ $hasAssignment ? 'true' : 'false' }}"
                                 data-assigned-kelas="{{ $hasAssignment ? $assignedKelas->nama_kelas : '' }}"
                                 data-assigned-kelas-full="{{ $hasAssignment ? $assignedKelas->nama_kelas . ' (' . $assignedKelas->jenjang . ') - ' . $assignedKelas->cabang->nama_cabang : '' }}"
                                 onclick="selectWaliKelas({{ $wk->id }}, '{{ $wk->nama_lengkap }}', '{{ $wk->user->cabang->nama_cabang ?? '-' }}', {{ $hasAssignment ? 'true' : 'false' }}, '{{ $hasAssignment ? $assignedKelas->nama_kelas . ' (' . $assignedKelas->jenjang . ') - ' . $assignedKelas->cabang->nama_cabang : '' }}')"
                                 style="padding: 12px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; {{ $hasAssignment ? 'background: #fef3c7; border: 1px solid #f59e0b;' : 'border: 1px solid transparent;' }}">
                                <div class="wali-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, {{ $hasAssignment ? '#f59e0b, #d97706' : '#8b5cf6, #7c3aed' }}); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                    {{ substr($wk->nama_lengkap, 0, 2) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #111827;">{{ $wk->nama_lengkap }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $wk->user->cabang->nama_cabang ?? '-' }}</div>
                                    @if($hasAssignment)
                                        <div style="font-size: 11px; color: #92400e; margin-top: 4px; display: flex; align-items: center; gap: 4px;">
                                            <i class="fas fa-info-circle"></i>
                                            <span>Sudah mengajar: <strong>{{ $assignedKelas->nama_kelas }}</strong> ({{ $assignedKelas->jenjang }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #f59e0b; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #92400e;">
                            Untuk menghapus wali kelas, klik tombol "Hapus Wali Kelas" di bawah.
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
                    Apakah Anda yakin ingin menghapus wali kelas untuk kelas baru ini?
                </p>
                <div style="padding: 12px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #f59e0b; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #92400e;">
                            Kelas akan dibuat tanpa wali kelas. Anda dapat menambahkannya nanti.
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

{{-- Modal Konfirmasi Pindah Wali Kelas --}}
<div class="modal fade" id="confirmReassignWaliModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 20px 24px; background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%);">
                <h5 class="modal-title" style="font-weight: 600; color: #9a3412; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-exclamation-triangle" style="color: #f59e0b;"></i>
                    PERINGATAN!
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="color: #374151; margin-bottom: 12px; font-weight: 500;">
                    Wali kelas ini sudah mengajar kelas lain:
                </p>
                <div style="padding: 16px; background: #fef3c7; border-radius: 8px; margin-bottom: 20px; border: 2px solid #fbbf24;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <i class="fas fa-chalkboard-teacher" style="color: #f59e0b; font-size: 24px;"></i>
                        <div>
                            <div style="font-weight: 600; color: #92400e; font-size: 15px;" id="reassignOldKelasInfo"></div>
                        </div>
                    </div>
                </div>

                <div style="padding: 16px; background: #f0fdf4; border-radius: 8px; border-left: 4px solid #22c55e; margin-bottom: 16px;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-arrow-right" style="color: #16a34a; margin-top: 2px; font-size: 16px;"></i>
                        <div>
                            <div style="font-size: 13px; color: #166534; margin-bottom: 4px;">
                                <strong>Jika Anda melanjutkan:</strong>
                            </div>
                            <ul style="margin: 0; padding-left: 20px; color: #166534; font-size: 13px;">
                                <li>Assignment lama akan <strong>OTOMATIS DIHAPUS</strong></li>
                                <li>Wali kelas akan dipindahkan ke kelas baru ini</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <p style="color: #374151; font-weight: 500; margin-bottom: 0;">
                    Apakah Anda yakin ingin melanjutkan?
                </p>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" id="confirmReassignWaliBtn" class="btn" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: white; border: none; padding: 10px 24px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-exchange-alt"></i> Ya, Pindahkan Wali Kelas
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedWaliId = null;
let selectedWaliName = '';
let selectedWaliCabang = '';

function openWaliKelasModal() {
    const modal = new bootstrap.Modal(document.getElementById('waliKelasModal'));
    modal.show();
}

// Search functionality
document.getElementById('searchWaliKelas').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const waliOptions = document.querySelectorAll('.wali-option-item');

    waliOptions.forEach(option => {
        const name = option.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            option.style.display = 'flex';
        } else {
            option.style.display = 'none';
        }
    });
});

// Hover effect
document.querySelectorAll('.wali-option-item').forEach(option => {
    option.addEventListener('mouseenter', function() {
        const hasAssignment = this.classList.contains('has-assignment');
        if (!hasAssignment) {
            this.style.background = '#f9fafb';
            this.style.borderColor = '#d1d5db';
        }
    });
    option.addEventListener('mouseleave', function() {
        const hasAssignment = this.classList.contains('has-assignment');
        if (!hasAssignment) {
            this.style.background = 'transparent';
            this.style.borderColor = 'transparent';
        }
    });
});

function selectWaliKelas(id, name, cabang, hasAssignment, assignedKelasInfo) {
    if (hasAssignment) {
        // Show confirmation modal
        selectedWaliId = id;
        selectedWaliName = name;
        selectedWaliCabang = cabang;

        document.getElementById('reassignOldKelasInfo').textContent = assignedKelasInfo;

        // Close wali modal
        const waliModal = bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'));
        if (waliModal) waliModal.hide();

        // Show confirm modal
        setTimeout(function() {
            const confirmModal = new bootstrap.Modal(document.getElementById('confirmReassignWaliModal'));
            confirmModal.show();
        }, 300);
    } else {
        // Directly set wali kelas
        setWaliKelas(id, name, cabang);

        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'));
        if (modal) modal.hide();
    }
}

function setWaliKelas(id, name, cabang) {
    document.getElementById('wali_kelas_id').value = id;

    // Update display
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

// Confirm reassign button
document.getElementById('confirmReassignWaliBtn').addEventListener('click', function() {
    setWaliKelas(selectedWaliId, selectedWaliName, selectedWaliCabang);

    // Close confirm modal
    const confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmReassignWaliModal'));
    if (confirmModal) confirmModal.hide();
});

// Remove wali kelas button
document.getElementById('btnRemoveWaliKelas').addEventListener('click', function() {
    // Close wali modal
    const waliModal = bootstrap.Modal.getInstance(document.getElementById('waliKelasModal'));
    if (waliModal) waliModal.hide();

    // Show confirm remove modal
    setTimeout(function() {
        const confirmModal = new bootstrap.Modal(document.getElementById('confirmRemoveWaliModal'));
        confirmModal.show();
    }, 300);
});

// Confirm remove button
document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
    document.getElementById('wali_kelas_id').value = '';

    // Update display
    const display = document.querySelector('.wali-kelas-display');
    display.innerHTML = `
        <div style="color: #f59e0b; font-style: italic;">
            <i class="fas fa-user-plus"></i> Klik untuk memilih wali kelas
        </div>
    `;

    // Close modal
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
