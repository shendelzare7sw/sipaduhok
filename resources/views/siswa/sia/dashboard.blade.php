@extends('layouts.sneat')

@section('title', 'Dashboard SIA')
@section('page-title', 'Sistem Informasi Akademik')
@section('page-subtitle', 'Selamat datang, ' . $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('siswa.partials.sneat-sidebar-sia')
@endsection

@section('styles')
<style>
    /* === FIX SIDEBAR OVERLAP ISSUE === */
    .layout-menu {
        z-index: 1045 !important;
    }

    .menu-item .menu-link {
        pointer-events: auto !important;
    }

    /* === 1. TATA LETAK & WELCOME ALERT === */
    .dashboard-wrapper {
        max-width: 1300px;
        margin: 0 auto;
        padding: 0;
        background: transparent;
    }

    .welcome-alert {
        background: #fff;
        border: none;
        border-left: 5px solid #4e73df !important;
        border-radius: 12px;
        box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.1);
    }

    /* === 2. PENGUMUMAN (BANNER ATAS) === */
    .announcement-card {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        color: white;
        border-radius: 15px;
        border: none;
    }

    .announcement-item-box {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 10px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(5px);
    }

    /* === 3. CARD CUSTOM (CHART & JADWAL) === */
    .card-custom {
        background: white;
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        height: 100%;
        overflow: hidden;
    }

    .card-header-custom {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-weight: 700;
        color: #4e73df;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fff;
    }

    /* === 4. ABSENSI (STAT CARD VIBRANT) === */
    .stat-card-vibrant {
        padding: 20px;
        border-radius: 12px;
        position: relative;
        overflow: hidden;
        color: white !important;
        border: none;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }

    .stat-card-vibrant:hover {
        transform: translateY(-5px);
    }

    .stat-card-vibrant .stat-number {
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
    }

    .stat-card-vibrant .stat-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        opacity: 0.9;
    }

    .stat-icon-bg {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 45px;
        opacity: 0.2;
    }

    .bg-grad-green  { background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; }
    .bg-grad-blue   { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%) !important; }
    .bg-grad-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important; }
    .bg-grad-red    { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important; }

    /* === 5. MENU CEPAT === */
    .menu-item-quick {
        background: white;
        padding: 25px 15px;
        border-radius: 15px;
        text-align: center;
        text-decoration: none !important;
        border: 1px solid #f1f5f9;
        transition: all 0.3s;
        display: block;
        height: 100%;
    }

    .menu-item-quick:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        border-color: #4e73df;
    }

    .menu-item-quick i {
        font-size: 2rem;
        margin-bottom: 15px;
        display: block;
    }

    .menu-text {
        font-weight: 700;
        color: #333;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Flyer Modal */
    .modal-flyer {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.8);
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(5px);
    }

    .modal-flyer.show {
        display: flex;
    }

    .modal-flyer-content {
        background: white;
        border-radius: 20px;
        max-width: 450px;
        width: 90%;
        overflow: hidden;
        position: relative;
        animation: zoomIn 0.3s ease;
    }

    @keyframes zoomIn {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .flyer-close {
        position: absolute;
        right: 15px;
        top: 15px;
        background: #fff;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
</style>
@endsection

@section('content')
<div class="dashboard-wrapper">

    {{-- ALERT SELAMAT DATANG --}}
    <div class="alert welcome-alert shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-12">
                <h5 class="fw-bold text-primary mb-1">Halo, {{ $siswa->nama_lengkap }}! 👋</h5>
                <p class="mb-0 text-muted small">Selamat belajar! Jangan lupa pantau progres akademik Anda hari ini.</p>
            </div>
        </div>
    </div>

    {{-- 1. PENGUMUMAN TERBARU (BAGIAN PERTAMA) --}}
    @if(isset($pengumuman) && $pengumuman->count() > 0)
    <div class="card announcement-card shadow mb-4">
        <div class="card-body p-4">
            <h6 class="fw-bold mb-3"><i class="fas fa-bullhorn me-2"></i>Pengumuman Instansi</h6>
            <div class="row">
                @foreach($pengumuman->take(2) as $item)
                <div class="col-md-6 mb-2 mb-md-0">
                    <div class="announcement-item-box">
                        <div class="fw-bold small text-white">{{ $item->judul }}</div>
                        <p class="small mb-1 opacity-75">{{ Str::limit($item->isi_pengumuman, 120) }}</p>
                        <small class="fw-bold"><i class="far fa-calendar-alt me-1"></i>{{ $item->tanggal_pengumuman->format('d M Y') }}</small>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <div class="row mb-4">
        {{-- 2. GRAFIK PERFORMA SISWA (KIRI) --}}
        <div class="col-lg-5 mb-4">
            <div class="card-custom shadow-sm">
                <div class="card-header-custom">
                    <span><i class="fas fa-chart-pie me-2"></i>Progres Belajar</span>
                </div>
                <div class="card-body">
                    <div style="height: 200px;">
                        <canvas id="performaChart"></canvas>
                    </div>
                    <div class="row text-center mt-4 g-0">
                        <div class="col-4 border-end"><div class="text-xs fw-bold text-success">LULUS</div><div class="h5 mb-0 fw-bold">{{ $performa['tugas']['selesai'] ?? 0 }}</div></div>
                        <div class="col-4 border-end"><div class="text-xs fw-bold text-warning">PROSES</div><div class="h5 mb-0 fw-bold">{{ ($performa['tugas']['total'] ?? 0) - ($performa['tugas']['selesai'] ?? 0) }}</div></div>
                        <div class="col-4"><div class="text-xs fw-bold text-danger">TUNDA</div><div class="h5 mb-0 fw-bold">0</div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. JADWAL HARI INI (KANAN) --}}
        <div class="col-lg-7 mb-4">
            <div class="card-custom shadow-sm">
                <div class="card-header-custom">
                    <span><i class="fas fa-clock me-2"></i>Jadwal Hari Ini ({{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }})</span>
                    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                    <a href="{{ route('siswa.lms.jadwal') }}" class="btn btn-sm btn-link text-primary fw-bold">Lihat Semua</a>
                    @endif
                </div>
                @if(isset($jadwalHariIni) && $jadwalHariIni->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light small fw-bold">
                            <tr><th class="ps-4">JAM</th><th>MATA PELAJARAN</th><th>GURU PENGAJAR</th><th class="text-center">AKSI</th></tr>
                        </thead>
                        <tbody class="small">
                            @foreach($jadwalHariIni as $jadwal)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</td>
                                <td>
                                    <span class="d-block fw-bold text-dark">{{ $jadwal->mataPelajaran->nama_mapel }}</span>
                                    <span class="text-muted small">{{ $jadwal->mataPelajaran->kode_mapel }}</span>
                                </td>
                                <td>{{ $jadwal->guru ? $jadwal->guru->nama_lengkap : '-' }}</td>
                                <td class="text-center">
                                    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
                                    <a href="{{ route('siswa.lms.mapel.show', $jadwal->mata_pelajaran_id) }}" class="btn btn-sm btn-primary rounded-pill px-3">Masuk</a>
                                    @else
                                    <span class="badge bg-secondary">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-25"></i>
                    <p class="text-muted mb-0">Tidak ada jadwal pelajaran hari ini</p>
                    <small class="text-muted">Silakan hubungi wali kelas atau admin</small>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- 3. REKAP ABSENSI (BAGIAN KETIGA) --}}
    <h6 class="fw-bold text-muted mb-3 ms-1"><i class="fas fa-calendar-check me-2 text-primary"></i>Statistik Kehadiran Bulan Ini</h6>
    <div class="row mb-4">
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card-vibrant bg-grad-green">
                <div class="stat-content"><div class="stat-title">Hadir</div><div class="stat-number">{{ $absensi['hadir'] ?? 0 }}</div></div>
                <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card-vibrant bg-grad-blue">
                <div class="stat-content"><div class="stat-title">Sakit</div><div class="stat-number">{{ $absensi['sakit'] ?? 0 }}</div></div>
                <div class="stat-icon-bg"><i class="fas fa-notes-medical"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card-vibrant bg-grad-orange">
                <div class="stat-content"><div class="stat-title">Izin</div><div class="stat-number">{{ $absensi['izin'] ?? 0 }}</div></div>
                <div class="stat-icon-bg"><i class="fas fa-envelope"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-3 mb-3">
            <div class="stat-card-vibrant bg-grad-red">
                <div class="stat-content"><div class="stat-title">Alpha</div><div class="stat-number">{{ $absensi['alpha'] ?? 0 }}</div></div>
                <div class="stat-icon-bg"><i class="fas fa-times-circle"></i></div>
            </div>
        </div>
    </div>

    {{-- 4. MENU CEPAT (DIPERBAIKI TATA LETAKNYA) --}}
    <h6 class="fw-bold text-muted mb-3 ms-1"><i class="fas fa-th me-2 text-warning"></i>Akses Layanan Cepat</h6>
    <div class="row mb-5">
        <div class="col-6 col-lg-4 mb-3">
            <a href="{{ route('siswa.sia.presensi.index') }}" class="menu-item-quick shadow-sm">
                <i class="fas fa-user-check text-primary"></i>
                <div class="menu-text">Presensi</div>
            </a>
        </div>
        <div class="col-6 col-lg-4 mb-3">
            <a href="{{ route('siswa.sia.penilaian') }}" class="menu-item-quick shadow-sm">
                <i class="fas fa-chart-line text-success"></i>
                <div class="menu-text">Nilai Tugas</div>
            </a>
        </div>
        <div class="col-6 col-lg-4 mb-3">
            <a href="{{ route('siswa.sia.rapor.index') }}" class="menu-item-quick shadow-sm">
                <i class="fas fa-file-signature text-info"></i>
                <div class="menu-text">E-Rapor</div>
            </a>
        </div>
    </div>

    {{-- LMS BANNER --}}
    @if($siswa->kelas && in_array($siswa->kelas->jenjang, ['SMP', 'SMA']))
    <div class="card border-0 shadow-lg mb-5 overflow-hidden" style="border-radius: 20px; background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <div class="card-body p-4 text-white d-flex align-items-center justify-content-between flex-wrap">
            <div class="d-flex align-items-center mb-3 mb-md-0">
                <i class="fas fa-graduation-cap fa-3x me-4 opacity-50 d-none d-md-block"></i>
                <div>
                    <h5 class="fw-bold mb-1">House Of Knowledge LMS</h5>
                    <p class="mb-0 small opacity-75">Klik untuk mengerjakan tugas dan materi online hari ini.</p>
                </div>
            </div>
            <a href="{{ route('siswa.lms.dashboard') }}" class="btn btn-light fw-bold text-success rounded-pill px-4 shadow">MASUK LMS</a>
        </div>
    </div>
    @endif

</div>

{{-- FLYER POPUP (DIPERTAHANKAN) --}}
@if(isset($flyers) && $flyers->count() > 0)
<div class="modal-flyer" id="flyerModal">
    <div class="modal-flyer-content shadow-lg">
        <span class="flyer-close" onclick="closeFlyerModal()"><i class="fas fa-times"></i></span>
        @foreach($flyers->take(1) as $flyer)
            <img src="{{ $flyer->gambar_url }}" style="width: 100%; height: 250px; object-fit: cover;">
            <div class="p-4 text-center">
                <h5 class="fw-bold text-primary mb-2">{{ $flyer->judul }}</h5>
                <p class="small text-muted mb-3">{{ $flyer->deskripsi }}</p>
                @if($flyer->link_url)
                    <a href="{{ $flyer->link_url }}" target="_blank" class="btn btn-primary btn-sm px-4 rounded-pill fw-bold">LIHAT SELENGKAPNYA</a>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endif

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. CHART LOGIC
    const ctx = document.getElementById('performaChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Lulus', 'Proses', 'Tunda'],
                datasets: [{
                    data: [{{ $performa['tugas']['selesai'] ?? 0 }}, {{ ($performa['tugas']['total'] ?? 0) - ($performa['tugas']['selesai'] ?? 0) }}, 0],
                    backgroundColor: ['#10b981', '#f6c23e', '#e74a3b'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }

    // 2. FLYER LOGIC (Muncul Sekali per User)
    const userId = '{{ auth()->id() }}';
    if (!localStorage.getItem('flyerShown_' + userId)) {
        setTimeout(() => {
            const modal = document.getElementById('flyerModal');
            if(modal) modal.classList.add('show');
        }, 1200);
    }
});

function closeFlyerModal() {
    document.getElementById('flyerModal').classList.remove('show');
    localStorage.setItem('flyerShown_' + '{{ auth()->id() }}', 'true');
}
</script>
@endsection
