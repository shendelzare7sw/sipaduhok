{{--
Komponen: Pilihan Ganda Kompleks (Multiple Answers)
Props: $soal, $index, $answers (existing answers array), $disabled
--}}
@php
    $soalData = $soal->pilihan_jawaban ?? [];
    $options = $soalData['options'] ?? ['A', 'B', 'C', 'D', 'E'];
    $existingAnswer = $answers[$soal->id] ?? [];
    if (is_string($existingAnswer)) {
        $existingAnswer = json_decode($existingAnswer, true) ?? [];
    }
@endphp

<div class="soal-item soal-pilgan-kompleks" data-soal-id="{{ $soal->id }}">
    <div class="soal-header">
        <span class="soal-nomor">{{ $index }}</span>
        <span class="badge bg-info">Pilihan Ganda Kompleks</span>
        <span class="badge bg-secondary">{{ $soal->bobot_nilai }} poin</span>
    </div>

    <div class="soal-pertanyaan">
        {!! nl2br(e($soal->pertanyaan)) !!}
    </div>

    <div class="soal-hint">
        <i class="fas fa-info-circle"></i>
        Pilih <strong>semua</strong> jawaban yang benar (bisa lebih dari satu)
    </div>

    <div class="pilihan-list">
        @foreach($options as $key => $optionText)
            @php
                $letter = is_numeric($key) ? chr(65 + $key) : $key; // A, B, C, D, E
            @endphp
            <label
                class="pilihan-item pilihan-checkbox {{ in_array($letter, $existingAnswer) ? 'selected' : '' }} {{ $disabled ? 'disabled' : '' }}">
                <input type="checkbox" name="jawaban[{{ $soal->id }}][]" value="{{ $letter }}" {{ in_array($letter, $existingAnswer) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }} onchange="updateCheckbox(this)">
                <span class="pilihan-letter">{{ $letter }}</span>
                <span class="pilihan-text">{{ $optionText }}</span>
                <span class="pilihan-check"><i class="fas fa-check"></i></span>
            </label>
        @endforeach
    </div>
</div>

<style>
    .soal-item {
        background: white;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .soal-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }

    .soal-nomor {
        background: var(--primary, #165fac);
        color: white;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .soal-pertanyaan {
        font-size: 15px;
        line-height: 1.7;
        color: #1f2937;
        margin-bottom: 12px;
    }

    .soal-hint {
        font-size: 13px;
        color: #6b7280;
        padding: 10px 14px;
        background: #f0f9ff;
        border-radius: 8px;
        margin-bottom: 16px;
    }

    .pilihan-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .pilihan-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        background: #f9fafb;
        border: 2px solid transparent;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .pilihan-item:hover:not(.disabled) {
        background: #f3f4f6;
        border-color: var(--primary, #165fac);
    }

    .pilihan-item.selected {
        background: #eff6ff;
        border-color: var(--primary, #165fac);
    }

    .pilihan-item.disabled {
        opacity: 0.7;
        cursor: not-allowed;
    }

    .pilihan-item input {
        display: none;
    }

    .pilihan-letter {
        width: 32px;
        height: 32px;
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        color: #6b7280;
        flex-shrink: 0;
    }

    .pilihan-item.selected .pilihan-letter {
        background: var(--primary, #165fac);
        border-color: var(--primary, #165fac);
        color: white;
    }

    .pilihan-text {
        flex: 1;
        color: #374151;
    }

    .pilihan-check {
        width: 24px;
        height: 24px;
        border: 2px solid #d1d5db;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: transparent;
    }

    .pilihan-item.selected .pilihan-check {
        background: var(--primary, #165fac);
        border-color: var(--primary, #165fac);
        color: white;
    }
</style>

<script>
    function updateCheckbox(input) {
        const item = input.closest('.pilihan-item');
        if (input.checked) {
            item.classList.add('selected');
        } else {
            item.classList.remove('selected');
        }
    }
</script>