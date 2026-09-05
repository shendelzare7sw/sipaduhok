@php
    $headerCabang = $cabang ?? null;
    $namaSekolahRaw = $headerCabang ? $headerCabang->nama_cabang : 'PKBM HOUSE OF KNOWLEDGE';
    $namaSekolah = preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $namaSekolahRaw);
    $alamat = $headerCabang ? $headerCabang->alamat : 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan';
    $telepon = $headerCabang ? ($headerCabang->telepon ?? '021-7412345') : '021-7412345';
@endphp

<header class="grid grid-cols-[5rem_minmax(0,1fr)_5rem] items-center border-b-4 border-double border-slate-950 pb-3 text-center print:grid-cols-[18mm_minmax(0,1fr)_18mm]">
    <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" class="h-16 w-16 object-contain print:h-[16mm] print:w-[16mm]">
    <div class="min-w-0"><h2 class="text-base font-extrabold uppercase print:text-[12pt]">{{ $namaSekolah }}</h2><p class="mt-0.5 text-xs font-semibold uppercase print:text-[9pt]">Pusat Kegiatan Belajar Masyarakat</p><p class="mt-1 text-[10px] leading-4 text-slate-600 print:text-[7.5pt]">{{ $alamat }}</p><p class="text-[10px] leading-4 text-slate-600 print:text-[7.5pt]">Telp: {{ $telepon }} · Email: info@hok.sch.id</p></div>
    <span aria-hidden="true"></span>
</header>
