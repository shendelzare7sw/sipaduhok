@extends('layouts.sneat')

@section('title', 'Edit Nilai Siswa')
@section('page-title', 'Edit Nilai Siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
/* ─── Table base ─────────────────────────────────────── */
.table-nilai {
    font-size: 12.5px;
    border-collapse: separate;
    border-spacing: 0;
}
.table-nilai th {
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    vertical-align: middle;
    white-space: nowrap;
    background: #f8f9fc;
}
.table-nilai td {
    vertical-align: middle;
    padding: 4px 3px;
}

.sticky-no {
    min-width: 36px;
}
.sticky-mapel {
    min-width: 160px;
    max-width: 180px;
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
    gap: 2px;
    justify-content: center;
}
.input-nilai {
    width: 52px;
    min-width: 52px;
    padding: 5px 3px;
    font-size: 12.5px;
    text-align: center;
    border-radius: 4px;
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
    width: 15px; height: 13px;
    padding: 0; font-size: 8px; line-height: 1;
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
@media (max-width: 575.98px) {
    .sticky-mapel { min-width: 120px; max-width: 140px; font-size: 11px; }
    .input-nilai  { width: 46px; min-width: 46px; font-size: 11.5px; }
    .table-nilai  { font-size: 11.5px; }
}
</style>
@endsection

@section('content')
@php
    $isKelasAkhir = str_contains(strtolower($kelas->nama_kelas), '9')  ||
                    str_contains(strtolower($kelas->nama_kelas), '12') ||
                    str_contains(strtolower($kelas->nama_kelas), 'ix') ||
                    str_contains(strtolower($kelas->nama_kelas), 'xii');
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
                                    <div class="fw-semibold lh-sm" style="font-size:12px;">{{ $mapel->nama_mapel }}</div>
                                    <small class="text-muted" style="font-size:10px;">{{ $mapel->kode_mapel }}</small>
                                </td>

                                {{-- Tugas 1-5 --}}
                                @for($j=1;$j<=5;$j++)
                                <td class="p-1">
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
                                <td class="rata-cell" id="rata_tugas_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}
                                </td>

                                {{-- Latihan 1-5 --}}
                                @for($j=1;$j<=5;$j++)
                                <td class="p-1">
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
                                <td class="rata-cell" id="rata_latihan_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}
                                </td>

                                {{-- UH 1-5 --}}
                                @for($j=1;$j<=5;$j++)
                                <td class="p-1">
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
                                <td class="rata-cell" id="rata_uh_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}
                                </td>

                                {{-- PTS --}}
                                <td class="p-1">
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
                                <td class="p-1">
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
                                <td class="nilai-akhir-cell" id="nilai_akhir_{{ $mapel->id }}">
                                    {{ $nilai && $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}
                                </td>

                                {{-- Tingkat Akhir (kelas 9/12 only) --}}
                                @if($isKelasAkhir)
                                @foreach(['to_1'=>'TO 1','to_2'=>'TO 2','to_3'=>'TO 3','upk'=>'UPK','ujian_praktek'=>'Ujian Praktek'] as $field => $label)
                                <td class="p-1">
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
