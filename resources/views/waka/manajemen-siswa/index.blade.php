@extends('layouts.sneat')

@section('title', 'Manajemen Siswa')
@section('page-title', 'Manajemen Siswa')
@section('page-subtitle', 'Kelola data dan penempatan kelas siswa di cabang Anda')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/manajemen-siswa.css') }}?v={{ filemtime(public_path('css/admin/manajemen-siswa.css')) }}">
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
                    @if($cabangs->first())
                        <span class="badge" style="background: #f3e8ff; color: #6b21a8; font-size: 10px; margin-left: 8px;">
                            <i class="fas fa-building"></i> {{ $cabangs->first()->nama_cabang }}
                        </span>
                    @endif
                    @if($isHistorical)
                        <span class="badge" style="background: #e0f2fe; color: #075985; font-size: 10px; margin-left: 4px;">
                            <i class="fas fa-history"></i> Snapshot {{ $tahunAjarans->firstWhere('id', $taFilterId)?->nama_tahun_ajaran }}
                        </span>
                    @endif
                </h5>
                <div class="ms-card-subtitle">Kelola penempatan siswa ke kelas (scope cabang Anda)</div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('waka.manajemen-siswa.print', request()->query()) }}" class="btn btn-secondary text-white btn-sm d-flex align-items-center gap-1" target="_blank">
                    <i class="fas fa-print"></i> <span class="d-none d-sm-inline">Cetak</span>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form action="{{ route('waka.manajemen-siswa.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama/NISN/NIS..." value="{{ request('search') }}">
                </div>

                <select name="jenjang" class="form-select filter-select" onchange="this.form.submit()" {{ $isHistorical ? 'disabled' : '' }}>
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangs as $j)
                        <option value="{{ $j }}" {{ request('jenjang') == $j ? 'selected' : '' }}>{{ $j }}</option>
                    @endforeach
                </select>

                <div class="dropdown" style="display: inline-block;">
                    <button class="form-select filter-select d-flex align-items-center justify-content-between" type="button" id="dropdownKelas" data-bs-toggle="dropdown" data-bs-auto-close="outside" data-bs-display="static" aria-expanded="false" style="min-width: 180px; text-align: left;">
                        <span id="selectedKelasText">Pilih Kelas</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-kelas-menu p-2" aria-labelledby="dropdownKelas">
                        <li>
                            <div class="px-2 pb-2 border-bottom mb-1">
                                <input type="text" id="searchKelasInput" class="form-control form-control-sm"
                                    placeholder="Cari kelas..." autocomplete="off" style="font-size: 12px;">
                            </div>
                        </li>
                        <li>
                            <div class="form-check p-2 border-bottom mb-1" style="padding-left: 2.2rem !important;">
                                <input class="form-check-input" type="checkbox" id="checkAllKelas">
                                <label class="form-check-label fw-bold" for="checkAllKelas">Pilih Semua (Terlihat)</label>
                            </div>
                        </li>
                        @foreach($kelasList->groupBy('jenjang') as $jenjang => $kelasGroup)
                            <li class="kelas-jenjang-group" data-jenjang="{{ $jenjang }}">
                                <h6 class="dropdown-header text-uppercase font-weight-bold p-2 mt-1">{{ $jenjang }}</h6>
                            </li>
                            @foreach($kelasGroup as $k)
                                <li class="kelas-item"
                                    data-jenjang="{{ $k->jenjang }}"
                                    data-cabang-id="{{ $k->cabang_id }}"
                                    data-search="{{ strtolower($k->nama_kelas . ' ' . ($k->cabang->nama_cabang ?? '')) }}">
                                    <div class="form-check py-1 pe-3" style="padding-left: 2.2rem;">
                                        <input class="form-check-input class-checkbox" type="checkbox" name="kelas_id[]" value="{{ $k->id }}" id="kelas_{{ $k->id }}"
                                            {{ (is_array(request('kelas_id')) && in_array($k->id, request('kelas_id'))) || request('kelas_id') == $k->id ? 'checked' : '' }}
                                            {{ $isHistorical ? 'disabled' : '' }}>
                                        <label class="form-check-label w-100" for="kelas_{{ $k->id }}" style="cursor: pointer;">
                                            {{ $k->nama_kelas }}
                                        </label>
                                    </div>
                                </li>
                            @endforeach
                        @endforeach
                        <li id="kelasEmptyState" class="px-3 py-3 text-center text-muted small" style="display: none;">
                            <i class="fas fa-search"></i> Tidak ada kelas yang cocok
                        </li>
                    </ul>
                </div>

                <select name="status" class="form-select filter-select" onchange="this.form.submit()">
                    <option value="aktif" {{ request('status', 'aktif') == 'aktif' ? 'selected' : '' }}>Status: Aktif</option>
                    <option value="lulus" {{ request('status') == 'lulus' ? 'selected' : '' }}>Lulus / Alumni</option>
                    <option value="pindah" {{ request('status') == 'pindah' ? 'selected' : '' }}>Pindah</option>
                    <option value="keluar" {{ request('status') == 'keluar' ? 'selected' : '' }}>Keluar</option>
                </select>

                <select name="tahun_ajaran_id" class="form-select filter-select" onchange="this.form.submit()" title="Filter berdasarkan tahun ajaran kelas">
                    <option value="">TA: Semua</option>
                    @foreach($tahunAjarans as $ta)
                        <option value="{{ $ta->id }}" {{ request('tahun_ajaran_id') == $ta->id ? 'selected' : '' }}>
                            {{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}
                        </option>
                    @endforeach
                </select>

                <label class="filter-checkbox">
                    <input type="checkbox" name="no_kelas" value="1" {{ request('no_kelas') == '1' ? 'checked' : '' }} onchange="this.form.submit()" {{ $isHistorical ? 'disabled' : '' }}>
                    Belum ada kelas
                </label>

                <button type="submit" class="btn btn-secondary btn-sm px-3" style="border-radius: 8px;">
                    <i class="fas fa-filter me-1"></i> Filter
                </button>

                @if(request()->hasAny(['search', 'jenjang', 'kelas_id', 'no_kelas', 'tahun_ajaran_id']) || request('status') != 'aktif')
                    <a href="{{ route('waka.manajemen-siswa.index') }}" class="btn btn-outline-danger btn-sm px-3" style="border-radius: 8px;">
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
                            <td data-label="Kelas">
                                @php
                                    $snapshot = $isHistorical ? $siswa->statusNaikKelas->first() : null;
                                @endphp
                                @if($isHistorical && $snapshot)
                                    <span class="kelas-badge" title="Kelas asal di TA {{ $tahunAjarans->firstWhere('id', $taFilterId)?->nama_tahun_ajaran }}">
                                        <i class="fas fa-history"></i>
                                        {{ $snapshot->kelas_asal ?? '-' }}
                                    </span>
                                    @if($snapshot->kelas_tujuan)
                                        <small class="text-muted d-block" style="font-size: 11px;">→ {{ $snapshot->kelas_tujuan }}</small>
                                    @endif
                                @elseif($siswa->kelas)
                                    <span class="kelas-badge">
                                        <i class="fas fa-graduation-cap"></i>
                                        {{ $siswa->kelas->nama_kelas }}
                                    </span>
                                @else
                                    <span class="no-kelas">Belum ada kelas</span>
                                @endif
                            </td>
                            <td data-label="Status">
                                @if($isHistorical && $snapshot)
                                    @php
                                        $kelMap = [
                                            'NAIK_KELAS' => ['badge-success', 'Naik Kelas'],
                                            'NAIK_KELAS_TUNGGAKAN' => ['badge-warning', 'Naik (Dispensasi)'],
                                            'TIDAK_NAIK_KELAS' => ['badge-danger', 'Tidak Naik'],
                                            'LULUS' => ['badge-info', 'Lulus'],
                                            'LULUS_TUNGGAKAN' => ['badge-warning', 'Lulus (Dispensasi)'],
                                        ];
                                        [$cls, $lbl] = $kelMap[$snapshot->status_kelulusan] ?? ['badge-secondary', $snapshot->status_kelulusan];
                                    @endphp
                                    <span class="badge {{ $cls }} badge-jnj">{{ $lbl }}</span>
                                @else
                                    @php
                                        $statusClass = [
                                            'aktif' => 'badge-success',
                                            'lulus' => 'badge-info',
                                            'pindah' => 'badge-warning',
                                            'keluar' => 'badge-secondary',
                                        ][$siswa->status] ?? 'badge-secondary';
                                    @endphp
                                    <span class="badge {{ $statusClass }} badge-jnj">{{ ucfirst($siswa->status) }}</span>
                                @endif
                            </td>
                            <td class="td-actions text-end" data-label="Aksi">
                                <div class="d-flex justify-content-end gap-1 action-btns">
                                    <a href="{{ route('waka.manajemen-siswa.show', $siswa) }}" class="btn btn-sm btn-info text-white" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('waka.manajemen-siswa.print-kartu', $siswa) }}" class="btn btn-sm btn-success text-white" title="Cetak Kartu" target="_blank">
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
        const jenjangSelect = document.querySelector('select[name="jenjang"]');
        const searchInput = document.getElementById('searchKelasInput');
        const emptyState = document.getElementById('kelasEmptyState');
        const kelasItems = document.querySelectorAll('.kelas-item');
        const jenjangGroups = document.querySelectorAll('.kelas-jenjang-group');

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

        function applyKelasFilter() {
            const jenjangFilter = jenjangSelect ? jenjangSelect.value : '';
            const searchText = searchInput ? searchInput.value.trim().toLowerCase() : '';
            let visibleCount = 0;
            const visibleJenjangs = new Set();

            kelasItems.forEach(item => {
                const itemJenjang = item.getAttribute('data-jenjang');
                const itemSearch = item.getAttribute('data-search') || '';

                const matchJenjang = !jenjangFilter || itemJenjang === jenjangFilter;
                const matchSearch = !searchText || itemSearch.includes(searchText);
                const visible = matchJenjang && matchSearch;

                item.style.display = visible ? '' : 'none';
                if (visible) {
                    visibleCount++;
                    visibleJenjangs.add(itemJenjang);
                }
            });

            jenjangGroups.forEach(g => {
                g.style.display = visibleJenjangs.has(g.getAttribute('data-jenjang')) ? '' : 'none';
            });

            if (emptyState) emptyState.style.display = visibleCount === 0 ? '' : 'none';
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    const li = cb.closest('.kelas-item');
                    if (li && li.style.display !== 'none') cb.checked = this.checked;
                });
                updateButtonText();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                updateButtonText();
                if (checkAll) {
                    const visibleCbs = Array.from(checkboxes).filter(c => {
                        const li = c.closest('.kelas-item');
                        return li && li.style.display !== 'none';
                    });
                    checkAll.checked = visibleCbs.length > 0 && visibleCbs.every(c => c.checked);
                }
            });
        });

        if (searchInput) {
            searchInput.addEventListener('input', applyKelasFilter);
            searchInput.addEventListener('click', e => e.stopPropagation());
        }

        if (jenjangSelect) jenjangSelect.addEventListener('change', applyKelasFilter);

        applyKelasFilter();
        updateButtonText();
        if (checkAll) {
            checkAll.checked = Array.from(checkboxes).length > 0 && Array.from(checkboxes).every(c => c.checked);
        }
    });
</script>
@endsection
