# Admin Kalender Refactored - Quick Reference

## File Location
📄 `/resources/views/admin/akademik/kalender/index.blade.php`

## Major Sections

### 1. STYLES (Lines 12-480)
Complete CSS styling untuk:
- Calendar header & container
- Navigation controls
- Calendar grid (table styles)
- Event styling & color classes
- Sidebar legend
- Resizable divider
- Responsive breakpoints
- Modal & table styling

### 2. CONTENT (Lines 482-795)

#### Stats Cards (Lines 485-537)
- Total Kegiatan
- Kegiatan Aktif
- Draft (Belum Rilis)
- Tahun Ajaran

#### Card Header (Lines 539-558)
- Title: "Agenda & Kalender Akademik"
- Print/Export dropdown
- Tambah Agenda button

#### Tabs (Lines 559-564)
- Tab 1: Kalender Visual (REFACTORED)
- Tab 2: Daftar Detail (UNCHANGED)

#### Calendar Visual Tab (Lines 566-733)
**Header:**
- Page header dengan icon
- Calendar title
- Search box (realtime filtering)
- Year info

**Main Content:**
- Navigation (view mode + prev/next buttons)
- Calendar table (grid layout)
- Resizable divider
- Sidebar with event list

**Details List Tab (Lines 735-795):**
- Table dengan columns: NO, NAMA KEGIATAN, JENIS, TANGGAL, STATUS, AKSI
- Edit & Delete buttons per item

#### Modals (Lines 797-847)
1. **Event Detail Modal** - Tampilkan info kegiatan
2. **Delete Confirmation Modal** - Konfirmasi hapus

### 3. SCRIPTS (Lines 849-1301)

#### Global Variables (Lines 855-879)
- `currentDate`: Current month/year
- `currentView`: Active view mode ('bulan', 'minggu', 'tahun')
- `allEvents`: Array dari semua events

#### Calendar Rendering Functions (Lines 884-1026)
| Function | Purpose |
|----------|---------|
| `getDaysInMonth()` | Get jumlah hari dalam bulan |
| `getFirstDayOfMonth()` | Get hari pertama bulan |
| `parseDate()` | Parse tanggal string |
| `isSameDay()` | Compare dua tanggal |
| `getEventClass()` | Get CSS class untuk event type |
| `getEventsForDate()` | Filter events untuk tanggal tertentu |
| `renderCalendar()` | Generate calendar grid |
| `updateSidebar()` | Update sidebar event list |
| `showEventDetail()` | Tampilkan event detail modal |
| `showEventsForDate()` | Tampilkan multiple events satu hari |

#### Navigation Functions (Lines 1031-1055)
| Function | Purpose |
|----------|---------|
| `previousMonth()` | Pindah ke bulan sebelumnya |
| `nextMonth()` | Pindah ke bulan berikutnya |
| `changeView()` | Switch view mode |
| `updateViewButtons()` | Update button states |

#### Search Functions (Lines 1060-1091)
| Function | Purpose |
|----------|---------|
| `initSearch()` | Setup search listener |

#### Resizer Functions (Lines 1096-1146)
| Function | Purpose |
|----------|---------|
| `initResizer()` | Setup drag-to-resize |

#### Delete & Export Functions (Lines 1151-1170)
| Function | Purpose |
|----------|---------|
| `confirmDelete()` | Show delete modal |
| `updateCetakUrl()` | Update PDF export URL |

#### Initialization (Lines 1175-1181)
- DOM ready listener
- Call `renderCalendar()`
- Call `initSearch()`
- Call `initResizer()`
- Call `updateCetakUrl()`

---

## Key Features

### ✅ Working
- [x] Custom grid calendar (bulan view)
- [x] Event color coding sesuai jenis
- [x] Today highlight (kuning)
- [x] Search/filter events realtime
- [x] Resizable sidebar divider
- [x] Event detail modal
- [x] Delete confirmation
- [x] Edit/Delete dari table
- [x] Print/Export PDF
- [x] Stats cards
- [x] Responsive design
- [x] Mobile friendly

### 🔄 Placeholder/Future
- [ ] Minggu view (TODO)
- [ ] Tahun view (TODO)
- [ ] AJAX navigation (optional)
- [ ] Event creation modal (optional)

---

## Color Reference

```css
/* Event Types */
.event-field_trip    → #17a2b8 (Cyan)
.event-outing        → #28a745 (Green)
.event-live_in       → #6610f2 (Purple)
.event-hokfest       → #fd7e14 (Orange)
.event-pts           → #ffc107 (Yellow/Black text)
.event-pas           → #dc3545 (Red)
.event-libur         → #6c757d (Gray)
.event-ujian         → #e83e8c (Pink)
.event-acara_sekolah → #20c997 (Teal)
.event-lainnya       → #4e73df (Blue)

/* Components */
today background     → #fffacd (Light yellow)
calendar header      → #1a4d8f (Dark blue)
search/nav buttons   → #4e73df (Primary blue)
```

---

## Data Flow

```
Blade View (Events Array)
        ↓
@json($kalender) → JavaScript allEvents
        ↓
renderCalendar() → Generate HTML grid
        ↓
Display: Calendar + Sidebar + Modals
        ↓
User Interactions:
- Click event → showEventDetail()
- Click date → showEventsForDate()
- Drag resizer → Resize sidebar
- Type search → Filter events
- Click prev/next → previousMonth()/nextMonth()
- Click edit → redirect to edit page
- Click delete → confirmDelete() → Delete modal
```

---

## Responsive Breakpoints

| Screen Size | Layout | Changes |
|------------|--------|---------|
| > 1200px | Desktop | Calendar left, sidebar right, resizer active |
| 768-1200px | Tablet | Column layout, no resizer, reduced heights |
| < 768px | Mobile | Single column, compact header, small fonts |

---

## Template Variables Used

```blade
{{ $kalender }}              → Array semua kalender/events
{{ $tahunAjaranAktif }}      → Current academic year
route('admin.akademik.kalender.create')    → Tambah agenda
route('admin.akademik.kalender.edit', id)  → Edit agenda
route('admin.akademik.kalender.destroy', id) → Delete agenda
route('admin.akademik.kalender.cetak')     → Export PDF
```

---

## Browser Compatibility

- ✅ Chrome/Chromium (latest)
- ✅ Firefox (latest)
- ✅ Safari (latest)
- ✅ Edge (latest)
- ✅ Mobile browsers

**Requirements:**
- ES6 JavaScript support
- Flexbox support
- Bootstrap 5

---

## Development Tips

### Add New Event Type Color
1. Add class in CSS (line ~400):
   ```css
   .event-my_type { background: #HEXCOLOR; color: white; }
   .dot-my_type { background: #HEXCOLOR; }
   ```

2. Update `eventTypeMap` in JS (line ~875):
   ```js
   'my_type': 'my_type',
   ```

### Customize Calendar Header
Edit lines 600-607 untuk mengubah header content

### Modify Cell Height
Change `height: 100px` di `.calendar-table td` (line ~269)

### Adjust Sidebar Width
Change `width: 300px` di `.legend` (line ~334) dan constraints di `initResizer()` (line ~1110)

---

## Performance Notes

- **Calendar rendering**: O(n) where n = days in month (31 max)
- **Event filtering**: O(m) where m = total events
- **Search**: O(n*m) but acceptable dengan debouncing
- **Memory**: Events stored di `allEvents` array (no pagination needed untuk admin)
- **No AJAX**: All data loaded server-side, faster initial render

---

## Maintenance

### Common Issues & Solutions

**Issue**: Calendar not rendering
- **Check**: Is `calendarBody` element present?
- **Check**: Is `allEvents` populated?
- **Debug**: `console.log(allEvents)` di DevTools

**Issue**: Events not showing
- **Check**: Event dates format (YYYY-MM-DD)
- **Check**: Event type matches color mapping
- **Debug**: `console.log(getEventsForDate(new Date()))`

**Issue**: Resizer not working
- **Check**: Is resizer element present?
- **Check**: Is JavaScript initialized?
- **Debug**: Check for JS errors di console

**Issue**: Search not filtering
- **Check**: Is search input `id="searchEvent"`?
- **Check**: Are events have `data-event` attribute?
- **Debug**: `console.log(searchTerm)` di search handler

---

## File History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | Jan 22, 2026 | ✅ Refactored from FullCalendar to custom grid |
| 0.9 | Jan 21, 2026 | Original FullCalendar implementation |

---

## Related Files

- 📄 Siswa Calendar Reference: `/resources/views/siswa/lms/kalender/index.blade.php`
- 📄 Controller: `/app/Http/Controllers/Admin/AkademikKalenderController.php`
- 📄 Model: `/app/Models/KalenderAkademik.php`
- 📄 Routes: `/routes/web.php` (admin.akademik.kalender.*)

