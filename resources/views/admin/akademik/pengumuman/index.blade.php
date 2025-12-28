@extends('layouts.sneat')

@section('title', 'Kelola Pengumuman')
@section('page-title', 'Kelola Pengumuman')
@section('page-subtitle', 'Pengumuman otomatis dari kalender atau manual')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* === STYLE STAT CARD VIBRANT === */
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
    border: none;
}
.stat-card:hover { transform: translateY(-5px); }
.stat-content { position: relative; z-index: 2; }
.stat-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.9; margin-bottom: 8px; }
.stat-number { font-size: 28px; font-weight: 800; margin-bottom: 4px; line-height: 1; }
.stat-desc { font-size: 13px; opacity: 0.8; }
.stat-icon-bg { position: absolute; right: 15px; top: 50%; transform: translateY(-50%); font-size: 60px; opacity: 0.15; z-index: 1; }

.bg-grad-blue   { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-grad-green  { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-grad-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }

/* === CUSTOM BADGE SYSTEM === */
.badge-pill-custom {
    padding: 5px 12px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
}

/* Prioritas */
.badge-prioritas-tinggi { background-color: #ffe5e5; color: #d63031; border: 1px solid #fab1a0; }
.badge-prioritas-sedang { background-color: #fff4e5; color: #e67e22; border: 1px solid #ffcc80; }
.badge-prioritas-rendah { background-color: #e5f1ff; color: #0984e3; border: 1px solid #74b9ff; }

/* Sumber */
.badge-sumber-auto { background-color: #ebfbee; color: #2ecc71; border: 1px solid #b7ebc6; }
.badge-sumber-manual { background-color: #eef2ff; color: #4e73df; border: 1px solid #c7d2fe; }

/* Status */
.badge-status-aktif { background-color: #10b981; color: white; }
.badge-status-dijadwalkan { background-color: #0dcaf0; color: white; }
.badge-status-nonaktif { background-color: #858796; color: white; }
.badge-status-kadaluarsa { background-color: #e74a3b; color: white; }

/* Table Styling */
.table thead th {
    background: #f8f9fc;
    color: #4e73df;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e3e6f0;
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- STATISTIK RINGKAS --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card bg-grad-blue shadow">
                <div class="stat-content">
                    <div class="stat-title">Total Pengumuman</div>
                    <div class="stat-number">{{ $pengumuman->total() }}</div>
                    <div class="stat-desc">Semua Arsip Berita</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-bullhorn"></i></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card bg-grad-green shadow">
                <div class="stat-content">
                    <div class="stat-title">Sumber Otomatis</div>
                    <div class="stat-number">{{ $pengumuman->where('is_from_kalender', true)->count() }}</div>
                    <div class="stat-desc">Generated dari Kalender</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-robot"></i></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-3">
            <div class="stat-card bg-grad-purple shadow">
                <div class="stat-content">
                    <div class="stat-title">Prioritas Tinggi</div>
                    <div class="stat-number">{{ $pengumuman->where('prioritas', 'tinggi')->count() }}</div>
                    <div class="stat-desc">Membutuhkan Perhatian</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-exclamation-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- MAIN TABLE CARD --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-list me-2"></i>Daftar Pengumuman Institusi</h6>
            <a href="{{ route('admin.akademik.pengumuman.create') }}" class="btn btn-primary btn-sm shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> Tambah Pengumuman
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>DETAIL PENGUMUMAN</th>
                            <th class="text-center" width="130">TANGGAL</th>
                            <th class="text-center" width="120">PRIORITAS</th>
                            <th class="text-center" width="110">SUMBER</th>
                            <th class="text-center" width="120">STATUS</th>
                            <th class="text-center" width="100">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pengumuman as $item)
                        <tr>
                        <td class="align-middle">
                            <div class="fw-bold text-gray-900 mb-1">{{ $item->judul }}</div>
                            <div class="small text-muted mb-2">{{ Str::limit($item->isi_pengumuman, 85) }}</div>

                            <div class="d-flex flex-wrap gap-2">
                                @if($item->lampiran_surat)
                                    <a href="{{ asset('storage/' . $item->lampiran_surat) }}"
                                    target="_blank"
                                    class="badge bg-light text-primary border border-primary"
                                    style="text-decoration: none; padding: 4px 10px;">
                                        <i class="fas fa-paperclip me-1"></i> Lihat Lampiran
                                    </a>
                                @endif

                                @if($item->kalenderAkademik)
                                    <span class="badge bg-info text-white"
                                        style="padding: 4px 10px;">
                                        <i class="fas fa-calendar me-1"></i> Ref: {{ Str::limit($item->kalenderAkademik->nama_kegiatan, 20) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                            <td class="text-center align-middle fw-bold small text-gray-700">
                                {{ $item->tanggal_pengumuman->format('d M Y') }}
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $prioritasClass = match($item->prioritas ?? 'rendah') {
                                        'tinggi' => 'badge-prioritas-tinggi',
                                        'sedang' => 'badge-prioritas-sedang',
                                        default => 'badge-prioritas-rendah'
                                    };
                                @endphp
                                <span class="badge-pill-custom {{ $prioritasClass }}">
                                    <i class="fas fa-circle small"></i> {{ $item->prioritas_badge['label'] ?? ucfirst($item->prioritas ?? 'Rendah') }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                @if($item->is_from_kalender)
                                    <span class="badge-pill-custom badge-sumber-auto" title="Otomatis dari kalender">
                                        <i class="fas fa-robot"></i> AUTO
                                    </span>
                                @else
                                    <span class="badge-pill-custom badge-sumber-manual" title="Dibuat manual">
                                        <i class="fas fa-user-edit"></i> MANUAL
                                    </span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $statusLower = strtolower($item->status ?? 'aktif');
                                    $statusClass = match($statusLower) {
                                        'aktif' => 'badge-status-aktif',
                                        'dijadwalkan' => 'badge-status-dijadwalkan',
                                        'nonaktif', 'non-aktif' => 'badge-status-nonaktif',
                                        'kadaluarsa' => 'badge-status-kadaluarsa',
                                        default => 'badge-status-aktif'
                                    };
                                @endphp
                                <span class="badge badge-pill shadow-sm px-3 py-1 fw-bold {{ $statusClass }}" style="font-size: 10px;">
                                    {{ $item->status_badge['label'] ?? ucfirst($item->status ?? 'Aktif') }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm">
                                    <a href="{{ route('admin.akademik.pengumuman.edit', $item->id) }}" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                                    <button type="button" class="btn btn-sm btn-danger border-left" title="Hapus" onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-bullhorn fa-4x text-gray-200 mb-3"></i>
                                <h5 class="text-gray-500 fw-bold">Belum ada pengumuman rilis</h5>
                                <p class="small text-muted">Pengumuman akan muncul otomatis dari kalender (H-3) atau ditambahkan manual.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pengumuman->hasPages())
                <div class="card-footer bg-white border-top py-3">
                    <div class="d-flex justify-content-center">
                        {{ $pengumuman->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus pengumuman ini?</h6>
                <p class="text-muted mb-0" id="deletePengumumanName"></p>
                <small class="text-danger d-block mt-2">
                    <i class="fas fa-info-circle me-1"></i>Tindakan ini tidak dapat dibatalkan
                </small>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('deletePengumumanName').textContent = name;
    document.getElementById('deleteForm').action = '/sekretaris/pengumuman/' + id;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection
