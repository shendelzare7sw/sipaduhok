@extends('layouts.sneat')

@section('title', 'Detail Tahun Ajaran')

@section('page-title', 'Detail Tahun Ajaran')
@section('page-subtitle', 'Informasi lengkap tahun ajaran')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
/* Styles copied from Index/Create for consistency */
.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}
.card-header {
    padding: 16px 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    border-radius: 12px 12px 0 0;
}
.card-header h5 { margin: 0; font-size: 16px; font-weight: 600; }
.card-body { padding: 20px; }
.card-footer {
    padding: 16px 20px;
    border-top: 1px solid #e5e7eb;
    background: white;
    border-radius: 0 0 12px 12px;
}

/* Grid & Layout */
.row { display: flex; flex-wrap: wrap; margin: -12px; }
.col-md-8 { flex: 0 0 66.666667%; max-width: 66.666667%; padding: 12px; }
.col-md-4 { flex: 0 0 33.333333%; max-width: 33.333333%; padding: 12px; }

/* Utilities */
.d-flex { display: flex !important; }
.justify-content-between { justify-content: space-between !important; }
.align-items-center { align-items: center !important; }
.gap-2 { gap: 8px !important; }
.mb-0 { margin-bottom: 0; }
.mb-3 { margin-bottom: 16px; }
.text-muted { color: #6b7280; }

/* Buttons & Badges */
.btn {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 8px 16px; font-size: 14px; font-weight: 500;
    text-decoration: none; cursor: pointer; border-radius: 6px;
    border: 1px solid transparent; transition: all .15s;
}
.btn-primary { background-color: #0d6efd; color: #fff; border-color: #0d6efd; }
.btn-warning { background-color: #f59e0b; color: #fff; border-color: #f59e0b; }
.btn-danger { background-color: #dc3545; color: #fff; border-color: #dc3545; }
.btn-success { background-color: #10b981; color: #fff; border-color: #10b981; }
.btn-secondary { background: #6b7280; color: white; border-color: #6b7280; }

.badge {
    padding: 5px 10px; border-radius: 99px; font-size: 12px; font-weight: 600;
}
.bg-success { background: #10b981 !important; color: white; }
.bg-secondary { background: #6b7280 !important; color: white; }

/* Table Styling for Details */
.table-detail { width: 100%; border-collapse: collapse; }
.table-detail th { text-align: left; padding: 12px 0; color: #6b7280; width: 35%; border-bottom: 1px solid #f3f4f6; }
.table-detail td { padding: 12px 0; font-weight: 500; color: #111827; border-bottom: 1px solid #f3f4f6; }
.table-detail tr:last-child th, .table-detail tr:last-child td { border-bottom: none; }

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

.modal-header .btn-close {
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

.modal-header .btn-close:hover {
    opacity: 0.8 !important;
    background: rgba(0, 0, 0, 0.1);
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

.modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    padding: 16px 24px;
    border-top: 1px solid #e5e7eb;
}

.modal-footer .btn-secondary {
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

.modal-footer .btn-secondary:hover {
    background: #e5e7eb;
}

.modal-footer .btn-success,
.modal-footer .btn-danger {
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

.modal-footer .btn-success {
    background: #16a34a;
}

.modal-footer .btn-success:hover {
    background: #15803d;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
}

.modal-footer .btn-danger {
    background: #dc2626;
}

.modal-footer .btn-danger:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    
    {{-- Header Action --}}
    <div class="d-flex justify-content-between align-items-center mb-3" style="padding: 0 12px;">
        <h5 class="mb-0" style="font-size: 20px; font-weight: 700;">
            Detail: {{ $tahunAjaran->nama_tahun_ajaran }}
        </h5>
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left" style="margin-right: 6px;"></i> Kembali
        </a>
    </div>

    <div class="row">
        {{-- Kolom Kiri: Informasi Utama & Kelas --}}
        <div class="col-md-8">
            {{-- Card 1: Informasi Detail --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle text-muted" style="margin-right: 8px;"></i> Informasi Umum</h5>
                </div>
                <div class="card-body">
                    <table class="table-detail">
                        <tr>
                            <th>Tahun Ajaran</th>
                            <td style="font-size: 16px;"><strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($tahunAjaran->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Tanggal Mulai</th>
                            <td>{{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Selesai</th>
                            <td>{{ \Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td>
                                {{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)) }} hari
                                <span class="text-muted" style="font-weight: normal;">
                                    ({{ \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai)->diffInMonths(\Carbon\Carbon::parse($tahunAjaran->tanggal_selesai)) }} bulan)
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Terakhir Diupdate</th>
                            <td>{{ $tahunAjaran->updated_at->format('d M Y, H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Card: Periode Semester --}}
            <div class="card">
                <div class="card-header" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0;">
                    <h5 class="mb-0" style="color: #166534;"><i class="fas fa-calendar-alt text-success" style="margin-right: 8px;"></i> Periode Semester</h5>
                </div>
                <div class="card-body">
                    @php
                        $periods = $tahunAjaran->getSemesterPeriods();
                        $currentSemester = \App\Models\TahunAjaran::getCurrentSemester();
                    @endphp
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        {{-- Semester Ganjil --}}
                        <div style="background: #fef3c7; border-radius: 8px; padding: 16px; border-left: 4px solid #f59e0b;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="fas fa-sun" style="color: #d97706;"></i>
                                <strong style="color: #92400e;">Semester Ganjil</strong>
                                @if($tahunAjaran->is_active && $currentSemester == 'ganjil')
                                    <span class="badge bg-success" style="font-size: 10px;">Aktif</span>
                                @endif
                            </div>
                            <div style="font-size: 13px; color: #78350f;">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $periods['ganjil']['start']->format('d M Y') }} - {{ $periods['ganjil']['end']->format('d M Y') }}
                            </div>
                        </div>
                        
                        {{-- Semester Genap --}}
                        <div style="background: #dbeafe; border-radius: 8px; padding: 16px; border-left: 4px solid #3b82f6;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <i class="fas fa-snowflake" style="color: #2563eb;"></i>
                                <strong style="color: #1e40af;">Semester Genap</strong>
                                @if($tahunAjaran->is_active && $currentSemester == 'genap')
                                    <span class="badge bg-success" style="font-size: 10px;">Aktif</span>
                                @endif
                            </div>
                            <div style="font-size: 13px; color: #1e3a8a;">
                                <i class="fas fa-calendar me-1"></i>
                                {{ $periods['genap']['start']->format('d M Y') }} - {{ $periods['genap']['end']->format('d M Y') }}
                            </div>
                        </div>
                    </div>
                    
                    @if(!$tahunAjaran->tanggal_mulai_genap)
                        <div style="margin-top: 12px; font-size: 12px; color: #6b7280; background: #f3f4f6; padding: 8px 12px; border-radius: 6px;">
                            <i class="fas fa-info-circle me-1"></i> Periode semester menggunakan perhitungan otomatis (Juli-Des = Ganjil, Jan-Jun = Genap).
                            <a href="{{ route('admin.tahun-ajaran.edit', $tahunAjaran->id) }}" style="color: #16a34a;">Atur periode kustom →</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Card 2: Data Kelas --}}
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chalkboard text-muted" style="margin-right: 8px;"></i> Data Kelas Terkait</h5>
                    <span class="badge bg-secondary" style="font-size: 11px;">{{ $tahunAjaran->kelas->count() }} Kelas</span>
                </div>
                <div class="card-body p-0">
                    @if($tahunAjaran->kelas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="width: 100%; border-collapse: collapse;">
                                <thead style="background: #f9fafb;">
                                    <tr>
                                        <th style="padding: 12px 20px; text-align: left; border-bottom: 1px solid #e5e7eb;">Nama Kelas</th>
                                        <th style="padding: 12px 20px; text-align: left; border-bottom: 1px solid #e5e7eb;">Tingkat</th>
                                        <th style="padding: 12px 20px; text-align: left; border-bottom: 1px solid #e5e7eb;">Program</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tahunAjaran->kelas as $kelas)
                                    <tr>
                                        <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6;">
                                            <strong>{{ $kelas->nama_kelas }}</strong>
                                        </td>
                                        <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6;">{{ $kelas->tingkat }}</td>
                                        <td style="padding: 12px 20px; border-bottom: 1px solid #f3f4f6;">
                                            <span class="badge bg-success">{{ $kelas->program }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 40px; color: #9ca3af;">
                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                            <p class="mb-0">Belum ada kelas yang terkait.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Aksi & Danger Zone --}}
        <div class="col-md-4">
            
            {{-- Aksi --}}
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Aksi</h5>
                </div>
                <div class="card-body d-grid" style="display: grid; gap: 10px;">
                    <a href="{{ route('admin.tahun-ajaran.edit', $tahunAjaran->id) }}" class="btn btn-warning" style="width: 100%;">
                        <i class="fas fa-edit" style="margin-right: 6px;"></i> Edit Data
                    </a>
                    
                    @if(!$tahunAjaran->is_active)
                        <button type="button" class="btn btn-success" style="width: 100%;" data-bs-toggle="modal" data-bs-target="#activateModal">
                            <i class="fas fa-check" style="margin-right: 6px;"></i> Aktifkan
                        </button>
                    @endif
                </div>
            </div>

            {{-- Danger Zone --}}
            <div class="card" style="border: 1px solid #fca5a5;">
                <div class="card-header" style="background: #fef2f2; border-bottom: 1px solid #fca5a5;">
                    <h5 class="mb-0" style="color: #dc2626;"><i class="fas fa-exclamation-triangle"></i> Peringatan</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted" style="font-size: 13px; margin-bottom: 16px;">
                        Menghapus tahun ajaran bersifat permanen. Data yang dihapus tidak dapat dikembalikan.
                    </p>
                    <button type="button" class="btn btn-danger" style="width: 100%;" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash" style="margin-right: 6px;"></i> Hapus Tahun Ajaran
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS (Menggunakan Bootstrap Standard seperti Index) --}}

@if(!$tahunAjaran->is_active)
<div class="modal fade" id="activateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; border: none;">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Aktifkan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: none; border: none; font-size: 20px;">×</button>
            </div>
            <div class="modal-body">
                Aktifkan tahun ajaran <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>?
                <div style="margin-top: 10px; background: #fffbeb; border-left: 4px solid #f59e0b; padding: 10px; font-size: 13px; color: #92400e;">
                    <i class="fas fa-exclamation-triangle"></i> Tahun aktif saat ini akan otomatis dinonaktifkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.tahun-ajaran.activate', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">Ya, Aktifkan</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="background: white; border-radius: 12px; border: none;">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" style="background: none; border: none; font-size: 20px;">×</button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus tahun ajaran <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>?
                <div style="margin-top: 10px; background: #fef2f2; border-left: 4px solid #ef4444; padding: 10px; font-size: 13px; color: #991b1b;">
                    <i class="fas fa-exclamation-circle"></i> Tindakan ini tidak dapat dibatalkan.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.tahun-ajaran.destroy', $tahunAjaran->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection