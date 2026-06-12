{{-- 
    Komponen: Benar/Salah (Tabel Pernyataan)
    Props: $soal, $index, $answers (existing answers array), $disabled
--}}
@pushOnce('styles', 'siswa-lms-soal-styles')
    @vite(['resources/css/siswa/lms/partials/soal.css'])
@endPushOnce

@pushOnce('scripts', 'siswa-lms-soal-scripts')
    @vite(['resources/js/siswa/lms/partials/soal.js'])
@endPushOnce

@php
    $soalData = $soal->pilihan_jawaban ?? [];
    $pernyataan = $soalData['pernyataan'] ?? [];
    $existingAnswer = $answers[$soal->id] ?? [];
    if (is_string($existingAnswer)) {
        $existingAnswer = json_decode($existingAnswer, true) ?? [];
    }
@endphp

<div class="soal-item soal-benar-salah" data-soal-id="{{ $soal->id }}">
    <div class="soal-header">
        <span class="soal-nomor">{{ $index }}</span>
        <span class="badge bg-warning text-dark">Benar / Salah</span>
        <span class="badge bg-secondary">{{ $soal->bobot_nilai }} poin</span>
    </div>
    
    <div class="soal-pertanyaan">
        {!! nl2br(e($soal->pertanyaan)) !!}
    </div>
    
    <div class="soal-hint">
        <i class="fas fa-info-circle"></i> 
        Tentukan apakah setiap pernyataan berikut <strong>Benar</strong> atau <strong>Salah</strong>
    </div>
    
    <div class="pernyataan-table">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th class="pernyataan-no-col">No</th>
                    <th>Pernyataan</th>
                    <th class="pernyataan-answer-col">Benar</th>
                    <th class="pernyataan-answer-col">Salah</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pernyataan as $pIndex => $item)
                @php
                    $pernyataanText = is_array($item) ? ($item['text'] ?? '') : $item;
                    $existingValue = $existingAnswer[$pIndex] ?? null;
                @endphp
                <tr>
                    <td class="text-center fw-bold">{{ $pIndex + 1 }}</td>
                    <td>{{ $pernyataanText }}</td>
                    <td class="text-center">
                        <label class="bs-option {{ $existingValue === true || $existingValue === 'true' ? 'selected' : '' }}">
                            <input type="radio" 
                                   name="jawaban[{{ $soal->id }}][{{ $pIndex }}]" 
                                   value="true"
                                   {{ ($existingValue === true || $existingValue === 'true') ? 'checked' : '' }}
                                   {{ $disabled ? 'disabled' : '' }}>
                            <i class="fas fa-check text-success"></i>
                        </label>
                    </td>
                    <td class="text-center">
                        <label class="bs-option {{ $existingValue === false || $existingValue === 'false' ? 'selected' : '' }}">
                            <input type="radio" 
                                   name="jawaban[{{ $soal->id }}][{{ $pIndex }}]" 
                                   value="false"
                                   {{ ($existingValue === false || $existingValue === 'false') ? 'checked' : '' }}
                                   {{ $disabled ? 'disabled' : '' }}>
                            <i class="fas fa-times text-danger"></i>
                        </label>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
