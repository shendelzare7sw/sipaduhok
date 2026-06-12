const loadScript = (src) => new Promise((resolve, reject) => {
  if (window.FullCalendar) {
    resolve();
    return;
  }

  const existingScript = document.querySelector(`script[src="${src}"]`);

  if (existingScript) {
    existingScript.addEventListener('load', resolve, { once: true });
    existingScript.addEventListener('error', reject, { once: true });
    return;
  }

  const script = document.createElement('script');
  script.src = src;
  script.addEventListener('load', resolve, { once: true });
  script.addEventListener('error', reject, { once: true });
  document.head.appendChild(script);
});

const formatDate = (date) => {
  if (!date) {
    return '';
  }

  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
  });
};

const appendLabel = (container, text) => {
  const label = document.createElement('label');
  label.className = 'small font-weight-bold text-uppercase text-muted d-block';
  label.textContent = text;
  container.appendChild(label);
};

const appendDateDetails = (container, event) => {
  const wrapper = document.createElement('div');
  const dateLine = document.createElement('div');
  const clockIcon = document.createElement('i');
  const realStart = event.extendedProps.originalStart ? new Date(event.extendedProps.originalStart) : event.start;
  const realEnd = event.extendedProps.originalEnd ? new Date(event.extendedProps.originalEnd) : event.end;

  wrapper.className = 'mb-3';
  appendLabel(wrapper, 'Waktu & Tanggal');

  dateLine.className = 'text-dark';
  clockIcon.className = 'far fa-clock me-1 text-primary';
  dateLine.append(clockIcon, document.createTextNode(` ${formatDate(realStart)}`));

  if (realEnd) {
    const arrowIcon = document.createElement('i');
    const displayEnd = new Date(realEnd.getTime() - 86400000);

    arrowIcon.className = 'fas fa-arrow-right mx-1 text-muted event-date-arrow';
    dateLine.append(arrowIcon, document.createTextNode(` s/d ${formatDate(displayEnd)}`));
  }

  wrapper.appendChild(dateLine);
  container.appendChild(wrapper);
};

const appendTypeDetails = (container, event) => {
  const wrapper = document.createElement('div');
  const badge = document.createElement('span');

  wrapper.className = 'mb-3';
  appendLabel(wrapper, 'Jenis Kegiatan');

  badge.className = 'badge border text-white event-type-badge';
  badge.style.setProperty('--event-color', event.backgroundColor || '#4361ee');
  badge.textContent = event.extendedProps.jenis || 'Umum';

  wrapper.appendChild(badge);
  container.appendChild(wrapper);
};

const appendDescriptionDetails = (container, event) => {
  if (!event.extendedProps.keterangan) {
    const empty = document.createElement('div');
    empty.className = 'text-muted fst-italic small';
    empty.textContent = 'Tidak ada keterangan tambahan.';
    container.appendChild(empty);
    return;
  }

  const wrapper = document.createElement('div');
  const description = document.createElement('div');

  wrapper.className = 'mb-0';
  appendLabel(wrapper, 'Keterangan');

  description.className = 'text-dark bg-light p-3 rounded-3 mt-1';
  description.textContent = event.extendedProps.keterangan;
  wrapper.appendChild(description);
  container.appendChild(wrapper);
};

const expandCalendarEvents = (events) => {
  const expandedEvents = [];

  events.forEach((event) => {
    if (!event.end) {
      expandedEvents.push(event);
      return;
    }

    const startDate = new Date(event.start);
    const endDate = new Date(event.end);
    const current = new Date(startDate);

    while (current < endDate) {
      expandedEvents.push({
        id: event.id,
        title: event.title,
        backgroundColor: event.backgroundColor,
        borderColor: event.borderColor,
        start: current.toISOString().substring(0, 10),
        end: null,
        extendedProps: {
          ...event.extendedProps,
          originalStart: event.start,
          originalEnd: event.end,
        },
      });

      current.setDate(current.getDate() + 1);
    }
  });

  return expandedEvents;
};

document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.sekretaris-dashboard-page');

  if (!page) {
    return;
  }

  const calendarElement = page.querySelector('#calendar');
  const fullCalendarSrc = page.dataset.fullcalendarSrc;
  const monthlyUrl = page.dataset.calendarMonthlyUrl;
  const editBaseUrl = page.dataset.calendarEditBaseUrl;

  if (!calendarElement || !fullCalendarSrc || !monthlyUrl || !editBaseUrl) {
    return;
  }

  loadScript(fullCalendarSrc)
    .then(() => {
      const calendar = new window.FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        contentHeight: 'auto',
        eventDisplay: 'list-item',
        headerToolbar: {
          left: 'prev,next today',
          center: 'title',
          right: 'dayGridMonth,listMonth',
        },
        buttonText: {
          today: 'Hari Ini',
          month: 'Bulan',
          list: 'Daftar',
        },
        events: (info, successCallback, failureCallback) => {
          const midDate = new Date((info.start.getTime() + info.end.getTime()) / 2);
          const year = midDate.getFullYear();
          const month = String(midDate.getMonth() + 1).padStart(2, '0');
          const url = new URL(monthlyUrl, window.location.origin);

          url.searchParams.set('bulan', `${year}-${month}`);

          fetch(url.toString())
            .then((response) => {
              if (!response.ok) {
                throw new Error('Gagal mengambil data kalender');
              }

              return response.json();
            })
            .then((data) => successCallback(expandCalendarEvents(data)))
            .catch((error) => {
              console.error('Error fetching events:', error);
              failureCallback(error);
            });
        },
        eventClick: (info) => {
          const event = info.event;
          const eventTitle = page.querySelector('#eventTitle');
          const eventDetails = page.querySelector('#eventDetails');
          const editButton = page.querySelector('#editEventBtn');
          const eventModal = page.querySelector('#eventModal');

          if (!eventTitle || !eventDetails || !editButton || !eventModal) {
            return;
          }

          eventTitle.textContent = event.title;
          eventDetails.replaceChildren();
          appendDateDetails(eventDetails, event);
          appendTypeDetails(eventDetails, event);
          appendDescriptionDetails(eventDetails, event);

          editButton.href = `${editBaseUrl}/${event.id}/edit`;

          if (window.bootstrap) {
            new window.bootstrap.Modal(eventModal).show();
          }
        },
      });

      calendar.render();
    })
    .catch((error) => {
      console.error('FullCalendar gagal dimuat:', error);
    });
});
