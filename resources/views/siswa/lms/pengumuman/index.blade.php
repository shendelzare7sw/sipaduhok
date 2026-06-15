@extends('layouts.lms')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Daftar pengumuman sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/siswa/lms/pengumuman/index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/siswa/lms/pengumuman/index.js'])
@endpush

@section('content')
<div class="siswa-lms-pengumuman-index-page" data-index-url="{{ route('siswa.lms.pengumuman.index') }}">
    <div class="pengumuman-full-container">
    <div class="announcement-main-card">
        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="filter-top-row">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari pengumuman..." id="searchInput">
                </div>
                
                <div class="date-filter-group">
                    <input type="date" id="dateFrom" placeholder="Dari tanggal">
                    <span class="date-range-separator">-</span>
                    <input type="date" id="dateTo" placeholder="Sampai tanggal">
                    <button type="button" class="filter-btn" data-action="date-filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>
        </div>

        <!-- Priority Tabs -->
        <div class="priority-tabs">
            <button class="priority-tab active" data-priority="all">
                Semua
                <span class="tab-count">{{ $pengumumanList->total() }}</span>
            </button>
            <button class="priority-tab" data-priority="normal">
                Biasa
                <span class="tab-count">{{ $pengumumanList->where('prioritas', 'biasa')->count() }}</span>
            </button>
            <button class="priority-tab" data-priority="high">
                Penting
                <span class="tab-count">{{ $pengumumanList->where('prioritas', 'penting')->count() }}</span>
            </button>
            <button class="priority-tab" data-priority="urgent">
                Mendesak
                <span class="tab-count">{{ $pengumumanList->where('prioritas', 'mendesak')->count() }}</span>
            </button>
        </div>

        <!-- Stats Bar -->
        <div class="stats-bar">
            <div class="result-count">
                <strong>{{ $pengumumanList->total() }}</strong> pengumuman ditemukan
            </div>
            <div class="sort-dropdown">
                <span class="sort-label">Urutkan:</span>
                <select id="sortSelect">
                    <option value="newest">Terbaru</option>
                    <option value="oldest">Terlama</option>
                    <option value="priority">Prioritas</option>
                </select>
            </div>
        </div>

        @if($pengumumanList->count() > 0)
            <div class="announcement-list" id="announcementList">
                @foreach($pengumumanList as $item)
                    <a href="{{ route('siswa.lms.pengumuman.show', $item->id) }}" 
                       class="announcement-item" 
                       data-priority="{{ $item->prioritas ?? 'biasa' }}">
                        <div class="item-priority {{ $item->prioritas == 'mendesak' ? 'urgent' : ($item->prioritas == 'penting' ? 'high' : 'normal') }}"></div>
                        
                        <div class="item-content">
                            <div class="item-sender">
                                <span class="priority-badge {{ $item->prioritas == 'mendesak' ? 'urgent' : ($item->prioritas == 'penting' ? 'high' : 'normal') }}">
                                    {{ $item->prioritas == 'mendesak' ? 'MENDESAK' : ($item->prioritas == 'penting' ? 'PENTING' : 'BIASA') }}
                                </span>
                                SIPADUHOK
                            </div>
                            
                            <div class="item-message">
                                <span class="item-subject">{{ $item->judul }}</span>
                                <span class="item-snippet">- {{ Str::limit(strip_tags($item->isi_pengumuman), 80) }}</span>
                            </div>
                            
                            <div class="item-meta">
                                @if($item->lampiran_surat)
                                    <i class="fas fa-paperclip item-attachment"></i>
                                @endif
                                <span class="item-date">{{ $item->created_at->copy()->locale('id')->diffForHumans(null, true) }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if($pengumumanList->hasPages())
                <div class="pagination-container">
                    {{ $pengumumanList->links() }}
                </div>
            @endif
        @else
            <div class="empty-inbox">
                <i class="far fa-envelope-open"></i>
                <h5>Tidak ada pengumuman</h5>
                <p>Kotak masuk Anda kosong</p>
            </div>
        @endif
    </div>
</div>

</div>
@endsection
