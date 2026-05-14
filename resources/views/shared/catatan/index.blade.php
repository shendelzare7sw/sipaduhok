@php
    $routePrefix = $routePrefix ?? 'ketua';
    $basePath = $basePath ?? '/' . str_replace('.', '/', $routePrefix) . '/catatan';
    $catatanItems = is_object($catatan) && method_exists($catatan, 'getCollection') ? $catatan->getCollection() : collect($catatan);
    $totalCatatan = is_object($catatan) && method_exists($catatan, 'total') ? $catatan->total() : $catatanItems->count();
    $totalSemua = $catatanItems->where('tipe_penerima', 'semua')->count();
    $totalMendesak = $catatanItems->where('prioritas', 'mendesak')->count();
    $totalPembaca = $catatanItems->sum(function ($item) {
        return $item->relationLoaded('pembaca') ? $item->pembaca->count() : $item->totalPembaca();
    });
@endphp

<div class="catatan-page">
    @if(session('success'))
        <div class="catatan-alert success">
            <i class="fas fa-check-circle mt-1"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="catatan-toolbar">
        <div class="catatan-toolbar-title">
            <span class="catatan-toolbar-icon"><i class="fas fa-clipboard-list"></i></span>
            <div>
                <h5>Manajemen Catatan</h5>
                <p>Kelola riwayat catatan, instruksi, dan teguran yang sudah dikirim.</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.catatan.create') }}" class="catatan-btn primary">
            <i class="fas fa-plus"></i>
            Buat Catatan
        </a>
    </div>

    <div class="catatan-stats">
        <div class="catatan-stat primary">
            <span>Total Catatan</span>
            <strong>{{ number_format($totalCatatan) }}</strong>
            <small>Semua riwayat terkirim</small>
            <i class="fas fa-paper-plane"></i>
        </div>
        <div class="catatan-stat success">
            <span>Publik</span>
            <strong>{{ number_format($totalSemua) }}</strong>
            <small>Catatan ke semua pengguna</small>
            <i class="fas fa-bullhorn"></i>
        </div>
        <div class="catatan-stat warning">
            <span>Total Dibaca</span>
            <strong>{{ number_format($totalPembaca) }}</strong>
            <small>Akumulasi pembaca halaman ini</small>
            <i class="fas fa-eye"></i>
        </div>
        <div class="catatan-stat purple">
            <span>Mendesak</span>
            <strong>{{ number_format($totalMendesak) }}</strong>
            <small>Butuh perhatian cepat</small>
            <i class="fas fa-exclamation-triangle"></i>
        </div>
    </div>

    <div class="catatan-panel">
        <div class="catatan-panel-header">
            <h5><i class="fas fa-history text-primary me-2"></i>Riwayat Catatan Terkirim</h5>
        </div>
        <div class="catatan-panel-body">
            @if($catatanItems->count() > 0)
                <div class="catatan-list">
                    @foreach($catatanItems as $item)
                        @php
                            $sentAt = $item->tanggal_kirim ? \Carbon\Carbon::parse($item->tanggal_kirim) : $item->created_at;
                            $readCount = $item->relationLoaded('pembaca') ? $item->pembaca->count() : $item->totalPembaca();
                            $priority = $item->prioritas ?: 'biasa';
                        @endphp

                        <article class="catatan-note-card priority-{{ $priority }}">
                            <div class="catatan-note-top">
                                <div class="catatan-note-title">
                                    <h6>{{ $item->judul }}</h6>
                                    <div class="catatan-meta">
                                        <span><i class="far fa-clock me-1"></i>{{ $sentAt?->format('d M Y, H:i') }}</span>
                                        <span><i class="fas fa-layer-group me-1"></i>{{ ucfirst($priority) }}</span>
                                    </div>
                                </div>

                                @if($item->tipe_penerima === 'semua')
                                    <span class="catatan-badge semua"><i class="fas fa-users"></i> Semua Pengguna</span>
                                @elseif($item->tipe_penerima === 'role')
                                    <span class="catatan-badge role"><i class="fas fa-user-tag"></i> {{ ucwords(str_replace('_', ' ', $item->role_penerima)) }}</span>
                                @else
                                    <span class="catatan-badge individu"><i class="fas fa-user"></i> {{ $item->penerima->name ?? 'Individu' }}</span>
                                @endif
                            </div>

                            <div class="catatan-excerpt">
                                {{ \Illuminate\Support\Str::limit($item->isi_catatan, 220) }}
                            </div>

                            <div class="catatan-note-footer">
                                <span class="catatan-read-chip">
                                    <i class="fas fa-chart-line"></i>
                                    Dibaca {{ number_format($readCount) }} pengguna
                                </span>

                                <div class="catatan-actions">
                                    <a href="{{ route($routePrefix . '.catatan.show', $item->id) }}" class="catatan-btn secondary">
                                        <i class="fas fa-eye"></i>
                                        Detail
                                    </a>
                                    <button type="button"
                                        class="catatan-btn danger"
                                        onclick="confirmDeleteCatatan({{ $item->id }}, @js($item->judul))">
                                        <i class="fas fa-trash"></i>
                                        Hapus
                                    </button>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                @if(is_object($catatan) && method_exists($catatan, 'hasPages') && $catatan->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $catatan->links() }}
                    </div>
                @endif
            @else
                <div class="catatan-empty">
                    <i class="fas fa-inbox"></i>
                    <h5>Belum ada catatan terkirim</h5>
                    <p class="mb-3">Catatan yang Anda buat akan tampil sebagai riwayat di halaman ini.</p>
                    <a href="{{ route($routePrefix . '.catatan.create') }}" class="catatan-btn primary">
                        <i class="fas fa-plus"></i>
                        Kirim Catatan Pertama
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="deleteCatatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 430px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hapus Catatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-1">Hapus catatan "<strong id="deleteCatatanJudul"></strong>" dari riwayat?</p>
                <p class="text-muted small mb-0">Catatan yang sudah terkirim ke penerima tidak akan terpengaruh.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="catatan-btn secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="button" class="catatan-btn danger" id="confirmDeleteCatatanBtn">
                    <i class="fas fa-trash"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteCatatanForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>
