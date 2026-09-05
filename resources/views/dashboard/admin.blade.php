@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page-title', 'Beranda Admin')
@section('page-subtitle', 'Panduan kerja dan ringkasan sekolah')

@section('content')
    <div class="mb-3 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold text-brand-600 sm:text-sm">Selamat datang, {{ auth()->user()->name }}</p>
            <h1 class="mt-0.5 text-2xl font-extrabold tracking-tight text-slate-950 sm:text-3xl">Apa yang ingin Anda kerjakan?</h1>
            <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500 sm:text-sm">Ikuti urutan persiapan untuk data awal, atau langsung pilih pekerjaan rutin di bawah.</p>
        </div>
        @if($nextSetupStep)
            <a href="{{ route($nextSetupStep['route']) }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2 text-xs font-bold text-white shadow-lg shadow-brand-600/20 transition hover:bg-brand-700 sm:text-sm">
                Lanjutkan: {{ $nextSetupStep['label'] }}
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        @endif
    </div>

    <section class="mb-5 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-soft sm:mb-6">
        <div class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center sm:justify-between sm:px-5 sm:py-4">
            <div class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fa-solid fa-route"></i></span>
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Urutan persiapan sekolah</h2>
                    <p class="text-xs text-slate-500">Kerjakan dari nomor pertama saat menyiapkan periode baru.</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                @if(!$nextSetupStep)
                    <span class="inline-flex h-8 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-emerald-50 px-3 text-[10px] font-bold text-emerald-700 ring-1 ring-emerald-200 sm:text-xs">
                        <i class="fa-solid fa-circle-check" aria-hidden="true"></i> Persiapan utama lengkap
                    </span>
                @endif
                <div class="flex min-w-40 flex-1 items-center gap-2 sm:flex-none">
                    <progress value="{{ $setupProgress }}" max="100" class="h-2 min-w-24 flex-1 overflow-hidden rounded-full accent-emerald-600 sm:w-36">{{ $setupProgress }}%</progress>
                    <span class="text-xs font-extrabold text-brand-700">{{ $setupProgress }}%</span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($setupSteps as $step)
                <a href="{{ route($step['route']) }}" class="group relative flex gap-3 border-b border-slate-100 p-4 transition hover:bg-brand-50/60 sm:border-r sm:p-5">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl text-sm font-extrabold {{ $step['complete'] ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 group-hover:bg-brand-600 group-hover:text-white' }}">
                        @if($step['complete'])
                            <i class="fa-solid fa-check"></i>
                        @else
                            {{ $loop->iteration }}
                        @endif
                    </span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center gap-2 text-sm font-bold text-slate-900">
                            {{ $step['label'] }}
                            <i class="fa-solid fa-arrow-right text-[10px] text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-brand-600"></i>
                        </span>
                        <span class="mt-1 block text-xs leading-5 text-slate-500">{{ $step['description'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mb-5 grid grid-cols-2 gap-3 sm:mb-7 sm:gap-4 lg:grid-cols-4" aria-label="Statistik sekolah">
        @php
            $statCards = [
                ['value' => number_format($totalSiswa), 'label' => 'Siswa Aktif', 'meta' => $siswaBaruBulanIni . ' baru bulan ini', 'icon' => 'fa-user-graduate', 'tone' => 'bg-blue-50 text-blue-600'],
                ['value' => number_format($totalGuru), 'label' => 'Tenaga Pendidik', 'meta' => 'Terdaftar di sistem', 'icon' => 'fa-person-chalkboard', 'tone' => 'bg-emerald-50 text-emerald-600'],
                ['value' => number_format($totalKelas), 'label' => 'Kelas Aktif', 'meta' => ($totalKelas > 0 ? round($totalSiswa / $totalKelas) : 0) . ' siswa / kelas', 'icon' => 'fa-school', 'tone' => 'bg-amber-50 text-amber-600'],
                ['value' => number_format($stats['total_users']), 'label' => 'Akun Pengguna', 'meta' => 'Memiliki kredensial', 'icon' => 'fa-id-badge', 'tone' => 'bg-violet-50 text-violet-600'],
            ];
        @endphp

        @foreach($statCards as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold tracking-tight text-slate-900 sm:text-2xl">{{ $stat['value'] }}</p>
                        <p class="mt-1 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-sm sm:h-10 sm:w-10 {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}"></i></span>
                </div>
                <p class="mt-3 truncate border-t border-slate-100 pt-3 text-[10px] text-slate-500 sm:text-xs">{{ $stat['meta'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="mb-5 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:mb-7 sm:p-4">
        <div class="mb-3 flex items-start gap-2.5">
            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-brand-50 text-brand-600"><i class="fa-solid fa-grip text-xs"></i></span>
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">Pekerjaan yang sering dilakukan</h2>
                <p class="mt-0.5 text-[10px] text-slate-500 sm:text-xs">Pilih berdasarkan tujuan, bukan nama modul.</p>
            </div>
        </div>
        @php
            $tasks = [
                ['label' => 'Masukkan banyak siswa', 'description' => 'Impor data murid dari file Excel', 'route' => 'admin.users.import-siswa', 'icon' => 'fa-file-arrow-up', 'tone' => 'bg-blue-50 text-blue-600'],
                ['label' => 'Tempatkan siswa ke kelas', 'description' => 'Atur anggota kelas secara massal', 'route' => 'admin.manajemen-siswa.index', 'icon' => 'fa-people-group', 'tone' => 'bg-violet-50 text-violet-600'],
                ['label' => 'Buat atau ubah jadwal', 'description' => 'Kelola jam, kelas, mapel, dan guru', 'route' => 'admin.jadwal-pelajaran.index', 'icon' => 'fa-calendar-plus', 'tone' => 'bg-emerald-50 text-emerald-600'],
                ['label' => 'Buat tagihan siswa', 'description' => 'Generate SPP atau tagihan khusus', 'route' => 'admin.keuangan.tagihan.index', 'icon' => 'fa-file-invoice-dollar', 'tone' => 'bg-amber-50 text-amber-600'],
                ['label' => 'Terbitkan pengumuman', 'description' => 'Bagikan informasi kepada warga sekolah', 'route' => 'admin.akademik.pengumuman.index', 'icon' => 'fa-bullhorn', 'tone' => 'bg-rose-50 text-rose-600'],
                ['label' => 'Pantau aktivitas LMS', 'description' => 'Lihat aktivitas kelas dan pembelajaran', 'route' => 'admin.monitoring.lms.index', 'icon' => 'fa-chart-column', 'tone' => 'bg-cyan-50 text-cyan-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 xl:grid-cols-6">
            @foreach($tasks as $task)
                <a href="{{ route($task['route']) }}" title="{{ $task['description'] }}" class="group flex min-h-20 min-w-0 flex-col items-center justify-center rounded-xl border border-slate-200 bg-white px-2 py-2.5 text-center transition hover:-translate-y-0.5 hover:border-brand-500 hover:bg-brand-50/40 hover:shadow-sm sm:min-h-24">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $task['tone'] }} transition group-hover:scale-105"><i class="fa-solid {{ $task['icon'] }} text-sm"></i></span>
                    <span class="mt-2 line-clamp-2 text-[10px] font-bold leading-4 text-slate-800 sm:text-[11px]">{{ $task['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <div class="grid grid-cols-1 gap-4 xl:grid-cols-3">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 p-4 sm:px-5">
                <div>
                    <h2 class="text-sm font-extrabold text-slate-900 sm:text-base"><i class="fa-solid fa-chart-line mr-2 text-brand-600"></i>Pendaftaran siswa</h2>
                    <p class="mt-1 text-xs text-slate-500">Tren siswa yang baru dimasukkan ke sistem.</p>
                </div>
                <select id="timeFilter" class="rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-600 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    <option value="3_bulan">3 bulan</option>
                    <option value="6_bulan" selected>6 bulan</option>
                    <option value="1_tahun">1 tahun</option>
                </select>
            </div>
            <div class="h-72 p-3 sm:h-80 sm:p-5"><canvas id="registrationChart"></canvas></div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 p-4 sm:px-5">
                <h2 class="text-sm font-extrabold text-slate-900 sm:text-base"><i class="fa-solid fa-clock-rotate-left mr-2 text-brand-600"></i>Login terbaru</h2>
                <p class="mt-1 text-xs text-slate-500">Aktivitas akses pengguna terakhir.</p>
            </div>
            <div class="divide-y divide-slate-100 px-4 sm:px-5">
                @forelse($recent_logins as $login)
                    <div class="flex items-center gap-3 py-3">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-extrabold text-slate-600">{{ strtoupper(substr($login->name, 0, 1)) }}</span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-xs font-bold text-slate-900 sm:text-sm">{{ $login->name }}</span>
                            <span class="block truncate text-[10px] text-slate-500 sm:text-xs">{{ $login->role_label }} · {{ \Carbon\Carbon::parse($login->last_login_at)->locale('id')->diffForHumans() }}</span>
                        </span>
                    </div>
                @empty
                    <div class="py-10 text-center text-xs text-slate-500">Belum ada aktivitas login.</div>
                @endforelse
            </div>
        </section>
    </div>

    <script type="application/json" id="admin-dashboard-chart-data">
        {!! json_encode(compact('chartPendaftaran', 'genderData', 'kelasLabels', 'kelasCounts'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!}
    </script>
@endsection

@section('scripts')
    @vite(['resources/js/dashboard/admin.js'])
@endsection
