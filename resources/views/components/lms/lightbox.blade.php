{{--
    Overlay galeri gambar (pakai sekali per halaman). Dibuka oleh gambar ber-atribut
    data-lightbox="<grup>" via $store.lightbox.buka($el) — lihat resources/js/components/media-lightbox.js.
--}}
<div x-data class="contents">
<template x-teleport="body">
    <div x-data="lightboxSwipe" x-show="$store.lightbox.open" x-cloak x-transition.opacity
         x-on:keydown.escape.window="$store.lightbox.open && $store.lightbox.tutup()"
         x-on:keydown.arrow-left.window="$store.lightbox.open && $store.lightbox.pindah(-1)"
         x-on:keydown.arrow-right.window="$store.lightbox.open && $store.lightbox.pindah(1)"
         x-on:touchstart.passive="mulai($event)" x-on:touchend="selesai($event)"
         class="fixed inset-0 z-[1100] flex flex-col bg-slate-950/[0.97] text-white backdrop-blur-sm" role="dialog" aria-modal="true" aria-label="Pratinjau gambar">
        {{-- Bilah atas --}}
        <div class="flex shrink-0 items-center justify-between gap-3 px-3 py-3 sm:px-5">
            <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-bold" x-text="`${$store.lightbox.index + 1} / ${$store.lightbox.items.length}`"></span>
            <div class="flex items-center gap-2">
                <button type="button" x-on:click="$store.lightbox.zoom = ! $store.lightbox.zoom" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20" x-bind:aria-label="$store.lightbox.zoom ? 'Perkecil' : 'Perbesar'">
                    <i class="fa-solid" x-bind:class="$store.lightbox.zoom ? 'fa-magnifying-glass-minus' : 'fa-magnifying-glass-plus'" aria-hidden="true"></i>
                </button>
                <a x-bind:href="$store.lightbox.current?.src" download class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 text-white no-underline transition hover:bg-white/20" aria-label="Unduh gambar"><i class="fa-solid fa-download" aria-hidden="true"></i></a>
                <button type="button" x-on:click="$store.lightbox.tutup()" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-white/20" aria-label="Tutup"><i class="fa-solid fa-xmark text-lg" aria-hidden="true"></i></button>
            </div>
        </div>

        {{-- Gambar utama: klik untuk zoom; saat diperbesar area bisa digeser (scroll). --}}
        <div class="relative min-h-0 flex-1" x-on:click.self="$store.lightbox.tutup()">
            <div class="absolute inset-0 flex items-center justify-center" x-bind:class="$store.lightbox.zoom ? 'overflow-auto items-start justify-start' : 'overflow-hidden px-3 sm:px-16'" x-on:click.self="$store.lightbox.tutup()">
                <img x-bind:src="$store.lightbox.current?.src" x-bind:alt="$store.lightbox.current?.alt"
                     x-on:click="$store.lightbox.zoom = ! $store.lightbox.zoom"
                     x-bind:class="$store.lightbox.zoom ? 'max-w-none w-[200%] cursor-zoom-out sm:w-auto sm:max-w-none' : 'max-h-full max-w-full cursor-zoom-in object-contain'"
                     class="select-none rounded-lg transition-transform">
            </div>

            <template x-if="$store.lightbox.items.length > 1">
                <div>
                    <button type="button" x-on:click="$store.lightbox.pindah(-1)" class="absolute left-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/15 backdrop-blur transition hover:bg-white/25 sm:left-4" aria-label="Gambar sebelumnya"><i class="fa-solid fa-chevron-left" aria-hidden="true"></i></button>
                    <button type="button" x-on:click="$store.lightbox.pindah(1)" class="absolute right-2 top-1/2 flex h-11 w-11 -translate-y-1/2 items-center justify-center rounded-full bg-white/15 backdrop-blur transition hover:bg-white/25 sm:right-4" aria-label="Gambar berikutnya"><i class="fa-solid fa-chevron-right" aria-hidden="true"></i></button>
                </div>
            </template>
        </div>

        {{-- Strip thumbnail --}}
        <div x-show="$store.lightbox.items.length > 1" class="flex shrink-0 justify-center gap-2 overflow-x-auto px-3 py-3 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            <template x-for="(item, i) in $store.lightbox.items" :key="i">
                <button type="button" x-on:click="$store.lightbox.ke(i)" class="h-14 w-14 shrink-0 overflow-hidden rounded-lg ring-2 transition sm:h-16 sm:w-16"
                        x-bind:class="i === $store.lightbox.index ? 'ring-white opacity-100' : 'ring-transparent opacity-50 hover:opacity-80'" x-bind:aria-label="`Gambar ${i + 1}`">
                    <img x-bind:src="item.src" alt="" class="h-full w-full object-cover">
                </button>
            </template>
        </div>
    </div>
</template>
</div>
