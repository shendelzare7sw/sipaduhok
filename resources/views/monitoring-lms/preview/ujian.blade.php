@php
    $ujian = $konten;
    $previewTitle = $ujian->judul_ujian ?? '-';
    $isLatihan = ($ujian->tipe_ujian ?? null) === 'latihan';
    $kontenLabel = $isLatihan ? 'Latihan' : 'Ujian';
    $tipeLabel = method_exists($ujian, 'getTipeLabelAttribute') ? $ujian->tipe_label : $ujian->tipe_ujian;
    $soalList = ($ujian->soalUjian ?? collect())->sortBy('urutan')->values();
    $totalBobot = $soalList->sum('bobot_nilai');
@endphp

@extends('monitoring-lms.preview.wrapper', compact('previewTitle', 'kontenLabel'))

@section('preview-content')
    <header class="border-b border-slate-200 pb-5">
        <h1 class="flex items-start gap-3 text-xl font-extrabold text-slate-950 sm:text-2xl"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isLatihan ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700' }}"><i class="fas {{ $isLatihan ? 'fa-pencil-ruler' : 'fa-file-alt' }}" aria-hidden="true"></i></span><span class="pt-1">{{ $ujian->judul_ujian }}</span></h1>
        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-600"><span class="rounded-lg px-3 py-2 font-bold {{ $isLatihan ? 'bg-violet-50 text-violet-700' : 'bg-rose-50 text-rose-700' }}">{{ $tipeLabel }}</span>
            @foreach([[optional($ujian->guru)->nama_lengkap, 'fa-user-tie'], [optional($ujian->mataPelajaran)->nama_mapel, 'fa-book'], [optional($ujian->kelas)->nama_kelas, 'fa-school'], [$ujian->tanggal_mulai?->locale('id')->translatedFormat('d M Y, H:i'), 'fa-play-circle'], [$ujian->durasi_menit ? $ujian->durasi_menit.' menit' : null, 'fa-clock']] as [$value, $icon])
                @if($value)<span class="inline-flex items-center gap-2 rounded-lg bg-slate-100 px-3 py-2"><i class="fas {{ $icon }} text-slate-400" aria-hidden="true"></i>{{ $value }}</span>@endif
            @endforeach
        </div>
    </header>

    <div class="mt-5 grid gap-4">
        @if($ujian->deskripsi)<section class="rounded-2xl border border-slate-200 p-4"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Deskripsi</h2><p class="mt-2 text-sm leading-7 text-slate-700">{!! nl2br(e($ujian->deskripsi)) !!}</p></section>@endif
        <section class="rounded-2xl border border-slate-200 p-4 sm:p-5"><h2 class="text-[11px] font-bold uppercase tracking-wide text-slate-500">Pengaturan {{ strtolower($kontenLabel) }}</h2><div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach([['fa-power-off', 'Status', ($ujian->is_active ?? false) ? 'Aktif' : 'Nonaktif'], ['fa-random', 'Acak soal', ($ujian->acak_soal ?? false) ? 'Ya' : 'Tidak'], ['fa-eye', 'Tampilkan nilai', ($ujian->tampilkan_nilai ?? false) ? 'Ya' : 'Tidak'], ['fa-redo', 'Bisa diulang', ($ujian->bisa_diulang ?? false) ? 'Ya' : 'Tidak']] as [$icon, $label, $value])
                <div class="flex items-center gap-3 rounded-xl bg-slate-50 p-3"><i class="fas {{ $icon }} text-brand-600" aria-hidden="true"></i><p class="text-xs text-slate-500">{{ $label }}<strong class="mt-0.5 block text-sm text-slate-900">{{ $value }}</strong></p></div>
            @endforeach
        </div></section>

        <section class="rounded-2xl border border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-2 border-b border-slate-200 pb-4 sm:flex-row sm:items-center sm:justify-between"><div><h2 class="text-base font-extrabold text-slate-950">Daftar soal</h2><p class="mt-1 text-xs text-slate-500">{{ $soalList->count() }} soal · Total bobot {{ $totalBobot }}</p></div>@if($soalList->isNotEmpty())<p class="flex items-center gap-2 text-xs text-emerald-700"><i class="fas fa-circle-check" aria-hidden="true"></i>Kunci ditandai hijau</p>@endif</div>
            @if($soalList->isEmpty())
                <div class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"><i class="fas fa-exclamation-triangle mt-0.5" aria-hidden="true"></i>Belum ada soal yang dibuat untuk {{ strtolower($kontenLabel) }} ini.</div>
            @else
                <div class="mt-4 grid gap-4">
                    @foreach($soalList as $soal)
                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                            <header class="flex flex-wrap items-center justify-between gap-3 bg-slate-50 px-4 py-3"><h3 class="text-sm font-extrabold text-slate-950">Soal #{{ $loop->iteration }}</h3><div class="flex gap-2"><span class="rounded-lg bg-white px-2.5 py-1 text-[10px] font-bold text-slate-600 ring-1 ring-slate-200">{{ \App\Models\SoalUjian::getTipeSoalLabel($soal->tipe_soal) }}</span><span class="rounded-lg bg-brand-50 px-2.5 py-1 text-[10px] font-bold text-brand-700">{{ $soal->bobot_nilai ?? 0 }} poin</span></div></header>
                            <div class="space-y-4 p-4">
                                @if($soal->narasi)<div class="rounded-xl border-l-4 border-brand-300 bg-brand-50 p-3 text-sm leading-6 text-slate-700">{!! nl2br(e($soal->narasi)) !!}</div>@endif
                                @if($soal->image_path)<img src="{{ asset('storage/'.$soal->image_path) }}" alt="Gambar soal #{{ $loop->iteration }}" data-expandable-image class="max-h-80 max-w-full cursor-zoom-in rounded-xl border border-slate-200 object-contain">@endif
                                <p class="text-sm font-semibold leading-7 text-slate-900">{!! nl2br(e($soal->pertanyaan)) !!}</p>
                                @if(in_array($soal->tipe_soal, ['pilihan_ganda', 'pilihan_ganda_kompleks']))
                                    @php
                                        $pilihan = $soal->pilihan_jawaban ?? [];
                                        $kunci = $soal->kunci_jawaban;
                                        if (!is_array($kunci)) { $decoded = json_decode($kunci ?? '', true); $kunci = is_array($decoded) ? $decoded : [strtoupper(trim((string) $kunci))]; }
                                        $kunciNorm = array_map(fn ($item) => strtoupper(trim((string) $item)), $kunci);
                                    @endphp
                                    <div class="grid gap-2">
                                        @foreach(['A', 'B', 'C', 'D', 'E'] as $letter)
                                            @if(isset($pilihan[$letter]) && $pilihan[$letter] !== '')
                                                @php
                                                    $isBenar = in_array($letter, $kunciNorm);
                                                @endphp
                                                <div class="flex items-start gap-3 rounded-xl border p-3 {{ $isBenar ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200 bg-slate-50' }}"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg text-xs font-extrabold {{ $isBenar ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-200' }}">{{ $letter }}</span><span class="flex-1 pt-1 text-sm text-slate-800">{{ $pilihan[$letter] }}</span>@if($isBenar)<i class="fas fa-check-circle pt-1 text-emerald-600" title="Kunci jawaban"></i>@endif</div>
                                            @endif
                                        @endforeach
                                    </div>
                                @elseif($soal->tipe_soal === 'benar_salah')
                                    @php
                                        $pernyataanList = $soal->pilihan_jawaban['pernyataan'] ?? [];
                                    @endphp
                                    @if(!empty($pernyataanList))
                                        <div class="grid gap-2">
                                            @foreach($pernyataanList as $idx2 => $p)
                                                <div class="flex items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-white text-xs font-extrabold text-slate-600 ring-1 ring-slate-200">{{ $idx2 + 1 }}</span><span class="flex-1 pt-1 text-sm text-slate-800">{{ $p['text'] ?? '' }}</span><span class="rounded-lg px-2 py-1 text-[10px] font-bold {{ ($p['benar'] ?? false) ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ ($p['benar'] ?? false) ? 'Benar' : 'Salah' }}</span></div>
                                            @endforeach
                                        </div>
                                    @endif
                                @elseif($soal->tipe_soal === 'isian_singkat')
                                    @php
                                        $answers = $soal->pilihan_jawaban['jawaban_benar'] ?? null;
                                        if (!is_array($answers)) {
                                            $answers = $soal->kunci_jawaban ? [$soal->kunci_jawaban] : [];
                                        }
                                    @endphp
                                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-800"><strong>Kunci jawaban:</strong> {{ implode(' / ', array_filter($answers)) ?: '-' }}</div>
                                @else
                                    <div class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-sm leading-6 text-blue-800"><i class="fas fa-info-circle mr-1" aria-hidden="true"></i>Soal esai/uraian dikoreksi manual oleh guru.
                                        @if($soal->kunci_jawaban)<p class="mt-2"><strong>Pedoman jawaban:</strong> {{ Str::limit($soal->kunci_jawaban, 200) }}</p>@endif
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>
    </div>
@endsection
