@extends('layouts.sneat')

@section('title', 'Edit Nilai Siswa')
@section('page-title', 'Edit Nilai Siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection
@section('styles')
    @vite(['resources/css/wali-kelas/nilai/edit.css', 'resources/js/wali-kelas/nilai/edit.js'])
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
            <span class="badge {{ $semester === 'ganjil' ? 'bg-label-warning' : 'bg-label-info' }} fw-semibold px-3 py-2 semester-badge">
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
                                <th class="text-center score-col-md" rowspan="2">PTS</th>
                                <th class="text-center score-col-md" rowspan="2">PAS</th>
                                <th class="text-center nilai-akhir-cell" rowspan="2">Nilai Akhir</th>
                                @if($isKelasAkhir)
                                <th colspan="5" class="text-center th-akhir">Tingkat Akhir</th>
                                @endif
                            </tr>
                            <tr>
                                {{-- Tugas --}}
                                @for($i=1;$i<=5;$i++)
                                <th class="text-center th-tugas score-col-md">T{{ $i }}</th>
                                @endfor
                                <th class="text-center th-tugas rata-cell">Rata</th>
                                {{-- Latihan --}}
                                @for($i=1;$i<=5;$i++)
                                <th class="text-center th-latihan score-col-sm">L{{ $i }}</th>
                                @endfor
                                <th class="text-center th-latihan rata-cell">Rata</th>
                                {{-- UH --}}
                                @for($i=1;$i<=5;$i++)
                                <th class="text-center th-uh score-col-sm">UH{{ $i }}</th>
                                @endfor
                                <th class="text-center th-uh rata-cell">Rata</th>
                                {{-- Tingkat Akhir sub-headers --}}
                                @if($isKelasAkhir)
                                <th class="text-center th-akhir score-col-sm">TO 1</th>
                                <th class="text-center th-akhir score-col-sm">TO 2</th>
                                <th class="text-center th-akhir score-col-sm">TO 3</th>
                                <th class="text-center th-akhir score-col-sm">UPK</th>
                                <th class="text-center th-akhir score-col-md">Ujian Praktek</th>
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
                                        <div class="flex-grow-1 mapel-info">
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
                                                        class="btn {{ $guruBaru ? 'btn-warning' : 'btn-outline-secondary is-disabled-soft' }} p-0 d-flex align-items-center justify-content-center mapel-action-btn"
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
                                            <button type="button" class="spinner-btn" data-adjust-target="tugas_{{ $mapel->id }}_{{ $j }}" data-adjust-delta="1">▲</button>
                                            <button type="button" class="spinner-btn" data-adjust-target="tugas_{{ $mapel->id }}_{{ $j }}" data-adjust-delta="-1">▼</button>
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
                                            <button type="button" class="spinner-btn" data-adjust-target="latihan_{{ $mapel->id }}_{{ $j }}" data-adjust-delta="1">▲</button>
                                            <button type="button" class="spinner-btn" data-adjust-target="latihan_{{ $mapel->id }}_{{ $j }}" data-adjust-delta="-1">▼</button>
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
                                            <button type="button" class="spinner-btn" data-adjust-target="uh_{{ $mapel->id }}_{{ $j }}" data-adjust-delta="1">▲</button>
                                            <button type="button" class="spinner-btn" data-adjust-target="uh_{{ $mapel->id }}_{{ $j }}" data-adjust-delta="-1">▼</button>
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
                                            <button type="button" class="spinner-btn" data-adjust-target="pts_{{ $mapel->id }}" data-adjust-delta="1">▲</button>
                                            <button type="button" class="spinner-btn" data-adjust-target="pts_{{ $mapel->id }}" data-adjust-delta="-1">▼</button>
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
                                            <button type="button" class="spinner-btn" data-adjust-target="pas_{{ $mapel->id }}" data-adjust-delta="1">▲</button>
                                            <button type="button" class="spinner-btn" data-adjust-target="pas_{{ $mapel->id }}" data-adjust-delta="-1">▼</button>
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
                                            <button type="button" class="spinner-btn" data-adjust-target="{{ $field }}_{{ $mapel->id }}" data-adjust-delta="1">▲</button>
                                            <button type="button" class="spinner-btn" data-adjust-target="{{ $field }}_{{ $mapel->id }}" data-adjust-delta="-1">▼</button>
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
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title fw-bold text-white">
                                <i class="bx bx-show me-2 text-white"></i>Preview Nilai: {{ $mapel->nama_mapel }}
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
                        <div class="modal-header bg-warning text-white">
                            <h5 class="modal-title fw-bold text-white">
                                <i class="bx bx-sync me-2 text-white"></i>Sinkronisasi dari Guru
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
