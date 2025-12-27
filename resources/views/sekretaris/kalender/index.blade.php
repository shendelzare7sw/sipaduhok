@extends('layouts.sneat')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Kelola kegiatan akademik tahun ajaran ' . ($tahunAjaranAktif->nama_tahun_ajaran ?? '–'))

@section('sidebar-menu')
    @include('sekretaris.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css' rel='stylesheet' />
<style>
    /* Sinkronisasi UI FullCalendar agar selaras dengan SB Admin 2 */
    #calendar {
        background: white;
        padding: 20px;
        border-radius: 8px;
        min-height: 600px;
        width: 100%;
    }

    .fc {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Roboto", "Oxygen", "Ubuntu", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
    }

    .fc .fc-button-primary {
        background: #4e73df !important;
        border-color: #4e73df !important;
    }
    .fc .fc-button-primary:hover {
        background: #2e59d9 !important;
    }
    .fc .fc-button-primary:not(:disabled).fc-button-active {
        background: #2e59d9 !important;
    }

    .fc-event {
        cursor: pointer;
        transition: opacity 0.2s;
        border: none !important;
        padding: 2px 4px;
    }
    .fc-event:hover {
        opacity: 0.8;
    }

    .fc .fc-daygrid-day:hover {
        background-color: #f8f9fa;
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #ddd;
    }

    /* Nav Tabs Styling Bootstrap 4 */
    .nav-tabs .nav-link { font-weight: 700; color: #858796; border: none; padding: 12px 20px; }
    .nav-tabs .nav-link.active { color: #4e73df; border-bottom: 3px solid #4e73df; background: transparent; }

    /* Perbaikan Visual Tabel */
    .table thead th { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05em; color: #4e73df; }

    /* Modal Detail Styling */
    .detail-label { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; color: #b7b9cc; margin-bottom: 2px; }
    .detail-value { font-weight: 700; color: #3a3b45; margin-bottom: 15px; }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- STATS CARDS (DEFAULT SB ADMIN 2 - STABIL & BERWARNA) --}}
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Kegiatan</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $kalender->total() ?? 0 }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-calendar-alt fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-success text-uppercase mb-1">Kegiatan Aktif</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $kalender->where('status', 'aktif')->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-warning text-uppercase mb-1">Draft (Belum Rilis)</div>
                            <div class="h5 mb-0 fw-bold text-gray-800">{{ $kalender->where('status', 'draft')->count() }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-clock fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row gx-0 align-items-center">
                        <div class="col me-2">
                            <div class="text-xs fw-bold text-info text-uppercase mb-1">Tahun Ajaran</div>
                            <div class="h6 mb-0 fw-bold text-gray-800">{{ $tahunAjaranAktif->nama_tahun_ajaran ?? '-' }}</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-graduation-cap fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- MAIN CONTENT --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <h6 class="m-0 fw-bold text-primary">Agenda & Kalender Akademik</h6>
            <div class="btn-group">
                <div class="dropdown no-arrow me-2">
                    <button class="btn btn-success btn-sm dropdown-toggle fw-bold shadow-sm" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-print fa-sm me-1"></i> Cetak / Export PDF
                    </button>
                    <div class="dropdown-menu dropdown-menu-end shadow animated--fade-in p-3" style="min-width: 250px;">
                        <a class="dropdown-item mb-2" href="{{ route('sekretaris.kalender.cetak', ['jenis' => 'bulanan', 'bulan' => now()->format('Y-m')]) }}" target="_blank">
                            <i class="fas fa-calendar-day me-2 text-primary"></i> Cetak Bulan Ini
                        </a>
                        <a class="dropdown-item" href="{{ route('sekretaris.kalender.cetak', ['jenis' => 'tahunan']) }}" target="_blank">
                            <i class="fas fa-list-ul me-2 text-info"></i> Cetak List Tahunan
                        </a>
                        <hr class="my-2">
                        <div class="px-2">
                            <label class="small fw-bold">Custom Bulan:</label>
                            <input type="month" id="customMonth" class="form-control form-control-sm mb-2" value="{{ now()->format('Y-m') }}" onchange="updateCetakUrl()">
                            <a href="#" id="customCetakBtn" target="_blank" class="btn btn-primary btn-sm w-100">CETAK PDF</a>
                        </div>
                    </div>
                </div>
                <a href="{{ route('sekretaris.kalender.create') }}" class="btn btn-primary btn-sm fw-bold shadow-sm">
                    <i class="fas fa-plus-circle fa-sm me-1"></i> Tambah Agenda
                </a>
            </div>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs mb-4" id="calendarTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="cal-view-tab" data-bs-toggle="tab" href="#cal-view" role="tab"><i class="fas fa-th-large me-1"></i> Kalender Visual</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="list-view-tab" data-bs-toggle="tab" href="#list-view" role="tab"><i class="fas fa-list me-1"></i> Daftar Detail</a>
                </li>
            </ul>

            <div class="tab-content">
                <div class="tab-pane fade show active" id="cal-view" role="tabpanel">
                    <div id="calendar" class="border rounded"></div>
                </div>

                <div class="tab-pane fade" id="list-view" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th class="text-center" width="50">NO</th>
                                    <th>NAMA KEGIATAN</th>
                                    <th class="text-center">JENIS</th>
                                    <th class="text-center">TANGGAL</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center" width="120">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kalender as $index => $item)
                                <tr>
                                    <td class="text-center align-middle">{{ $index + 1 }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-dark">{{ $item->nama_kegiatan }}</div>
                                        @if($item->lampiran_surat)
                                            <a href="{{ asset('storage/' . $item->lampiran_surat) }}" target="_blank" class="small text-primary"><i class="fas fa-paperclip"></i> Lihat Lampiran</a>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle"><span class="badge bg-info shadow-sm">{{ $item->jenis_label }}</span></td>
                                    <td class="text-center align-middle small fw-bold">
                                        {{ $item->tanggal_mulai->format('d/m/Y') }}
                                        @if($item->tanggal_selesai) <br><span class="text-muted">s.d {{ $item->tanggal_selesai->format('d/m/Y') }}</span> @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($item->status == 'aktif') <span class="badge bg-success px-3">AKTIF</span>
                                        @elseif($item->status == 'draft') <span class="badge bg-warning text-white px-3">DRAFT</span>
                                        @else <span class="badge bg-secondary px-3">SELESAI</span> @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <a href="{{ route('sekretaris.kalender.edit', $item->id) }}"
                                               class="btn btn-warning btn-sm btn-circle shadow-sm"
                                               title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                    class="btn btn-danger btn-sm btn-circle shadow-sm"
                                                    onclick="confirmDelete({{ $item->id }}, `{{ str_replace('`', '\`', $item->nama_kegiatan) }}`)"
                                                    title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: DETAIL EVENT --}}
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered shadow-lg" role="document">
        <div class="modal-content border-0">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="eventTitle">Detail Agenda</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4" id="eventDetails">
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                <a href="#" id="editEventBtn" class="btn btn-warning btn-sm fw-bold shadow-sm">
                    <i class="fas fa-edit me-1"></i> Edit Kegiatan
                </a>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: KONFIRMASI HAPUS (BOOTSTRAP MODAL - SAMA SEPERTI PENGUMUMAN) --}}
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
</div>

@endsection

@section('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js'></script>
<script>
    // 1. LOGIC UPDATE URL GENERATE PDF
    function updateCetakUrl() {
        const val = document.getElementById('customMonth').value;
        const btn = document.getElementById('customCetakBtn');
        if(val) {
            btn.href = "{{ route('sekretaris.kalender.cetak') }}?jenis=bulanan&bulan=" + val;
        }
    }

    // 2. LOGIC MODAL HAPUS - EXACT COPY DARI PENGUMUMAN YANG BERHASIL
    function confirmDelete(id, name) {
        document.getElementById('deleteKalenderName').textContent = name;
        document.getElementById('deleteForm').action = '/sekretaris/kalender/' + id;
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }

    // 3. INITIALIZE FULLCALENDAR & EVENT HANDLING
    document.addEventListener('DOMContentLoaded', function() {
        updateCetakUrl();

        var calendarEl = document.getElementById('calendar');

        if (!calendarEl) {
            console.error('Calendar element not found');
            return;
        }

        window.calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'id',
            height: 'auto',
            contentHeight: 'auto',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            buttonText: { today: 'Hari Ini', month: 'Bulan', week: 'Minggu' },

            // Logic Fetch Events dari API
            events: function(info, successCallback, failureCallback) {
                try {
                    const bulanObj = info.start;
                    const bulan = bulanObj.getFullYear() + '-' + String(bulanObj.getMonth() + 1).padStart(2, '0');

                    fetch('{{ route("sekretaris.kalender.bulanan") }}?bulan=' + bulan)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Events fetched:', data);
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Fetch error:', error);
                            failureCallback(error);
                        });
                } catch (error) {
                    console.error('Error in events function:', error);
                    failureCallback(error);
                }
            },

            // Logic Klik Event (Popup Detail)
            eventClick: function(info) {
                const event = info.event;
                const start = event.start ? event.start.toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '-';
                const endDate = event.end ? new Date(event.end.getTime() - 86400000) : null;
                const end = endDate ? ' s.d ' + endDate.toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '';

                document.getElementById('eventTitle').textContent = event.title;
                document.getElementById('eventDetails').innerHTML = `
                    <div class="detail-label">Waktu Agenda:</div>
                    <div class="detail-value text-primary h6">${start}${end}</div>

                    <div class="detail-label">Keterangan:</div>
                    <div class="detail-value">${event.extendedProps.keterangan || 'Tidak ada keterangan tambahan'}</div>

                    <div class="detail-label">Jenis Agenda:</div>
                    <div><span class="badge bg-info">${event.extendedProps.jenis || 'Umum'}</span></div>
                `;
                document.getElementById('editEventBtn').href = "/sekretaris/kalender/" + event.id + "/edit";
                const eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
                eventModal.show();
            }
        });

        window.calendar.render();

        // Logic Re-render saat ganti tab (agar Kalender tidak ciut) - Bootstrap 5
        const tabElements = document.querySelectorAll('a[data-bs-toggle="tab"]');
        tabElements.forEach(function(tabEl) {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                if (event.target.id === 'cal-view-tab' && window.calendar) {
                    setTimeout(() => {
                        window.calendar.updateSize();
                    }, 100);
                }
            });
        });
    });
</script>
@endsection
