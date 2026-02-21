@php
    $headerCabang = $cabang ?? null;
    $namaSekolahRaw = $headerCabang ? $headerCabang->nama_cabang : 'PKBM HOUSE OF KNOWLEDGE';
    // Strip location suffix (e.g. "Gedung Utama") — keep only the core school name
    $namaSekolah = preg_replace('/\s*\(?\s*Gedung\s+\w+\s*\)?$/i', '', $namaSekolahRaw);
    $subJudul = 'PUSAT KEGIATAN BELAJAR MASYARAKAT';
    $alamat = $headerCabang ? $headerCabang->alamat : 'Jl. Ruko Reni Jaya Blok AF No. 22-23 Pamulang Barat, Tangerang Selatan';
    $telepon = $headerCabang ? ($headerCabang->telepon ?? '021-7412345') : '021-7412345';
    $email = 'info@hok.sch.id';
@endphp
<div class="header" style="position: relative; border-bottom: 3px double #000; padding-bottom: 12px; margin-bottom: 15px;">
    <img src="{{ asset('img/logo/hok-watermark.png') }}" alt="Logo HOK" style="position: absolute; left: 0; top: 50%; transform: translateY(-50%); height: 80px; width: auto;">
    <div style="text-align: center;">
        <h1 style="font-size: 14pt; font-weight: bold; margin-bottom: 3px;">{{ $namaSekolah }}</h1>
        <h2 style="font-size: 12pt; font-weight: normal; margin-bottom: 5px;">{{ $subJudul }}</h2>
        <p style="font-size: 9pt; color: #333; margin: 0;">{{ $alamat }}</p>
        <p style="font-size: 9pt; color: #333; margin: 0;">Telp: {{ $telepon }} | Email: {{ $email }}</p>
    </div>
</div>
