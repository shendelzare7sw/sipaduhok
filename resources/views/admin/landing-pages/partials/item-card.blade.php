<div class="card mb-4 border shadow-sm item-wrapper {{ $isTemplate ? '' : '' }}">
    <div class="card-header bg-light d-flex justify-content-between align-items-center py-2">
        <h6 class="mb-0 text-muted">
            <span class="fw-bold">
                @if($isTemplate)
                    <i class="bx bx-menu me-1"></i> Item Baru
                @else
                    Item #{{ $itemIndex + 1 }}
                @endif
            </span>
        </h6>
        @if($canAddRemove)
            <button type="button" class="btn btn-sm btn-outline-danger remove-item" title="Hapus Item">
                <i class="bx bx-trash"></i>
            </button>
        @endif
    </div>
    <div class="card-body">
        @php $hasVisual = !empty($visualFields); @endphp
        <div class="row g-4">
            {{-- Visual Column (Left) — hanya tampil jika ada field visual --}}
            @if($hasVisual)
            <div class="col-md-4 border-end">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Tampilan & Icon</h6>

                @foreach($visualFields as $key => $value)
                    <div class="mb-3">
                        <label class="form-label fw-semibold small text-muted">
                            @php
                                $label = match($key) {
                                    'icon_color' => 'Warna Icon',
                                    'card_color' => 'Warna Card',
                                    'card_gradient_start' => 'Gradient Awal',
                                    'card_gradient_end' => 'Gradient Akhir',
                                    'background_image' => 'Gambar Latar',
                                    'image' => 'Gambar',
                                    'icon' => 'Icon / Logo',
                                    'color' => 'Warna Tema',
                                    default => ucwords(str_replace('_', ' ', $key))
                                };
                            @endphp
                            {{ $label }}
                        </label>
                        
                        @if($key === 'image' || str_contains($key, 'image') || $key === 'icon')
                            <div class="text-center mb-2 p-2 bg-light rounded border">
                                @if($value)
                                    <input type="hidden" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}" {{ $isTemplate ? 'disabled' : '' }}>
                                @endif

                                @if($value && (str_contains($value, '/') || str_contains($value, '.')))
                                    <img src="{{ asset($value) }}" alt="Preview" class="img-fluid" style="max-height: 80px; object-fit: contain;">
                                @elseif($value && $key === 'icon')
                                    <div class="fs-1 text-primary">
                                        <i class="{{ $value }}"></i>
                                    </div>
                                    <small class="d-block text-muted mt-1">{{ $value }}</small>
                                @else
                                    <span class="text-muted small fst-italic">Belum ada {{ $key === 'icon' ? 'icon' : 'gambar' }}</span>
                                @endif
                            </div>
                            <input type="file" class="form-control form-control-sm" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" accept="image/*" {{ $isTemplate ? 'disabled' : '' }}>
                        
                        @elseif(str_contains($key, 'color') || str_contains($key, 'gradient') || $key === 'color')
                            <div class="input-group input-group-sm">
                                @php
                                    $colorVal = $value;
                                    if(empty($colorVal) || substr($colorVal, 0, 1) !== '#' || strlen($colorVal) !== 7) {
                                        $colorVal = '#165fac';
                                    }
                                @endphp
                                <input type="color" class="form-control form-control-color w-100" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $colorVal }}" title="Pilih Warna" {{ $isTemplate ? 'disabled' : '' }}>
                            </div>
                        @else
                             <input type="text" class="form-control" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}" {{ $isTemplate ? 'disabled' : '' }}>
                        @endif
                    </div>
                @endforeach
            </div>
            @endif

            {{-- Content Column (Right) --}}
            <div class="{{ $hasVisual ? 'col-md-8' : 'col-12' }}">
                <h6 class="text-muted small text-uppercase fw-bold mb-3">Konten Text</h6>
                <div class="row g-3">
                    @foreach($textFields as $key => $value)
                        {{-- Skip array values just in case --}}
                        @if(is_array($value)) @continue @endif
                        
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                {{ ucwords(str_replace('_', ' ', $key)) }}
                            </label>
                            @if(str_contains($key, 'description') || (is_string($value) && strlen($value) > 60))
                                <textarea class="form-control" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" rows="3" {{ $isTemplate ? 'disabled' : '' }}>{{ $value }}</textarea>
                            @else
                                <input type="text" class="form-control" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}" {{ $isTemplate ? 'disabled' : '' }}>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
