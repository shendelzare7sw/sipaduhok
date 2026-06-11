@extends('layouts.sneat')

@section('title', 'Validasi Akses')
@section('page-title', 'Validasi Akses Ujian & Rapor')
@section('page-subtitle', 'Validasi akses berdasarkan status pembayaran siswa')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/bendahara/validasi-akses/index.css'])
@endsection

@section('content')
@php
    $hasActiveFilter = request()->hasAny(['search', 'cabang_id', 'jenjang', 'kelas_id', 'status_ujian', 'status_rapor']);
    $quickClasses = $quickKelasList ?? $kelasList;
@endphp

<div class="access-shell" data-validasi-akses data-csrf-token="{{ csrf_token() }}">
    <div class="access-flow">
        <span class="flow-label">Alur Validasi</span>
        <span class="badge bg-warning text-dark"><i class="fas fa-user-check me-1"></i>Ketua Approve Rapor</span>
        <i class="fas fa-arrow-right small d-none d-sm-inline"></i>
        <span class="badge bg-primary"><i class="fas fa-money-bill me-1"></i>Bendahara Validasi</span>
        <i class="fas fa-arrow-right small d-none d-sm-inline"></i>
        <span class="badge bg-success"><i class="fas fa-unlock me-1"></i>Akses Terbuka</span>
    </div>

    <div class="stat-row">
        <div class="stat-widget">
            <div class="stat-icon stat-icon-blue">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div>
                <div class="stat-value">{{ $totalSiswa }}</div>
                <div class="stat-label">Total Siswa Aktif</div>
                <div class="stat-desc">Siswa terdaftar</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-green">
                <i class="fas fa-file-signature"></i>
            </div>
            <div>
                <div class="stat-value">{{ $validasiUjian }}</div>
                <div class="stat-label">Akses Ujian Valid</div>
                <div class="stat-desc">Sudah divalidasi</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-purple">
                <i class="fas fa-file-invoice"></i>
            </div>
            <div>
                <div class="stat-value">{{ $validasiRapor }}</div>
                <div class="stat-label">Akses Rapor Valid</div>
                <div class="stat-desc">Sudah divalidasi</div>
            </div>
        </div>
        <div class="stat-widget">
            <div class="stat-icon stat-icon-orange">
                <i class="fas fa-hourglass-half"></i>
            </div>
            <div>
                <div class="stat-value">{{ $belumValidasi }}</div>
                <div class="stat-label">Belum Divalidasi</div>
                <div class="stat-desc">Menunggu antrean</div>
            </div>
        </div>
    </div>

    <div class="access-card">
        <div class="access-card-header">
            <div>
                <h5 class="access-card-title"><i class="fas fa-user-shield title-icon-primary"></i> Daftar Kendali Akses Siswa</h5>
                <div class="access-card-subtitle">Pilih siswa, validasi akses, atau kirim dispensasi ke Ketua PKBM.</div>
            </div>
        </div>

        <form action="{{ route('bendahara.validasi-akses.index') }}" method="GET" class="mb-0">
            <div class="filter-wrapper">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" name="search" placeholder="Cari nama atau NISN..." value="{{ request('search') }}">
                </div>
                <select name="cabang_id" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Cabang</option>
                    @foreach($cabangList as $cabang)
                        <option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>
                    @endforeach
                </select>
                <select name="jenjang" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Jenjang</option>
                    @foreach($jenjangList as $jenjang)
                        <option value="{{ $jenjang }}" {{ request('jenjang') == $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>
                    @endforeach
                </select>
                <select name="kelas_id" class="form-select filter-select" data-auto-submit>
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }}</option>
                    @endforeach
                </select>
                <select name="status_ujian" class="form-select filter-select" data-auto-submit>
                    <option value="">Status Ujian</option>
                    <option value="valid" {{ request('status_ujian') == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="belum" {{ request('status_ujian') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
                <select name="status_rapor" class="form-select filter-select" data-auto-submit>
                    <option value="">Status Rapor</option>
                    <option value="valid" {{ request('status_rapor') == 'valid' ? 'selected' : '' }}>Valid</option>
                    <option value="belum" {{ request('status_rapor') == 'belum' ? 'selected' : '' }}>Belum</option>
                </select>
                <button type="submit" class="btn btn-secondary btn-sm btn-soft px-3">
                    <i class="fas fa-filter"></i> Filter
                </button>
                @if($hasActiveFilter)
                    <a href="{{ route('bendahara.validasi-akses.index') }}" class="btn btn-outline-danger btn-sm btn-soft px-3">
                        <i class="fas fa-times"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="access-toolbar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <label class="mobile-select-all mb-0" for="select-all-mobile">
                    <input type="checkbox" id="select-all-mobile" class="form-check-input m-0">
                    <span>Pilih semua</span>
                </label>
                <div class="selected-badge">
                    <i class="fas fa-check-circle"></i>
                    <span><span id="selectedCount">0</span> siswa terpilih</span>
                </div>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <button type="button" class="btn btn-success btn-sm btn-soft" data-bulk-ujian>
                    <i class="fas fa-check-double"></i> Validasi Ujian
                </button>
                <button type="button" id="btn-bulk-rapor" class="btn btn-outline-secondary btn-sm btn-soft" data-bulk-rapor title="Belum ada siswa yang di-approve Ketua">
                    <i class="fas fa-lock" id="btn-bulk-rapor-icon"></i> Validasi Rapor <small class="opacity-75" id="btn-bulk-rapor-label">(Perlu Ketua)</small>
                </button>
                <button type="button" class="btn btn-warning btn-sm btn-soft" data-bs-toggle="modal" data-bs-target="#dispensasiModal">
                    <i class="fas fa-hand-holding-heart"></i> Ajukan Dispensasi
                    @if(($dispensasiPending ?? 0) > 0)
                        <span class="badge bg-danger ms-1">{{ $dispensasiPending }}</span>
                    @endif
                </button>
            </div>
        </div>

        <form id="bulk-form" action="{{ route('bendahara.validasi-akses.bulk-validasi-selected') }}" method="POST">
            @csrf
            <input type="hidden" name="tipe" id="bulk-action" value="">

            <div class="table-responsive">
                <table class="table table-clean align-middle">
                    <thead>
                        <tr>
                            <th width="42" class="text-center"><input type="checkbox" id="select-all" class="form-check-input"></th>
                            <th width="60" class="text-center">No</th>
                            <th>Identitas Siswa</th>
                            <th>Kelas</th>
                            <th>Tagihan</th>
                            <th>Sisa</th>
                            <th>Akses Ujian</th>
                            <th>Akses Rapor</th>
                            <th class="text-end" width="130">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($siswa as $index => $s)
                            <tr>
                                <td class="text-center" data-label="Pilih">
                                    <input type="checkbox" name="siswa_ids[]" value="{{ $s->id }}" class="siswa-checkbox form-check-input" data-ketua-approved="{{ $s->validasi_rapor_ketua ? '1' : '0' }}">
                                </td>
                                <td class="text-center fw-bold text-muted" data-label="No">{{ $siswa->firstItem() + $index }}</td>
                                <td class="mobile-card-head" data-label="Siswa">
                                    <div class="student-info">
                                        <div class="student-avatar">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</div>
                                        <div class="student-text">
                                            <div class="student-name">{{ $s->nama_lengkap }}</div>
                                            <div class="student-meta">{{ $s->nisn }} | {{ $s->cabang->nama_cabang ?? '-' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="Kelas">
                                    <span class="badge bg-label-primary px-2 py-1">{{ $s->kelas->nama_kelas ?? '-' }}</span>
                                </td>
                                <td data-label="Tagihan">
                                    <span class="currency-font">Rp {{ number_format($s->total_tagihan, 0, ',', '.') }}</span>
                                </td>
                                <td data-label="Sisa">
                                    <span class="currency-font {{ $s->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}</span>
                                </td>
                                <td data-label="Akses Ujian">
                                    @if($s->validasi_ujian_bendahara)
                                        <div class="text-end">
                                            <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i>Valid</span>
                                            <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($s->tanggal_validasi_ujian_bendahara)->format('d/m/Y') }}</div>
                                        </div>
                                    @else
                                        <span class="badge bg-warning text-dark badge-status"><i class="fas fa-clock me-1"></i>Belum</span>
                                    @endif
                                </td>
                                <td data-label="Akses Rapor">
                                    @if($s->validasi_rapor_bendahara)
                                        <div class="text-end">
                                            <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i>Valid</span>
                                            <div class="small text-muted mt-1">{{ \Carbon\Carbon::parse($s->tanggal_validasi_rapor_bendahara)->format('d/m/Y') }}</div>
                                        </div>
                                    @elseif(!$s->validasi_rapor_ketua)
                                        <span class="badge bg-secondary badge-status"><i class="fas fa-hourglass-half me-1"></i>Tunggu Ketua</span>
                                    @else
                                        <span class="badge bg-warning text-dark badge-status"><i class="fas fa-clock me-1"></i>Belum</span>
                                    @endif
                                </td>
                                <td class="mobile-card-actions" data-label="Aksi">
                                    <div class="action-btns">
                                        @if(!$s->validasi_ujian_bendahara)
                                            <button type="button" class="btn btn-sm btn-success" title="Validasi Ujian" data-confirm-action data-url="{{ route('bendahara.validasi-akses.validasi-ujian', $s->id) }}" data-message="Validasi ujian {{ $s->nama_lengkap }}?">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Batal Ujian" data-confirm-action data-url="{{ route('bendahara.validasi-akses.batalkan-ujian', $s->id) }}" data-message="Batalkan validasi ujian?">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif

                                        @if(!$s->validasi_rapor_bendahara)
                                            @if($s->validasi_rapor_ketua)
                                                <button type="button" class="btn btn-sm btn-info text-white" title="Validasi Rapor" data-confirm-action data-url="{{ route('bendahara.validasi-akses.validasi-rapor', $s->id) }}" data-message="Validasi rapor {{ $s->nama_lengkap }}?">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-light border" disabled title="Menunggu validasi Ketua">
                                                    <i class="fas fa-lock text-muted"></i>
                                                </button>
                                            @endif
                                        @else
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="Batal Rapor" data-confirm-action data-url="{{ route('bendahara.validasi-akses.batalkan-rapor', $s->id) }}" data-message="Batalkan validasi rapor?">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        @endif

                                        <a href="{{ route('bendahara.tagihan.show', $s->id) }}" class="btn btn-sm btn-secondary" title="Detail Tagihan">
                                            <i class="fas fa-file-invoice"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-search"></i>
                                        <h6 class="mb-1">Data tidak ditemukan</h6>
                                        <p class="small mb-0">Coba ubah kata kunci atau filter yang sedang aktif.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @if($siswa->hasPages())
            <div class="border-top p-3 d-flex justify-content-center">
                {{ $siswa->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <div class="access-card">
        <div class="access-card-header">
            <div>
                <h5 class="access-card-title"><i class="fas fa-cog title-icon-info"></i> Pengaturan Batas Pembayaran</h5>
                <div class="access-card-subtitle">Tentukan jenis tagihan yang harus lunas untuk tiap periode ujian dan rapor.</div>
            </div>
            <span class="badge bg-label-info px-3 py-2">
                {{ $tahunAjaran->nama_tahun_ajaran ?? $tahunAjaran->nama ?? 'Tahun ajaran aktif' }}
            </span>
        </div>
        <div class="period-settings">
            <p class="text-muted small mb-3">
                Jika tidak ada jenis tagihan yang dipilih, semua siswa dianggap memenuhi batas pembayaran untuk periode tersebut.
            </p>

            @php
                $periodeList = [
                    'pts_ganjil' => 'PTS Ganjil',
                    'pas_ganjil' => 'PAS Ganjil',
                    'pts_genap' => 'PTS Genap',
                    'pas_genap' => 'PAS Genap',
                    'ujian_akhir' => 'Ujian Akhir',
                ];
            @endphp

            <ul class="nav nav-tabs" id="periodeTabs" role="tablist">
                @foreach($periodeList as $key => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="tab-{{ $key }}" data-bs-toggle="tab" data-bs-target="#pane-{{ $key }}" type="button" role="tab">
                            {{ $label }}
                            @if(isset($batasPembayaran[$key]) && !empty($batasPembayaran[$key]->jenis_tagihan_required))
                                <span class="badge bg-success ms-1">{{ count($batasPembayaran[$key]->jenis_tagihan_required) }}</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>

            <div class="tab-content" id="periodeTabContent">
                @foreach($periodeList as $key => $label)
                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="pane-{{ $key }}" role="tabpanel">
                        <div class="period-pane">
                            <form action="{{ route('bendahara.validasi-akses.batas-pembayaran') }}" method="POST">
                                @csrf
                                <input type="hidden" name="periode" value="{{ $key }}">

                                @php
                                    $currentRequired = isset($batasPembayaran[$key]) ? ($batasPembayaran[$key]->jenis_tagihan_required ?? []) : [];
                                    $grouped = [
                                        'Biaya Tetap' => ['uang_pendaftaran', 'uang_pangkal', 'kegiatan', 'buku', 'seragam', 'rapor_foto', 'ujian', 'akm'],
                                        'SPP Bulanan' => ['spp_juli', 'spp_agustus', 'spp_september', 'spp_oktober', 'spp_november', 'spp_desember', 'spp_januari', 'spp_februari', 'spp_maret', 'spp_april', 'spp_mei', 'spp_juni'],
                                    ];
                                @endphp

                                <div class="row g-3">
                                    @foreach($grouped as $groupLabel => $items)
                                        <div class="col-md-6">
                                            <div class="billing-group">
                                                <h6 class="fw-bold text-secondary mb-3">{{ $groupLabel }}</h6>
                                                @foreach($items as $jenis)
                                                    @if($jenisTagihanList->contains($jenis))
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox" name="jenis_tagihan_required[]" value="{{ $jenis }}" id="chk-{{ $key }}-{{ $jenis }}" {{ in_array($jenis, $currentRequired) ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="chk-{{ $key }}-{{ $jenis }}">{{ ucwords(str_replace('_', ' ', $jenis)) }}</label>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm btn-soft">
                                        <i class="fas fa-save"></i> Simpan {{ $label }}
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="access-card">
        <div class="access-card-header">
            <div>
                <h5 class="access-card-title"><i class="fas fa-bolt title-icon-warning"></i> Validasi Kilat Per Kelas</h5>
                <div class="access-card-subtitle">Jalankan validasi massal sesuai filter cabang, jenjang, dan kelas yang aktif.</div>
            </div>
            <span class="quick-count-badge">
                <i class="fas fa-layer-group"></i>
                {{ $quickClasses->count() }} kelas
            </span>
        </div>
        <div class="quick-grid">
            @forelse($quickClasses as $kelas)
                <div class="quick-card">
                    <div class="quick-title">{{ $kelas->nama_kelas }}</div>
                    <div class="quick-meta">{{ $kelas->jenjang }} | {{ $kelas->cabang->nama_cabang ?? '-' }} | {{ $kelas->siswa_aktif_count ?? $kelas->siswa->count() }} Siswa</div>
                    <div class="row g-2">
                        <div class="col">
                            <form id="form-ujian-{{ $kelas->id }}" action="{{ route('bendahara.validasi-akses.bulk-validasi-ujian', $kelas->id) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-success btn-sm w-100 btn-soft" data-confirm-class-action data-form-id="form-ujian-{{ $kelas->id }}" data-title="Validasi Ujian Se-Kelas" data-message="Validasi ujian untuk seluruh siswa di kelas {{ $kelas->nama_kelas }}?">Ujian</button>
                            </form>
                        </div>
                        <div class="col">
                            <form id="form-rapor-{{ $kelas->id }}" action="{{ route('bendahara.validasi-akses.bulk-validasi-rapor', $kelas->id) }}" method="POST">
                                @csrf
                                <button type="button" class="btn btn-info btn-sm w-100 text-white btn-soft" data-confirm-class-action data-form-id="form-rapor-{{ $kelas->id }}" data-title="Validasi Rapor Se-Kelas" data-message="Validasi rapor untuk seluruh siswa di kelas {{ $kelas->nama_kelas }}?">Rapor</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-school"></i>
                    <h6 class="mb-1">Tidak ada kelas</h6>
                    <p class="small mb-0">Coba ubah filter cabang atau jenjang.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-content-clean">
            <div class="modal-header border-0 modal-header-warning">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    <span id="modalTitle">Konfirmasi Aksi</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0 text-dark" id="modalMessage">Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light border" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-warning fw-bold" id="confirmBtn">
                    <i class="fas fa-check me-1"></i> Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-content-clean">
            <div class="modal-header border-0 modal-header-danger">
                <h5 class="modal-title fw-bold text-dark">
                    <i class="fas fa-exclamation-circle text-danger me-2"></i>
                    <span id="alertTitle">Peringatan</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-4">
                <p class="mb-0 text-dark" id="alertMessage">Terjadi kesalahan!</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-primary fw-bold" data-bs-dismiss="modal">
                    <i class="fas fa-check me-1"></i> Mengerti
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dispensasiModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow modal-content-clean">
            <form action="{{ route('bendahara.validasi-akses.dispensasi') }}" method="POST">
                @csrf
                <div class="modal-header border-0 modal-header-warning">
                    <h5 class="modal-title fw-bold text-dark">
                        <i class="fas fa-hand-holding-heart text-warning me-2"></i>Ajukan Dispensasi ke Ketua PKBM
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tipe Dispensasi</label>
                        <select name="tipe" class="form-select" required>
                            <option value="ujian">Akses Ujian</option>
                            <option value="rapor">Akses Rapor</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Periode</label>
                        <select name="periode" class="form-select">
                            <option value="">Semua Periode</option>
                            <option value="pts_ganjil">PTS Ganjil</option>
                            <option value="pas_ganjil">PAS Ganjil</option>
                            <option value="pts_genap">PTS Genap</option>
                            <option value="pas_genap">PAS Genap</option>
                            <option value="ujian_akhir">Ujian Akhir</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Dispensasi</label>
                        <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Siswa memiliki cicilan yang sedang berjalan..."></textarea>
                    </div>
                    <div class="alert alert-info border-0 small mb-0">
                        <i class="fas fa-info-circle me-1"></i> Siswa yang dicentang di tabel akan dimasukkan ke pengajuan dispensasi.
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <div id="dispensasi-siswa-ids"></div>
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold" id="submitDispensasi"><i class="fas fa-paper-plane me-1"></i> Kirim ke Ketua</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/bendahara/validasi-akses/index.js'])
@endsection
