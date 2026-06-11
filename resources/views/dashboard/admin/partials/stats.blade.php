<div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="dashboard-card">
            <div class="stat-widget">
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($totalSiswa) }}</div>
                    <div class="stat-label">Siswa Aktif</div>
                </div>
                <div class="stat-icon-wrapper stat-icon-wrapper-blue">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div class="stat-footer px-4 pb-3">
                <span>
                    <i class="fas fa-arrow-up text-success me-1"></i>
                    <span class="text-success fw-medium">{{ $siswaBaruBulanIni }}</span> bulan ini
                </span>
                <i class="fas fa-users text-muted opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="dashboard-card">
            <div class="stat-widget">
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($totalGuru) }}</div>
                    <div class="stat-label">Tenaga Pendidik</div>
                </div>
                <div class="stat-icon-wrapper stat-icon-wrapper-green">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <div class="stat-footer px-4 pb-3">
                <span>Terdaftar aktif di sistem</span>
                <i class="fas fa-check-circle text-muted opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="dashboard-card">
            <div class="stat-widget">
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($totalKelas) }}</div>
                    <div class="stat-label">Total Kelas</div>
                </div>
                <div class="stat-icon-wrapper stat-icon-wrapper-yellow">
                    <i class="fas fa-school"></i>
                </div>
            </div>
            <div class="stat-footer px-4 pb-3">
                <span>Avg. {{ $totalKelas > 0 ? round($totalSiswa / $totalKelas) : 0 }} siswa/kelas</span>
                <i class="fas fa-layer-group text-muted opacity-50"></i>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="dashboard-card">
            <div class="stat-widget">
                <div class="stat-details">
                    <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
                    <div class="stat-label">Akun Pengguna</div>
                </div>
                <div class="stat-icon-wrapper stat-icon-wrapper-purple">
                    <i class="fas fa-id-badge"></i>
                </div>
            </div>
            <div class="stat-footer px-4 pb-3">
                <span>Telah memiliki kredensial</span>
                <i class="fas fa-shield-alt text-muted opacity-50"></i>
            </div>
        </div>
    </div>
</div>
