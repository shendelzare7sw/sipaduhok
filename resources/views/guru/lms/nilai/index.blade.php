@extends('layouts.lms-guru')

@section('title', 'Rekap Nilai Siswa')
@section('page-title', 'Nilai Siswa')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
<style>
    .table-nilai {
        font-size: 13px;
    }
    .table-nilai th {
        background: #f8f9fc;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 11px;
        vertical-align: middle;
        white-space: nowrap;
    }
    
    /* Input wrapper dengan spinner di luar */
    .input-wrapper {
        display: flex;
        align-items: center;
        gap: 2px;
        justify-content: center;
    }

    /* Input field */
    .table-nilai .input-nilai {
        width: 55px !important;
        min-width: 55px;
        padding: 6px 4px;
        font-size: 13px;
        text-align: center;
        border-radius: 4px;
        border: 1px solid #ced4da;
        -moz-appearance: textfield;
    }
    .table-nilai .input-nilai:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 2px rgba(78,115,223,0.25);
        outline: none;
    }
    
    /* Hide Default Spinner */
    .table-nilai .input-nilai::-webkit-outer-spin-button,
    .table-nilai .input-nilai::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input.input-nilai[type="number"] {
        -moz-appearance: textfield;
    }
    
    /* Custom Spinner Buttons */
    .spinner-btns {
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    .spinner-btn {
        width: 16px;
        height: 14px;
        padding: 0;
        font-size: 9px;
        line-height: 1;
        border: 1px solid #ced4da;
        background: #f8f9fa;
        border-radius: 2px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
    }
    .spinner-btn:hover {
        background: #e9ecef;
        border-color: #4e73df;
        color: #4e73df;
    }
    .spinner-btn:active {
        background: #4e73df;
        color: white;
    }

    .table-nilai .rata-cell {
        background: #e9ecef;
        font-weight: 700;
        color: #165fac;
    }
    .table-nilai .nilai-akhir-cell {
        background: #d4edda;
        font-weight: 800;
        color: #155724;
    }
    .th-group {
        background: #4e73df !important;
        color: white !important;
    }
    .th-tugas { background: #e3f2fd !important; }
    .th-latihan { background: #fff3e0 !important; }
    .th-uh { background: #fce4ec !important; }
    
    /* Sticky columns - fixed properly */
    .table-nilai .sticky-col {
        position: sticky;
        left: 0;
        background: white;
        z-index: 3;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    .table-nilai .sticky-col-2 {
        position: sticky;
        left: 35px;
        background: white;
        z-index: 3;
        box-shadow: 2px 0 4px rgba(0,0,0,0.1);
    }
    .table-nilai thead th.sticky-col,
    .table-nilai thead th.sticky-col-2 {
        z-index: 4;
        background: #f8f9fc;
    }
    
    /* Student name - larger font */
    .student-name {
        font-size: 14px;
        font-weight: 600;
    }
    .student-nis {
        font-size: 11px;
    }

    @media (max-width: 767.98px) {
        .table-responsive {
            overflow-x: hidden;
            max-width: 100%;
        }

        .table-nilai,
        .table-nilai tbody,
        .table-nilai tr {
            display: block;
            width: auto;
        }

        .table-nilai {
            border: 0;
            background: transparent;
            max-width: 100%;
        }

        .table-nilai thead {
            display: none;
        }

        .table-nilai tbody {
            display: grid;
            gap: 12px;
        }

        .table-nilai tr {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 6px;
            padding: 12px;
            width: auto;
            max-width: 100%;
            box-sizing: border-box;
            overflow: hidden;
            border: 1px solid #dbe4f0;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        }

        .table-nilai td {
            display: flex;
            flex-direction: column;
            gap: 5px;
            width: auto;
            max-width: 100%;
            min-width: 0;
            box-sizing: border-box;
            border: 1px solid #e8eef7 !important;
            border-radius: 8px;
            padding: 7px 6px !important;
            background: #f8fafc;
            overflow: hidden;
        }

        .table-nilai tr > * {
            min-width: 0 !important;
        }

        .table-nilai td::before {
            content: attr(data-label);
            font-size: 10px;
            font-weight: 800;
            line-height: 1;
            color: #64748b;
            text-transform: uppercase;
            text-align: left;
        }

        .table-nilai .sticky-col,
        .table-nilai .sticky-col-2 {
            position: static;
            left: auto;
            box-shadow: none;
            z-index: auto;
        }

        .table-nilai td.sticky-col {
            display: none;
        }

        .table-nilai td[data-label="No"] {
            display: none;
        }

        .table-nilai td.sticky-col-2,
        .table-nilai td.student-cell {
            grid-column: 1 / -1;
            display: block;
            border: 0 !important;
            border-bottom: 1px solid #e2e8f0 !important;
            border-radius: 0;
            padding: 0 0 10px !important;
            background: #fff;
        }

        .table-nilai td.sticky-col-2::before,
        .table-nilai td.student-cell::before {
            display: none;
        }

        .student-name {
            font-size: 15px;
            line-height: 1.25;
        }

        .student-nis {
            font-size: 12px;
        }

        .input-wrapper {
            width: 100%;
            min-width: 0;
            justify-content: center;
            gap: 0;
        }

        .table-nilai .input-nilai {
            flex: 1 1 100%;
            width: 100% !important;
            min-width: 0;
            height: 34px;
            padding: 5px 4px;
            font-size: 12.5px;
            border-radius: 7px;
        }

        .spinner-btns {
            display: none;
        }

        .table-nilai .rata-cell,
        .table-nilai .nilai-akhir-cell {
            grid-column: 1 / -1;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            font-size: 15px;
        }

        .table-nilai .rata-cell::before,
        .table-nilai .nilai-akhir-cell::before {
            align-self: stretch;
        }

        .table-nilai .nilai-akhir-cell {
            background: #dcfce7;
        }

        .p-3.text-end.bg-light {
            position: sticky;
            bottom: 0;
            z-index: 5;
            display: flex;
            justify-content: stretch;
            background: rgba(255, 255, 255, 0.96) !important;
            backdrop-filter: blur(8px);
            border-top: 1px solid #e2e8f0;
        }

        .p-3.text-end.bg-light .btn {
            width: 100%;
        }
    }

    @media (max-width: 420px) {
        .table-nilai tr {
            gap: 4px;
            padding: 10px;
        }

        .table-nilai td {
            padding: 6px 4px !important;
        }

        .table-nilai td::before {
            font-size: 9px;
        }

        .table-nilai .input-nilai {
            height: 32px;
            padding: 4px 3px;
            font-size: 11.5px;
        }
    }
</style>
@endpush

@section('content')
@php
    $isKelasAkhir = $kelas->isTingkatAkhir();
@endphp
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
                                <select name="semester" class="form-select form-select-sm" style="width: auto; min-width: 130px;" onchange="this.form.submit()">
                                    <option value="ganjil" {{ $semester == 'ganjil' ? 'selected' : '' }}>Ganjil (Jul-Des)</option>
                                    <option value="genap" {{ $semester == 'genap' ? 'selected' : '' }}>Genap (Jan-Jun)</option>
                                </select>
                                @if($semester == $currentSemester)
                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Aktif</span>
                                @endif
                            </form>
                            
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                onclick="confirmRecalculate()">
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
                    <form action="{{ route('guru.lms.nilai.updateBatch', [$kelas->id, $mapel->id]) }}" method="POST" id="nilaiForm">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover align-middle mb-0 table-nilai">
                                <thead>
                                    <tr>
                                        <th rowspan="2" class="text-center sticky-col" style="width: 35px;">No</th>
                                        <th rowspan="2" class="sticky-col-2" style="min-width: 130px;">Nama Siswa</th>
                                        <th colspan="6" class="text-center th-tugas">Tugas</th>
                                        <th colspan="6" class="text-center th-latihan">Latihan</th>
                                        <th colspan="6" class="text-center th-uh">Ulangan Harian</th>
                                        <th rowspan="2" class="text-center" style="width: 60px;">PTS</th>
                                        <th rowspan="2" class="text-center" style="width: 60px;">PAS</th>
                                        <th rowspan="2" class="text-center nilai-akhir-cell" style="width: 65px;">N. Akhir</th>
                                    </tr>
                                    <tr>
                                        @for($i = 1; $i <= 5; $i++)
                                            <th class="text-center th-tugas" style="width: 60px;">T{{ $i }}</th>
                                        @endfor
                                        <th class="text-center th-tugas rata-cell" style="width: 50px;">Rata</th>
                                        @for($i = 1; $i <= 5; $i++)
                                            <th class="text-center th-latihan" style="width: 60px;">L{{ $i }}</th>
                                        @endfor
                                        <th class="text-center th-latihan rata-cell" style="width: 50px;">Rata</th>
                                        @for($i = 1; $i <= 5; $i++)
                                            <th class="text-center th-uh" style="width: 60px;">UH{{ $i }}</th>
                                        @endfor
                                        <th class="text-center th-uh rata-cell" style="width: 50px;">Rata</th>
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
                                                        <input type="number" step="0.01" min="0" max="100" 
                                                            name="nilai[{{ $nilai->id }}][tugas_{{ $i }}]"
                                                            class="form-control input-nilai"
                                                            id="tugas_{{ $nilai->id }}_{{ $i }}"
                                                            value="{{ $nilai->{'tugas_'.$i} }}"
                                                            placeholder="-"
                                                            inputmode="decimal">
                                                        <div class="spinner-btns">
                                                            <button type="button" class="spinner-btn" onclick="adjustValue('tugas_{{ $nilai->id }}_{{ $i }}', 1)">▲</button>
                                                            <button type="button" class="spinner-btn" onclick="adjustValue('tugas_{{ $nilai->id }}_{{ $i }}', -1)">▼</button>
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
                                                        <input type="number" step="0.01" min="0" max="100" 
                                                            name="nilai[{{ $nilai->id }}][latihan_{{ $i }}]"
                                                            class="form-control input-nilai"
                                                            id="latihan_{{ $nilai->id }}_{{ $i }}"
                                                            value="{{ $nilai->{'latihan_'.$i} }}"
                                                            placeholder="-"
                                                            inputmode="decimal">
                                                        <div class="spinner-btns">
                                                            <button type="button" class="spinner-btn" onclick="adjustValue('latihan_{{ $nilai->id }}_{{ $i }}', 1)">▲</button>
                                                            <button type="button" class="spinner-btn" onclick="adjustValue('latihan_{{ $nilai->id }}_{{ $i }}', -1)">▼</button>
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
                                                        <input type="number" step="0.01" min="0" max="100" 
                                                            name="nilai[{{ $nilai->id }}][uh_{{ $i }}]"
                                                            class="form-control input-nilai"
                                                            id="uh_{{ $nilai->id }}_{{ $i }}"
                                                            value="{{ $nilai->{'uh_'.$i} }}"
                                                            placeholder="-"
                                                            inputmode="decimal">
                                                        <div class="spinner-btns">
                                                            <button type="button" class="spinner-btn" onclick="adjustValue('uh_{{ $nilai->id }}_{{ $i }}', 1)">▲</button>
                                                            <button type="button" class="spinner-btn" onclick="adjustValue('uh_{{ $nilai->id }}_{{ $i }}', -1)">▼</button>
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
                                                    <input type="number" step="0.01" min="0" max="100" 
                                                        name="nilai[{{ $nilai->id }}][pts]"
                                                        class="form-control input-nilai"
                                                        id="pts_{{ $nilai->id }}"
                                                        value="{{ $nilai->pts }}"
                                                        placeholder="-"
                                                        inputmode="decimal">
                                                    <div class="spinner-btns">
                                                        <button type="button" class="spinner-btn" onclick="adjustValue('pts_{{ $nilai->id }}', 1)">▲</button>
                                                        <button type="button" class="spinner-btn" onclick="adjustValue('pts_{{ $nilai->id }}', -1)">▼</button>
                                                    </div>
                                                </div>
                                            </td>
                                            
                                            {{-- PAS --}}
                                            <td class="text-center p-1" data-label="PAS">
                                                <div class="input-wrapper">
                                                    <input type="number" step="0.01" min="0" max="100" 
                                                        name="nilai[{{ $nilai->id }}][pas]"
                                                        class="form-control input-nilai"
                                                        id="pas_{{ $nilai->id }}"
                                                        value="{{ $nilai->pas }}"
                                                        placeholder="-"
                                                        inputmode="decimal">
                                                    <div class="spinner-btns">
                                                        <button type="button" class="spinner-btn" onclick="adjustValue('pas_{{ $nilai->id }}', 1)">▲</button>
                                                        <button type="button" class="spinner-btn" onclick="adjustValue('pas_{{ $nilai->id }}', -1)">▼</button>
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
                        <div class="p-3 text-end bg-light">
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
                                        <th class="text-center" style="width: 50px;">No</th>
                                        <th style="min-width: 200px;">Nama Siswa</th>
                                        <th class="text-center th-latihan" style="width: 70px;">TO 1</th>
                                        <th class="text-center th-latihan" style="width: 70px;">TO 2</th>
                                        <th class="text-center th-latihan" style="width: 70px;">TO 3</th>
                                        <th class="text-center th-uh" style="width: 70px;">UPK</th>
                                        <th class="text-center th-uh" style="width: 90px;">Ujian Praktek</th>
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
                                                    <input type="number" step="0.01" min="0" max="100" 
                                                        name="nilai[{{ $nilai->id }}][{{ $field }}]"
                                                        class="form-control input-nilai"
                                                        id="{{ $field }}_{{ $nilai->id }}"
                                                        value="{{ $nilai->$field }}"
                                                        placeholder="-"
                                                        inputmode="decimal">
                                                    <div class="spinner-btns">
                                                        <button type="button" class="spinner-btn" onclick="adjustValue('{{ $field }}_{{ $nilai->id }}', 1)">▲</button>
                                                        <button type="button" class="spinner-btn" onclick="adjustValue('{{ $field }}_{{ $nilai->id }}', -1)">▼</button>
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
                        <div class="p-3 text-end bg-light">
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
                        <li>Nilai Akhir: <strong>((Rata Tugas × 1) + (Rata Latihan × 1) + (Rata UH × 2) + (PTS × 3) + (PAS × 3)) / 10</strong></li>
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
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('recalculateForm').submit()">Hitung Ulang</button>
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
@endsection

@push('scripts')
<script>
function adjustValue(inputId, delta) {
    const input = document.getElementById(inputId);
    if (!input) return;
    
    let currentValue = parseFloat(input.value) || 0;
    let newValue = currentValue + delta;
    
    // Clamp between 0 and 100
    newValue = Math.max(0, Math.min(100, newValue));
    
    // Limit to 2 decimals
    newValue = Math.round(newValue * 100) / 100;
    
    input.value = newValue;
    // Trigger input event to ensure any listeners pick it up
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.input-nilai');
    
    inputs.forEach(input => {
        // Validation and Auto-Decimal on Input
        input.addEventListener('input', function(e) {
            let value = this.value;
            
            if (value === '') return;
            
            let num = parseFloat(value);
            
            if (num > 100) {
                // Auto-Decimal Logic:
                // User input > 100 (e.g., 675). Try to convert to 67.5
                let strVal = value.toString();
                
                // If likely appending a digit to an integer
                if (!strVal.includes('.')) {
                    // Try inserting dot before the last digit
                    let corrected = strVal.slice(0, -1) + '.' + strVal.slice(-1);
                    
                    if (parseFloat(corrected) <= 100) {
                        this.value = corrected;
                        return; // Successfully auto-corrected
                    }
                } 
                // Case: 67.5 -> user types 4 -> 67.54 (OK)
                // Case: 100 -> user types 5 -> 1005 -> 100.5 (Revert to 100)
                
                // Fallback: Clamp to 100 strict
                this.value = 100;
            }
            
            // Limit decimal places to 2 if needed (browser often handles this with step, but safe to force?)
            // If user types 67.543, standard step 0.01 might invalidate it or truncate.
            // Let's leave strict decimal limiting to step attribute validaton or blur.
        });
        
        // Prevent typing non-numeric keys that might bypass number type (like 'e')
        input.addEventListener('keydown', function(e) {
            if (['e', 'E', '-', '+'].includes(e.key)) {
                e.preventDefault();
            }
        });
        
        // On Blur: Ensure cleanly formatted (optional)
        input.addEventListener('blur', function() {
            let val = parseFloat(this.value);
            if (!isNaN(val)) {
                if (val > 100) this.value = 100;
                if (val < 0) this.value = 0;
            }
        });
    });
});
    function confirmRecalculate() {
        var modal = new bootstrap.Modal(document.getElementById('recalculateModal'));
        modal.show();
    }

    // Import Form Validation
    document.getElementById('importForm')?.addEventListener('submit', function(e) {
        const fileInput = document.getElementById('importFile');
        const file = fileInput.files[0];

        if (!file) {
            e.preventDefault();
            alert('Pilih file Excel terlebih dahulu!');
            return;
        }

        // Validate file extension
        const allowedExtensions = /(\.xlsx|\.xls)$/i;
        if (!allowedExtensions.exec(file.name)) {
            e.preventDefault();
            alert('File harus berformat .xlsx atau .xls');
            fileInput.value = '';
            return;
        }

        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            e.preventDefault();
            alert('Ukuran file maksimal 5MB');
            fileInput.value = '';
            return;
        }

        // Show loading state
        const importBtn = document.getElementById('importBtn');
        importBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Mengimport...';
        importBtn.disabled = true;
    });

    // Reset form when modal is closed
    document.getElementById('importModal')?.addEventListener('hidden.bs.modal', function() {
        document.getElementById('importForm').reset();
        const importBtn = document.getElementById('importBtn');
        importBtn.innerHTML = '<i class="fas fa-upload me-1"></i> Import Nilai';
        importBtn.disabled = false;
    });
</script>
@endpush
