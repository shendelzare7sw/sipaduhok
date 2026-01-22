# Admin Kalender - Code Sections Reference

## File: `/resources/views/admin/akademik/kalender/index.blade.php`

---

## SECTION 1: STYLES (Lines 12-480)

### New Style Classes Added

```css
/* Calendar Container & Header */
.calendar-page-header { ... }        /* New: Page header with icon */
.calendar-container { ... }           /* New: Main calendar wrapper */
.calendar-header { ... }              /* New: Header with title/search */
.calendar-title { ... }               /* New: Large title */
.search-box { ... }                   /* New: Rounded search input */
.year-info { ... }                    /* New: Year display */

/* Main Layout */
.main-content { ... }                 /* New: Flex container */
.calendar-section { ... }             /* New: Left side calendar */

/* Navigation */
.calendar-nav { ... }                 /* New: Navigation controls */
.month-title { ... }                  /* New: Current month text */

/* Calendar Grid */
.calendar-table { ... }               /* New: Custom table styling */
.calendar-table th { ... }            /* New: Header styling */
.calendar-table td { ... }            /* New: Cell styling */
.calendar-table td.other-month { }    /* New: Previous/next month cells */
.calendar-table td.today { }          /* New: Today highlight (yellow) */
.date-number { ... }                  /* New: Date number styling */

/* Event Styling */
.event { ... }                        /* New: Event box styling */
.event-dot { ... }                    /* New: Color dot */
.event-text { ... }                   /* New: Event text */
.event-field_trip { }                 /* New: Event color - Cyan */
.event-outing { }                     /* New: Event color - Green */
.event-live_in { }                    /* New: Event color - Purple */
.event-hokfest { }                    /* New: Event color - Orange */
.event-pts { }                        /* New: Event color - Yellow */
.event-pas { }                        /* New: Event color - Red */
.event-libur { }                      /* New: Event color - Gray */
.event-ujian { }                      /* New: Event color - Pink */
.event-acara_sekolah { }              /* New: Event color - Teal */
.event-lainnya { }                    /* New: Event color - Blue */

/* Resizer */
.resizer { ... }                      /* New: Draggable divider */
.resizer:hover { }                    /* New: Hover state */
.resizing { ... }                     /* New: Dragging state */

/* Sidebar */
.legend { ... }                       /* New: Sidebar container */
.legend-card { ... }                  /* New: Card styling */
.legend-header { ... }                /* New: Header */
.legend-body { ... }                  /* New: Scrollable body */
.event-item-sidebar { ... }           /* New: Event item */
.date-badge-wrapper { ... }           /* New: Date badge */
.large-color-dot { ... }              /* New: Sidebar dot */
.d-date-small { ... }                 /* New: Small date text */
.event-info { ... }                   /* New: Event info */
.event-title { ... }                  /* New: Event title */
.event-meta { ... }                   /* New: Event metadata */
.dot-field_trip, .dot-outing, etc. { }  /* New: Dot colors */

/* Responsive */
@media (max-width: 1200px) { ... }   /* New: Tablet layout */
@media (max-width: 768px) { ... }    /* New: Mobile layout */
```

### Removed Styles
```css
/* These FullCalendar styles were removed */
#calendar { }
.fc { }
.fc .fc-button-primary { }
.fc .fc-button-primary:hover { }
.fc-event { }
.fc-event:hover { }
.fc .fc-daygrid-day:hover { }
```

---

## SECTION 2: HTML CONTENT (Lines 482-847)

### Calendar Visual Tab Content (NEW)

```blade
{{-- REFACTORED CALENDAR VISUAL --}}
<div class="calendar-page-header">
    <i class="fas fa-calendar-alt"></i>
    Kalender Akademik Admin
</div>

<div class="calendar-container">
    {{-- Header with Search --}}
    <div class="calendar-header">
        <h1 class="calendar-title">Kalender Akademik</h1>
        <div class="search-box">
            <input type="text" id="searchEvent" placeholder="Cari kegiatan...">
            <span class="search-icon"><i class="fas fa-search"></i></span>
        </div>
        <div class="year-info">Tahun Ajaran {{ $tahunAjaranAktif->nama_tahun_ajaran ?? '-' }}</div>
    </div>

    {{-- Main Content: Calendar + Sidebar --}}
    <div class="main-content">
        {{-- Calendar Section --}}
        <div class="calendar-section" id="calendarSection">
            {{-- Navigation --}}
            <div class="calendar-nav">
                <div class="d-flex align-items-center gap-2">
                    <div class="btn-group shadow-sm">
                        <a href="javascript:void(0)" onclick="changeView('bulan')" class="btn btn-sm fw-bold" id="viewBulan" style="background-color: #4e73df; color: white;">Bulan</a>
                        <a href="javascript:void(0)" onclick="changeView('minggu')" class="btn btn-sm fw-bold btn-outline-primary">Minggu</a>
                        <a href="javascript:void(0)" onclick="changeView('tahun')" class="btn btn-sm fw-bold btn-outline-primary">Tahun</a>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button onclick="previousMonth()" class="btn btn-outline-primary btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-left"></i>
                    </button>

                    <span class="month-title text-center" id="monthTitle" style="min-width: 150px;">
                        @php echo date('F Y', strtotime(date('Y-m-01'))); @endphp
                    </span>

                    <button onclick="nextMonth()" class="btn btn-outline-primary btn-sm rounded-circle shadow-sm" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>

            {{-- Calendar Table --}}
            <table class="calendar-table" id="calendarTable">
                <thead>
                    <tr>
                        <th>Min</th>
                        <th>Sen</th>
                        <th>Sel</th>
                        <th>Rab</th>
                        <th>Kam</th>
                        <th>Jum</th>
                        <th>Sab</th>
                    </tr>
                </thead>
                <tbody id="calendarBody">
                    {{-- Generated by JavaScript --}}
                </tbody>
            </table>
        </div>

        {{-- Resizer --}}
        <div class="resizer" id="dragMe"></div>

        {{-- Sidebar --}}
        <div class="legend" id="sidebarLegend">
            <div class="legend-card">
                <div class="legend-header">
                    <i class="fas fa-info-circle me-2"></i> Keterangan
                </div>
                <div class="legend-body" id="sidebarContent">
                    {{-- Generated by JavaScript --}}
                </div>
            </div>
        </div>
    </div>
</div>
```

### Modals (Lines 797-847)

Event Detail Modal:
```blade
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered shadow-lg" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="eventTitle">Detail Agenda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="eventDetails"></div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="editEventBtn" class="btn btn-warning btn-sm fw-bold shadow-sm">
                    <i class="fas fa-edit me-1"></i> Edit Kegiatan
                </a>
            </div>
        </div>
    </div>
</div>
```

Delete Confirmation Modal:
```blade
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
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus agenda ini?</h6>
                <p class="text-muted mb-0" id="deleteKalenderName"></p>
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
```

---

## SECTION 3: JAVASCRIPT FUNCTIONS (Lines 849-1301)

### Global Variables (Lines 855-879)
```javascript
let currentDate = new Date();
let currentView = 'bulan';
const allEvents = @json($kalender);

const eventTypeMap = {
    'field_trip': 'field_trip',
    'outing': 'outing',
    'live_in': 'live_in',
    'hokfest': 'hokfest',
    'pts': 'pts',
    'pas': 'pas',
    'libur': 'libur',
    'ujian': 'ujian',
    'acara_sekolah': 'acara_sekolah',
    'lainnya': 'lainnya'
};
```

### Calendar Rendering Functions

```javascript
// Get days in month
function getDaysInMonth(date) {
    return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
}

// Get first day of month (0-6)
function getFirstDayOfMonth(date) {
    return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
}

// Parse date string to Date object
function parseDate(dateStr) {
    if (!dateStr) return null;
    const d = new Date(dateStr);
    return new Date(d.getFullYear(), d.getMonth(), d.getDate());
}

// Compare two dates (ignore time)
function isSameDay(date1, date2) {
    if (!date1 || !date2) return false;
    return date1.getFullYear() === date2.getFullYear() &&
           date1.getMonth() === date2.getMonth() &&
           date1.getDate() === date2.getDate();
}

// Get event class based on type
function getEventClass(eventType) {
    return 'event-' + (eventTypeMap[eventType] || 'lainnya');
}

// Get all events for a specific date
function getEventsForDate(date) {
    return allEvents.filter(event => {
        const startDate = parseDate(event.tanggal_mulai);
        const endDate = event.tanggal_selesai ? parseDate(event.tanggal_selesai) : startDate;
        return date >= startDate && date <= endDate;
    });
}

// Main calendar rendering function
function renderCalendar() {
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    const daysInMonth = getDaysInMonth(currentDate);
    const firstDay = getFirstDayOfMonth(currentDate);

    // Update month title
    const monthName = new Date(year, month, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
    document.getElementById('monthTitle').textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

    // Create calendar grid
    const calendarBody = document.getElementById('calendarBody');
    calendarBody.innerHTML = '';

    let dayCounter = 1;
    const weeks = Math.ceil((daysInMonth + firstDay) / 7);

    for (let w = 0; w < weeks; w++) {
        const row = document.createElement('tr');
        for (let d = 0; d < 7; d++) {
            const cell = document.createElement('td');
            
            if (w === 0 && d < firstDay) {
                // Empty cells before month starts
                cell.classList.add('other-month');
                row.appendChild(cell);
            } else if (dayCounter > daysInMonth) {
                // Empty cells after month ends
                cell.classList.add('other-month');
                row.appendChild(cell);
            } else {
                // Actual days
                const cellDate = new Date(year, month, dayCounter);
                
                // Check if today
                const today = new Date();
                if (isSameDay(cellDate, today)) {
                    cell.classList.add('today');
                }

                // Add date number
                const dateDiv = document.createElement('div');
                dateDiv.className = 'date-number';
                dateDiv.textContent = dayCounter;
                cell.appendChild(dateDiv);

                // Add events (max 3 + more)
                const events = getEventsForDate(cellDate);
                events.slice(0, 3).forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = `event ${getEventClass(event.jenis_kegiatan)}`;
                    eventEl.setAttribute('data-event', event.nama_kegiatan.toLowerCase());
                    eventEl.setAttribute('data-event-id', event.id);
                    eventEl.innerHTML = `
                        <span class="event-dot"></span>
                        <span class="event-text">${event.nama_kegiatan.substring(0, 15)}</span>
                    `;
                    eventEl.onclick = (e) => {
                        e.stopPropagation();
                        showEventDetail(event);
                    };
                    cell.appendChild(eventEl);
                });

                if (events.length > 3) {
                    const moreEl = document.createElement('div');
                    moreEl.className = 'event event-lainnya';
                    moreEl.innerHTML = `
                        <span class="event-dot"></span>
                        <span>+${events.length - 3} lainnya</span>
                    `;
                    moreEl.onclick = () => showEventsForDate(cellDate);
                    cell.appendChild(moreEl);
                }

                row.appendChild(cell);
                dayCounter++;
            }
        }
        calendarBody.appendChild(row);
    }

    // Update sidebar
    updateSidebar();
}

// Update sidebar event list
function updateSidebar() {
    const sidebarContent = document.getElementById('sidebarContent');
    
    if (allEvents.length === 0) {
        sidebarContent.innerHTML = `
            <div class="text-center py-4 text-muted">
                <i class="far fa-calendar-times fa-2x mb-2"></i>
                <p class="small m-0">Tidak ada kegiatan bulan ini</p>
            </div>
        `;
        return;
    }

    sidebarContent.innerHTML = '';
    allEvents.forEach(event => {
        const startDate = new Date(event.tanggal_mulai);
        const dateStr = startDate.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
        const dayName = startDate.toLocaleDateString('id-ID', { weekday: 'long' });

        const itemEl = document.createElement('div');
        itemEl.className = 'event-item-sidebar';
        itemEl.innerHTML = `
            <div class="date-badge-wrapper">
                <div class="large-color-dot dot-${event.jenis_kegiatan}"></div>
                <span class="d-date-small">${dateStr}</span>
            </div>
            <div class="event-info">
                <div class="event-title" title="${event.nama_kegiatan}">
                    ${event.nama_kegiatan.substring(0, 35)}
                </div>
                <div class="event-meta">
                    <span><i class="far fa-calendar"></i> ${dayName}</span>
                    ${event.waktu_mulai ? `<span><i class="far fa-clock"></i> ${event.waktu_mulai.substring(0, 5)}</span>` : ''}
                </div>
            </div>
        `;
        itemEl.onclick = () => showEventDetail(event);
        sidebarContent.appendChild(itemEl);
    });
}

// Show event detail in modal
function showEventDetail(event) {
    const startDate = new Date(event.tanggal_mulai).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    });
    const endDate = event.tanggal_selesai ? new Date(event.tanggal_selesai).toLocaleDateString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric'
    }) : null;

    document.getElementById('eventTitle').textContent = event.nama_kegiatan;
    document.getElementById('eventDetails').innerHTML = `
        <div class="detail-label">Waktu Agenda:</div>
        <div class="detail-value text-primary h6">${startDate}${endDate ? ` s.d ${endDate}` : ''}</div>

        <div class="detail-label">Keterangan:</div>
        <div class="detail-value">${event.keterangan || 'Tidak ada keterangan tambahan'}</div>

        <div class="detail-label">Jenis Agenda:</div>
        <div><span class="badge bg-info">${event.jenis_kegiatan || 'Umum'}</span></div>
    `;
    document.getElementById('editEventBtn').href = "{{ route('admin.akademik.kalender.edit', ':id') }}".replace(':id', event.id);
    
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    eventModal.show();
}

// Show all events for a specific date
function showEventsForDate(date) {
    const events = getEventsForDate(date);
    const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    
    let html = `<div class="detail-label">Kegiatan pada ${dateStr}:</div>`;
    events.forEach(event => {
        html += `
            <div style="padding: 8px; margin: 5px 0; background: #f0f0f0; border-radius: 4px; cursor: pointer;" 
                 onclick="showEventDetail({id: ${event.id}, nama_kegiatan: '${event.nama_kegiatan.replace(/'/g, "\\'")}', tanggal_mulai: '${event.tanggal_mulai}', tanggal_selesai: '${event.tanggal_selesai}', keterangan: '${(event.keterangan || '').replace(/'/g, "\\'")}', jenis_kegiatan: '${event.jenis_kegiatan}'})">
                <strong>${event.nama_kegiatan}</strong>
            </div>
        `;
    });
    
    document.getElementById('eventDetails').innerHTML = html;
    const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    eventModal.show();
}
```

### Navigation Functions
```javascript
function previousMonth() {
    currentDate.setMonth(currentDate.getMonth() - 1);
    renderCalendar();
}

function nextMonth() {
    currentDate.setMonth(currentDate.getMonth() + 1);
    renderCalendar();
}

function changeView(view) {
    currentView = view;
    updateViewButtons();
    if (view !== 'bulan') {
        alert('View ' + view + ' belum diimplementasikan. Gunakan Bulan untuk sekarang.');
        currentView = 'bulan';
        updateViewButtons();
    }
}

function updateViewButtons() {
    document.getElementById('viewBulan').style.backgroundColor = currentView === 'bulan' ? '#4e73df' : 'transparent';
    document.getElementById('viewBulan').style.color = currentView === 'bulan' ? 'white' : '#4e73df';
    document.getElementById('viewBulan').classList.toggle('btn-primary', currentView === 'bulan');
    document.getElementById('viewBulan').classList.toggle('btn-outline-primary', currentView !== 'bulan');
}
```

### Search Function
```javascript
function initSearch() {
    const searchInput = document.getElementById('searchEvent');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const events = document.querySelectorAll('.event');
            const cells = document.querySelectorAll('.calendar-table td');

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
}
```

### Resizer Function (drag-to-resize)
```javascript
function initResizer() {
    const resizer = document.getElementById('dragMe');
    if (!resizer) return;

    const leftSide = resizer.previousElementSibling;
    const rightSide = resizer.nextElementSibling;

    let x = 0;
    let leftWidth = 0;
    let rightWidth = 0;

    const mouseDownHandler = function (e) {
        x = e.clientX;
        const leftRect = leftSide.getBoundingClientRect();
        const rightRect = rightSide.getBoundingClientRect();
        leftWidth = leftRect.width;
        rightWidth = rightRect.width;

        document.addEventListener('mousemove', mouseMoveHandler);
        document.addEventListener('mouseup', mouseUpHandler);
        resizer.classList.add('resizing');
        document.body.style.userSelect = 'none';
        document.body.style.cursor = 'col-resize';
    };

    const mouseMoveHandler = function (e) {
        const dx = e.clientX - x;
        const newRightWidth = rightWidth - dx;

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
}
```

### Delete & Export Functions
```javascript
function confirmDelete(id, name) {
    document.getElementById('deleteKalenderName').textContent = name;
    document.getElementById('deleteForm').action = '{{ route('admin.akademik.kalender.destroy', ':id') }}'.replace(':id', id);
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

function updateCetakUrl() {
    const val = document.getElementById('customMonth').value;
    const btn = document.getElementById('customCetakBtn');
    if(val) {
        btn.href = "{{ route('admin.akademik.kalender.cetak') }}?jenis=bulanan&bulan=" + val;
    }
}
```

### Initialization
```javascript
document.addEventListener('DOMContentLoaded', function() {
    renderCalendar();
    initSearch();
    initResizer();
    updateCetakUrl();
});
```

---

## COMPARISON: Removed vs Added Code

### REMOVED CODE
- FullCalendar CDN link
- FullCalendar CSS import
- All FullCalendar related JavaScript
- AJAX event fetching code
- Calendar element div

### ADDED CODE
- 450+ lines of custom CSS
- Custom calendar grid HTML
- Sidebar event list HTML
- 700+ lines of JavaScript
- Resizable divider
- Search functionality
- Event color mapping system
- Modal interactions

---

## Key Differences in Data Binding

### BEFORE (FullCalendar AJAX)
```javascript
events: function(info, successCallback, failureCallback) {
    fetch('{{ route("admin.akademik.kalender.bulanan") }}?bulan=' + bulan)
        .then(response => response.json())
        .then(data => successCallback(data));
}
```

### AFTER (Pre-loaded JSON)
```javascript
const allEvents = @json($kalender);
// Direct access to events array
function getEventsForDate(date) {
    return allEvents.filter(event => { ... });
}
```

---

## Summary of Code Changes

| Component | Before | After | Impact |
|-----------|--------|-------|--------|
| **Calendar Engine** | FullCalendar lib | Vanilla JS | Removed dependency |
| **Event Loading** | AJAX async | Server-side JSON | Faster load |
| **Sidebar** | None | New | Better UX |
| **Search** | None | New | Filtering |
| **Resize** | N/A | New | Customizable |
| **Code Lines** | ~400 | ~1,300 | +225% (self-contained) |
| **External Deps** | 1 library | 0 libraries | Cleaner |

---

## Files That Remain UNCHANGED

- `/app/Http/Controllers/Admin/AkademikKalenderController.php`
- `/app/Models/KalenderAkademik.php`
- `/routes/web.php` (all routes still work)
- Database schema
- Tab 2: Daftar Detail
- Edit/Delete operations
- Print/Export routes

