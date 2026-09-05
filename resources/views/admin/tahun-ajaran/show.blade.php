@extends('layouts.app')

@section('title', 'Detail Tahun Ajaran')
@section('page-title', 'Detail Tahun Ajaran')
@section('page-subtitle', $tahunAjaran->nama_tahun_ajaran)

@section('content')
@php
    $start = \Carbon\Carbon::parse($tahunAjaran->tanggal_mulai);
    $end = \Carbon\Carbon::parse($tahunAjaran->tanggal_selesai);
    $periods = $tahunAjaran->getSemesterPeriods();
    $currentSemester = \App\Models\TahunAjaran::getCurrentSemester();
    $raporPeriods = [
        ['label' => 'PTS Ganjil', 'period' => $tahunAjaran->getRaporPeriod('ganjil', 'tengah_semester'), 'tone' => 'border-violet-200 bg-violet-50 text-violet-900', 'automatic' => !$tahunAjaran->tanggal_akhir_pts_ganjil],
        ['label' => 'PAS Ganjil', 'period' => $tahunAjaran->getRaporPeriod('ganjil', 'akhir_semester'), 'tone' => 'border-amber-200 bg-amber-50 text-amber-900', 'automatic' => false],
        ['label' => 'PTS Genap', 'period' => $tahunAjaran->getRaporPeriod('genap', 'tengah_semester'), 'tone' => 'border-violet-200 bg-violet-50 text-violet-900', 'automatic' => !$tahunAjaran->tanggal_akhir_pts_genap],
        ['label' => 'PAS Genap', 'period' => $tahunAjaran->getRaporPeriod('genap', 'akhir_semester'), 'tone' => 'border-blue-200 bg-blue-50 text-blue-900', 'automatic' => false],
    ];
@endphp

<div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_300px]">
    <div class="min-w-0 space-y-5">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5"><div class="min-w-0"><h2 class="truncate text-lg font-extrabold text-slate-900">{{ $tahunAjaran->nama_tahun_ajaran }}</h2><p class="mt-1 text-xs text-slate-500">Ringkasan periode akademik</p></div><span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5 text-[11px] font-bold {{ $tahunAjaran->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}"><i class="fas {{ $tahunAjaran->is_active ? 'fa-check-circle' : 'fa-pause-circle' }}" aria-hidden="true"></i>{{ $tahunAjaran->is_active ? 'Aktif' : 'Tidak Aktif' }}</span></header>
            <dl class="grid grid-cols-2 divide-x divide-y divide-slate-100 sm:grid-cols-4"><div class="p-4"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Mulai</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ $start->format('d M Y') }}</dd></div><div class="p-4"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Selesai</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ $end->format('d M Y') }}</dd></div><div class="p-4"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Durasi</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ $start->diffInMonths($end) }} bulan</dd></div><div class="p-4"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Diperbarui</dt><dd class="mt-1 text-sm font-bold text-slate-800">{{ $tahunAjaran->updated_at->format('d M Y') }}</dd></div></dl>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3"><div><h2 class="text-base font-extrabold text-slate-900"><i class="fas fa-calendar-alt mr-2 text-emerald-600" aria-hidden="true"></i>Periode Semester</h2><p class="mt-1 text-xs text-slate-500">Batas semester yang dipakai oleh jadwal dan laporan.</p></div><a href="{{ route('admin.tahun-ajaran.edit', $tahunAjaran->id) }}" class="text-xs font-bold text-brand-700 no-underline hover:underline">Atur</a></div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                @foreach(['ganjil' => ['Semester Ganjil', 'fa-sun', 'border-amber-200 bg-amber-50 text-amber-900'], 'genap' => ['Semester Genap', 'fa-snowflake', 'border-blue-200 bg-blue-50 text-blue-900']] as $semester => $meta)
                    <article class="rounded-xl border p-4 {{ $meta[2] }}"><div class="flex items-center justify-between gap-2"><h3 class="text-sm font-extrabold"><i class="fas {{ $meta[1] }} mr-1.5" aria-hidden="true"></i>{{ $meta[0] }}</h3>@if($tahunAjaran->is_active && $currentSemester === $semester)<span class="rounded-full bg-emerald-600 px-2 py-0.5 text-[9px] font-bold uppercase text-white">Berjalan</span>@endif</div><p class="mt-3 text-xs font-semibold">{{ $periods[$semester]['start']->format('d M Y') }} - {{ $periods[$semester]['end']->format('d M Y') }}</p></article>
                @endforeach
            </div>
            @if(!$tahunAjaran->tanggal_mulai_genap)<p class="mt-3 rounded-xl bg-slate-50 p-3 text-[11px] leading-5 text-slate-600"><i class="fas fa-info-circle mr-1 text-slate-400" aria-hidden="true"></i>Pembagian semester masih menggunakan perhitungan otomatis.</p>@endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><h2 class="text-base font-extrabold text-slate-900"><i class="fas fa-flag-checkered mr-2 text-violet-600" aria-hidden="true"></i>Periode Rapor</h2><p class="mt-1 text-xs text-slate-500">Rentang PTS dan PAS untuk perhitungan kehadiran rapor.</p><div class="mt-4 grid grid-cols-2 gap-3 lg:grid-cols-4">@foreach($raporPeriods as $item)<article class="min-w-0 rounded-xl border p-3 {{ $item['tone'] }}"><h3 class="text-xs font-extrabold">{{ $item['label'] }}</h3><p class="mt-2 text-[11px] font-semibold leading-5">{{ $item['period']['start']->format('d M Y') }}<br>{{ $item['period']['end']->format('d M Y') }}</p>@if($item['automatic'])<span class="mt-2 inline-flex rounded-full bg-white/70 px-2 py-0.5 text-[9px] font-bold">Otomatis</span>@endif</article>@endforeach</div></section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><header class="flex items-center justify-between border-b border-slate-200 p-4 sm:p-5"><div><h2 class="text-base font-extrabold text-slate-900"><i class="fas fa-chalkboard mr-2 text-brand-600" aria-hidden="true"></i>Kelas Terkait</h2><p class="mt-1 text-xs text-slate-500">Kelas yang memakai tahun ajaran ini.</p></div><span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">{{ $tahunAjaran->kelas->count() }} kelas</span></header>@if($tahunAjaran->kelas->isNotEmpty())<div class="overflow-x-auto"><table class="w-full min-w-[560px] text-left text-sm"><thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3">Nama Kelas</th><th class="px-5 py-3">Jenjang</th><th class="px-5 py-3">Cabang</th></tr></thead><tbody class="divide-y divide-slate-100">@foreach($tahunAjaran->kelas as $kelas)<tr><td class="px-5 py-3 font-bold text-slate-800">{{ $kelas->nama_kelas }}</td><td class="px-5 py-3 text-xs text-slate-600">{{ $kelas->jenjang ?? '-' }}</td><td class="px-5 py-3 text-xs text-slate-600">{{ $kelas->cabang->nama_cabang ?? '-' }}</td></tr>@endforeach</tbody></table></div>@else<div class="p-10 text-center text-xs text-slate-500"><i class="fas fa-folder-open mb-3 block text-3xl text-slate-300" aria-hidden="true"></i>Belum ada kelas yang terkait.</div>@endif</section>
    </div>

    <aside class="space-y-4">
        <a href="{{ route('admin.tahun-ajaran.index') }}" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-slate-100 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke Daftar</a>
        <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><h2 class="text-sm font-extrabold text-slate-900">Tindakan</h2><div class="mt-3 grid gap-2"><a href="{{ route('admin.tahun-ajaran.edit', $tahunAjaran->id) }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-amber-50 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit Data</a>@if(!$tahunAjaran->is_active)<form action="{{ route('admin.tahun-ajaran.activate', $tahunAjaran->id) }}" method="POST" data-confirm data-confirm-title="Aktifkan tahun ajaran?" data-confirm-message="Periode aktif saat ini akan dinonaktifkan." data-confirm-text="Ya, aktifkan">@csrf<button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-emerald-50 text-xs font-bold text-emerald-700 hover:bg-emerald-100"><i class="fas fa-check" aria-hidden="true"></i>Aktifkan</button></form>@endif</div></section>
        <section class="rounded-2xl border border-red-200 bg-red-50 p-4"><h2 class="text-sm font-extrabold text-red-900"><i class="fas fa-exclamation-triangle mr-1.5" aria-hidden="true"></i>Zona Berbahaya</h2><p class="mt-2 text-xs leading-5 text-red-800">Penghapusan permanen dan tidak dapat dibatalkan.</p><form action="{{ route('admin.tahun-ajaran.destroy', $tahunAjaran->id) }}" method="POST" class="mt-3" data-confirm data-confirm-title="Hapus tahun ajaran?" data-confirm-message="{{ $tahunAjaran->nama_tahun_ajaran }} akan dihapus permanen." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="inline-flex h-10 w-full items-center justify-center gap-2 rounded-xl bg-red-600 text-xs font-bold text-white hover:bg-red-700"><i class="fas fa-trash" aria-hidden="true"></i>Hapus Tahun Ajaran</button></form></section>
    </aside>
</div>
@endsection
