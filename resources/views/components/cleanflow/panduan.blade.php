{{--
    Panduan singkat halaman (isi dari App\Support\Panduan), dirender otomatis oleh layout di atas konten.
    Awalnya tertutup (hanya tombol kecil) agar konten tetap bersih; bila dibuka, pilihan diingat per halaman di perangkat.
    tema: 'sia' (biru brand) atau 'lms' (indigo).
--}}
@props(['panduan', 'kunci', 'tema' => 'sia'])
@php
    $lms = $tema === 'lms';
    $warna = $lms
        ? ['panel' => 'border-indigo-100 bg-indigo-50/60', 'ikon' => 'bg-indigo-600', 'label' => 'text-indigo-700', 'tombol' => 'text-indigo-700 hover:bg-indigo-100', 'nomor' => 'bg-indigo-600', 'istilah' => 'ring-indigo-100', 'judulIstilah' => 'text-indigo-800', 'chip' => 'bg-indigo-50 text-indigo-700 ring-indigo-200 hover:bg-indigo-100']
        : ['panel' => 'border-brand-100 bg-brand-50/60', 'ikon' => 'bg-brand-600', 'label' => 'text-brand-700', 'tombol' => 'text-brand-700 hover:bg-brand-100', 'nomor' => 'bg-brand-600', 'istilah' => 'ring-brand-100', 'judulIstilah' => 'text-brand-800', 'chip' => 'bg-brand-50 text-brand-700 ring-brand-200 hover:bg-brand-100'];
    $simpan = 'panduan:'.$kunci;
@endphp
<div {{ $attributes->class('min-w-0 print:hidden') }} data-kunci="{{ $simpan }}"
    x-data="{ buka: false, kunci: '', istilah: window.innerWidth >= 640,
        init() { this.kunci = this.$el.dataset.kunci; try { this.buka = localStorage.getItem(this.kunci) === 'buka' } catch (e) {} },
        ingat(v) { try { v ? localStorage.setItem(this.kunci, 'buka') : localStorage.removeItem(this.kunci) } catch (e) {} } }">
    <button type="button" x-show="!buka" @click="buka = true; ingat(true)" aria-expanded="false"
        class="inline-flex min-h-9 items-center gap-1.5 rounded-xl px-3 text-xs font-bold ring-1 ring-inset transition {{ $warna['chip'] }}">
        <i class="fa-regular fa-lightbulb text-sm" aria-hidden="true"></i> Tampilkan panduan halaman ini
    </button>

    <section x-show="buka" x-cloak class="rounded-2xl border p-4 sm:p-5 {{ $warna['panel'] }}" aria-label="Panduan halaman">
        <div class="flex items-center gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl text-white sm:h-10 sm:w-10 {{ $warna['ikon'] }}"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i></span>
            <div class="min-w-0 flex-1">
                <p class="text-[10px] font-bold uppercase tracking-wide sm:text-[11px] {{ $warna['label'] }}">Panduan halaman</p>
                <h2 class="text-sm font-extrabold leading-snug text-slate-900 sm:text-base">{{ $panduan['judul'] }}</h2>
            </div>
            <button type="button" @click="buka = false; ingat(false)" aria-expanded="true" aria-label="Sembunyikan panduan"
                class="flex h-9 shrink-0 items-center gap-1 rounded-lg px-2 text-xs font-bold transition {{ $warna['tombol'] }}">
                <i class="fa-solid fa-xmark" aria-hidden="true"></i><span class="hidden sm:inline">Sembunyikan</span>
            </button>
        </div>
        <div class="mt-3 sm:pl-[3.25rem]">
            <p class="text-sm leading-6 text-slate-700">{{ $panduan['isi'] }}</p>
            @if (! empty($panduan['langkah']))
                <ol class="mt-3 space-y-1.5">
                    @foreach ($panduan['langkah'] as $langkah)
                        <li class="flex gap-2 text-sm leading-6 text-slate-700">
                            <span class="mt-0.5 flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[11px] font-bold text-white {{ $warna['nomor'] }}">{{ $loop->iteration }}</span>
                            <span class="min-w-0">{{ $langkah }}</span>
                        </li>
                    @endforeach
                </ol>
            @endif
            @if (! empty($panduan['istilah']))
                <button type="button" @click="istilah = !istilah" class="mt-3 inline-flex items-center gap-1 text-xs font-bold sm:hidden {{ $warna['label'] }}">
                    <i class="fa-solid fa-chevron-right text-[10px] transition" :class="istilah && 'rotate-90'" aria-hidden="true"></i> Arti istilah ({{ count($panduan['istilah']) }})
                </button>
                <dl x-show="istilah" class="mt-2 grid gap-2 sm:mt-3 sm:grid-cols-2 xl:grid-cols-3">
                    @foreach ($panduan['istilah'] as $nama => $arti)
                        <div class="rounded-xl bg-white/80 px-3 py-2 ring-1 {{ $warna['istilah'] }}">
                            <dt class="text-xs font-extrabold {{ $warna['judulIstilah'] }}">{{ $nama }}</dt>
                            <dd class="mt-0.5 text-xs leading-5 text-slate-600">{{ $arti }}</dd>
                        </div>
                    @endforeach
                </dl>
            @endif
        </div>
    </section>
</div>
