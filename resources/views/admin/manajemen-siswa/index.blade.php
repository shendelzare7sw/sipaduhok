@extends('layouts.sneat')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')
@section('page-subtitle', 'Kelola data dan penempatan kelas siswa')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/manajemen-siswa.css') }}">
@endsection

@section('content')

    <!-- Stats Row -->
    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon" style="background: #eff6ff; color: #3b82f6;">
                <i class="fas fa-graduation-cap"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['totalSiswa'] }}</div>
                <div class="stat-label">Total Siswa</div>
                <div class="stat-desc">Siswa aktif</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfdf5; color: #10b981;">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['siswaWithKelas'] }}</div>
                <div class="stat-label">Sudah Ada Kelas</div>
                <div class="stat-desc">Terdaftar di kelas</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon" style="background: #fff7ed; color: #f59e0b;">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['siswaNoKelas'] }}</div>
                <div class="stat-label">Belum Ada Kelas</div>
                <div class="stat-desc">Perlu ditempatkan</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon" style="background: #ecfeff; color: #06b6d4;">
                <i class="fas fa-male"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['siswaLaki'] }}</div>
                <div class="stat-label">Laki-laki</div>
                <div class="stat-desc">Siswa</div>
            </div>
        </div>

        <div class="stat-widget">
            <div class="stat-icon" style="background: #fdf2f8; color: #ec4899;">
                <i class="fas fa-female"></i>
            </div>
            <div class="stat-details">
                <div class="stat-value">{{ $stats['siswaPerempuan'] }}</div>
                <div class="stat-label">Perempuan</div>
                <div class="stat-desc">Siswi</div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="ms-card">
        <div class="ms-card-header">
            <div>
                <h5 class="ms-card-title">
                    <i class="fas fa-user-graduate" style="color: #3b82f6;"></i> Daftar Siswa
                </h5>
                <div class="ms-card-subtitle">Kelola penempatan siswa ke kelas</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.manajemen-siswa.print', request()->query()) }}" class="btn btn-secondary text-white btn-sm d-flex align-items-center gap-1" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form action="{{ route('admin.manajemen-siswa.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama/NISN/NIS..." value="{{ request('search') }}">
                </div>

                <select name="cabang_id" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Cabang</option>
                    @foreach($cabangs as $c)
                        <option value="{{ $c->id }}" {{ request('cabang_id') == $c->id ? 'selected' : '' }}>{{ $c->nama_cabang }}</option>
                    @endforeach
                </select>

                <select name="jenjang" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $j)
                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>

                <div class="dropdown" style="display: inline-block;">
                    <button class="form-select filter-select d-flex align-items-center justify-content-between" type="button" id="dropdownKelas" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" style="min-width: 180px; text-align: left;">
                        <span id="selectedKelasText">Pilih Kelas</span>
                        <i class="fas fa-chevron-down ms-2" style="font-size: 0.7em; color: #94a3b8;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-kelas-menu p-2" aria-labelledby="dropdownKelas">
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
                                    <div class="form-check px-3 py-1">
                                        <input class="form-check-input class-checkbox" type="checkbox" name="kelas_id[]" value="{{ $k->id }}" id="kelas_{{ $k->id }}"
                                            {{ (is_array(request('kelas_id')) && in_array($k->id, request('kelas_id'))) || request('kelas_id') == $k->id ? 'checked' : '' }}>
                                        <label class="form-check-label w-100" for="kelas_{{ $k->id }}" style="cursor: pointer;">
                                            {{ $k->nama_kelas }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        @endforeach
                    </ul>
                </div>

                <select name="status" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="aktif" {{ request('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus</option>
                    <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                </select>

                <label class="filter-checkbox">
                    <input type="checkbox" name="no_kelas" value="1" {{ request('no_kelas') == '1' ? 'checked' : '' }} onchange="this.form.submit()">
                    Belum ada kelas
                </label>

                <button type="submit" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'cabang_id', 'jenjang', 'kelas_id', 'no_kelas']) || request('status') != 'aktif')
                    <a href="{{ route('admin.manajemen-siswa.index') }}" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 8px;">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <!-- Table -->
        @if($siswaList->count() > 0)
            <div class="table-responsive">
                <table class="table table-clean">
                    <thead>
                        <tr>
                            <th>Siswa</th>
                            <th>Jenis Kelamin</th>
                            <th>Cabang</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th class="text-end" width="100">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siswaList as $siswa)
                        <tr>
                            <td class="mobile-card-head" data-label="Siswa">
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
                                <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-l' : 'badge-p' }} badge-jnj">
                                    {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                </span>
                            </td>
                            <td data-label="Cabang">{{ $siswa->cabang->nama_cabang ?? '-' }}</td>
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
                                <span class="badge {{ $statusClass }} badge-jnj">{{ ucfirst($siswa->status) }}</span>
                            </td>
                            <td class="td-actions text-end" data-label="Aksi">
                                <div class="d-flex justify-content-end gap-1 action-btns">
                                    <a href="{{ route('admin.manajemen-siswa.show', $siswa) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.manajemen-siswa.print-kartu', $siswa) }}" class="btn btn-sm btn-success text-white" title="Cetak Kartu" target="_blank">
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
            <div class="border-top p-3 d-flex justify-content-between align-items-center flex-wrap">
                <span class="text-muted small">Menampilkan {{ $siswaList->firstItem() }} - {{ $siswaList->lastItem() }} dari {{ $siswaList->total() }} siswa</span>
                <div class="mt-2 mt-sm-0">
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
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.class-checkbox');
        const checkAll = document.getElementById('checkAllKelas');
        const buttonText = document.getElementById('selectedKelasText');

        function updateButtonText() {
            const checked = Array.from(checkboxes).filter(cb => cb.checked);
            if (checked.length === 0) {
                buttonText.textContent = 'Pilih Kelas';
                buttonText.style.color = '#94a3b8';
            } else if (checked.length === checkboxes.length) {
                buttonText.textContent = 'Semua Kelas (' + checked.length + ')';
                buttonText.style.color = '#1e293b';
            } else {
                buttonText.textContent = checked.length + ' Kelas Dipilih';
                buttonText.style.color = '#1e293b';
            }
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateButtonText();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateButtonText();
                if (checkAll) {
                    checkAll.checked = Array.from(checkboxes).every(c => c.checked);
                }
            });
        });

        updateButtonText();
        if (checkAll) {
            checkAll.checked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(c => c.checked);
        }
    });
</script>
@endsection

