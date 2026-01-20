{{-- 
    Komponen: Pilihan Ganda Biasa (Single Answer)
    Props: $soal, $index, $answers (existing answers array), $disabled
--}}
@php
    $soalData = $soal->pilihan_jawaban ?? [];
    $options = is_array($soalData) ? $soalData : [];
    $existingAnswer = $answers[$soal->id] ?? '';
@endphp

<div class="soal-item soal-pilgan" data-soal-id="{{ $soal->id }}">
    <div class="soal-header">
        <span class="soal-nomor">{{ $index }}</span>
        <span class="badge bg-primary">Pilihan Ganda</span>
        <span class="badge bg-secondary">{{ $soal->bobot_nilai }} poin</span>
    </div>
    
    <div class="soal-pertanyaan">
        {!! nl2br(e($soal->pertanyaan)) !!}
    </div>
    
    <div class="pilihan-list">
        @foreach($options as $key => $optionText)
        @php
            $letter = is_numeric($key) ? chr(65 + $key) : $key; // A, B, C, D, E
        @endphp
        <label class="pilihan-item pilihan-radio {{ $existingAnswer == $letter ? 'selected' : '' }} {{ $disabled ? 'disabled' : '' }}">
            <input type="radio" 
                   name="jawaban[{{ $soal->id }}]" 
                   value="{{ $letter }}"
                   {{ $existingAnswer == $letter ? 'checked' : '' }}
                   {{ $disabled ? 'disabled' : '' }}
                   onchange="updateRadio(this)">
            <span class="pilihan-letter">{{ $letter }}</span>
            <span class="pilihan-text">{{ $optionText }}</span>
            <span class="pilihan-radio-dot"></span>
        </label>
        @endforeach
    </div>
</div>

<style>
.pilihan-radio-dot {
    width: 22px;
    height: 22px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
}
.pilihan-radio-dot::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 10px;
    height: 10px;
    background: transparent;
    border-radius: 50%;
    transition: all 0.2s;
}
.pilihan-item.selected .pilihan-radio-dot {
    border-color: var(--primary, #165fac);
}
.pilihan-item.selected .pilihan-radio-dot::after {
    background: var(--primary, #165fac);
}
</style>

<script>
function updateRadio(input) {
    const container = input.closest('.pilihan-list');
    container.querySelectorAll('.pilihan-item').forEach(item => item.classList.remove('selected'));
    input.closest('.pilihan-item').classList.add('selected');
}
</script>
