@extends('layouts.app')

@section('title', 'Siswa Kelas '.$kelas->nama_kelas)
@section('page-title', 'Kelola Siswa Kelas')
@section('page-subtitle', $kelas->nama_kelas.' - '.($kelas->tahunAjaran->nama_tahun_ajaran ?? ''))

@section('content')
<div class="min-w-0 w-full space-y-4">
    <header class="flex flex-wrap items-start justify-between gap-3 rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 p-4 text-white shadow-sm sm:p-5">
        <div class="flex min-w-0 items-start gap-3">
            <a href="{{ route('admin.manajemen-siswa.index') }}" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/10 text-white no-underline ring-1 ring-inset ring-white/20 hover:bg-white/20"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
            <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wide text-blue-200">Penempatan per kelas</p><h2 class="truncate text-xl font-extrabold !text-white sm:text-2xl">Kelas {{ $kelas->nama_kelas }}</h2><p class="mt-1 truncate text-xs text-blue-100">{{ $kelas->jenjang }} &middot; {{ $kelas->cabang->nama_cabang ?? 'Tanpa cabang' }} &middot; {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</p>@if($kelas->waliKelas)<p class="mt-1 truncate text-xs text-blue-100"><i class="fas fa-user-tie mr-1"></i>{{ $kelas->waliKelas->nama_lengkap }}</p>@endif</div>
        </div>
        <a href="{{ route('admin.manajemen-siswa.print',['kelas_id'=>$kelas->id]) }}" target="_blank" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-white px-4 text-xs font-bold text-brand-800 no-underline hover:bg-blue-50"><i class="fas fa-print"></i>Cetak kelas</a>
    </header>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
        @foreach([
            ['value'=>$stats['totalSiswa'],'label'=>'Total siswa','tone'=>'bg-blue-50 text-blue-700','icon'=>'fas fa-users'],
            ['value'=>$stats['siswaLaki'],'label'=>'Laki-laki','tone'=>'bg-cyan-50 text-cyan-700','icon'=>'fas fa-mars'],
            ['value'=>$stats['siswaPerempuan'],'label'=>'Perempuan','tone'=>'bg-fuchsia-50 text-fuchsia-700','icon'=>'fas fa-venus'],
            ['value'=>max(0,$stats['sisaKuota']),'label'=>'Sisa kuota','tone'=>$stats['sisaKuota'] > 0 ? 'bg-emerald-50 text-emerald-700' : 'bg-red-50 text-red-700','icon'=>'fas fa-chair'],
        ] as $stat)
            <article class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="{{ $stat['icon'] }}"></i></span><div><strong class="block text-xl font-extrabold text-slate-950">{{ $stat['value'] }}</strong><span class="text-[11px] font-bold uppercase tracking-wide text-slate-500">{{ $stat['label'] }}</span></div></article>
        @endforeach
    </section>

    <div class="grid min-w-0 gap-4 xl:grid-cols-[minmax(280px,.72fr)_minmax(0,1.28fr)]">
        <section class="self-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-user-plus mr-2 text-brand-600"></i>Tambahkan siswa</h3><p class="mt-1 text-xs text-slate-500">Hanya siswa aktif tanpa kelas dari cabang yang sama.</p></header>
            <div class="p-4 sm:p-5">
                @if($stats['sisaKuota'] <= 0)
                    <div class="flex gap-3 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700"><i class="fas fa-circle-exclamation mt-0.5"></i><p><strong>Kelas sudah penuh.</strong><br>Naikkan kuota kelas atau keluarkan siswa terlebih dahulu.</p></div>
                @elseif($availableSiswa->isNotEmpty())
                    <form action="{{ route('admin.manajemen-siswa.add-to-kelas',$kelas) }}" method="POST" class="space-y-3">@csrf<label class="block"><span class="mb-1.5 block text-xs font-bold text-slate-700">Pilih siswa</span><select name="siswa_id" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Pilih siswa tanpa kelas</option>@foreach($availableSiswa as $candidate)<option value="{{ $candidate->id }}">{{ $candidate->nama_lengkap }} ({{ $candidate->nisn ?: 'tanpa NISN' }})</option>@endforeach</select></label><button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-plus"></i>Tambahkan ke kelas</button></form>
                @else
                    <div class="py-5 text-center"><i class="fas fa-circle-check text-3xl text-emerald-500"></i><h4 class="mt-3 font-bold text-slate-900">Tidak ada kandidat</h4><p class="mt-1 text-xs text-slate-500">Semua siswa di cabang ini sudah memiliki kelas.</p></div>
                @endif
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-users mr-2 text-brand-600"></i>Anggota kelas</h3><p class="mt-1 text-xs text-slate-500">{{ $stats['totalSiswa'] }} dari {{ $kelas->kuota_siswa }} kursi terisi.</p></header>
            <div class="divide-y divide-slate-100 lg:hidden">
                @forelse($siswaList as $siswa)
                    <article class="flex items-center gap-3 p-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $siswa->jenis_kelamin === 'P' ? 'bg-fuchsia-50 text-fuchsia-700' : 'bg-blue-50 text-blue-700' }} text-xs font-extrabold">{{ strtoupper(substr($siswa->nama_lengkap,0,1)) }}</span><div class="min-w-0 flex-1"><a href="{{ route('admin.manajemen-siswa.show',$siswa) }}" class="block truncate text-sm font-extrabold text-slate-950 no-underline hover:text-brand-700">{{ $siswa->nama_lengkap }}</a><span class="block truncate text-xs text-slate-500">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }} &middot; NISN {{ $siswa->nisn ?: '-' }}</span></div><form action="{{ route('admin.manajemen-siswa.remove-from-kelas',$kelas) }}" method="POST" data-confirm data-confirm-title="Keluarkan siswa?" data-confirm-message="{{ $siswa->nama_lengkap }} akan dikeluarkan dari Kelas {{ $kelas->nama_kelas }}, tetapi datanya tetap tersimpan." data-confirm-text="Ya, keluarkan">@csrf<input type="hidden" name="siswa_id" value="{{ $siswa->id }}"><x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-user-minus" label="Keluarkan siswa" /></form></article>
                @empty
                    <div class="p-10 text-center text-sm text-slate-500">Belum ada siswa di kelas ini.</div>
                @endforelse
            </div>
            <div class="hidden overflow-x-auto lg:block"><table class="w-full table-fixed text-left text-sm"><colgroup><col class="w-14"><col><col class="w-40"><col class="w-24"><col class="w-20"></colgroup><thead class="bg-slate-50 text-[10px] uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3 text-center">No</th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">NISN</th><th class="px-3 py-3">JK</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead><tbody class="divide-y divide-slate-100">@forelse($siswaList as $siswa)<tr class="hover:bg-slate-50/70"><td class="px-4 py-3 text-center text-xs text-slate-400">{{ $loop->iteration }}</td><td class="px-3 py-3"><a href="{{ route('admin.manajemen-siswa.show',$siswa) }}" class="block truncate font-bold text-slate-950 no-underline hover:text-brand-700" title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</a></td><td class="whitespace-nowrap px-3 py-3 text-xs text-slate-600">{{ $siswa->nisn ?: '-' }}</td><td class="whitespace-nowrap px-3 py-3 text-xs text-slate-600">{{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td><td class="px-4 py-3"><form action="{{ route('admin.manajemen-siswa.remove-from-kelas',$kelas) }}" method="POST" class="flex justify-end" data-confirm data-confirm-title="Keluarkan siswa?" data-confirm-message="{{ $siswa->nama_lengkap }} akan dikeluarkan dari Kelas {{ $kelas->nama_kelas }}, tetapi datanya tetap tersimpan." data-confirm-text="Ya, keluarkan">@csrf<input type="hidden" name="siswa_id" value="{{ $siswa->id }}"><x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-user-minus" label="Keluarkan siswa" /></form></td></tr>@empty<tr><td colspan="5" class="p-10 text-center text-sm text-slate-500">Belum ada siswa di kelas ini.</td></tr>@endforelse</tbody></table></div>
        </section>
    </div>
</div>
@endsection
