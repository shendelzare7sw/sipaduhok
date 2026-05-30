@php
    /** @var \Illuminate\Support\Collection $items */
    $items = $items ?? collect();
    $grouped = $items->groupBy('mata_pelajaran_id');
@endphp

@if($items->isEmpty())
    <div class="empty-state">
        <i class="fas fa-folder-open empty-icon"></i>
        <p class="text-muted mb-1">{{ $emptyText ?? 'Belum ada konten.' }}</p>
        <small class="text-muted">Konten akan muncul di sini setelah guru mengupload.</small>
    </div>
@else
    @foreach($grouped as $mapelId => $mapelItems)
        @php $firstItem = $mapelItems->first(); $mapel = $firstItem->mataPelajaran ?? null; @endphp
        <div class="mapel-group">
            <div class="mapel-group-header">
                <div class="mapel-group-icon"><i class="fas fa-book-open"></i></div>
                <div>
                    <h6 class="mapel-group-title">{{ $mapel->nama_mapel ?? 'Mata Pelajaran' }}</h6>
                    <p class="mapel-group-count">{{ $mapelItems->count() }} item</p>
                </div>
            </div>
            <div class="konten-list">
                @foreach($mapelItems as $item)
                    @php
                        $judul = $item->{$titleField} ?? '-';
                        $tanggal = $item->{$dateField} ?? null;
                        $badgeText = is_callable($badgeText) ? $badgeText($item) : $badgeText;
                    @endphp
                    <div class="konten-item">
                        <div class="konten-icon {{ $iconBg }}">
                            <i class="fas {{ $iconClass }}"></i>
                        </div>
                        <div class="konten-info">
                            <h6 class="konten-title">{{ $judul }}</h6>
                            <div class="konten-meta">
                                <span class="konten-badge {{ $badgeClass }}">{{ $badgeText }}</span>
                                @if($item->guru)
                                    <span><i class="fas fa-user-tie me-1"></i>{{ $item->guru->nama_lengkap }}</span>
                                @endif
                                @if($tanggal)
                                    <span><i class="fas fa-calendar me-1"></i>{{ $dateLabel }}: {{ \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y') }}</span>
                                @endif
                                @if($type === 'ujian' && isset($item->soal_ujian_count))
                                    <span><i class="fas fa-list-ol me-1"></i>{{ $item->soal_ujian_count }} soal</span>
                                @endif
                            </div>
                        </div>
                        <div class="konten-actions">
                            <a href="{{ route($baseRoute . '.preview', [$type, $item->id]) }}"
                               class="btn-konten btn-konten-preview"
                               target="_blank">
                                <i class="fas fa-eye"></i><span>Pratinjau</span>
                            </a>
                            <button type="button"
                                class="btn-konten btn-konten-catatan"
                                data-monitoring-catatan
                                data-konten-type="{{ $type }}"
                                data-konten-id="{{ $item->id }}"
                                data-konten-label="{{ $badgeText }}"
                                data-konten-judul="{{ $judul }}">
                                <i class="fas fa-comment-dots"></i><span>Catatan</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
@endif
