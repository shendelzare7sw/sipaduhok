@extends('layouts.app')

@section('title', 'Detail Presensi Harian')
@section('page-title', 'Detail Presensi')
@section('page-subtitle', \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y'))

@section('content')
<div class="min-w-0 w-full space-y-4" x-data="{ editing: false }">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm print:hidden"><div><h1 class="text-lg font-extrabold text-slate-900">{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }}</h1><p class="mt-0.5 text-xs text-slate-500">Kelas {{ $kelas->nama_kelas }}</p></div><div class="flex flex-wrap gap-2"><button type="button" x-show="!editing" @click="editing = true" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-amber-50 px-3 text-xs font-bold text-amber-800 hover:bg-amber-100"><i class="fas fa-pen" aria-hidden="true"></i>Edit</button><button type="button" @click="window.print()" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-print" aria-hidden="true"></i>Cetak</button><a href="{{ route('wali.presensi.rekap-harian', ['bulan' => \Carbon\Carbon::parse($tanggal)->month, 'tahun' => \Carbon\Carbon::parse($tanggal)->year]) }}" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a></div></header>

    <header class="hidden border-b border-slate-300 pb-3 print:!block">
        <p class="text-xs font-bold uppercase tracking-wide text-slate-600">SIPADUHOK · Presensi Harian</p>
        <h1 class="mt-1 text-xl font-extrabold text-slate-900">Kelas {{ $kelas->nama_kelas }}</h1>
        <p class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($tanggal)->locale('id')->translatedFormat('l, d F Y') }} · {{ $kelas->cabang->nama_cabang ?? 'Cabang belum diatur' }}</p>
    </header>

    <dl class="grid grid-cols-2 gap-2.5 sm:grid-cols-4 print:!grid-cols-4">
        @foreach([['label' => 'Hadir', 'value' => $summary['hadir'], 'tone' => 'text-emerald-800 bg-emerald-50'], ['label' => 'Sakit', 'value' => $summary['sakit'], 'tone' => 'text-amber-800 bg-amber-50'], ['label' => 'Izin', 'value' => $summary['izin'], 'tone' => 'text-sky-800 bg-sky-50'], ['label' => 'Alpha', 'value' => $summary['alpha'], 'tone' => 'text-rose-800 bg-rose-50']] as $stat)
            <div class="rounded-xl border border-slate-200 bg-white px-3 py-3 shadow-sm print:shadow-none"><dt class="text-[11px] font-bold uppercase text-slate-500">{{ $stat['label'] }}</dt><dd class="mt-1 text-lg font-extrabold {{ $stat['tone'] }} inline-flex rounded-lg px-2 py-0.5">{{ $stat['value'] }}</dd></div>
        @endforeach
    </dl>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm print:overflow-visible print:shadow-none" aria-labelledby="detail-presensi-title">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3"><h2 id="detail-presensi-title" class="text-sm font-extrabold text-slate-900">Presensi siswa</h2><div x-show="editing" x-cloak class="flex gap-2 print:hidden"><button type="button" @click="editing = false; $refs.editForm.reset()" class="min-h-9 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" form="formEditPresensi" class="min-h-9 rounded-lg bg-emerald-700 px-3 text-xs font-bold text-white">Simpan</button></div></div>
        <form id="formEditPresensi" x-ref="editForm" action="{{ route('wali.presensi.input-harian') }}" method="POST">@csrf<input type="hidden" name="kelas_id" value="{{ $kelas->id }}"><input type="hidden" name="tanggal" value="{{ $tanggal }}">
            <div class="hidden grid-cols-[48px_minmax(0,1fr)_160px_minmax(180px,1fr)] bg-slate-50 px-4 py-2 text-[11px] font-bold uppercase tracking-wide text-slate-500 lg:grid"><span>No</span><span>Siswa</span><span>Status</span><span>Keterangan</span></div>
            <div class="divide-y divide-slate-100">
                @forelse($siswaList as $index => $siswa)
                    @php($presensi = $presensiData->get($siswa->id))
                    @php($status = $presensi?->status)
                    <div class="grid min-w-0 grid-cols-2 gap-2 px-4 py-3 lg:grid-cols-[48px_minmax(0,1fr)_160px_minmax(180px,1fr)] lg:items-center lg:gap-0 lg:py-2.5 print:break-inside-avoid">
                        <span class="hidden text-xs text-slate-500 lg:block">{{ $index + 1 }}</span>
                        <div class="col-span-2 min-w-0 lg:col-span-1"><p class="truncate text-sm font-bold text-slate-900" title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</p><p class="text-[11px] text-slate-500">NIS: {{ $siswa->nis ?? $siswa->nisn ?? '-' }}</p></div>
                        <input type="hidden" name="presensi[{{ $index }}][siswa_id]" value="{{ $siswa->id }}" :disabled="!editing">
                        <div class="min-w-0"><p x-show="!editing" class="inline-flex rounded-lg px-2.5 py-1 text-xs font-bold print:!inline-flex {{ match($status) { 'hadir' => 'bg-emerald-50 text-emerald-800', 'sakit' => 'bg-amber-50 text-amber-800', 'izin' => 'bg-sky-50 text-sky-800', 'alpha' => 'bg-rose-50 text-rose-800', default => 'bg-slate-100 text-slate-600' } }}">{{ $status ? ucfirst($status) : 'Belum diisi' }}</p><label x-show="editing" x-cloak class="block text-[11px] font-bold text-slate-600 lg:pr-3 print:!hidden">Status<select name="presensi[{{ $index }}][status]" :disabled="!editing" class="mt-1 h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-xs font-semibold text-slate-800 lg:mt-0"><option value="hadir" @selected(($status ?? 'hadir') === 'hadir')>Hadir</option><option value="sakit" @selected($status === 'sakit')>Sakit</option><option value="izin" @selected($status === 'izin')>Izin</option><option value="alpha" @selected($status === 'alpha')>Alpha</option></select></label></div>
                        <div class="min-w-0"><p x-show="!editing" class="break-words text-xs text-slate-600 print:!block">{{ $presensi?->keterangan ?: '-' }}</p><label x-show="editing" x-cloak class="block text-[11px] font-bold text-slate-600 print:!hidden">Keterangan<input type="text" name="presensi[{{ $index }}][keterangan]" :disabled="!editing" value="{{ $presensi?->keterangan ?? '' }}" placeholder="Opsional" class="mt-1 h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-xs text-slate-800 lg:mt-0"></label></div>
                    </div>
                @empty
                    <p class="px-4 py-8 text-center text-sm text-slate-500">Tidak ada siswa aktif di kelas ini.</p>
                @endforelse
            </div>
        </form>
    </section>
</div>
@endsection
