@extends('layouts.print')

@section('title', 'Kartu Siswa - '.$siswa->nama_lengkap)

@php
    $cabang = $siswa->cabang;
    $namaSekolah = $cabang ? strtoupper(preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i','',$cabang->nama_cabang)) : 'PKBM HOUSE OF KNOWLEDGE';
    $alamatSekolah = $cabang->alamat ?? 'Jl. Ruko Reni Jaya, Pamulang';
    $primaryParent = $siswa->studentParents->firstWhere('is_primary',true) ?? $siswa->studentParents->first();
    $parentUser = $primaryParent?->parent;
    $namaWali = $parentUser?->name ?: implode(' / ',array_filter([$siswa->nama_ayah,$siswa->nama_ibu]));
    $kontakWali = $parentUser?->phone ?: $siswa->telepon_orangtua;
    $initialPhoto = $siswa->foto ? asset('storage/'.$siswa->foto) : '';
@endphp

@section('page-content')
<div x-data="{ photo: @js($initialPhoto) }" class="min-h-screen">
    <header class="sticky top-0 z-20 flex items-center justify-between gap-3 border-b border-slate-200 bg-white/95 px-3 py-3 shadow-sm backdrop-blur print:hidden sm:px-5"><a href="{{ route('admin.manajemen-siswa.show',$siswa) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 px-4 text-sm font-bold text-slate-700 no-underline"><i class="fas fa-arrow-left"></i>Kembali</a><div class="min-w-0 text-center"><p class="truncate text-xs font-bold uppercase tracking-wider text-brand-600">Pratinjau kartu siswa</p><p class="truncate text-sm font-extrabold">{{ $siswa->nama_lengkap }}</p></div><button type="button" data-print-page class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white"><i class="fas fa-print"></i><span class="hidden sm:inline">Cetak</span></button></header>

    <main class="min-w-0 w-full p-3 print:p-0 sm:p-6">
        <div class="mx-auto flex max-w-6xl flex-wrap justify-center gap-5 print:max-w-none print:justify-start print:gap-[5mm]">
            <article class="relative h-[53.98mm] w-[85.6mm] shrink-0 overflow-hidden rounded-[4mm] bg-gradient-to-br from-brand-950 via-brand-900 to-brand-700 p-[4mm] text-white shadow-xl print:break-inside-avoid print:shadow-none">
                <div class="absolute -right-10 -top-12 h-40 w-40 rounded-full bg-white/10"></div>
                <header class="relative flex items-center gap-2 border-b border-white/20 pb-2"><img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="h-9 w-9 rounded-full bg-white object-contain p-1"><div class="min-w-0"><h1 class="truncate text-[11px] font-extrabold tracking-wide !text-white">{{ $namaSekolah }}</h1><p class="text-[7px] uppercase tracking-[.18em] text-blue-200">Kartu tanda siswa</p></div></header>
                <div class="relative mt-3 flex gap-3"><div class="flex h-[27mm] w-[20mm] shrink-0 items-center justify-center overflow-hidden rounded-[2mm] border-2 border-white/50 bg-white/10"><template x-if="photo"><img :src="photo" alt="Foto siswa" class="h-full w-full object-cover"></template><span x-show="!photo" class="text-center text-[8px] leading-3 text-blue-100">PAS FOTO<br>3 x 4</span></div><div class="min-w-0 flex-1"><h2 class="truncate text-[12px] font-extrabold uppercase !text-white">{{ $siswa->nama_lengkap }}</h2><dl class="mt-2 grid grid-cols-[16mm_2mm_1fr] gap-y-1 text-[7.5px]"><dt>NISN</dt><dd>:</dd><dd class="truncate font-bold">{{ $siswa->nisn ?: '-' }}</dd>@if($siswa->nis)<dt>NIS</dt><dd>:</dd><dd class="truncate">{{ $siswa->nis }}</dd>@endif<dt>Kelas</dt><dd>:</dd><dd class="truncate">{{ $siswa->kelas->nama_kelas ?? '-' }}</dd><dt>Tempat lahir</dt><dd>:</dd><dd class="truncate">{{ $siswa->tempat_lahir ?: '-' }}</dd><dt>Tanggal lahir</dt><dd>:</dd><dd class="truncate">{{ $siswa->tanggal_lahir?->format('d/m/Y') ?? '-' }}</dd></dl></div></div>
                <footer class="absolute inset-x-[4mm] bottom-[2.5mm] flex items-center justify-between border-t border-white/20 pt-1 text-[6.5px] text-blue-100"><span class="max-w-[65mm] truncate">{{ $cabang->nama_cabang ?? 'PKBM HOK' }}</span><span>SIPADUHOK</span></footer>
            </article>

            <article class="relative h-[53.98mm] w-[85.6mm] shrink-0 overflow-hidden rounded-[4mm] border border-slate-200 bg-white p-[4mm] shadow-xl print:break-inside-avoid print:shadow-none">
                <header class="flex items-center gap-2 border-b border-brand-100 pb-2"><img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="h-9 w-9 rounded-full bg-brand-50 object-contain p-1"><div class="min-w-0"><h2 class="truncate text-[10px] font-extrabold text-brand-950">{{ $namaSekolah }}</h2><p class="text-[6.5px] uppercase tracking-wider text-brand-600">Pusat kegiatan belajar masyarakat</p></div></header>
                <div class="mt-3 grid gap-2 text-[7.5px]"><div><span class="block text-[6px] font-bold uppercase tracking-wide text-slate-400">Alamat siswa</span><p class="line-clamp-2 font-semibold text-slate-800">{{ $siswa->alamat ?: '-' }}</p></div><div class="grid grid-cols-2 gap-3"><div><span class="block text-[6px] font-bold uppercase tracking-wide text-slate-400">Wali siswa</span><p class="truncate font-semibold text-slate-800">{{ $namaWali ?: '-' }}</p></div><div><span class="block text-[6px] font-bold uppercase tracking-wide text-slate-400">Kontak darurat</span><p class="truncate font-semibold text-slate-800">{{ $kontakWali ?: '-' }}</p></div></div><div class="mt-1 flex h-7 items-center justify-center bg-[repeating-linear-gradient(90deg,#0f172a_0,#0f172a_1px,transparent_1px,transparent_3px)]"><span class="bg-white px-2 text-[6px] font-bold tracking-[.25em]">{{ $siswa->nisn ?: $siswa->id }}</span></div></div>
                <footer class="absolute inset-x-[4mm] bottom-[2.5mm] truncate border-t border-slate-200 pt-1 text-center text-[6px] text-slate-500" title="{{ $alamatSekolah }}">{{ $alamatSekolah }}</footer>
            </article>
        </div>

        <section class="mx-auto mt-5 max-w-xl rounded-2xl border border-slate-200 bg-white p-4 shadow-sm print:hidden"><h3 class="text-sm font-extrabold text-slate-950">Foto khusus untuk cetak</h3><p class="mt-1 text-xs text-slate-500">Foto yang dipilih hanya dipakai pada pratinjau ini dan tidak mengubah profil siswa.</p><label class="mt-3 flex min-h-11 cursor-pointer items-center justify-center gap-2 rounded-xl border border-dashed border-brand-300 bg-brand-50 px-4 text-sm font-bold text-brand-700"><i class="fas fa-camera"></i>Pilih foto dari perangkat<input type="file" accept="image/*" class="sr-only" @change="if ($event.target.files[0]) photo = URL.createObjectURL($event.target.files[0])"></label><p class="mt-3 text-center text-[11px] text-slate-400">Ukuran kartu 85,6 mm x 53,98 mm (standar kartu identitas).</p></section>
    </main>
</div>
@endsection
