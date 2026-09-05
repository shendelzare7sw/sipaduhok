@php
    $sections = [
        [
            'label' => 'Mulai',
            'items' => [
                ['route' => 'sekretaris.dashboard', 'patterns' => ['sekretaris.dashboard'], 'icon' => 'fa-house', 'label' => 'Beranda Sekretaris', 'description' => 'Ringkasan pekerjaan hari ini'],
            ],
        ],
        [
            'label' => 'Publikasi Sekolah',
            'hint' => 'Kelola informasi dari agenda hingga kanal publik.',
            'items' => [
                ['route' => 'sekretaris.kalender.index', 'patterns' => ['sekretaris.kalender.*'], 'icon' => 'fa-calendar-days', 'label' => 'Kalender Akademik', 'description' => 'Agenda utama sekolah'],
                ['route' => 'sekretaris.pengumuman.index', 'patterns' => ['sekretaris.pengumuman.*'], 'icon' => 'fa-bullhorn', 'label' => 'Pengumuman', 'description' => 'Informasi warga sekolah'],
                ['route' => 'sekretaris.flyer.index', 'patterns' => ['sekretaris.flyer.*'], 'icon' => 'fa-images', 'label' => 'Flyer / Iklan', 'description' => 'Materi visual terjadwal'],
                ['route' => 'sekretaris.berita.index', 'patterns' => ['sekretaris.berita.*'], 'icon' => 'fa-newspaper', 'label' => 'Berita', 'description' => 'Tautan dan berita unggulan'],
            ],
        ],
    ];
@endphp

@foreach($sections as $section)
    <li class="mb-5">
        <div class="mb-2 px-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>
            @isset($section['hint'])
                <p class="mt-1 text-[10px] leading-4 text-sky-100/75">{{ $section['hint'] }}</p>
            @endisset
        </div>
        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php($active = request()->routeIs(...$item['patterns']))
                <li>
                    <a href="{{ route($item['route']) }}" class="group flex min-h-11 w-full items-center gap-3 rounded-xl border px-3 py-2.5 text-sm font-semibold no-underline transition {{ $active ? 'border-white/30 bg-white/20 text-white shadow-lg shadow-slate-950/15' : 'border-white/10 bg-white/[0.03] text-blue-50/90 hover:border-white/20 hover:bg-white/10 hover:text-white' }}">
                        <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate">{{ $item['label'] }}</span>
                            <span class="mt-0.5 block truncate text-[10px] font-medium {{ $active ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
