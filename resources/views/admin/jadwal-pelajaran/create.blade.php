@extends('layouts.sneat')

@section('title', 'Tambah Jadwal Pelajaran')
@section('page-title', 'Tambah Jadwal Pelajaran')
@section('page-subtitle', 'Buat jadwal pelajaran baru')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
.form-section {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.form-section-title {
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 16px;
    color: #111827;
    border-left: 4px solid #3b82f6;
    padding-left: 12px;
}

.time-input-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.conflict-warning {
    background: #fef3c7;
    border-left: 4px solid #f59e0b;
    padding: 12px;
    border-radius: 6px;
    margin-top: 12px;
    display: none;
}

.conflict-warning i {
    color: #f59e0b;
}
</style>
@endsection

@section('content')
{{-- Error Messages --}}
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-calendar-plus text-primary me-2"></i>Form Tambah Jadwal Pelajaran
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.jadwal-pelajaran.store') }}" method="POST" id="jadwalForm">
            @csrf

            {{-- Tahun Ajaran & Kelas Section --}}
            <div class="form-section">
                <div class="form-section-title">Informasi Dasar</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahun_ajaran_id" id="tahunAjaranSelect" class="form-select" required>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ old('tahun_ajaran_id', $currentTahunAjaran?->id) == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="kelasSelect" class="form-select" required>
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" data-jenjang="{{ $kls->jenjang }}" {{ old('kelas_id') == $kls->id ? 'selected' : '' }}>
                                    {{ $kls->nama_kelas }} - {{ $kls->cabang->nama_cabang }} ({{ $kls->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Mata Pelajaran & Guru Section --}}
            <div class="form-section">
                <div class="form-section-title">Penugasan Mengajar</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mata Pelajaran <span class="text-danger">*</span></label>
                        <select name="mata_pelajaran_id" id="mapelSelect" class="form-select" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}" data-jenjang="{{ $mapel->jenjang }}" {{ old('mata_pelajaran_id') == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }} ({{ $mapel->jenjang }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Mata pelajaran akan difilter otomatis sesuai jenjang kelas</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guru Pengajar <small class="text-muted">(Opsional)</small></label>
                        <input type="hidden" id="guru_id" name="guru_id" value="{{ old('guru_id') }}">
                        <div class="guru-display" onclick="openGuruModal()" style="cursor: pointer; padding: 12px 16px; border: 1px solid #d1d5db; border-radius: 10px; background: white; transition: all 0.2s;">
                            <div style="color: #f59e0b; font-style: italic;">
                                <i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar
                            </div>
                        </div>
                        <small class="text-muted">Guru bisa mengajar di banyak kelas. Kosongkan jika belum ditentukan.</small>
                    </div>
                </div>
            </div>

            {{-- Waktu Section --}}
            <div class="form-section">
                <div class="form-section-title">Jadwal Waktu</div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">Hari <span class="text-danger">*</span></label>
                        <select name="hari" class="form-select" required>
                            <option value="">-- Pilih Hari --</option>
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}" {{ old('hari') == $hari ? 'selected' : '' }}>{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', '07:00') }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', '08:30') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan <small class="text-muted">(Opsional)</small></label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan untuk jadwal ini">{{ old('keterangan') }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.jadwal-pelajaran.index', request()->query()) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const kelasSelect = document.getElementById('kelasSelect');
    const mapelSelect = document.getElementById('mapelSelect');

    // Filter mata pelajaran berdasarkan jenjang kelas
    kelasSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const jenjang = selectedOption.getAttribute('data-jenjang');

        // Reset mapel selection
        mapelSelect.value = '';

        // Filter options
        Array.from(mapelSelect.options).forEach(option => {
            if (option.value === '') {
                option.style.display = 'block';
                return;
            }

            const mapelJenjang = option.getAttribute('data-jenjang');
            if (jenjang && mapelJenjang !== jenjang) {
                option.style.display = 'none';
            } else {
                option.style.display = 'block';
            }
        });
    });

    // Trigger filter on page load if kelas is already selected
    if (kelasSelect.value) {
        kelasSelect.dispatchEvent(new Event('change'));
    }
});

// Guru Modal Functions
let selectedGuruId = null;
let selectedGuruName = '';
let selectedGuruCabang = '';

function openGuruModal() {
    const modal = new bootstrap.Modal(document.getElementById('guruModal'));
    modal.show();
}

// Search functionality
document.getElementById('searchGuru').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const guruOptions = document.querySelectorAll('.guru-option-item');

    guruOptions.forEach(option => {
        const name = option.getAttribute('data-name');
        if (name.includes(searchTerm)) {
            option.style.display = 'flex';
        } else {
            option.style.display = 'none';
        }
    });
});

// Hover effect
document.querySelectorAll('.guru-option-item').forEach(option => {
    option.addEventListener('mouseenter', function() {
        this.style.background = '#f9fafb';
        this.style.borderColor = '#d1d5db';
    });
    option.addEventListener('mouseleave', function() {
        this.style.background = 'transparent';
        this.style.borderColor = 'transparent';
    });
});

function selectGuru(id, name, cabang) {
    setGuru(id, name, cabang);

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('guruModal'));
    if (modal) modal.hide();
}

function setGuru(id, name, cabang) {
    document.getElementById('guru_id').value = id;

    // Update display
    const display = document.querySelector('.guru-display');
    display.innerHTML = `
        <div class="guru-info" style="display: flex; align-items: center; gap: 12px;">
            <div class="guru-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px;">
                ${name.substring(0, 2)}
            </div>
            <div class="guru-details">
                <div class="guru-name" style="font-weight: 600; color: #111827;">${name}</div>
                <div class="guru-role" style="font-size: 12px; color: #6b7280;">${cabang}</div>
            </div>
        </div>
    `;
}

// Remove guru button
document.getElementById('btnRemoveGuru').addEventListener('click', function() {
    document.getElementById('guru_id').value = '';

    // Update display
    const display = document.querySelector('.guru-display');
    display.innerHTML = `
        <div style="color: #f59e0b; font-style: italic;">
            <i class="fas fa-chalkboard-teacher"></i> Klik untuk memilih guru pengajar
        </div>
    `;

    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('guruModal'));
    if (modal) modal.hide();
});
</script>

{{-- Modal Pilih Guru --}}
<div class="modal fade" id="guruModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header" style="border-bottom: 1px solid #e5e7eb; padding: 24px;">
                <h5 class="modal-title" style="font-weight: 600; color: #111827;">
                    <i class="fas fa-chalkboard-teacher" style="color: #10b981; margin-right: 10px;"></i>
                    Pilih Guru Pengajar
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #374151;">
                        <i class="fas fa-search" style="color: #9ca3af; margin-right: 6px;"></i>
                        Cari Guru
                    </label>
                    <input type="text" id="searchGuru" class="form-control" placeholder="Ketik nama guru..." style="border: 1px solid #d1d5db; border-radius: 8px; padding: 10px 16px; margin-bottom: 12px;">
                </div>

                <div class="mb-3">
                    <label class="form-label" style="font-weight: 600; color: #374151;">Pilih Guru</label>
                    <div id="guruList" style="max-height: 350px; overflow-y: auto; border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px;">
                        @foreach($guruList as $g)
                            <div class="guru-option-item"
                                 data-id="{{ $g->id }}"
                                 data-name="{{ strtolower($g->nama_lengkap) }}"
                                 onclick="selectGuru({{ $g->id }}, '{{ $g->nama_lengkap }}', '{{ $g->user->cabang->nama_cabang ?? '-' }}')"
                                 style="padding: 12px; border-radius: 8px; margin-bottom: 4px; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 12px; border: 1px solid transparent;">
                                <div class="guru-avatar" style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #059669); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 14px; flex-shrink: 0;">
                                    {{ substr($g->nama_lengkap, 0, 2) }}
                                </div>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #111827;">{{ $g->nama_lengkap }}</div>
                                    <div style="font-size: 12px; color: #6b7280;">{{ $g->user->cabang->nama_cabang ?? '-' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div style="padding: 12px; background: #dbeafe; border-radius: 8px; border-left: 4px solid #3b82f6;">
                    <div style="display: flex; gap: 8px; align-items: start;">
                        <i class="fas fa-info-circle" style="color: #3b82f6; margin-top: 2px;"></i>
                        <div style="font-size: 13px; color: #1e40af;">
                            Guru bisa mengajar di banyak kelas. Untuk menghapus guru, klik tombol "Hapus Guru" di bawah.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e5e7eb; padding: 20px 24px; gap: 12px;">
                <button type="button" id="btnRemoveGuru" class="btn" style="background: #fef2f2; color: #dc2626; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-times"></i> Hapus Guru
                </button>
                <button type="button" class="btn" data-bs-dismiss="modal" style="background: #f3f4f6; color: #374151; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500;">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<style>
#guruList::-webkit-scrollbar {
    width: 8px;
}

#guruList::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#guruList::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 4px;
}

#guruList::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

.guru-display:hover {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}
</style>
@endsection
