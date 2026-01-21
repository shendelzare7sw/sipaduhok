<div class="soal-card" id="card-{{ $index }}">
    <div class="soal-header" data-bs-toggle="collapse" data-bs-target="#collapseSoal-{{ $index }}">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-chevron-down icon-toggle"></i>
            <span class="fw-bold">
                Soal #<span class="soal-number">{{ is_numeric($index) ? $index + 1 : 'TEMPLATE_NUMBER' }}</span>
                <span class="badge bg-secondary ms-2" id="badge-{{ $index }}">{{ $soal ? \App\Models\Ujian::getSoalTypeLabels()[$soal->tipe_soal] ?? $soal->tipe_soal : 'Baru' }}</span>
            </span>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger z-index-2" onclick="event.stopPropagation(); removeSoal({{ $index }}, {{ $soal ? $soal->id : 'null' }})">
            <i class="fas fa-trash"></i>
        </button>
    </div>
    
    <div id="collapseSoal-{{ $index }}" class="collapse {{ $isTemplate ? 'show' : '' }}">
        <div class="soal-body">
            @if($soal)
                <input type="hidden" name="soal[{{ $index }}][id]" value="{{ $soal->id }}">
            @endif

            <div class="row mb-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Bobot Nilai</label>
                    <input type="number" name="soal[{{ $index }}][bobot]" class="form-control" value="{{ $soal->bobot_nilai ?? 1 }}" min="1" required>
                </div>
                <div class="col-md-9">
                    <label class="form-label small text-muted">Tipe Soal</label>
                    <select name="soal[{{ $index }}][tipe_soal]" class="form-select" required>
                        <option value="pilihan_ganda" {{ ($soal && $soal->tipe_soal == 'pilihan_ganda') ? 'selected' : '' }}>Pilihan Ganda</option>
                        <option value="pilihan_ganda_kompleks" {{ ($soal && $soal->tipe_soal == 'pilihan_ganda_kompleks') ? 'selected' : '' }}>Pilihan Ganda Kompleks</option>
                        <option value="benar_salah" {{ ($soal && $soal->tipe_soal == 'benar_salah') ? 'selected' : '' }}>Benar/Salah</option>
                        <option value="isian_singkat" {{ ($soal && $soal->tipe_soal == 'isian_singkat') ? 'selected' : '' }}>Isian Singkat</option>
                        <option value="uraian" {{ ($soal && $soal->tipe_soal == 'uraian') ? 'selected' : '' }}>Uraian</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label small text-muted">Pertanyaan</label>
                <textarea name="soal[{{ $index }}][pertanyaan]" class="form-control" rows="3" required>{{ $soal->pertanyaan ?? '' }}</textarea>
            </div>

            <!-- Opsi PG -->
            <div id="opsiPilihanGanda-{{ $index }}" style="display: none;">
                <div class="form-section-title">Pilihan Jawaban</div>
                @php 
                    $pilihan = [];
                    if ($soal && in_array($soal->tipe_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks'])) {
                        $pilihan = is_array($soal->pilihan_jawaban) 
                            ? $soal->pilihan_jawaban 
                            : json_decode($soal->pilihan_jawaban, true);
                    }
                @endphp
                <div class="row g-2">
                    @foreach(['A','B','C','D','E'] as $opt)
                        <div class="col-md-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">{{ $opt }}</span>
                                <input type="text" name="soal[{{ $index }}][pilihan_{{ strtolower($opt) }}]" class="form-control" value="{{ $pilihan[$opt] ?? '' }}">
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Kunci Jawaban -->
            <div class="mt-3">
                <div class="form-section-title">Kunci Jawaban</div>
                
                <div id="kunciPG-{{ $index }}" style="display: none;">
                    <select name="soal[{{ $index }}][kunci_pg]" class="form-select">
                        <option value="">-- Pilih Kunci --</option>
                        @foreach(['A','B','C','D','E'] as $k)
                            <option value="{{ $k }}" {{ ($soal && $soal->kunci_jawaban == $k) ? 'selected' : '' }}>{{ $k }}</option>
                        @endforeach
                    </select>
                </div>

                <div id="kunciPGKompleks-{{ $index }}" style="display: none;">
                    <input type="text" name="soal[{{ $index }}][kunci_pg_kompleks]" class="form-control" placeholder="Contoh: A, C, E" 
                           value="{{ ($soal && $soal->tipe_soal == 'pilihan_ganda_kompleks') ? trim($soal->kunci_jawaban, '[]"') : '' }}">
                </div>

                <div id="kunciBenarSalah-{{ $index }}" style="display: none;">
                    <select name="soal[{{ $index }}][kunci_bs]" class="form-select">
                        <option value="benar" {{ ($soal && $soal->kunci_jawaban == 'benar') ? 'selected' : '' }}>Benar</option>
                        <option value="salah" {{ ($soal && $soal->kunci_jawaban == 'salah') ? 'selected' : '' }}>Salah</option>
                    </select>
                </div>

                <div id="kunciIsian-{{ $index }}" style="display: none;">
                    <input type="text" name="soal[{{ $index }}][kunci_isian]" class="form-control" placeholder="Jawaban singkat" value="{{ $soal->kunci_jawaban ?? '' }}">
                </div>

                <div id="kunciUraian-{{ $index }}" style="display: none;">
                    <p class="text-muted small fst-italic mb-0">Dinilai manual oleh guru.</p>
                </div>
            </div>
        </div>
    </div>
</div>
