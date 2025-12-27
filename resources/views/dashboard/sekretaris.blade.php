@extends('layouts.sneat')

@section('title', 'Dashboard Sekretaris')

@section('page-title', 'Dashboard Sekretaris')
@section('page-subtitle', 'Kelola Kalender Akademik, Pengumuman, dan Flyer')

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<style>
    /* UX Friendly Stat Cards */
    .stat-card {
        border: none;
        border-left: 4px solid; /* Indikator warna di samping */
        border-radius: 10px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover { 
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 2rem 0 rgba(58, 59, 69, 0.2) !important;
    }

    /* Warna spesifik untuk setiap kategori */
    .border-kegiatan { border-left-color: #4e73df !important; }
    .border-aktif { border-left-color: #1cc88a !important; }
    .border-pengumuman { border-left-color: #36b9cc !important; }
    .border-konten { border-left-color: #f6c23e !important; }

    /* Pengaturan teks agar mudah dibaca */
    .stat-title {
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
    }

    .stat-number {
        font-size: 1.75rem;
        font-weight: 700;
        color: #3a3b45; /* Warna gelap untuk kontras tinggi */
    }

    .stat-icon {
        color: #dddfeb; /* Ikon dibuat samar agar angka lebih menonjol */
    }

    /* Sisanya tetap sama */
    .calendar-card { border-radius: 15px; border: none; box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15); }
    #calendar { padding: 10px; min-height: 600px; }
    .table-fixed-height { max-height: 400px; overflow-y: auto; }
    .modal-content { border-radius: 15px; border: none; }
    .modal-header { background-color: #f8f9fc; border-radius: 15px 15px 0 0; }
</style>
@endsection

@section('content')

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-kegiatan shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center border-0">
                    <div class="col mr-2">
                        <div class="stat-title text-primary">Total Kegiatan</div>
                        <div class="stat-number">{{ $stats['totalKalender'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-aktif shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-title text-success">Kegiatan Aktif</div>
                        <div class="stat-number">{{ $stats['kegiatanAktif'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play-circle fa-2x stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-pengumuman shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-title text-info">Pengumuman Aktif</div>
                        <div class="stat-number">{{ $stats['pengumumanAktif'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bullhorn fa-2x stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-konten shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="stat-title text-warning">Flyer & Berita</div>
                        <div class="stat-number">{{ $stats['flyerAktif'] + $stats['beritaAktif'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-newspaper fa-2x stat-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card calendar-card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center bg-white">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-calendar-alt mr-2"></i>Kalender Akademik Utama
                </h6>
                <a href="{{ route('sekretaris.kalender.create') }}" class="btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm mr-1"></i>Tambah Kegiatan
                </a>
            </div>
            <div class="card-body">
                <div id="calendar"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-left-primary shadow mb-4">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Info Sistem</div>
                        <p class="small text-gray-600 mb-0">
                            Kegiatan kalender otomatis menjadi <strong>Pengumuman</strong> jika waktu pelaksanaan kurang dari 3 hari.
                        </p>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-robot fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="fas fa-clock mr-2"></i>Kegiatan Hari Ini
                </h6>
            </div>
            <div class="card-body p-0 table-fixed-height">
                @if($kegiatanHariIni->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <tbody>
                                @foreach($kegiatanHariIni as $kegiatan)
                                <tr>
                                    <td class="border-0">
                                        <div class="font-weight-bold text-dark">{{ $kegiatan->nama_kegiatan }}</div>
                                        <div class="small text-muted">{{ $kegiatan->waktu_mulai ?? 'Seharian' }}</div>
                                    </td>
                                    <td class="border-0 text-right">
                                        <a href="{{ route('sekretaris.kalender.edit', $kegiatan->id) }}" class="btn btn-sm btn-circle btn-light">
                                            <i class="fas fa-edit text-warning"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-day fa-3x text-gray-200 mb-3"></i>
                        <p class="text-gray-500 small">Tidak ada kegiatan hari ini</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="eventModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow">
            <div class="modal-header">
                <h5 class="modal-title font-weight-bold text-primary" id="eventTitle">Detail Kegiatan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="eventDetails">
                </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Tutup</button>
                <a href="#" id="editEventBtn" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit mr-1"></i>Edit Kegiatan
                </a>
            </div>
        </div>
    </div>
</div>
</div>

@endsection

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        height: 'auto',
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
            const bulan = calendar.view.currentStart.toISOString().substring(0, 7);
            fetch(`{{ route('sekretaris.kalender.bulanan') }}?bulan=${bulan}`)
                .then(response => response.json())
                .then(data => successCallback(data))
                .catch(error => failureCallback(error));
        },
        dateClick: function(info) {
            window.location.href = `{{ route('sekretaris.kalender.create') }}?tanggal=${info.dateStr}`;
        },
        eventClick: function(info) {
            const event = info.event;
            document.getElementById('eventTitle').textContent = event.title;
            
            const details = `
                <div class="mb-3">
                    <label class="small font-weight-bold text-uppercase text-muted d-block">Waktu & Tanggal</label>
                    <div class="text-dark">${formatDate(event.start)} ${event.end ? ' s/d ' + formatDate(new Date(event.end.getTime() - 86400000)) : ''}</div>
                </div>
                <div class="mb-3">
                    <label class="small font-weight-bold text-uppercase text-muted d-block">Jenis Kegiatan</label>
                    <span class="badge badge-pill text-white" style="background-color: ${event.backgroundColor}">${event.extendedProps.jenis || 'Umum'}</span>
                </div>
                ${event.extendedProps.keterangan ? `
                <div class="mb-0">
                    <label class="small font-weight-bold text-uppercase text-muted d-block">Keterangan</label>
                    <div class="text-dark">${event.extendedProps.keterangan}</div>
                </div>
                ` : ''}
            `;
            
            document.getElementById('eventDetails').innerHTML = details;
            document.getElementById('editEventBtn').href = `{{ url('sekretaris/kalender') }}/${event.id}/edit`;
            
            // Menggunakan jQuery (standar SB Admin 2) untuk trigger modal
            $('#eventModal').modal('show');
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
@endpush