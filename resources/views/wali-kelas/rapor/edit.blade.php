@extends('layouts.app')

@section('title', 'Edit Rapor')
@section('page-title', 'Edit Rapor')
@section('page-subtitle', 'Edit catatan dan kelengkapan rapor siswa')


@section('styles')
    @vite(['resources/css/wali-kelas/rapor/edit.css', 'resources/js/wali-kelas/rapor/edit.js'])
@endsection

@section('content')
<div class="rapor-edit-page wk-page"
     data-reorder-url="{{ route('wali.rapor.reorder-nilai', $rapor->id) }}"
     data-kehadiran-url="{{ route('wali.rapor.kehadiran-auto', $rapor->id) }}"
     data-csrf-token="{{ csrf_token() }}">
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-gray-900 mb-1">Edit Rapor - {{ $rapor->siswa->nama_lengkap }}</h4>
                    <p class="text-muted mb-0">
                        Semester: {{ ucfirst($rapor->semester) }} |
                        Jenis: {{ $rapor->jenis_rapor === 'akhir_semester' ? 'PAS' : 'PTS' }} |
                        Kelas: {{ $rapor->kelas->nama_kelas }}
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    @php
                        $importLocked = $rapor->status !== 'draft' || $rapor->siswa->validasi_rapor_wali;
                        $importLockReason = $rapor->status !== 'draft'
                            ? 'Rapor sudah diterbitkan — tarik kembali ke draft dulu.'
                            : ($rapor->siswa->validasi_rapor_wali ? 'Rapor sudah dikirim ke Ketua PKBM — batalkan kiriman dulu.' : '');
                    @endphp
                    <a href="{{ route('wali.rapor.export-excel', $rapor->id) }}" class="btn btn-success btn-sm shadow-sm">
                        <i class="fas fa-file-excel me-1"></i>Export / Template Excel
                    </a>
                    <button type="button" class="btn btn-outline-success btn-sm shadow-sm"
                            {{ $importLocked ? 'disabled' : '' }}
                            @if(!$importLocked) data-bs-toggle="modal" data-bs-target="#importExcelModal" @endif
                            title="{{ $importLocked ? $importLockReason : 'Upload file Excel hasil export untuk update rapor' }}">
                        <i class="fas fa-file-upload me-1"></i>Import Excel
                    </button>
                    <button type="button" class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#resetNilaiModal">
                        <i class="fas fa-sync-alt me-1"></i>Reset Nilai
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#applyFormatModal">
                        <i class="fas fa-layer-group me-1"></i>Terapkan Template
                    </button>
                    <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank">
                        <i class="fas fa-eye me-1"></i>Preview
                    </a>
                    <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash success/error/warning sudah dirender layouts.sneat secara global - jangan
         diulang di sini (dulu dobel, mis. pesan import Excel muncul 2x). --}}

    {{-- Modal Import Excel --}}
    <div class="modal fade" id="importExcelModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-file-upload me-2"></i>Import Rapor dari Excel
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('wali.rapor.import-excel', $rapor->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="alert alert-info bg-light border-info small mb-3">
                            <strong>Cara penggunaan:</strong>
                            <ol class="mb-0 ps-3 mt-1">
                                <li>Klik tombol <strong>Export / Template Excel</strong> untuk download template berisi data rapor saat ini.</li>
                                <li>Buka file Excel, edit nilai/deskripsi/kehadiran/kegiatan/catatan sesuai kebutuhan.</li>
                                <li>Simpan file, lalu upload kembali di sini untuk update rapor.</li>
                            </ol>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Pilih File Excel</label>
                            <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                            <small class="text-muted">Format: .xlsx atau .xls — maksimal 5 MB. File harus hasil export dari rapor ini.</small>
                        </div>
                        <div class="alert alert-warning bg-light border-warning mb-0 small">
                            <ul class="mb-0">
                                <li>File yang di-upload <strong>harus hasil export rapor yang sama</strong> (identifier RAPOR_ID di file harus match).</li>
                                <li>Nilai di luar range 0-100 akan dilewati dengan peringatan.</li>
                                <li>Kegiatan ekstrakurikuler <strong>akan di-replace</strong> (data lama dihapus, diganti yang di Excel).</li>
                                <li>Hanya bisa import saat rapor masih draft & belum dikirim ke Ketua.</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-file-upload me-1"></i> Upload &amp; Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Reset Nilai --}}
    <div class="modal fade" id="resetNilaiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-sync-alt me-2"></i>Reset Nilai Rapor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold mb-2">Reset semua nilai rapor dari data nilai terbaru?</h6>
                    <div class="alert alert-warning bg-light border-warning mb-0 small text-start">
                        <ul class="mb-0">
                            <li>Nilai angka akan di-generate ulang dari data nilai siswa</li>
                            <li>Deskripsi capaian yang sudah diedit <strong>akan hilang</strong></li>
                            <li>Urutan mata pelajaran akan direset</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('wali.rapor.reset-nilai', $rapor->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt me-1"></i> Ya, Reset Nilai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Terapkan Template Rapor --}}
    <div class="modal fade" id="applyFormatModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold text-white">
                        <i class="fas fa-layer-group me-2"></i>Terapkan Template Rapor
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('wali.rapor.apply-format', $rapor->id) }}" method="POST">
                    @csrf
                    <div class="modal-body py-4">
                        <div class="alert alert-info bg-light border-info small mb-3">
                            Format dari rapor <strong>{{ $rapor->siswa->nama_lengkap }}</strong> akan disalin ke rapor target dengan semester dan jenis rapor yang sama. Target hanya rapor draft yang belum dikirim ke Ketua PKBM.
                        </div>

                        <label class="form-label fw-bold small">Target penerapan</label>
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="border rounded p-3 w-100 h-100">
                                    <input type="radio" name="scope" value="kelas_ini" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Kelas ini saja</span>
                                    <div class="small text-muted mt-1">{{ $rapor->kelas->nama_kelas }}</div>
                                </label>
                            </div>
                            <div class="col-md-6">
                                <label class="border rounded p-3 w-100 h-100">
                                    <input type="radio" name="scope" value="semua_kelas_wali" class="form-check-input me-2" {{ $kelasList->count() <= 1 ? 'disabled' : '' }}>
                                    <span class="fw-bold">Semua kelas saya</span>
                                    <div class="small text-muted mt-1">{{ $kelasList->count() }} kelas wali aktif</div>
                                </label>
                            </div>
                        </div>

                        <label class="form-label fw-bold small">Bagian yang disalin</label>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label class="form-check border rounded p-3 h-100">
                                    <input type="checkbox" name="include_order" value="1" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Susunan mata pelajaran</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-check border rounded p-3 h-100">
                                    <input type="checkbox" name="include_deskripsi" value="1" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Deskripsi capaian</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-check border rounded p-3 h-100">
                                    <input type="checkbox" name="include_display" value="1" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Tampil & kelompok mapel</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-check border rounded p-3 h-100">
                                    <input type="checkbox" name="include_kegiatan" value="1" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Kegiatan ekstra & keterangan</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-check border rounded p-3 h-100">
                                    <input type="checkbox" name="include_catatan" value="1" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Catatan wali kelas</span>
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-check border rounded p-3 h-100">
                                    <input type="checkbox" name="include_alignment" value="1" class="form-check-input me-2" checked>
                                    <span class="fw-bold">Alignment tampilan</span>
                                </label>
                            </div>
                        </div>

                        <label class="form-check border rounded p-3 mt-3">
                            <input type="checkbox" name="overwrite_filled" value="1" class="form-check-input me-2" checked>
                            <span class="fw-bold">Timpa isi yang sudah ada di rapor target</span>
                            <div class="small text-muted mt-1">Matikan opsi ini jika hanya ingin mengisi data yang masih kosong.</div>
                        </label>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check me-1"></i>Terapkan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{ route('wali.rapor.update', $rapor->id) }}" method="POST">
        @csrf
        @method('PUT')

        @php
            $alignmentOptions = [
                'left' => 'Kiri',
                'center' => 'Tengah',
                'right' => 'Kanan',
                'justify' => 'Rata kiri-kanan',
            ];
            $catatanAlignment = old('catatan_alignment', $rapor->catatan_alignment ?? 'center');
            $deskripsiAlignment = old('deskripsi_alignment', $rapor->deskripsi_alignment ?? 'left');
            $keteranganEkstraAlignment = old('keterangan_ekstra_alignment', $rapor->keterangan_ekstra_alignment ?? 'left');
        @endphp

        {{-- Pengaturan Tampilan Rapor --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-sliders-h me-2"></i>Pengaturan Tampilan Rapor
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Alignment Deskripsi Mapel</label>
                        <select name="deskripsi_alignment" class="form-select rapor-alignment-select" data-target=".deskripsi-input">
                            @foreach($alignmentOptions as $value => $label)
                                <option value="{{ $value }}" {{ $deskripsiAlignment === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Alignment Keterangan Ekstra</label>
                        <select name="keterangan_ekstra_alignment" class="form-select rapor-alignment-select" data-target=".keterangan-ekstra-input">
                            @foreach($alignmentOptions as $value => $label)
                                <option value="{{ $value }}" {{ $keteranganEkstraAlignment === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Alignment Catatan Wali</label>
                        <select name="catatan_alignment" class="form-select rapor-alignment-select" data-target=".catatan-wali-input">
                            @foreach($alignmentOptions as $value => $label)
                                <option value="{{ $value }}" {{ $catatanAlignment === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Data Kehadiran --}}
        @php
            $periodRapor = $rapor->tahunAjaran
                ? $rapor->tahunAjaran->getRaporPeriod($rapor->semester, $rapor->jenis_rapor)
                : null;
            $totalPresensiPeriode = $periodRapor
                ? \App\Models\Presensi::where('siswa_id', $rapor->siswa_id)
                    ->where('kelas_id', $rapor->kelas_id)
                    ->whereBetween('tanggal', [$periodRapor['start'], $periodRapor['end']])
                    ->count()
                : 0;
            $jenisLabel = $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS';
        @endphp
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-calendar-check me-2"></i>Data Kehadiran
                </h6>
                <button type="button" class="btn btn-outline-info btn-sm" id="btnSyncKehadiran" title="Sinkron otomatis dari tabel presensi (sumber: /wali/presensi)">
                    <i class="fas fa-sync-alt me-1"></i>Sinkron dari Presensi
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-light border small mb-3 d-flex align-items-start gap-2">
                    <i class="fas fa-info-circle text-info mt-1"></i>
                    <div>
                        <strong>Sumber:</strong> tabel presensi (input via menu Presensi).
                        Yang dihitung: status <strong>sakit/izin/alpha</strong> dalam periode
                        <strong>{{ $jenisLabel }} {{ ucfirst($rapor->semester) }}</strong>
                        @if($periodRapor)
                            ({{ \Carbon\Carbon::parse($periodRapor['start'])->locale('id')->isoFormat('D MMM Y') }}
                            – {{ \Carbon\Carbon::parse($periodRapor['end'])->locale('id')->isoFormat('D MMM Y') }})
                        @endif.
                        <span class="text-muted">Total presensi tercatat periode ini: <strong>{{ $totalPresensiPeriode }}</strong> record.</span>
                        @if($rapor->jenis_rapor === 'tengah_semester')
                            <div class="mt-1 text-muted small">
                                <i class="fas fa-clock"></i> PTS dihitung dari 3 bulan pertama semester (Jul-Sep untuk ganjil, Jan-Mar untuk genap).
                                PAS akan dihitung dari periode penuh semester.
                            </div>
                        @endif
                        @if($totalPresensiPeriode === 0)
                            <div class="mt-1 text-warning"><i class="fas fa-exclamation-triangle"></i>
                                Belum ada presensi tercatat untuk periode ini. Input dulu di
                                <a href="{{ route('wali.presensi.index') }}" target="_blank">menu Presensi</a>.
                            </div>
                        @endif
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Sakit (hari)</label>
                            <input type="number" name="jumlah_sakit" class="form-control"
                                   value="{{ old('jumlah_sakit', $rapor->jumlah_sakit) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Izin (hari)</label>
                            <input type="number" name="jumlah_izin" class="form-control"
                                   value="{{ old('jumlah_izin', $rapor->jumlah_izin) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alpha (hari)</label>
                            <input type="number" name="jumlah_alpha" class="form-control"
                                   value="{{ old('jumlah_alpha', $rapor->jumlah_alpha) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="total-box">
                            <small class="text-muted">Total Ketidakhadiran</small>
                            <div class="h3 mb-0 fw-bold text-primary" id="totalKetidakhadiran">
                                {{ $rapor->jumlah_sakit + $rapor->jumlah_izin + $rapor->jumlah_alpha }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Daftar Nilai --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Daftar Nilai Mata Pelajaran
                </h6>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <label class="form-check form-switch mb-0 d-flex align-items-center gap-2 small text-muted">
                        <input class="form-check-input" type="checkbox" id="syncOrderAll" checked>
                        <span>Sinkron urutan ke semua kelas saya</span>
                    </label>
                    <small class="text-muted"><i class="fas fa-grip-vertical me-1"></i>Drag untuk mengubah urutan</small>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="nilaiTable">
                        <thead>
                            <tr>
                                <th width="30"></th>
                                <th width="40">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center" width="100">Nilai</th>
                                @if($rapor->jenis_rapor === 'akhir_semester')
                                    <th class="text-center" width="80">Kelompok</th>
                                @endif
                                <th class="text-center" width="60">Tampil</th>
                                <th width="30"></th>
                                <th>Deskripsi Capaian</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiSortable">
                            @php $totalNilai = 0; $jumlahMapel = 0; @endphp
                            @foreach($rapor->raporNilai as $index => $raporNilai)
                                @php
                                    $totalNilai += $raporNilai->nilai_angka;
                                    $jumlahMapel++;
                                    // Null override = "Tidak diatur" (mapel jadi Lainnya/-) — beda dari default 'A'
                                    $kelompok = $raporNilai->kelompok_override;
                                    $defaultKelompok = $raporNilai->mataPelajaran->kelompok ?? null;
                                @endphp
                                <tr data-id="{{ $raporNilai->id }}" class="{{ !$raporNilai->is_visible ? 'row-hidden' : '' }}">
                                    <td class="text-center align-middle" data-label="Drag">
                                        <i class="fas fa-grip-vertical drag-handle"></i>
                                    </td>
                                    <td class="text-center align-middle row-number" data-label="No">{{ $index + 1 }}</td>
                                    <td class="align-middle" data-label="Mata Pelajaran"><strong>{{ $raporNilai->mataPelajaran->nama_mapel }}</strong></td>
                                    <td class="text-center align-middle nilai-score-cell" data-label="Nilai">
                                        <strong class="text-primary nilai-score-value">
                                            {{ number_format($raporNilai->nilai_angka, 2) }}
                                        </strong>
                                    </td>
                                    @if($rapor->jenis_rapor === 'akhir_semester')
                                        <td class="text-center align-middle" data-label="Kelompok">
                                            <select name="kelompok_override[{{ $raporNilai->id }}]" class="form-select form-select-sm kelompok-select"
                                                    title="{{ $defaultKelompok ? 'Default mapel: '.$defaultKelompok : 'Belum ada default di admin mapel' }}">
                                                <option value="" {{ $kelompok === null ? 'selected' : '' }}>-</option>
                                                <option value="A" {{ $kelompok === 'A' ? 'selected' : '' }}>A</option>
                                                <option value="B" {{ $kelompok === 'B' ? 'selected' : '' }}>B</option>
                                            </select>
                                            @if($defaultKelompok)
                                                <div class="small text-muted mt-1 default-kelompok-note" title="Default dari admin mapel">
                                                    <i class="fas fa-info-circle"></i> {{ $defaultKelompok }}
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="text-center align-middle" data-label="Tampil">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="hidden" name="visible[{{ $raporNilai->id }}]" value="0">
                                            <input class="form-check-input visibility-toggle" type="checkbox"
                                                   name="visible[{{ $raporNilai->id }}]" value="1"
                                                   {{ $raporNilai->is_visible ? 'checked' : '' }}
                                                   data-row-id="{{ $raporNilai->id }}">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle" data-label="Urutan">
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-light btn-arrow border" data-move-row="up" title="Naik"><i class="fas fa-chevron-up"></i></button>
                                            <button type="button" class="btn btn-light btn-arrow border" data-move-row="down" title="Turun"><i class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </td>
                                    <td class="align-middle" data-label="Deskripsi Capaian">
                                        <textarea name="deskripsi[{{ $raporNilai->id }}]"
                                               rows="2"
                                               class="form-control form-control-sm deskripsi-input alignment-{{ $deskripsiAlignment }}"
                                               placeholder="Deskripsi capaian kompetensi...">{{ old('deskripsi.' . $raporNilai->id, $raporNilai->deskripsi) }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="average-row">
                                <td colspan="{{ $rapor->jenis_rapor === 'akhir_semester' ? 3 : 2 }}" class="text-end fw-bold align-middle average-label">RATA-RATA:</td>
                                <td class="text-center fw-bold align-middle text-primary average-value">
                                    {{ $jumlahMapel > 0 ? number_format($totalNilai / $jumlahMapel, 2) : '0.00' }}
                                </td>
                                <td colspan="{{ $rapor->jenis_rapor === 'akhir_semester' ? 4 : 3 }}"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($rapor->raporNilai->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-gray-200 mb-3"></i>
                        <p class="text-muted">Belum ada data nilai</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kegiatan Ekstra --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-running me-2"></i>Kegiatan Ekstrakurikuler
                </h6>
                <button type="button" class="btn btn-outline-success btn-sm" data-add-kegiatan>
                    <i class="fas fa-plus me-1"></i>Tambah Kegiatan
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="kegiatanTable">
                        <thead>
                            <tr>
                                <th width="40" class="text-center">No</th>
                                <th>Nama Kegiatan</th>
                                <th width="120" class="text-center">Predikat</th>
                                <th>Keterangan</th>
                                <th width="50" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="kegiatanBody">
                            @foreach($kegiatanEkstra as $index => $kegiatan)
                                <tr>
                                    <td class="text-center align-middle kegiatan-no" data-label="No">{{ $index + 1 }}</td>
                                    <td data-label="Nama Kegiatan">
                                        <input type="text" name="kegiatan_ekstra[{{ $index }}][kegiatan_nama]"
                                               class="form-control form-control-sm"
                                               value="{{ old("kegiatan_ekstra.{$index}.kegiatan_nama", $kegiatan->kegiatan_nama) }}"
                                               placeholder="Nama kegiatan...">
                                    </td>
                                    <td data-label="Predikat">
                                        <select name="kegiatan_ekstra[{{ $index }}][predikat]" class="form-select form-select-sm">
                                            <option value="">-</option>
                                            <option value="A" {{ ($kegiatan->predikat ?? '') === 'A' ? 'selected' : '' }}>A</option>
                                            <option value="B" {{ ($kegiatan->predikat ?? '') === 'B' ? 'selected' : '' }}>B</option>
                                            <option value="C" {{ ($kegiatan->predikat ?? '') === 'C' ? 'selected' : '' }}>C</option>
                                        </select>
                                    </td>
                                    <td data-label="Keterangan">
                                        <input type="text" name="kegiatan_ekstra[{{ $index }}][keterangan]"
                                               class="form-control form-control-sm keterangan-ekstra-input alignment-{{ $keteranganEkstraAlignment }}"
                                               value="{{ old("kegiatan_ekstra.{$index}.keterangan", $kegiatan->keterangan) }}"
                                               placeholder="Keterangan...">
                                    </td>
                                    <td class="text-center" data-label="Aksi">
                                        <button type="button" class="btn btn-outline-danger btn-sm" data-remove-kegiatan title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Catatan Wali Kelas --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-comment-alt me-2"></i>Catatan Wali Kelas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan / Komentar untuk Siswa</label>
                    <textarea name="catatan_wali_kelas" class="form-control catatan-wali-input alignment-{{ $catatanAlignment }}" rows="5"
                              placeholder="Contoh: Siswa menunjukkan peningkatan yang baik dalam...">{{ old('catatan_wali_kelas', $rapor->catatan_wali_kelas) }}</textarea>
                    <small class="text-muted">Berikan catatan positif dan saran untuk perkembangan siswa</small>
                </div>
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="card shadow mb-5 submit-card">
            <div class="card-body text-end">
                <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
@endsection
