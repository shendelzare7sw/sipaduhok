@extends('layouts.app')

@section('title', 'Riwayat Presensi - ' . $siswa->nama_lengkap)
@section('page-title', 'Riwayat Presensi')

@section('content')
<div class="min-w-0 w-full space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-extrabold text-slate-800">Riwayat Presensi</h4>
            <p class="mt-0.5 text-xs text-slate-500 sm:text-sm">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-1">·</span>
                <i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('wali-siswa.presensi.ajukan-izin', $siswa->id) }}" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-bold text-white transition hover:bg-amber-600">
                <i class="fa-solid fa-file-medical"></i> Ajukan Izin / Sakit
            </a>
            <a href="{{ route('wali-siswa.presensi.anak', $siswa->id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    @php
        $summaryStats = [
            ['label' => 'Hadir', 'value' => $rekap['hadir'], 'color' => 'emerald'],
            ['label' => 'Sakit', 'value' => $rekap['sakit'], 'color' => 'amber'],
            ['label' => 'Izin',  'value' => $rekap['izin'],  'color' => 'blue'],
            ['label' => 'Alpha', 'value' => $rekap['alpha'], 'color' => 'red'],
        ];
        $borderColors = ['emerald' => 'border-l-emerald-500', 'amber' => 'border-l-amber-500', 'blue' => 'border-l-blue-500', 'red' => 'border-l-red-500'];
    @endphp
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
        @foreach($summaryStats as $s)
            <div class="relative overflow-hidden rounded-xl border border-slate-200 border-l-4 {{ $borderColors[$s['color']] }} bg-white p-3 shadow-sm sm:p-4">
                <div class="text-[11px] font-extrabold uppercase tracking-wide text-slate-500">{{ $s['label'] }}</div>
                <div class="mt-1 text-xl font-extrabold text-slate-800 sm:text-2xl">{{ $s['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm">
        <form action="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" method="GET" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4 items-end">
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-600">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-600">Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}"
                       class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
            </div>
            <div>
                <label class="mb-1 block text-xs font-bold text-slate-600">Status</label>
                <select name="status" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700">
                    <i class="fa-solid fa-search mr-1"></i> Filter
                </button>
                <a href="{{ route('wali-siswa.presensi.riwayat-presensi', $siswa->id) }}" class="flex-1 rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                    <i class="fa-solid fa-sync-alt mr-1"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 bg-slate-50">
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Tanggal</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Hari</th>
                        <th class="px-4 py-2.5 text-center text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Status</th>
                        <th class="px-4 py-2.5 text-center text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Validasi</th>
                        <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Keterangan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $item)
                        @php
                            $statusBadgeMap = [
                                'hadir' => 'bg-emerald-100 text-emerald-700',
                                'sakit' => 'bg-amber-100 text-amber-700',
                                'izin'  => 'bg-blue-100 text-blue-700',
                                'alpha' => 'bg-red-100 text-red-700',
                            ];
                            $badgeCls = $statusBadgeMap[$item->status] ?? 'bg-slate-100 text-slate-600';

                            $validasiBadge = match($item->status_validasi) {
                                'disetujui' => 'bg-emerald-100 text-emerald-700',
                                'ditolak'   => 'bg-red-100 text-red-700',
                                'pending'   => 'bg-amber-100 text-amber-700',
                                default     => 'bg-slate-100 text-slate-500',
                            };
                            $validasiLabel = match($item->status_validasi) {
                                'disetujui' => 'Disetujui',
                                'ditolak'   => 'Ditolak',
                                'pending'   => 'Menunggu',
                                default     => '-',
                            };

                            $buktiPath = $item->bukti_file;
                            $buktiUrl = $buktiPath ? asset('storage/' . $buktiPath) : null;
                            $buktiExtension = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                            $isBuktiImage = in_array($buktiExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isBuktiPdf = $buktiExtension === 'pdf';
                        @endphp
                        <tr class="hover:bg-slate-50 transition" x-data="{ showBukti: false }">
                            <td class="px-4 py-2.5 font-bold text-slate-800">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}</td>
                            <td class="px-4 py-2.5 text-slate-500">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l') }}</td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $badgeCls }}">{{ ucfirst($item->status) }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-center">
                                <span class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $validasiBadge }}">{{ $validasiLabel }}</span>
                            </td>
                            <td class="px-4 py-2.5 text-sm text-slate-500">
                                <div>{{ $item->keterangan ?: 'Tidak ada catatan' }}</div>
                                @if($buktiPath)
                                    <button type="button" @click="showBukti = true"
                                            class="mt-1 inline-flex items-center gap-1 rounded-lg border border-brand-300 px-2.5 py-1 text-[11px] font-semibold text-brand-700 transition hover:bg-brand-50">
                                        <i class="fa-solid fa-paperclip"></i> Lihat Bukti
                                    </button>

                                    {{-- Alpine Dialog --}}
                                    <template x-teleport="body">
                                        <div x-show="showBukti" x-cloak
                                             class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4"
                                             @keydown.escape.window="showBukti = false">
                                            <div @click.outside="showBukti = false"
                                                 x-show="showBukti" x-transition
                                                 class="relative w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-2xl">
                                                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                                                    <h5 class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                                                        <i class="fa-solid fa-paperclip text-brand-600"></i> Lampiran Bukti
                                                    </h5>
                                                    <button @click="showBukti = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                                        <i class="fa-solid fa-xmark"></i>
                                                    </button>
                                                </div>
                                                <div>
                                                    @if($isBuktiImage)
                                                        <div class="p-4 text-center">
                                                            <img src="{{ $buktiUrl }}" alt="Lampiran bukti presensi {{ $siswa->nama_lengkap }}" class="mx-auto max-h-[75vh] rounded-lg object-contain">
                                                        </div>
                                                    @elseif($isBuktiPdf)
                                                        <iframe src="{{ $buktiUrl }}" title="Lampiran bukti presensi {{ $siswa->nama_lengkap }}" class="h-[75vh] w-full border-0"></iframe>
                                                    @else
                                                        <div class="px-5 py-12 text-center">
                                                            <i class="fa-solid fa-file text-5xl text-slate-300"></i>
                                                            <p class="mt-3 text-sm text-slate-500">Format lampiran tidak dapat dipreview.</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center">
                                <i class="fa-solid fa-calendar-times mb-2 block text-3xl text-slate-300"></i>
                                <p class="text-sm text-slate-500">Tidak ada data presensi ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="border-t border-slate-200 bg-slate-50 px-5 py-3">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
