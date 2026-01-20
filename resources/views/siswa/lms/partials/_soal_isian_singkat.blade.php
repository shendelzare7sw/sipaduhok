{{-- 
    Komponen: Isian Singkat
    Props: $soal, $index, $answers (existing answers array), $disabled
--}}
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

<style>
.isian-input-container {
    max-width: 500px;
}
.isian-input {
    font-size: 16px;
    padding: 12px 16px;
    border-radius: 8px;
}
.isian-input:focus {
    border-color: var(--primary, #165fac);
    box-shadow: 0 0 0 3px rgba(22, 95, 172, 0.1);
}
.isian-input-container .input-group-text {
    background: var(--primary, #165fac);
    color: white;
    border: none;
}
</style>
