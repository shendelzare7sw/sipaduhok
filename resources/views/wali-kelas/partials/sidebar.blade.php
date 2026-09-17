@php
    $tenagaPendidik = \App\Models\TenagaPendidik::query()->where('user_id', auth()->id())->first();
    $kelasAktifWaliIds = collect();

    if ($tenagaPendidik) {
        $tahunAjaranAktifId = \App\Models\TahunAjaran::query()->where('is_active', true)->value('id');
        $kelasAktifWaliIds = \App\Models\Kelas::query()
            ->whereHas('waliKelasAssignments', fn ($query) => $query->where('tenaga_pendidik_id', $tenagaPendidik->id))
            ->when($tahunAjaranAktifId, fn ($query) => $query->where('tahun_ajaran_id', $tahunAjaranAktifId))
            ->pluck('id');
    }

    $hasMultipleKelas = $kelasAktifWaliIds->count() > 1;
    $selectedKelasId = session('wali_kelas_selected');
    $selectedKelas = $selectedKelasId && $kelasAktifWaliIds->contains($selectedKelasId)
        ? \App\Models\Kelas::with('cabang')->find($selectedKelasId)
        : null;

    $navigation = [
        ['label' => 'Utama', 'items' => [
            ['label' => 'Dashboard', 'route' => 'wali.dashboard', 'patterns' => ['wali.dashboard'], 'icon' => 'fa-home'],
            ...($hasMultipleKelas ? [[
                'label' => 'Pilih Kelas', 'route' => 'wali.pilih-kelas', 'patterns' => ['wali.pilih-kelas', 'wali.pilih-kelas.select'], 'icon' => 'fa-right-left',
            ]] : []),
        ]],
        ['label' => 'Akademik', 'items' => [
            ['label' => 'Jadwal Pelajaran', 'route' => 'wali.jadwal.index', 'patterns' => ['wali.jadwal.*', 'wali.jadwal-pelajaran'], 'icon' => 'fa-calendar-week'],
            [
                'label' => 'Kelola Presensi',
                'patterns' => ['wali.presensi.*'],
                'icon' => 'fa-clipboard-check',
                'children' => [
                    ['label' => 'Input Harian', 'route' => 'wali.presensi.index', 'patterns' => ['wali.presensi.index']],
                    ['label' => 'Validasi Izin', 'route' => 'wali.presensi.validasi-izin', 'patterns' => ['wali.presensi.validasi-izin', 'wali.presensi.preview-bukti', 'wali.presensi.proses-validasi-izin']],
                    ['label' => 'Rekap Harian', 'route' => 'wali.presensi.rekap-harian', 'patterns' => ['wali.presensi.rekap-harian', 'wali.presensi.show-harian', 'wali.presensi.print-rekap']],
                    ['label' => 'Riwayat & Edit', 'route' => 'wali.presensi.riwayat', 'patterns' => ['wali.presensi.riwayat', 'wali.presensi.riwayat.update']],
                ],
            ],
            [
                'label' => 'Kelola Rapor',
                'patterns' => ['wali.nilai.*', 'wali.rapor.*', 'wali.rapor-pending', 'wali.arsip.*', 'wali.template-capaian.*'],
                'icon' => 'fa-file-lines',
                'children' => [
                    ['label' => 'Nilai Siswa', 'route' => 'wali.nilai.index', 'patterns' => ['wali.nilai.*']],
                    ['label' => 'Rapor Kelas', 'route' => 'wali.rapor.index', 'patterns' => ['wali.rapor.index', 'wali.rapor.edit', 'wali.rapor.preview', 'wali.rapor.print']],
                    ['label' => 'Rapor Tertunda', 'route' => 'wali.rapor-pending', 'patterns' => ['wali.rapor-pending']],
                    ['label' => 'Permintaan Unduh', 'route' => 'wali.rapor.request-download.index', 'patterns' => ['wali.rapor.request-download.*']],
                    ['label' => 'Template Capaian', 'route' => 'wali.template-capaian.index', 'patterns' => ['wali.template-capaian.*']],
                    ['label' => 'Arsip Kelas', 'route' => 'wali.arsip.index', 'patterns' => ['wali.arsip.*']],
                ],
            ],
        ]],
        ['label' => 'Tindak Lanjut', 'items' => [
            ['label' => 'Prediksi Kenaikan', 'route' => 'wali.kenaikan-kelas.prediction', 'patterns' => ['wali.kenaikan-kelas.prediction'], 'icon' => 'fa-chart-column'],
            ['label' => 'Validasi Akses', 'route' => 'wali.validasi-akses.index', 'patterns' => ['wali.validasi-akses.*'], 'icon' => 'fa-user-check'],
        ]],
    ];
@endphp

@if($selectedKelas && $hasMultipleKelas)
    <li class="mb-4 px-1">
        <div class="rounded-2xl bg-cyan-300/15 p-3 ring-1 ring-inset ring-white/15">
            <div class="flex items-center gap-2 text-[10px] font-bold uppercase tracking-[0.14em] text-cyan-100">
                <i class="fas fa-school" aria-hidden="true"></i>
                Kelas aktif
            </div>
            <p class="mt-2 truncate text-base font-extrabold text-white">{{ $selectedKelas->nama_kelas }}</p>
            <p class="mt-0.5 truncate text-xs text-blue-100/80">{{ $selectedKelas->cabang->nama_cabang ?? 'Cabang belum diatur' }} · {{ strtoupper($selectedKelas->jenjang) }}</p>
            <a href="{{ route('wali.pilih-kelas') }}" class="mt-3 inline-flex min-h-9 w-full items-center justify-center gap-2 rounded-xl border border-white/15 bg-white/10 px-3 text-xs font-bold text-white transition hover:bg-white/20">
                <i class="fas fa-right-left" aria-hidden="true"></i>
                Ganti kelas
            </a>
        </div>
    </li>
@endif

@foreach($navigation as $section)
    <li class="mb-4">
        <div class="menu-header mb-1.5 px-3">
            <p class="menu-header-text text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">{{ $section['label'] }}</p>
        </div>
        <ul class="space-y-1">
            @foreach($section['items'] as $item)
                @php
                    $isActive = request()->routeIs(...($item['patterns'] ?? []));
                    $hasChildren = ! empty($item['children']);
                    $targetId = 'wali-menu-'.$loop->parent->index.'-'.$loop->index;
                    $toneClasses = ['!bg-white/[0.07]', '!bg-cyan-200/[0.12]', '!bg-sky-300/[0.14]', '!bg-indigo-200/[0.12]'];
                    $toneClass = $toneClasses[($loop->parent->index + $loop->index) % count($toneClasses)];
                    $stateClasses = $isActive
                        ? '!border-0 !ring-0 !bg-white/25 text-white shadow-lg shadow-slate-950/15'
                        : "!border-0 !ring-0 {$toneClass} text-blue-50/90 hover:!bg-white/15 hover:text-white";
                @endphp
                <li class="menu-item {{ $isActive ? 'active open' : '' }}">
                    @if($hasChildren)
                        <button type="button" class="menu-link menu-toggle group flex min-h-11 w-full items-center gap-3 rounded-xl bg-transparent px-3 py-2.5 text-left text-sm font-semibold transition {{ $stateClasses }}" data-menu-toggle aria-controls="{{ $targetId }}" aria-expanded="{{ $isActive ? 'true' : 'false' }}">
                            <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                            <i class="fa-solid fa-chevron-down text-[10px] transition-transform {{ $isActive ? 'rotate-180' : '' }}" data-menu-chevron aria-hidden="true"></i>
                        </button>
                        <ul id="{{ $targetId }}" class="menu-sub mt-1 space-y-1 pl-8 {{ $isActive ? '' : 'hidden' }}">
                            @foreach($item['children'] as $child)
                                @php($childActive = request()->routeIs(...($child['patterns'] ?? [])))
                                <li class="menu-item {{ $childActive ? 'active' : '' }}">
                                    <a href="{{ route($child['route']) }}" class="menu-link flex min-h-10 items-center rounded-lg border px-3 py-2 text-sm transition {{ $childActive ? 'border-white/30 bg-white/20 font-semibold text-white' : 'border-white/10 bg-white/[0.03] text-blue-100/80 hover:border-white/20 hover:bg-white/10 hover:text-white' }}">
                                        {{ $child['label'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <a href="{{ route($item['route']) }}" class="menu-link group flex min-h-11 w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-semibold transition {{ $stateClasses }}" @if($isActive) aria-current="page" @endif>
                            <i class="fa-solid {{ $item['icon'] }} w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                        </a>
                    @endif
                </li>
            @endforeach
        </ul>
    </li>
@endforeach
