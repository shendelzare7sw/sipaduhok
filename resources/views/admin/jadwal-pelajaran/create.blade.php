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
                        <label class="form-label">Guru Pengajar <small class="text-muted">(Opsional - bisa diisi kemudian)</small></label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}" {{ old('guru_id') == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Kosongkan jika guru belum ditentukan saat rapat</small>
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
</script>
@endsection
