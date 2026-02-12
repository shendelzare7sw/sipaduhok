@extends('layouts.sneat')

@section('title', 'Edit Nilai Siswa')
@section('page-title', 'Edit Nilai Siswa')
@section('page-subtitle', 'Perbarui nilai per mata pelajaran')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }
    .nilai-akhir-display {
        padding: 8px;
        background: #d4edda;
        border-radius: 6px;
        font-weight: 700;
        color: #155724;
    }
    .student-info-card {
        background: white;
        border-radius: 15px;
        border-left: 5px solid #4e73df;
    }
    /* Input wrapper dengan spinner di luar */
    .input-wrapper {
        display: flex;
        align-items: center;
        gap: 2px;
    }
    .input-nilai {
        width: 55px;
        padding: 7px 5px;
        font-size: 14px;
        text-align: center;
        border-radius: 5px;
        border: 1px solid #ced4da;
        -moz-appearance: textfield;
    }
    .input-nilai::-webkit-outer-spin-button,
    .input-nilai::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .input-nilai:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 3px rgba(78,115,223,0.25);
        outline: none;
    }
    /* Spinner buttons di luar input */
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
    .rata-cell {
        background: #e9ecef;
        font-weight: 700;
        color: #165fac;
        text-align: center;
        font-size: 14px;
    }
    .th-tugas { background: #e3f2fd !important; }
    .th-latihan { background: #fff3e0 !important; }
    .th-uh { background: #fce4ec !important; }
    .th-tingkat-akhir { background: #e8f5e9 !important; }
    .mapel-row {
        border-left: 3px solid #4e73df;
    }
</style>
@endsection

@section('content')
@php
    $isKelasAkhir = str_contains(strtolower($kelas->nama_kelas), '9') || 
                    str_contains(strtolower($kelas->nama_kelas), '12') ||
                    str_contains(strtolower($kelas->nama_kelas), 'ix') ||
                    str_contains(strtolower($kelas->nama_kelas), 'xii');
@endphp
<div style="max-width: 1600px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Back Button --}}
    <div class="mb-4 no-print">
        <a href="{{ route('wali.nilai.index') }}" class="btn btn-light btn-sm fw-bold shadow-sm border text-gray-700">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Nilai
        </a>
    </div>

    {{-- Student Info Card --}}
    <div class="card student-info-card shadow-sm mb-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-gray-900 mb-2">Edit Nilai - {{ $siswa->nama_lengkap }}</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">NIS</div>
                    <div class="fw-bold text-dark">{{ $siswa->nis }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">NISN</div>
                    <div class="fw-bold text-dark">{{ $siswa->nisn }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Kelas</div>
                    <div class="fw-bold text-dark">{{ $kelas->nama_kelas }}</div>
                </div>
                <div class="col-md-3">
                    <div class="small text-muted fw-bold text-uppercase">Tahun Ajaran</div>
                    <div class="fw-bold text-dark">{{ $kelas->tahunAjaran->nama_tahun_ajaran }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger shadow-sm border-start border-danger border-4 mb-4">
            <h6 class="fw-bold mb-2"><i class="fas fa-exclamation-triangle me-2"></i>Ada kesalahan dalam input data:</h6>
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Edit Form --}}
    <form action="{{ route('wali.nilai.update', $siswa->id) }}" method="POST" id="nilaiForm">
        @csrf
        @method('PUT')

        @foreach($mataPelajaranList as $mapelIndex => $mapel)
            @php
                $nilai = $nilaiData[$mapel->id] ?? null;
            @endphp
            
            <div class="card shadow mb-3">
                <div class="card-header py-2 bg-white border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="m-0 fw-bold text-primary">
                            <i class="fas fa-book me-2"></i>{{ $mapel->nama_mapel }}
                            <small class="text-muted ms-2">({{ $mapel->kode_mapel }})</small>
                        </h6>
                        <span class="badge bg-primary fs-6">Nilai Akhir: <span id="nilai_akhir_{{ $mapel->id }}">{{ $nilai ? number_format($nilai->nilai_akhir, 2) : '-' }}</span></span>
                    </div>
                </div>
                <div class="card-body p-3">
                    <input type="hidden" name="nilai[{{ $mapel->id }}][mata_pelajaran_id]" value="{{ $mapel->id }}">
                    
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered mb-0">
                            <thead>
                                <tr>
                                    <th colspan="6" class="text-center th-tugas">Tugas</th>
                                    <th colspan="6" class="text-center th-latihan">Latihan</th>
                                    <th colspan="6" class="text-center th-uh">Ulangan Harian</th>
                                    <th rowspan="2" class="text-center" style="width: 75px; vertical-align: middle;">PTS</th>
                                    <th rowspan="2" class="text-center" style="width: 75px; vertical-align: middle;">PAS</th>
                                </tr>
                                <tr>
                                    @for($i = 1; $i <= 5; $i++)
                                        <th class="text-center th-tugas" style="width: 75px;">T{{ $i }}</th>
                                    @endfor
                                    <th class="text-center th-tugas rata-cell" style="width: 60px;">Rata</th>
                                    @for($i = 1; $i <= 5; $i++)
                                        <th class="text-center th-latihan" style="width: 75px;">L{{ $i }}</th>
                                    @endfor
                                    <th class="text-center th-latihan rata-cell" style="width: 60px;">Rata</th>
                                    @for($i = 1; $i <= 5; $i++)
                                        <th class="text-center th-uh" style="width: 75px;">UH{{ $i }}</th>
                                    @endfor
                                    <th class="text-center th-uh rata-cell" style="width: 60px;">Rata</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    {{-- Tugas 1-5 --}}
                                    @for($i = 1; $i <= 5; $i++)
                                        <td class="text-center p-1">
                                            <div class="input-wrapper justify-content-center">
                                                <input type="number" step="0.01" min="0" max="100"
                                                    name="nilai[{{ $mapel->id }}][tugas_{{ $i }}]"
                                                    class="form-control input-nilai tugas-input-{{ $mapel->id }}"
                                                    id="tugas_{{ $mapel->id }}_{{ $i }}"
                                                    data-mapel="{{ $mapel->id }}"
                                                    value="{{ $nilai && $nilai->{'tugas_'.$i} !== null ? $nilai->{'tugas_'.$i} : '' }}"
                                                    placeholder="-">
                                                <div class="spinner-btns">
                                                    <button type="button" class="spinner-btn" onclick="adjustValue('tugas_{{ $mapel->id }}_{{ $i }}', 1)">▲</button>
                                                    <button type="button" class="spinner-btn" onclick="adjustValue('tugas_{{ $mapel->id }}_{{ $i }}', -1)">▼</button>
                                                </div>
                                            </div>
                                        </td>
                                    @endfor
                                    <td class="text-center rata-cell p-2" id="rata_tugas_{{ $mapel->id }}">
                                        {{ $nilai && $nilai->rata_tugas !== null ? number_format($nilai->rata_tugas, 1) : '-' }}
                                    </td>
                                    
                                    {{-- Latihan 1-5 --}}
                                    @for($i = 1; $i <= 5; $i++)
                                        <td class="text-center p-1">
                                            <div class="input-wrapper justify-content-center">
                                                <input type="number" step="0.01" min="0" max="100"
                                                    name="nilai[{{ $mapel->id }}][latihan_{{ $i }}]"
                                                    class="form-control input-nilai latihan-input-{{ $mapel->id }}"
                                                    id="latihan_{{ $mapel->id }}_{{ $i }}"
                                                    data-mapel="{{ $mapel->id }}"
                                                    value="{{ $nilai && $nilai->{'latihan_'.$i} !== null ? $nilai->{'latihan_'.$i} : '' }}"
                                                    placeholder="-">
                                                <div class="spinner-btns">
                                                    <button type="button" class="spinner-btn" onclick="adjustValue('latihan_{{ $mapel->id }}_{{ $i }}', 1)">▲</button>
                                                    <button type="button" class="spinner-btn" onclick="adjustValue('latihan_{{ $mapel->id }}_{{ $i }}', -1)">▼</button>
                                                </div>
                                            </div>
                                        </td>
                                    @endfor
                                    <td class="text-center rata-cell p-2" id="rata_latihan_{{ $mapel->id }}">
                                        {{ $nilai && $nilai->rata_latihan !== null ? number_format($nilai->rata_latihan, 1) : '-' }}
                                    </td>
                                    
                                    {{-- UH 1-5 --}}
                                    @for($i = 1; $i <= 5; $i++)
                                        <td class="text-center p-1">
                                            <div class="input-wrapper justify-content-center">
                                                <input type="number" step="0.01" min="0" max="100"
                                                    name="nilai[{{ $mapel->id }}][uh_{{ $i }}]"
                                                    class="form-control input-nilai uh-input-{{ $mapel->id }}"
                                                    id="uh_{{ $mapel->id }}_{{ $i }}"
                                                    data-mapel="{{ $mapel->id }}"
                                                    value="{{ $nilai && $nilai->{'uh_'.$i} !== null ? $nilai->{'uh_'.$i} : '' }}"
                                                    placeholder="-">
                                                <div class="spinner-btns">
                                                    <button type="button" class="spinner-btn" onclick="adjustValue('uh_{{ $mapel->id }}_{{ $i }}', 1)">▲</button>
                                                    <button type="button" class="spinner-btn" onclick="adjustValue('uh_{{ $mapel->id }}_{{ $i }}', -1)">▼</button>
                                                </div>
                                            </div>
                                        </td>
                                    @endfor
                                    <td class="text-center rata-cell p-2" id="rata_uh_{{ $mapel->id }}">
                                        {{ $nilai && $nilai->rata_uh !== null ? number_format($nilai->rata_uh, 1) : '-' }}
                                    </td>
                                    
                                    {{-- PTS --}}
                                    <td class="text-center p-1">
                                        <div class="input-wrapper justify-content-center">
                                            <input type="number" step="0.01" min="0" max="100"
                                                name="nilai[{{ $mapel->id }}][pts]"
                                                class="form-control input-nilai pts-input-{{ $mapel->id }}"
                                                id="pts_{{ $mapel->id }}"
                                                data-mapel="{{ $mapel->id }}"
                                                value="{{ $nilai && $nilai->pts !== null ? $nilai->pts : '' }}"
                                                placeholder="-">
                                            <div class="spinner-btns">
                                                <button type="button" class="spinner-btn" onclick="adjustValue('pts_{{ $mapel->id }}', 1)">▲</button>
                                                <button type="button" class="spinner-btn" onclick="adjustValue('pts_{{ $mapel->id }}', -1)">▼</button>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    {{-- PAS --}}
                                    <td class="text-center p-1">
                                        <div class="input-wrapper justify-content-center">
                                            <input type="number" step="0.01" min="0" max="100"
                                                name="nilai[{{ $mapel->id }}][pas]"
                                                class="form-control input-nilai pas-input-{{ $mapel->id }}"
                                                id="pas_{{ $mapel->id }}"
                                                data-mapel="{{ $mapel->id }}"
                                                value="{{ $nilai && $nilai->pas !== null ? $nilai->pas : '' }}"
                                                placeholder="-">
                                            <div class="spinner-btns">
                                                <button type="button" class="spinner-btn" onclick="adjustValue('pas_{{ $mapel->id }}', 1)">▲</button>
                                                <button type="button" class="spinner-btn" onclick="adjustValue('pas_{{ $mapel->id }}', -1)">▼</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Tingkat Akhir Fields (only for kelas 9/12) --}}
                    @if($isKelasAkhir)
                    <div class="mt-3 pt-3 border-top">
                        <small class="text-muted fw-bold text-uppercase">Penilaian Tingkat Akhir</small>
                        <div class="row mt-2">
                            @foreach(['to_1' => 'TO 1', 'to_2' => 'TO 2', 'to_3' => 'TO 3', 'upk' => 'UPK', 'ujian_praktek' => 'Ujian Praktek'] as $field => $label)
                            <div class="col">
                                <label class="form-label small text-muted mb-1">{{ $label }}</label>
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
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Information Box --}}
        <div class="alert alert-info border-0 mb-4">
            <div class="d-flex align-items-start">
                <i class="fas fa-info-circle fa-2x text-info opacity-50 me-3"></i>
                <div>
                    <div class="fw-bold text-info text-uppercase small mb-1">Informasi Pengisian Nilai</div>
                    <ul class="mb-0 small text-gray-700">
                        <li>Kolom <strong>Tugas, Latihan, UH</strong> dapat diisi sebagian. Rata-rata dihitung hanya dari nilai yang terisi.</li>
                        <li>Kolom kosong <strong>tidak dihitung sebagai 0</strong> dalam perhitungan rata-rata.</li>
                        <li>Nilai Akhir dihitung otomatis: <strong>((Rata Tugas × 1) + (Rata Latihan × 1) + (Rata UH × 2) + (PTS × 3) + (PAS × 3)) / 10</strong></li>
                        <li>Nilai valid: <strong>0 - 100</strong></li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end mb-5">
            <a href="{{ route('wali.nilai.show', $siswa->id) }}" class="btn btn-secondary shadow-sm">
                <i class="fas fa-times me-1"></i> Batal
            </a>
            <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                <i class="fas fa-save me-1"></i> Simpan Semua Perubahan
            </button>
        </div>
    </form>

</div>
</div>
@endsection

@section('scripts')
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
    input.dispatchEvent(new Event('input', { bubbles: true }));
}

document.addEventListener('DOMContentLoaded', function() {
    // Global Input Validation (Auto-Decimal & Max 100)
    const allGradeInputs = document.querySelectorAll('.input-nilai');
    allGradeInputs.forEach(input => {
        input.addEventListener('input', function() {
            let value = this.value;
            if (value === '') return;
            let num = parseFloat(value);
            
            if (num > 100) {
                let strVal = value.toString();
                if (!strVal.includes('.')) {
                    let corrected = strVal.slice(0, -1) + '.' + strVal.slice(-1);
                    if (parseFloat(corrected) <= 100) {
                        this.value = corrected;
                        return;
                    }
                }
                this.value = 100;
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (['e', 'E', '-', '+'].includes(e.key)) e.preventDefault();
        });
        
        input.addEventListener('blur', function() {
             let val = parseFloat(this.value);
             if (!isNaN(val)) {
                 if (val > 100) this.value = 100;
                 if (val < 0) this.value = 0;
             }
        });
    });
    // Calculate rata-rata and nilai akhir for each mapel
    @foreach($mataPelajaranList as $mapel)
    (function() {
        const mapelId = {{ $mapel->id }};
        
        const tugasInputs = document.querySelectorAll(`.tugas-input-${mapelId}`);
        const latihanInputs = document.querySelectorAll(`.latihan-input-${mapelId}`);
        const uhInputs = document.querySelectorAll(`.uh-input-${mapelId}`);
        const ptsInput = document.querySelector(`.pts-input-${mapelId}`);
        const pasInput = document.querySelector(`.pas-input-${mapelId}`);
        
        const rataTugasDisplay = document.getElementById(`rata_tugas_${mapelId}`);
        const rataLatihanDisplay = document.getElementById(`rata_latihan_${mapelId}`);
        const rataUhDisplay = document.getElementById(`rata_uh_${mapelId}`);
        const nilaiAkhirDisplay = document.getElementById(`nilai_akhir_${mapelId}`);
        
        function calculateAverage(inputs) {
            let sum = 0;
            let count = 0;
            inputs.forEach(input => {
                const val = parseFloat(input.value);
                if (!isNaN(val) && input.value.trim() !== '') {
                    sum += val;
                    count++;
                }
            });
            return count > 0 ? sum / count : null;
        }
        
        function updateAll() {
            const rataTugas = calculateAverage(tugasInputs);
            const rataLatihan = calculateAverage(latihanInputs);
            const rataUh = calculateAverage(uhInputs);
            const pts = parseFloat(ptsInput?.value) || 0;
            const pas = parseFloat(pasInput?.value) || 0;
            
            // Display averages
            rataTugasDisplay.textContent = rataTugas !== null ? rataTugas.toFixed(1) : '-';
            rataLatihanDisplay.textContent = rataLatihan !== null ? rataLatihan.toFixed(1) : '-';
            rataUhDisplay.textContent = rataUh !== null ? rataUh.toFixed(1) : '-';
            
            // Calculate nilai akhir using new formula
            const rt = rataTugas || 0;
            const rl = rataLatihan || 0;
            const ru = rataUh || 0;
            
            if (rt || rl || ru || pts || pas) {
                const nilaiAkhir = ((rt * 1) + (rl * 1) + (ru * 2) + (pts * 3) + (pas * 3)) / 10;
                nilaiAkhirDisplay.textContent = nilaiAkhir.toFixed(2);
            } else {
                nilaiAkhirDisplay.textContent = '-';
            }
        }
        
        // Attach listeners
        tugasInputs.forEach(input => input.addEventListener('input', updateAll));
        latihanInputs.forEach(input => input.addEventListener('input', updateAll));
        uhInputs.forEach(input => input.addEventListener('input', updateAll));
        ptsInput?.addEventListener('input', updateAll);
        pasInput?.addEventListener('input', updateAll);
    })();
    @endforeach
});
</script>
@endsection
