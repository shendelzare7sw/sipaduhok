{{-- 
    Komponen: Isian Singkat
    Props: $soal, $index, $answers (existing answers array), $disabled
--}}
@pushOnce('styles', 'siswa-lms-soal-styles')
    @vite(['resources/css/siswa/lms/partials/soal.css'])
@endPushOnce

@php
    $existingAnswer = $answers[$soal->id] ?? '';
@endphp

<div class="soal-item soal-isian" data-soal-id="{{ $soal->id }}">
    <div class="soal-header">
        <span class="soal-nomor">{{ $index }}</span>
        <span class="badge bg-success">Isian Singkat</span>
        <span class="badge bg-secondary">{{ $soal->bobot_nilai }} poin</span>
    </div>
    
    <div class="soal-pertanyaan">
        {!! nl2br(e($soal->pertanyaan)) !!}
    </div>
    
    <div class="soal-hint">
        <i class="fas fa-info-circle"></i> 
        Ketik jawaban Anda dengan singkat dan tepat
    </div>
    
    <div class="isian-input-container">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-pencil-alt"></i>
            </span>
            <input type="text" 
                   name="jawaban[{{ $soal->id }}]" 
                   class="form-control isian-input"
                   value="{{ $existingAnswer }}"
                   placeholder="Ketik jawaban di sini..."
                   autocomplete="off"
                   {{ $disabled ? 'disabled' : '' }}>
        </div>
        <small class="form-text text-muted mt-2">
            <i class="fas fa-lightbulb"></i> 
            Perhatikan ejaan dan kapitalisasi, meskipun penilaian tidak case-sensitive
        </small>
    </div>
</div>
