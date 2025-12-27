@extends('layouts.sneat')

@section('title', 'Kelola Guru Kelas - ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Guru Pengajar Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas . ' - ' . $kelas->tahunAjaran->nama_tahun_ajaran)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
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
    color: #14b8a6;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

.info-banner {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
    border-radius: 16px;
    padding: 24px 32px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.info-banner h2 {
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 8px;
}

.info-banner-meta {
    display: flex;
    gap: 16px;
    flex-wrap: wrap;
}

.info-banner-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    opacity: 0.9;
}

.info-banner-stats {
    display: flex;
    gap: 24px;
}

.info-banner-stat {
    text-align: center;
    background: rgba(255,255,255,0.15);
    padding: 12px 20px;
    border-radius: 10px;
}

.info-banner-stat-value {
    font-size: 28px;
    font-weight: 700;
}

.info-banner-stat-label {
    font-size: 12px;
    opacity: 0.8;
}

.grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 992px) {
    .grid-2 {
        grid-template-columns: 1fr;
    }
}

.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: center;
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
    color: #14b8a6;
}

.card-body {
    padding: 24px;
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
    padding: 10px 14px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
}

.form-group select:focus {
    outline: none;
    border-color: #14b8a6;
    box-shadow: 0 0 0 3px rgba(20, 184, 166, 0.1);
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

.btn-primary {
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
}

.btn-danger {
    background: #fee2e2;
    color: #dc2626;
    border: none;
}

.btn-danger:hover {
    background: #fecaca;
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 12px 16px;
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    font-size: 14px;
}

.table tr:hover td {
    background: #f9fafb;
}

.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
}

.badge-teal { background: #ccfbf1; color: #0d9488; }

.guru-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.guru-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 13px;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
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
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.guru-pengajar.index') }}">Data Guru Pengajar</a>
        <span>/</span>
        <span class="current">Kelas {{ $kelas->nama_kelas }}</span>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="info-banner">
        <div>
            <h2>Kelas {{ $kelas->nama_kelas }}</h2>
            <div class="info-banner-meta">
                <div class="info-banner-item">
                    <i class="fas fa-building"></i>
                    {{ $kelas->cabang->nama_cabang ?? '-' }}
                </div>
                <div class="info-banner-item">
                    <i class="fas fa-layer-group"></i>
                    {{ $kelas->jenjang }}
                </div>
                <div class="info-banner-item">
                    <i class="fas fa-calendar"></i>
                    {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}
                </div>
            </div>
        </div>
        <div class="info-banner-stats">
            <div class="info-banner-stat">
                <div class="info-banner-stat-value">{{ $kelas->guruPengajar->count() }}</div>
                <div class="info-banner-stat-label">Guru Pengajar</div>
            </div>
        </div>
    </div>

    <div class="grid-2">
        {{-- Form Tambah Guru --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-plus-circle"></i> Tambah Guru Pengajar</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.guru-pengajar.assign-to-kelas', $kelas) }}" method="POST">
                    @csrf
                    
                    <div class="form-group">
                        <label for="tenaga_pendidik_id">Pilih Guru</label>
                        <select name="tenaga_pendidik_id" id="tenaga_pendidik_id" required>
                            <option value="">-- Pilih Guru --</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="mata_pelajaran_id">Pilih Mata Pelajaran</label>
                        <select name="mata_pelajaran_id" id="mata_pelajaran_id" required>
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            @foreach($mataPelajaranList as $mapel)
                                <option value="{{ $mapel->id }}">{{ $mapel->nama_mapel }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="fas fa-plus"></i> Tambah Guru
                    </button>
                </form>
            </div>
        </div>

        {{-- Daftar Guru di Kelas Ini --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-users"></i> Guru di Kelas Ini</h5>
            </div>
            <div class="card-body">
                @if($kelas->guruPengajar->count() > 0)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Guru</th>
                                    <th>Mata Pelajaran</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kelas->guruPengajar as $assignment)
                                <tr>
                                    <td>
                                        <div class="guru-info">
                                            <div class="guru-avatar">{{ strtoupper(substr($assignment->tenagaPendidik->nama_lengkap, 0, 1)) }}</div>
                                            <span>{{ $assignment->tenagaPendidik->nama_lengkap }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-teal">{{ $assignment->mataPelajaran->nama_mapel }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.guru-pengajar.remove-from-kelas', $kelas) }}" method="POST" id="deleteForm{{ $assignment->id }}">
                                            @csrf
                                            <input type="hidden" name="assignment_id" value="{{ $assignment->id }}">
                                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmDeleteGuru('{{ $assignment->id }}', '{{ $assignment->tenagaPendidik->nama_lengkap }}', '{{ $assignment->mataPelajaran->nama_mapel }}')">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <p>Belum ada guru ditugaskan di kelas ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="margin-top: 24px;">
        <a href="{{ route('admin.guru-pengajar.index') }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Guru
        </a>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 16px; overflow: hidden;">
            <div class="modal-header" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); color: white; border: none; padding: 20px 24px;">
                <h5 class="modal-title" id="deleteModalLabel" style="display: flex; align-items: center; gap: 10px; margin: 0; font-weight: 600;">
                    <i class="fas fa-exclamation-triangle"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 24px;">
                <p style="margin-bottom: 16px; color: #374151; font-size: 15px;">Apakah Anda yakin ingin mengeluarkan guru berikut dari kelas ini?</p>
                <div style="background: #fef2f2; border-left: 4px solid #ef4444; padding: 16px; border-radius: 8px; margin-bottom: 16px;">
                    <div style="font-weight: 600; color: #111827; margin-bottom: 4px;" id="guruName"></div>
                    <div style="font-size: 14px; color: #6b7280;" id="mapelName"></div>
                </div>
                <p style="color: #6b7280; font-size: 14px; margin: 0;">
                    <i class="fas fa-info-circle"></i> Tindakan ini akan menghapus penugasan guru pada kelas.
                </p>
            </div>
            <div class="modal-footer" style="border: none; padding: 16px 24px; background: #f9fafb; gap: 10px;">
                <button type="button" class="btn btn-outline" data-bs-dismiss="modal" style="flex: 1;">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="flex: 1;">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let deleteFormId = null;

function confirmDeleteGuru(assignmentId, guruName, mapelName) {
    deleteFormId = assignmentId;
    document.getElementById('guruName').textContent = guruName;
    document.getElementById('mapelName').textContent = 'Mata Pelajaran: ' + mapelName;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteFormId) {
        document.getElementById('deleteForm' + deleteFormId).submit();
    }
});
</script>
@endsection
