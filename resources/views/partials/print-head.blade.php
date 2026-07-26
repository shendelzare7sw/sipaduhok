{{--
    Basis <head> bersama untuk halaman cetak (window.print()).
    Sisipkan di AKHIR <head> tiap halaman cetak portrait:  @include('partials.print-head')
    Memberi: <meta viewport> (banyak halaman cetak belum punya) + basis A4/responsif.
    Di-load terakhir agar aturan basis menang atas CSS halaman.
--}}
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="{{ asset('css/shared/print-base.css') }}">
