@extends('layouts.sneat')

@section('title', 'Manajemen Siswa')

@section('page-title', 'Manajemen Siswa')
@section('page-subtitle', 'Kelola data dan penempatan kelas siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<style>
.stat-card {
    padding: 24px;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.2s;
    height: 100%;
    color: white;
}

.stat-card:hover { transform: translateY(-5px); }

.stat-content { position: relative; z-index: 2; }

.stat-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    opacity: 0.9;
    margin-bottom: 8px;
}

.stat-number {
    font-size: 38px;
    font-weight: 700;
    margin-bottom: 4px;
    line-height: 1.2;
}

.stat-desc { font-size: 13px; opacity: 0.8; }

.stat-icon-bg {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 70px;
    opacity: 0.15;
    z-index: 1;
}

.bg-gradient-blue { background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); }
.bg-gradient-green { background: linear-gradient(135deg, #10b981 0%, #059669 100%); }
.bg-gradient-orange { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); }
.bg-gradient-pink { background: linear-gradient(135deg, #ec4899 0%, #db2777 100%); }
.bg-gradient-cyan { background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%); }

.row { display: flex; flex-wrap: wrap; margin: -10px; }
.col-md-2-4 { flex: 0 0 20%; max-width: 20%; padding: 10px; }

@media (max-width: 1200px) { .col-md-2-4 { flex: 0 0 33.33%; max-width: 33.33%; } }
@media (max-width: 768px) { .col-md-2-4 { flex: 0 0 50%; max-width: 50%; } }
@media (max-width: 480px) { .col-md-2-4 { flex: 0 0 100%; max-width: 100%; } }

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
    flex-wrap: wrap;
    gap: 16px;
}

.card-header h5 {
    margin: 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.card-body { padding: 24px; }

.filter-section {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 24px;
}

.search-box { position: relative; }

.search-box input {
    padding: 10px 16px 10px 42px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    width: 220px;
    max-width: 100%;
    transition: all 0.3s;
}

.search-box input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.search-box i {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
}

.filter-select {
    padding: 10px 16px;
    border: 1px solid #d1d5db;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    min-width: 140px;
    cursor: pointer;
}

.filter-select:focus { outline: none; border-color: #3b82f6; }

.table-responsive { overflow-x: auto; }

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    text-align: left;
    padding: 14px 16px;
    background: #f9fafb;
    color: #4b5563;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid #e5e7eb;
}

.table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f3f4f6;
    color: #374151;
    font-size: 14px;
    vertical-align: middle;
}

.table tr:hover td { background: #f9fafb; }

.badge {
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 600;
}

.badge-success { background: #dcfce7; color: #166534; }
.badge-warning { background: #fef3c7; color: #92400e; }
.badge-info { background: #e0f2fe; color: #075985; }
.badge-purple { background: #f3e8ff; color: #7c3aed; }
.badge-secondary { background: #f3f4f6; color: #6b7280; }

.badge-l { background: #dbeafe; color: #1e40af; }
.badge-p { background: #fce7f3; color: #9d174d; }

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
}

.btn-outline {
    background: white;
    border: 1px solid #d1d5db;
    color: #374151;
}

.btn-outline:hover { background: #f9fafb; }

.btn-sm { padding: 8px 14px; font-size: 13px; }

.btn-icon {
    width: 34px;
    height: 34px;
    padding: 0;
    border-radius: 8px;
}

.btn-light-primary { background: #eff6ff; color: #3b82f6; border: none; }
.btn-light-primary:hover { background: #dbeafe; }

.btn-light-success { background: #dcfce7; color: #16a34a; border: none; }
.btn-light-success:hover { background: #bbf7d0; }

.btn-print {
    background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    color: white;
}

.siswa-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.siswa-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 14px;
}

.siswa-avatar.female {
    background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
}

.siswa-details { display: flex; flex-direction: column; }

.siswa-name { font-weight: 600; color: #111827; }

.siswa-nisn {
    font-size: 11px;
    color: #6b7280;
    font-family: 'Monaco', 'Consolas', monospace;
}

.kelas-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
    font-size: 13px;
    color: #166534;
}

.no-kelas {
    color: #f59e0b;
    font-style: italic;
    font-size: 13px;
}

.action-buttons {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #9ca3af;
}

.empty-state i { font-size: 64px; margin-bottom: 16px; opacity: 0.5; }
.empty-state h3 { font-size: 18px; color: #6b7280; margin-bottom: 8px; }

.pagination-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 0;
    flex-wrap: wrap;
    gap: 16px;
}

.pagination-info { font-size: 14px; color: #6b7280; }

.quick-assign {
    display: flex;
    align-items: center;
    gap: 6px;
}

.quick-assign select {
    padding: 6px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 12px;
    max-width: 150px;
}

/* ── Mobile Responsive ── */
@media (max-width: 767.98px) {
    .search-box {
        width: 100% !important;
    }
    .search-box input {
        width: 100% !important;
    }
    .filter-section {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    .filter-section .filter-select,
    .filter-section .btn,
    .filter-section > label,
    .filter-section .dropdown {
        width: 100% !important;
        min-width: unset !important;
    }
    .filter-section .dropdown button {
        width: 100% !important;
    }
    /* Table → Card per row */
    .table-card-mobile thead { display: none; }
    .table-card-mobile tbody tr {
        display: block;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 12px;
        overflow: hidden;
        box-shadow: 0 1px 4px rgba(0,0,0,0.08);
        background: #fff;
    }
    .table-card-mobile tbody tr:hover td { background: transparent !important; }
    .table-card-mobile tbody td {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border: none !important;
        border-bottom: 1px solid #f3f4f6 !important;
        min-height: 44px;
        font-size: 13px;
    }
    .table-card-mobile tbody td.mobile-card-head {
        background: #f8fafc;
        padding: 14px;
        border-bottom: 2px solid #e5e7eb !important;
        justify-content: flex-start;
    }
    .table-card-mobile tbody td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        font-size: 10px;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.5px;
        flex-shrink: 0;
        padding-right: 10px;
        min-width: 60px;
    }
    .table-card-mobile tbody td.mobile-card-actions {
        border-bottom: none !important;
        justify-content: flex-end;
    }
    .stat-number { font-size: 26px; }
    .col-md-2-4 { flex: 0 0 50% !important; max-width: 50% !important; }
}
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
    {{-- Stats Section --}}
    <div class="row" style="margin-bottom: 24px;">
        <div class="col-md-2-4">
            <div class="stat-card bg-gradient-blue">
                <div class="stat-content">
                    <div class="stat-title">Total Siswa</div>
                    <div class="stat-number">{{ $stats['totalSiswa'] }}</div>
                    <div class="stat-desc">Siswa aktif</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-graduation-cap"></i></div>
            </div>
        </div>
        
        <div class="col-md-2-4">
            <div class="stat-card bg-gradient-green">
                <div class="stat-content">
                    <div class="stat-title">Sudah Ada Kelas</div>
                    <div class="stat-number">{{ $stats['siswaWithKelas'] }}</div>
                    <div class="stat-desc">Terdaftar di kelas</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-check-circle"></i></div>
            </div>
        </div>
        
        <div class="col-md-2-4">
            <div class="stat-card bg-gradient-orange">
                <div class="stat-content">
                    <div class="stat-title">Belum Ada Kelas</div>
                    <div class="stat-number">{{ $stats['siswaNoKelas'] }}</div>
                    <div class="stat-desc">Perlu ditempatkan</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-hourglass-half"></i></div>
            </div>
        </div>
        
        <div class="col-md-2-4">
            <div class="stat-card bg-gradient-cyan">
                <div class="stat-content">
                    <div class="stat-title">Laki-laki</div>
                    <div class="stat-number">{{ $stats['siswaLaki'] }}</div>
                    <div class="stat-desc">Siswa</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-male"></i></div>
            </div>
        </div>

        <div class="col-md-2-4">
            <div class="stat-card bg-gradient-pink">
                <div class="stat-content">
                    <div class="stat-title">Perempuan</div>
                    <div class="stat-number">{{ $stats['siswaPerempuan'] }}</div>
                    <div class="stat-desc">Siswi</div>
                </div>
                <div class="stat-icon-bg"><i class="fas fa-female"></i></div>
            </div>
        </div>
    </div>

    {{-- Main Card --}}
    <div class="card">
        <div class="card-header">
            <div>
                <h5><i class="fas fa-user-graduate" style="color: #3b82f6; margin-right: 10px;"></i>Daftar Siswa</h5>
                <small style="color: #6b7280;">Kelola penempatan siswa ke kelas</small>
            </div>
            <div style="display: flex; gap: 10px;">
                <a href="{{ route('waka.manajemen-siswa.print', request()->query()) }}" class="btn btn-print" target="_blank">
                    <i class="fas fa-print"></i> Cetak
                </a>
            </div>
        </div>
        
        <div class="card-body">
            {{-- Filter Section --}}
            <form action="{{ route('waka.manajemen-siswa.index') }}" method="GET">
                <div class="filter-section">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" placeholder="Cari nama/NISN/NIS..." value="{{ request('search') }}">
                    </div>
                    
                    <select name="jenjang" class="filter-select" onchange="this.form.submit()">
                        <option value="">Semua Jenjang</option>
                        @foreach($jenjangs as $j)
                            <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                        @endforeach
                    </select>

                    <div class="dropdown" style="display: inline-block;">
                        <button class="filter-select d-flex align-items-center justify-content-between" type="button" id="dropdownKelas" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="min-width: 200px; text-align: left; background: white;">
                            <span id="selectedKelasText">Pilih Kelas</span>
                            <i class="fas fa-chevron-down ms-2" style="font-size: 0.8em; color: #6b7280;"></i>
                        </button>
                        <ul class="dropdown-menu p-2" aria-labelledby="dropdownKelas" style="max-height: 300px; overflow-y: auto; width: 100%; min-width: 250px;">
                            <li>
                                <div class="form-check p-2 border-bottom mb-1">
                                    <input class="form-check-input" type="checkbox" id="checkAllKelas">
                                    <label class="form-check-label fw-bold" for="checkAllKelas">Pilih Semua</label>
                                </div>
                            </li>
                            @foreach($kelasList->groupBy('jenjang') as $jenjang => $kelasGroup)
                                <li><h6 class="dropdown-header text-uppercase font-weight-bold p-2 mt-1">{{ $jenjang }}</h6></li>
                                @foreach($kelasGroup as $k)
                                    <li>
                                        <div class="form-check px-3 py-1 hover-bg-light">
                                            <input class="form-check-input class-checkbox" type="checkbox" name="kelas_id[]" value="{{ $k->id }}" id="kelas_{{ $k->id }}"
                                                {{ (is_array(request('kelas_id')) && in_array($k->id, request('kelas_id'))) || request('kelas_id') == $k->id ? 'checked' : '' }}>
                                            <label class="form-check-label w-100 cursor-pointer" for="kelas_{{ $k->id }}">
                                                {{ $k->nama_kelas }}
                                            </label>
                                        </div>
                                    </li>
                                @endforeach
                            @endforeach
                        </ul>
                    </div>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            const checkboxes = document.querySelectorAll('.class-checkbox');
                            const checkAll = document.getElementById('checkAllKelas');
                            const buttonText = document.getElementById('selectedKelasText');
                            
                            function updateButtonText() {
                                const checked = Array.from(checkboxes).filter(cb => cb.checked);
                                if (checked.length === 0) {
                                    buttonText.textContent = 'Pilih Kelas';
                                    buttonText.style.color = '#6b7280';
                                } else if (checked.length === checkboxes.length) {
                                    buttonText.textContent = 'Semua Kelas (' + checked.length + ')';
                                    buttonText.style.color = '#111827';
                                } else {
                                    buttonText.textContent = checked.length + ' Kelas Dipilih';
                                    buttonText.style.color = '#111827';
                                }
                            }

                            // Check all functionality
                            checkAll.addEventListener('change', function() {
                                checkboxes.forEach(cb => cb.checked = this.checked);
                                updateButtonText();
                            });

                            // Individual checkbox change
                            checkboxes.forEach(cb => {
                                cb.addEventListener('change', function() {
                                    updateButtonText();
                                    checkAll.checked = Array.from(checkboxes).every(c => c.checked);
                                });
                            });

                            // Initial update
                            updateButtonText();
                            checkAll.checked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(c => c.checked);
                        });
                    </script>
                    
                    <select name="status" class="filter-select" onchange="this.form.submit()">
                        <option value="aktif" {{ request('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                        <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                        <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                        <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                    </select>

                    <label style="display: flex; align-items: center; gap: 6px; font-size: 14px; cursor: pointer;">
                        <input type="checkbox" name="no_kelas" value="1" {{ request('no_kelas') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                        Belum ada kelas
                    </label>
                    
                    <button type="submit" class="btn btn-outline">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    
                    @if(request()->hasAny(['search', 'jenjang', 'kelas_id', 'no_kelas']) || request('status') != 'aktif')
                        <a href="{{ route('waka.manajemen-siswa.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i> Reset
                        </a>
                    @endif
                </div>
            </form>

            {{-- Table --}}
            @if($siswaList->count() > 0)
                <div class="table-responsive">
                    <table class="table table-card-mobile">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Jenis Kelamin</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th style="text-align: center; width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($siswaList as $siswa)
                            <tr>
                                <td class="mobile-card-head">
                                    <div class="siswa-info">
                                        <div class="siswa-avatar {{ $siswa->jenis_kelamin == 'P' ? 'female' : '' }}">
                                            {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                                        </div>
                                        <div class="siswa-details">
                                            <span class="siswa-name">{{ $siswa->nama_lengkap }}</span>
                                            <span class="siswa-nisn">NISN: {{ $siswa->nisn }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="JK">
                                    <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-l' : 'badge-p' }}">
                                        {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </span>
                                </td>
                                <td data-label="Kelas">
                                    @if($siswa->kelas)
                                        <span class="kelas-badge">
                                            <i class="fas fa-graduation-cap"></i>
                                            {{ $siswa->kelas->nama_kelas }}
                                        </span>
                                    @else
                                        <span class="no-kelas">Belum ada kelas</span>
                                    @endif
                                </td>
                                <td data-label="Status">
                                    @php
                                        $statusClass = [
                                            'aktif' => 'badge-success',
                                            'lulus' => 'badge-info',
                                            'pindah' => 'badge-warning',
                                            'keluar' => 'badge-secondary',
                                        ][$siswa->status] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ ucfirst($siswa->status) }}</span>
                                </td>
                                <td class="mobile-card-actions">
                                    <div class="action-buttons">
                                        <a href="{{ route('waka.manajemen-siswa.show', $siswa) }}" class="btn btn-icon btn-light-primary" title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('waka.manajemen-siswa.print-kartu', $siswa) }}" class="btn btn-icon btn-light-success" title="Cetak Kartu" target="_blank">
                                            <i class="fas fa-id-card"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($siswaList->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Menampilkan {{ $siswaList->firstItem() }} - {{ $siswaList->lastItem() }} dari {{ $siswaList->total() }} siswa
                        </div>
                        <div>
                            {{ $siswaList->withQueryString()->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="fas fa-user-graduate"></i>
                    <h3>Tidak Ada Data Siswa</h3>
                    <p>Belum ada siswa yang sesuai dengan filter yang dipilih.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
