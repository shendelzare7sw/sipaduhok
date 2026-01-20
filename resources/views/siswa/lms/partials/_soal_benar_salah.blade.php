{{-- 
    Komponen: Benar/Salah (Tabel Pernyataan)
    Props: $soal, $index, $answers (existing answers array), $disabled
--}}
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
                    <th style="width: 50px;">No</th>
                    <th>Pernyataan</th>
                    <th style="width: 100px; text-align: center;">Benar</th>
                    <th style="width: 100px; text-align: center;">Salah</th>
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
                                   {{ $disabled ? 'disabled' : '' }}
                                   onchange="updateBSOption(this)">
                            <i class="fas fa-check text-success"></i>
                        </label>
                    </td>
                    <td class="text-center">
                        <label class="bs-option {{ $existingValue === false || $existingValue === 'false' ? 'selected' : '' }}">
                            <input type="radio" 
                                   name="jawaban[{{ $soal->id }}][{{ $pIndex }}]" 
                                   value="false"
                                   {{ ($existingValue === false || $existingValue === 'false') ? 'checked' : '' }}
                                   {{ $disabled ? 'disabled' : '' }}
                                   onchange="updateBSOption(this)">
                            <i class="fas fa-times text-danger"></i>
                        </label>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.pernyataan-table table {
    margin-bottom: 0;
}
.pernyataan-table th {
    background: #f8fafc;
    font-weight: 600;
    color: #374151;
    font-size: 13px;
}
.pernyataan-table td {
    vertical-align: middle;
}
.bs-option {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    background: white;
}
.bs-option:hover {
    border-color: var(--primary, #165fac);
    background: #f8fafc;
}
.bs-option.selected {
    border-color: var(--primary, #165fac);
    background: #eff6ff;
}
.bs-option input {
    display: none;
}
.bs-option i {
    font-size: 16px;
    opacity: 0.3;
}
.bs-option.selected i {
    opacity: 1;
}
</style>

<script>
function updateBSOption(input) {
    const row = input.closest('tr');
    row.querySelectorAll('.bs-option').forEach(opt => opt.classList.remove('selected'));
    input.closest('.bs-option').classList.add('selected');
}
</script>
