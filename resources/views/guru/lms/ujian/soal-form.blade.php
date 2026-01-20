@extends('layouts.lms-guru')

@section('title', $soal ? 'Edit Soal' : 'Tambah Soal')
@section('page-title', $soal ? 'Edit Soal' : 'Tambah Soal Baru')
@section('page-subtitle', 'Ujian: ' . $ujian->judul_ujian)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card-custom">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <span>{{ $soal ? 'Form Edit Soal' : 'Form Soal Baru' }}</span>
                <a href="{{ route('guru.lms.ujian.soal.index', [$kelas->id, $mapel->id, $ujian->id]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
            </div>
            <div class="p-4">
                <form action="{{ $soal ? route('guru.lms.ujian.soal.update', [$kelas->id, $mapel->id, $ujian->id, $soal->id]) : route('guru.lms.ujian.soal.store', [$kelas->id, $mapel->id, $ujian->id]) }}" method="POST" id="soalForm">
                    @csrf
                    @if($soal) @method('PUT') @endif

                    {{-- Tipe Soal & Bobot & Urutan --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Tipe Soal <span class="text-danger">*</span></label>
                            <select name="tipe_soal" id="tipeSoal" class="form-select" required {{ $soal ? '' : '' }}>
                                <option value="pilihan_ganda" {{ ($soal->tipe_soal ?? old('tipe_soal')) == 'pilihan_ganda' ? 'selected' : '' }}>Pilihan Ganda (Satu Jawaban)</option>
                                <option value="pilihan_ganda_kompleks" {{ ($soal->tipe_soal ?? old('tipe_soal')) == 'pilihan_ganda_kompleks' ? 'selected' : '' }}>Pilihan Ganda Kompleks (Banyak Jawaban)</option>
                                <option value="benar_salah" {{ ($soal->tipe_soal ?? old('tipe_soal')) == 'benar_salah' ? 'selected' : '' }}>Benar - Salah</option>
                                <option value="isian_singkat" {{ ($soal->tipe_soal ?? old('tipe_soal')) == 'isian_singkat' ? 'selected' : '' }}>Isian Singkat</option>
                                <option value="uraian" {{ ($soal->tipe_soal ?? old('tipe_soal')) == 'uraian' ? 'selected' : '' }}>Uraian / Essay</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Bobot Nilai <span class="text-danger">*</span></label>
                            <input type="number" name="bobot" class="form-control" value="{{ $soal->bobot ?? old('bobot', 10) }}" min="1" required>
                            <small class="text-muted">Nilai jika jawaban benar</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">No. Urut <span class="text-danger">*</span></label>
                            <input type="number" name="urutan" class="form-control" value="{{ $soal->urutan ?? old('urutan', $ujian->soal()->count() + 1) }}" min="1" required>
                        </div>
                    </div>

                    {{-- Pertanyaan --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold">Pertanyaan <span class="text-danger">*</span></label>
                        <textarea name="pertanyaan" class="form-control" rows="4" required>{{ $soal->pertanyaan ?? old('pertanyaan') }}</textarea>
                    </div>

                    {{-- Container Pilihan Jawaban (Akan berubah sesuai JS) --}}
                    <div id="pilihanContainer" class="mb-4 p-3 bg-light rounded border">
                        <h6 class="fw-bold border-bottom pb-2 mb-3">Opsi Jawaban & Kunci</h6>
                        
                        {{-- 1. Pilihan Ganda (Single) --}}
                        <div id="sectionPilgan" class="tipe-section" style="display:none;">
                            @php 
                                $pilganOpts = ['A','B','C','D','E']; 
                                $existingPilgan = json_decode($soal->pilihan_jawaban ?? '{}', true);
                                $kunciPilgan = $soal->kunci_jawaban ?? '';
                            @endphp
                            @foreach($pilganOpts as $opt)
                            <div class="input-group mb-2">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="radio" name="kunci_jawaban_pilgan" value="{{ $opt }}" {{ $kunciPilgan == $opt ? 'checked' : '' }}>
                                    <span class="ms-2 fw-bold">{{ $opt }}</span>
                                </div>
                                <input type="text" name="pilihan_jawaban_pilgan[{{ $opt }}]" class="form-control" value="{{ $existingPilgan[$opt] ?? '' }}" placeholder="Teks jawaban opsi {{ $opt }}...">
                            </div>
                            @endforeach
                            <small class="text-info"><i class="fas fa-info-circle"></i> Pilih radio button untuk menentukan kunci jawaban yang benar.</small>
                        </div>

                        {{-- 2. Pilihan Ganda Kompleks (Multiple) --}}
                        <div id="sectionPilganKompleks" class="tipe-section" style="display:none;">
                            @php 
                                $existingKompleks = json_decode($soal->pilihan_jawaban ?? '{}', true);
                                $kunciKompleks = json_decode($soal->kunci_jawaban ?? '[]', true) ?? [];
                            @endphp
                            @foreach($pilganOpts as $opt)
                            <div class="input-group mb-2">
                                <div class="input-group-text">
                                    <input class="form-check-input mt-0" type="checkbox" name="kunci_jawaban_kompleks[]" value="{{ $opt }}" {{ in_array($opt, $kunciKompleks) ? 'checked' : '' }}>
                                    <span class="ms-2 fw-bold">{{ $opt }}</span>
                                </div>
                                <input type="text" name="pilihan_jawaban_kompleks[{{ $opt }}]" class="form-control" value="{{ $existingKompleks[$opt] ?? '' }}" placeholder="Teks jawaban opsi {{ $opt }}...">
                            </div>
                            @endforeach
                            <small class="text-info"><i class="fas fa-info-circle"></i> Centang kotak untuk menentukan semua jawaban yang benar (lebih dari satu).</small>
                        </div>

                        {{-- 3. Benar Salah --}}
                        <div id="sectionBenarSalah" class="tipe-section" style="display:none;">
                            <table class="table table-bordered bg-white">
                                <thead>
                                    <tr>
                                        <th>Pernyataan</th>
                                        <th width="150" class="text-center">Kunci Jawaban</th>
                                    </tr>
                                </thead>
                                <tbody id="bsTbody">
                                    {{-- JS generated rows or loops --}}
                                    @php
                                        $existingBS = json_decode($soal->pilihan_jawaban ?? '[]', true) ?? [['pernyataan' => '', 'kunci' => 'B']];
                                        // Format storage: pilihan_jawaban = [{pernyataan: "...", kunci: "B"}, ...]
                                    @endphp
                                    @foreach($existingBS as $idx => $bs)
                                    <tr>
                                        <td>
                                            <input type="text" name="pilihan_jawaban_bs[{{ $idx }}][pernyataan]" class="form-control" value="{{ $bs['pernyataan'] ?? ($bs['text'] ?? '') }}" placeholder="Tulis pernyataan...">
                                        </td>
                                        <td class="text-center align-middle">
                                            <select name="pilihan_jawaban_bs[{{ $idx }}][kunci]" class="form-select form-select-sm">
                                                <option value="B" {{ ($bs['kunci'] ?? '') == 'B' ? 'selected' : '' }}>Benar</option>
                                                <option value="S" {{ ($bs['kunci'] ?? '') == 'S' ? 'selected' : '' }}>Salah</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                    {{-- Add more rows logic handled by simple array hardcode or JS --}}
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="addBsRow"><i class="fas fa-plus"></i> Tambah Pernyataan</button>
                        </div>

                        {{-- 4. Isian Singkat --}}
                        <div id="sectionIsian" class="tipe-section" style="display:none;">
                            <label class="form-label text-muted">Kunci Jawaban Singkat</label>
                            <input type="text" name="kunci_jawaban_isian" class="form-control" value="{{ $soal->kunci_jawaban ?? '' }}" placeholder="Contoh: Soekarno">
                            <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Jawaban siswa harus persis sama (case-insensitive).</small>
                        </div>

                        {{-- 5. Uraian --}}
                        <div id="sectionUraian" class="tipe-section" style="display:none;">
                            <div class="alert alert-info border-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Untuk soal uraian, guru harus melakukan koreksi manual.
                            </div>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-light border">Reset</button>
                        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save me-1"></i> Simpan Soal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tipeSelect = document.getElementById('tipeSoal');
    const sections = {
        'pilihan_ganda': document.getElementById('sectionPilgan'),
        'pilihan_ganda_kompleks': document.getElementById('sectionPilganKompleks'),
        'benar_salah': document.getElementById('sectionBenarSalah'),
        'isian_singkat': document.getElementById('sectionIsian'),
        'uraian': document.getElementById('sectionUraian'),
    };

    function showSection(type) {
        // Hide all
        Object.values(sections).forEach(el => el.style.display = 'none');
        // Show selected
        if(sections[type]) sections[type].style.display = 'block';
    }

    tipeSelect.addEventListener('change', function() {
        showSection(this.value);
    });

    // Init
    showSection(tipeSelect.value);

    // Benar Salah Row Adder
    let bsIdx = {{ count($existingBS ?? []) }};
    const addBsBtn = document.getElementById('addBsRow');
    const bsTbody = document.getElementById('bsTbody');
    
    if (addBsBtn && bsTbody) {
        addBsBtn.addEventListener('click', function() {
            const row = `
                <tr>
                    <td>
                        <input type="text" name="pilihan_jawaban_bs[${bsIdx}][pernyataan]" class="form-control" placeholder="Tulis pernyataan...">
                    </td>
                    <td class="text-center align-middle">
                        <select name="pilihan_jawaban_bs[${bsIdx}][kunci]" class="form-select form-select-sm">
                            <option value="B">Benar</option>
                            <option value="S">Salah</option>
                        </select>
                    </td>
                </tr>
            `;
            bsTbody.insertAdjacentHTML('beforeend', row);
            bsIdx++;
        });
    }
});
</script>
@endpush
@endsection
