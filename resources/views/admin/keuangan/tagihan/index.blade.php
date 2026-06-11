@extends('layouts.sneat')

@section('title', 'Kelola Tagihan')
@section('page-title', 'Kelola Tagihan')
@section('page-subtitle', 'Daftar tagihan semua siswa (Admin)')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/keuangan/tagihan/index.css'])
@endsection

@section('content')
    <div class="tagihan-index-page">
        <div class="container-fluid px-0">
            {{-- ALERT TUNGGAKAN TAHUN SEBELUMNYA --}}
            @if(!empty($tunggakanSummary))
            <div class="alert alert-danger border-start border-danger border-4 shadow-sm mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-exclamation-triangle fa-lg me-3 mt-1 text-danger"></i>
                    <div class="flex-grow-1">
                        <h6 class="fw-bold text-danger mb-1">Tunggakan Tahun Sebelumnya</h6>
                        <p class="mb-2 small">
                            Terdapat <strong>{{ $tunggakanSummary['jumlah_siswa'] }} siswa</strong> dengan total tunggakan
                            <strong class="text-danger">Rp {{ number_format($tunggakanSummary['total_tunggakan'], 0, ',', '.') }}</strong>
                            dari tahun ajaran sebelumnya.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tunggakanSummary['per_tahun'] as $item)
                                <a href="{{ route('admin.keuangan.tagihan.index', ['tahun_ajaran_id' => $item['tahun_ajaran_id']]) }}"
                                   class="btn btn-outline-danger btn-sm fw-bold">
                                    <i class="fas fa-eye me-1"></i> {{ $item['nama_tahun'] }}
                                    ({{ $item['jumlah_siswa'] }} siswa - Rp {{ number_format($item['total'], 0, ',', '.') }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- INFORMASI TAMBAHAN --}}
            <div class="alert alert-warning border-start border-warning border-4 shadow-sm mb-4">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                    <small class="fw-bold text-gray-800">
                        Catatan: Total Tagihan mencakup seluruh kewajiban siswa di periode berjalan. Gunakan fitur "Buat
                        Tagihan Massal" untuk efisiensi waktu jika tagihan per jenjang bersifat seragam.
                    </small>
                </div>
            </div>

            {{-- TABEL UTAMA --}}
            <div class="card shadow mb-4">
                <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
                    {{-- Left Group: Title & Filter --}}
                    <div class="d-flex flex-wrap align-items-center gap-3 w-100-mobile">
                        {{-- Title --}}
                        <div>
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="fas fa-list me-2"></i>Daftar Tagihan Siswa
                            </h6>
                            <small class="text-muted">{{ $siswaList ? $siswaList->total() : 0 }} siswa terdaftar</small>
                        </div>
                        <form id="filterForm" action="{{ route('admin.keuangan.tagihan.index') }}" method="GET" class="search-form">
                            <div class="dropdown filter-dropdown">
                                <button class="btn btn-secondary dropdown-toggle w-100-mobile d-flex justify-content-between align-items-center" type="button" id="filterDropdown"
                                    data-bs-toggle="dropdown" aria-expanded="false"
                                    data-bs-auto-close="outside" data-bs-display="static">
                                    <span><i class="fas fa-filter me-1"></i> Filter</span>
                                </button>
                                <div class="dropdown-menu p-3 shadow-lg border-0 tagihan-filter-menu" aria-labelledby="filterDropdown">
                                    <h6 class="dropdown-header px-0 text-uppercase small fw-bold text-primary mb-2">Opsi Filter</h6>

                                    {{-- Filter Tahun Ajaran --}}
                                    <div class="mb-2">
                                        <label class="form-label small fw-bold">Tahun Ajaran</label>
                                        <select name="tahun_ajaran_id" class="form-select form-select-sm" data-auto-submit>
                                            @forelse($allTahunAjaran ?? [] as $ta)
                                                <option value="{{ $ta->id ?? '' }}" {{ optional($selectedYear)->id == ($ta->id ?? null) ? 'selected' : '' }}>
                                                    {{ $ta->nama_tahun_ajaran ?? 'Tahun Ajaran' }} {{ optional($ta)->is_active ? '(Aktif)' : '' }}
                                                </option>
                                            @empty
                                                <option value="">Tidak ada tahun ajaran</option>
                                            @endforelse
                                        </select>
                                    </div>

                                    {{-- Filter Kelas --}}
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold">Kelas</label>
                                        <select name="kelas_id" class="form-select form-select-sm">
                                            <option value="">Semua Kelas</option>
                                            @forelse($kelasList ?? [] as $kelas)
                                                <option value="{{ $kelas->id ?? '' }}" {{ (($filters ?? [])['kelas_id'] ?? '') == ($kelas->id ?? '') ? 'selected' : '' }}>
                                                    {{ $kelas->nama_kelas ?? 'Kelas' }} ({{ $kelas->jenjang ?? '-' }}) - {{ optional($kelas->cabang)->nama_cabang ?? 'Cabang tidak diketahui' }}
                                                </option>
                                            @empty
                                            @endforelse
                                        </select>
                                    </div>

                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Search Input --}}
                            <div class="search-input-wrapper w-100-mobile">
                                <i class="fas fa-search search-icon"></i>
                                <input type="text" name="search" id="searchInput" class="search-input"
                                    placeholder="Cari nama/NISN..." value="{{ ($filters ?? [])['search'] ?? '' }}"
                                    autocomplete="off">
                                <button type="button" class="clear-search {{ (($filters ?? [])['search'] ?? '') ? 'show' : '' }}"
                                    id="clearSearch" title="Hapus pencarian">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Right Group: Action Buttons --}}
                    <div class="d-flex gap-2 action-group-mobile">
                        <a href="{{ route('admin.keuangan.tagihan.import') }}"
                            class="btn btn-outline-danger btn-sm shadow-sm fw-bold">
                            <i class="fas fa-file-import me-1"></i> Import
                        </a>
                        <a href="{{ route('admin.keuangan.tagihan.cetak-laporan', request()->query()) }}"
                            class="btn btn-outline-secondary btn-sm shadow-sm fw-bold" target="_blank">
                            <i class="fas fa-print me-1"></i> Cetak Laporan
                        </a>
                        <div class="btn-group shadow-sm" role="group">
                            <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-plus-circle me-1"></i> Buat Tagihan
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.bulk-create') }}">
                                    <i class="fas fa-users text-success me-2"></i> Tagihan Massal
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.create-custom') }}">
                                    <i class="fas fa-user-plus text-primary me-2"></i> Tagihan Custom
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.generate-spp') }}">
                                    <i class="fas fa-calendar-alt text-info me-2"></i> Generate SPP
                                </a></li>
                            </ul>
                        </div>
                        <a href="{{ route('admin.keuangan.tagihan.duplicate') }}" class="btn btn-outline-info btn-sm shadow-sm fw-bold">
                            <i class="fas fa-copy me-1"></i> Duplikasi
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if(!$siswaList || $siswaList->isEmpty())
                        <div class="text-center py-5 text-muted opacity-50">
                            <i class="fas fa-folder-open fa-4x mb-3"></i>
                            <h5>Data siswa tidak ditemukan</h5>
                        </div>
                    @else
                        {{-- Mobile Select All (Only visible on small screens since thead is hidden) --}}
                        <div class="d-md-none p-3 border-bottom d-flex align-items-center bg-light">
                            <input type="checkbox" id="selectAllMobile" class="me-2 mobile-select-checkbox" title="Pilih Semua" data-select-all-tagihan>
                            <label for="selectAllMobile" class="fw-bold text-gray-700 mb-0 mobile-select-label">Pilih Semua Siswa</label>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="checkbox-cell">
                                            <input type="checkbox" id="selectAll" title="Pilih Semua" data-select-all-tagihan>
                                        </th>
                                        <th width="50">NO</th>
                                        <th class="text-start">IDENTITAS SISWA</th>
                                        <th>NISN</th>
                                        <th>KELAS</th>
                                        <th>CABANG</th>
                                        <th>TOTAL TAGIHAN</th>
                                        <th>SUDAH BAYAR</th>
                                        <th>SISA</th>
                                        <th>STATUS</th>
                                        <th width="120">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($siswaList ?? [] as $index => $siswa)
                                        <tr data-siswa-id="{{ $siswa->id ?? '' }}" data-siswa-name="{{ $siswa->nama_lengkap ?? 'Siswa' }}">
                                            <td class="checkbox-cell align-middle" data-label="PILIH UNTUK RESET">
                                                <input type="checkbox" class="row-checkbox" value="{{ $siswa->id ?? '' }}" data-row-checkbox>
                                            </td>
                                            <td class="text-center align-middle fw-bold text-gray-600" data-label="NO">
                                                {{ ($siswaList && method_exists($siswaList, 'firstItem')) ? $siswaList->firstItem() + $index : $index + 1 }}</td>
                                            <td class="align-middle" data-label="IDENTITAS SISWA">
                                                <div class="student-identity">
                                                    <span class="student-name">{{ $siswa->nama_lengkap }}</span>
                                                    <span class="student-nisn">Siswa Aktif</span>
                                                </div>
                                            </td>
                                            <td class="text-center align-middle fw-bold text-gray-800" data-label="NISN">{{ $siswa->nisn }}</td>
                                            <td class="text-center align-middle" data-label="KELAS">
                                                <span class="badge bg-primary px-2 py-1 fw-bold text-uppercase tagihan-kelas-badge">
                                                    {{ optional($siswa->kelas)->nama_kelas ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle" data-label="CABANG">
                                                <span class="cabang-badge">{{ optional($siswa->cabang)->kode_cabang ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle currency-font text-dark" data-label="TOTAL TAGIHAN">
                                                Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="align-middle currency-font text-success" data-label="SUDAH BAYAR">
                                                Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}
                                            </td>
                                            <td data-label="SISA"
                                                class="align-middle currency-font {{ $siswa->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle" data-label="STATUS">
                                                @if($siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)
                                                    <span class="badge bg-success badge-status shadow-sm"><i
                                                            class="fas fa-check-circle"></i> LUNAS</span>
                                                @elseif($siswa->total_tagihan == 0)
                                                    <span class="badge bg-light border badge-status text-muted">KOSONG</span>
                                                @else
                                                    <span class="badge bg-danger badge-status shadow-sm"><i
                                                            class="fas fa-times-circle"></i> BELUM LUNAS</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle" data-label="AKSI">
                                                <div class="btn-group shadow-sm">
                                                    <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.tagihan.edit', $siswa->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit Tagihan">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.pembayaran.riwayat-siswa', $siswa->id) }}"
                                                        class="btn btn-sm btn-success" title="Riwayat Bayar">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.tagihan.cetak', $siswa->id) }}"
                                                        class="btn btn-sm btn-secondary" title="Cetak Tagihan" target="_blank">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light py-3 border-top">
                            <div class="d-flex justify-content-center">
                                {{ $siswaList ? $siswaList->withQueryString()->links() : '' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    {{-- Floating Reset Toolbar --}}
    <div class="reset-toolbar" id="resetToolbar">
        <span class="selected-count">
            <i class="fas fa-check-square me-1"></i>
            <span id="selectedCount">0</span> siswa dipilih
        </span>
        <button type="button" class="btn-cancel-select" data-clear-selection>
            <i class="fas fa-times me-1"></i> Batal
        </button>
        <button type="button" class="btn-reset" data-bs-toggle="modal" data-bs-target="#resetTagihanModal">
            <i class="fas fa-trash-restore me-1"></i> Reset Tagihan
        </button>
    </div>

    {{-- Modal Konfirmasi Reset Tagihan --}}
    <div class="modal fade" id="resetTagihanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header reset-modal-header">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>PERINGATAN: Reset Tagihan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center mb-3">
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3 reset-warning-icon"></i>
                        <h5 class="fw-bold text-danger mb-2">TINDAKAN BERBAHAYA!</h5>
                    </div>

                    <div class="card bg-light border mb-3">
                        <div class="card-body p-3">
                            <h6 class="fw-bold small text-uppercase text-muted mb-2">
                                <i class="fas fa-users me-1"></i> Siswa yang akan direset:
                            </h6>
                            <div id="resetSiswaList" class="small reset-siswa-list"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batalkan
                    </button>
                    <form id="formResetTagihan" action="{{ url('admin/keuangan/tagihan/reset-tagihan') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="siswa_ids" id="resetSiswaIds">
                        <input type="hidden" name="tahun_ajaran_id" value="{{ optional($selectedYear)->id }}">
                        <button type="button" class="btn btn-danger fw-bold" id="btnExecReset" data-execute-reset>
                            <i class="fas fa-trash-restore me-1"></i> Ya, Reset Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @vite(['resources/js/admin/keuangan/tagihan/index.js'])
@endsection