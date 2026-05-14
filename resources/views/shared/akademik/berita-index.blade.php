@php
    $routePrefix = $routePrefix ?? 'sekretaris';
    $basePath = $basePath ?? '/sekretaris/berita';
    $items = is_object($berita) && method_exists($berita, 'getCollection') ? $berita->getCollection() : collect($berita);
    $total = is_object($berita) && method_exists($berita, 'total') ? $berita->total() : $items->count();
@endphp

<div class="ak-page">
    <div class="ak-toolbar">
        <div class="ak-toolbar-title">
            <span class="ak-toolbar-icon"><i class="fas fa-newspaper"></i></span>
            <div>
                <h5>Kelola Berita</h5>
                <p>Kelola konten berita publik, kategori, status tayang, dan featured.</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.berita.create') }}" class="ak-btn primary">
            <i class="fas fa-plus"></i>
            Tambah Berita
        </a>
    </div>

    <div class="ak-stats">
        <div class="ak-stat primary">
            <span>Total Berita</span>
            <strong>{{ number_format($total) }}</strong>
            <small>Semua berita terdaftar</small>
            <i class="fas fa-newspaper"></i>
        </div>
        <div class="ak-stat success">
            <span>Published</span>
            <strong>{{ number_format($items->where('status', 'aktif')->count()) }}</strong>
            <small>Tayang di website</small>
            <i class="fas fa-check-circle"></i>
        </div>
        <div class="ak-stat warning">
            <span>Featured</span>
            <strong>{{ number_format($items->where('is_featured', true)->count()) }}</strong>
            <small>Berita unggulan</small>
            <i class="fas fa-star"></i>
        </div>
        <div class="ak-stat purple">
            <span>Kategori</span>
            <strong>{{ number_format(count($kategoriOptions ?? [])) }}</strong>
            <small>Topik tersedia</small>
            <i class="fas fa-tags"></i>
        </div>
    </div>

    <div class="ak-panel">
        <div class="ak-panel-header">
            <h5><i class="fas fa-list text-primary me-2"></i>Daftar Berita</h5>
        </div>
        <div class="ak-panel-body">
            <form method="GET" action="{{ route($routePrefix . '.berita.index') }}" class="ak-filter">
                <input type="text" name="search" value="{{ request('search') }}" class="ak-input" placeholder="Cari judul berita...">

                <select name="kategori" class="ak-select" onchange="this.form.submit()">
                    <option value="all" {{ request('kategori') === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                    @foreach(($kategoriOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" {{ request('kategori') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <select name="status" class="ak-select" onchange="this.form.submit()">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Semua Status</option>
                    @foreach(($statusOptions ?? []) as $value => $label)
                        <option value="{{ $value }}" {{ request('status') === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>

                <button type="submit" class="ak-btn secondary">
                    <i class="fas fa-search"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'kategori', 'status']))
                    <a href="{{ route($routePrefix . '.berita.index') }}" class="ak-btn danger">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </form>
        </div>

        @if($items->count() > 0)
            <div class="ak-table-wrap">
                <table class="ak-table">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Informasi Berita</th>
                            <th class="text-center">Kategori</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $status = strtolower($item->status ?? 'draft');
                                $statusClass = match($status) {
                                    'aktif' => 'success',
                                    'draft' => 'warning',
                                    'arsip' => 'danger',
                                    default => 'muted',
                                };
                            @endphp
                            <tr>
                                <td data-label="Gambar">
                                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}" class="ak-thumb">
                                </td>
                                <td class="ak-main-cell" data-label="Berita">
                                    <div class="ak-title">
                                        {{ $item->judul }}
                                        @if($item->is_featured)
                                            <i class="fas fa-star text-warning ms-1" title="Featured"></i>
                                        @endif
                                    </div>
                                    <div class="ak-sub">{{ \Illuminate\Support\Str::limit($item->deskripsi_singkat, 110) }}</div>
                                    @if($item->url_berita)
                                        <a href="{{ $item->url_berita }}" target="_blank" class="ak-sub d-inline-flex gap-1 text-primary">
                                            <i class="fas fa-link"></i>{{ \Illuminate\Support\Str::limit($item->url_berita, 42) }}
                                        </a>
                                    @endif
                                </td>
                                <td class="text-center" data-label="Kategori">
                                    <span class="ak-badge info">{{ $item->kategori_label }}</span>
                                </td>
                                <td class="text-center" data-label="Tanggal">
                                    {{ $item->tanggal_berita?->format('d M Y') ?? '-' }}
                                    <div class="ak-sub">Urutan {{ $item->urutan_tampil }}</div>
                                </td>
                                <td class="text-center" data-label="Status">
                                    <span class="ak-badge {{ $statusClass }}">{{ ucfirst($item->status) }}</span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="ak-actions">
                                        <button type="button"
                                            onclick="openModal('featuredModal{{ $item->id }}')"
                                            class="ak-btn {{ $item->is_featured ? 'warning' : 'secondary' }} ak-icon-btn"
                                            title="{{ $item->is_featured ? 'Hapus Featured' : 'Jadikan Featured' }}">
                                            <i class="{{ $item->is_featured ? 'fas' : 'far' }} fa-star"></i>
                                        </button>
                                        <a href="{{ route($routePrefix . '.berita.edit', $item->id) }}" class="ak-btn secondary ak-icon-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="ak-btn danger ak-icon-btn" title="Hapus" onclick="confirmDelete({{ $item->id }}, @js($item->judul))">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(is_object($berita) && method_exists($berita, 'hasPages') && $berita->hasPages())
                <div class="ak-pagination">{{ $berita->links() }}</div>
            @endif
        @else
            <div class="ak-empty">
                <i class="fas fa-newspaper"></i>
                <h5>Belum ada berita</h5>
                <p>Tambahkan berita atau informasi publik terbaru.</p>
                <a href="{{ route($routePrefix . '.berita.create') }}" class="ak-btn primary">
                    <i class="fas fa-plus"></i> Tambah Berita
                </a>
            </div>
        @endif
    </div>
</div>

@include('shared.akademik.delete-modal', [
    'modalTitle' => 'Hapus Berita',
    'itemLabelId' => 'deleteAkademikName',
])

@foreach($items as $item)
    <div class="modal fade" id="featuredModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-warning">
                        <i class="fas fa-star me-2"></i>Konfirmasi Featured
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-star fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold mb-2">
                        {{ $item->is_featured ? 'Hapus berita ini dari Featured?' : 'Jadikan berita ini sebagai Featured?' }}
                    </h6>
                    <p class="text-muted mb-0">{{ $item->judul }}</p>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="ak-btn secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i>Batal
                    </button>
                    <button type="button" class="ak-btn warning" onclick="confirmFeatured({{ $item->id }})">
                        <i class="fas fa-star"></i>{{ $item->is_featured ? 'Hapus Featured' : 'Jadikan Featured' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteAkademikName').textContent = name;
    document.getElementById('deleteForm').action = @js($basePath) + '/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function openModal(modalId) {
    new bootstrap.Modal(document.getElementById(modalId)).show();
}

async function confirmFeatured(beritaId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('CSRF token tidak ditemukan.');
        return;
    }

    try {
        const response = await fetch(@js($basePath) + '/' + beritaId + '/toggle-featured', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.content
            }
        });

        const data = await response.json();
        if (data.success) {
            const modalElement = document.getElementById('featuredModal' + beritaId);
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) modal.hide();
            setTimeout(() => location.reload(), 250);
            return;
        }

        alert('Gagal: ' + (data.message || 'Terjadi kesalahan'));
    } catch (error) {
        console.error(error);
        alert('Terjadi kesalahan koneksi.');
    }
}
</script>
