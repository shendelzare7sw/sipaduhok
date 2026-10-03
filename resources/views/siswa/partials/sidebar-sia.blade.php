{{--
    Navigasi CleanFlow untuk Siswa SIA.
    Pembayaran dan Rapor sengaja tidak ada: keduanya menjadi tanggung jawab Wali Siswa.
--}}

@php
    $siswa = \App\Models\Siswa::where('user_id', auth()->id())->with('kelas')->first();
    $isAlumni = $siswa?->status === 'lulus';

    $setting = \App\Models\AppSetting::where('key', 'lms_allowed_jenjang')->first();
    $allowedJenjang = $setting ? json_decode($setting->value, true) : [];
    $showLms = $siswa && $siswa->kelas && in_array($siswa->kelas->jenjang, (array) $allowedJenjang);

    $sections = [
        [
            'label' => 'Mulai',
            'items' => [
                ['label' => 'Dashboard', 'description' => 'Ringkasan belajar hari ini', 'icon' => 'fa-house', 'route' => 'siswa.sia.dashboard', 'patterns' => ['siswa.sia.dashboard']],
            ],
        ],
    ];

    if ($showLms) {
        $sections[] = [
            'label' => 'Pembelajaran',
            'items' => [
                ['label' => 'HOK-LMS', 'description' => 'Materi, tugas, dan ujian', 'icon' => 'fa-graduation-cap', 'route' => 'siswa.lms.dashboard', 'patterns' => ['siswa.lms.*']],
            ],
        ];
    }

    $sections[] = [
        'label' => 'Akademik',
        'items' => [
            ['label' => 'Presensi', 'description' => 'Rekap kehadiran bulan ini', 'icon' => 'fa-calendar-check', 'route' => 'siswa.sia.presensi.index', 'patterns' => ['siswa.sia.presensi.*']],
            ['label' => 'Data Penilaian', 'description' => 'Nilai tugas, latihan, dan ujian', 'icon' => 'fa-chart-line', 'route' => 'siswa.sia.penilaian', 'patterns' => ['siswa.sia.penilaian']],
        ],
    ];

    $baseClasses = 'group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition';
    $toneClasses = ['!bg-white/[0.07]', '!bg-sky-300/[0.15]', '!bg-cyan-200/[0.12]', '!bg-indigo-200/[0.13]'];
    $toneIdx = 0;
@endphp

@if($isAlumni)
    {{-- Alumni: hanya menu yang masih boleh diakses (lihat middleware student.active). --}}
    @include('siswa.partials.sidebar-alumni')
@else
@foreach($sections as $section)
    <li class="mb-5">
        <div class="mb-2 px-3">
            <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>
        </div>

        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php
                    $isActive = request()->routeIs(...$item['patterns']);
                    $toneClass = $toneClasses[$toneIdx % count($toneClasses)];
                    $toneIdx++;
                    $stateClasses = $isActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                @endphp
                <li class="menu-item {{ $isActive ? 'active' : '' }}">
                    <a href="{{ route($item['route']) }}" class="menu-link {{ $baseClasses }} {{ $stateClasses }}">
                        <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate">{{ $item['label'] }}</span>
                            <span class="mt-0.5 block truncate text-[10px] font-medium {{ $isActive ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">{{ $item['description'] }}</span>
                        </span>
                    </a>
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
@endif
