<div class="col-12 mt-4">
    <div class="card">
        <div class="card-header">
            <div>
                <h5>
                    <i class="fas fa-user-friends section-title-icon section-title-icon-yellow"></i>
                    Orang Tua Terbaru
                </h5>
                <small>5 data orang tua yang baru ditambahkan</small>
            </div>
            <div>
                @if($orangTua->count() > 0)
                    <a href="{{ route('admin.users.orang-tua') }}" class="btn btn-outline">
                        Lihat Semua <i class="fas fa-arrow-right icon-ms"></i>
                    </a>
                @else
                    <a href="{{ route('admin.users.orang-tua') }}" class="btn btn-primary">
                        <i class="fas fa-arrow-right icon-me"></i> Kelola Orang Tua
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($orangTua->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Anak (Siswa)</th>
                                <th>Status</th>
                                <th class="actions-header actions-header-small">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($orangTua as $ortu)
                                <tr>
                                    <td class="desktop-only-cell">
                                        <div class="entity-name">{{ $ortu->name }}</div>
                                        <small class="entity-meta">
                                            Dibuat: {{ $ortu->created_at->copy()->locale('id')->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td class="mobile-only-cell mobile-card-head">
                                        <div class="entity-name">{{ $ortu->name }}</div>
                                        <div class="mobile-badge-row">
                                            @if($ortu->is_active)
                                                <span class="badge badge-success badge-xs">Aktif</span>
                                            @else
                                                <span class="badge badge-warning badge-xs">Non-Aktif</span>
                                            @endif
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
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-warning">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="mobile-card-actions actions-cell">
                                        <a href="{{ route('admin.users.orang-tua') }}"
                                            class="btn btn-icon btn-light-primary"
                                            title="Kelola">
                                            <i class="fas fa-cog"></i>
                                        </a>
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
</div>
