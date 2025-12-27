@extends('layouts.sneat')

@section('title', 'Manajemen User')

@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Overview data Tenaga Pendidik dan Siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* --- STATS CARD STYLING (Solid Gradient - High Contrast) --- */
.stat-card {
    padding: 28px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-content {
    position: relative;
    z-index: 2;
}

.stat-title {
    font-size: 14px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 42px;
    font-weight: 700;
    margin-bottom: 6px;
    line-height: 1.2;
}

.stat-desc {
    font-size: 14px;
    opacity: 0.85;
}

.stat-icon-bg {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 75px;
    opacity: 0.15;
    z-index: 1;
}

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-purple { background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%); }
.bg-gradient-red { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); }

/* --- Base Styles --- */
.card { 
    background: white; 
    border-radius: 12px; 
    box-shadow: 0 1px 3px rgba(0,0,0,0.1); 
    margin-bottom: 24px; 
    border: none; 
}

.card-header { 
    padding: 20px 24px; 
    border-bottom: 1px solid #e5e7eb; 
    background: #fff; 
    border-radius: 12px 12px 0 0; 
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
}

.card-header h5 { 
    margin: 0; 
    font-size: 17px; 
    font-weight: 600; 
    color: #111827; 
}

.card-header small {
    font-size: 13px;
    color: #6b7280;
    display: block;
    margin-top: 4px;
}

/* Grid System */
.row { display: flex; flex-wrap: wrap; margin: -12px; }
.col-md-3 { flex: 0 0 25%; max-width: 25%; padding: 12px; }
.col-12 { flex: 0 0 100%; max-width: 100%; padding: 12px; }

/* Table Styling */
.table-responsive { overflow-x: auto; }
.table { width: 100%; border-collapse: collapse; }
.table th { 
    text-align: left; 
    padding: 14px 18px; 
    background: #f9fafb; 
    color: #4b5563; 
    font-weight: 600; 
    font-size: 14px; 
    text-transform: uppercase; 
    border-bottom: 1px solid #e5e7eb; 
}

.table td { 
    padding: 14px 18px; 
    border-bottom: 1px solid #f3f4f6; 
    color: #374151; 
    font-size: 15px; 
    vertical-align: middle; 
}

.table tr:last-child td { border-bottom: none; }
.table tr:hover td { background: #f9fafb; }

/* Badges & Buttons */
.badge { 
    padding: 5px 12px; 
    border-radius: 99px; 
    font-size: 13px; 
    font-weight: 600; 
    display: inline-block; 
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-info { background: #e0f2fe; color: #075985; }

/* Action Buttons */
.btn { 
    display: inline-flex; 
    align-items: center; 
    justify-content: center; 
    border-radius: 6px; 
    text-decoration: none; 
    border: 1px solid transparent; 
    cursor: pointer; 
    transition: all 0.2s; 
}

.btn-primary { 
    background: #0d6efd; 
    color: white; 
    padding: 8px 16px; 
    font-size: 14px; 
    font-weight: 500; 
}

.btn-primary:hover {
    background: #0b5ed7;
    transform: translateY(-1px);
}

.btn-outline { 
    background: white; 
    border: 1px solid #d1d5db; 
    color: #374151; 
    padding: 8px 16px; 
    font-size: 14px; 
    font-weight: 500; 
}

.btn-outline:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-icon { 
    width: 36px; 
    height: 36px; 
    padding: 0; 
}

.btn-light-primary { 
    background: #eff6ff; 
    color: #3b82f6; 
    border: none; 
}

.btn-light-primary:hover { 
    background: #dbeafe; 
}

.btn-light-warning { 
    background: #fffbeb; 
    color: #d97706; 
    border: none; 
}

.btn-light-warning:hover { 
    background: #fef3c7; 
}

.btn-light-danger { 
    background: #fef2f2; 
    color: #dc2626; 
    border: none; 
}

.btn-light-danger:hover { 
    background: #fee2e2; 
}

.text-muted { color: #6b7280; }
.empty-state { 
    text-align: center; 
    padding: 50px 20px; 
    color: #9ca3af; 
}

.empty-state i {
    font-size: 3.5rem;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 16px;
    margin: 0;
}

/* Modal Styles */
.modal {
    display: none;
    position: fixed;
    z-index: 1055;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal.show {
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-dialog {
    position: relative;
    width: auto;
    max-width: 500px;
    margin: 1.75rem auto;
    animation: slideDown 0.3s;
}

@keyframes slideDown {
    from { transform: translateY(-50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

.modal-content {
    position: relative;
    background-color: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    padding: 0;
}

.modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    position: relative;
}

.modal-header.bg-danger {
    background: #dc2626 !important;
    border-bottom-color: rgba(255, 255, 255, 0.2);
}

.modal-header .modal-title {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.modal-header.bg-danger .modal-title {
    color: white;
}

.modal-header .btn-close,
.modal-header .btn-close-white {
    background: transparent;
    border: none;
    font-size: 24px;
    line-height: 1;
    color: #6b7280;
    cursor: pointer;
    padding: 8px;
    width: 40px;
    height: 40px;
    transition: all 0.2s;
    margin: 0 !important;
    opacity: 1;
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 300;
}

.modal-header .btn-close-white {
    color: #ffffff !important;
    opacity: 1 !important;
    filter: brightness(1.2);
}

.modal-header .btn-close:hover,
.modal-header .btn-close-white:hover {
    opacity: 0.8 !important;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 4px;
}

.modal-body {
    padding: 24px;
    color: #374151;
    font-size: 14px;
    line-height: 1.6;
}

.modal-body strong {
    color: #111827;
}

.modal-body .text-muted {
    color: #6b7280;
    font-size: 13px;
}

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
}

.btn-secondary {
    background: #f3f4f6;
    color: #374151;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-secondary:hover {
    background: #e5e7eb;
}

.btn-danger {
    background: #dc2626;
    color: white;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
}

.btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

@media (max-width: 768px) {
    .col-md-3 {
        flex: 0 0 100%;
        max-width: 100%;
    }
    
    .stat-number {
        font-size: 36px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">

    {{-- 1. Quick Stats Section --}}
    <div class="row mb-4">
        {{-- Card 1: Guru --}}
        <div class="col-md-3">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Total Guru</div>
                    <div class="stat-number">{{ $stats['totalTenagaPendidik'] }}</div>
                    <div class="stat-desc">Tenaga Pendidik</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
        </div>

        {{-- Card 2: Siswa --}}
        <div class="col-md-3">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Siswa</div>
                    <div class="stat-number">{{ $stats['totalSiswa'] }}</div>
                    <div class="stat-desc">Siswa Terdaftar</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
        </div>

        {{-- Card 3: User Aktif --}}
        <div class="col-md-3">
            <div class="stat-card bg-gradient-purple">
                <div class="stat-content">
                    <div class="stat-title">User Aktif</div>
                    <div class="stat-number">{{ $stats['totalUserAktif'] }}</div>
                    <div class="stat-desc">Akun dapat login</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>

        {{-- Card 4: Non-Aktif --}}
        <div class="col-md-3">
            <div class="stat-card bg-gradient-red">
                <div class="stat-content">
                    <div class="stat-title">Non-Aktif</div>
                    <div class="stat-number">{{ $stats['totalUserNonAktif'] }}</div>
                    <div class="stat-desc">Perlu peninjauan</div>
                </div>
                <div class="stat-icon-bg">
                    <i class="fas fa-user-times"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- 2. Tenaga Pendidik Table --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h5><i class="fas fa-chalkboard-teacher" style="color: #10b981; margin-right: 8px;"></i> Tenaga Pendidik Terbaru</h5>
                        <small>5 data tenaga pendidik yang baru ditambahkan</small>
                    </div>
                    <div>
                         @if($tenagaPendidik->count() > 0)
                            <a href="{{ route('admin.users.tenaga-pendidik') }}" class="btn btn-outline">
                                Lihat Semua <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                            </a>
                        @else
                            <a href="{{ route('admin.users.create-tenaga-pendidik') }}" class="btn btn-primary">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Tambah Baru
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($tenagaPendidik->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th>NIP</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Status</th>
                                        <th style="width: 140px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tenagaPendidik as $tp)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; font-size: 15px;">{{ $tp->nama_lengkap }}</div>
                                            <small style="color: #6b7280; font-size: 12px;">Dibuat: {{ $tp->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td style="font-size: 14px;">{{ $tp->nip ?? '-' }}</td>
                                        <td style="font-size: 14px;">{{ $tp->email }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ ucwords(str_replace('_', ' ', $tp->user->role)) }}</span>
                                        </td>
                                        <td>
                                            @if($tp->user->is_active)
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-warning">Non-Aktif</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; gap: 5px; justify-content: center;">
                                                {{-- Tombol Detail --}}
                                                <a href="{{ route('admin.users.show-tenaga-pendidik', $tp->id) }}" class="btn btn-icon btn-light-primary" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.users.edit-tenaga-pendidik', $tp->id) }}" class="btn btn-icon btn-light-warning" title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- Tombol Delete --}}
                                                <button type="button" class="btn btn-icon btn-light-danger" title="Hapus Data" onclick="confirmDeleteTenagaPendidik({{ $tp->id }}, '{{ addslashes($tp->nama_lengkap) }}', '{{ addslashes($tp->email) }}', '{{ addslashes(ucwords(str_replace('_', ' ', $tp->user->role))) }}')">
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

        {{-- 3. Siswa Table --}}
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h5><i class="fas fa-user-graduate" style="color: #3b82f6; margin-right: 8px;"></i> Siswa Terbaru</h5>
                        <small>5 data siswa yang baru ditambahkan</small>
                    </div>
                    <div>
                        @if($siswa->count() > 0)
                            <a href="{{ route('admin.users.siswa') }}" class="btn btn-outline">
                                Lihat Semua <i class="fas fa-arrow-right" style="margin-left: 6px;"></i>
                            </a>
                        @else
                            <a href="{{ route('admin.users.create-siswa') }}" class="btn btn-primary">
                                <i class="fas fa-plus" style="margin-right: 6px;"></i> Tambah Siswa
                            </a>
                        @endif
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($siswa->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama Lengkap</th>
                                        <th>NIS</th>
                                        <th>NISN</th>
                                        <th>Kelas</th>
                                        <th>Status</th>
                                        <th style="width: 140px; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswa as $s)
                                    <tr>
                                        <td>
                                            <div style="font-weight: 600; font-size: 15px;">{{ $s->nama_lengkap }}</div>
                                            <small style="color: #6b7280; font-size: 12px;">{{ $s->email ?? 'No Email' }}</small>
                                        </td>
                                        <td><span style="font-weight: 500; font-size: 14px;">{{ $s->nis }}</span></td>
                                        <td><span style="color: #6b7280; font-size: 14px;">{{ $s->nisn }}</span></td>
                                        <td>
                                            @if($s->kelas)
                                                <span class="badge badge-info">{{ $s->kelas->nama_kelas }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($s->status === 'aktif')
                                                <span class="badge badge-success">Aktif</span>
                                            @else
                                                <span class="badge badge-warning">{{ ucfirst($s->status) }}</span>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <div style="display: flex; gap: 5px; justify-content: center;">
                                                {{-- Tombol Detail --}}
                                                <a href="{{ route('admin.users.show-siswa', $s->id) }}" class="btn btn-icon btn-light-primary" title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                {{-- Tombol Edit --}}
                                                <a href="{{ route('admin.users.edit-siswa', $s->id) }}" class="btn btn-icon btn-light-warning" title="Edit Data">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                {{-- Tombol Delete --}}
                                                <button type="button" class="btn btn-icon btn-light-danger" title="Hapus Data" onclick="confirmDeleteSiswa({{ $s->id }}, '{{ addslashes($s->nama_lengkap) }}', '{{ $s->nis }}', '{{ $s->nisn }}', '{{ $s->kelas ? addslashes($s->kelas->nama_kelas) : '' }}')">
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
    </div>
</div>

{{-- Delete Modal for Tenaga Pendidik (Single Reusable) --}}
<div class="modal fade" id="deleteTenagaPendidikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data tenaga pendidik:</p>
                <div style="background: #f9fafb; padding: 12px; border-radius: 8px; margin: 12px 0; border: 1px solid #e5e7eb;">
                    <div style="font-weight: 600; color: #111827; margin-bottom: 4px;">
                        <i class="fas fa-chalkboard-teacher" style="color: #3b82f6;"></i>
                        <span id="deleteTenagaPendidikName"></span>
                    </div>
                    <small style="color: #64748b;"><span id="deleteTenagaPendidikRole"></span> • <span id="deleteTenagaPendidikEmail"></span></small>
                </div>
                <p style="margin-top: 12px;">
                    <i class="fas fa-info-circle" style="color: #dc2626;"></i>
                    <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait termasuk akun login.</small>
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <form id="deleteTenagaPendidikForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4 shadow">
                        <i class="fas fa-trash"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal for Siswa (Single Reusable) --}}
<div class="modal fade" id="deleteSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data siswa:</p>
                <div style="background: #f9fafb; padding: 12px; border-radius: 8px; margin: 12px 0; border: 1px solid #e5e7eb;">
                    <div style="font-weight: 600; color: #111827; margin-bottom: 8px;">
                        <i class="fas fa-user-graduate" style="color: #3b82f6;"></i>
                        <span id="deleteSiswaName"></span>
                    </div>
                    <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                        <small style="color: #64748b;">
                            <i class="fas fa-id-card" style="font-size: 11px;"></i> NIS: <span id="deleteSiswaNis"></span>
                        </small>
                        <small style="color: #64748b;">
                            <i class="fas fa-hashtag" style="font-size: 11px;"></i> NISN: <span id="deleteSiswaNisn"></span>
                        </small>
                        <small style="color: #64748b;" id="deleteSiswaKelasContainer">
                            <i class="fas fa-door-open" style="font-size: 11px;"></i> <span id="deleteSiswaKelas"></span>
                        </small>
                    </div>
                </div>
                <p style="margin-top: 12px;">
                    <i class="fas fa-info-circle" style="color: #dc2626;"></i>
                    <small class="text-muted">Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait termasuk akun login siswa.</small>
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <form id="deleteSiswaForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4 shadow">
                        <i class="fas fa-trash"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDeleteTenagaPendidik(id, name, email, role) {
    // Set the tenaga pendidik info in the modal
    document.getElementById('deleteTenagaPendidikName').textContent = name;
    document.getElementById('deleteTenagaPendidikEmail').textContent = email;
    document.getElementById('deleteTenagaPendidikRole').textContent = role;

    // Set the form action URL
    const form = document.getElementById('deleteTenagaPendidikForm');
    form.action = "{{ route('admin.users.tenaga-pendidik') }}/" + id;

    // Show the modal using Bootstrap 5 API
    const modal = new bootstrap.Modal(document.getElementById('deleteTenagaPendidikModal'));
    modal.show();
}

function confirmDeleteSiswa(id, name, nis, nisn, kelas) {
    // Set the siswa info in the modal
    document.getElementById('deleteSiswaName').textContent = name;
    document.getElementById('deleteSiswaNis').textContent = nis;
    document.getElementById('deleteSiswaNisn').textContent = nisn;

    // Handle kelas (show/hide container if kelas is empty)
    const kelasContainer = document.getElementById('deleteSiswaKelasContainer');
    if (kelas) {
        document.getElementById('deleteSiswaKelas').textContent = kelas;
        kelasContainer.style.display = '';
    } else {
        kelasContainer.style.display = 'none';
    }

    // Set the form action URL
    const form = document.getElementById('deleteSiswaForm');
    form.action = "{{ route('admin.users.siswa') }}/" + id;

    // Show the modal using Bootstrap 5 API
    const modal = new bootstrap.Modal(document.getElementById('deleteSiswaModal'));
    modal.show();
}
</script>
@endsection