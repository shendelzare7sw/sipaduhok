<div class="usr-card">
    <div class="usr-card-header">
        <div>
            <h5 class="usr-card-title">
                <i class="fas fa-user-graduate section-icon-blue"></i>
                Siswa Terbaru
            </h5>
            <div class="usr-card-subtitle">5 data siswa yang baru ditambahkan</div>
        </div>
        <div>
            @if($siswa->count() > 0)
                <a href="{{ route('admin.users.siswa') }}" class="btn-outline-action">
                    Lihat Semua <i class="fas fa-arrow-right icon-ms"></i>
                </a>
            @else
                <a href="{{ route('admin.users.create-siswa') }}" class="btn-primary-action">
                    <i class="fas fa-plus icon-me"></i> Tambah Siswa
                </a>
            @endif
        </div>
    </div>
    <div>
        @if($siswa->count() > 0)
            <div class="table-responsive">
                <table class="table-clean table-card-mobile">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th class="text-end" width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswa as $s)
                            @php
                                $initials = strtoupper(substr($s->nama_lengkap, 0, 2));
                            @endphp
                            <tr>
                                <td class="desktop-only-cell">
                                    <div class="entity-info">
                                        <div class="entity-avatar entity-avatar-blue">{{ $initials }}</div>
                                        <div>
                                            <div class="entity-name">{{ $s->nama_lengkap }}</div>
                                            <div class="entity-meta">{{ $s->user->email ?? 'No Email' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="mobile-only-cell mobile-card-head">
                                    <div class="entity-info">
                                        <div class="entity-avatar entity-avatar-blue">{{ $initials }}</div>
                                        <div>
                                            <div class="entity-name">{{ $s->nama_lengkap }}</div>
                                            <div class="mobile-badge-row">
                                                @if($s->kelas)
                                                    <span class="badge-jnj badge-info badge-xs">{{ $s->kelas->nama_kelas }}</span>
                                                @endif
                                                @if($s->status === 'aktif')
                                                    <span class="badge-jnj badge-success badge-xs">Aktif</span>
                                                @else
                                                    <span class="badge-jnj badge-warning badge-xs">{{ ucfirst($s->status) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="NIS">
                                    <span class="text-strong-sm">{{ $s->nis }}</span>
                                </td>
                                <td data-label="NISN">
                                    <span class="text-muted-sm">{{ $s->nisn }}</span>
                                </td>
                                <td class="desktop-only-cell">
                                    @if($s->kelas)
                                        <span class="badge-jnj badge-info">{{ $s->kelas->nama_kelas }}</span>
                                    @else
                                        <span class="text-muted-sm">-</span>
                                    @endif
                                </td>
                                <td class="desktop-only-cell">
                                    @if($s->status === 'aktif')
                                        <span class="badge-jnj badge-success">Aktif</span>
                                    @else
                                        <span class="badge-jnj badge-warning">{{ ucfirst($s->status) }}</span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-btns">
                                        <a href="{{ route('admin.users.show-siswa', $s->id) }}"
                                            class="btn btn-light-primary"
                                            title="Lihat Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit-siswa', $s->id) }}"
                                            class="btn btn-light-warning"
                                            title="Edit Data">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-light-danger"
                                            title="Hapus Data"
                                            data-delete-siswa
                                            data-id="{{ $s->id }}"
                                            data-name="{{ $s->nama_lengkap }}"
                                            data-nis="{{ $s->nis }}"
                                            data-nisn="{{ $s->nisn }}"
                                            data-kelas="{{ $s->kelas ? $s->kelas->nama_kelas : '' }}">
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
                <i class="fas fa-user-graduate"></i>
                <p>Belum ada data Siswa.</p>
            </div>
        @endif
    </div>
</div>
