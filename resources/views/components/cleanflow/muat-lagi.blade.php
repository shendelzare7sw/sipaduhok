{{--
    Footer daftar pengganti pagination bernomor: "Menampilkan X dari Y" + tombol "Tampilkan semua".
    Pasangan App\Support\Tampil::ambil(). Setelah dimuat, layar langsung menuju data pertama yang baru:
    item daftar memakai id {jangkar}m-{no} (kartu ponsel) dan {jangkar}d-{no} (baris tabel desktop);
    daftar tunggal untuk semua layar cukup memakai id {jangkar}m-{no}. Tanpa JavaScript tautan tetap berfungsi.
    Data untuk Alpine dikirim lewat atribut data-* (bukan @js di atribut) agar ter-escape.

    tema: 'sia' (biru brand) atau 'lms' (indigo, shell HOK Learning/Teaching).
    lepas: true bila footer berdiri sendiri di luar kartu daftar (mis. grid kartu LMS); disembunyikan bila semua muat dalam satu langkah.
--}}
@props([
    'items',
    'param' => 'tampil',
    'langkah' => null,
    'jangkar' => '',
    'tema' => 'sia',
    'lepas' => false,
])
@php
    $langkah = $langkah ?? \App\Support\Tampil::langkahUntuk($param);
    $paginator = $items instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    $ditampilkan = $items->count();
    $total = $paginator ? $items->total() : $ditampilkan;
    $sisa = $total - $ditampilkan;
    $maks = \App\Support\Tampil::MAKS;
    $url = request()->fullUrlWithQuery([$param => \App\Support\Tampil::SEMUA]);
    $tombol = $tema === 'lms'
        ? 'bg-indigo-50 text-indigo-700 ring-indigo-100 hover:bg-indigo-100'
        : 'bg-brand-50 text-brand-700 ring-brand-100 hover:bg-brand-100';
@endphp
@if ($paginator && $total > 0 && ! ($lepas && $total <= $langkah))
<footer {{ $attributes->class([
    'flex flex-col items-stretch gap-3 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5',
    'border-t border-slate-200' => ! $lepas,
    'rounded-2xl border border-slate-200 bg-white shadow-sm' => $lepas,
]) }} data-muat-lagi>
    <p class="text-center text-xs text-slate-500 sm:text-left">Menampilkan <strong class="font-extrabold text-slate-800">{{ $ditampilkan }}</strong> dari <strong class="font-extrabold text-slate-800">{{ $total }}</strong></p>
    @if ($sisa > 0 && $ditampilkan < $maks)
        <a href="{{ $url }}" data-tujuan="{{ $url }}" data-jangkar="{{ $jangkar }}" data-terakhir="{{ $ditampilkan }}"
            x-data="{ busy: false }" :class="busy && 'pointer-events-none opacity-60'"
            @click.prevent="busy = true; location.href = $el.dataset.tujuan + '#' + $el.dataset.jangkar + (document.getElementById($el.dataset.jangkar + 'd-' + $el.dataset.terakhir)?.getClientRects().length ? 'd' : 'm') + '-' + (Number($el.dataset.terakhir) + 1)"
            class="inline-flex min-h-11 items-center justify-center gap-2 whitespace-nowrap rounded-xl px-4 text-sm font-bold no-underline ring-1 ring-inset transition {{ $tombol }}">
            <i class="fa-solid fa-spinner fa-spin text-xs" x-show="busy" x-cloak aria-hidden="true"></i>
            <i class="fa-solid fa-angles-down text-xs" x-show="!busy" aria-hidden="true"></i>
            {{ $total <= $maks ? 'Tampilkan semua' : 'Tampilkan '.$maks.' data' }}
        </a>
    @elseif ($sisa > 0)
        <p class="text-center text-xs font-semibold text-amber-700 sm:text-right">Gunakan pencarian atau filter untuk mempersempit daftar.</p>
    @elseif ($total > $langkah)
        <p class="text-center text-xs font-semibold text-emerald-700 sm:text-right"><i class="fa-solid fa-circle-check mr-1" aria-hidden="true"></i>Semua data sudah ditampilkan.</p>
    @endif
</footer>
@endif
