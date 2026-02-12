{{-- Multi-Kelas Selector Partial --}}
{{-- Required: $kelasLain (collection of GuruPengajarKelas with kelas relation) --}}
@if(isset($kelasLain) && $kelasLain->count() > 0)
<div class="card border-primary mb-3">
    <div class="card-header bg-light py-2">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="enableMultiKelas" onchange="toggleMultiKelas(this)"
            {{ (isset($relatedClassIds) && !empty($relatedClassIds)) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="enableMultiKelas">
                <i class="fas fa-copy me-1"></i> Tambahkan juga ke kelas lain
            </label>
        </div>
    </div>
    <div class="card-body py-2" id="multiKelasBody" style="display: {{ (isset($relatedClassIds) && !empty($relatedClassIds)) ? 'block' : 'none' }};">
        <small class="text-muted d-block mb-2">Konten akan diduplikasi ke kelas yang dipilih (mata pelajaran yang sama):</small>
        @php
            $ids = isset($relatedClassIds) ? $relatedClassIds : [];
            $hasRelated = !empty($ids);
        @endphp
        
        @if($hasRelated)
            <div class="alert alert-info py-2 small mb-2">
                <i class="fas fa-info-circle me-1"></i> Item ini terdeteksi juga ada di <b>{{ count($ids) }}</b> kelas lain.
                Checkbox kelas terkait telah dicentang otomatis untuk memudahkan sinkronisasi update.
            </div>
        @endif

        @foreach($kelasLain as $gpk)
            @php
                $isChecked = in_array($gpk->kelas_id, $ids);
            @endphp
            <div class="form-check">
                <input class="form-check-input kelas-lain-cb" type="checkbox" name="kelas_tambahan[]"
                    value="{{ $gpk->kelas_id }}" id="kelasLain{{ $gpk->kelas_id }}"
                    {{ $isChecked ? 'checked' : '' }}>
                <label class="form-check-label" for="kelasLain{{ $gpk->kelas_id }}">
                    {{ $gpk->kelas->nama_kelas ?? 'Kelas #'.$gpk->kelas_id }}
                    @if($isChecked)
                        <span class="badge bg-info text-dark ms-1" style="font-size: 0.7em;">Terkait</span>
                    @endif
                </label>
            </div>
        @endforeach
        <div class="mt-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllKelas(true)">Pilih Semua</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleAllKelas(false)">Batal Semua</button>
        </div>
    </div>
</div>
<script>
function toggleMultiKelas(cb) {
    document.getElementById('multiKelasBody').style.display = cb.checked ? 'block' : 'none';
    if (!cb.checked) {
        document.querySelectorAll('.kelas-lain-cb').forEach(c => c.checked = false);
    }
}
function toggleAllKelas(state) {
    document.querySelectorAll('.kelas-lain-cb').forEach(c => c.checked = state);
}
</script>
@endif
