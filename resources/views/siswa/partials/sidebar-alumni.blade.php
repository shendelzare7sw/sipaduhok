{{--
    Navigasi minimal untuk siswa berstatus LULUS (alumni): dashboard alumni dan keluar.
    Dipakai dashboard alumni dan otomatis oleh sidebar-sia (profil, notifikasi, pengaturan akun),
    agar alumni tidak melihat menu yang aksesnya sudah ditutup middleware student.active.
--}}
@php
    $alumniDashboardAktif = request()->routeIs('siswa.sia.dashboard');
@endphp
<li class="mb-5">
    <div class="mb-2 px-3">
        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-blue-100/90">Alumni</p>
    </div>
    <ul class="space-y-1">
        <li class="menu-item {{ $alumniDashboardAktif ? 'active' : '' }}">
            <a href="{{ route('siswa.sia.dashboard') }}" class="menu-link group flex min-h-11 w-full items-center gap-3 rounded-xl !border-0 !ring-0 px-3 py-2.5 text-left text-sm font-semibold transition {{ $alumniDashboardAktif ? '!bg-white/25 text-white shadow-lg shadow-slate-950/15' : '!bg-white/[0.07] text-blue-50/90 hover:!bg-white/15 hover:text-white' }}">
                <i class="fa-solid fa-graduation-cap w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                <span class="min-w-0 flex-1">
                    <span class="block truncate">Dashboard Alumni</span>
                    <span class="mt-0.5 block truncate text-[10px] font-medium {{ $alumniDashboardAktif ? 'text-sky-50' : 'text-sky-100/80 group-hover:text-white' }}">Profil dan rapor terakhir</span>
                </span>
            </a>
        </li>
        <li class="menu-item">
            <form method="POST" action="{{ route('logout') }}" data-confirm="logout" class="m-0 p-0">
                @csrf
                <button type="submit" class="menu-link group flex min-h-11 w-full items-center gap-3 rounded-xl !border-0 !ring-0 !bg-white/[0.07] px-3 py-2.5 text-left text-sm font-semibold text-blue-50/90 transition hover:!bg-white/15 hover:text-white">
                    <i class="fa-solid fa-right-from-bracket w-5 shrink-0 text-center text-sm" aria-hidden="true"></i>
                    <span class="min-w-0 flex-1">
                        <span class="block truncate">Keluar</span>
                        <span class="mt-0.5 block truncate text-[10px] font-medium text-sky-100/80 group-hover:text-white">Akhiri sesi Anda</span>
                    </span>
                </button>
            </form>
        </li>
    </ul>
</li>
