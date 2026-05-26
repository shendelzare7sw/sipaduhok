@extends('layouts.sneat')

@section('title', 'Edit Nilai Siswa')
@section('page-title', 'Edit Nilai Siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
@include('shared.wali-kelas.styles')
<style>
/* ─── Table base ─────────────────────────────────────── */
.table-nilai {
    font-size: 13px;
    border-collapse: separate;
    border-spacing: 0;
}
.table-nilai th {
    font-weight: 700;
    font-size: 11.5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    vertical-align: middle;
    white-space: nowrap;
    background: #f8f9fc;
}
.table-nilai td {
    vertical-align: middle;
    padding: 6px 4px;
}

.sticky-no {
    min-width: 36px;
}
.sticky-mapel {
    min-width: 190px;
    max-width: 220px;
}
.mapel-cell-header {
    min-width: 0;
}
.mapel-name {
    display: -webkit-box;
    overflow: hidden;
    font-size: 13.25px;
    line-height: 1.25;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}
.mapel-code {
    font-size: 11px;
}
.mapel-status-badge {
    font-size: 9px;
    padding: 3px 5px;
}
.mapel-action-btn {
    width: 30px !important;
    height: 30px !important;
    font-size: 17px !important;
    border-radius: 8px;
}
.mapel-action-btn .bx {
    font-size: 17px;
    line-height: 1;
}
.mapel-action-label {
    display: none;
}

/* ─── Group header colours ───────────────────────────── */
.th-tugas   { background: #e3f2fd !important; }
.th-latihan { background: #fff3e0 !important; }
.th-uh      { background: #fce4ec !important; }
.th-akhir   { background: #e8f5e9 !important; }

/* ─── Rata / Nilai Akhir cells ───────────────────────── */
.rata-cell {
    background: #e9ecef;
    font-weight: 700;
    color: #165fac;
    text-align: center;
    min-width: 50px;
}
.nilai-akhir-cell {
    background: #d4edda;
    font-weight: 800;
    color: #155724;
    text-align: center;
    min-width: 68px;
}

/* ─── Input + spinner ────────────────────────────────── */
.input-wrapper {
    display: flex;
    align-items: center;
    gap: 3px;
    justify-content: center;
}
.input-nilai {
    width: 56px;
    min-width: 56px;
    padding: 6px 4px;
    font-size: 13px;
    text-align: center;
    border-radius: 8px;
    border: 1px solid #ced4da;
    -moz-appearance: textfield;
}
.input-nilai::-webkit-outer-spin-button,
.input-nilai::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
.input-nilai:focus {
    border-color: #4e73df;
    box-shadow: 0 0 0 2px rgba(78,115,223,.2);
    outline: none;
}
.spinner-btns { display: flex; flex-direction: column; gap: 1px; }
.spinner-btn {
    width: 17px; height: 15px;
    padding: 0; font-size: 9px; line-height: 1;
    border: 1px solid #ced4da; background: #f8f9fa;
    border-radius: 2px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    color: #666;
}
.spinner-btn:hover { background: #e9ecef; border-color: #4e73df; color: #4e73df; }
.spinner-btn:active { background: #4e73df; color: white; }

/* ─── Scroll wrapper ─────────────────────────────────── */
.scroll-wrapper {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    position: relative; /* needed so sticky is relative to this container */
}

/* ─── Mobile: shrink mapel column ───────────────────── */
@media (max-width: 991.98px) {
    .sticky-mapel { min-width: 180px; max-width: 210px; }
    .mapel-action-btn { width: 32px !important; height: 32px !important; font-size: 18px !important; }
}

/* Mobile: turn the dense score table into per-subject input cards. */
@media (max-width: 767.98px) {
    .scroll-wrapper {
        overflow-x: hidden;
        width: 100%;
        max-width: 100%;
    }

    .table-nilai,
    .table-nilai tbody,
    .table-nilai tr {
        display: block;
        width: 100% !important;
        min-width: 0 !important;
    }

    .table-nilai {
        border: 0;
        background: transparent;
        max-width: 100% !important;
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
        grid-template-columns: repeat(5, minmax(0, 1fr)) !important;
        gap: 6px;
        padding: 12px;
        width: 100% !important;
        max-width: 100% !important;
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
        width: auto !important;
        max-width: 100%;
        min-width: 0 !important;
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
    }

    .table-nilai td.sticky-no {
        display: none;
    }

    .table-nilai td.sticky-mapel {
        grid-column: 1 / -1;
        display: block;
        width: 100% !important;
        min-width: 0 !important;
        max-width: none !important;
        border: 0 !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-radius: 0;
        padding: 0 0 10px !important;
        background: #fff;
    }

    .table-nilai td.sticky-mapel::before {
        display: none;
    }

    .mapel-cell-header {
        align-items: center !important;
        flex-wrap: wrap;
        gap: 10px !important;
    }

    .mapel-name {
        font-size: 15px !important;
    }

    .mapel-code {
        font-size: 12px !important;
    }

    .mapel-actions {
        display: grid !important;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        width: 100%;
        gap: 8px !important;
    }

    .mapel-action-btn {
        width: 100% !important;
        height: 36px !important;
        gap: 5px;
        font-size: 18px !important;
        border-radius: 10px;
    }

    .mapel-action-btn .bx {
        font-size: 18px;
    }

    .mapel-action-label {
        display: inline;
        font-size: 11px;
        font-weight: 700;
    }

    .input-wrapper {
        width: 100%;
        min-width: 0;
        justify-content: center;
        gap: 0;
    }

    .input-nilai {
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

    .rata-cell,
    .nilai-akhir-cell {
        grid-column: 1 / -1;
        align-items: center;
        justify-content: center;
        min-height: 46px;
        font-size: 15px;
    }

    .rata-cell::before,
    .nilai-akhir-cell::before {
        align-self: stretch;
        text-align: left;
    }
    .card-footer {
        position: sticky;
        bottom: 0;
        z-index: 5;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(8px);
        border-top: 1px solid #e2e8f0;
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

    .input-nilai {
        height: 32px;
        padding: 4px 3px;
        font-size: 11.5px;
    }
}
</style>
@endsection

@section('content')
@php
    $isKelasAkhir = $kelas->isTingkatAkhir();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">

    {{-- ── Top bar: back + import buttons ── --}}
    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center mb-3 no-print">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('wali.nilai.index') }}?semester={{ $semester }}" class="btn btn-light btn-sm border fw-semibold">
                <i class="bx bx-arrow-back me-1"></i> Kembali ke Daftar Nilai
            </a>
            <span class="badge {{ $semester === 'ganjil' ? 'bg-label-warning' : 'bg-label-info' }} fw-semibold px-3 py-2" style="font-size:12px;">
                <i class="bx bx-calendar me-1"></i> Semester {{ ucfirst($semester) }}
                @if($semester === $currentSemester) <span class="ms-1 text-success">(Aktif)</span> @endif
            </span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('wali.nilai.download-template', $siswa->id) }}?semester={{ $semester }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="bx bx-download me-1"></i> Template Excel
            </a>
            <button type="button" class="btn btn-outline-success btn-sm"
                    data-bs-toggle="modal" data-bs-target="#importModal">
                <i class="bx bx-import me-1"></i> Import Nilai
            </button>
        </div>
    </div>

    {{-- ── Student info card ── --}}
    <div class="card mb-3 border-start border-primary border-4">
        <div class="card-body py-3">
            <h5 class="fw-bold mb-2">Edit Nilai — {{ $siswa->nama_lengkap }}</h5>
            <div class="row g-2">
                <div class="col-6 col-sm-3">
                    <div class="small text-muted fw-semibold text-uppercase">NIS</div>
                    <div class="fw-bold">{{ $siswa->nis }}</div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="small text-muted fw-semibold text-uppercase">NISN</div>
                    <div class="fw-bold">{{ $siswa->nisn }}</div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="small text-muted fw-semibold text-uppercase">Kelas</div>
                    <div class="fw-bold">{{ $kelas->nama_kelas }}</div>
                </div>
                <div class="col-6 col-sm-3">
                    <div class="small text-muted fw-semibold text-uppercase">Tahun Ajaran</div>
                    <div class="fw-bold">{{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Validation errors ── --}}
    @if ($errors->any())
        <div class="alert alert-danger border-start border-danger border-4 mb-3">
            <h6 class="fw-bold mb-1"><i class="bx bx-error-circle me-1"></i>Ada kesalahan:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show mb-3">
            <i class="bx bx-error me-1"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            <i class="bx bx-x-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $countGuruUpdate = $nilaiData->filter(function ($n) {
            return $n->hasGuruUpdate()
                && $n->guru_terakhir_simpan_at
                && (!$n->wali_terakhir_edit_at || $n->guru_terakhir_simpan_at->gt($n->wali_terakhir_edit_at));
        })->count();
    @endphp
    @if($countGuruUpdate > 0)
        <div class="alert alert-warning border-start border-warning border-4 mb-3">
            <div class="d-flex align-items-start">
                <i class="bx bx-bell fs-3 me-3 text-warning"></i>
                <div>
                    <h6 class="fw-bold mb-1">{{ $countGuruUpdate }} mata pelajaran punya nilai baru dari guru</h6>
                    <p class="small mb-0 text-muted">Klik <strong>Preview vs Guru</strong> pada baris mapel untuk lihat perbedaan, lalu <strong>Sinkronisasi</strong> jika ingin pakai nilai guru. Edit Anda tidak akan ditimpa otomatis.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Main form ── --}}
    <form action="{{ route('wali.nilai.update', $siswa->id) }}" method="POST" id="nilaiForm">
        @csrf
        @method('PUT')
        <input type="hidden" name="semester" value="{{ $semester }}">

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <span class="fw-semibold">Daftar Nilai Mata Pelajaran</span>
                <span class="badge bg-label-primary">{{ $mataPelajaranList->count() }} Mapel</span>
            </div>

            @if($mataPelajaranList->isEmpty())
                <div class="card-body text-center py-5">
                    <i class="bx bx-book-open fs-1 text-muted"></i>
                    <p class="mt-2 text-muted">Belum ada mata pelajaran yang terdaftar untuk kelas ini.</p>
                </div>
            @else
                <div class="scroll-wrapper">
                    <table class="table table-sm table-bordered table-nilai mb-0">
                        <thead>
                            <tr>
                                <th class="sticky-no text-center" rowspan="2">#</th>
                                <th class="sticky-mapel" rowspan="2">Mata Pelajaran</th>
                                <th colspan="6" class="text-center th-tugas">Tugas</th>
                                <th colspan="6" class="text-center th-latihan">Latihan</th>
                                <th colspan="6" class="text-center th-uh">Ulangan Harian</th>
                                <th class="text-center" rowspan="2" style="min-width:80px;">PTS</th>
                                <th class="text-center" rowspan="2" style="min-width:80px;">PAS</th>
                                <th class="text-center nilai-akhir-cell" rowspan="2" style="min-width:72px;">Nilai Akhir</th>
                                @if($isKelasAkhir)
                                <th colspan="5" class="text-center th-akhir">Tingkat Akhir</th>
                                @endif
                            </tr>
                            <tr>
                                {{-- Tugas --}}
                                @for($i=1;$i<=5;$i++)
                                <th class="text-center th-tugas" style="min-width:80px;">T{{ $i }}</th>
                                @endfor
                                <th class="text-center th-tugas rata-cell">Rata</th>
                                {{-- Latihan --}}
                                @for($i=1;$i<=5;$i++)
                                <th class="text-center th-latihan" style="min-width:62px;">L{{ $i }}</th>
                                @endfor
                                <th class="text-center th-latihan rata-cell">Rata</th>
                                {{-- UH --}}
                                @for($i=1;$i<=5;$i++)
                                <th class="text-center th-uh" style="min-width:62px;">UH{{ $i }}</th>
                                @endfor
                                <th class="text-center th-uh rata-cell">Rata</th>
                                {{-- Tingkat Akhir sub-headers --}}
                                @if($isKelasAkhir)
                                <th class="text-center th-akhir" style="min-width:62px;">TO 1</th>
                                <th class="text-center th-akhir" style="min-width:62px;">TO 2</th>
                                <th class="text-center th-akhir" style="min-width:62px;">TO 3</th>
                                <th class="text-center th-akhir" style="min-width:62px;">UPK</th>
                                <th class="text-center th-akhir" style="min-width:80px;">Ujian Praktek</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mataPelajaranList as $idx => $mapel)
                            @php $nilai = $nilaiData[$mapel->id] ?? null; @endphp
                            <tr>
                                {{-- No --}}
                                <td class="sticky-no text-center text-muted">{{ $loop->iteration }}</td>

                                {{-- Mata Pelajaran --}}
                                <td class="sticky-mapel">
                                    <input type="hidden" name="nilai[{{ $mapel->id }}][mata_pelajaran_id]" value="{{ $mapel->id }}">
                                    @php
                                        $hasDiff = $nilai && $nilai->hasGuruUpdate();
                                        $guruBaru = $hasDiff
                                            && $nilai->guru_terakhir_simpan_at
                                            && (!$nilai->wali_terakhir_edit_at
                                                || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
                                        $waliEditDiff = $hasDiff && $nilai->wali_terakhir_edit_at && !$guruBaru;
                                    @endphp
                                    <div class="mapel-cell-header d-flex align-items-start justify-content-between gap-2">
                                        <div class="flex-grow-1" style="min-width:0;">
                                            <div class="mapel-name fw-semibold lh-sm">{{ $mapel->nama_mapel }}</div>
                                            <small class="mapel-code text-muted">{{ $mapel->kode_mapel }}</small>
                                            @if($guruBaru)
                                                <div class="mt-1">
                                                    <span class="badge bg-warning text-dark mapel-status-badge">
                                                        <i class="bx bx-bell"></i> Guru update
                                                    </span>
                                                </div>
                                            @elseif($waliEditDiff)
                                                <div class="mt-1">
                                                    <span class="badge bg-info text-white mapel-status-badge">
                                                        <i class="bx bx-user-check"></i> Edit wali
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        @if($nilai)
                                            <div class="mapel-actions d-flex flex-column gap-1 flex-shrink-0">
                                                <button type="button"
                                                        class="btn btn-outline-info p-0 d-flex align-items-center justify-content-center mapel-action-btn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#previewDiffModal{{ $nilai->id }}"
                                                        title="Preview vs nilai guru">
                                                    <i class="bx bx-show"></i>
                                                    <span class="mapel-action-label">Preview</span>
                                                </button>
                                                <button type="button"
                                                        class="btn {{ $guruBaru ? 'btn-warning' : 'btn-outline-secondary' }} p-0 d-flex align-items-center justify-content-center mapel-action-btn"
                                                        style="{{ !$guruBaru ? 'opacity:0.55; cursor:not-allowed;' : '' }}"
                                                        @if($guruBaru) data-bs-toggle="modal" data-bs-target="#syncGuruModal{{ $nilai->id }}" @else disabled @endif
                                                        title="{{ $guruBaru ? 'Sinkronisasi dari guru' : 'Tidak ada update guru baru — tidak perlu sinkronisasi' }}">
                                                    <i class="bx bx-sync"></i>
                                                    <span class="mapel-action-label">Sinkron</span>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                </td>

                                {{-- Tugas 1-5 --}}
                                @for($j=1;$j<=5;$j++)
                                <td class="p-1" data-label="T{{ $j }}">
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $mapel->id }}][tugas_{{ $j }}]"
                                            class="form-control input-nilai tugas-input-{{ $mapel->id }}"
                                            id="tugas_{{ $mapel->id }}_{{ $j }}"
                                            value="{{ $nilai && $nilai->{'tugas_'.$j} !== null ? $nilai->{'tugas_'.$j} : '' }}"
                                            placeholder="-">
                                        <div class="spinner-btns">
                                            <button type="button" class="spinner-btn" onclick="adjustValue('tugas_{{ $mapel->id }}_{{ $j }}', 1)">▲</button>
                                            <button type="button" class="spinner-btn" onclick="adjustValue('tugas_{{ $mapel->id }}_{{ $j }}', -1)">▼</button>
                                        </div>
                                    </div>
                                </td>
                                @endfor
                                <td class="rata-cell" data-label="Rata Tugas" id="rata_tugas_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}
                                </td>

                                {{-- Latihan 1-5 --}}
                                @for($j=1;$j<=5;$j++)
                                <td class="p-1" data-label="L{{ $j }}">
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $mapel->id }}][latihan_{{ $j }}]"
                                            class="form-control input-nilai latihan-input-{{ $mapel->id }}"
                                            id="latihan_{{ $mapel->id }}_{{ $j }}"
                                            value="{{ $nilai && $nilai->{'latihan_'.$j} !== null ? $nilai->{'latihan_'.$j} : '' }}"
                                            placeholder="-">
                                        <div class="spinner-btns">
                                            <button type="button" class="spinner-btn" onclick="adjustValue('latihan_{{ $mapel->id }}_{{ $j }}', 1)">▲</button>
                                            <button type="button" class="spinner-btn" onclick="adjustValue('latihan_{{ $mapel->id }}_{{ $j }}', -1)">▼</button>
                                        </div>
                                    </div>
                                </td>
                                @endfor
                                <td class="rata-cell" data-label="Rata Latihan" id="rata_latihan_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}
                                </td>

                                {{-- UH 1-5 --}}
                                @for($j=1;$j<=5;$j++)
                                <td class="p-1" data-label="UH{{ $j }}">
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $mapel->id }}][uh_{{ $j }}]"
                                            class="form-control input-nilai uh-input-{{ $mapel->id }}"
                                            id="uh_{{ $mapel->id }}_{{ $j }}"
                                            value="{{ $nilai && $nilai->{'uh_'.$j} !== null ? $nilai->{'uh_'.$j} : '' }}"
                                            placeholder="-">
                                        <div class="spinner-btns">
                                            <button type="button" class="spinner-btn" onclick="adjustValue('uh_{{ $mapel->id }}_{{ $j }}', 1)">▲</button>
                                            <button type="button" class="spinner-btn" onclick="adjustValue('uh_{{ $mapel->id }}_{{ $j }}', -1)">▼</button>
                                        </div>
                                    </div>
                                </td>
                                @endfor
                                <td class="rata-cell" data-label="Rata UH" id="rata_uh_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}
                                </td>

                                {{-- PTS --}}
                                <td class="p-1" data-label="PTS">
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $mapel->id }}][pts]"
                                            class="form-control input-nilai pts-input-{{ $mapel->id }}"
                                            id="pts_{{ $mapel->id }}"
                                            value="{{ $nilai && $nilai->pts !== null ? $nilai->pts : '' }}"
                                            placeholder="-">
                                        <div class="spinner-btns">
                                            <button type="button" class="spinner-btn" onclick="adjustValue('pts_{{ $mapel->id }}', 1)">▲</button>
                                            <button type="button" class="spinner-btn" onclick="adjustValue('pts_{{ $mapel->id }}', -1)">▼</button>
                                        </div>
                                    </div>
                                </td>

                                {{-- PAS --}}
                                <td class="p-1" data-label="PAS">
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $mapel->id }}][pas]"
                                            class="form-control input-nilai pas-input-{{ $mapel->id }}"
                                            id="pas_{{ $mapel->id }}"
                                            value="{{ $nilai && $nilai->pas !== null ? $nilai->pas : '' }}"
                                            placeholder="-">
                                        <div class="spinner-btns">
                                            <button type="button" class="spinner-btn" onclick="adjustValue('pas_{{ $mapel->id }}', 1)">▲</button>
                                            <button type="button" class="spinner-btn" onclick="adjustValue('pas_{{ $mapel->id }}', -1)">▼</button>
                                        </div>
                                    </div>
                                </td>

                                {{-- Nilai Akhir --}}
                                <td class="nilai-akhir-cell" data-label="Nilai Akhir" id="nilai_akhir_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}
                                </td>

                                {{-- Tingkat Akhir (kelas 6 SD, 9 SMP, 12 SMA only) --}}
                                @if($isKelasAkhir)
                                @foreach(['to_1'=>'TO 1','to_2'=>'TO 2','to_3'=>'TO 3','upk'=>'UPK','ujian_praktek'=>'Ujian Praktek'] as $field => $label)
                                <td class="p-1" data-label="{{ $label }}">
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" max="100"
                                            name="nilai[{{ $mapel->id }}][{{ $field }}]"
                                            class="form-control input-nilai"
                                            id="{{ $field }}_{{ $mapel->id }}"
                                            value="{{ $nilai && $nilai->$field !== null ? $nilai->$field : '' }}"
                                            placeholder="-">
                                        <div class="spinner-btns">
                                            <button type="button" class="spinner-btn" onclick="adjustValue('{{ $field }}_{{ $mapel->id }}', 1)">▲</button>
                                            <button type="button" class="spinner-btn" onclick="adjustValue('{{ $field }}_{{ $mapel->id }}', -1)">▼</button>
                                        </div>
                                    </div>
                                </td>
                                @endforeach
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Info box --}}
                <div class="px-3 pt-3 pb-0">
                    <div class="alert alert-info border-0 mb-3 py-2 small">
                        <i class="bx bx-info-circle me-1"></i>
                        Kolom kosong <strong>tidak dihitung sebagai 0</strong>.
                        Nilai Akhir = <strong>((Rata Tugas×1) + (Rata Latihan×1) + (Rata UH×2) + (PTS×3) + (PAS×3)) / 10</strong>.
                        Nilai valid: <strong>0–100</strong>.
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="card-footer d-flex flex-wrap gap-2 justify-content-end">
                    <a href="{{ route('wali.nilai.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bx bx-x me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary btn-sm px-4 fw-semibold">
                        <i class="bx bx-save me-1"></i> Simpan Semua Perubahan
                    </button>
                </div>
            @endif
        </div>
    </form>

    {{-- ── Modal Preview Diff & Sync per Mapel ── --}}
    @foreach($mataPelajaranList as $mapel)
        @php $nilai = $nilaiData[$mapel->id] ?? null; @endphp
        @if($nilai)
            @php
                $diff = $nilai->diffWithGuru();
                $labelMap = [
                    'tugas_1' => 'Tugas 1', 'tugas_2' => 'Tugas 2', 'tugas_3' => 'Tugas 3', 'tugas_4' => 'Tugas 4', 'tugas_5' => 'Tugas 5',
                    'latihan_1' => 'Latihan 1', 'latihan_2' => 'Latihan 2', 'latihan_3' => 'Latihan 3', 'latihan_4' => 'Latihan 4', 'latihan_5' => 'Latihan 5',
                    'uh_1' => 'UH 1', 'uh_2' => 'UH 2', 'uh_3' => 'UH 3', 'uh_4' => 'UH 4', 'uh_5' => 'UH 5',
                    'pts' => 'PTS', 'pas' => 'PAS', 'keterampilan' => 'Keterampilan',
                    'to_1' => 'TO 1', 'to_2' => 'TO 2', 'to_3' => 'TO 3', 'upk' => 'UPK', 'ujian_praktek' => 'Ujian Praktek',
                ];
            @endphp
            {{-- Preview Modal --}}
            <div class="modal fade" id="previewDiffModal{{ $nilai->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-info" style="color:#fff !important;">
                            <h5 class="modal-title fw-bold" style="color:#fff !important;">
                                <i class="bx bx-show me-2" style="color:#fff !important;"></i>Preview Nilai: {{ $mapel->nama_mapel }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-4">
                            <p class="text-muted small mb-3">
                                Bandingkan nilai yang sekarang tersimpan (kolom utama, bisa hasil edit Anda) dengan snapshot nilai terakhir dari guru pengajar.
                            </p>
                            @if(empty($diff))
                                <div class="alert alert-success mb-3">
                                    <i class="bx bx-check-circle"></i> <strong>Identik dengan nilai guru.</strong>
                                    Tidak ada perbedaan antara nilai Anda dan snapshot terakhir dari guru pengajar.
                                </div>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Komponen</th>
                                                <th class="text-center">Nilai Guru</th>
                                                <th class="text-center">Nilai Saat Ini</th>
                                                <th class="text-center">Selisih</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($diff as $field => $values)
                                                @php
                                                    $guruVal = $values['guru'];
                                                    $curVal = $values['current'];
                                                    $selisih = ($guruVal !== null && $curVal !== null) ? (float)$curVal - (float)$guruVal : null;
                                                @endphp
                                                <tr>
                                                    <td class="fw-semibold">{{ $labelMap[$field] ?? $field }}</td>
                                                    <td class="text-center">{{ $guruVal !== null ? number_format($guruVal, 2) : '—' }}</td>
                                                    <td class="text-center fw-bold {{ $selisih === null ? '' : ($selisih > 0 ? 'text-success' : 'text-danger') }}">
                                                        {{ $curVal !== null ? number_format($curVal, 2) : '—' }}
                                                    </td>
                                                    <td class="text-center small">
                                                        @if($selisih === null)
                                                            <span class="text-muted">—</span>
                                                        @elseif($selisih > 0)
                                                            <span class="text-success">+{{ number_format($selisih, 2) }}</span>
                                                        @elseif($selisih < 0)
                                                            <span class="text-danger">{{ number_format($selisih, 2) }}</span>
                                                        @else
                                                            <span class="text-muted">0</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div class="alert alert-light border mt-3 mb-0 small">
                                <strong>Snapshot guru terakhir disimpan:</strong>
                                {{ $nilai->guru_terakhir_simpan_at ? $nilai->guru_terakhir_simpan_at->format('d M Y, H:i') : 'Belum pernah disimpan guru' }}<br>
                                <strong>Edit wali terakhir:</strong>
                                {{ $nilai->wali_terakhir_edit_at ? $nilai->wali_terakhir_edit_at->format('d M Y, H:i') : 'Belum pernah diedit wali' }}
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            @if(!empty($diff))
                                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#syncGuruModal{{ $nilai->id }}" data-bs-dismiss="modal">
                                    <i class="bx bx-sync me-1"></i> Sinkronisasi dari Guru
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sync Modal --}}
            <div class="modal fade" id="syncGuruModal{{ $nilai->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-warning" style="color:#fff !important;">
                            <h5 class="modal-title fw-bold" style="color:#fff !important;">
                                <i class="bx bx-sync me-2" style="color:#fff !important;"></i>Sinkronisasi dari Guru
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body py-4">
                            <div class="text-center mb-3">
                                <i class="bx bx-sync fs-1 text-warning mb-2"></i>
                                <h6 class="fw-bold">Timpa nilai Anda dengan nilai dari guru?</h6>
                                <p class="text-muted small mb-0">{{ $mapel->nama_mapel }} — {{ $siswa->nama_lengkap }}</p>
                            </div>
                            <div class="alert alert-warning bg-light border-warning mb-0 small">
                                <ul class="mb-0">
                                    <li>Semua nilai komponen akan disalin dari <strong>snapshot guru</strong> ke kolom utama.</li>
                                    <li>Edit Anda sebelumnya akan <strong>hilang</strong> untuk mapel ini.</li>
                                    <li>Anda bisa edit kembali setelah sinkronisasi jika diperlukan.</li>
                                </ul>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <form action="{{ route('wali.nilai.sync-guru', $nilai->id) }}" method="POST" class="d-inline">
                                @csrf
                                <input type="hidden" name="semester" value="{{ $semester }}">
                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                <button type="submit" class="btn btn-warning">
                                    <i class="bx bx-sync me-1"></i> Ya, Sinkronisasi
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach

</div>

{{-- ── Import Modal ── --}}
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">
                    <i class="bx bx-import me-1"></i> Import Nilai dari Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('wali.nilai.import', $siswa->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="semester" value="{{ $semester }}">
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bx bx-info-circle me-1"></i>
                        Download <strong>Template Excel</strong> terlebih dahulu, isi nilai, lalu upload di sini.<br>
                        <strong>Jangan ubah</strong> kolom <code>kode_mapel</code> pada template.<br>
                        Nilai akan diimport ke <strong>Semester {{ ucfirst($semester) }}</strong>.
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">File Excel (.xlsx / .xls)</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls" required>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bx bx-download text-secondary"></i>
                        <a href="{{ route('wali.nilai.download-template', $siswa->id) }}" class="small text-decoration-none">
                            Download template untuk siswa ini
                        </a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-sm fw-semibold">
                        <i class="bx bx-upload me-1"></i> Upload & Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function adjustValue(inputId, delta) {
    const input = document.getElementById(inputId);
    if (!input) return;
    let val = parseFloat(input.value) || 0;
    val = Math.max(0, Math.min(100, Math.round((val + delta) * 100) / 100));
    input.value = val;
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', function () {

    /* ── Input validation ── */
    document.querySelectorAll('.input-nilai').forEach(input => {
        input.addEventListener('input', function () {
            if (this.value === '') return;
            let num = parseFloat(this.value);
            if (num > 100) {
                const s = this.value.toString();
                if (!s.includes('.')) {
                    const corrected = s.slice(0, -1) + '.' + s.slice(-1);
                    if (parseFloat(corrected) <= 100) { this.value = corrected; return; }
                }
                this.value = 100;
            }
        });
        input.addEventListener('keydown', e => { if (['e','E','-','+'].includes(e.key)) e.preventDefault(); });
        input.addEventListener('blur', function () {
            const v = parseFloat(this.value);
            if (!isNaN(v)) {
                if (v > 100) this.value = 100;
                if (v < 0)   this.value = 0;
            }
        });
    });

    /* ── Per-mapel live recalculation ── */
    @foreach($mataPelajaranList as $mapel)
    (function () {
        const mid = {{ $mapel->id }};

        const tugasInputs   = document.querySelectorAll(`.tugas-input-${mid}`);
        const latihanInputs = document.querySelectorAll(`.latihan-input-${mid}`);
        const uhInputs      = document.querySelectorAll(`.uh-input-${mid}`);
        const ptsInput      = document.querySelector(`.pts-input-${mid}`);
        const pasInput      = document.querySelector(`.pas-input-${mid}`);

        const rataTugasEl   = document.getElementById(`rata_tugas_${mid}`);
        const rataLatihanEl = document.getElementById(`rata_latihan_${mid}`);
        const rataUhEl      = document.getElementById(`rata_uh_${mid}`);
        const nilaiAkhirEl  = document.getElementById(`nilai_akhir_${mid}`);

        function avg(inputs) {
            let sum = 0, count = 0;
            inputs.forEach(inp => {
                const v = parseFloat(inp.value);
                if (!isNaN(v) && inp.value.trim() !== '') { sum += v; count++; }
            });
            return count > 0 ? sum / count : null;
        }

        function recalc() {
            const rt = avg(tugasInputs);
            const rl = avg(latihanInputs);
            const ru = avg(uhInputs);
            const pts = parseFloat(ptsInput?.value) || 0;
            const pas = parseFloat(pasInput?.value) || 0;

            rataTugasEl.textContent   = rt !== null ? rt.toFixed(1) : '-';
            rataLatihanEl.textContent = rl !== null ? rl.toFixed(1) : '-';
            rataUhEl.textContent      = ru !== null ? ru.toFixed(1) : '-';

            if (rt || rl || ru || pts || pas) {
                const na = ((rt||0)*1 + (rl||0)*1 + (ru||0)*2 + pts*3 + pas*3) / 10;
                nilaiAkhirEl.textContent = na.toFixed(2);
            } else {
                nilaiAkhirEl.textContent = '-';
            }
        }

        tugasInputs.forEach(i => i.addEventListener('input', recalc));
        latihanInputs.forEach(i => i.addEventListener('input', recalc));
        uhInputs.forEach(i => i.addEventListener('input', recalc));
        ptsInput?.addEventListener('input', recalc);
        pasInput?.addEventListener('input', recalc);
    })();
    @endforeach
});
</script>
@endsection
