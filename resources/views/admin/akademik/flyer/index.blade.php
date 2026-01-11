@extends('layouts.sneat')

@section('title', 'Kelola Flyer')
@section('page-title', 'Kelola Flyer / Iklan')
@section('page-subtitle', 'Pop-up informasi untuk siswa saat login')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* === STYLE STAT CARD VIBRANT (KONSISTEN) === */
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
.bg-grad-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

/* === FLYER CARD IMPROVEMENT === */
.flyer-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
}
.flyer-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15) !important;
}
.flyer-img-container {
    position: relative;
    height: 200px;
    overflow: hidden;
}
.flyer-img-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}
.flyer-card:hover .flyer-img-container img {
    transform: scale(1.1);
}
.flyer-status-overlay {
    position: absolute;
    top: 15px;
    right: 15px;
    z-index: 3;
}
.flyer-badge-info {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(5px);
    color: #4e73df;
    font-weight: 800;
    font-size: 10px;
    padding: 5px 12px;
    border-radius: 50px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- STATISTIK FLYER --}}
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card bg-grad-blue shadow">
                <div class="stat-content">
                    <div class="stat-title">Total Flyer</div>
                    <div class="stat-number">{{ $flyer->total() }}</div>
                    <div class="stat-desc">Media Promosi & Info</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-images"></i></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-6 mb-3">
            <div class="stat-card bg-grad-green shadow">
                <div class="stat-content">
                    <div class="stat-title">Flyer Aktif</div>
                    <div class="stat-number">{{ $flyer->where('status', 'aktif')->count() }}</div>
                    <div class="stat-desc">Muncul saat Siswa Login</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-toggle-on"></i></div>
            </div>
        </div>
        <div class="col-xl-4 col-md-12 mb-3">
            <div class="stat-card bg-grad-orange shadow">
                <div class="stat-content">
                    <div class="stat-title">Antrean / Draft</div>
                    <div class="stat-number">{{ $flyer->where('status', 'nonaktif')->count() }}</div>
                    <div class="stat-desc">Belum Dipublikasi</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-layer-group"></i></div>
            </div>
        </div>
    </div>

    {{-- HEADER ACTIONS --}}
    <div class="card shadow mb-4">
        <div class="card-body py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-ad me-2"></i>Daftar Konten Pop-up Siswa</h6>
            <a href="{{ route('admin.akademik.flyer.create') }}" class="btn btn-primary btn-sm shadow-sm fw-bold">
                <i class="fas fa-plus-circle me-1"></i> Tambah Flyer Baru
            </a>
        </div>
    </div>

    {{-- GRID FLYER --}}
    <div class="row">
        @forelse($flyer as $item)
        <div class="col-xl-4 col-lg-6 mb-4">
            <div class="card flyer-card shadow-sm h-100">
                <div class="flyer-img-container">
                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}">
                    <div class="flyer-status-overlay">
                        <span class="badge {{ $item->status_badge['class'] }} shadow-sm px-3 py-2">
                            {{ $item->status_badge['label'] }}
                        </span>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h5 class="card-title fw-bold text-gray-900 mb-0">{{ $item->judul }}</h5>
                        <span class="flyer-badge-info" title="Urutan Tampil">#{{ $item->urutan_tampil }}</span>
                    </div>
                    
                    <p class="card-text small text-muted mb-3">
                        {{ Str::limit($item->deskripsi, 90) }}
                    </p>

                    <div class="bg-light p-2 rounded mb-3">
                        <div class="d-flex align-items-center mb-1">
                            <i class="fas fa-users fa-fw text-primary me-2 small"></i>
                            <span class="small fw-bold text-dark">Target: {{ $item->target_label }}</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-calendar-alt fa-fw text-info me-2 small"></i>
                            <span class="small text-muted">{{ $item->tanggal_mulai->format('d M') }} — {{ $item->tanggal_selesai->format('d M Y') }}</span>
                        </div>
                    </div>

                    @if($item->link_url)
                        <a href="{{ $item->link_url }}" target="_blank" class="btn btn-outline-info btn-sm w-100 mb-3 fw-bold">
                            <i class="fas fa-external-link-alt me-1"></i> Kunjungi Tautan
                        </a>
                    @endif

                    <div class="row no-gutters gap-2">
                        <div class="col pe-1">
                            <a href="{{ route('admin.akademik.flyer.edit', $item->id) }}" class="btn btn-warning btn-sm w-100 fw-bold">
                                <i class="fas fa-edit me-1"></i> Edit
                            </a>
                        </div>
                        <div class="col ps-1">
                            <button type="button" class="btn btn-danger btn-sm w-100 fw-bold" onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')">
                                <i class="fas fa-trash me-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card shadow-sm border-0 py-5 text-center">
                <div class="card-body">
                    <i class="fas fa-images fa-4x text-gray-200 mb-3"></i>
                    <h5 class="text-gray-500 fw-bold">Belum ada flyer yang dibuat</h5>
                    <p class="small text-muted">Klik tombol "Tambah Flyer Baru" untuk membuat pop-up iklan login siswa.</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if($flyer->hasPages())
        <div class="card shadow-sm mb-4">
            <div class="card-body py-3">
                <div class="d-flex justify-content-center">
                    {{ $flyer->links() }}
                </div>
            </div>
        </div>
    @endif
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
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus flyer ini?</h6>
                <p class="text-muted mb-0" id="deleteFlyerName"></p>
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
    document.getElementById('deleteFlyerName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/akademik/flyer/' + id;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}
</script>
@endsection