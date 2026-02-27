@extends('layouts.sneat')

@section('title', 'Kelola Siswa - ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Siswa Kelas')
@section('page-subtitle', $kelas->nama_kelas . ' - ' . $kelas->tahunAjaran->nama_tahun_ajaran)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    font-size: 14px;
}

.breadcrumb a {
    color: #6b7280;
    text-decoration: none;
}

.breadcrumb a:hover {
    color: #3b82f6;
}

.breadcrumb span {
    color: #9ca3af;
}

.breadcrumb .current {
    color: #111827;
    font-weight: 500;
}

.kelas-info-banner {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    border-radius: 16px;
    padding: 24px;
    color: white;
    margin-bottom: 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
}

.kelas-info-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.kelas-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
}

.kelas-title h2 {
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 4px;
}

.kelas-title p {
    font-size: 14px;
    opacity: 0.9;
    margin: 0;
}

.kelas-info-right {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.kelas-stat {
    text-align: center;
    padding: 12px 20px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    flex: 1;
    min-width: 80px;
}

.kelas-stat-value {
    font-size: 28px;
    font-weight: 700;
}

.kelas-stat-label {
    font-size: 12px;
    opacity: 0.9;
}

.card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 24px;
    border: none;
    overflow: hidden;
}

.card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
}

.card-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    display: flex;
    align-items: center;
    gap: 10px;
}

.card-header h5 i {
    color: #10b981;
}

.card-header.danger h5 i {
    color: #ef4444;
}

.card-body {
    padding: 24px;
}

.grid-2 {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 24px;
}

@media (max-width: 1200px) {
    .grid-2 {
        grid-template-columns: 1fr;
    }
}

.table-responsive {
    overflow-x: auto;
    max-height: 500px;
    overflow-y: auto;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 12px 16px;
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table td {
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    font-size: 14px;
    vertical-align: middle;
}

.table tr:hover td {
    background: #f9fafb;
}

.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-info { background: #e0f2fe; color: #075985; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }

.user-info {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    min-width: 32px;
    min-height: 32px;
    flex-shrink: 0;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 12px;
}

.user-avatar.blue {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.user-name {
    font-weight: 500;
    color: #111827;
}

.user-nisn {
    font-size: 11px;
    color: #6b7280;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-success:hover {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn-danger:hover {
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover {
    background: #f9fafb;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    accent-color: #10b981;
    cursor: pointer;
}

.select-all-row {
    background: #f0fdf4 !important;
}

.empty-state {
    text-align: center;
    padding: 40px 20px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 12px;
    opacity: 0.5;
}

.empty-state p {
    font-size: 14px;
    margin: 0;
}

.search-box {
    position: relative;
    margin-bottom: 16px;
}

.search-box input {
    width: 100%;
    padding: 10px 16px 10px 42px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
}

.search-box input:focus {
    outline: none;
    border-color: #10b981;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.action-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    flex-wrap: wrap;
    gap: 12px;
}

.selected-count {
    font-size: 14px;
    color: #6b7280;
}

.selected-count strong {
    color: #10b981;
}

.alert {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
}

.alert-warning {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fcd34d;
}

@media (max-width: 767.98px) {
    .kelas-info-banner {
        flex-direction: column;
        align-items: flex-start;
    }
    .kelas-info-right {
        width: 100%;
    }
    .kelas-stat-value {
        font-size: 22px;
    }

    /* Table → Card layout */
    .table-card-mobile thead {
        display: none;
    }
    .table-card-mobile tbody tr {
        display: block;
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        margin-bottom: 10px;
        padding: 10px 14px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
        position: relative;
    }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 5px 0;
        border: none;
        font-size: 13px;
    }
    .table-card-mobile tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6b7280;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        flex-shrink: 0;
        margin-right: 8px;
    }
    .table-card-mobile tbody td.mobile-card-head {
        font-size: 13px;
        font-weight: 600;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 8px;
        padding-right: 32px;
        margin-bottom: 2px;
        justify-content: flex-start;
        gap: 10px;
    }
    .table-card-mobile tbody td.mobile-card-head::before {
        display: none;
    }
    .table-card-mobile tbody td.mobile-card-hide {
        display: none;
    }
    .table-card-mobile tbody td.mobile-card-actions {
        justify-content: flex-end;
        padding-top: 8px;
    }
    .table-card-mobile tbody td.mobile-card-actions::before {
        display: none;
    }
    .table-card-mobile tbody td.mobile-card-checkbox {
        position: absolute;
        top: 10px;
        right: 12px;
        display: flex !important;
        width: 28px;
        padding: 0;
        border: none;
        z-index: 2;
    }
    .table-card-mobile tbody td.mobile-card-checkbox::before {
        display: none;
    }
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    <div class="breadcrumb">
        <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
        <span>/</span>
        <a href="{{ route('admin.kelas.index') }}">Data Kelas</a>
        <span>/</span>
        <a href="{{ route('admin.kelas.show', $kelas) }}">{{ $kelas->nama_kelas }}</a>
        <span>/</span>
        <span class="current">Kelola Siswa</span>
    </div>

    <div class="kelas-info-banner">
        <div class="kelas-info-left">
            <div class="kelas-icon">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="kelas-title">
                <h2>{{ $kelas->nama_kelas }}</h2>
                <p><i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang }} &bull; <i class="fas fa-calendar"></i> {{ $kelas->tahunAjaran->nama_tahun_ajaran }}</p>
            </div>
        </div>
        <div class="kelas-info-right">
            <div class="kelas-stat">
                <div class="kelas-stat-value">{{ $siswaInKelas->count() }}</div>
                <div class="kelas-stat-label">Siswa Saat Ini</div>
            </div>
            <div class="kelas-stat">
                <div class="kelas-stat-value">{{ $kelas->kuota_siswa }}</div>
                <div class="kelas-stat-label">Kuota</div>
            </div>
            <div class="kelas-stat">
                <div class="kelas-stat-value">{{ $sisaKuota }}</div>
                <div class="kelas-stat-label">Sisa Kuota</div>
            </div>
        </div>
    </div>

    @if($sisaKuota <= 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> 
            <strong>Kuota Penuh!</strong> Kelas ini sudah mencapai batas kuota maksimal. Tidak dapat menambahkan siswa baru.
        </div>
    @endif

    <div class="grid-2">
        {{-- Siswa di Kelas Ini --}}
        <div class="card">
            <div class="card-header danger">
                <h5><i class="fas fa-users"></i> Siswa di Kelas Ini ({{ $siswaInKelas->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInKelas" placeholder="Cari siswa di kelas ini...">
                </div>
                
                @if($siswaInKelas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-card-mobile" id="tableInKelas">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>Siswa</th>
                                    <th>JK</th>
                                    <th style="width: 80px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaInKelas as $index => $s)
                                <tr data-nama="{{ strtolower($s->nama_lengkap) }}">
                                    <td class="mobile-card-hide" data-label="No">{{ $index + 1 }}</td>
                                    <td class="mobile-card-head">
                                        <div class="user-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                        <div>
                                            <div class="user-name">{{ $s->nama_lengkap }}</div>
                                            <div class="user-nisn">{{ $s->nis }}</div>
                                        </div>
                                    </td>
                                    <td data-label="JK">
                                        <span class="badge {{ $s->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                            {{ $s->jenis_kelamin }}
                                        </span>
                                    </td>
                                    <td class="mobile-card-actions">
                                        <form action="{{ route('admin.kelas.remove-siswa', $kelas) }}" method="POST" id="deleteForm{{ $s->id }}">
                                            @csrf
                                            <input type="hidden" name="siswa_id" value="{{ $s->id }}">
                                            <button type="button" class="btn btn-danger btn-sm" title="Keluarkan dari kelas" onclick="confirmRemoveSiswa('{{ $s->id }}', '{{ $s->nama_lengkap }}')">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-users"></i>
                        <p>Belum ada siswa di kelas ini</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Siswa Tersedia untuk Ditambahkan --}}
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-user-plus"></i> Siswa Tersedia ({{ $siswaAvailable->count() }})</h5>
            </div>
            <div class="card-body">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchAvailable" placeholder="Cari siswa tersedia...">
                </div>

                @if($siswaAvailable->count() > 0 && $sisaKuota > 0)
                    <form action="{{ route('admin.kelas.add-siswa', $kelas) }}" method="POST" id="formAddSiswa">
                        @csrf
                        <div class="action-bar">
                            <div class="selected-count">
                                <span id="selectedCount">0</span> siswa dipilih
                            </div>
                            <button type="submit" class="btn btn-success" id="btnAddSiswa" disabled>
                                <i class="fas fa-plus"></i> Tambahkan ke Kelas
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-card-mobile" id="tableAvailable">
                                <thead>
                                    <tr class="select-all-row">
                                        <th style="width: 40px;">
                                            <input type="checkbox" class="checkbox-custom" id="selectAll">
                                        </th>
                                        <th>Siswa</th>
                                        <th>JK</th>
                                        <th>Kelas Saat Ini</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaAvailable as $s)
                                    <tr data-nama="{{ strtolower($s->nama_lengkap) }}">
                                        <td class="mobile-card-checkbox">
                                            <input type="checkbox" class="checkbox-custom siswa-checkbox"
                                                   name="siswa_ids[]" value="{{ $s->id }}">
                                        </td>
                                        <td class="mobile-card-head">
                                            <div class="user-avatar blue">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                            <div>
                                                <div class="user-name">{{ $s->nama_lengkap }}</div>
                                                <div class="user-nisn">{{ $s->nis }}</div>
                                            </div>
                                        </td>
                                        <td data-label="JK">
                                            <span class="badge {{ $s->jenis_kelamin == 'L' ? 'badge-info' : 'badge-purple' }}">
                                                {{ $s->jenis_kelamin }}
                                            </span>
                                        </td>
                                        <td data-label="Kelas">
                                            @if($s->kelas_id)
                                                <span class="badge badge-warning">{{ $s->kelas->nama_kelas ?? 'Ada kelas' }}</span>
                                            @else
                                                <span style="color: #9ca3af;">Belum ada kelas</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                @elseif($sisaKuota <= 0)
                    <div class="empty-state">
                        <i class="fas fa-ban"></i>
                        <p>Kuota kelas sudah penuh</p>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fas fa-check-circle"></i>
                        <p>Semua siswa di cabang ini sudah terdaftar di kelas</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div style="text-align: center; margin-top: 24px;">
        <a href="{{ route('admin.kelas.show', $kelas) }}" class="btn btn-outline">
            <i class="fas fa-arrow-left"></i> Kembali ke Detail Kelas
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sisaKuota = {{ $sisaKuota }};
    const checkboxes = document.querySelectorAll('.siswa-checkbox');
    const selectAll = document.getElementById('selectAll');
    const selectedCountEl = document.getElementById('selectedCount');
    const btnAddSiswa = document.getElementById('btnAddSiswa');
    
    function updateSelectedCount() {
        const checked = document.querySelectorAll('.siswa-checkbox:checked').length;
        if (selectedCountEl) selectedCountEl.textContent = checked;
        if (btnAddSiswa) {
            btnAddSiswa.disabled = checked === 0 || checked > sisaKuota;
            if (checked > sisaKuota) {
                btnAddSiswa.textContent = 'Melebihi Kuota!';
            } else {
                btnAddSiswa.innerHTML = '<i class="fas fa-plus"></i> Tambahkan ke Kelas';
            }
        }
    }
    
    checkboxes.forEach(function(cb) {
        cb.addEventListener('change', function() {
            updateSelectedCount();
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.siswa-checkbox:checked').length === checkboxes.length;
            }
        });
    });
    
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            const maxSelect = Math.min(checkboxes.length, sisaKuota);
            checkboxes.forEach(function(cb, index) {
                cb.checked = selectAll.checked && index < maxSelect;
            });
            updateSelectedCount();
        });
    }
    
    // Search functionality
    const searchInKelas = document.getElementById('searchInKelas');
    const searchAvailable = document.getElementById('searchAvailable');
    
    function filterTable(input, tableId) {
        const filter = input.value.toLowerCase();
        const table = document.getElementById(tableId);
        if (!table) return;
        
        const rows = table.querySelectorAll('tbody tr');
        rows.forEach(function(row) {
            const nama = row.getAttribute('data-nama') || '';
            row.style.display = nama.includes(filter) ? '' : 'none';
        });
    }
    
    if (searchInKelas) {
        searchInKelas.addEventListener('input', function() {
            filterTable(this, 'tableInKelas');
        });
    }
    
    if (searchAvailable) {
        searchAvailable.addEventListener('input', function() {
            filterTable(this, 'tableAvailable');
        });
    }
});
</script>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden;">
            <button type="button" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 16px; background: rgba(0,0,0,0.05); border: none; color: #6b7280; transition: all 0.2s; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; z-index: 10;" onmouseover="this.style.background='rgba(0,0,0,0.1)'; this.style.color='#1f2937'" onmouseout="this.style.background='rgba(0,0,0,0.05)'; this.style.color='#6b7280'">
                <i class="fas fa-times" style="font-size: 16px;"></i>
            </button>
            <div class="modal-body" style="padding: 40px 30px 30px; text-align: center;">
                <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                    <i class="fas fa-exclamation-triangle" style="font-size: 36px; color: #ef4444;"></i>
                </div>
                
                <h4 style="font-weight: 700; color: #111827; margin-bottom: 12px;">Keluarkan Siswa?</h4>
                
                <p style="color: #4b5563; font-size: 15px; margin-bottom: 24px; line-height: 1.6;">
                    Apakah Anda yakin ingin mengeluarkan <br>
                    <strong style="color: #111827; font-size: 16px;" id="siswaName"></strong><br>
                    dari <span style="font-weight: 500;">Kelas {{ $kelas->nama_kelas }}</span>?
                </p>
                
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px; margin-bottom: 28px; text-align: left; display: flex; align-items: flex-start; gap: 12px;">
                    <i class="fas fa-info-circle" style="color: #64748b; font-size: 20px; margin-top: 2px;"></i>
                    <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.5;">
                        Siswa akan dikeluarkan dari kelas ini, namun data siswa tetap tersimpan dan dapat ditambahkan kembali kapan saja.
                    </p>
                </div>
                
                <div style="display: flex; gap: 16px;">
                    <button type="button" class="btn" data-bs-dismiss="modal" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; background: #f1f5f9; color: #475569; border: none; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">
                        Batal
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
                        <i class="fas fa-user-minus me-2"></i> Ya, Keluarkan
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let deleteFormId = null;

function confirmRemoveSiswa(siswaId, siswaName) {
    deleteFormId = siswaId;
    document.getElementById('siswaName').textContent = siswaName;

    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (deleteFormId) {
        document.getElementById('deleteForm' + deleteFormId).submit();
    }
});
</script>
@endsection
