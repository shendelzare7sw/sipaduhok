<article class="item-wrapper overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <header class="flex items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3">
        <h4 class="item-title text-xs font-extrabold uppercase tracking-wide text-slate-600">{{ $isTemplate ? 'Item baru' : 'Item #'.($itemIndex + 1) }}</h4>
        @if($canAddRemove)
            <button type="button" class="remove-item flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-700 ring-1 ring-red-100 hover:bg-red-100" title="Hapus item" aria-label="Hapus item"><i class="fas fa-trash" aria-hidden="true"></i></button>
        @endif
    </header>

    @php $hasVisual = !empty($visualFields); @endphp
    <div class="grid gap-5 p-4 {{ $hasVisual ? 'md:grid-cols-[minmax(12rem,.7fr)_minmax(0,1.3fr)]' : '' }} sm:p-5">
        @if($hasVisual)
            <section class="min-w-0 md:border-r md:border-slate-200 md:pr-5">
                <h5 class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Tampilan dan ikon</h5>
                <div class="space-y-4">
                    @foreach($visualFields as $key => $value)
                        @php
                            $fieldLabel = match($key) {
                                'icon_color' => 'Warna ikon',
                                'card_color' => 'Warna kartu',
                                'card_gradient_start' => 'Gradasi awal',
                                'card_gradient_end' => 'Gradasi akhir',
                                'background_image' => 'Gambar latar',
                                'image' => 'Gambar',
                                'icon' => 'Ikon / logo',
                                'color' => 'Warna tema',
                                default => ucwords(str_replace('_', ' ', $key)),
                            };
                        @endphp
                        <label class="block min-w-0">
                            <span class="mb-1.5 block text-xs font-bold text-slate-700">{{ $fieldLabel }}</span>
                            @if($key === 'image' || str_contains($key, 'image') || $key === 'icon')
                                <span data-image-preview class="mb-2 flex min-h-20 items-center justify-center overflow-hidden rounded-xl border border-dashed border-slate-300 bg-slate-50 p-2 text-center">
                                    @if($value)
                                        <input type="hidden" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}" {{ $isTemplate ? 'disabled' : '' }}>
                                    @endif
                                    @if($value && (str_contains($value, '/') || str_contains($value, '.')))
                                        <img src="{{ asset($value) }}" alt="Pratinjau {{ strtolower($fieldLabel) }}" class="max-h-24 max-w-full object-contain" x-on:error="$el.hidden = true">
                                    @elseif($value && $key === 'icon')
                                        <span class="text-brand-700"><i class="{{ $value }} text-3xl" aria-hidden="true"></i><small class="mt-1 block text-[10px] text-slate-500">{{ $value }}</small></span>
                                    @else
                                        <span class="text-xs italic text-slate-400">Belum ada {{ $key === 'icon' ? 'ikon' : 'gambar' }}</span>
                                    @endif
                                </span>
                                <input type="file" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" accept="image/*" @change="previewImage($event)" class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-brand-700" {{ $isTemplate ? 'disabled' : '' }}>
                            @elseif(str_contains($key, 'color') || str_contains($key, 'gradient') || $key === 'color')
                                @php $colorValue = (!empty($value) && str_starts_with($value, '#') && strlen($value) === 7) ? $value : '#165fac'; @endphp
                                <span class="flex h-11 items-center gap-3 rounded-xl border border-slate-300 bg-white px-2"><input type="color" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $colorValue }}" class="h-8 w-full cursor-pointer rounded-lg border-0 bg-transparent" title="Pilih warna" {{ $isTemplate ? 'disabled' : '' }}></span>
                            @else
                                <input type="text" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" {{ $isTemplate ? 'disabled' : '' }}>
                            @endif
                        </label>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="min-w-0">
            <h5 class="mb-4 text-[10px] font-extrabold uppercase tracking-widest text-slate-400">Isi konten</h5>
            <div class="space-y-4">
                @foreach($textFields as $key => $value)
                    @if(is_array($value)) @continue @endif
                    <label class="block min-w-0">
                        <span class="mb-1.5 block text-xs font-bold text-slate-700">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                        @if(str_contains($key, 'description') || (is_string($value) && strlen($value) > 60))
                            <textarea name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" rows="3" class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm leading-6 text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" {{ $isTemplate ? 'disabled' : '' }}>{{ $value }}</textarea>
                        @else
                            <input type="text" name="sections[{{ $section->id }}][items][{{ $itemIndex }}][{{ $key }}]" value="{{ $value }}" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" {{ $isTemplate ? 'disabled' : '' }}>
                        @endif
                    </label>
                @endforeach
            </div>
        </section>
    </div>
</article>
