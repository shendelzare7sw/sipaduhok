{{-- Multi-Kelas Selector Partial --}}
{{-- Required: $kelasLain (collection of GuruPengajarKelas with kelas relation) --}}
@if(isset($kelasLain) && $kelasLain->count() > 0)
@once
    @push('scripts')
        @vite(['resources/js/guru/partials/multi-kelas-selector.js'])
    @endpush
@endonce

@php
    $ids = isset($relatedClassIds) ? $relatedClassIds : [];
    $hasRelated = !empty($ids);
@endphp

<div class="card border-primary mb-3" data-multi-kelas-selector>
    <div class="card-header bg-light py-2">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="enableMultiKelas" data-multi-kelas-toggle
            {{ (isset($relatedClassIds) && !empty($relatedClassIds)) ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="enableMultiKelas">
                <i class="fas fa-copy me-1"></i> Tambahkan juga ke kelas lain
            </label>
        </div>
    </div>
    <div class="card-body py-2 {{ $hasRelated ? '' : 'd-none' }}" id="multiKelasBody" data-multi-kelas-body>
        <small class="text-muted d-block mb-2">Konten akan diduplikasi ke kelas yang dipilih (mata pelajaran yang sama):</small>
        
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
                    data-kelas-lain-checkbox
                    {{ $isChecked ? 'checked' : '' }}>
                <label class="form-check-label" for="kelasLain{{ $gpk->kelas_id }}">
                    {{ $gpk->kelas->nama_kelas ?? 'Kelas #'.$gpk->kelas_id }}
                    @if($isChecked)
                        <span class="badge bg-info text-dark ms-1 small">Terkait</span>
                    @endif
                </label>
            </div>
        @endforeach
        <div class="mt-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle-all-kelas="true">Pilih Semua</button>
            <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle-all-kelas="false">Batal Semua</button>
        </div>
    </div>
</div>
@endif
