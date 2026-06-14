@extends('layouts.sneat')

@section('title', 'Dashboard Sekretaris')

@section('page-title', 'Dashboard Sekretaris')
@section('page-subtitle', 'Kelola Kalender Akademik, Pengumuman, dan Flyer')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    /* CSS Reset & Shared Variables */
    :root {
        --primary-color: #4361ee;
        --secondary-color: #f8f9fa;
        --text-main: #2b2d42;
        --text-muted: #8d99ae;
        --border-color: #e9ecef;
    }

    /* Core Card Styling */
    .dashboard-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--border-color);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        height: 100%;
    }

    .dashboard-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }

    /* Clean Card Headers */
    .card-header-clean {
        padding: 1.25rem 1.5rem;
        background: transparent;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-clean {
        margin: 0;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Stat Widgets */
    .stat-widget {
        display: flex;
        align-items: center;
        padding: 1.5rem;
        gap: 1.5rem;
    }

    .stat-icon-wrapper {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .stat-content {
        display: flex;
        flex-direction: column;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text-main);
        line-height: 1.2;
        margin-bottom: 0.2rem;
    }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Custom Calendar Layout & Responsiveness */
    #calendar {
        padding: 1.25rem;
        width: 100%;
        min-height: 500px;
    }
    
    .fc .fc-toolbar-title {
        font-size: 1.25rem !important;
        font-weight: 700;
        color: var(--text-main);
    }

    /* Wrap the header cleanly on smaller screens */
    .fc .fc-toolbar {
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    /* Make events look good */
    .fc-event {
        border-radius: 6px;
        padding: 2px 4px;
        font-size: 0.8rem;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        border: none !important;
        margin-bottom: 3px !important;
    }

    .fc-event-title-container {
        font-weight: 600;
        white-space: normal !important;
    }

    /* Modifikasi UI Mobile Khusus Kalender */
    @media (max-width: 992px) {
        #calendar {
            padding: 1rem;
            min-height: auto;
        }
        /* Layout untuk header kalender agar title di atas, tombol di urutan bawah */
        .fc .fc-toolbar {
            justify-content: center;
            flex-direction: column;
            gap: 1rem;
        }
        .fc .fc-toolbar-chunk {
            display: flex;
            justify-content: center;
            width: 100%;
        }
        /* Title chunk jadi ditengah Atas */
        .fc .fc-toolbar-chunk:nth-child(2) {
            order: -1;
            margin-bottom: 0.25rem;
        }
        
        .fc .fc-toolbar-title {
            font-size: 1.1rem !important;
            text-align: center;
        }

        .fc .fc-button {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
        }
        
        .fc .fc-daygrid-day-number {
            font-size: 0.8rem;
            padding: 4px;
        }

        /* Saat masuk mobile, event-nya bisa disingkat */
        .fc-event {
            padding: 1px 2px;
            font-size: 0.75rem;
        }
    }
    
    @media (max-width: 576px) {
        #calendar {
            padding: 0.5rem;
        }
        /* Sesuaikan ukuran font grid tanggal agar tetap muat di mobile */
        .fc .fc-daygrid-day-number {
            font-size: 0.75rem;
            padding: 2px;
        }
        .fc-event {
            font-size: 0.65rem;
            padding: 1px 2px;
        }
    }

    .table-fixed-height {
        max-height: 480px;
        overflow-y: auto;
    }
    .table-fixed-height::-webkit-scrollbar {
        width: 6px;
    }
    .table-fixed-height::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .table-fixed-height::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .table-fixed-height::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Quick Links */
    .quick-links-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    .quick-link-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        border-radius: 12px;
        background: var(--secondary-color);
        border: 1px solid var(--border-color);
        text-decoration: none !important;
        color: var(--text-main);
        transition: all 0.2s ease;
        text-align: center;
        height: 90px;
    }
    .quick-link-item:hover {
        background: #ffffff;
        border-color: var(--primary-color);
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1);
    }
    .quick-link-item i {
        font-size: 1.5rem;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        transition: color 0.2s ease;
    }
    .quick-link-item:hover i {
        color: var(--primary-color);
    }
    .quick-link-text {
        font-size: 0.85rem;
        font-weight: 600;
        line-height: 1.2;
    }

    .modal-content { border-radius: 15px; border: none; }
    .modal-header { background-color: #f8f9fc; border-radius: 15px 15px 0 0; }
</style>
@endsection

@section('content')

    <!-- Top Header & Date -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center align-items-start gap-3 mb-4">
        <h5 class="mb-0 fw-bold text-dark" style="font-size: 1.15rem;">
            <i class="fas fa-clipboard-list me-2 text-primary"></i> Ringkasan Sekretariat
        </h5>
        
        <div class="d-flex flex-wrap gap-2">
            @if(isset($tahunAjaranAktif) && $tahunAjaranAktif)
                <span class="badge bg-white text-dark px-3 py-2 fs-6 rounded-pill shadow-sm border" style="border-color: var(--border-color) !important;">
                    <i class="fas fa-flag-checkered me-2 text-primary"></i> TA: {{ $tahunAjaranAktif->nama_tahun_ajaran }}
                </span>
            @endif
            <span class="badge bg-white text-primary px-3 py-2 fs-6 rounded-pill shadow-sm border" style="border-color: var(--border-color) !important;">
                <i class="fas fa-calendar-alt me-2"></i> {{ now()->translatedFormat('d F Y') }}
            </span>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-primary">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['totalKalender'] }}</div>
                        <div class="stat-label">Total Kegiatan</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-success">
                        <i class="fas fa-play-circle"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['kegiatanAktif'] }}</div>
                        <div class="stat-label">Kegiatan Aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-info">
                        <i class="fas fa-bullhorn"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['pengumumanAktif'] }}</div>
                        <div class="stat-label">Pengumuman</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-xl-3">
            <div class="dashboard-card border-0 shadow-sm">
                <div class="stat-widget">
                    <div class="stat-icon-wrapper bg-label-warning">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $stats['flyerAktif'] + $stats['beritaAktif'] }}</div>
                        <div class="stat-label">Flyer & Berita</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Layout -->
    <div class="row g-4 mb-4">
        <!-- Calendar Section -->
        <div class="col-lg-8">
            <div class="dashboard-card h-100 flex-column d-flex">
                <div class="card-header-clean">
                    <h5 class="card-title-clean">
                        <i class="fas fa-calendar-alt text-primary card-title-icon"></i> Kalender Akademik Utama
                    </h5>
                    <a href="{{ route('sekretaris.kalender.create') }}" class="btn btn-sm btn-primary shadow-sm btn-action">
                        <i class="fas fa-plus mr-1"></i> Tambah
                    </a>
                </div>
                <div class="card-body p-0 flex-grow-1">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

        <!-- Info & Activities -->
        <div class="col-lg-4 d-flex flex-column gap-4">
            <!-- Info Sistem Alert -->
            <div class="alert alert-primary d-flex align-items-center rounded-3 shadow-none border-0 m-0" role="alert" style="background-color: rgba(67, 97, 238, 0.08); color: var(--primary-dark);">
                <i class="fas fa-robot fs-4 me-3 text-primary"></i>
                <div style="font-size: 0.9rem;">
                    <strong>Info Sistem:</strong> Kegiatan kalender otomatis menjadi <strong class="text-primary">Pengumuman</strong> jika waktu pengerjaan kurang dari 3 hari.
                </div>
            </div>

            <!-- Akses Modul Utama -->
            <div class="dashboard-card border-0 shadow-sm">
                <div class="card-header-clean border-bottom">
                    <h5 class="card-title-clean">
                        <i class="fas fa-bolt text-warning card-title-icon"></i> Akses Modul Utama
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="quick-links-grid">
                        <a href="{{ route('sekretaris.kalender.index') }}" class="quick-link-item">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            <span class="quick-link-text">Kelola<br>Kalender</span>
                        </a>
                        <a href="{{ route('sekretaris.pengumuman.index') }}" class="quick-link-item">
                            <i class="fas fa-bullhorn text-info"></i>
                            <span class="quick-link-text">Kelola<br>Pengumuman</span>
                        </a>
                        <a href="{{ route('sekretaris.flyer.index') }}" class="quick-link-item">
                            <i class="fas fa-image text-warning"></i>
                            <span class="quick-link-text">Publikasi<br>Flyer</span>
                        </a>
                        <a href="{{ route('sekretaris.berita.index') }}" class="quick-link-item">
                            <i class="fas fa-newspaper text-success"></i>
                            <span class="quick-link-text">Portal<br>Berita</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Kegiatan Hari Ini -->
            <div class="dashboard-card flex-grow-1 d-flex flex-column">
                <div class="card-header-clean border-bottom">
                    <h5 class="card-title-clean">
                        <i class="fas fa-clock text-success card-title-icon"></i> Kegiatan Hari Ini
                    </h5>
                </div>
                <div class="card-body p-0 flex-grow-1 table-fixed-height">
                    @if($kegiatanHariIni->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0">
                                <tbody>
                                    @foreach($kegiatanHariIni as $kegiatan)
                                    <tr>
                                        <td class="ps-4 py-3 border-0 border-bottom">
                                            <div class="fw-bold text-dark">{{ $kegiatan->nama_kegiatan }}</div>
                                            <div class="small text-muted mt-1"><i class="far fa-clock me-1"></i>{{ $kegiatan->waktu_mulai ?? 'Seharian' }}</div>
                                        </td>
                                        <td class="text-end pe-4 py-3 align-middle border-0 border-bottom">
                                            <a href="{{ route('sekretaris.kalender.edit', $kegiatan->id) }}" class="btn btn-sm btn-outline-warning shadow-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 d-flex flex-column align-items-center justify-content-center h-100">
                            <div class="bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="fas fa-calendar-day fa-2x text-secondary"></i>
                            </div>
                            <p class="text-muted small mb-0 fw-medium">Tidak ada kegiatan hari ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light border-0">
                    <h5 class="modal-title fw-bold text-primary" id="eventTitle">
                        <i class="fas fa-calendar-check me-2"></i>Detail Kegiatan
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" id="eventDetails">
                    <!-- Event Details will be injected here -->
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <a href="#" id="editEventBtn" class="btn btn-warning">
                        <i class="fas fa-edit me-1"></i> Edit
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
        contentHeight: 'auto',
        eventDisplay: 'list-item',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            list: 'Daftar'
        },
        events: function(info, successCallback, failureCallback) {
            // Gunakan date pertengahan range untuk menentukan bulan yg aktif
            const midDate = new Date((info.start.getTime() + info.end.getTime()) / 2);
            const year = midDate.getFullYear();
            const month = String(midDate.getMonth() + 1).padStart(2, '0');
            const bulan = `${year}-${month}`;
            
            fetch(`{{ route('sekretaris.kalender.bulanan') }}?bulan=${bulan}`)
                .then(response => {
                    if (!response.ok) throw new Error('Gagal mengambil data kalender');
                    return response.json();
                })
                .then(data => {
                    let expandedEvents = [];
                    data.forEach(event => {
                        if (event.end) {
                            let startDate = new Date(event.start);
                            let endDate = new Date(event.end); // eksklusif hari selanjutnya
                            
                            // Jika ada perbedaan hari
                            let current = new Date(startDate);
                            while (current < endDate) {
                                expandedEvents.push({
                                    id: event.id,
                                    title: event.title,
                                    backgroundColor: event.backgroundColor,
                                    borderColor: event.borderColor,
                                    start: current.toISOString().substring(0, 10),
                                    end: null, // dipaksa 1 hari
                                    extendedProps: {
                                        ...event.extendedProps,
                                        originalStart: event.start,
                                        originalEnd: event.end
                                    }
                                });
                                current.setDate(current.getDate() + 1);
                            }
                        } else {
                            // Event 1 hari langsung dipush
                            expandedEvents.push(event);
                        }
                    });
                    successCallback(expandedEvents);
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const event = info.event;
            document.getElementById('eventTitle').textContent = event.title;
            
            // Dapatkan tanggal original dari prop jika event ini hasil expand multi-day
            const realStart = event.extendedProps.originalStart ? new Date(event.extendedProps.originalStart) : event.start;
            const realEnd = event.extendedProps.originalEnd ? new Date(event.extendedProps.originalEnd) : event.end;

            const details = `
                <div class="mb-3">
                    <label class="small font-weight-bold text-uppercase text-muted d-block">Waktu & Tanggal</label>
                    <div class="text-dark">
                        <i class="far fa-clock me-1 text-primary"></i>
                        ${formatDate(realStart)} 
                        ${realEnd ? ' <i class="fas fa-arrow-right mx-1 text-muted" style="font-size:0.8rem"></i> s/d ' + formatDate(new Date(realEnd.getTime() - 86400000)) : ''}
                    </div>
                </div>
                <div class="mb-3">
                    <label class="small font-weight-bold text-uppercase text-muted d-block">Jenis Kegiatan</label>
                    <span class="badge border text-white" style="background-color: ${event.backgroundColor}; border-color: ${event.backgroundColor} !important;">
                        ${event.extendedProps.jenis || 'Umum'}
                    </span>
                </div>
                ${event.extendedProps.keterangan ? `
                <div class="mb-0">
                    <label class="small font-weight-bold text-uppercase text-muted d-block">Keterangan</label>
                    <div class="text-dark bg-light p-3 rounded-3 mt-1">${event.extendedProps.keterangan}</div>
                </div>
                ` : '<div class="text-muted fst-italic small">Tidak ada keterangan tambahan.</div>'}
            `;
            
            document.getElementById('eventDetails').innerHTML = details;
            
            const editUrl = `{{ url('sekretaris/kalender') }}/${event.id}/edit`;
            document.getElementById('editEventBtn').href = editUrl;
            
            // Menggunakan Vanilla JS standar Bootstrap 5
            var myModal = new bootstrap.Modal(document.getElementById('eventModal'));
            myModal.show();
        }
    });
    
    calendar.render();
    
    function formatDate(date) {
        if(!date) return '';
        const options = { day: 'numeric', month: 'long', year: 'numeric' };
        return date.toLocaleDateString('id-ID', options);
    }
});
</script>
@endsection