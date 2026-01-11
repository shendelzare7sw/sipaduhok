@extends('layouts.sneat')

@section('title', 'Kelola Berita')
@section('page-title', 'Kelola Berita')
@section('page-subtitle', 'Kelola konten dan informasi publik')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* --- STYLE KONSISTEN --- */
/* Stats & Gradients */
.stat-card { padding: 24px; border-radius: 12px; position: relative; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.2s; height: 100%; color: white; }
.stat-card:hover { transform: translateY(-5px); }
.stat-content { position: relative; z-index: 2; }
.stat-title { font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.9; margin-bottom: 8px; }
.stat-number { font-size: 38px; font-weight: 700; margin-bottom: 4px; line-height: 1.2; }
.stat-desc { font-size: 13px; opacity: 0.8; }
.stat-icon-bg { position: absolute; right: 20px; top: 50%; transform: translateY(-50%); font-size: 70px; opacity: 0.15; z-index: 1; }

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }

/* Table & Layout */
.news-thumbnail { width: 80px; height: 60px; object-fit: cover; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
.news-title { font-weight: 600; color: #111827; font-size: 15px; margin-bottom: 4px; display: block; }
.news-desc { font-size: 13px; color: #6b7280; line-height: 1.4; }
.news-link { font-size: 12px; color: #3b82f6; text-decoration: none; display: flex; align-items: center; gap: 4px; margin-top: 4px; }
.news-link:hover { text-decoration: underline; }

.card { background: white; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 24px; border: none; }
.card-header { padding: 20px 24px; border-bottom: 1px solid #e5e7eb; background: #fff; border-radius: 12px 12px 0 0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
.card-header h5 { margin: 0; font-size: 18px; font-weight: 600; color: #111827; }
.card-body { padding: 24px; }

.filter-section { display: flex; gap: 12px; flex-wrap: wrap; align-items: center; margin-bottom: 24px; }
.search-box { position: relative; }
.search-box input { padding: 10px 16px 10px 42px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; width: 250px; }
.search-box i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #9ca3af; }
.filter-select { padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; min-width: 160px; }

.table-responsive { overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; }
.table th { text-align: left; padding: 14px 16px; background: #f9fafb; color: #4b5563; font-weight: 600; font-size: 12px; text-transform: uppercase; border-bottom: 2px solid #e5e7eb; }
.table td { padding: 16px; border-bottom: 1px solid #f3f4f6; color: #374151; font-size: 14px; vertical-align: middle; }

.badge { padding: 6px 12px; border-radius: 50px; font-size: 12px; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-danger { background: #fee2e2; color: #991b1b; }
.badge-info { background: #e0f2fe; color: #075985; }

.btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; }
.btn-primary { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: white; }
.btn-outline { background: white; border: 1px solid #d1d5db; color: #374151; }
.btn-icon { width: 36px; height: 36px; padding: 0; border-radius: 8px; }
.btn-light-primary { background: #eff6ff; color: #3b82f6; }
.btn-light-warning { background: #fffbeb; color: #d97706; }
.btn-light-danger { background: #fef2f2; color: #dc2626; }
.action-buttons { display: flex; gap: 6px; justify-content: center; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div style="max-width: 1400px;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 12px; background: #dcfce7; color: #166534; padding: 15px; border: 1px solid #bbf7d0;">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row" style="display: flex; gap: 20px; margin-bottom: 24px;">
        <div style="flex: 1;">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Berita</div>
                    <div class="stat-number">{{ $berita->total() ?? 0 }}</div>
                    <div class="stat-desc">Semua berita terdaftar</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-newspaper"></i></div>
            </div>
        </div>
        <div style="flex: 1;">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Published</div>
                    <div class="stat-number">{{ $berita->where('status', 'aktif')->count() }}</div>
                    <div class="stat-desc">Tayang di website</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div style="flex: 1;">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Featured</div>
                    <div class="stat-number">{{ $berita->where('is_featured', true)->count() }}</div>
                    <div class="stat-desc">Berita unggulan</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-star"></i></div>
            </div>
        </div>
        <div style="flex: 1;">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">Kategori</div>
                    <div class="stat-number">{{ count($kategoriOptions) }}</div>
                    <div class="stat-desc">Topik tersedia</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-tags"></i></div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h5><i class="fas fa-newspaper" style="color: #3b82f6; margin-right: 10px;"></i>Kelola Berita</h5>
                <small style="color: #6b7280;">Kelola konten dan informasi publik</small>
            </div>
            <a href="{{ route('admin.akademik.berita.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> Tambah Berita
            </a>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('admin.akademik.berita.index') }}">
                <div class="filter-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita...">
                    </div>

                    <select name="kategori" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ request('kategori') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        @foreach($kategoriOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('kategori') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                        @foreach($statusOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if(request()->hasAny(['search', 'kategori', 'status']) && request('kategori') != 'all')
                        <a href="{{ route('admin.akademik.berita.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            @if($berita->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="width: 100px;">Gambar</th>
                                <th>Informasi Berita</th>
                                <th>Kategori</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th style="text-align: center; width: 150px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($berita as $item)
                                <tr>
                                    <td>
                                        <img src="{{ $item->gambar_url }}" alt="Thumb" class="news-thumbnail">
                                    </td>
                                    <td style="max-width: 350px;">
                                        <span class="news-title">
                                            {{ $item->judul }}
                                            @if($item->is_featured)
                                                <i class="fas fa-star text-warning ms-1" title="Featured"></i>
                                            @endif
                                        </span>
                                        <div class="news-desc">{{ Str::limit($item->deskripsi_singkat, 90) }}</div>
                                        @if($item->url_berita)
                                            <a href="{{ $item->url_berita }}" target="_blank" class="news-link">
                                                <i class="fas fa-link"></i> {{ Str::limit($item->url_berita, 30) }}
                                            </a>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">{{ $item->kategori_label }}</span></td>
                                    <td>
                                        <div style="font-weight: 500;">{{ $item->tanggal_berita->format('d M Y') }}</div>
                                        <small class="text-muted">Urutan: {{ $item->urutan_tampil }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $statusLabel = strtolower($item->status);
                                            $badgeClass = 'bg-secondary';
                                            if ($statusLabel == 'aktif') $badgeClass = 'bg-success';
                                            elseif ($statusLabel == 'draft') $badgeClass = 'bg-warning';
                                            elseif ($statusLabel == 'arsip') $badgeClass = 'bg-danger';
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($item->status) }}</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">

                                            {{-- TOMBOL BINTANG: SEKARANG MEMBUKA MODAL (openModal) --}}
                                            <button type="button"
                                                    onclick="openModal('featuredModal{{ $item->id }}')"
                                                    class="btn btn-icon {{ $item->is_featured ? 'btn-light-warning' : 'btn-outline' }}"
                                                    title="{{ $item->is_featured ? 'Hapus Featured' : 'Jadikan Featured' }}"
                                                    style="{{ $item->is_featured ? '' : 'border: 1px solid #d1d5db;' }}">
                                                <i class="{{ $item->is_featured ? 'fas' : 'far' }} fa-star"></i>
                                            </button>

                                            <a href="{{ route('admin.akademik.berita.edit', $item->id) }}" class="btn btn-icon btn-light-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <button type="button" class="btn btn-icon btn-light-danger" title="Hapus" onclick="confirmDelete({{ $item->id }}, '{{ addslashes($item->judul) }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($berita->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">Menampilkan {{ $berita->firstItem() }} - {{ $berita->lastItem() }} dari {{ $berita->total() }} data</div>
                        <div>{{ $berita->links() }}</div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-newspaper"></i>
                    <h3>Belum Ada Berita</h3>
                    <p>Silakan tambahkan berita atau informasi terbaru.</p>
                    <a href="{{ route('admin.akademik.berita.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i> Tambah Berita</a>
                </div>
            @endif
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
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus berita ini?</h6>
                <p class="text-muted mb-0" id="deleteBeritaName"></p>
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

{{-- Featured Modal Loop --}}
@foreach($berita as $item)
    <div class="modal fade" id="featuredModal{{ $item->id }}" tabindex="-1" aria-labelledby="featuredModalLabel{{ $item->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-white border-0">
                    <h5 class="modal-title" id="featuredModalLabel{{ $item->id }}">
                        <i class="fas fa-star me-2"></i>Konfirmasi Featured
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold mb-2">
                        @if($item->is_featured)
                            Apakah Anda yakin ingin menghapus berita ini dari Featured?
                        @else
                            Apakah Anda yakin ingin menjadikan berita ini sebagai Featured?
                        @endif
                    </h6>
                    <p class="text-muted mb-0">{{ $item->judul }}</p>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        @if($item->is_featured)
                            Berita tidak akan tampil lagi di slider utama
                        @else
                            Berita akan ditampilkan di halaman depan (slider utama)
                        @endif
                    </small>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Batal
                    </button>
                    <button type="button" class="btn btn-warning" onclick="confirmFeatured({{ $item->id }})">
                        <i class="fas fa-star me-1"></i>
                        @if($item->is_featured)
                            Hapus Featured
                        @else
                            Jadikan Featured
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- SCRIPT JAVASCRIPT --}}
<script>
// Delete Confirmation Function (Bootstrap 5)
function confirmDelete(id, name) {
    document.getElementById('deleteBeritaName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/akademik/berita/' + id;
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Open Featured Modal (Bootstrap 5)
function openModal(modalId) {
    const modal = new bootstrap.Modal(document.getElementById(modalId));
    modal.show();
}

// Fungsi Eksekusi Featured (Dipanggil dari dalam Modal)
async function confirmFeatured(beritaId) {
    // Ambil CSRF Token
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('Error: CSRF Token tidak ditemukan di layout!');
        return;
    }

    try {
        const response = await fetch(`/admin/akademik/berita/${beritaId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            }
        });

        const data = await response.json();

        if (data.success) {
            // Tutup modal terlebih dahulu
            const modalElement = document.getElementById('featuredModal' + beritaId);
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
            // Reload halaman setelah modal tertutup
            setTimeout(() => {
                location.reload();
            }, 300);
        } else {
            alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
            // Tutup modal jika gagal
            const modalElement = document.getElementById('featuredModal' + beritaId);
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan koneksi.');
        // Tutup modal jika error
        const modalElement = document.getElementById('featuredModal' + beritaId);
        const modal = bootstrap.Modal.getInstance(modalElement);
        if (modal) {
            modal.hide();
        }
    }
}
</script>
</div>
@endsection
