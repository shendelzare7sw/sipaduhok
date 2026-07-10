<div class="dashboard-card flex-grow-1">
    <div class="card-header-clean">
        <h5 class="card-title-clean">
            <i class="fas fa-history card-title-icon"></i> Login Pengguna Terbaru
        </h5>
    </div>
    <div class="activity-feed">
        @forelse($recent_logins as $login)
            <div class="activity-item">
                <div class="activity-avatar">
                    {{ strtoupper(substr($login->name, 0, 1)) }}
                </div>
                <div class="activity-content">
                    <div class="activity-title">{{ $login->name }}</div>
                    <div class="activity-meta">
                        <span class="badge bg-label-primary me-1">
                            {{ $login->role_label }}
                        </span>
                        <i class="far fa-clock ms-1 me-1"></i>
                        {{ \Carbon\Carbon::parse($login->last_login_at)->locale('id')->diffForHumans() }}
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                <i class="fas fa-inbox mb-2 fs-2"></i>
                <p class="mb-0">Belum ada aktivitas</p>
            </div>
        @endforelse
    </div>
</div>
