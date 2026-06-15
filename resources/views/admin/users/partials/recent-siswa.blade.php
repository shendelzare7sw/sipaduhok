<div class="col-12">
    <div class="card">
        <div class="card-header">
            <div>
                <h5>
                    <i class="fas fa-user-graduate section-title-icon section-title-icon-blue"></i>
                    Siswa Terbaru
                </h5>
                <small>5 data siswa yang baru ditambahkan</small>
            </div>
            <div>
                @if($siswa->count() > 0)
                    <a href="{{ route('admin.users.siswa') }}" class="btn btn-outline">
                        Lihat Semua <i class="fas fa-arrow-right icon-ms"></i>
                    </a>
                @else
                    <a href="{{ route('admin.users.create-siswa') }}" class="btn btn-primary">
                        <i class="fas fa-plus icon-me"></i> Tambah Siswa
                    </a>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($siswa->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Nama Lengkap</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th class="actions-header actions-header-wide">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswa as $s)
                                <tr>
                                    <td class="desktop-only-cell">
                                        <div class="entity-name">{{ $s->nama_lengkap }}</div>
                                        <small class="entity-meta">{{ $s->user->email ?? 'No Email' }}</small>
                                    </td>
                                    <td class="mobile-only-cell mobile-card-head">
                                        <div class="entity-name">{{ $s->nama_lengkap }}</div>
                                        <div class="mobile-badge-row">
                                            @if($s->kelas)
                                                <span class="badge badge-info badge-xs">{{ $s->kelas->nama_kelas }}</span>
                                            @endif
                                            @if($s->status === 'aktif')
                                                <span class="badge badge-success badge-xs">Aktif</span>
                                            @else
                                                <span class="badge badge-warning badge-xs">{{ ucfirst($s->status) }}</span>
                                            @endif
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
                                            <span class="badge badge-info">{{ $s->kelas->nama_kelas }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td class="desktop-only-cell">
                                        @if($s->status === 'aktif')
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-warning">{{ ucfirst($s->status) }}</span>
                                        @endif
                                    </td>
                                    <td class="mobile-card-actions actions-cell">
                                        <div class="action-group">
                                            <a href="{{ route('admin.users.show-siswa', $s->id) }}"
                                                class="btn btn-icon btn-light-primary"
                                                title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit-siswa', $s->id) }}"
                                                class="btn btn-icon btn-light-warning"
                                                title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-icon btn-light-danger"
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
</div>
