@php
    $userRole = auth()->user()->role ?? 'siswa';
    $ctx = request('ctx');
    $layout = match ($userRole) {
        'siswa' => $ctx === 'lms' ? 'layouts.lms' : 'layouts.app',
        'guru_pengajar' => $ctx === 'lms-guru' ? 'layouts.lms-guru' : 'layouts.app',
        default => 'layouts.app',
    };
    $sidebarPartial = match (true) {
        $userRole === 'siswa' && $ctx === 'lms' => 'siswa.partials.sidebar-lms',
        $userRole === 'siswa' => 'siswa.partials.sidebar-sia',
        $userRole === 'guru_pengajar' && $ctx === 'lms-guru' => 'guru.partials.sidebar-lms-notif',
        $userRole === 'guru_pengajar' => 'guru.partials.sidebar',
        $userRole === 'admin' => 'admin.partials.cleanflow-sidebar',
        $userRole === 'bendahara' => 'bendahara.partials.sidebar',
        $userRole === 'wali_kelas' => 'wali-kelas.partials.sidebar',
        $userRole === 'ketua_pkbm' => 'ketua.partials.sidebar',
        $userRole === 'wakil_kepala_sekolah' => 'waka.partials.sidebar',
        $userRole === 'sekretaris' => 'sekretaris.partials.sidebar',
        $userRole === 'orang_tua' => 'wali-siswa.partials.sidebar',
        default => 'admin.partials.cleanflow-sidebar',
    };
    $typeLabels = [
        'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian', 'forum' => 'Forum',
        'pengumuman' => 'Pengumuman', 'deadline' => 'Tenggat', 'nilai' => 'Nilai', 'izin' => 'Izin',
        'catatan' => 'Catatan', 'pembayaran' => 'Keuangan', 'rapor' => 'Rapor', 'sistem' => 'Sistem',
        'kelas' => 'Kelas', 'kenaikan' => 'Kenaikan', 'recovery' => 'Pemulihan',
    ];
    $toneClasses = [
        'primary' => ['icon' => 'bg-blue-50 text-blue-600 ring-blue-100', 'badge' => 'bg-blue-50 text-blue-700'],
        'success' => ['icon' => 'bg-emerald-50 text-emerald-600 ring-emerald-100', 'badge' => 'bg-emerald-50 text-emerald-700'],
        'danger' => ['icon' => 'bg-red-50 text-red-600 ring-red-100', 'badge' => 'bg-red-50 text-red-700'],
        'warning' => ['icon' => 'bg-amber-50 text-amber-600 ring-amber-100', 'badge' => 'bg-amber-50 text-amber-700'],
        'info' => ['icon' => 'bg-cyan-50 text-cyan-600 ring-cyan-100', 'badge' => 'bg-cyan-50 text-cyan-700'],
        'secondary' => ['icon' => 'bg-slate-100 text-slate-600 ring-slate-200', 'badge' => 'bg-slate-100 text-slate-700'],
    ];
    $pageIds = $notifications->pluck('id')->map(fn ($id) => (string) $id)->values();
    $indexParams = fn (array $overrides = [], array $except = ['page']) => array_merge(
        request()->except($except),
        $ctx ? ['ctx' => $ctx] : [],
        $overrides,
    );
@endphp

@extends($layout)
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Pusat pesan dan pembaruan sistem')

@section('sidebar-menu')
    @include($sidebarPartial)
@endsection

@section('content')
<div
    data-notifications-page
    class="min-w-0 space-y-4"
    x-data="{
        selected: [],
        pageIds: @js($pageIds),
        action: '',
        get allSelected() { return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id)) },
        toggleAll() { this.selected = this.allSelected ? [] : [...this.pageIds] },
        async submitBulk(action) {
            if (!this.selected.length) return;
            if (action === 'delete') {
                const result = await window.CleanFlow.confirmAction({
                    title: 'Hapus notifikasi terpilih?',
                    text: `${this.selected.length} notifikasi akan dihapus permanen.`,
                    confirmText: 'Ya, hapus'
                });
                if (!result.isConfirmed) return;
            }
            this.action = action;
            this.$nextTick(() => this.$refs.bulkForm.requestSubmit());
        }
    }"
>
    <section class="rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 border-b border-slate-200 px-4 py-4 sm:px-5 lg:flex-row lg:items-center lg:justify-between">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                    <i class="fas fa-bell" aria-hidden="true"></i>
                </span>
                <div class="min-w-0">
                    <h2 class="text-base font-extrabold text-slate-900 sm:text-lg">Pusat Notifikasi</h2>
                    <p class="mt-0.5 text-xs text-slate-500">
                        @if($unreadCount > 0)
                            <span class="font-bold text-blue-700">{{ $unreadCount }} belum dibaca</span> dari pesan terbaru Anda.
                        @else
                            Semua pesan sudah dibaca.
                        @endif
                    </p>
                </div>
            </div>

            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 text-xs font-bold text-blue-700 transition hover:border-blue-300 hover:bg-blue-100 sm:w-auto">
                        <i class="fas fa-check-double" aria-hidden="true"></i> Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <form method="GET" action="{{ route('notifications.index') }}" class="grid min-w-0 grid-cols-1 gap-3 border-b border-slate-200 bg-slate-50/70 p-4 sm:p-5 xl:grid-cols-[minmax(16rem,1fr)_auto_auto] xl:items-center">
            @if($ctx)<input type="hidden" name="ctx" value="{{ $ctx }}">@endif
            @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif

            <label class="flex min-h-11 min-w-0 items-center gap-3 rounded-xl border border-slate-200 bg-white px-3 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20">
                <i class="fas fa-search shrink-0 text-sm text-slate-400" aria-hidden="true"></i>
                <span class="sr-only">Cari notifikasi</span>
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari judul atau isi pesan..." class="min-w-0 flex-1 border-0 bg-transparent p-0 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0">
            </label>

            <div class="grid grid-cols-2 gap-2 sm:flex sm:items-center">
                <label class="min-w-0">
                    <span class="sr-only">Dari tanggal</span>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="min-h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 sm:w-auto">
                </label>
                <label class="min-w-0">
                    <span class="sr-only">Sampai tanggal</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="min-h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 sm:w-auto">
                </label>
            </div>

            <div class="grid grid-cols-2 gap-2 sm:flex">
                <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white transition hover:bg-slate-900">
                    <i class="fas fa-filter" aria-hidden="true"></i> Terapkan
                </button>
                @if(request()->hasAny(['search', 'filter', 'date_from', 'date_to']))
                    <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 no-underline transition hover:bg-slate-100">
                        <i class="fas fa-rotate-left" aria-hidden="true"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        <div class="flex flex-col gap-3 border-b border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <nav class="grid grid-cols-3 rounded-xl bg-slate-100 p-1 text-xs font-bold" aria-label="Status notifikasi">
                <a href="{{ route('notifications.index', $indexParams([], ['filter', 'page'])) }}" class="rounded-lg px-3 py-2 text-center no-underline transition {{ !request('filter') ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">Semua</a>
                <a href="{{ route('notifications.index', $indexParams(['filter' => 'unread'], ['filter', 'page'])) }}" class="rounded-lg px-3 py-2 text-center no-underline transition {{ request('filter') === 'unread' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">Belum dibaca</a>
                <a href="{{ route('notifications.index', $indexParams(['filter' => 'read'], ['filter', 'page'])) }}" class="rounded-lg px-3 py-2 text-center no-underline transition {{ request('filter') === 'read' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-500 hover:text-slate-800' }}">Sudah dibaca</a>
            </nav>

            @if($notifications->isNotEmpty())
                <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-semibold text-slate-600">
                    <input type="checkbox" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500" :checked="allSelected" @change="toggleAll()">
                    Pilih semua di halaman ini
                </label>
            @endif
        </div>

        <div x-cloak x-show="selected.length > 0" x-transition class="flex flex-col gap-3 border-b border-blue-100 bg-blue-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <span class="text-xs font-bold text-blue-800"><span x-text="selected.length"></span> notifikasi dipilih</span>
            <div class="grid grid-cols-3 gap-2">
                <button type="button" @click="submitBulk('read')" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-white px-3 text-[11px] font-bold text-blue-700 hover:bg-blue-100"><i class="fas fa-envelope-open" aria-hidden="true"></i><span class="hidden sm:inline">Dibaca</span></button>
                <button type="button" @click="submitBulk('unread')" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-blue-200 bg-white px-3 text-[11px] font-bold text-blue-700 hover:bg-blue-100"><i class="fas fa-envelope" aria-hidden="true"></i><span class="hidden sm:inline">Belum dibaca</span></button>
                <button type="button" @click="submitBulk('delete')" class="inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-red-200 bg-white px-3 text-[11px] font-bold text-red-700 hover:bg-red-50"><i class="fas fa-trash" aria-hidden="true"></i><span class="hidden sm:inline">Hapus</span></button>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                @php
                    $tone = $toneClasses[$notification->color] ?? $toneClasses['secondary'];
                    $isUnread = is_null($notification->read_at);
                    $showParams = ['id' => $notification->id] + ($ctx ? ['ctx' => $ctx] : []);
                @endphp
                <article class="group relative flex min-w-0 items-start gap-3 px-4 py-4 transition hover:bg-slate-50 sm:gap-4 sm:px-5 {{ $isUnread ? 'bg-blue-50/30' : 'bg-white' }}">
                    <label class="relative z-10 mt-3 inline-flex shrink-0 cursor-pointer" aria-label="Pilih notifikasi {{ $notification->judul }}">
                        <input type="checkbox" value="{{ $notification->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    </label>
                    <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl ring-1 {{ $tone['icon'] }} sm:h-11 sm:w-11">
                        <i class="{{ $notification->icon ?? 'fas fa-bell' }}" aria-hidden="true"></i>
                    </span>
                    <a href="{{ route('notifications.show', $showParams) }}" class="min-w-0 flex-1 pr-5 text-left no-underline sm:pr-10">
                        <div class="flex min-w-0 flex-col gap-1 sm:flex-row sm:items-center sm:gap-2">
                            <span class="w-fit rounded-full px-2 py-1 text-[10px] font-extrabold {{ $tone['badge'] }}">{{ $typeLabels[$notification->tipe] ?? ucfirst($notification->tipe) }}</span>
                            <h3 class="min-w-0 truncate text-sm {{ $isUnread ? 'font-extrabold text-slate-950' : 'font-bold text-slate-800' }}">{{ $notification->judul }}</h3>
                        </div>
                        <p class="mt-1.5 line-clamp-2 text-xs leading-5 text-slate-500">{{ $notification->pesan }}</p>
                        <time datetime="{{ $notification->created_at->toIso8601String() }}" title="{{ $notification->created_at->copy()->locale('id')->translatedFormat('d M Y, H:i') }}" class="mt-2 block text-[10px] font-semibold text-slate-400">
                            <i class="far fa-clock mr-1" aria-hidden="true"></i>{{ $notification->created_at->copy()->locale('id')->diffForHumans() }}
                        </time>
                    </a>
                    @if($isUnread)<span class="absolute right-4 top-5 h-2.5 w-2.5 rounded-full bg-blue-500 ring-4 ring-blue-100 sm:right-5" title="Belum dibaca"></span>@endif
                </article>
            @empty
                <div class="flex min-h-72 flex-col items-center justify-center px-5 py-12 text-center">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-500"><i class="fas fa-bell-slash" aria-hidden="true"></i></span>
                    <h3 class="mt-4 text-sm font-extrabold text-slate-900">{{ request()->hasAny(['search', 'filter', 'date_from', 'date_to']) ? 'Notifikasi tidak ditemukan' : 'Belum ada notifikasi' }}</h3>
                    <p class="mt-1 max-w-sm text-xs leading-5 text-slate-500">{{ request()->hasAny(['search', 'filter', 'date_from', 'date_to']) ? 'Ubah kata kunci atau rentang tanggal untuk melihat hasil lain.' : 'Pesan dan pembaruan terbaru akan tampil di halaman ini.' }}</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="border-t border-slate-200 px-4 py-4 sm:px-5">{{ $notifications->links() }}</div>
        @endif
    </section>

    <form x-ref="bulkForm" method="POST" action="{{ route('notifications.bulk-action') }}" class="hidden">
        @csrf
        <input type="hidden" name="action" :value="action">
        @if($ctx)<input type="hidden" name="ctx" value="{{ $ctx }}">@endif
        <template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template>
    </form>
</div>
@endsection
