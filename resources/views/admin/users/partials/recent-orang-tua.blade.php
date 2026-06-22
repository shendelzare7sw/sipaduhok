<div class="usr-card">
    <div class="usr-card-header">
        <div>
            <h5 class="usr-card-title">
                <i class="fas fa-user-friends section-icon-yellow"></i>
                Orang Tua Terbaru
            </h5>
            <div class="usr-card-subtitle">{{ $orangTua->count() }} data orang tua yang baru ditambahkan</div>
        </div>
        <div>
            @if($orangTua->count() > 0)
                <a href="{{ route('admin.users.orang-tua') }}" class="btn-outline-action">
                    Lihat Semua <i class="fas fa-arrow-right icon-ms"></i>
                </a>
            @else
                <a href="{{ route('admin.users.orang-tua') }}" class="btn-primary-action">
                    <i class="fas fa-arrow-right icon-me"></i> Kelola Orang Tua
                </a>
            @endif
        </div>
    </div>
    <div>
        @if($orangTua->count() > 0)
            <div class="table-responsive">
                <table class="table-clean table-card-mobile">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Anak (Siswa)</th>
                            <th>Status</th>
                            <th class="text-end" width="80">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orangTua as $ortu)
                            @php
                                $initials = strtoupper(substr($ortu->name, 0, 2));
                            @endphp
                            <tr>
                                <td class="desktop-only-cell">
                                    <div class="entity-info">
                                        <div class="entity-avatar entity-avatar-yellow">{{ $initials }}</div>
                                        <div>
                                            <div class="entity-name">{{ $ortu->name }}</div>
                                            <div class="entity-meta">
                                                Dibuat: {{ $ortu->created_at->copy()->locale('id')->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="mobile-only-cell mobile-card-head">
                                    <div class="entity-info">
                                        <div class="entity-avatar entity-avatar-yellow">{{ $initials }}</div>
                                        <div>
                                            <div class="entity-name">{{ $ortu->name }}</div>
                                            <div class="mobile-badge-row">
                                                @if($ortu->is_active)
                                                    <span class="badge-jnj badge-success badge-xs">Aktif</span>
                                                @else
                                                    <span class="badge-jnj badge-warning badge-xs">Non-Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Username" class="mono-cell">{{ $ortu->username }}</td>
                                <td data-label="Email" class="cell-sm">{{ $ortu->email }}</td>
                                <td data-label="Anak (Siswa)">
                                    @if($ortu->studentParents && $ortu->studentParents->count() > 0)
                                        <div class="children-list">
                                            @foreach($ortu->studentParents->take(2) as $sp)
                                                <span class="child-name">
                                                    <i class="fas fa-user-graduate"></i>
                                                    {{ $sp->siswa->nama_lengkap }}
                                                </span>
                                            @endforeach
                                            @if($ortu->studentParents->count() > 2)
                                                <small class="children-more">
                                                    +{{ $ortu->studentParents->count() - 2 }} lainnya
                                                </small>
                                            @endif
                                        </div>
                                    @else
                                        <span class="orphan-warning">
                                            <i class="fas fa-exclamation-circle"></i> Belum ada
                                        </span>
                                    @endif
                                </td>
                                <td class="desktop-only-cell">
                                    @if($ortu->is_active)
                                        <span class="badge-jnj badge-success">Aktif</span>
                                    @else
                                        <span class="badge-jnj badge-warning">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-btns">
                                        <a href="{{ route('admin.users.orang-tua') }}"
                                            class="btn btn-light-primary"
                                            title="Kelola">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-user-friends"></i>
                <p>Belum ada data Orang Tua.</p>
            </div>
        @endif
    </div>
</div>
