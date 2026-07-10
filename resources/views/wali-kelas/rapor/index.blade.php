@extends('layouts.sneat')

@section('title', 'Kelola Rapor')
@section('page-title', 'Kelola Rapor')
@section('page-subtitle', isset($kelas) && $kelas ? 'Kelola dan terbitkan rapor siswa kelas ' . $kelas->nama_kelas : 'Kelola rapor siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/wali-kelas/rapor/index.css', 'resources/js/wali-kelas/rapor/index.js'])
@endsection

@section('content')
<div class="wk-page">
<div class="container-fluid px-0">
    @if($error ?? false)
        <div class="alert alert-danger shadow-sm border-start border-danger border-4">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ $error }}
        </div>
    @else
        {{-- HEADER ACTIONS --}}
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h4 class="m-0 fw-bold text-primary">Kelola Rapor - {{ $kelas->nama_kelas }}</h4>
                        <p class="text-muted small mb-0">Tahun Ajaran: {{ $kelas->tahunAjaran->nama_tahun_ajaran }} | Cabang: {{ $kelas->cabang->nama_cabang }}</p>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
                        <button type="button" class="btn btn-primary shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#generateAllModal">
                            <i class="fas fa-file-invoice me-1"></i> Generate Semua
                        </button>
                        @if(($statusCount['belum_dibuat'] ?? 0) > 0)
                            <button type="button" class="btn btn-success shadow-sm fw-bold" disabled
                                    title="Generate dulu rapor untuk {{ $statusCount['belum_dibuat'] }} siswa yang belum memiliki rapor sebelum mengirim semua."
                                    data-bs-toggle="tooltip" data-bs-placement="bottom">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Semua ke Ketua
                            </button>
                        @else
                            <button type="button" class="btn btn-success shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#kirimSemuaModal">
                                <i class="fas fa-paper-plane me-1"></i> Kirim Semua ke Ketua
                            </button>
                        @endif
                        <button type="button" class="btn btn-info shadow-sm fw-bold text-white" data-bs-toggle="modal" data-bs-target="#terapkanTemplateModal">
                            <i class="fas fa-magic me-1"></i> Terapkan Template
                        </button>
                        <a href="{{ route('wali.template-capaian.index') }}" class="btn btn-outline-secondary shadow-sm fw-bold">
                            <i class="fas fa-book me-1"></i> Kelola Template
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER SEMESTER & JENIS RAPOR --}}
        <div class="card shadow mb-4">
            <div class="card-body py-3">
                <form action="{{ route('wali.rapor.index') }}" method="GET">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <label class="small fw-bold text-uppercase">Pilih Semester</label>
                            <select name="semester" class="form-select" data-auto-submit>
                                <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Semester Ganjil</option>
                                <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Semester Genap</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="small fw-bold text-uppercase">Jenis Rapor</label>
                            <select name="jenis_rapor" class="form-select" data-auto-submit>
                                <option value="akhir_semester" {{ $jenisRapor == 'akhir_semester' ? 'selected' : '' }}>Akhir Semester (PAS)</option>
                                <option value="tengah_semester" {{ $jenisRapor == 'tengah_semester' ? 'selected' : '' }}>Tengah Semester (PTS)</option>
                            </select>
                        </div>
                        <div class="col-md-2 mt-2">
                            <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary w-100 shadow-sm">Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- ALUR VALIDASI INFO --}}
        <div class="alert alert-light border border-primary border-opacity-25 shadow-sm mb-4">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <small class="text-muted fw-bold text-uppercase">Alur Validasi:</small>
                <span class="badge bg-primary"><i class="fas fa-edit me-1"></i>1. Buat Rapor</span>
                <i class="fas fa-arrow-right text-muted small"></i>
                <span class="badge bg-info"><i class="fas fa-paper-plane me-1"></i>2. Kirim ke Ketua</span>
                <i class="fas fa-arrow-right text-muted small"></i>
                <span class="badge bg-warning text-white"><i class="fas fa-user-check me-1"></i>3. Ketua Approve</span>
                <i class="fas fa-arrow-right text-muted small"></i>
                <span class="badge bg-primary"><i class="fas fa-calendar-alt me-1"></i>4. Set Tanggal Rilis</span>
                <i class="fas fa-arrow-right text-muted small"></i>
                <span class="badge bg-secondary"><i class="fas fa-money-bill me-1"></i>5. Cek Keuangan</span>
                <i class="fas fa-arrow-right text-muted small"></i>
                <span class="badge bg-success"><i class="fas fa-unlock me-1"></i>6. Akses Wali Siswa</span>
            </div>
        </div>

        {{-- TABEL DATA SISWA & RAPOR --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-graduation-cap me-2"></i>Daftar Rapor - {{ ucwords(str_replace('_', ' ', $jenisRapor)) }} {{ ucfirst($semester) }}
                </h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover wk-card-table mb-0">
                        <thead>
                            <tr>
                                <th width="50">NO</th>
                                <th width="120">NIS</th>
                                <th class="text-start">NAMA LENGKAP SISWA</th>
                                <th width="170">STATUS VALIDASI</th>
                                <th>STATUS RAPOR</th>
                                <th>RATA-RATA</th>
                                <th width="370">AKSI KELOLA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($raporList ?? [] as $index => $item)
                                @php
                                    $siswa = $item['siswa'];
                                    $rapor = $item['rapor'];
                                    $status = $rapor ? $rapor->status : null;

                                    if ($rapor && $rapor->raporNilai->count() > 0) {
                                        $rataRata = $rapor->raporNilai->avg('nilai_angka');
                                    } else {
                                        $rataRata = 0;
                                    }

                                    // Determine validation stage
                                    $validasiStage = 'belum';
                                    if ($siswa->validasi_rapor_bendahara) {
                                        $validasiStage = 'bendahara'; // Akses terbuka
                                    } elseif ($siswa->validasi_rapor_ketua) {
                                        $validasiStage = 'ketua'; // Menunggu bendahara
                                    } elseif ($siswa->validasi_rapor_wali) {
                                        $validasiStage = 'wali'; // Menunggu ketua
                                    }
                                @endphp
                                <tr>
                                    <td class="text-center align-middle fw-bold text-gray-600">{{ $index + 1 }}</td>
                                    <td class="align-middle text-center fw-bold text-gray-800">{{ $siswa->nis }}</td>
                                    <td class="align-middle">
                                        <div class="fw-bold text-gray-900">{{ $siswa->nama_lengkap }}</div>
                                        <small class="text-muted text-uppercase">{{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</small>
                                        @if($rapor && $rapor->status_review_ketua === 'perlu_revisi' && $rapor->catatan_revisi_ketua)
                                            <div class="mt-1">
                                                <span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Perlu Revisi</span>
                                                <div class="small text-danger mt-1" title="{{ $rapor->catatan_revisi_ketua }}">
                                                    <i class="fas fa-comment-dots me-1"></i>{{ Str::limit($rapor->catatan_revisi_ketua, 50) }}
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($validasiStage === 'bendahara')
                                            <span class="badge bg-success" title="Disetujui Bendahara - Akses wali siswa terbuka">
                                                <i class="fas fa-unlock me-1"></i> Akses Terbuka
                                            </span>
                                        @elseif($validasiStage === 'ketua')
                                            <span class="badge bg-secondary" title="Sudah divalidasi Ketua, menunggu Bendahara">
                                                <i class="fas fa-clock me-1"></i> Tunggu Bendahara
                                            </span>
                                        @elseif($validasiStage === 'wali')
                                            <span class="badge bg-info text-white" title="Sudah dikirim ke Ketua PKBM, menunggu validasi">
                                                <i class="fas fa-paper-plane me-1"></i> Tunggu Ketua
                                            </span>
                                        @else
                                            <span class="badge bg-light text-secondary border" title="Belum dikirim ke Ketua PKBM">
                                                <i class="fas fa-minus-circle me-1"></i> Belum Dikirim
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        @if($status == 'diterbitkan')
                                            <span class="badge bg-success badge-status"><i class="fas fa-check-circle me-1"></i> Diterbitkan</span>
                                        @elseif($rapor)
                                            <span class="badge bg-warning badge-status text-white"><i class="fas fa-edit me-1"></i> Draft</span>
                                        @else
                                            <span class="text-xs text-muted fst-italic">Belum Generate</span>
                                        @endif
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="rata-rata-val">{{ $rataRata > 0 ? number_format($rataRata, 2) : '-' }}</span>
                                    </td>
                                    <td class="text-center align-middle btn-action-group">
                                        @if($rapor)
                                            <a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="btn btn-warning btn-sm shadow-sm" title="Edit Catatan & Kehadiran">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank" title="Preview Rapor">
                                                <i class="fas fa-eye"></i> Preview
                                            </a>
                                            <a href="{{ route('wali.rapor.export-excel', $rapor->id) }}" class="btn btn-success btn-sm shadow-sm" title="Export ke Excel">
                                                <i class="fas fa-file-excel"></i> Excel
                                            </a>

                                            @if($status == 'draft')
                                                {{-- Kirim / Batalkan Kirim ke Ketua --}}
                                                @if(!$siswa->validasi_rapor_wali)
                                                    <button type="button" class="btn btn-success btn-sm shadow-sm" title="Kirim ke Ketua PKBM untuk divalidasi"
                                                            data-bs-toggle="modal" data-bs-target="#kirimKetuaModal{{ $rapor->id }}">
                                                        <i class="fas fa-paper-plane"></i> Kirim ke Ketua
                                                    </button>

                                                    {{-- Modal Kirim ke Ketua --}}
                                                    <div class="modal fade" id="kirimKetuaModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title fw-bold text-white">
                                                                        <i class="fas fa-paper-plane me-2"></i>Kirim Rapor ke Ketua
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body py-4">
                                                                    <div class="text-center mb-3">
                                                                        <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
                                                                        <h6 class="fw-bold mb-1">Kirim rapor untuk divalidasi?</h6>
                                                                        <p class="text-muted small mb-0">{{ $siswa->nama_lengkap }}</p>
                                                                    </div>
                                                                    <div class="alert alert-success bg-light border-success mb-0 small">
                                                                        <ul class="mb-0">
                                                                            <li>Rapor dikirim ke <strong>Ketua PKBM</strong> untuk review</li>
                                                                            <li>Anda tidak bisa edit sampai proses validasi selesai</li>
                                                                            <li>Anda bisa <strong>batalkan kiriman</strong> selama belum divalidasi</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        <i class="fas fa-times me-1"></i> Batal
                                                                    </button>
                                                                    <form action="{{ route('wali.rapor.kirim-validasi', $rapor->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success">
                                                                            <i class="fas fa-paper-plane me-1"></i> Ya, Kirim ke Ketua
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif(!$siswa->validasi_rapor_ketua)
                                                    <button type="button" class="btn btn-outline-secondary btn-sm shadow-sm"
                                                            data-bs-toggle="modal" data-bs-target="#batalkanKirimanModal{{ $rapor->id }}"
                                                            title="Batalkan kiriman ke Ketua PKBM">
                                                        <i class="fas fa-undo"></i> Batalkan Kiriman
                                                    </button>

                                                    {{-- Modal Batalkan Kiriman --}}
                                                    <div class="modal fade" id="batalkanKirimanModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header bg-secondary text-white">
                                                                    <h5 class="modal-title fw-bold text-white">
                                                                        <i class="fas fa-undo me-2"></i>Batalkan Kiriman ke Ketua?
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body py-4">
                                                                    <div class="text-center mb-3">
                                                                        <i class="fas fa-undo fa-3x text-secondary mb-3"></i>
                                                                        <h6 class="fw-bold mb-1">Batalkan kiriman rapor</h6>
                                                                        <p class="text-muted small mb-0">{{ $siswa->nama_lengkap }}</p>
                                                                    </div>
                                                                    <div class="alert alert-warning bg-light border-warning mb-0 small">
                                                                        <ul class="mb-0">
                                                                            <li>Status validasi <strong>direset ke awal</strong></li>
                                                                            <li>Wali kelas perlu mengirim ulang ke Ketua PKBM</li>
                                                                            <li>Data rapor <strong>tidak terhapus</strong></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                        <i class="fas fa-times me-1"></i> Batal
                                                                    </button>
                                                                    <form action="{{ route('wali.rapor.batalkan-kirim-validasi', $rapor->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-danger">
                                                                            <i class="fas fa-undo me-1"></i> Ya, Batalkan Kiriman
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- Set Tanggal Rilis / Rilis Sekarang: hanya jika Bendahara sudah approve --}}
                                                @if($siswa->validasi_rapor_bendahara)
                                                    @if(!$rapor->tanggal_rilis)
                                                        <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tanggalRilisModal{{ $rapor->id }}" title="Set tanggal rilis rapor">
                                                            <i class="fas fa-calendar-alt"></i> Set Tanggal Rilis
                                                        </button>
                                                    @else
                                                        <span class="badge bg-info text-white me-1" title="Dijadwalkan rilis {{ $rapor->tanggal_rilis->format('d/m/Y') }}">
                                                            <i class="fas fa-calendar-check me-1"></i>{{ $rapor->tanggal_rilis->format('d/m/Y') }}
                                                        </span>
                                                    @endif

                                                    <button type="button" class="btn btn-success btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#rilisSekarangModal{{ $rapor->id }}" title="Terbitkan rapor sekarang">
                                                        <i class="fas fa-paper-plane"></i> Rilis Sekarang
                                                    </button>

                                                    {{-- Modal Set Tanggal Rilis --}}
                                                    <div class="modal fade" id="tanggalRilisModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title fw-bold text-white">
                                                                        <i class="fas fa-calendar-alt me-2"></i>Set Tanggal Rilis Rapor
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <form action="{{ route('wali.rapor.terbitkan', $rapor->id) }}" method="POST">
                                                                    @csrf
                                                                    <div class="modal-body py-4">
                                                                        <div class="text-center mb-3">
                                                                            <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
                                                                            <h6 class="fw-bold mb-2">{{ $siswa->nama_lengkap }}</h6>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold small">Tanggal Rilis</label>
                                                                            <input type="date" name="tanggal_rilis" class="form-control"
                                                                                   value="{{ $rapor->tanggal_rilis ? $rapor->tanggal_rilis->format('Y-m-d') : '' }}"
                                                                                   min="{{ now()->format('Y-m-d') }}" required>
                                                                            <small class="text-muted">Rapor akan otomatis terlihat oleh wali siswa pada tanggal ini</small>
                                                                        </div>
                                                                        <div class="alert alert-info bg-light border-info mb-0 small">
                                                                            <ul class="mb-0">
                                                                                <li>Status rapor berubah ke <strong>Diterbitkan</strong></li>
                                                                                <li>Wali siswa dapat melihat rapor mulai tanggal rilis</li>
                                                                                <li>Anda bisa <strong>tarik kembali</strong> kapan saja</li>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer bg-light">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-primary">
                                                                            <i class="fas fa-calendar-check me-1"></i> Set Tanggal & Terbitkan
                                                                        </button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    {{-- Modal Rilis Sekarang --}}
                                                    <div class="modal fade" id="rilisSekarangModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content border-0 shadow-lg">
                                                                <div class="modal-header bg-success text-white">
                                                                    <h5 class="modal-title fw-bold text-white">
                                                                        <i class="fas fa-paper-plane me-2"></i>Rilis Rapor Sekarang
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body py-4 text-center">
                                                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                                                    <h6 class="fw-bold mb-2">Terbitkan rapor {{ $siswa->nama_lengkap }} sekarang?</h6>
                                                                    <div class="alert alert-success bg-light border-success mb-0 small">
                                                                        <ul class="mb-0 text-start">
                                                                            <li>Tanggal rilis diset ke <strong>hari ini</strong></li>
                                                                            <li>Wali siswa <strong>langsung bisa melihat</strong> rapor</li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer bg-light">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <form action="{{ route('wali.rapor.terbitkan', $rapor->id) }}" method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="btn btn-success">
                                                                            <i class="fas fa-paper-plane me-1"></i> Ya, Rilis Sekarang
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <button type="button" class="btn btn-danger btn-sm shadow-sm"
                                                        data-bs-toggle="modal" data-bs-target="#hapusRaporModal{{ $rapor->id }}"
                                                        title="Hapus rapor draft ini">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                                {{-- Modal Hapus Rapor --}}
                                                <div class="modal fade" id="hapusRaporModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            <div class="modal-header bg-danger text-white">
                                                                <h5 class="modal-title fw-bold text-white">
                                                                    <i class="fas fa-trash me-2"></i>Hapus Rapor Draft
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body py-4">
                                                                <div class="text-center mb-3">
                                                                    <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                                                    <h6 class="fw-bold mb-1">Hapus rapor draft?</h6>
                                                                    <p class="text-muted small mb-0">{{ $siswa->nama_lengkap }}</p>
                                                                </div>
                                                                <div class="alert alert-danger bg-light border-danger mb-0 small">
                                                                    <ul class="mb-0">
                                                                        <li>Rapor draft ini akan <strong>dihapus permanen</strong></li>
                                                                        <li>Anda dapat <strong>generate ulang</strong> setelah nilai diperbaiki</li>
                                                                        <li>Data nilai siswa <strong>tidak ikut terhapus</strong></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-1"></i> Batal
                                                                </button>
                                                                <form action="{{ route('wali.rapor.destroy', $rapor->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">
                                                                        <i class="fas fa-trash me-1"></i> Ya, Hapus
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            @elseif($status == 'diterbitkan')
                                                <button type="button" class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#tarikKembaliModal{{ $rapor->id }}" title="Tarik kembali rapor">
                                                    <i class="fas fa-undo"></i> Tarik Kembali
                                                </button>

                                                {{-- Modal Tarik Kembali --}}
                                                <div class="modal fade" id="tarikKembaliModal{{ $rapor->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered">
                                                        <div class="modal-content border-0 shadow-lg">
                                                            <div class="modal-header bg-warning text-white">
                                                                <h5 class="modal-title fw-bold text-white">
                                                                    <i class="fas fa-exclamation-triangle me-2"></i>Konfirmasi Tarik Kembali Rapor
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body py-4">
                                                                <div class="text-center mb-3">
                                                                    <i class="fas fa-undo fa-3x text-warning mb-3"></i>
                                                                    <h6 class="fw-bold mb-2">Tarik kembali rapor {{ $siswa->nama_lengkap }}?</h6>
                                                                </div>
                                                                <div class="alert alert-warning bg-light border-warning mb-0">
                                                                    <ul class="mb-0 small">
                                                                        <li>Status rapor berubah dari <strong>Diterbitkan</strong> ke <strong>Draft</strong></li>
                                                                        <li>Rapor <strong>tidak akan terlihat</strong> oleh wali siswa/siswa</li>
                                                                        <li>Data rapor <strong>tetap tersimpan</strong>, Anda bisa edit dan terbitkan ulang</li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="fas fa-times me-1"></i> Batal
                                                                </button>
                                                                <form action="{{ route('wali.rapor.tarik-kembali', $rapor->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-warning">
                                                                        <i class="fas fa-undo me-1"></i> Ya, Tarik Kembali
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            {{-- Rapor belum ada → Generate atau Upload PDF --}}
                                            <button type="button" class="btn btn-primary btn-sm shadow-sm" title="Generate rapor siswa ini"
                                                    data-bs-toggle="modal" data-bs-target="#generateRaporModal{{ $siswa->id }}">
                                                <i class="fas fa-file-invoice"></i> Generate
                                            </button>

                                            {{-- Modal Generate Rapor --}}
                                            <div class="modal fade" id="generateRaporModal{{ $siswa->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title fw-bold text-white">
                                                                <i class="fas fa-file-invoice me-2"></i>Generate Rapor
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body py-4">
                                                            <div class="text-center mb-3">
                                                                <i class="fas fa-file-invoice fa-3x text-primary mb-3"></i>
                                                                <h6 class="fw-bold mb-1">Generate rapor untuk siswa ini?</h6>
                                                                <p class="text-muted small mb-0">{{ $siswa->nama_lengkap }}</p>
                                                            </div>
                                                            <div class="alert alert-info bg-light border-info mb-0 small">
                                                                <ul class="mb-0">
                                                                    <li>Rapor akan di-generate berdasarkan <strong>nilai yang sudah diinput</strong></li>
                                                                    <li>Anda dapat <strong>edit catatan & kehadiran</strong> setelah generate</li>
                                                                    <li>Rapor berstatus <strong>Draft</strong> hingga diterbitkan</li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer bg-light">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="fas fa-times me-1"></i> Batal
                                                            </button>
                                                            <form action="{{ route('wali.rapor.generate-single', $siswa->id) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <input type="hidden" name="semester" value="{{ $semester }}">
                                                                <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="fas fa-file-invoice me-1"></i> Ya, Generate
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm shadow-sm"
                                                    data-bs-toggle="modal" data-bs-target="#uploadPdfModal{{ $siswa->id }}"
                                                    title="Upload rapor PDF manual">
                                                <i class="fas fa-file-pdf"></i> Upload PDF
                                            </button>

                                            {{-- Modal Upload PDF --}}
                                            <div class="modal fade" id="uploadPdfModal{{ $siswa->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content border-0 shadow-lg">
                                                        <div class="modal-header bg-primary text-white">
                                                            <h5 class="modal-title fw-bold text-white">
                                                                <i class="fas fa-file-pdf me-2"></i>Upload Rapor PDF
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form action="{{ route('wali.rapor.create-with-mode') }}" method="POST" enctype="multipart/form-data">
                                                            @csrf
                                                            <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                                            <input type="hidden" name="semester" value="{{ $semester }}">
                                                            <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                                                            <input type="hidden" name="mode" value="upload_pdf">
                                                            <div class="modal-body py-4">
                                                                <div class="text-center mb-3">
                                                                    <h6 class="fw-bold">{{ $siswa->nama_lengkap }}</h6>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label class="form-label fw-bold small">File PDF Rapor</label>
                                                                    <input type="file" name="uploaded_pdf" class="form-control" accept=".pdf" required>
                                                                    <small class="text-muted">Maks. 10MB, format PDF</small>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer bg-light">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" class="btn btn-primary">
                                                                    <i class="fas fa-upload me-1"></i> Upload
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        <i class="fas fa-users-slash fa-3x text-gray-200 mb-2"></i>
                                        <p class="text-gray-500 mb-0">Tidak ada data siswa ditemukan di kelas ini.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- INFO PETUNJUK --}}
        <div class="card shadow-sm border-start border-info border-4 mb-4">
            <div class="card-body">
                <h6 class="fw-bold text-info"><i class="fas fa-info-circle me-2"></i>Petunjuk Pengelolaan Rapor</h6>
                <ul class="small text-gray-700 mb-0 mt-2">
                    <li><strong>Generate:</strong> Membuat draf rapor berdasarkan nilai yang ada. Bisa dilakukan kapan saja.</li>
                    <li><strong>Edit:</strong> Mengisi catatan wali kelas, data kehadiran, dan kegiatan ekstrakurikuler.</li>
                    <li><strong>Preview:</strong> Melihat tampilan akhir rapor sebelum dikirmkan.</li>
                    <li><strong>Kirim ke Ketua:</strong> Mengirim rapor untuk divalidasi Ketua PKBM.</li>
                    <li><strong>Set Tanggal Rilis:</strong> Muncul setelah Bendahara menyetujui. Pilih tanggal kapan rapor bisa dilihat wali siswa.</li>
                    <li><strong>Rilis Sekarang:</strong> Terbitkan rapor dan langsung bisa dilihat wali siswa hari ini.</li>
                    <li><strong>Upload PDF:</strong> Upload rapor PDF manual untuk siswa yang belum ada rapornya.</li>
                    <li><strong>Tarik Kembali:</strong> Menarik rapor yang sudah diterbitkan kembali ke draft jika ada perbaikan.</li>
                </ul>
            </div>
        </div>
    @endif
</div>
</div>

{{-- MODAL KONFIRMASI GENERATE ALL --}}
<div class="modal fade" id="generateAllModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-file-invoice me-2"></i>Konfirmasi Generate Rapor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-file-invoice fa-3x text-primary mb-3"></i>
                <h6 class="fw-bold mb-2">Generate rapor untuk semua siswa?</h6>
                <p class="text-muted small mb-0">
                    Sistem akan membuat draf rapor <strong>{{ ucwords(str_replace('_', ' ', $jenisRapor)) }}</strong> untuk <strong>semua siswa aktif</strong>
                    berdasarkan nilai semester <strong>{{ ucfirst($semester) }}</strong>. Siswa yang sudah ada rapornya akan dilewati.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form action="{{ route('wali.rapor.generate-all') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-1"></i> Ya, Generate Sekarang
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI KIRIM SEMUA KE KETUA --}}
<div class="modal fade" id="kirimSemuaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-paper-plane me-2"></i>Kirim Semua Rapor ke Ketua PKBM
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-paper-plane fa-3x text-success mb-3"></i>
                <h6 class="fw-bold mb-2">Kirim semua rapor ke Ketua PKBM?</h6>
                <p class="text-muted small mb-0">
                    Semua rapor <strong>{{ ucwords(str_replace('_', ' ', $jenisRapor)) }} {{ ucfirst($semester) }}</strong>
                    yang belum dikirim akan dikirim ke Ketua PKBM untuk divalidasi.
                </p>
                <div class="alert alert-info bg-light border-info mt-3 text-start small mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Pastikan semua rapor sudah lengkap diisi sebelum dikirim. Rapor yang sudah dikirim sebelumnya tidak akan terkirim ulang.
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <form action="{{ route('wali.rapor.kirim-validasi-semua') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-1"></i> Ya, Kirim Semua
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: Terapkan Template Deskripsi Capaian ke seluruh kelas --}}
<div class="modal fade" id="terapkanTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="{{ route('wali.rapor.apply-template-batch') }}" method="POST">
                @csrf
                <input type="hidden" name="semester" value="{{ $semester }}">
                <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor }}">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-magic me-2"></i>Terapkan Template Deskripsi Capaian
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body py-4">
                    @if($templateMapelList->isEmpty())
                        <div class="alert alert-warning border-warning bg-light mb-0 small">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            Belum ada rapor untuk <strong>{{ ucwords(str_replace('_', ' ', $jenisRapor)) }} {{ ucfirst($semester) }}</strong>.
                            <strong>Generate rapor</strong> terlebih dahulu, baru template bisa diterapkan.
                        </div>
                    @else
                        <p class="text-muted small mb-3">
                            Pilih template deskripsi per mata pelajaran. Saat diterapkan, deskripsi capaian
                            <strong>semua siswa kelas {{ $kelas->nama_kelas }}</strong> ({{ ucwords(str_replace('_', ' ', $jenisRapor)) }} {{ ucfirst($semester) }})
                            akan terisi otomatis, lalu tetap bisa diedit per siswa. Mapel yang dibiarkan "— Lewati —" tidak diubah.
                        </p>
                        <div class="border rounded">
                            @foreach($templateMapelList as $mapel)
                                @php $opts = $templatesByMapel[$mapel->id] ?? collect(); @endphp
                                <div class="row align-items-center g-2 px-3 py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="col-5 col-md-4">
                                        <span class="fw-semibold small">{{ $mapel->nama_mapel }}</span>
                                    </div>
                                    <div class="col-7 col-md-8">
                                        @if($opts->isEmpty())
                                            <span class="small text-muted fst-italic">
                                                <i class="fas fa-info-circle me-1"></i>Belum ada template — buat lewat tombol "Kelola Template"
                                            </span>
                                        @else
                                            <select name="templates[{{ $mapel->id }}]" class="form-select form-select-sm">
                                                <option value="">— Lewati —</option>
                                                @foreach($opts as $t)
                                                    <option value="{{ $t->id }}">{{ \Illuminate\Support\Str::limit($t->template_text, 70) }}</option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="form-check mt-3">
                            <input type="checkbox" class="form-check-input" id="tplOverwrite" name="overwrite" value="1">
                            <label class="form-check-label small" for="tplOverwrite">
                                Timpa deskripsi yang sudah terisi <span class="text-muted">(default: hanya mengisi yang masih kosong)</span>
                            </label>
                        </div>
                    @endif
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Batal
                    </button>
                    @if($templateMapelList->isNotEmpty())
                        <button type="submit" class="btn btn-info text-white">
                            <i class="fas fa-magic me-1"></i> Terapkan ke Kelas
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
