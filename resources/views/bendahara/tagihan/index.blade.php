@extends('layouts.sneat')

@section('title', 'Kelola Tagihan')
@section('page-title', 'Kelola Tagihan')
@section('page-subtitle', 'Daftar tagihan semua siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/tagihan/index.css'])
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
                        <a href="{{ route('bendahara.tagihan.index', ['tahun_ajaran_id' => $item['tahun_ajaran_id']]) }}"
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
                Catatan: Total Tagihan mencakup seluruh kewajiban siswa di periode berjalan. Gunakan fitur "Buat Tagihan Massal" untuk efisiensi waktu jika tagihan per jenjang bersifat seragam.
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
                    <small class="text-muted">{{ $siswaList->total() }} siswa terdaftar</small>
                </div>

                {{-- Filter Form --}}
                <form action="{{ route('bendahara.tagihan.index') }}" method="GET" id="filterForm" class="d-flex gap-2 align-items-center w-100-mobile">
                    {{-- Filter Dropdown --}}
                    <div class="dropdown filter-dropdown w-100-mobile">
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
                                    @foreach($allTahunAjaran as $ta)
                                        <option value="{{ $ta->id }}" {{ ($selectedYear->id ?? '') == $ta->id ? 'selected' : '' }}>
                                            {{ $ta->nama_tahun_ajaran }} {{ $ta->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Filter Kelas --}}
                            <div class="mb-3">
                                <label class="form-label small fw-bold">Kelas</label>
                                <select name="kelas_id" class="form-select form-select-sm">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ ($filters['kelas_id'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }}) - {{ $kelas->cabang->nama_cabang ?? 'Cabang tidak diketahui' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-sm">Terapkan Filter</button>
                                <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                            </div>
                        </div>
                    </div>

                    {{-- Search Input --}}
                    <div class="search-input-wrapper w-100-mobile">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" id="searchInput" class="search-input"
                            placeholder="Cari nama/NISN..." value="{{ $filters['search'] ?? '' }}"
                            autocomplete="off">
                        <button type="button" class="clear-search {{ ($filters['search'] ?? '') ? 'show' : '' }}"
                            id="clearSearch" title="Hapus pencarian">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Right Group: Action Buttons --}}
            <div class="d-flex gap-2 action-group-mobile">
                <a href="{{ route('bendahara.tagihan.cetak-laporan', request()->query()) }}"
                   class="btn btn-outline-secondary btn-sm shadow-sm fw-bold" target="_blank">
                    <i class="fas fa-print me-1"></i> Cetak Laporan
                </a>
                <div class="btn-group shadow-sm" role="group">
                    <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle me-1"></i> Buat Tagihan
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('bendahara.tagihan.bulk-create') }}">
                            <i class="fas fa-users text-success me-2"></i> Tagihan Massal
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('bendahara.tagihan.create-custom') }}">
                            <i class="fas fa-user-plus text-primary me-2"></i> Tagihan Custom
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('bendahara.tagihan.generate-spp') }}">
                            <i class="fas fa-calendar-alt text-info me-2"></i> Generate SPP
                        </a></li>
                    </ul>
                </div>
                <a href="{{ route('bendahara.tagihan.duplicate') }}" class="btn btn-outline-info btn-sm shadow-sm fw-bold">
                    <i class="fas fa-copy me-1"></i> Duplikasi
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            @if($siswaList->isEmpty())
                <div class="text-center py-5 text-muted opacity-50">
                    <i class="fas fa-folder-open fa-4x mb-3"></i>
                    <h5>Data siswa tidak ditemukan</h5>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
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
                            @foreach($siswaList as $index => $siswa)
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600" data-label="NO">{{ $siswaList->firstItem() + $index }}</td>
                                    <td class="align-middle" data-label="IDENTITAS SISWA">
                                        <div class="student-identity">
                                            <span class="student-name">{{ $siswa->nama_lengkap }}</span>
                                            <span class="student-nisn">Siswa Aktif</span>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle fw-bold text-gray-800" data-label="NISN">{{ $siswa->nisn }}</td>
                                    <td class="text-center align-middle" data-label="KELAS">
                                        <span class="badge bg-primary px-2 py-1 fw-bold text-uppercase tagihan-kelas-badge">
                                            {{ $siswa->kelas->nama_kelas ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="text-center align-middle" data-label="CABANG">
                                        <span class="cabang-badge">{{ $siswa->cabang->kode_cabang ?? '-' }}</span>
                                    </td>
                                    <td class="align-middle currency-font text-dark" data-label="TOTAL TAGIHAN">
                                        Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle currency-font text-success" data-label="SUDAH BAYAR">
                                        Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle currency-font {{ $siswa->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}" data-label="SISA">
                                        Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}
                                    </td>
                                    <td class="text-center align-middle" data-label="STATUS">
                                        @if($siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)
                                            <span class="badge bg-success badge-status shadow-sm"><i class="fas fa-check-circle"></i> LUNAS</span>
                                        @elseif($siswa->total_tagihan == 0)
                                            <span class="badge bg-light border badge-status text-muted">KOSONG</span>
                                        @else
                                            <span class="badge bg-danger badge-status shadow-sm"><i class="fas fa-times-circle"></i> BELUM LUNAS</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle" data-label="AKSI">
                                        <div class="btn-group shadow-sm">
                                            <a href="{{ route('bendahara.tagihan.show', $siswa->id) }}" class="btn btn-sm btn-info" title="Lihat Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('bendahara.tagihan.edit', $siswa->id) }}" class="btn btn-sm btn-warning" title="Edit Tagihan">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('bendahara.pembayaran.riwayat-siswa', $siswa->id) }}" class="btn btn-sm btn-success" title="Riwayat Bayar">
                                                <i class="fas fa-history"></i>
                                            </a>
                                            <a href="{{ route('bendahara.tagihan.cetak', $siswa->id) }}" class="btn btn-sm btn-secondary" title="Cetak Tagihan" target="_blank">
                                                <i class="fas fa-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light py-3 border-top">
                    <div class="d-flex justify-content-center">
                        {{ $siswaList->withQueryString()->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</div>


@section('scripts')
    @vite(['resources/js/bendahara/tagihan/index.js'])
@endsection