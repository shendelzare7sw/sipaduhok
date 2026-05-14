@php
    $routePrefix = $routePrefix ?? 'sekretaris';
    $basePath = $basePath ?? '/sekretaris/pengumuman';
    $items = is_object($pengumuman) && method_exists($pengumuman, 'getCollection') ? $pengumuman->getCollection() : collect($pengumuman);
    $total = is_object($pengumuman) && method_exists($pengumuman, 'total') ? $pengumuman->total() : $items->count();
    $autoCount = $items->where('is_from_kalender', true)->count();
    $highCount = $items->where('prioritas', 'tinggi')->count();
@endphp

<div class="ak-page">
    <div class="ak-toolbar">
        <div class="ak-toolbar-title">
            <span class="ak-toolbar-icon"><i class="fas fa-bullhorn"></i></span>
            <div>
                <h5>Kelola Pengumuman</h5>
                <p>Pengumuman manual dan otomatis dari kalender akademik.</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.pengumuman.create') }}" class="ak-btn primary">
            <i class="fas fa-plus"></i>
            Tambah Pengumuman
        </a>
    </div>

    <div class="ak-stats three">
        <div class="ak-stat primary">
            <span>Total Pengumuman</span>
            <strong>{{ number_format($total) }}</strong>
            <small>Semua arsip pengumuman</small>
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="ak-stat success">
            <span>Sumber Otomatis</span>
            <strong>{{ number_format($autoCount) }}</strong>
            <small>Generated dari kalender</small>
            <i class="fas fa-robot"></i>
        </div>
        <div class="ak-stat purple">
            <span>Prioritas Tinggi</span>
            <strong>{{ number_format($highCount) }}</strong>
            <small>Membutuhkan perhatian</small>
            <i class="fas fa-exclamation-circle"></i>
        </div>
    </div>

    <div class="ak-panel">
        <div class="ak-panel-header">
            <h5><i class="fas fa-list text-primary me-2"></i>Daftar Pengumuman</h5>
        </div>

        @if($items->count() > 0)
            <div class="ak-table-wrap">
                <table class="ak-table">
                    <thead>
                        <tr>
                            <th>Detail Pengumuman</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Prioritas</th>
                            <th class="text-center">Sumber</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            @php
                                $priority = $item->prioritas ?? 'rendah';
                                $priorityClass = match($priority) {
                                    'tinggi' => 'danger',
                                    'sedang' => 'warning',
                                    default => 'primary',
                                };
                                $status = strtolower($item->status ?? 'aktif');
                                $statusClass = match($status) {
                                    'aktif' => 'success',
                                    'dijadwalkan' => 'info',
                                    'kadaluarsa' => 'danger',
                                    default => 'muted',
                                };
                            @endphp
                            <tr>
                                <td class="ak-main-cell" data-label="Pengumuman">
                                    <div class="ak-title">{{ $item->judul }}</div>
                                    <div class="ak-sub">{{ \Illuminate\Support\Str::limit($item->isi_pengumuman, 110) }}</div>
                                    <div class="d-flex flex-wrap gap-2 mt-2">
                                        @if($item->lampiran_surat)
                                            <x-file-preview
                                                :path="$item->lampiran_surat"
                                                label="Lampiran"
                                                class="ak-badge primary"
                                                icon="fas fa-paperclip"
                                            />
                                        @endif
                                        @if($item->kalenderAkademik)
                                            <span class="ak-badge info">
                                                <i class="fas fa-calendar"></i>
                                                {{ \Illuminate\Support\Str::limit($item->kalenderAkademik->nama_kegiatan, 26) }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center" data-label="Tanggal">
                                    {{ $item->tanggal_pengumuman?->format('d M Y') ?? '-' }}
                                </td>
                                <td class="text-center" data-label="Prioritas">
                                    <span class="ak-badge {{ $priorityClass }}">
                                        <i class="fas fa-circle"></i>
                                        {{ $item->prioritas_badge['label'] ?? ucfirst($priority) }}
                                    </span>
                                </td>
                                <td class="text-center" data-label="Sumber">
                                    @if($item->is_from_kalender)
                                        <span class="ak-badge success"><i class="fas fa-robot"></i> Auto</span>
                                    @else
                                        <span class="ak-badge primary"><i class="fas fa-pen"></i> Manual</span>
                                    @endif
                                </td>
                                <td class="text-center" data-label="Status">
                                    <span class="ak-badge {{ $statusClass }}">
                                        {{ $item->status_badge['label'] ?? ucfirst($item->status ?? 'Aktif') }}
                                    </span>
                                </td>
                                <td data-label="Aksi">
                                    <div class="ak-actions">
                                        <a href="{{ route($routePrefix . '.pengumuman.edit', $item->id) }}" class="ak-btn warning ak-icon-btn" title="Edit">
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

            @if(is_object($pengumuman) && method_exists($pengumuman, 'hasPages') && $pengumuman->hasPages())
                <div class="ak-pagination">{{ $pengumuman->links() }}</div>
            @endif
        @else
            <div class="ak-empty">
                <i class="fas fa-bullhorn"></i>
                <h5>Belum ada pengumuman</h5>
                <p>Pengumuman akan muncul otomatis dari kalender atau ditambahkan manual.</p>
                <a href="{{ route($routePrefix . '.pengumuman.create') }}" class="ak-btn primary">
                    <i class="fas fa-plus"></i> Tambah Pengumuman
                </a>
            </div>
        @endif
    </div>
</div>

@include('shared.akademik.delete-modal', [
    'modalTitle' => 'Hapus Pengumuman',
    'itemLabelId' => 'deleteAkademikName',
])

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteAkademikName').textContent = name;
    document.getElementById('deleteForm').action = @js($basePath) + '/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
