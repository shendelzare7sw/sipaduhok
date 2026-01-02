@extends('layouts.sneat')

@section('title', 'Edit Jadwal Pelajaran')
@section('page-title', 'Edit Jadwal Pelajaran')
@section('page-subtitle', 'Perbarui jadwal pelajaran')

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

.info-badge {
    background: #e0f2fe;
    border-left: 4px solid #0ea5e9;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 20px;
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

<div class="info-badge">
    <i class="fas fa-info-circle me-2"></i>
    <strong>Info:</strong> Perubahan pada jadwal akan dicatat dalam history untuk audit trail.
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-edit text-primary me-2"></i>Form Edit Jadwal Pelajaran
        </h5>
    </div>

    <div class="card-body">
        <form action="{{ route('admin.jadwal-pelajaran.update', $jadwalPelajaran) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Tahun Ajaran & Kelas Section --}}
            <div class="form-section">
                <div class="form-section-title">Informasi Dasar</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tahun Ajaran <span class="text-danger">*</span></label>
                        <select name="tahun_ajaran_id" class="form-select" required disabled>
                            @foreach($tahunAjarans as $ta)
                                <option value="{{ $ta->id }}" {{ $jadwalPelajaran->tahun_ajaran_id == $ta->id ? 'selected' : '' }}>
                                    {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="tahun_ajaran_id" value="{{ $jadwalPelajaran->tahun_ajaran_id }}">
                        <small class="text-muted">Tahun ajaran tidak dapat diubah</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kelas <span class="text-danger">*</span></label>
                        <select name="kelas_id" id="kelasSelect" class="form-select" required>
                            @foreach($kelasList as $kls)
                                <option value="{{ $kls->id }}" data-jenjang="{{ $kls->jenjang }}"
                                        {{ old('kelas_id', $jadwalPelajaran->kelas_id) == $kls->id ? 'selected' : '' }}>
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
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}" data-jenjang="{{ $mapel->jenjang }}"
                                        {{ old('mata_pelajaran_id', $jadwalPelajaran->mata_pelajaran_id) == $mapel->id ? 'selected' : '' }}>
                                    {{ $mapel->nama_mapel }} ({{ $mapel->jenjang }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Guru Pengajar <small class="text-muted">(Opsional)</small></label>
                        <select name="guru_id" class="form-select">
                            <option value="">-- Belum Ditentukan --</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}"
                                        {{ old('guru_id', $jadwalPelajaran->guru_id) == $guru->id ? 'selected' : '' }}>
                                    {{ $guru->nama_lengkap }}
                                </option>
                            @endforeach
                        </select>
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
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}"
                                        {{ old('hari', $jadwalPelajaran->hari) == $hari ? 'selected' : '' }}>
                                    {{ $hari }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Mulai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_mulai" class="form-control"
                               value="{{ old('jam_mulai', \Carbon\Carbon::parse($jadwalPelajaran->jam_mulai)->format('H:i')) }}" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Jam Selesai <span class="text-danger">*</span></label>
                        <input type="time" name="jam_selesai" class="form-control"
                               value="{{ old('jam_selesai', \Carbon\Carbon::parse($jadwalPelajaran->jam_selesai)->format('H:i')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Keterangan <small class="text-muted">(Opsional)</small></label>
                    <textarea name="keterangan" class="form-control" rows="2" placeholder="Catatan tambahan untuk jadwal ini">{{ old('keterangan', $jadwalPelajaran->keterangan) }}</textarea>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.jadwal-pelajaran.index', ['tahun_ajaran_id' => $jadwalPelajaran->tahun_ajaran_id]) }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save me-1"></i> Update Jadwal
                </button>
            </div>
        </form>
    </div>
</div>

{{-- History Section --}}
@if($jadwalPelajaran->histories->count() > 0)
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-history text-info me-2"></i>Riwayat Perubahan
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Field</th>
                        <th>Nilai Lama</th>
                        <th>Nilai Baru</th>
                        <th>Keterangan</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwalPelajaran->histories->sortByDesc('changed_at') as $history)
                    <tr>
                        <td>{{ $history->changed_at->format('d/m/Y H:i') }}</td>
                        <td><strong>{{ ucfirst(str_replace('_', ' ', $history->field_changed)) }}</strong></td>
                        <td>{{ $history->old_value }}</td>
                        <td>{{ $history->new_value }}</td>
                        <td>{{ $history->keterangan }}</td>
                        <td>{{ $history->changedBy->name ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
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

    // Trigger filter on page load
    kelasSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection
