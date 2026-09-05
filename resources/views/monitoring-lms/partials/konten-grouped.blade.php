@php
    $items = $items ?? collect();
    $grouped = $items->groupBy('mata_pelajaran_id');
    $iconTone = $iconTone ?? 'bg-brand-50 text-brand-700';
    $badgeTone = $badgeTone ?? 'bg-brand-50 text-brand-700';
@endphp

@if($items->isEmpty())
    <div class="flex min-h-52 flex-col items-center justify-center rounded-2xl border border-dashed border-slate-200 bg-slate-50 p-6 text-center"><span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white text-lg text-slate-400 shadow-sm"><i class="fas fa-folder-open" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">{{ $emptyText ?? 'Belum ada konten.' }}</h3><p class="mt-1 text-xs text-slate-500">Konten akan muncul setelah guru mengunggahnya.</p></div>
@else
    <div class="space-y-4">
        @foreach($grouped as $mapelItems)
            @php
                $firstItem = $mapelItems->first();
                $mapel = $firstItem->mataPelajaran ?? null;
            @endphp
            <section class="overflow-hidden rounded-2xl border border-slate-200">
                <header class="flex items-center gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-brand-600 shadow-sm ring-1 ring-slate-200"><i class="fas fa-book-open" aria-hidden="true"></i></span><div class="min-w-0"><h3 class="truncate text-sm font-extrabold text-slate-900" title="{{ $mapel->nama_mapel ?? 'Mata Pelajaran' }}">{{ $mapel->nama_mapel ?? 'Mata Pelajaran' }}</h3><p class="mt-0.5 text-[10px] font-semibold text-slate-500">{{ $mapelItems->count() }} konten</p></div></header>
                <div class="divide-y divide-slate-100">
                    @foreach($mapelItems as $item)
                        @php
                            $judul = $item->{$titleField} ?? '-';
                            $tanggal = $item->{$dateField} ?? null;
                            $resolvedBadge = is_callable($badgeText) ? $badgeText($item) : $badgeText;
                            $noteType = $type === 'latihan' ? 'ujian' : $type;
                        @endphp
                        <article class="flex min-w-0 flex-col gap-3 p-4 sm:flex-row sm:items-center">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $iconTone }}"><i class="fas {{ $iconClass }}" aria-hidden="true"></i></span>
                            <div class="min-w-0 flex-1"><h4 class="break-words text-sm font-extrabold leading-5 text-slate-900">{{ $judul }}</h4><div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[10px] text-slate-500"><span class="rounded-full px-2 py-1 font-bold {{ $badgeTone }}">{{ $resolvedBadge }}</span>@if($item->guru)<span><i class="fas fa-user-tie mr-1 text-slate-400" aria-hidden="true"></i>{{ $item->guru->nama_lengkap }}</span>@endif @if($tanggal)<span><i class="fas fa-calendar-day mr-1 text-slate-400" aria-hidden="true"></i>{{ $dateLabel }}: {{ \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat('d M Y') }}</span>@endif @if($type === 'ujian' && isset($item->soal_ujian_count))<span><i class="fas fa-list-ol mr-1 text-slate-400" aria-hidden="true"></i>{{ $item->soal_ujian_count }} soal</span>@endif</div></div>
                            <div class="grid grid-cols-2 gap-2 sm:flex sm:shrink-0"><a href="{{ route($baseRoute.'.preview', [$type, $item->id]) }}" target="_blank" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-blue-50 px-3 text-xs font-bold text-blue-700 no-underline ring-1 ring-inset ring-blue-100 hover:bg-blue-100"><i class="fas fa-eye" aria-hidden="true"></i>Preview</a><button type="button" data-konten-type="{{ $noteType }}" data-konten-id="{{ $item->id }}" data-konten-label="{{ $resolvedBadge }}" data-konten-judul="{{ $judul }}" @click="$dispatch('monitoring-note', { type: $el.dataset.kontenType, id: $el.dataset.kontenId, label: $el.dataset.kontenLabel, title: $el.dataset.kontenJudul })" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-amber-50 px-3 text-xs font-bold text-amber-700 ring-1 ring-inset ring-amber-100 hover:bg-amber-100"><i class="fas fa-comment-dots" aria-hidden="true"></i>Catatan</button></div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach
    </div>
@endif
