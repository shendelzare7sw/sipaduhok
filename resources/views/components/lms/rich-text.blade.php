{{--
    Isi postingan LMS: teks di-escape, baris baru dipertahankan, tautan http(s) bisa diklik,
    dan tautan YouTube ditampilkan sebagai pratinjau yang bisa diputar di halaman ini.
--}}
@props(['text' => ''])

@php
    $teks = (string) $text;
    $polaUrl = '~https?://[^\s<]+[^\s<.,;:!?)\]\'"]~i';

    // Escape dulu, lalu ubah URL (yang sudah ter-escape) menjadi tautan.
    $html = preg_replace_callback($polaUrl, function ($m) {
        return '<a href="' . $m[0] . '" target="_blank" rel="noopener noreferrer" class="break-all font-semibold text-indigo-700 underline decoration-indigo-200 underline-offset-2 hover:decoration-indigo-500">' . $m[0] . '</a>';
    }, e($teks));

    // ID video YouTube (watch, youtu.be, shorts, embed, live), tanpa duplikat.
    preg_match_all('~(?:youtube\.com/(?:watch\?(?:[^\s&]*&(?:amp;)?)*v=|shorts/|embed/|live/)|youtu\.be/)([A-Za-z0-9_-]{11})~i', $teks, $cocok);
    $videoIds = array_values(array_unique($cocok[1] ?? []));
@endphp

<div {{ $attributes }}>{!! nl2br($html) !!}</div>

@foreach($videoIds as $videoId)
    <div x-data="youtubePreview" class="mt-3 w-full max-w-xl overflow-hidden rounded-xl border border-slate-200 bg-black shadow-sm">
        <div class="relative aspect-video">
            <template x-if="putar">
                <iframe src="https://www.youtube-nocookie.com/embed/{{ $videoId }}?autoplay=1&rel=0" title="Video YouTube" class="absolute inset-0 h-full w-full"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </template>
            <button type="button" x-show="! putar" x-on:click="putar = true" class="group absolute inset-0 h-full w-full" aria-label="Putar video YouTube">
                <img src="https://i.ytimg.com/vi/{{ $videoId }}/hqdefault.jpg" alt="Pratinjau video YouTube" loading="lazy" class="h-full w-full object-cover opacity-90 transition group-hover:opacity-100">
                <span class="absolute inset-0 flex items-center justify-center">
                    <span class="flex h-14 w-20 items-center justify-center rounded-2xl bg-red-600 text-2xl text-white shadow-lg transition group-hover:scale-105"><i class="fa-solid fa-play" aria-hidden="true"></i></span>
                </span>
            </button>
        </div>
        <a href="https://www.youtube.com/watch?v={{ $videoId }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2 bg-white px-3 py-2 text-xs font-bold text-slate-600 no-underline hover:text-red-600">
            <i class="fa-brands fa-youtube text-red-600" aria-hidden="true"></i>Buka di YouTube
        </a>
    </div>
@endforeach
