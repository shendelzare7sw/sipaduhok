@extends('layouts.lms-guru')

@section('title', 'Rekap Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/nilai/index.css'])
@endpush

@push('scripts')
    @vite(['resources/js/guru/lms/nilai/index.js'])
@endpush

@section('content')
@php
    $isKelasAkhir = $kelas->isTingkatAkhir();
@endphp
    <div class="guru-lms-nilai-page">
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header-custom">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Daftar Nilai Siswa</h6>
                            <small class="text-muted">Tahun Ajaran Aktif - Semester {{ ucfirst($semester) }}</small>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            {{-- Semester Selector --}}
                            <form action="{{ route('guru.lms.nilai.index', [$kelas->id, $mapel->id]) }}" method="GET" class="d-flex align-items-center gap-2">
                                <label class="mb-0 fw-semibold text-nowrap"><i class="fas fa-calendar-alt me-1"></i>Semester:</label>
                                <select name="semester" class="form-select form-select-sm semester-select" data-auto-submit>
                                    <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Ganjil (Jul-Des)</option>
                                    <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Genap (Jan-Jun)</option>
                                </select>
                                @if($semester == $currentSemester)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                                @endif
                            </form>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                data-open-recalculate>
                                <i class="fas fa-sync-alt me-1"></i> Hitung Ulang
                            </button>
                            <form id="recalculateForm" action="{{ route('guru.lms.nilai.recalculate', [$kelas->id, $mapel->id]) }}" method="POST" class="d-none">
                                @csrf
                                <input type="hidden" name="semester" value="{{ $semester }}">
                            </form>

                            <div class="btn-group">
                                <button type="button" class="btn btn-sm btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-file-excel me-1"></i> Excel
                                </button>
                                <ul class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.lms.nilai.export-excel', [$kelas->id, $mapel->id, 'semester' => $semester]) }}" target="_blank">
                                            <i class="fas fa-download me-2"></i> Export Nilai
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('guru.lms.nilai.download-template', [$kelas->id, $mapel->id, 'semester' => $semester]) }}">
                                            <i class="fas fa-file-download me-2"></i> Download Template
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                                            <i class="fas fa-file-upload me-2"></i> Import Nilai
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-0">
                    <div class="px-3 pt-2">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Nilai desimal gunakan <strong>titik</strong>, contoh <code>9.8</code>. Jika mengetik koma (<code>9,8</code>) otomatis diubah menjadi titik.
                        </small>
                    </div>
                    <form action="{{ route('guru.lms.nilai.updateBatch', [$kelas->id, $mapel->id]) }}" method="POST" id="nilaiForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0 table-nilai">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center sticky-col col-no">No</th>
                                        <th rowspan="2" class="sticky-col-2 col-student">Nama Siswa</th>
                                        <th colspan="6" class="text-center th-tugas">Tugas</th>
                                        <th colspan="6" class="text-center th-latihan">Latihan</th>
                                        <th colspan="6" class="text-center th-uh">Ulangan Harian</th>
                                        <th rowspan="2" class="text-center col-score">PTS</th>
                                        <th rowspan="2" class="text-center col-score">PAS</th>
                                        <th rowspan="2" class="text-center nilai-akhir-cell col-final">N. Akhir</th>
                                    </tr>
                                    <tr>
                                        @for($i = 1; $i <= 5; $i++)
                                            <th class="text-center th-tugas col-score">T{{ $i }}</th>
                                        @endfor
                                        <th class="text-center th-tugas rata-cell col-score-sm">Rata</th>
                                        @for($i = 1; $i <= 5; $i++)
                                            <th class="text-center th-latihan col-score">L{{ $i }}</th>
                                        @endfor
                                        <th class="text-center th-latihan rata-cell col-score-sm">Rata</th>
                                        @for($i = 1; $i <= 5; $i++)
                                            <th class="text-center th-uh col-score">UH{{ $i }}</th>
                                        @endfor
                                        <th class="text-center th-uh rata-cell col-score-sm">Rata</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nilaiList as $index => $nilai)
                                        <tr>
                                            <td class="text-center fw-bold sticky-col">{{ $index + 1 }}</td>
                                            <td class="sticky-col-2">
                                                <div class="student-name">{{ $nilai->siswa->nama_lengkap ?? '-' }}</div>
                                                <small class="text-muted student-nis">{{ $nilai->siswa->nis ?? $nilai->siswa->nisn ?? '-' }}</small>
                                            </td>
                                            
                                            <input type="hidden" name="nilai[{{ $nilai->id }}][id]" value="{{ $nilai->id }}">
                                            
                                            {{-- Tugas 1-5 --}}
                                            @for($i = 1; $i <= 5; $i++)
                                                <td class="text-center p-1" data-label="T{{ $i }}">
                                                    <div class="input-wrapper">
                                                        <input type="text" maxlength="6" 
                                                            name="nilai[{{ $nilai->id }}][tugas_{{ $i }}]"
                                                            class="form-control input-nilai"
                                                            id="tugas_{{ $nilai->id }}_{{ $i }}"
                                                            value="{{ $nilai->{'tugas_'.$i} }}"
                                                            placeholder="-"
                                                            inputmode="decimal">
                                                        <div class="spinner-btns">
                                                            <button type="button" class="spinner-btn" data-adjust-target="tugas_{{ $nilai->id }}_{{ $i }}" data-adjust-delta="1">&uarr;</button>
                                                            <button type="button" class="spinner-btn" data-adjust-target="tugas_{{ $nilai->id }}_{{ $i }}" data-adjust-delta="-1">&darr;</button>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endfor
                                            <td class="text-center rata-cell" data-label="Rata Tugas" id="rata_tugas_{{ $nilai->id }}">
                                                {{ $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}
                                            </td>
                                            
                                            {{-- Latihan 1-5 --}}
                                            @for($i = 1; $i <= 5; $i++)
                                                <td class="text-center p-1" data-label="L{{ $i }}">
                                                    <div class="input-wrapper">
                                                        <input type="text" maxlength="6" 
                                                            name="nilai[{{ $nilai->id }}][latihan_{{ $i }}]"
                                                            class="form-control input-nilai"
                                                            id="latihan_{{ $nilai->id }}_{{ $i }}"
                                                            value="{{ $nilai->{'latihan_'.$i} }}"
                                                            placeholder="-"
                                                            inputmode="decimal">
                                                        <div class="spinner-btns">
                                                            <button type="button" class="spinner-btn" data-adjust-target="latihan_{{ $nilai->id }}_{{ $i }}" data-adjust-delta="1">&uarr;</button>
                                                            <button type="button" class="spinner-btn" data-adjust-target="latihan_{{ $nilai->id }}_{{ $i }}" data-adjust-delta="-1">&darr;</button>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endfor
                                            <td class="text-center rata-cell" data-label="Rata Latihan" id="rata_latihan_{{ $nilai->id }}">
                                                {{ $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}
                                            </td>
                                            
                                            {{-- UH 1-5 --}}
                                            @for($i = 1; $i <= 5; $i++)
                                                <td class="text-center p-1" data-label="UH{{ $i }}">
                                                    <div class="input-wrapper">
                                                        <input type="text" maxlength="6" 
                                                            name="nilai[{{ $nilai->id }}][uh_{{ $i }}]"
                                                            class="form-control input-nilai"
                                                            id="uh_{{ $nilai->id }}_{{ $i }}"
                                                            value="{{ $nilai->{'uh_'.$i} }}"
                                                            placeholder="-"
                                                            inputmode="decimal">
                                                        <div class="spinner-btns">
                                                            <button type="button" class="spinner-btn" data-adjust-target="uh_{{ $nilai->id }}_{{ $i }}" data-adjust-delta="1">&uarr;</button>
                                                            <button type="button" class="spinner-btn" data-adjust-target="uh_{{ $nilai->id }}_{{ $i }}" data-adjust-delta="-1">&darr;</button>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endfor
                                            <td class="text-center rata-cell" data-label="Rata UH" id="rata_uh_{{ $nilai->id }}">
                                                {{ $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}
                                            </td>
                                            
                                            {{-- PTS --}}
                                            <td class="text-center p-1" data-label="PTS">
                                                <div class="input-wrapper">
                                                    <input type="text" maxlength="6" 
                                                        name="nilai[{{ $nilai->id }}][pts]"
                                                        class="form-control input-nilai"
                                                        id="pts_{{ $nilai->id }}"
                                                        value="{{ $nilai->pts }}"
                                                        placeholder="-"
                                                        inputmode="decimal">
                                                    <div class="spinner-btns">
                                                        <button type="button" class="spinner-btn" data-adjust-target="pts_{{ $nilai->id }}" data-adjust-delta="1">&uarr;</button>
                                                        <button type="button" class="spinner-btn" data-adjust-target="pts_{{ $nilai->id }}" data-adjust-delta="-1">&darr;</button>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- PAS --}}
                                            <td class="text-center p-1" data-label="PAS">
                                                <div class="input-wrapper">
                                                    <input type="text" maxlength="6" 
                                                        name="nilai[{{ $nilai->id }}][pas]"
                                                        class="form-control input-nilai"
                                                        id="pas_{{ $nilai->id }}"
                                                        value="{{ $nilai->pas }}"
                                                        placeholder="-"
                                                        inputmode="decimal">
                                                    <div class="spinner-btns">
                                                        <button type="button" class="spinner-btn" data-adjust-target="pas_{{ $nilai->id }}" data-adjust-delta="1">&uarr;</button>
                                                        <button type="button" class="spinner-btn" data-adjust-target="pas_{{ $nilai->id }}" data-adjust-delta="-1">&darr;</button>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- Nilai Akhir --}}
                                            <td class="text-center nilai-akhir-cell" data-label="Nilai Akhir" id="nilai_akhir_{{ $nilai->id }}">
                                                {{ $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="21" class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($nilaiList->count() > 0)
                        <div class="p-3 text-end bg-light nilai-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Semua Nilai
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>

            {{-- Tingkat Akhir Section (Kelas 6 SD / 9 SMP / 12 SMA) --}}
            @if($isKelasAkhir)
            <div class="card-custom mt-4">
                <div class="card-header-custom">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-graduation-cap me-2"></i>Penilaian Tingkat Akhir (TO, UPK, Ujian Praktek)</h6>
                    <small class="text-muted">Khusus kelas tingkat akhir: kelas 6 SD, kelas 9 SMP, kelas 12 SMA</small>
                </div>
                <div class="p-0">
                    <form action="{{ route('guru.lms.nilai.updateBatch', [$kelas->id, $mapel->id]) }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0 table-nilai">
                                <thead>
                                    <tr>
                                        <th class="text-center col-score-sm">No</th>
                                        <th class="col-final-student">Nama Siswa</th>
                                        <th class="text-center th-latihan col-score-md">TO 1</th>
                                        <th class="text-center th-latihan col-score-md">TO 2</th>
                                        <th class="text-center th-latihan col-score-md">TO 3</th>
                                        <th class="text-center th-uh col-score-md">UPK</th>
                                        <th class="text-center th-uh col-score-lg">Ujian Praktek</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nilaiList as $index => $nilai)
                                        <tr>
                                            <td class="text-center fw-bold" data-label="No">{{ $index + 1 }}</td>
                                            <td class="student-cell">
                                                <div class="student-name">{{ $nilai->siswa->nama_lengkap ?? '-' }}</div>
                                                <small class="text-muted student-nis">{{ $nilai->siswa->nis ?? $nilai->siswa->nisn ?? '-' }}</small>
                                            </td>
                                            <input type="hidden" name="nilai[{{ $nilai->id }}][id]" value="{{ $nilai->id }}">
                                            
                                            {{-- Helper for generating cells --}}
                                            @foreach(['to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek'] as $field)
                                            <td class="text-center p-1" data-label="{{ str_replace('_', ' ', strtoupper($field)) }}">
                                                <div class="input-wrapper">
                                                    <input type="text" maxlength="6" 
                                                        name="nilai[{{ $nilai->id }}][{{ $field }}]"
                                                        class="form-control input-nilai"
                                                        id="{{ $field }}_{{ $nilai->id }}"
                                                        value="{{ $nilai->$field }}"
                                                        placeholder="-"
                                                        inputmode="decimal">
                                                    <div class="spinner-btns">
                                                        <button type="button" class="spinner-btn" data-adjust-target="{{ $field }}_{{ $nilai->id }}" data-adjust-delta="1">&uarr;</button>
                                                        <button type="button" class="spinner-btn" data-adjust-target="{{ $field }}_{{ $nilai->id }}" data-adjust-delta="-1">&darr;</button>
                                                    </div>
                                                </div>
                                            </td>
                                            @endforeach
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4 text-muted">Belum ada siswa di kelas ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($nilaiList->count() > 0)
                        <div class="p-3 text-end bg-light nilai-actions">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save me-1"></i> Simpan Nilai Tingkat Akhir
                            </button>
                        </div>
                        @endif
                    </form>
                </div>
            </div>
            @endif

            <div class="alert alert-info mt-3 border-0 d-flex align-items-center">
                <i class="fas fa-info-circle me-3 fa-2x"></i>
                <div>
                    <h6 class="fw-bold mb-1">Informasi Penilaian</h6>
                    <ul class="mb-0 small ps-3">
                        <li>Isi nilai <strong>Tugas 1-5</strong>, <strong>Latihan 1-5</strong>, dan <strong>UH 1-5</strong> secara parsial. Rata-rata dihitung otomatis saat disimpan.</li>
                        <li>Kolom kosong <strong>tidak dihitung sebagai 0</strong> dalam perhitungan rata-rata.</li>
                        <li>Nilai Akhir: <strong>((Rata Tugas x 1) + (Rata Latihan x 1) + (Rata UH x 2) + (PTS x 3) + (PAS x 3)) / 10</strong></li>
                        <li>Klik tombol <strong>"Simpan Semua Nilai"</strong> untuk menyimpan semua perubahan sekaligus.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <!-- Recalculate Confirmation Modal -->
    <div class="modal fade" id="recalculateModal" tabindex="-1" aria-labelledby="recalculateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recalculateModalLabel">Konfirmasi Hitung Ulang</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Hitung ulang semua nilai berdasarkan Tugas dan Ujian yang ada?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" data-submit-form="recalculateForm">Hitung Ulang</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Excel Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('guru.lms.nilai.import-excel', [$kelas->id, $mapel->id]) }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    <input type="hidden" name="semester" value="{{ $semester }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="importModalLabel">
                            <i class="fas fa-file-upload me-2"></i>Import Nilai dari Excel
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Panduan Import:</strong>
                            <ol class="mb-0 mt-2">
                                <li>Download template Excel terlebih dahulu</li>
                                <li>Isi nilai siswa pada kolom yang tersedia (0-100)</li>
                                <li><strong>Jangan mengubah</strong> kolom: No, Nama Siswa, dan NIS/NISN</li>
                                <li><strong>Kosongkan sel</strong> jika tidak ingin mengubah nilai yang sudah ada</li>
                                <li><strong>Isi dengan nilai baru</strong> untuk menimpa nilai yang sudah tersimpan</li>
                                <li>Upload file Excel yang sudah diisi</li>
                            </ol>
                        </div>

                        <div class="mb-3">
                            <label for="importFile" class="form-label fw-bold">
                                <i class="fas fa-file-excel me-1"></i>Pilih File Excel
                            </label>
                            <input type="file" class="form-control" id="importFile" name="file" accept=".xlsx,.xls" required>
                            <div class="form-text">Format: .xlsx atau .xls (Max: 5MB)</div>
                        </div>

                        <div class="alert alert-success mb-0">
                            <i class="fas fa-check-circle me-2"></i>
                            <small>
                                <strong>Smart Import:</strong>
                                <ul class="mb-0 mt-1">
                                    <li>Nilai yang <strong>diisi di Excel</strong> akan menimpa nilai lama</li>
                                    <li>Nilai yang <strong>kosong di Excel</strong> akan tetap menggunakan nilai lama (tidak tertimpa)</li>
                                    <li>Anda dapat mengimport hanya sebagian siswa tanpa khawatir menghapus nilai siswa lain</li>
                                </ul>
                            </small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i> Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="importBtn">
                            <i class="fas fa-upload me-1"></i> Import Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    </div>
@endsection
