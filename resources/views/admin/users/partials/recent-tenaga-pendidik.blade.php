<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div>
                <h5>
                    <i class="fas fa-chalkboard-teacher section-title-icon section-title-icon-green"></i>
                    Tenaga Pendidik Terbaru
                </h5>
                <small>5 data tenaga pendidik yang baru ditambahkan</small>
            </div>
            <div>
                @if($tenagaPendidik->count() > 0)
                    <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn btn-outline">
                        Lihat Semua <i class="fas fa-arrow-right icon-ms"></i>
                    </a>
                @else
                    <a href="{{ route('admin.users.create-tenaga-pendidik') }}" class="btn btn-primary">
                        <i class="fas fa-plus icon-me"></i> Tambah Baru
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($tenagaPendidik->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>NIP</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th class="actions-header actions-header-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tenagaPendidik as $tp)
                                @php
                                    $roleLabel = ucwords(str_replace('_', ' ', $tp->user->role));
                                @endphp
                                <tr>
                                    <td class="desktop-only-cell">
                                        <div class="entity-name">{{ $tp->nama_lengkap }}</div>
                                        <small class="entity-meta">
                                            Dibuat: {{ $tp->created_at->copy()->locale('id')->diffForHumans() }}
                                        </small>
                                    </td>
                                    <td class="mobile-only-cell mobile-card-head">
                                        <div class="entity-name">{{ $tp->nama_lengkap }}</div>
                                        <div class="mobile-badge-row">
                                            <span class="badge badge-info badge-xs">{{ $roleLabel }}</span>
                                            @if($tp->user->is_active)
                                                <span class="badge badge-success badge-xs">Aktif</span>
                                            @else
                                                <span class="badge badge-warning badge-xs">Non-Aktif</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="NIP" class="cell-sm">{{ $tp->nip ?? '-' }}</td>
                                    <td data-label="Email" class="cell-sm">{{ $tp->user->email }}</td>
                                    <td class="desktop-only-cell">
                                        <span class="badge badge-info">{{ $roleLabel }}</span>
                                    </td>
                                    <td class="desktop-only-cell">
                                        @if($tp->user->is_active)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-warning">Non-Aktif</span>
                                        @endif
                                    </td>
                                    <td class="mobile-card-actions actions-cell">
                                        <div class="action-group">
                                            <a href="{{ route('admin.users.show-tenaga-pendidik', $tp->id) }}"
                                                class="btn btn-icon btn-light-primary"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit-tenaga-pendidik', $tp->id) }}"
                                                class="btn btn-icon btn-light-warning"
                                                title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-icon btn-light-danger"
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
</div>
