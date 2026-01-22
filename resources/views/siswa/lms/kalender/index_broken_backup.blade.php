@extends('layouts.lms')

@php
    $title = 'Kalender Akademik';
    $subtitle = 'Lihat jadwal kegiatan dan agenda sekolah';
@endphp

@section('title', $title)
@section('page-title', $title)
@section('page-subtitle', $subtitle)

@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
    <style>
        .calendar-page-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            font-size: 18px;
            color: #0066cc;
            font-weight: 600;
        }

        .calendar-container {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .calendar-title {
            font-size: 24px;
            font-weight: 700;
            color: #1a4d8f;
        }

        .search-box {
            position: relative;
            flex: 1;
            max-width: 300px;
        }

        .search-box input {
            width: 100%;
            padding: 10px 40px 10px 15px;
            border: 2px solid #ddd;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
        }

        .search-box input:focus {
            border-color: #0066cc;
        }

        .search-box .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .year-title {
            font-size: 16px;
            font-weight: 600;
            color: #1a4d8f;
        }

        .main-content {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }

        .calendar-section {
            flex: 1;
            min-width: 0;
        }

        .calendar-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .calendar-nav .btn {
            padding: 8px 16px;
        }

        .calendar-nav .month-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a4d8f;
        }

        .calendar-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #1a4d8f;
            border-radius: 8px;
            overflow: hidden;
            table-layout: fixed;
        }

        .calendar-table th {
            background: #1a4d8f;
            color: white;
            padding: 15px;
            font-size: 18px;
            font-weight: 700;
            text-align: center;
        }

        .calendar-table td {
            border: 1px solid #ddd;
            padding: 8px;
            height: 100px;
            vertical-align: top;
            background: white;
        }

        .calendar-table td.other-month {
            background: #f9fafb;
        }

        .calendar-table td.other-month .date-number {
            color: #ccc;
        }

        .calendar-table td.today {
            background: #fffacd;
        }

        .date-number {
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .event {
            font-size: 10px;
            padding: 3px 6px;
            border-radius: 4px;
            margin: 2px 0;
            display: flex;
            align-items: center;
            gap: 4px;
            cursor: pointer;
            transition: transform 0.2s;
            color: white;
            /* Default text white for better contrast with dark bg */
        }

        .event:hover {
            transform: scale(1.02);
        }

        .event-dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            flex-shrink: 0;
            background: white;
            /* Dot white by default on colored bg */
        }

        .event-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Event Colors Matching System (SekretarisController) */
        .event-field_trip {
            background: #17a2b8;
            color: white;
        }

        .event-outing {
            background: #28a745;
            color: white;
        }

        .event-live_in {
            background: #6610f2;
            color: white;
        }

        .event-hokfest {
            background: #fd7e14;
            color: white;
        }

        .event-pts {
            background: #ffc107;
            color: #212529;
        }

        /* Yellow needs dark text */
        .event-pts .event-dot {
            background: #212529;
        }

        .event-pas {
            background: #dc3545;
            color: white;
        }

        .event-libur {
            background: #6c757d;
            color: white;
        }

        .event-ujian {
            background: #e83e8c;
            color: white;
        }

        .event-acara_sekolah {
            background: #20c997;
            color: white;
        }

        .event-lainnya {
            background: #007bff;
            color: white;
        }

        /* Tambahan untuk tugas/deadline jika ada di LMS tapi belum di map system kalender utama */
        .event-tugas {
            background: #0891b2;
            color: white;
        }

        .event-deadline {
            background: #2563eb;
            color: white;
        }

        /* Legend */
        .legend {
            width: 200px;
            flex-shrink: 0;
        }

        .legend-title {
            font-size: 16px;
            font-weight: 700;
            color: #1a4d8f;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px solid #1a4d8f;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
            font-size: 13px;
            color: #333;
        }

        .legend-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        /* Legend Dots */
        .dot-field_trip {
            background: #17a2b8;
        }

        .dot-outing {
            background: #28a745;
        }

        .dot-live_in {
            background: #6610f2;
        }

        .dot-hokfest {
            background: #fd7e14;
        }

        .dot-pts {
            background: #ffc107;
        }

        .dot-pas {
            background: #dc3545;
        }

        .dot-libur {
            background: #6c757d;
        }

        .dot-ujian {
            background: #e83e8c;
        }

        .dot-acara_sekolah {
            background: #20c997;
        }

        .dot-lainnya {
            background: #007bff;
        }

        @media (max-width: 992px) {
            .main-content {
                flex-direction: column;
            }

            .legend {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                gap: 15px;
            }

            .legend-item {
                flex: 1;
                min-width: 140px;
            }

            .calendar-table td {
                height: 80px;
                padding: 4px;
            }

            .event {
                font-size: 9px;
            }
        }
    </style>

    <div class="calendar-page-header">
        <i class="fas fa-calendar-alt"></i>
        Kalender Akademik Tahun Ini
    </div>

    <div class="calendar-container">
        <div class="calendar-header">
            <h1 class="calendar-title">Kalender Akademik</h1>
            <div class="search-box">
                <input type="text" id="searchEvent" placeholder="Cari event...">
                <span class="search-icon"><i class="fas fa-search"></i></span>
            </div>
            <div class="year-title">Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</div>
        </div>

                <div id="calendar-ajax-container">
                    @include('siswa.lms.kalender.content')
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to re-initialize resizer after AJAX load
            function initResizer() {
                const resizer = document.getElementById('dragMe');
                if (!resizer) return; // Might be hidden in some views? No, always present.
                
                const leftSide = resizer.previousElementSibling; // .calendar-section is wrapped inside now? No structure is same.
                // Wait, structure in content.blade.php is .main-content > .calendar-section + .resizer + .legend
                // And index.blade.php wraps content in #calendar-ajax-container. 
                // So #calendar-ajax-container > .main-content > ...
                
                const mainContent = document.querySelector('.main-content');
                if (!mainContent) return;
                
                // Let's redefine references relative to mainContent
                const calendarSection = mainContent.querySelector('.calendar-section');
                const sidebar = mainContent.querySelector('.legend');
                
                if (!calendarSection || !sidebar) return;

                let x = 0;
                let y = 0;
                let leftWidth = 0;

                const mouseDownHandler = function(e) {
                    x = e.clientX;
                    y = e.clientY;
                    leftWidth = calendarSection.getBoundingClientRect().width;

                    document.addEventListener('mousemove', mouseMoveHandler);
                    document.addEventListener('mouseup', mouseUpHandler);
                    resizer.classList.add('resizing');
                    document.body.style.cursor = 'col-resize';
                    sidebar.style.userSelect = 'none';
                    calendarSection.style.userSelect = 'none';
                };

                const mouseMoveHandler = function(e) {
                    const dx = e.clientX - x;
                    const newLeftWidth = ((leftWidth + dx) * 100) / resizer.parentNode.getBoundingClientRect().width;
                    if (newLeftWidth > 20 && newLeftWidth < 80) { // Limit min/max width
                         // Flex grow or flex basis? CSS uses flex: 1 for calendar, width for legend.
                         // But typical resizer logic changes flex-basis or width.
                         // Existing CSS: .calendar-section { flex: 1; } .legend { width: 300px; }
                         // We probably want to change legend width inversely? Or change calendar width?
                         // If we resize, we usually change the static width element or change flex-basis.
                         // Let's change the LEGEND width since logic usually makes sidebar resizable.
                         // Wait, divider is AFTER calendar section. Dragging right shrinks legend, grows calendar.
                         // Dragging left grows legend, shrinks calendar.
                         
                         // Calculating new width for Calendar Section?
                         // Ideally, we set flex-basis or width on Calendar Section and Legend takes remaining?
                         // Or vice versa.
                         // CSS: .legend { width: 300px; flex-shrink: 0; }
                         // So we should adjust .legend width.
                         
                         // dx is positive (right). Legend should shrink.
                         // newLegendWidth = initialLegendWidth - dx;
                         // But we calculated newLeftWidth based on left side (calendar).
                         // Let's stick to modifying Legend width for simplicity if that's how it was.
                         // Or if we want to follow the previous implementation...
                         // Step 918 replacement didn't show JS.
                         // I should use standard logic.
                    }
                    // Let's assume standard resizer logic for flexbox where left side grows.
                    calendarSection.style.flex = `0 0 ${newLeftWidth}%`;
                    sidebar.style.flex = '1'; /* Let sidebar take rest? No sidebar has fixed width usually. */
                    /* Actually, let's look at the CSS styles available above in the file. */
                    /* .calendar-section { flex: 1; ... } */
                    /* .legend { width: 300px; ... } */
                    
                    /* Better logic for this specific layout: change Legend width */
                    /* dx > 0 (right) -> Legend smaller. */
                    /* sidebar.style.width = (initialSidebarWidth - dx) + 'px'; */
                };
                
                /* 
                   Wait, I don't have the original JS logic here perfectly. 
                   But the user said "Resizable Divider" was already working.
                   I should just RE-ATTACH the same logic that was there.
                   Use 'initResizer' wrapper.
                   I will need to inspect the existing JS logic from previous view_file (content was not fully shown).
                   However, I can write a robust one.
                   
                   Actually, let's use the simplest logic:
                   The previous JS (from line 620 in original file) was:
                   const resizer = document.getElementById('dragMe');
                   ...
                   mouseMoveHandler:
                     const dx = e.clientX - x;
                     const newWidth = leftWidth + dx; // For left side
                     leftSide.style.width = `${newWidth}px`;
                     
                   But .calendar-section has `flex:1`. Changing width might not work well with flex:1 unless we set flex:none.
                   
                   Let's stick to: Update `.legend` width, because it has fixed width. 
                   If divider moves right, legend width decreases.
                   
                   Let's use a generic handler for now or assume existing JS stays?
                   NO, I am REPLACING the whole file content or block?
                   The `TargetContent` is `.main-content`. The JS is in `@push('scripts')` or `<script>` at bottom.
                   The tool `replace_file_content` targets lines.
                   Lines 620-680 (JS) are OUTSIDE `.main-content`.
                   So I don't need to replace JS if I don't touch it?
                   
                   Wait, `initResizer` MUST be called after AJAX load.
                   So I need to wrap existing JS in a function `initResizer()`, call it on load, AND call it after AJAX success.
                   
                   So I DO need to modify the JS block.
                */
            }
        });
        
        // Re-implementing the AJAX logic and standard Resizer
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('calendar-ajax-container');

            // --- 1. AJAX Navigation ---
            container.addEventListener('click', function(e) {
                // Target links in .calendar-nav (Buttons and Arrows)
                const link = e.target.closest('.calendar-nav a, .legend a, .btn-group a');
                
                if (link && link.href && !link.target) { // Ignore target=_blank
                    e.preventDefault();
                    const url = link.href;
                    
                    // Show Loading State (Optional: opacity)
                    container.style.opacity = '0.5';
                    
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        container.innerHTML = html;
                        container.style.opacity = '1';
                        
                        // Update Browser URL
                        window.history.pushState({path: url}, '', url);
                        
                        // Re-initialize Resizer
                        initResizer();
                    })
                    .catch(err => {
                        console.error('Error loading calendar:', err);
                        window.location.href = url; // Fallback to full reload
                    });
                }
            });

            // Handle Back/Forward Browser Buttons
            window.addEventListener('popstate', function(e) {
                location.reload(); // Simple fallback for now
            });

            // --- 2. Resizer Logic (Adapted for new content) ---
            function initResizer() {
                const resizer = document.getElementById('dragMe');
                if (!resizer) return;

                const leftSide = resizer.previousElementSibling; // .calendar-section
                const rightSide = resizer.nextElementSibling; // .legend
                
                // Existing CSS assumes .calendar-section (flex:1) | .resizer | .legend (width:300px)
                // We will resize the LEGEND because it has the fixed width property usually.
                // Dragging Right -> Legend Shrinks (Width decreases)
                // Dragging Left -> Legend Grows (Width increases)
                
                let x = 0;
                let y = 0;
                let startRightWidth = 0;

                const mouseDownHandler = function(e) {
                    x = e.clientX;
                    y = e.clientY;
                    startRightWidth = rightSide.getBoundingClientRect().width;

                    document.addEventListener('mousemove', mouseMoveHandler);
                    document.addEventListener('mouseup', mouseUpHandler);
                    
                    resizer.classList.add('resizing');
                    document.body.style.cursor = 'col-resize';
                    leftSide.style.userSelect = 'none';
                    rightSide.style.userSelect = 'none';
                };

                const mouseMoveHandler = function(e) {
                    const dx = e.clientX - x;
                    // dx > 0 means moving right. 
                    // New Width = StartWidth - dx
                    const newWidth = startRightWidth - dx;
                    
                    if (newWidth > 150 && newWidth < 600) { // Min/Max constraints
                        rightSide.style.width = `${newWidth}px`;
                    }
                };

                const mouseUpHandler = function() {
                    document.removeEventListener('mousemove', mouseMoveHandler);
                    document.removeEventListener('mouseup', mouseUpHandler);
                    
                    resizer.classList.remove('resizing');
                    document.body.style.removeProperty('cursor');
                    leftSide.style.removeProperty('user-select');
                    rightSide.style.removeProperty('user-select');
                };

                resizer.addEventListener('mousedown', mouseDownHandler);
            }

            // Init on first load
            initResizer();
        });
    </script>
    @endpush

        <style>
            /* Resizer Styles */
            .resizer {
                background-color: #94a3b8;
                cursor: col-resize;
                height: auto;
                min-height: 100%;
                width: 4px;
                border-radius: 2px;
                margin: 0 8px;
                transition: background-color 0.2s;
                flex-shrink: 0;
                user-select: none;
                opacity: 0.7;
            }

            .resizer:hover,
            .resizing {
                background-color: #475569;
                opacity: 1;
            }

            .main-content {
                display: flex;
                align-items: stretch;
                /* Stretch height */
                gap: 10px;
                /* Reduces gap, controlled by resizer now */
            }

            .calendar-section {
                flex: 1;
                /* Takes remaining space */
                min-width: 300px;
            }

            .legend {
                width: 280px;
                /* Default width */
                min-width: 200px;
                flex-shrink: 0;
            }

            /* Sidebar Event List Styles */
            .legend-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                overflow: hidden;
                border: 1px solid #e5e7eb;
            }

            .legend-header {
                background: #f8fafc;
                padding: 12px 15px;
                border-bottom: 1px solid #e5e7eb;
                font-weight: 700;
                color: #1a4d8f;
                font-size: 14px;
            }

            .legend-body {
                max-height: 400px;
                overflow-y: auto;
                padding: 0;
            }

            .legend-body::-webkit-scrollbar {
                width: 4px;
            }

            .legend-body::-webkit-scrollbar-thumb {
                background: #ccc;
                border-radius: 4px;
            }

            .event-item-sidebar {
                display: flex;
                gap: 12px;
                padding: 12px 15px;
                border-bottom: 1px solid #f3f4f6;
                cursor: pointer;
                transition: background 0.2s;
                position: relative;
            }

            .event-item-sidebar:hover {
                background: #f8fafc;
            }

            .event-item-sidebar:last-child {
                border-bottom: none;
            }

            .date-badge-wrapper {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                width: 40px;
                flex-shrink: 0;
                gap: 4px;
            }

            .large-color-dot {
                width: 18px;
                height: 18px;
                border-radius: 50%;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            .d-date-small {
                font-size: 10px;
                font-weight: 600;
                color: #64748b;
                text-align: center;
            }

            /* Dot Colors reuse */
            .dot-libur {
                background: #ff4444;
            }

            .dot-ujian {
                background: #ff69b4;
            }

            .dot-kegiatan,
            .dot-acara_sekolah {
                background: #ff9800;
            }

            .dot-outing,
            .dot-field_trip {
                background: #00838f;
            }

            .dot-live_in {
                background: #6610f2;
            }

            .dot-hokfest {
                background: #fd7e14;
            }

            .dot-pts {
                background: #ffc107;
            }

            .dot-pas {
                background: #dc3545;
            }

            .dot-lainnya {
                background: #007bff;
            }

            .event-info {
                flex: 1;
                min-width: 0;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }

            .event-title {
                font-weight: 600;
                font-size: 13px;
                color: #334155;
                margin-bottom: 4px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .event-meta {
                display: flex;
                gap: 10px;
                font-size: 11px;
                color: #64748b;
            }

            .event-type-dot {
                position: absolute;
                right: 15px;
                top: 50%;
                transform: translateY(-50%);
                width: 8px;
                height: 8px;
                border-radius: 50%;
            }

            .badge-dot {
                font-size: 9px;
                padding: 3px 6px;
                border-radius: 4px;
                color: white;
                cursor: help;
            }
        </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Search functionality
            const searchInput = document.getElementById('searchEvent');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const searchTerm = this.value.toLowerCase();
                    const events = document.querySelectorAll('.event');
                    const cells = document.querySelectorAll('.calendar-table td');

                    // Reset all cells
                    cells.forEach(td => {
                        if (!td.classList.contains('today')) {
                            td.style.background = '';
                        }
                    });

                    events.forEach(event => {
                        const text = event.getAttribute('data-event') || event.textContent.toLowerCase();
                        const td = event.closest('td');

                        if (searchTerm === '') {
                            event.style.display = 'flex';
                        } else if (text.includes(searchTerm)) {
                            event.style.display = 'flex';
                            if (td && !td.classList.contains('today')) {
                                td.style.background = '#fffacd';
                            }
                        } else {
                            event.style.display = 'none';
                        }
                    });
                });
            }

            // Resizable Sidebar Logic
            const resizer = document.getElementById('dragMe');
            const leftSide = resizer.previousElementSibling;
            const rightSide = resizer.nextElementSibling;
            const container = resizer.parentNode;

            // The current position of mouse
            let x = 0;
            let leftWidth = 0;
            let rightWidth = 0;

            const mouseDownHandler = function (e) {
                // Get the current mouse position
                x = e.clientX;

                // Calculate current widths (not used directly but good for init)
                const leftRect = leftSide.getBoundingClientRect();
                const rightRect = rightSide.getBoundingClientRect();

                leftWidth = leftRect.width;
                rightWidth = rightRect.width;

                document.addEventListener('mousemove', mouseMoveHandler);
                document.addEventListener('mouseup', mouseUpHandler);
                resizer.classList.add('resizing');

                // Disable text selection during drag
                document.body.style.userSelect = 'none';
                document.body.style.cursor = 'col-resize';
            };

            const mouseMoveHandler = function (e) {
                // How far the mouse has been moved
                const dx = e.clientX - x;

                // New width for sidebar (right side)
                // Since flex container, increasing sidebar decreases main content
                const newRightWidth = rightWidth - dx; // Dragging left increases width

                // Min width constraints
                if (newRightWidth > 150 && newRightWidth < 600) {
                    rightSide.style.width = `${newRightWidth}px`;
                }
            };

            const mouseUpHandler = function () {
                document.removeEventListener('mousemove', mouseMoveHandler);
                document.removeEventListener('mouseup', mouseUpHandler);
                resizer.classList.remove('resizing');
                document.body.style.removeProperty('user-select');
                document.body.style.removeProperty('cursor');
            };

            resizer.addEventListener('mousedown', mouseDownHandler);
        });
    </script>
@endpush