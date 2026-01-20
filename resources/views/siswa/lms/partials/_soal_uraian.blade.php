{{--
Komponen: Uraian / Essay
Props: $soal, $index, $answers (existing answers array), $disabled, $allowFileUpload
--}}
@php
    $existingAnswer = $answers[$soal->id] ?? [];
    $existingText = is_array($existingAnswer) ? ($existingAnswer['text'] ?? '') : $existingAnswer;
    $existingFile = is_array($existingAnswer) ? ($existingAnswer['file'] ?? null) : null;
    $allowFileUpload = $allowFileUpload ?? true;
@endphp

<div class="soal-item soal-uraian" data-soal-id="{{ $soal->id }}">
    <div class="soal-header">
        <span class="soal-nomor">{{ $index }}</span>
        <span class="badge bg-danger">Uraian</span>
        <span class="badge bg-secondary">{{ $soal->bobot_nilai }} poin</span>
    </div>

    <div class="soal-pertanyaan">
        {!! nl2br(e($soal->pertanyaan)) !!}
    </div>

    @if($soal->kunci_jawaban)
        <div class="soal-hint">
            <i class="fas fa-lightbulb"></i>
            <strong>Petunjuk:</strong> {{ $soal->kunci_jawaban }}
        </div>
    @endif

    <div class="uraian-container">
        {{-- Text Area --}}
        <div class="mb-3">
            <label class="form-label fw-bold">
                <i class="fas fa-edit"></i> Jawaban Anda
            </label>
            <textarea name="jawaban[{{ $soal->id }}][text]" class="form-control uraian-textarea" rows="8"
                placeholder="Tuliskan jawaban Anda secara lengkap..." {{ $disabled ? 'disabled' : '' }}>{{ $existingText }}</textarea>
            <div class="textarea-footer">
                <span class="char-counter">
                    <span id="charCount-{{ $soal->id }}">0</span> karakter
                </span>
                <span class="word-counter">
                    <span id="wordCount-{{ $soal->id }}">0</span> kata
                </span>
            </div>
        </div>

        {{-- File Upload --}}
        @if($allowFileUpload)
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-paperclip"></i> Lampiran (Opsional)
                </label>
                <input type="file" name="jawaban_file[{{ $soal->id }}]" class="form-control"
                    accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png" {{ $disabled ? 'disabled' : '' }}>
                <small class="form-text text-muted">
                    Format: PDF, Word, Excel, PowerPoint, atau Gambar (JPG/PNG). Maks 10MB
                </small>

                @if($existingFile)
                    <div class="mt-2">
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle"></i> File terlampir
                        </span>
                        <a href="{{ asset('storage/' . $existingFile) }}" target="_blank"
                            class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-eye"></i> Lihat
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
    .uraian-textarea {
        font-size: 15px;
        line-height: 1.7;
        border-radius: 10px;
        resize: vertical;
        min-height: 200px;
    }

    .uraian-textarea:focus {
        border-color: var(--primary, #165fac);
        box-shadow: 0 0 0 3px rgba(22, 95, 172, 0.1);
    }

    .textarea-footer {
        display: flex;
        gap: 20px;
        padding: 8px 0;
        font-size: 12px;
        color: #6b7280;
    }

    .char-counter,
    .word-counter {
        padding: 4px 10px;
        background: #f3f4f6;
        border-radius: 4px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textarea = document.querySelector('[name="jawaban[{{ $soal->id }}][text]"]');
        if (textarea) {
            const charCounter = document.getElementById('charCount-{{ $soal->id }}');
            const wordCounter = document.getElementById('wordCount-{{ $soal->id }}');

            function updateCounters() {
                const text = textarea.value;
                charCounter.textContent = text.length;
                wordCounter.textContent = text.trim() ? text.trim().split(/\s+/).length : 0;
            }

            textarea.addEventListener('input', updateCounters);
            updateCounters(); // Initial count
        }
    });
</script>