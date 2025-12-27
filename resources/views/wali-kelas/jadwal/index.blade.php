@extends('layouts.sneat')

@section('title', 'Jadwal Pelajaran')
@section('page-title', 'Jadwal Pelajaran')
@section('page-subtitle', 'Kelola jadwal pelajaran kelas ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .card-hari {
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }
    .header-hari {
        background-color: #f8f9fc;
        border-bottom: 2px solid #e3e6f0;
        padding: 15px 20px;
    }
    .table-jadwal thead th {
        background: #f1f4f9;
        text-transform: uppercase;
        font-size: 11px;
        color: #4e73df;
        letter-spacing: 1px;
    }
    .jam-badge {
        background: #eef2ff;
        color: #4e73df;
        padding: 5px 10px;
        border-radius: 6px;
        font-weight: 700;
        display: inline-block;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @endif

    {{-- HEADER ACTIONS --}}
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col">
                    <h4 class="m-0 fw-bold text-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Jadwal Kelas {{ $kelas->nama_kelas }}
                    </h4>
                    <p class="text-muted small mb-0 mt-1">Gunakan tombol di sebelah kanan untuk memodifikasi atau mencetak jadwal.</p>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahJadwalModal">
                        <i class="fas fa-plus-circle me-1"></i> Tambah Jadwal
                    </button>
                    <a href="{{ route('wali.jadwal.print') }}" target="_blank" class="btn btn-outline-secondary shadow-sm ms-2">
                        <i class="fas fa-print me-1"></i> Cetak
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- JADWAL PER HARI --}}
    <div class="row">
        @foreach($hariList as $hari)
        <div class="col-lg-6 mb-4">
            <div class="card shadow card-hari h-100">
                <div class="header-hari d-flex justify-content-between align-items-center">
                    <h5 class="m-0 fw-bold text-gray-800">
                        <i class="fas fa-clock me-2 text-primary"></i>{{ $hari }}
                    </h5>
                    <span class="badge bg-primary rounded-pill px-3 py-2">
                        {{ $jadwalPerHari[$hari]->count() }} Pelajaran
                    </span>
                </div>
                <div class="card-body p-0">
                    @if($jadwalPerHari[$hari]->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-jadwal mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Jam</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Pengajar</th>
                                    <th class="text-center pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($jadwalPerHari[$hari] as $jadwal)
                                <tr>
                                    <td class="ps-4 align-middle">
                                        <div class="jam-badge small">
                                            {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-800">{{ $jadwal->mataPelajaran->nama_mapel }}</div>
                                        <small class="text-muted">{{ $jadwal->mataPelajaran->kode_mapel }}</small>
                                    </td>
                                    <td class="align-middle small fw-bold text-gray-600">
                                        {{ $jadwal->guru->nama_lengkap }}
                                    </td>
                                    <td class="text-center pe-4 align-middle">
                                        <div class="btn-group">
                                            <button onclick="editJadwal({{ $jadwal->id }}, '{{ $jadwal->mata_pelajaran_id }}', '{{ $jadwal->guru_id }}', '{{ $jadwal->hari }}', '{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}', '{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}')"
                                                    class="btn btn-sm btn-warning mx-1 rounded-circle" style="width: 32px; height: 32px;" title="Edit">
                                                <i class="fas fa-edit fa-sm"></i>
                                            </button>
                                            <button onclick="hapusJadwal({{ $jadwal->id }}, '{{ $jadwal->mataPelajaran->nama_mapel }}', '{{ $hari }}')"
                                                    class="btn btn-sm btn-danger mx-1 rounded-circle" style="width: 32px; height: 32px;" title="Hapus">
                                                <i class="fas fa-trash fa-sm"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-3x text-gray-200 mb-3"></i>
                        <p class="text-gray-500 small">Belum ada jadwal hari ini</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
</div>

{{-- MODAL TAMBAH (CONTOH) --}}
<div class="modal fade" id="tambahJadwalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog shadow-lg">
        <div class="modal-content border-0">
            <form action="{{ route('wali.jadwal.store') }}" method="POST">
                @csrf
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-plus-circle me-2"></i>Tambah Jadwal Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Hari</label>
                        <select name="hari" class="form-select" required>
                            @foreach($hariList as $hari)
                                <option value="{{ $hari }}">{{ $hari }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" class="form-select" required>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase">Guru Pengajar</label>
                        <select name="guru_id" class="form-select" required>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Jam Mulai</label>
                            <input type="time" name="jam_mulai" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold small text-uppercase">Jam Selesai</label>
                            <input type="time" name="jam_selesai" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button class="btn btn-secondary btn-sm" type="button" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary btn-sm px-4 shadow" type="submit">Simpan Jadwal</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL EDIT & HAPUS DISESUAIKAN SEPERTI TAMBAH DI ATAS --}}
@endsection

@section('scripts')
<script>
    function editJadwal(id, mapelId, guruId, hari, jamMulai, jamSelesai) {
        // Implementasi logika pengisian form edit seperti kode Anda sebelumnya
        // Sesuaikan target selector ID jika menggunakan modal edit baru
        const editModal = new bootstrap.Modal(document.getElementById('editJadwalModal'));
        editModal.show();
    }

    function hapusJadwal(id, namaMapel, hari) {
        document.getElementById('hapus_nama_mapel').textContent = namaMapel;
        document.getElementById('hapus_hari').textContent = hari;
        document.getElementById('hapusJadwalForm').action = '/wali/jadwal/' + id;
        const hapusModal = new bootstrap.Modal(document.getElementById('hapusJadwalModal'));
        hapusModal.show();
    }
</script>
@endsection