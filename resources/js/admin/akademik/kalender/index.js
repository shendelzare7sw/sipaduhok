// ==========================================
    // 1. GLOBAL VARIABLES
    // ==========================================
    let currentDate = new Date();
    let currentView = 'bulan';
    
    // Robust data ingestion: Handle case where data might be paginated (inside .data property)
    const rawData = JSON.parse(document.getElementById('calendarData')?.textContent || '[]');
    const calendarConfig = document.getElementById('calendarConfig')?.dataset || {};
    const sourceEvents = Array.isArray(rawData) ? rawData : (rawData.data || []);
    let allEvents = [...sourceEvents]; // Mutable array for filtering

    // Map event types to class names
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

    // SEARCH LISTENER
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchEvent');
        if(searchInput) {
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.toLowerCase();
                if(query) {
                    allEvents = sourceEvents.filter(ev => 
                        (ev.nama_kegiatan && ev.nama_kegiatan.toLowerCase().includes(query)) ||
                        (ev.keterangan && ev.keterangan.toLowerCase().includes(query))
                    );
                } else {
                    allEvents = [...sourceEvents];
                }
                
                // Re-render
                renderCalendar();
                updateSidebar();
            });
        }
    });

    // ==========================================
    // 2. CALENDAR GENERATION FUNCTIONS
    // ==========================================
    function getDaysInMonth(date) {
        return new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate();
    }

    function getFirstDayOfMonth(date) {
        return new Date(date.getFullYear(), date.getMonth(), 1).getDay();
    }

    function parseDate(dateStr) {
        if (!dateStr) return null;
        
        // Handle ISO strings (e.g., 2025-01-20T00:00:00.000000Z) by taking just the date part
        if (dateStr.includes('T')) {
            dateStr = dateStr.split('T')[0];
        } else if (dateStr.includes(' ')) {
            dateStr = dateStr.split(' ')[0];
        }

        const parts = dateStr.split('-');
        if (parts.length !== 3) return null;
        
        // Ensure parts are integers
        const year = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10);
        const day = parseInt(parts[2], 10);
        
        if (isNaN(year) || isNaN(month) || isNaN(day)) return null;

        return new Date(year, month - 1, day);
    }

    function isSameDay(date1, date2) {
        if (!date1 || !date2) return false;
        return date1.getFullYear() === date2.getFullYear() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getDate() === date2.getDate();
    }

    function getEventClass(eventType) {
        return 'event-' + (eventTypeMap[eventType] || 'lainnya');
    }

    function getEventsForDate(date) {
        if (!date) return [];
        
        return allEvents.filter(event => {
            if (!event.tanggal_mulai) return false;
            
            const startDate = parseDate(event.tanggal_mulai);
            if (!startDate) return false;
            
            const endDate = event.tanggal_selesai ? parseDate(event.tanggal_selesai) : startDate;
            if (!endDate) return false;

            // Normalize dates for comparison (remove time component)
            const checkDate = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            const normalizedStart = new Date(startDate.getFullYear(), startDate.getMonth(), startDate.getDate());
            const normalizedEnd = new Date(endDate.getFullYear(), endDate.getMonth(), endDate.getDate());

            return checkDate >= normalizedStart && checkDate <= normalizedEnd;
        });
    }

    function renderCalendar() {
        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();
        const daysInMonth = getDaysInMonth(currentDate);
        const firstDay = getFirstDayOfMonth(currentDate);

        // Update month title
        const monthName = new Date(year, month, 1).toLocaleDateString('id-ID', { month: 'long', year: 'numeric' });
        document.getElementById('monthTitle').textContent = monthName.charAt(0).toUpperCase() + monthName.slice(1);

        // Ensure calendar table structure is correct (restore if needed)
        const calendarSection = document.getElementById('calendarSection');
        let calendarTable = document.getElementById('calendarTable');
        
        // robustly check if we need to rebuild the table
        // We need to rebuild if:
        // 1. Table doesn't exist
        // 2. It's not a TABLE tag (e.g. it's the year view GRID div)
        // 3. It doesn't have the correct structure (missing #calendarBody)
        const needsRebuild = !calendarTable || 
                             calendarTable.tagName !== 'TABLE' || 
                             !document.getElementById('calendarBody');

        if (needsRebuild) {
            // Remove existing element if it exists
            if (calendarTable) {
                calendarTable.remove();
            }
            
            // Create new table structure
            calendarTable = document.createElement('table');
            calendarTable.id = 'calendarTable';
            calendarTable.className = 'calendar-table';
            calendarTable.style.tableLayout = 'fixed';
            
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const dayHeaders = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            dayHeaders.forEach(day => {
                const th = document.createElement('th');
                th.textContent = day;
                headerRow.appendChild(th);
            });
            thead.appendChild(headerRow);
            calendarTable.appendChild(thead);
            
            const tbody = document.createElement('tbody');
            tbody.id = 'calendarBody';
            calendarTable.appendChild(tbody);
            
            // Insert table into calendar section
            // Try to find where to insert it (after nav)
            const calendarNav = calendarSection.querySelector('.calendar-nav');
            if (calendarNav && calendarNav.nextSibling) {
                calendarSection.insertBefore(calendarTable, calendarNav.nextSibling);
            } else {
                calendarSection.appendChild(calendarTable);
            }
        }

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

                    // Add events
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

    function renderWeekView() {
        // Generate week dates
        const dayOfWeek = currentDate.getDay();
        const startOfWeek = new Date(currentDate);
        startOfWeek.setDate(currentDate.getDate() - dayOfWeek);

        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6);

        // Update title
        const startStr = startOfWeek.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        const endStr = endOfWeek.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
        document.getElementById('monthTitle').textContent = `Minggu: ${startStr} - ${endStr}`;

        // Build week grid - ensure we have a table element
        const calendarSection = document.getElementById('calendarSection');
        let calendarTable = document.getElementById('calendarTable');
        
        // Remove existing element if it's not a table or doesn't exist
        if (calendarTable && calendarTable.tagName !== 'TABLE') {
            calendarTable.remove();
            calendarTable = null;
        }
        
        // Create new table for week view
        const newTable = document.createElement('table');
        newTable.id = 'calendarTable';
        newTable.className = 'calendar-table';
        newTable.style.tableLayout = 'fixed';

        // Create header with days of week
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        headerRow.style.backgroundColor = '#1a4d8f';
        headerRow.style.color = 'white';

        // Time column header
        const timeHeader = document.createElement('th');
        timeHeader.style.width = '80px';
        timeHeader.style.padding = '8px'; // Reduced padding
        timeHeader.style.fontSize = '0.75rem'; // Small font for Waktu
        timeHeader.textContent = 'Waktu';
        headerRow.appendChild(timeHeader);

        // Day columns
        const dayNames = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        for (let d = 0; d < 7; d++) {
            const date = new Date(startOfWeek);
            date.setDate(startOfWeek.getDate() + d);

            const th = document.createElement('th');
            th.style.padding = '8px'; // Reduced padding
            th.style.textAlign = 'center';
            th.style.borderBottom = '2px solid white';

            const isToday = isSameDay(date, new Date());
            if (isToday) {
                th.style.backgroundColor = '#FFF9C4';
                th.style.color = '#333';
            }

            // Smaller fonts: 0.85rem for Day Name, 0.75rem for Date
            th.innerHTML = `<div style="font-size: 0.85rem; font-weight: 600;">${dayNames[d]}</div><div style="font-weight: 400; font-size: 0.75rem;">${date.getDate()}/${(date.getMonth() + 1).toString().padStart(2, '0')}</div>`;
            headerRow.appendChild(th);
        }
        thead.appendChild(headerRow);
        newTable.appendChild(thead);

        // Create tbody with time slots
        const tbody = document.createElement('tbody');

        // All day row
        const allDayRow = document.createElement('tr');
        const allDayTimeCell = document.createElement('td');
        allDayTimeCell.style.padding = '8px';
        allDayTimeCell.style.backgroundColor = '#f5f5f5';
        allDayTimeCell.style.fontWeight = 'bold';
        allDayTimeCell.style.fontSize = '12px';
        allDayTimeCell.style.textAlign = 'center';
        allDayTimeCell.textContent = 'All Day';
        allDayRow.appendChild(allDayTimeCell);

        for (let d = 0; d < 7; d++) {
            const date = new Date(startOfWeek);
            date.setDate(startOfWeek.getDate() + d);

            const cell = document.createElement('td');
            cell.style.padding = '8px';
            cell.style.minHeight = '50px';
            cell.style.verticalAlign = 'top';

            const isToday = isSameDay(date, new Date());
            if (isToday) {
                cell.style.backgroundColor = 'rgba(255, 249, 196, 0.3)';
            }

            // All day events
            const events = getEventsForDate(date);
            const allDayEvents = events.filter(e => !e.waktu_mulai);
            allDayEvents.forEach(event => {
                const eventEl = document.createElement('div');
                eventEl.className = `event event-${event.jenis_kegiatan}`;
                eventEl.style.fontSize = '11px';
                eventEl.style.marginBottom = '4px';
                eventEl.style.padding = '4px';
                eventEl.style.cursor = 'pointer';
                eventEl.innerHTML = `<span class="event-text">${event.nama_kegiatan.substring(0, 20)}</span>`;
                eventEl.onclick = () => showEventDetail(event);
                cell.appendChild(eventEl);
            });

            allDayRow.appendChild(cell);
        }
        tbody.appendChild(allDayRow);

        // Time slots (06:00 - 18:00)
        for (let hour = 6; hour <= 18; hour++) {
            const timeRow = document.createElement('tr');

            const timeCell = document.createElement('td');
            timeCell.style.padding = '8px';
            timeCell.style.backgroundColor = '#f5f5f5';
            timeCell.style.fontWeight = 'bold';
            timeCell.style.fontSize = '0.75rem'; // Smaller font size for time label
            timeCell.style.textAlign = 'center';
            timeCell.style.height = '60px';
            timeCell.textContent = `${hour.toString().padStart(2, '0')}:00`;
            timeRow.appendChild(timeCell);

            for (let d = 0; d < 7; d++) {
                const date = new Date(startOfWeek);
                date.setDate(startOfWeek.getDate() + d);

                const cell = document.createElement('td');
                cell.style.padding = '8px';
                cell.style.verticalAlign = 'top';
                cell.style.height = '60px';
                cell.style.borderLeft = '1px solid #e0e0e0';

                const isToday = isSameDay(date, new Date());
                if (isToday) {
                    cell.style.backgroundColor = 'rgba(255, 249, 196, 0.3)';
                }

                // Timed events
                const events = getEventsForDate(date);
                const timedEvents = events.filter(e => e.waktu_mulai && new Date(`2000-01-01 ${e.waktu_mulai}`).getHours() === hour);
                timedEvents.forEach(event => {
                    const eventEl = document.createElement('div');
                    eventEl.className = `event event-${event.jenis_kegiatan}`;
                    eventEl.style.fontSize = '10px';
                    eventEl.style.padding = '4px';
                    eventEl.style.marginBottom = '2px';
                    eventEl.style.cursor = 'pointer';
                    eventEl.style.borderRadius = '3px';
                    eventEl.innerHTML = `
                        <div style="font-weight: 600; font-size: 11px;">${event.nama_kegiatan.substring(0, 18)}</div>
                    `;
                    eventEl.onclick = () => showEventDetail(event);
                    cell.appendChild(eventEl);
                });

                timeRow.appendChild(cell);
            }
            tbody.appendChild(timeRow);
        }

        newTable.appendChild(tbody);
        
        // Replace existing table or append if it doesn't exist
        if (calendarTable) {
            calendarTable.parentNode.replaceChild(newTable, calendarTable);
        } else {
            // Find where to insert (after calendar-nav)
            const calendarNav = calendarSection.querySelector('.calendar-nav');
            if (calendarNav && calendarNav.nextSibling) {
                calendarSection.insertBefore(newTable, calendarNav.nextSibling);
            } else {
                calendarSection.appendChild(newTable);
            }
        }

        updateSidebar();
    }

    function toggleVisibility(id, checkbox) {
        const isChecked = checkbox.checked;
        const labelIcon = checkbox.nextElementSibling.querySelector('i');
        
        // Optimistic UI update
        labelIcon.className = isChecked ? 'fas fa-eye text-success' : 'fas fa-eye-slash text-muted';

        const toggleUrl = calendarConfig.toggleRouteTemplate.replace(':id', id);

        fetch(toggleUrl, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': calendarConfig.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Update local data
                const event = allEvents.find(e => e.id === id);
                if (event) {
                    event.is_hidden_siswa = data.is_hidden;
                }
                
                // Show toast
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: data.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                } else {
                    console.log(data.message);
                }
            } else {
                throw new Error(data.message || 'Gagal update status');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            // Revert UI on error
            checkbox.checked = !isChecked;
            labelIcon.className = !isChecked ? 'fas fa-eye text-success' : 'fas fa-eye-slash text-muted';
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Gagal mengubah status visibilitas. Silakan coba lagi.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            } else {
                console.error('Gagal update status');
            }
        });
    }

    function renderYearView() {
        const year = currentDate.getFullYear();
        document.getElementById('monthTitle').textContent = `Tahun ${year}`;

        const calendarTable = document.getElementById('calendarTable');
        const newDiv = document.createElement('div');
        newDiv.style.display = 'grid';
        newDiv.style.gridTemplateColumns = 'repeat(auto-fill, minmax(280px, 1fr))';
        newDiv.style.gap = '15px';
        newDiv.style.padding = '10px';

        const yearStart = new Date(year, 0, 1);
        const yearEnd = new Date(year, 11, 31);

        const monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        for (let m = 0; m < 12; m++) {
            const card = document.createElement('div');
            card.style.backgroundColor = '#fff';
            card.style.border = '1px solid #e0e0e0';
            card.style.borderRadius = '8px';
            card.style.padding = '20px';
            card.style.textAlign = 'center';
            card.style.cursor = 'pointer';
            card.style.transition = 'all 0.3s ease';
            card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';

            card.onmouseover = () => {
                card.style.transform = 'translateY(-4px)';
                card.style.boxShadow = '0 6px 12px rgba(0,0,0,0.15)';
            };
            card.onmouseout = () => {
                card.style.transform = 'translateY(0)';
                card.style.boxShadow = '0 2px 4px rgba(0,0,0,0.1)';
            };

            // Get events for this month
            const monthEvents = allEvents.filter(event => {
                if (!event.tanggal_mulai) return false;
                const start = parseDate(event.tanggal_mulai);
                const end = event.tanggal_selesai ? parseDate(event.tanggal_selesai) : start;
                
                if (!start) return false;
                
                // Get month and year for comparison
                const sMonth = start.getMonth();
                const sYear = start.getFullYear();
                const eMonth = end.getMonth();
                const eYear = end.getFullYear();

                // Check if event overlaps with this month in this specific year
                // Logic:
                // 1. Starts in this month/year
                // 2. Ends in this month/year
                // 3. Spans over this month (starts before and ends after)
                
                const monthStart = new Date(year, m, 1);
                const monthEnd = new Date(year, m + 1, 0);
                
                return (start <= monthEnd && end >= monthStart);
            }).sort((a, b) => new Date(a.tanggal_mulai) - new Date(b.tanggal_mulai));

            const monthEventsCount = monthEvents.length;

            const iconDiv = document.createElement('div');
            iconDiv.style.marginBottom = '12px';
            iconDiv.style.fontSize = '28px';
            iconDiv.style.color = '#1a4d8f';
            iconDiv.innerHTML = '<i class="far fa-calendar-alt"></i>';
            card.appendChild(iconDiv);

            const monthTitle = document.createElement('h5');
            monthTitle.style.fontWeight = '700';
            monthTitle.style.color = '#1a4d8f';
            monthTitle.style.marginBottom = '4px';
            monthTitle.textContent = monthNames[m];
            card.appendChild(monthTitle);

            const yearDiv = document.createElement('div');
            yearDiv.style.fontSize = '12px';
            yearDiv.style.color = '#999';
            yearDiv.style.marginBottom = '12px';
            yearDiv.textContent = year;
            card.appendChild(yearDiv);

            const badgeDiv = document.createElement('div');
            badgeDiv.style.marginTop = '12px';
            badgeDiv.style.marginBottom = '12px';
            if (monthEventsCount > 0) {
                badgeDiv.innerHTML = `
                    <span style="display: inline-block; background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
                                 color: white; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 600;">
                        <i class="fas fa-check-circle" style="margin-right: 4px;"></i> ${monthEventsCount} Kegiatan
                    </span>
                `;
            } else {
                badgeDiv.innerHTML = `
                    <span style="display: inline-block; background: #f0f0f0; color: #999; padding: 6px 12px;
                                 border-radius: 20px; font-size: 12px; border: 1px solid #ddd;">Kosong</span>
                `;
            }
            card.appendChild(badgeDiv);

            // Add event list if there are events
            if (monthEventsCount > 0) {
                const eventsListDiv = document.createElement('div');
                eventsListDiv.style.textAlign = 'left';
                eventsListDiv.style.marginTop = '12px';
                eventsListDiv.style.maxHeight = '150px';
                eventsListDiv.style.overflowY = 'auto';
                eventsListDiv.style.padding = '8px';
                eventsListDiv.style.backgroundColor = '#f8f9fa';
                eventsListDiv.style.borderRadius = '4px';
                eventsListDiv.style.fontSize = '11px';

                // Color map for event types
                const eventColorMap = {
                    'field_trip': '#17a2b8',
                    'outing': '#28a745',
                    'live_in': '#6610f2',
                    'hokfest': '#fd7e14',
                    'pts': '#ffc107',
                    'pas': '#dc3545',
                    'libur': '#6c757d',
                    'ujian': '#e83e8c',
                    'acara_sekolah': '#20c997',
                    'lainnya': '#4e73df'
                };

                monthEvents.slice(0, 5).forEach(event => {
                    const eventItem = document.createElement('div');
                    eventItem.style.padding = '6px 8px';
                    eventItem.style.marginBottom = '4px';
                    eventItem.style.backgroundColor = 'white';
                    eventItem.style.borderRadius = '3px';
                    const eventColor = eventColorMap[event.jenis_kegiatan] || eventColorMap['lainnya'];
                    eventItem.style.borderLeft = `3px solid ${eventColor}`;
                    eventItem.style.cursor = 'pointer';
                    eventItem.style.transition = 'all 0.2s';
                    
                    const eventName = document.createElement('div');
                    eventName.style.fontWeight = '600';
                    eventName.style.color = '#333';
                    eventName.style.marginBottom = '2px';
                    eventName.textContent = event.nama_kegiatan || 'Tanpa Nama';
                    eventItem.appendChild(eventName);

                    const eventDate = document.createElement('div');
                    eventDate.style.fontSize = '10px';
                    eventDate.style.color = '#666';
                    const startDate = parseDate(event.tanggal_mulai);
                    const dateStr = startDate ? startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' }) : '';
                    eventDate.textContent = dateStr;
                    eventItem.appendChild(eventDate);

                    eventItem.onmouseover = () => {
                        eventItem.style.backgroundColor = '#e9ecef';
                    };
                    eventItem.onmouseout = () => {
                        eventItem.style.backgroundColor = 'white';
                    };
                    eventItem.onclick = (e) => {
                        e.stopPropagation();
                        showEventDetail(event);
                    };

                    eventsListDiv.appendChild(eventItem);
                });

                if (monthEventsCount > 5) {
                    const moreItem = document.createElement('div');
                    moreItem.style.padding = '6px 8px';
                    moreItem.style.textAlign = 'center';
                    moreItem.style.fontSize = '10px';
                    moreItem.style.color = '#666';
                    moreItem.style.fontStyle = 'italic';
                    moreItem.textContent = `+${monthEventsCount - 5} kegiatan lainnya`;
                    eventsListDiv.appendChild(moreItem);
                }

                card.appendChild(eventsListDiv);
            }

            // Add click handler to switch to month view
            card.onclick = (e) => {
                // Prevent bubbling if clicking on an event inside the card
                if (e.target.closest('.event-item-sidebar')) return;
                
                // Go to month view
                currentDate = new Date(year, m, 1);
                currentView = 'bulan';
                updateViewButtons();
                
                // Force Render new view
                renderCalendar();
            };

            newDiv.appendChild(card);
        }

        calendarTable.parentNode.replaceChild(newDiv, calendarTable);
        newDiv.id = 'calendarTable';

        updateSidebar();
    }

    function updateSidebar() {
        const sidebarContent = document.getElementById('sidebarContent');

        // Filter events based on current view
        let filteredEvents = [];
        
        // Helper to check if event overlaps with a range [rangeStart, rangeEnd]
        const overlaps = (ev, rangeStart, rangeEnd) => {
             if (!ev.tanggal_mulai) return false;
             const start = parseDate(ev.tanggal_mulai);
             const end = ev.tanggal_selesai ? parseDate(ev.tanggal_selesai) : start;
             if (!start) return false;
             
             // Check intersection: start <= rangeEnd AND end >= rangeStart
             return (start <= rangeEnd && end >= rangeStart);
        };

        if (currentView === 'bulan') {
            // Show events for current month
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();
            const monthStart = new Date(year, month, 1);
            const monthEnd = new Date(year, month + 1, 0); // Last day of month
            
            filteredEvents = allEvents.filter(ev => overlaps(ev, monthStart, monthEnd));
            
        } else if (currentView === 'minggu') {
            // Show events for current week
            const dayOfWeek = currentDate.getDay();
            const startOfWeek = new Date(currentDate);
            startOfWeek.setDate(currentDate.getDate() - dayOfWeek);
            const endOfWeek = new Date(startOfWeek);
            endOfWeek.setDate(startOfWeek.getDate() + 6);
            
            filteredEvents = allEvents.filter(ev => overlaps(ev, startOfWeek, endOfWeek));

        } else if (currentView === 'tahun') {
            // Show all events for current year
            const year = currentDate.getFullYear();
            const yearStart = new Date(year, 0, 1);
            const yearEnd = new Date(year, 11, 31);
            
            filteredEvents = allEvents.filter(ev => overlaps(ev, yearStart, yearEnd));
        } else {
            filteredEvents = allEvents;
        }

        // Sort by date
        filteredEvents.sort((a, b) => {
            const dateA = parseDate(a.tanggal_mulai);
            const dateB = parseDate(b.tanggal_mulai);
            if (!dateA || !dateB) return 0;
            return dateA - dateB;
        });

        if (filteredEvents.length === 0) {
            sidebarContent.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <i class="far fa-calendar-times fa-2x mb-2"></i>
                    <p class="small m-0">Tidak ada kegiatan ${currentView === 'bulan' ? 'bulan ini' : currentView === 'minggu' ? 'minggu ini' : 'tahun ini'}</p>
                </div>
            `;
            return;
        }

        sidebarContent.innerHTML = '';
        filteredEvents.forEach(event => {
            if (!event.tanggal_mulai) return;
            const startDate = parseDate(event.tanggal_mulai);
            if (!startDate) return;
            
            const dateStr = startDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            const dayName = startDate.toLocaleDateString('id-ID', { weekday: 'long' });

            const itemEl = document.createElement('div');
            itemEl.className = 'event-item-sidebar';
            
            // Match student view design: Badge on left, Info on right
            itemEl.innerHTML = `
                <div class="date-badge-wrapper">
                    <div class="large-color-dot" style="background-color: var(--event-color-${event.jenis_kegiatan});"></div>
                    <span class="d-date-small">${dateStr}</span>
                </div>
                <div class="event-info flex-grow-1">
                    <div class="event-title" title="${(event.nama_kegiatan || '').replace(/"/g, '&quot;')}">
                        ${(event.nama_kegiatan || 'Tanpa Nama').substring(0, 35)}
                    </div>
                    <div class="event-meta">
                        <span class="meta-day"><i class="far fa-calendar"></i> ${dayName}</span>
                        ${event.waktu_mulai ? `<span class="meta-time"><i class="far fa-clock"></i> ${event.waktu_mulai.substring(0, 5)}</span>` : ''}
                    </div>
                </div>
                <div class="ms-2" data-stop-event-click>
                    <div class="form-check form-switch" title="Tampilkan/Sembunyikan dari Siswa">
                        <input class="form-check-input" type="checkbox" role="switch"
                            style="cursor: pointer; transform: scale(0.8);"
                            id="visibilitySwitch-Sidebar-${event.id}" 
                            ${!event.is_hidden_siswa ? 'checked' : ''}
                            data-toggle-sidebar-visibility
                            data-event-id="${event.id}">
                        <label class="form-check-label" for="visibilitySwitch-Sidebar-${event.id}">
                            <i class="fas ${!event.is_hidden_siswa ? 'fa-eye text-success' : 'fa-eye-slash text-muted'}" style="font-size: 0.8rem;"></i>
                        </label>
                    </div>
                </div>
            `;
            itemEl.onclick = () => showEventDetail(event);
            itemEl.querySelector('[data-stop-event-click]')?.addEventListener('click', (clickEvent) => {
                clickEvent.stopPropagation();
            });
            itemEl.querySelector('[data-toggle-sidebar-visibility]')?.addEventListener('change', (changeEvent) => {
                toggleVisibility(event.id, changeEvent.currentTarget);
            });
            sidebarContent.appendChild(itemEl);
        });
    }

    function showEventDetail(event) {
        window.location.href = calendarConfig.showRouteTemplate.replace(':id', event.id);
    }

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value || '';
        return element.innerHTML;
    }

    function showEventsForDate(date) {
        const events = getEventsForDate(date);
        const dateStr = date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });

        let html = `<div class="detail-label">Kegiatan pada ${dateStr}:</div>`;
        events.forEach(event => {
            html += `
                <div class="event-detail-choice" data-modal-event-id="${event.id}">
                    <strong>${escapeHtml(event.nama_kegiatan || 'Tanpa Nama')}</strong>
                </div>
            `;
        });

        const eventDetails = document.getElementById('eventDetails');
        eventDetails.innerHTML = html;
        eventDetails.querySelectorAll('[data-modal-event-id]').forEach((item) => {
            item.addEventListener('click', () => {
                const selectedEvent = events.find((event) => String(event.id) === item.dataset.modalEventId);

                if (selectedEvent) {
                    showEventDetail(selectedEvent);
                }
            });
        });
        const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
        eventModal.show();
    }

    // ==========================================
    // 3. NAVIGATION FUNCTIONS
    // ==========================================
    function previousMonth() {
        if (currentView === 'bulan') {
            currentDate.setMonth(currentDate.getMonth() - 1);
        } else if (currentView === 'minggu') {
            currentDate.setDate(currentDate.getDate() - 7);
        } else if (currentView === 'tahun') {
            currentDate.setFullYear(currentDate.getFullYear() - 1);
        }
        renderCalendar();
    }

    function nextMonth() {
        if (currentView === 'bulan') {
            currentDate.setMonth(currentDate.getMonth() + 1);
        } else if (currentView === 'minggu') {
            currentDate.setDate(currentDate.getDate() + 7);
        } else if (currentView === 'tahun') {
            currentDate.setFullYear(currentDate.getFullYear() + 1);
        }
        renderCalendar();
    }

    function changeView(view) {
        currentView = view;
        updateViewButtons();

        if (view === 'minggu') {
            renderWeekView();
        } else if (view === 'tahun') {
            renderYearView();
        } else {
            renderCalendar();
        }
    }

    function updateViewButtons() {
        const btnBulan = document.getElementById('viewBulan');
        const btnMinggu = document.getElementById('viewMinggu');
        const btnTahun = document.getElementById('viewTahun');

        [btnBulan, btnMinggu, btnTahun].forEach(btn => {
            btn.style.backgroundColor = 'transparent';
            btn.style.color = '#4e73df';
            btn.classList.remove('btn-primary');
            btn.classList.add('btn-outline-primary');
        });

        if (currentView === 'bulan') {
            btnBulan.style.backgroundColor = '#4e73df';
            btnBulan.style.color = 'white';
            btnBulan.classList.remove('btn-outline-primary');
            btnBulan.classList.add('btn-primary');
        } else if (currentView === 'minggu') {
            btnMinggu.style.backgroundColor = '#4e73df';
            btnMinggu.style.color = 'white';
            btnMinggu.classList.remove('btn-outline-primary');
            btnMinggu.classList.add('btn-primary');
        } else if (currentView === 'tahun') {
            btnTahun.style.backgroundColor = '#4e73df';
            btnTahun.style.color = 'white';
            btnTahun.classList.remove('btn-outline-primary');
            btnTahun.classList.add('btn-primary');
        }
    }

    // ==========================================
    // 4. SEARCH FUNCTIONALITY
    // ==========================================
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

    // ==========================================
    // 5. RESIZABLE SIDEBAR
    // ==========================================
    function initResizer() {
        const resizer = document.getElementById('dragMe');
        if (!resizer) return;

        const leftSide = resizer.previousElementSibling;
        const rightSide = resizer.nextElementSibling;
        const container = resizer.parentNode;

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

    // ==========================================
    // 6. DELETE CONFIRMATION
    // ==========================================
    function confirmDelete(id, name) {
        document.getElementById('deleteKalenderName').textContent = name;
        document.getElementById('deleteForm').action = calendarConfig.deleteRouteTemplate.replace(':id', id);
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // ==========================================
    // 7. PDF EXPORT
    // ==========================================
    function updateCetakUrl() {
        const val = document.getElementById('customMonth').value;
        const btn = document.getElementById('customCetakBtn');
        if(val) {
            btn.href = `${calendarConfig.printRoute}?jenis=bulanan&bulan=${val}`;
        }
    }
    function initCalendarControls() {
        document.querySelectorAll('[data-calendar-view]').forEach((control) => {
            control.addEventListener('click', (event) => {
                event.preventDefault();
                changeView(control.dataset.calendarView);
            });
        });

        document.querySelector('[data-calendar-prev]')?.addEventListener('click', previousMonth);
        document.querySelector('[data-calendar-next]')?.addEventListener('click', nextMonth);
        document.getElementById('customMonth')?.addEventListener('change', updateCetakUrl);

        document.querySelectorAll('[data-delete-kalender]').forEach((button) => {
            button.addEventListener('click', () => {
                confirmDelete(button.dataset.id, button.dataset.name);
            });
        });
    }

    // ==========================================
    // 8. INITIALIZATION
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        if (currentView === 'bulan') {
            renderCalendar();
        } else if (currentView === 'minggu') {
            renderWeekView();
        } else if (currentView === 'tahun') {
            renderYearView();
        }
        initSearch();
        initResizer();
        initCalendarControls();
        updateCetakUrl();
    });


