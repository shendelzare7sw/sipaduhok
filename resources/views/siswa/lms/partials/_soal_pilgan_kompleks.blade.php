{{--
Komponen: Pilihan Ganda Kompleks (Multiple Answers)
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
                <input type="checkbox" name="jawaban[{{ $soal->id }}][]" value="{{ $letter }}" {{ in_array($letter, $existingAnswer) ? 'checked' : '' }} {{ $disabled ? 'disabled' : '' }}>
                <span class="pilihan-letter">{{ $letter }}</span>
                <span class="pilihan-text">{{ $optionText }}</span>
                <span class="pilihan-check"><i class="fas fa-check"></i></span>
            </label>
        @endforeach
    </div>
</div>
