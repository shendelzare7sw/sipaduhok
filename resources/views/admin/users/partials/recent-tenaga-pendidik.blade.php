<div class="usr-card">
    <div class="usr-card-header">
        <div>
            <h5 class="usr-card-title">
                <i class="fas fa-chalkboard-teacher section-icon-green"></i>
                Tenaga Pendidik Terbaru
            </h5>
            <div class="usr-card-subtitle">5 data tenaga pendidik yang baru ditambahkan</div>
        </div>
        <div>
            @if($tenagaPendidik->count() > 0)
                <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn-outline-action">
                    Lihat Semua <i class="fas fa-arrow-right icon-ms"></i>
                </a>
            @else
                <a href="{{ route('admin.users.create-tenaga-pendidik') }}" class="btn-primary-action">
                    <i class="fas fa-plus icon-me"></i> Tambah Baru
                </a>
            @endif
        </div>
    </div>
    <div>
        @if($tenagaPendidik->count() > 0)
            <div class="table-responsive">
                <table class="table-clean table-card-mobile">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>NIP</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="text-end" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tenagaPendidik as $tp)
                            @php
                                $roleLabel = ucwords(str_replace('_', ' ', $tp->user->role));
                                $initials  = strtoupper(substr($tp->nama_lengkap, 0, 2));
                            @endphp
                            <tr>
                                <td class="desktop-only-cell">
                                    <div class="entity-info">
                                        <div class="entity-avatar entity-avatar-green">{{ $initials }}</div>
                                        <div>
                                            <div class="entity-name">{{ $tp->nama_lengkap }}</div>
                                            <div class="entity-meta">
                                                Dibuat: {{ $tp->created_at->copy()->locale('id')->diffForHumans() }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="mobile-only-cell mobile-card-head">
                                    <div class="entity-info">
                                        <div class="entity-avatar entity-avatar-green">{{ $initials }}</div>
                                        <div>
                                            <div class="entity-name">{{ $tp->nama_lengkap }}</div>
                                            <div class="mobile-badge-row">
                                                <span class="badge-jnj badge-info badge-xs">{{ $roleLabel }}</span>
                                                @if($tp->user->is_active)
                                                    <span class="badge-jnj badge-success badge-xs">Aktif</span>
                                                @else
                                                    <span class="badge-jnj badge-warning badge-xs">Non-Aktif</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="NIP" class="cell-sm">{{ $tp->nip ?? '-' }}</td>
                                <td data-label="Email" class="cell-sm">{{ $tp->user->email }}</td>
                                <td class="desktop-only-cell">
                                    <span class="badge-jnj badge-info">{{ $roleLabel }}</span>
                                </td>
                                <td class="desktop-only-cell">
                                    @if($tp->user->is_active)
                                        <span class="badge-jnj badge-success">Aktif</span>
                                    @else
                                        <span class="badge-jnj badge-warning">Non-Aktif</span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-btns">
                                        <a href="{{ route('admin.users.show-tenaga-pendidik', $tp->id) }}"
                                            class="btn btn-light-primary"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit-tenaga-pendidik', $tp->id) }}"
                                            class="btn btn-light-warning"
                                            title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-light-danger"
                                            title="Hapus Data"
                                            data-delete-tenaga-pendidik
                                            data-id="{{ $tp->id }}"
                                            data-name="{{ $tp->nama_lengkap }}"
                                            data-email="{{ $tp->user->email }}"
                                            data-role="{{ $roleLabel }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-chalkboard-teacher"></i>
                <p>Belum ada data Tenaga Pendidik.</p>
            </div>
        @endif
    </div>
</div>
