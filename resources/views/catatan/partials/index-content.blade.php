@php
    $catatanItems = is_object($catatan) && method_exists($catatan, 'getCollection') ? $catatan->getCollection() : collect($catatan);
    $totalCatatan = is_object($catatan) && method_exists($catatan, 'total') ? $catatan->total() : $catatanItems->count();
    $totalSemua = $catatanItems->where('tipe_penerima', 'semua')->count();
    $totalMendesak = $catatanItems->where('prioritas', 'mendesak')->count();
    $totalPembaca = $catatanItems->sum(fn ($item) => $item->relationLoaded('pembaca') ? $item->pembaca->count() : $item->totalPembaca());
    $priorityTone = [
        'biasa' => 'border-blue-500 bg-blue-50 text-blue-700',
        'penting' => 'border-amber-500 bg-amber-50 text-amber-700',
        'mendesak' => 'border-red-500 bg-red-50 text-red-700',
    ];
    $priorityBorder = [
        'biasa' => 'border-l-blue-500',
        'penting' => 'border-l-amber-500',
        'mendesak' => 'border-l-red-500',
    ];
@endphp

<div class="min-w-0 w-full space-y-5">
    <section class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5">
        <div class="flex min-w-0 items-start gap-3">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-600 to-cyan-500 text-white">
                <i class="fas fa-clipboard-list" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <h2 class="text-base font-extrabold text-slate-900 sm:text-lg">Manajemen Catatan</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">{{ $toolbarDescription }}</p>
            </div>
        </div>
        <a href="{{ route($routePrefix . '.catatan.create') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-brand-700">
            <i class="fas fa-plus" aria-hidden="true"></i> Buat Catatan
        </a>
    </section>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4" aria-label="Ringkasan catatan">
        @foreach([
            ['Total Catatan', $totalCatatan, 'Semua riwayat', 'fa-paper-plane', 'bg-blue-50 text-blue-600'],
            ['Publik', $totalSemua, 'Ke semua pengguna', 'fa-bullhorn', 'bg-emerald-50 text-emerald-600'],
            ['Total Dibaca', $totalPembaca, 'Akumulasi halaman ini', 'fa-eye', 'bg-amber-50 text-amber-600'],
            ['Mendesak', $totalMendesak, 'Perlu perhatian', 'fa-triangle-exclamation', 'bg-violet-50 text-violet-600'],
        ] as [$label, $value, $help, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold leading-none text-slate-900 sm:text-2xl">{{ number_format($value) }}</p>
                        <p class="mt-2 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $label }}</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span>
                </div>
                <p class="mt-3 truncate text-[10px] text-slate-400 sm:text-xs">{{ $help }}</p>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900">
                <i class="fas fa-history text-brand-600" aria-hidden="true"></i>{{ $listTitle }}
            </h2>
        </header>

        @if($catatanItems->isNotEmpty())
            <div class="divide-y divide-slate-100 p-3 sm:p-4">
                @foreach($catatanItems as $item)
                    @php
                        $sentAt = $item->tanggal_kirim ? \Carbon\Carbon::parse($item->tanggal_kirim) : $item->created_at;
                        $readCount = $item->relationLoaded('pembaca') ? $item->pembaca->count() : $item->totalPembaca();
                        $priority = $item->prioritas ?: 'biasa';
                        $isSentByCurrentUser = (int) $item->pengirim_id === (int) auth()->id();
                    @endphp
                    <article class="my-3 min-w-0 rounded-2xl border border-l-4 border-slate-200 bg-white p-4 shadow-sm {{ $priorityBorder[$priority] ?? $priorityBorder['biasa'] }} sm:p-5">
                        <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <h3 class="break-words text-sm font-extrabold leading-6 text-slate-900 sm:text-base">{{ $item->judul }}</h3>
                                <div class="mt-2 flex flex-wrap gap-x-3 gap-y-1 text-[11px] text-slate-500">
                                    <span><i class="far fa-clock mr-1" aria-hidden="true"></i>{{ $sentAt?->format('d M Y, H:i') }}</span>
                                    @if($showDirection)
                                        <span><i class="fas {{ $isSentByCurrentUser ? 'fa-paper-plane' : 'fa-inbox' }} mr-1" aria-hidden="true"></i>{{ $isSentByCurrentUser ? 'Terkirim' : 'Dari ' . ($item->pengirim->name ?? '-') }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-wrap gap-2 sm:max-w-[45%] sm:justify-end">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-[10px] font-bold {{ $priorityTone[$priority] ?? $priorityTone['biasa'] }}">{{ ucfirst($priority) }}</span>
                                @if($item->tipe_penerima === 'semua')
                                    <span class="inline-flex min-w-0 items-center gap-1 rounded-full bg-blue-50 px-2.5 py-1 text-[10px] font-bold text-blue-700 ring-1 ring-blue-200"><i class="fas fa-users" aria-hidden="true"></i><span class="truncate">Semua Pengguna</span></span>
                                @elseif($item->tipe_penerima === 'role')
                                    <span class="inline-flex min-w-0 items-center gap-1 rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200"><i class="fas fa-user-tag" aria-hidden="true"></i><span class="truncate">{{ ucwords(str_replace('_', ' ', $item->role_penerima)) }}</span></span>
                                @else
                                    <span class="inline-flex min-w-0 items-center gap-1 rounded-full bg-violet-50 px-2.5 py-1 text-[10px] font-bold text-violet-700 ring-1 ring-violet-200"><i class="fas fa-user" aria-hidden="true"></i><span class="truncate">{{ $item->penerima->name ?? 'Individu' }}</span></span>
                                @endif
                            </div>
                        </div>

                        <p class="mt-4 break-words text-sm leading-6 text-slate-600">{{ \Illuminate\Support\Str::limit($item->isi_catatan, 220) }}</p>

                        <footer class="mt-4 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-bold text-emerald-700 ring-1 ring-emerald-200">
                                <i class="fas fa-chart-line" aria-hidden="true"></i> Dibaca {{ number_format($readCount) }} pengguna
                            </span>
                            <div class="grid grid-cols-2 gap-2 sm:flex">
                                <a href="{{ route($routePrefix . '.catatan.show', $item->id) }}" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200">
                                    <i class="fas fa-eye" aria-hidden="true"></i> Detail
                                </a>
                                @if(!$showDirection || $isSentByCurrentUser)
                                    <form action="{{ route($routePrefix . '.catatan.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus catatan?" data-confirm-message="Catatan {{ $item->judul }} akan dihapus dari riwayat. Catatan yang sudah diterima pengguna tidak terpengaruh." data-confirm-text="Ya, hapus catatan">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-3 text-xs font-bold text-red-700 hover:bg-red-100">
                                            <i class="fas fa-trash" aria-hidden="true"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </footer>
                    </article>
                @endforeach
            </div>

            @if(is_object($catatan) && method_exists($catatan, 'hasPages') && $catatan->hasPages())
                <footer class="border-t border-slate-200 px-4 py-3">{{ $catatan->links() }}</footer>
            @endif
        @else
            <div class="px-5 py-14 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-blue-50 text-xl text-brand-600"><i class="fas fa-inbox" aria-hidden="true"></i></span>
                <h3 class="mt-4 text-sm font-extrabold text-slate-800">{{ $emptyTitle }}</h3>
                <p class="mt-1 text-xs text-slate-500">{{ $emptyDescription }}</p>
                <a href="{{ route($routePrefix . '.catatan.create') }}" class="mt-4 inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i> Kirim Catatan Pertama</a>
            </div>
        @endif
    </section>
</div>
