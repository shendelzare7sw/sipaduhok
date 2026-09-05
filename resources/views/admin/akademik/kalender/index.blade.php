@extends('layouts.app')

@section('title', 'Kalender Akademik')
@section('page-title', 'Kalender Akademik')
@section('page-subtitle', 'Kelola agenda tahun ajaran ' . ($tahunAjaranAktif->nama_tahun_ajaran ?? '-'))

@section('content')
@php
    $routeBase = request()->routeIs('sekretaris.*') ? 'sekretaris' : 'admin.akademik';
    $mode = in_array($viewMode ?? 'bulan', ['bulan', 'minggu', 'tahun'], true) ? $viewMode : 'bulan';
    $statusTones = ['aktif' => 'bg-emerald-50 text-emerald-700', 'draft' => 'bg-amber-50 text-amber-700', 'selesai' => 'bg-slate-100 text-slate-600'];
    $typeTones = ['field_trip' => 'bg-cyan-50 text-cyan-700', 'outing' => 'bg-emerald-50 text-emerald-700', 'live_in' => 'bg-violet-50 text-violet-700', 'hokfest' => 'bg-orange-50 text-orange-700', 'pts' => 'bg-amber-50 text-amber-700', 'pas' => 'bg-red-50 text-red-700', 'libur' => 'bg-slate-100 text-slate-700', 'ujian' => 'bg-pink-50 text-pink-700', 'acara_sekolah' => 'bg-teal-50 text-teal-700'];
    $typeDots = ['field_trip' => 'bg-cyan-500', 'outing' => 'bg-emerald-500', 'live_in' => 'bg-violet-500', 'hokfest' => 'bg-orange-500', 'pts' => 'bg-amber-500', 'pas' => 'bg-red-500', 'libur' => 'bg-slate-500', 'ujian' => 'bg-pink-500', 'acara_sekolah' => 'bg-teal-500'];
    $periodTitle = match($mode) {
        'minggu' => $startOfWeek->translatedFormat('d M') . '–' . $endOfWeek->translatedFormat('d M Y'),
        'tahun' => (string) $year,
        default => \Carbon\Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y'),
    };
@endphp

<div class="min-w-0 w-full space-y-5" x-data="{
    tab: 'calendar',
    query: '',
    async toggleVisibility(id, title) {
        const result = await Swal.fire({ title: 'Ubah visibilitas siswa?', text: title, icon: 'question', showCancelButton: true, confirmButtonText: 'Ya, ubah', cancelButtonText: 'Batal', confirmButtonColor: '#2563eb' });
        if (!result.isConfirmed) return;
        try {
            const endpoint = @js(route($routeBase . '.kalender.toggle-visibility', ['id' => '__ID__'])).replace('__ID__', id);
            const response = await fetch(endpoint, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || 'Visibilitas gagal diperbarui.');
            await Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: data.message, showConfirmButton: false, timer: 1200 });
            window.location.reload();
        } catch (error) {
            Swal.fire({ title: 'Tidak dapat memproses', text: error.message, icon: 'error' });
        }
    }
}">
    <section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-3">
        @foreach([
            ['label' => 'Total Kegiatan', 'value' => $kalender->count(), 'tone' => 'bg-brand-50 text-brand-700'],
            ['label' => 'Aktif', 'value' => $kalender->where('status', 'aktif')->count(), 'tone' => 'bg-emerald-50 text-emerald-700'],
            ['label' => 'Draft', 'value' => $kalender->where('status', 'draft')->count(), 'tone' => 'bg-amber-50 text-amber-700'],
            ['label' => 'Tahun Ajaran', 'value' => $tahunAjaranAktif->nama_tahun_ajaran ?? '-', 'tone' => 'bg-violet-50 text-violet-700'],
        ] as $stat)
            <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><p class="truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</p><p class="mt-2 truncate text-xl font-extrabold text-slate-900" title="{{ $stat['value'] }}">{{ is_numeric($stat['value']) ? number_format($stat['value']) : $stat['value'] }}</p><span class="mt-2 block h-1.5 w-10 rounded-full {{ $stat['tone'] }}"></span></article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"><div class="min-w-0"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-calendar-days text-brand-600" aria-hidden="true"></i>Agenda Akademik</h2><p class="mt-1 text-xs text-slate-500">Pilih rentang waktu, lalu buka agenda untuk melihat atau mengubah detail.</p></div><div class="grid grid-cols-2 gap-2 sm:flex"><a href="{{ route($routeBase . '.kalender.cetak', ['jenis' => 'tahunan']) }}" target="_blank" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700 no-underline hover:bg-emerald-100"><i class="fas fa-print" aria-hidden="true"></i>Cetak tahunan</a><a href="{{ route($routeBase . '.kalender.create') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i>Tambah agenda</a></div></div>
            <div class="mt-4 grid gap-3 lg:grid-cols-[auto_minmax(220px,1fr)_auto] lg:items-end"><div class="grid grid-cols-3 gap-1 rounded-xl bg-slate-100 p-1">@foreach(['bulan' => 'Bulan', 'minggu' => 'Minggu', 'tahun' => 'Tahun'] as $value => $label)<a href="{{ route($routeBase . '.kalender.index', ['mode' => $value, 'year' => $year, 'month' => $month, 'date' => $baseDate->format('Y-m-d')]) }}" class="inline-flex h-9 items-center justify-center rounded-lg px-3 text-xs font-bold no-underline {{ $mode === $value ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">{{ $label }}</a>@endforeach</div><label class="relative min-w-0"><span class="sr-only">Cari agenda pada daftar</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" x-model.debounce.200ms="query" placeholder="Cari agenda pada daftar..." class="h-10 w-full rounded-xl border border-slate-200 bg-white !pl-10 pr-3 text-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></label><form action="{{ route($routeBase . '.kalender.cetak') }}" method="GET" target="_blank" class="flex gap-2"><input type="hidden" name="jenis" value="bulanan"><input type="month" name="bulan" value="{{ sprintf('%04d-%02d', $year, $month) }}" class="h-10 min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700 outline-none focus:border-brand-500"><button type="submit" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-800 px-3 text-xs font-bold text-white hover:bg-slate-900" title="Cetak bulan pilihan"><i class="fas fa-print" aria-hidden="true"></i></button></form></div>
            <div class="mt-4 flex items-center justify-between gap-3"><button type="button" @click="tab = 'calendar'" class="h-9 rounded-lg px-3 text-xs font-bold" :class="tab === 'calendar' ? 'bg-brand-50 text-brand-700' : 'text-slate-500 hover:bg-slate-50'">Kalender visual</button><h3 class="text-center text-sm font-extrabold text-slate-900">{{ $periodTitle }}</h3><button type="button" @click="tab = 'list'" class="h-9 rounded-lg px-3 text-xs font-bold" :class="tab === 'list' ? 'bg-brand-50 text-brand-700' : 'text-slate-500 hover:bg-slate-50'">Daftar detail</button></div>
        </header>

        <div x-show="tab === 'calendar'">
            <nav class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                @if($mode === 'bulan')
                    <a href="{{ route($routeBase . '.kalender.index', ['mode' => 'bulan', 'year' => $prevMonth['year'], 'month' => $prevMonth['month']]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Bulan sebelumnya"><i class="fas fa-chevron-left" aria-hidden="true"></i></a><a href="{{ route($routeBase . '.kalender.index', ['mode' => 'bulan', 'year' => $nextMonth['year'], 'month' => $nextMonth['month']]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Bulan berikutnya"><i class="fas fa-chevron-right" aria-hidden="true"></i></a>
                @elseif($mode === 'minggu')
                    <a href="{{ route($routeBase . '.kalender.index', ['mode' => 'minggu', 'date' => $prevWeekDate]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Minggu sebelumnya"><i class="fas fa-chevron-left" aria-hidden="true"></i></a><a href="{{ route($routeBase . '.kalender.index', ['mode' => 'minggu', 'date' => $nextWeekDate]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Minggu berikutnya"><i class="fas fa-chevron-right" aria-hidden="true"></i></a>
                @else
                    <a href="{{ route($routeBase . '.kalender.index', ['mode' => 'tahun', 'year' => $prevYear]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Tahun sebelumnya"><i class="fas fa-chevron-left" aria-hidden="true"></i></a><a href="{{ route($routeBase . '.kalender.index', ['mode' => 'tahun', 'year' => $nextYear]) }}" class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-700 no-underline hover:bg-slate-200" aria-label="Tahun berikutnya"><i class="fas fa-chevron-right" aria-hidden="true"></i></a>
                @endif
            </nav>

            @if ($mode === 'bulan')
                <div class="grid grid-cols-7 border-b border-slate-200 bg-slate-50 text-center text-[9px] font-bold uppercase tracking-wide text-slate-500 sm:text-[10px]">
                    @foreach (['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'] as $dayName)
                        <div class="px-0.5 py-2.5 sm:px-2 sm:py-3">{{ $dayName }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7">
                    @foreach ($calendarDays as $day)
                        <div class="min-h-14 min-w-0 border-b border-r border-slate-100 p-1 sm:min-h-28 sm:p-2 {{ $day['isOtherMonth'] ? 'bg-slate-50/60 text-slate-400' : 'bg-white' }}">
                            <div class="flex items-center justify-between">
                                <span class="flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-bold sm:h-7 sm:w-7 sm:text-xs {{ $day['isToday'] ? 'bg-brand-600 text-white' : '' }}">{{ $day['day'] }}</span>
                                @if (!$day['isOtherMonth'])
                                    <a href="{{ route($routeBase . '.kalender.create', ['tanggal' => $day['fullDate']]) }}" class="hidden text-[10px] text-slate-300 no-underline hover:text-brand-600 sm:block" aria-label="Tambah agenda {{ $day['fullDate'] }}"><i class="fas fa-plus" aria-hidden="true"></i></a>
                                @endif
                            </div>
                            <div class="mt-1 flex flex-wrap gap-0.5 sm:mt-2 sm:block sm:space-y-1">
                                @foreach ($day['events']->take(3) as $event)
                                    <a href="{{ route($routeBase . '.kalender.show', $event->id) }}" class="no-underline sm:block" title="{{ $event->nama_kegiatan }}"><span class="block h-1.5 w-1.5 rounded-full sm:hidden {{ $typeDots[$event->jenis_kegiatan] ?? 'bg-brand-500' }}"></span><span class="hidden truncate rounded-md px-2 py-1 text-[10px] font-bold sm:block {{ $typeTones[$event->jenis_kegiatan] ?? 'bg-brand-50 text-brand-700' }}">{{ $event->nama_kegiatan }}</span></a>
                                @endforeach
                                @if ($day['events']->count() > 3)
                                    <span class="text-[8px] font-bold text-slate-500 sm:block sm:px-1 sm:text-[9px] sm:font-semibold sm:text-slate-400">+{{ $day['events']->count() - 3 }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif ($mode === 'minggu')
                <div class="grid divide-y divide-slate-100 sm:grid-cols-7 sm:divide-x sm:divide-y-0">
                    @foreach ($weekDays as $day)
                        @php
                            $dayEvents = $eventsMinggu->filter(fn ($event) => $day->between(
                                $event->tanggal_mulai->copy()->startOfDay(),
                                ($event->tanggal_selesai ?? $event->tanggal_mulai)->copy()->endOfDay()
                            ));
                        @endphp
                        <div class="grid min-h-20 grid-cols-[56px_minmax(0,1fr)] gap-3 p-3 sm:min-h-64 sm:grid-cols-1 sm:gap-0">
                            <div class="text-center"><p class="text-[10px] font-bold uppercase text-slate-400">{{ $day->translatedFormat('D') }}</p><p class="mt-1 text-lg font-extrabold {{ $day->isToday() ? 'text-brand-600' : 'text-slate-900' }}">{{ $day->format('d') }}</p></div>
                            <div class="space-y-2 sm:mt-4">
                                @forelse ($dayEvents as $event)
                                    <a href="{{ route($routeBase . '.kalender.show', $event->id) }}" class="block rounded-lg p-2 text-[10px] font-bold leading-4 no-underline {{ $typeTones[$event->jenis_kegiatan] ?? 'bg-brand-50 text-brand-700' }}">{{ $event->nama_kegiatan }}</a>
                                @empty
                                    <p class="py-2 text-[10px] text-slate-400 sm:text-center">Tidak ada agenda</p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="grid grid-cols-2 gap-3 p-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                    @for ($calendarMonth = 1; $calendarMonth <= 12; $calendarMonth++)
                        @php
                            $monthEvents = $eventsTahun->filter(fn ($event) => (int) $event->tanggal_mulai->format('n') === $calendarMonth);
                        @endphp
                        <a href="{{ route($routeBase . '.kalender.index', ['mode' => 'bulan', 'year' => $year, 'month' => $calendarMonth]) }}" class="rounded-xl border border-slate-200 p-3 text-center no-underline hover:border-brand-300 hover:bg-brand-50/30"><p class="text-xs font-extrabold text-slate-800">{{ \Carbon\Carbon::createFromDate($year, $calendarMonth, 1)->translatedFormat('F') }}</p><p class="mt-2 text-xl font-extrabold text-brand-700">{{ $monthEvents->count() }}</p><p class="mt-1 text-[10px] text-slate-500">agenda</p></a>
                    @endfor
                </div>
            @endif
        </div>

        <div x-show="tab === 'list'" x-cloak>
            <div class="divide-y divide-slate-100 lg:hidden">@forelse($kalender as $item)<article x-show="@js(Str::lower($item->nama_kegiatan . ' ' . $item->keterangan)).includes(query.toLowerCase())" class="p-4"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $item->nama_kegiatan }}</h3><p class="mt-1 text-[11px] text-slate-500">{{ $item->tanggal_mulai->translatedFormat('d M Y') }}@if($item->tanggal_selesai)–{{ $item->tanggal_selesai->translatedFormat('d M Y') }}@endif</p></div><span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTones[$item->status] ?? $statusTones['selesai'] }}">{{ ucfirst($item->status) }}</span></div><div class="mt-3 grid grid-cols-4 gap-2"><a href="{{ route($routeBase . '.kalender.show', $item->id) }}" class="inline-flex min-h-9 items-center justify-center rounded-lg bg-blue-50 text-blue-700 no-underline"><i class="fas fa-eye" aria-hidden="true"></i></a><a href="{{ route($routeBase . '.kalender.edit', $item->id) }}" class="inline-flex min-h-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700 no-underline"><i class="fas fa-edit" aria-hidden="true"></i></a><button type="button" @click="toggleVisibility({{ $item->id }}, @js($item->nama_kegiatan))" class="inline-flex min-h-9 items-center justify-center rounded-lg bg-violet-50 text-violet-700"><i class="fas {{ $item->is_hidden_siswa ? 'fa-eye-slash' : 'fa-eye' }}" aria-hidden="true"></i></button><form action="{{ route($routeBase . '.kalender.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus agenda?" data-confirm-message="{{ $item->nama_kegiatan }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex min-h-9 w-full items-center justify-center rounded-lg bg-red-50 text-red-700"><i class="fas fa-trash" aria-hidden="true"></i></button></form></div></article>@empty<div class="px-5 py-12 text-center text-xs text-slate-500">Belum ada agenda akademik.</div>@endforelse</div>
            @if($kalender->isNotEmpty())<div class="hidden overflow-x-auto lg:block"><table class="w-full min-w-[940px] table-fixed border-collapse text-left text-sm"><colgroup><col class="w-14"><col><col class="w-40"><col class="w-48"><col class="w-24"><col class="w-44"></colgroup><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-3 py-3 text-center">No</th><th class="px-3 py-3">Kegiatan</th><th class="px-3 py-3">Jenis</th><th class="px-3 py-3">Tanggal</th><th class="px-3 py-3">Status</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($kalender as $index => $item)<tr x-show="@js(Str::lower($item->nama_kegiatan . ' ' . $item->keterangan)).includes(query.toLowerCase())" class="hover:bg-slate-50/80"><td class="px-3 py-4 text-center text-xs text-slate-500">{{ $index + 1 }}</td><td class="min-w-0 px-3 py-4"><p class="truncate font-bold text-slate-900" title="{{ $item->nama_kegiatan }}">{{ $item->nama_kegiatan }}</p>@if($item->lampiran_surat)<a href="{{ asset('storage/' . $item->lampiran_surat) }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-[10px] font-bold text-brand-700 no-underline"><i class="fas fa-paperclip" aria-hidden="true"></i>Lampiran</a>@endif</td><td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[10px] font-bold {{ $typeTones[$item->jenis_kegiatan] ?? 'bg-brand-50 text-brand-700' }}">{{ $item->jenis_label }}</span></td><td class="whitespace-nowrap px-3 py-4 text-xs text-slate-600">{{ $item->tanggal_mulai->format('d/m/Y') }}@if($item->tanggal_selesai)<span class="block text-[10px] text-slate-400">s.d. {{ $item->tanggal_selesai->format('d/m/Y') }}</span>@endif</td><td class="px-3 py-4"><span class="inline-flex rounded-full px-2.5 py-1 text-[10px] font-bold {{ $statusTones[$item->status] ?? $statusTones['selesai'] }}">{{ ucfirst($item->status) }}</span></td><td class="px-4 py-4"><div class="flex items-center justify-end gap-2"><x-cleanflow.table-action href="{{ route($routeBase . '.kalender.show', $item->id) }}" tone="view" icon="fas fa-eye" label="Detail agenda" /><x-cleanflow.table-action href="{{ route($routeBase . '.kalender.edit', $item->id) }}" tone="edit" icon="fas fa-edit" label="Edit agenda" /><x-cleanflow.table-action type="button" tone="visibility" icon="fas {{ $item->is_hidden_siswa ? 'fa-eye-slash' : 'fa-eye' }}" label="Ubah visibilitas siswa" data-title="{{ $item->nama_kegiatan }}" x-on:click="toggleVisibility({{ $item->id }}, $el.dataset.title)" /><form action="{{ route($routeBase . '.kalender.destroy', $item->id) }}" method="POST" data-confirm data-confirm-title="Hapus agenda?" data-confirm-message="{{ $item->nama_kegiatan }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus agenda" /></form></div></td></tr>@endforeach</tbody></table></div>@endif
        </div>
    </section>
</div>
@endsection
