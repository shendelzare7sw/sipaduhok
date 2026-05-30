@extends('layouts.lms')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Daftar pengumuman sekolah')

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@push('styles')
<style>
* {
    box-sizing: border-box;
}

/* Full width container - no side margins */
.pengumuman-full-container {
    width: 100%;
    padding: 0;
}

.announcement-main-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 1px 2px 0 rgba(60,64,67,0.3), 0 1px 3px 1px rgba(60,64,67,0.15);
    overflow: hidden;
}

/* Filter Bar */
.filter-bar {
    background: #f8f9fa;
    border-bottom: 1px solid #e0e0e0;
    padding: 16px 24px;
}

.filter-top-row {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    flex-wrap: wrap;
}

.search-box {
    flex: 1;
    min-width: 250px;
    position: relative;
}

.search-box input {
    width: 100%;
    padding: 10px 16px 10px 40px;
    border: 1px solid #dadce0;
    border-radius: 24px;
    font-size: 14px;
    transition: all 0.2s;
}

.search-box input:focus {
    outline: none;
    border-color: #1a73e8;
    box-shadow: 0 1px 6px rgba(26, 115, 232, 0.3);
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #5f6368;
}

.date-filter-group {
    display: flex;
    gap: 8px;
    align-items: center;
}

.date-filter-group input[type="date"] {
    padding: 8px 12px;
    border: 1px solid #dadce0;
    border-radius: 4px;
    font-size: 13px;
    color: #5f6368;
}

.date-filter-group input[type="date"]:focus {
    outline: none;
    border-color: #1a73e8;
}

.filter-btn {
    padding: 8px 16px;
    border: 1px solid #dadce0;
    background: white;
    border-radius: 4px;
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
    color: #5f6368;
}

.filter-btn:hover {
    background: #f8f9fa;
    border-color: #5f6368;
}

.filter-btn.active {
    background: #1a73e8;
    border-color: #1a73e8;
    color: white;
}

/* Priority Tabs */
.priority-tabs {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #e0e0e0;
    padding: 0 24px;
}

.priority-tab {
    padding: 12px 24px;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #5f6368;
    transition: all 0.2s;
    position: relative;
    margin-bottom: -2px;
}

.priority-tab:hover {
    background: rgba(0, 0, 0, 0.04);
}

.priority-tab.active {
    color: #1a73e8;
    border-bottom-color: #1a73e8;
}

.priority-tab .tab-count {
    display: inline-block;
    margin-left: 6px;
    padding: 2px 8px;
    background: #e8f0fe;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
}

.priority-tab.active .tab-count {
    background: #1a73e8;
    color: white;
}

/* Stats Bar */
.stats-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 24px;
    background: white;
    border-bottom: 1px solid #f1f3f4;
}

.result-count {
    font-size: 14px;
    color: #5f6368;
}

.sort-dropdown {
    display: flex;
    align-items: center;
    gap: 8px;
}

.sort-dropdown select {
    padding: 6px 12px;
    border: 1px solid #dadce0;
    border-radius: 4px;
    font-size: 13px;
    color: #5f6368;
    cursor: pointer;
}

/* Announcement List */
.announcement-header {
    padding: 20px 24px;
    background: #ffffff;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.announcement-header h4 {
    margin: 0;
    font-size: 22px;
    font-weight: 400;
    color: #202124;
    display: flex;
    align-items: center;
    gap: 12px;
}

.announcement-header h4 i {
    color: #5f6368;
}

.announcement-list {
    background: #ffffff;
}

.announcement-item {
    display: flex;
    padding: 12px 24px;
    border-bottom: 1px solid #f0f0f0;
    cursor: pointer;
    text-decoration: none;
    color: inherit;
    transition: all 0.15s cubic-bezier(0.4,0.0,0.2,1);
    position: relative;
}

.announcement-item:hover {
    box-shadow: inset 1px 0 0 #dadce0, inset -1px 0 0 #dadce0, 0 1px 2px 0 rgba(60,64,67,.3), 0 1px 3px 1px rgba(60,64,67,.15);
    z-index: 1;
}

.announcement-item:last-child {
    border-bottom: none;
}

.item-priority {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    margin-right: 12px;
    flex-shrink: 0;
    margin-top: 6px;
}

.item-priority.urgent {
    background-color: #d93025;
    box-shadow: 0 0 0 3px rgba(217, 48, 37, 0.1);
}

.item-priority.high {
    background-color: #f9ab00;
    box-shadow: 0 0 0 3px rgba(249, 171, 0, 0.1);
}

.item-priority.normal {
    background-color: #1a73e8;
    box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.1);
}

.item-content {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: center;
    gap: 16px;
}

.item-sender {
    width: 180px;
    flex-shrink: 0;
    font-size: 13px;
    font-weight: 500;
    color: #202124;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.item-message {
    flex: 1;
    min-width: 0;
    display: flex;
    align-items: baseline;
    gap: 8px;
}

.item-subject {
    font-size: 13px;
    font-weight: 500;
    color: #202124;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 1;
}

.item-snippet {
    font-size: 13px;
    color: #5f6368;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    flex-shrink: 2;
}

.item-meta {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}

.item-attachment {
    color: #5f6368;
    font-size: 16px;
}

.item-date {
    font-size: 12px;
    color: #5f6368;
    white-space: nowrap;
    width: 80px;
    text-align: right;
}

.priority-badge {
    display: inline-block;
    font-size: 10px;
    font-weight: 600;
    padding: 3px 8px;
    border-radius: 3px;
    margin-right: 8px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.priority-badge.urgent {
    background-color: #fce8e6;
    color: #d93025;
}

.priority-badge.high {
    background-color: #fef7e0;
    color: #f9ab00;
}

.priority-badge.normal {
    background-color: #e8f0fe;
    color: #1a73e8;
}

.empty-inbox {
    text-align: center;
    padding: 80px 20px;
}

.empty-inbox i {
    font-size: 72px;
    color: #e0e0e0;
    margin-bottom: 16px;
}

.empty-inbox h5 {
    font-size: 20px;
    font-weight: 400;
    color: #5f6368;
    margin: 0 0 8px 0;
}

.empty-inbox p {
    font-size: 14px;
    color: #80868b;
    margin: 0;
}

.pagination-container {
    padding: 16px 24px;
    background: #ffffff;
    border-top: 1px solid #e0e0e0;
    display: flex;
    justify-content: center;
}

/* Loading State */
.loading-state {
    text-align: center;
    padding: 40px 20px;
    color: #5f6368;
}

.loading-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid #f3f3f3;
    border-top: 3px solid #1a73e8;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 12px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive */
@media (max-width: 768px) {
    .item-sender {
        width: 120px;
    }
    
    .item-snippet {
        display: none;
    }
    
    .item-date {
        width: 60px;
        font-size: 11px;
    }

    .priority-tabs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .filter-top-row {
        flex-direction: column;
    }

    .search-box {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .item-sender {
        display: none;
    }
    
    .priority-tab {
        padding: 12px 16px;
        font-size: 13px;
    }

    .stats-bar {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }
}
</style>
@endpush

@section('content')
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
                    <span style="color: #5f6368;">-</span>
                    <input type="date" id="dateTo" placeholder="Sampai tanggal">
                    <button class="filter-btn" onclick="applyDateFilter()">
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
                <span style="font-size: 13px; color: #5f6368;">Urutkan:</span>
                <select onchange="sortBy(this.value)">
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

@push('scripts')
<script>
// Priority Filter
document.querySelectorAll('.priority-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        // Update active tab
        document.querySelectorAll('.priority-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        const priority = this.dataset.priority;
        const items = document.querySelectorAll('.announcement-item');
        
        items.forEach(item => {
            if (priority === 'all') {
                item.style.display = 'flex';
            } else {
                const itemPriority = item.dataset.priority;
                const match = (priority === 'normal' && itemPriority === 'biasa') ||
                              (priority === 'high' && itemPriority === 'penting') ||
                              (priority === 'urgent' && itemPriority === 'mendesak');
                item.style.display = match ? 'flex' : 'none';
            }
        });
    });
});

// Search Filter
document.getElementById('searchInput').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const items = document.querySelectorAll('.announcement-item');
    
    items.forEach(item => {
        const subject = item.querySelector('.item-subject').textContent.toLowerCase();
        const snippet = item.querySelector('.item-snippet') ? item.querySelector('.item-snippet').textContent.toLowerCase() : '';
        
        if (subject.includes(searchTerm) || snippet.includes(searchTerm)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
});

// Placeholder functions for date filter and sort
function applyDateFilter() {
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    
    if (dateFrom && dateTo) {
        // Reload page with date parameters
        window.location.href = `{{ route('siswa.lms.pengumuman.index') }}?from=${dateFrom}&to=${dateTo}`;
    }
}

function sortBy(value) {
    window.location.href = `{{ route('siswa.lms.pengumuman.index') }}?sort=${value}`;
}
</script>
@endpush
@endsection
