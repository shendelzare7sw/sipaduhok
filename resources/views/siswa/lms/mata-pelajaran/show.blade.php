@extends('layouts.lms')

@section('title', $mataPelajaran->nama_mapel)
@section('page-title', $mataPelajaran->nama_mapel)
@section('page-subtitle', 'Materi, Tugas, dan Ujian')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection

@section('content')
@php
    // Kelompokkan item per tanggal; grup bertanggal hari ini atau berisi item baru hari ini terbuka otomatis.
    $kelompokkan = function ($items, string $field) {
        $hasil = [];
        foreach ($items as $item) {
            $tgl = $item->{$field};
            $key = $tgl->format('Y-m-d');
            $label = $tgl->translatedFormat('d M Y');
            if ($key === now()->format('Y-m-d')) {
                $label = 'Hari Ini (' . $label . ')';
            } elseif ($key === now()->subDay()->format('Y-m-d')) {
                $label = 'Kemarin (' . $label . ')';
            }
            $hasil[$key] ??= ['label' => $label, 'today' => $key === now()->format('Y-m-d'), 'items' => []];
            $hasil[$key]['items'][] = $item;
            // Grup juga terbuka bila berisi item yang baru dibuat/diterbitkan hari ini
            // (mis. tugas dibuat hari ini dengan tenggat minggu depan).
            if ($item->created_at?->isToday()) {
                $hasil[$key]['today'] = true;
            }
        }
        return $hasil;
    };

    $latihanList = \App\Models\Ujian::where('tipe_ujian', 'latihan')
        ->where('mata_pelajaran_id', $mataPelajaran->id)
        ->where('kelas_id', $siswa->kelas_id)
        ->orderBy('tanggal_mulai', 'desc')
        ->get();
    $latestForums = \App\Models\ForumDiskusi::where('mata_pelajaran_id', $mataPelajaran->id)
        ->where('kelas_id', $siswa->kelas_id)
        ->with('user', 'replies')
        ->orderBy('is_pinned', 'desc')
        ->orderBy('created_at', 'desc')
        ->take(3)
        ->get();
    $meetingList = \App\Models\LmsMeeting::where('kelas_id', $siswa->kelas_id)
        ->where('mata_pelajaran_id', $mataPelajaran->id)
        ->where('is_active', true)
        ->orderBy('waktu_mulai', 'asc')
        ->take(5)
        ->get();
    $aksesService = app(\App\Services\ValidasiAksesService::class);

    // Tombol aksi latihan/ujian sesuai status (logika sama seperti sebelumnya).
    $aksiUjian = function ($ujian, bool $isLatihan) use ($mataPelajaran, $siswa, $aksesService) {
        $route = route($isLatihan ? 'siswa.lms.mapel.latihan.show' : 'siswa.lms.mapel.ujian.show', [$mataPelajaran->id, $ujian->id]);
        if (! $ujian->is_active) {
            return ['label' => 'Belum Dirilis', 'icon' => 'fa-lock', 'url' => null];
        }
        if ($ujian->isOngoing()) {
            // Akses ujian murni dari sisi keuangan (lunas / validasi Bendahara / dispensasi).
            if (! $isLatihan && $ujian->requiresValidation() && ! $aksesService->cekAksesUjian($siswa)) {
                return ['label' => 'Belum Memiliki Akses', 'icon' => 'fa-lock', 'url' => null];
            }
            return ['label' => $isLatihan ? 'Mulai Latihan' : 'Mulai Ujian', 'icon' => 'fa-play', 'url' => $route,
                'tone' => $isLatihan ? 'bg-amber-500 hover:bg-amber-600' : 'bg-rose-600 hover:bg-rose-700'];
        }
        if ($ujian->tanggal_mulai->isFuture()) {
            return ['label' => 'Belum Dimulai', 'icon' => 'fa-hourglass-start', 'url' => null];
        }
        return $ujian->tampilkan_nilai
            ? ['label' => 'Lihat Hasil', 'icon' => 'fa-square-poll-vertical', 'url' => $route, 'tone' => 'bg-sky-600 hover:bg-sky-700']
            : ['label' => 'Selesai', 'icon' => 'fa-check', 'url' => null];
    };

    $sections = [
        ['id' => 'materi', 'judul' => 'Materi pembelajaran', 'icon' => 'fa-file-lines', 'tone' => 'bg-sky-50 text-sky-600', 'groups' => $kelompokkan($materiList, 'tanggal_upload'), 'kosong' => 'Belum ada materi yang tersedia'],
        ['id' => 'tugas', 'judul' => 'Tugas', 'icon' => 'fa-list-check', 'tone' => 'bg-amber-50 text-amber-600', 'groups' => $kelompokkan($tugasList, 'tanggal_deadline'), 'kosong' => 'Belum ada tugas yang tersedia'],
        ['id' => 'latihan', 'judul' => 'Latihan', 'icon' => 'fa-pencil-ruler', 'tone' => 'bg-violet-50 text-violet-600', 'groups' => $kelompokkan($latihanList, 'tanggal_mulai'), 'kosong' => 'Belum ada latihan yang tersedia'],
        ['id' => 'ujian', 'judul' => 'Ujian', 'icon' => 'fa-file-signature', 'tone' => 'bg-rose-50 text-rose-600', 'groups' => $kelompokkan($ujianList, 'tanggal_mulai'), 'kosong' => 'Belum ada ujian yang tersedia'],
    ];
    $hitung = fn ($groups) => collect($groups)->sum(fn ($g) => count($g['items']));
    $card = 'min-w-0 rounded-2xl border border-slate-200 bg-white shadow-sm';
    $btn = 'inline-flex min-h-9 shrink-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg px-3 text-xs font-bold no-underline transition';
    $btnOff = $btn . ' cursor-not-allowed bg-slate-100 text-slate-400';
@endphp

<div class="min-w-0 w-full space-y-5">
    <nav class="flex min-w-0 items-center gap-2 text-xs font-semibold text-slate-500" aria-label="Breadcrumb">
        <a href="{{ route('siswa.lms.dashboard') }}" class="inline-flex items-center gap-1.5 no-underline hover:text-indigo-700"><i class="fa-solid fa-house" aria-hidden="true"></i>Beranda LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px] text-slate-300" aria-hidden="true"></i>
        <span class="truncate text-slate-800">{{ $mataPelajaran->nama_mapel }}</span>
    </nav>

    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-600 p-5 text-white shadow-lg shadow-indigo-900/15 sm:p-6">
        <span class="pointer-events-none absolute -right-8 -top-10 h-40 w-40 rounded-full bg-white/10" aria-hidden="true"></span>
        <div class="relative flex items-center justify-between gap-4">
            <div class="min-w-0">
                <p class="text-xs font-bold text-indigo-100">Kode {{ $mataPelajaran->kode_mapel }} · Jenjang {{ $mataPelajaran->jenjang }}</p>
                <h2 class="mt-1 truncate text-xl font-extrabold text-white sm:text-2xl">{{ $mataPelajaran->nama_mapel }}</h2>
            </div>
            <span class="hidden h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-2xl ring-1 ring-inset ring-white/25 sm:flex"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>
        </div>
        {{-- Pintasan ke setiap bagian --}}
        <div class="relative mt-4 flex gap-2 overflow-x-auto pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach($sections as $section)
                <a href="#{{ $section['id'] }}" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold text-white no-underline ring-1 ring-inset ring-white/25 transition hover:bg-white/25">
                    {{ $section['judul'] }} <span class="rounded-full bg-white/25 px-1.5 text-[10px]">{{ $hitung($section['groups']) }}</span>
                </a>
            @endforeach
            <a href="#forum" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold text-white no-underline ring-1 ring-inset ring-white/25 transition hover:bg-white/25">Forum</a>
            <a href="#meeting" class="inline-flex shrink-0 items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold text-white no-underline ring-1 ring-inset ring-white/25 transition hover:bg-white/25">Kelas virtual</a>
        </div>
    </section>

    @foreach($sections as $section)
        <section id="{{ $section['id'] }}" class="{{ $card }} scroll-mt-24">
            <h2 class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 text-base font-extrabold text-slate-900 sm:px-5">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $section['tone'] }}"><i class="fa-solid {{ $section['icon'] }} text-sm" aria-hidden="true"></i></span>
                {{ $section['judul'] }}
                <span class="text-sm font-bold text-slate-400">({{ $hitung($section['groups']) }})</span>
            </h2>

            @forelse($section['groups'] as $group)
                <details class="group/tgl border-b border-slate-100 last:border-b-0" @if($group['today']) open @endif>
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 text-sm font-bold text-slate-700 transition hover:bg-slate-50 sm:px-5 [&::-webkit-details-marker]:hidden">
                        <span class="flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-slate-400" aria-hidden="true"></i>{{ $group['label'] }}
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[11px] text-slate-500">{{ count($group['items']) }}</span>
                        </span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition group-open/tgl:rotate-180" aria-hidden="true"></i>
                    </summary>
                    <ul class="space-y-2 px-3 pb-3 sm:px-4">
                        @foreach($group['items'] as $item)
                            <li class="flex flex-col gap-3 rounded-xl border border-slate-200 p-3 sm:flex-row sm:items-start sm:justify-between">
                                <div class="min-w-0">
                                    @switch($section['id'])
                                        @case('materi')
                                            <h3 class="flex flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">{{ $item->judul_materi }}
                                                @if($item->created_at->diffInDays(now()) < 3)<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700">BARU</span>@endif
                                            </h3>
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ Str::limit($item->deskripsi, 150) }}</p>
                                            <p class="mt-1.5 text-[11px] font-semibold text-slate-400"><i class="fa-solid fa-file mr-1" aria-hidden="true"></i>{{ strtoupper($item->tipe_file ?? 'File') }} · <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $item->created_at->format('H:i') }}</p>
                                            @break
                                        @case('tugas')
                                            @php $aktif = $item->tanggal_deadline->isFuture(); @endphp
                                            <h3 class="flex flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">{{ $item->judul_tugas }}
                                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $aktif ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">{{ $aktif ? 'Aktif' : 'Ditutup' }}</span>
                                            </h3>
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ Str::limit($item->deskripsi, 150) }}</p>
                                            <p class="mt-1.5 text-[11px] font-semibold {{ $aktif ? 'text-slate-400' : 'text-rose-500' }}"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $item->tanggal_deadline->format('H:i') }} · {{ $aktif ? $item->tanggal_deadline->copy()->locale('id')->diffForHumans() : 'Sudah lewat' }}</p>
                                            @break
                                        @default
                                            @php $isLatihan = $section['id'] === 'latihan'; @endphp
                                            <h3 class="flex flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">{{ $item->judul_ujian }}
                                                <span class="rounded-full px-2 py-0.5 text-[10px] font-bold {{ $isLatihan ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700' }}">{{ $isLatihan ? 'Latihan' : $item->tipe_label }}</span>
                                            </h3>
                                            <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500">{{ $item->deskripsi ?? ($isLatihan ? 'Latihan pembelajaran interaktif' : 'Ujian ' . $item->tipe_label) }}</p>
                                            <p class="mt-1.5 text-[11px] font-semibold text-slate-400"><i class="fa-solid fa-stopwatch mr-1" aria-hidden="true"></i>{{ $item->durasi_menit > 0 ? $item->durasi_menit . ' menit' : 'Tidak terbatas' }} · <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $item->tanggal_mulai->format('H:i') }} – {{ $item->tanggal_selesai->format('H:i') }}</p>
                                    @endswitch
                                </div>

                                <div class="sm:pt-0.5">
                                    @if($section['id'] === 'materi')
                                        <a href="{{ route('siswa.lms.mapel.materi', [$mataPelajaran->id, $item->id]) }}" class="{{ $btn }} w-full bg-indigo-600 text-white hover:bg-indigo-700 sm:w-auto"><i class="fa-solid fa-eye" aria-hidden="true"></i>Lihat</a>
                                    @elseif($section['id'] === 'tugas')
                                        <a href="{{ route('siswa.lms.mapel.tugas.show', [$mataPelajaran->id, $item->id]) }}" class="{{ $btn }} w-full bg-indigo-600 text-white hover:bg-indigo-700 sm:w-auto"><i class="fa-solid fa-pen" aria-hidden="true"></i>Kerjakan</a>
                                    @else
                                        @php $aksi = $aksiUjian($item, $section['id'] === 'latihan'); @endphp
                                        @if($aksi['url'])
                                            <a href="{{ $aksi['url'] }}" class="{{ $btn }} w-full text-white sm:w-auto {{ $aksi['tone'] }}"><i class="fa-solid {{ $aksi['icon'] }}" aria-hidden="true"></i>{{ $aksi['label'] }}</a>
                                        @else
                                            <button type="button" disabled class="{{ $btnOff }} w-full sm:w-auto"><i class="fa-solid {{ $aksi['icon'] }}" aria-hidden="true"></i>{{ $aksi['label'] }}</button>
                                        @endif
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </details>
            @empty
                <p class="px-4 py-8 text-center text-sm text-slate-500"><i class="fa-solid {{ $section['icon'] }} mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>{{ $section['kosong'] }}</p>
            @endforelse
        </section>
    @endforeach

    <div class="grid min-w-0 gap-5 xl:grid-cols-2">
        <section id="forum" class="{{ $card }} scroll-mt-24">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5">
                <h2 class="flex items-center gap-3 text-base font-extrabold text-slate-900"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm text-emerald-600"><i class="fa-solid fa-comments" aria-hidden="true"></i></span>Forum diskusi</h2>
                <a href="{{ route('siswa.lms.mapel.forum.index', $mataPelajaran->id) }}" class="shrink-0 text-xs font-bold text-indigo-600 no-underline hover:text-indigo-800">Lihat semua</a>
            </div>
            <div class="space-y-1 p-2">
                @forelse($latestForums as $forum)
                    <a href="{{ route('siswa.lms.mapel.forum.show', [$mataPelajaran->id, $forum->id]) }}" class="group flex items-center justify-between gap-3 rounded-xl px-3 py-2.5 no-underline transition hover:bg-indigo-50/60">
                        <span class="min-w-0">
                            <span class="block truncate text-sm font-bold text-slate-900 group-hover:text-indigo-700">@if($forum->is_pinned)<i class="fa-solid fa-thumbtack mr-1 text-amber-500" aria-hidden="true"></i>@endif{{ $forum->judul }}</span>
                            <span class="block truncate text-[11px] text-slate-500"><i class="fa-solid fa-user mr-1" aria-hidden="true"></i>{{ $forum->user->name ?? 'Guru' }} · {{ $forum->created_at->copy()->locale('id')->diffForHumans() }}</span>
                        </span>
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full bg-indigo-50 px-2.5 py-1 text-[11px] font-bold text-indigo-700"><i class="fa-solid fa-comment" aria-hidden="true"></i>{{ $forum->replies->count() }}</span>
                    </a>
                @empty
                    <p class="px-3 py-8 text-center text-sm text-slate-500"><i class="fa-solid fa-comments mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>Belum ada diskusi untuk mata pelajaran ini</p>
                @endforelse
            </div>
        </section>

        <section id="meeting" class="{{ $card }} scroll-mt-24">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5">
                <h2 class="flex items-center gap-3 text-base font-extrabold text-slate-900"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-teal-50 text-sm text-teal-600"><i class="fa-solid fa-video" aria-hidden="true"></i></span>Kelas virtual</h2>
                <a href="{{ route('siswa.lms.mapel.meeting.index', $mataPelajaran->id) }}" class="shrink-0 text-xs font-bold text-indigo-600 no-underline hover:text-indigo-800">Lihat semua</a>
            </div>
            <div class="space-y-2 p-3">
                @forelse($meetingList as $meeting)
                    <div class="flex flex-col gap-3 rounded-xl border border-slate-200 p-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap gap-1.5">
                                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[10px] font-bold text-sky-700">{{ ucfirst(str_replace('_', ' ', $meeting->platform)) }}</span>
                                @if($meeting->waktu_mulai->isFuture())
                                    <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[10px] font-bold text-amber-700"><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>Akan datang</span>
                                @else
                                    <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-bold text-emerald-700"><i class="fa-solid fa-circle-check mr-1" aria-hidden="true"></i>Live</span>
                                @endif
                            </div>
                            <h3 class="mt-1.5 truncate text-sm font-extrabold text-slate-900">{{ $meeting->judul }}</h3>
                            <p class="text-[11px] text-slate-500"><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>{{ $meeting->waktu_mulai->translatedFormat('d M Y') }} · <i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $meeting->waktu_mulai->format('H:i') }}</p>
                        </div>
                        <a href="{{ $meeting->link_meeting }}" target="_blank" rel="noopener" class="{{ $btn }} bg-teal-600 text-white hover:bg-teal-700"><i class="fa-solid fa-video" aria-hidden="true"></i>Gabung</a>
                    </div>
                @empty
                    <p class="px-3 py-8 text-center text-sm text-slate-500"><i class="fa-solid fa-video mb-2 block text-2xl text-slate-300" aria-hidden="true"></i>Belum ada jadwal meeting aktif saat ini</p>
                @endforelse
            </div>
        </section>
    </div>
</div>
@endsection
