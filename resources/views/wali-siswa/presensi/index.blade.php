@extends('layouts.app')

@section('title', 'Presensi - ' . $siswa->nama_lengkap)
@section('page-title', 'Presensi')

@section('content')
<div class="min-w-0 w-full space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-extrabold text-slate-800">Presensi Kehadiran</h4>
            <p class="mt-0.5 text-xs text-slate-500 sm:text-sm">Pantau rekap kehadiran anak pada bulan berjalan.</p>
        </div>
        <div class="grid grid-cols-2 gap-2 md:flex md:flex-wrap md:justify-end">
            <a href="{{ route('wali-siswa.presensi.ajukan-izin', $siswa->id) }}" class="inline-flex w-full min-w-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-amber-500 px-2 py-2 text-[11px] font-bold text-white shadow-md shadow-amber-500/25 transition hover:bg-amber-600 sm:px-3 sm:text-xs md:w-auto">
                <i class="fa-solid fa-file-medical"></i>
                <span class="sm:hidden">Ajukan Izin</span>
                <span class="hidden sm:inline">Ajukan Izin / Sakit</span>
            </a>
            <a href="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" class="inline-flex w-full min-w-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-slate-200 bg-white px-2 py-2 text-[11px] font-bold text-slate-700 transition hover:border-brand-400 hover:text-brand-600 sm:px-3 sm:text-xs md:w-auto">
                <i class="fa-solid fa-calendar-alt"></i> Riwayat Presensi
            </a>
            <a href="{{ route('wali-siswa.presensi.riwayat-izin', $siswa->id) }}" class="inline-flex w-full min-w-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg bg-brand-600 px-2 py-2 text-[11px] font-bold text-white shadow-md shadow-brand-600/20 transition hover:bg-brand-700 sm:px-3 sm:text-xs md:w-auto">
                <i class="fa-solid fa-history"></i> Riwayat Pengajuan
            </a>
            <a href="{{ route('wali-siswa.dashboard') }}" class="inline-flex w-full min-w-0 items-center justify-center gap-1.5 whitespace-nowrap rounded-lg border border-slate-300 px-2 py-2 text-[11px] font-semibold text-slate-600 transition hover:bg-slate-50 sm:px-3 sm:text-xs md:w-auto">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Student Info Card --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4">
        <div class="flex min-w-0 items-start gap-3 sm:items-center sm:gap-4">
            <div class="flex h-14 w-14 aspect-square shrink-0 items-center justify-center overflow-hidden rounded-full bg-brand-50 shadow-lg shadow-brand-600/20 sm:h-16 sm:w-16">
                @if($siswa->user && $siswa->user->foto_profil)
                    <img src="{{ asset('storage/' . $siswa->user->foto_profil) }}" alt="avatar" class="h-full w-full object-cover">
                @elseif($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="avatar" class="h-full w-full object-cover">
                @else
                        <span class="flex h-full w-full items-center justify-center rounded-full bg-brand-600 text-2xl font-bold text-white">
                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div class="min-w-0">
                <div class="break-words text-base font-extrabold leading-snug text-slate-800">{{ $siswa->nama_lengkap }}</div>
                <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs font-semibold text-slate-500 sm:gap-x-4">
                    <span><i class="fa-solid fa-id-card mr-1"></i>NISN: {{ $siswa->nisn }}</span>
                    <span><i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                    <span><i class="fa-solid fa-building mr-1"></i>{{ $siswa->cabang->nama_cabang ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Grid --}}
    @php
        $stats = [
            ['label' => 'Hari Hadir',  'value' => $rekap['hadir'],  'icon' => 'fa-check-circle',  'color' => 'emerald'],
            ['label' => 'Hari Sakit',  'value' => $rekap['sakit'],  'icon' => 'fa-notes-medical', 'color' => 'amber'],
            ['label' => 'Hari Izin',   'value' => $rekap['izin'],   'icon' => 'fa-file-alt',      'color' => 'blue'],
            ['label' => 'Hari Alpha',  'value' => $rekap['alpha'],  'icon' => 'fa-times-circle',  'color' => 'red'],
        ];
    @endphp
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
        @foreach($stats as $stat)
            @php
                $colorMap = [
                    'emerald' => ['border' => 'border-l-emerald-500', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-500'],
                    'amber'   => ['border' => 'border-l-amber-500',   'bg' => 'bg-amber-50',   'text' => 'text-amber-500'],
                    'blue'    => ['border' => 'border-l-blue-500',    'bg' => 'bg-blue-50',     'text' => 'text-blue-500'],
                    'red'     => ['border' => 'border-l-red-500',     'bg' => 'bg-red-50',      'text' => 'text-red-500'],
                ];
                $c = $colorMap[$stat['color']];
            @endphp
            <div class="relative min-w-0 overflow-hidden rounded-xl border border-slate-200 border-l-4 {{ $c['border'] }} bg-white p-3 shadow-sm sm:p-4">
                <div class="truncate text-[10px] font-extrabold uppercase tracking-wide text-slate-500 sm:text-[11px]">{{ $stat['label'] }}</div>
                <div class="mt-1 text-xl font-extrabold text-slate-800 sm:text-2xl">{{ $stat['value'] }}</div>
                <div class="absolute right-2.5 top-2.5 flex h-8 w-8 items-center justify-center rounded-lg {{ $c['bg'] }} {{ $c['text'] }} sm:right-3 sm:top-3 sm:h-9 sm:w-9">
                    <i class="fa-solid {{ $stat['icon'] }} text-sm"></i>
                </div>
            </div>
        @endforeach
    </div>

    {{-- History Header --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 px-4 py-3">
            <div>
                <h5 class="text-base font-extrabold text-slate-800">
                    <i class="fa-solid fa-calendar-check mr-2 text-brand-600"></i>Riwayat Presensi Bulan Ini
                </h5>
                <p class="mt-0.5 text-xs text-slate-500">Bulan {{ now()->translatedFormat('F Y') }}</p>
            </div>
            <a href="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-bold text-slate-600 transition hover:border-brand-400 hover:text-brand-600">
                <i class="fa-solid fa-calendar-alt"></i> Lihat Semua Riwayat
            </a>
        </div>

        @forelse($presensi as $minggu => $dataList)
            {{-- Week Label --}}
            <div class="border-b border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-extrabold uppercase tracking-wide text-brand-600">
                <i class="fa-solid fa-calendar-week mr-1"></i> Minggu ke-{{ $minggu }}
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 bg-slate-50/50">
                            <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Tanggal</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Hari</th>
                            <th class="px-4 py-2.5 text-center text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($dataList as $item)
                            @php
                                $statusLabel = strtoupper($item->status);
                                $badgeClasses = 'bg-slate-100 text-slate-600';

                                if ($item->status === 'hadir') {
                                    $badgeClasses = 'bg-emerald-100 text-emerald-700';
                                } elseif ($item->status === 'sakit' || $item->status === 'izin') {
                                    if ($item->status_validasi === 'disetujui') {
                                        $badgeClasses = $item->status === 'sakit' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700';
                                    } elseif ($item->status_validasi === 'ditolak') {
                                        $statusLabel = 'DITOLAK';
                                        $badgeClasses = 'bg-red-100 text-red-700';
                                    } else {
                                        $statusLabel = 'MENUNGGU VALIDASI';
                                        $badgeClasses = 'bg-amber-100 text-amber-700';
                                    }
                                } elseif ($item->status === 'alpha') {
                                    if ($item->status_validasi === 'ditolak') {
                                        $statusLabel = 'DITOLAK';
                                    }
                                    $badgeClasses = 'bg-red-100 text-red-700';
                                }
                            @endphp
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-4 py-2.5 font-bold text-slate-800">{{ $item->tanggal->format('d F Y') }}</td>
                                <td class="px-4 py-2.5 text-slate-500">{{ ucfirst($item->tanggal->locale('id')->dayName) }}</td>
                                <td class="px-4 py-2.5 text-center">
                                    <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $badgeClasses }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-4 py-2.5 text-sm text-slate-500">
                                    <div>{{ $item->keterangan ?? 'Tidak ada catatan' }}</div>
                                    @if($item->bukti_file)
                                        <a href="{{ asset('storage/' . $item->bukti_file) }}" target="_blank" class="mt-1 inline-flex items-center gap-1 text-xs font-bold text-brand-600 hover:underline">
                                            <i class="fa-solid fa-paperclip"></i> Lihat Bukti
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @empty
            <div class="px-5 py-8 text-center">
                <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                    <i class="fa-solid fa-calendar-times text-2xl"></i>
                </div>
                <h5 class="font-bold text-slate-500">Belum Ada Data Presensi Bulan Ini</h5>
                <p class="mt-1 text-sm text-slate-400">Data presensi akan diperbarui otomatis oleh wali kelas setelah pembelajaran.</p>
            </div>
        @endforelse
    </div>

</div>
@endsection
