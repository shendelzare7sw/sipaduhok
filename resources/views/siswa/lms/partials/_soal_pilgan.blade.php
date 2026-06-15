{{-- 
    Komponen: Pilihan Ganda Biasa (Single Answer)
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
                   {{ $disabled ? 'disabled' : '' }}>
            <span class="pilihan-letter">{{ $letter }}</span>
            <span class="pilihan-text">{{ $optionText }}</span>
            <span class="pilihan-radio-dot"></span>
        </label>
        @endforeach
    </div>
</div>
