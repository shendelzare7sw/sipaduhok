@extends('layouts.app')

@php
    $kontenLabel = match($kontenType) {
        'materi' => 'Materi',
        'tugas' => 'Tugas',
        'latihan' => 'Latihan',
        default => 'Ujian',
    };
    $firstTarget = $kelasMapelTujuan->isNotEmpty()
        ? $kelasMapelTujuan->first()['kelas_id'].'|'.$kelasMapelTujuan->first()['mata_pelajaran_id']
        : '';
@endphp

@section('title', 'Salin ' . $kontenLabel . ' ke Kelas Aktif')
@section('page-title', 'Salin Konten Arsip')
@section('page-subtitle', 'Pilih kelas + mata pelajaran tujuan di TA aktif')

@section('content')
<div class="min-w-0 w-full space-y-5">
    <nav aria-label="Breadcrumb" class="flex flex-wrap items-center gap-2 text-xs font-semibold text-slate-500">
        <a href="{{ route('guru.lms.arsip.index') }}" class="text-brand-700 no-underline hover:text-brand-800">Arsip LMS</a>
        <i class="fa-solid fa-chevron-right text-[9px]" aria-hidden="true"></i>
        <span class="text-slate-700">Salin {{ $kontenLabel }}</span>
    </nav>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <ul class="list-disc space-y-1 pl-5">@foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach</ul>
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 px-4 py-4 sm:px-5">
            <h1 class="flex items-center gap-2 text-lg font-extrabold text-slate-900"><i class="fa-solid fa-copy text-brand-600" aria-hidden="true"></i>Salin {{ $kontenLabel }} ke kelas aktif</h1>
        </header>

        <div class="space-y-5 p-4 sm:p-5">
            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                <p class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Konten sumber</p>
                <p class="mt-1 break-words text-base font-extrabold text-slate-900">{{ $previewTitle }}</p>
                <p class="mt-1 text-xs text-slate-500">
                    {{ $konten->kelas?->nama_kelas ?? '-' }} · {{ $konten->mataPelajaran?->nama_mapel ?? '-' }} · TA {{ $konten->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}
                </p>
            </div>

            @if($kelasMapelTujuan->isEmpty())
                <p class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                    <i class="fa-solid fa-triangle-exclamation mt-0.5" aria-hidden="true"></i>
                    <span>Anda belum ditugaskan ke kelas dan mapel di TA aktif. Hubungi admin agar Anda ditugaskan sebagai guru pengajar terlebih dahulu.</span>
                </p>
                <div class="flex justify-end">
                    <a href="{{ route('guru.lms.arsip.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50">Kembali</a>
                </div>
            @else
                <form method="POST" action="{{ route('guru.lms.arsip.salin') }}" class="space-y-5" x-data="{ target: @js($firstTarget) }">
                    @csrf
                    <input type="hidden" name="type" value="{{ $kontenType }}">
                    <input type="hidden" name="sumber_id" value="{{ $kontenId }}">
                    <input type="hidden" name="kelas_id" :value="target.split('|')[0]">
                    <input type="hidden" name="mata_pelajaran_id" :value="target.split('|')[1]">

                    <fieldset>
                        <legend class="text-sm font-extrabold text-slate-900">Pilih kelas + mata pelajaran tujuan</legend>
                        <div class="mt-3 grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                            @foreach($kelasMapelTujuan as $tujuan)
                                @php $nilaiTujuan = $tujuan['kelas_id'].'|'.$tujuan['mata_pelajaran_id']; @endphp
                                <label class="flex min-w-0 cursor-pointer items-center gap-3 rounded-xl border-2 p-3 transition" :class="target === '{{ $nilaiTujuan }}' ? 'border-brand-500 bg-brand-50' : 'border-slate-200 bg-white hover:border-brand-300'">
                                    <input type="radio" name="target" value="{{ $nilaiTujuan }}" x-model="target" required class="h-4 w-4 shrink-0 border-slate-300 text-brand-600 focus:ring-brand-500">
                                    <span class="min-w-0">
                                        <span class="block truncate text-sm font-extrabold text-slate-900"><i class="fa-solid fa-school mr-1.5 text-brand-600" aria-hidden="true"></i>{{ $tujuan['kelas']?->nama_kelas ?? '-' }}<span class="ml-1 text-xs font-semibold text-slate-500">({{ $tujuan['kelas']?->jenjang ?? '' }})</span></span>
                                        <span class="mt-0.5 block truncate text-xs text-slate-500"><i class="fa-solid fa-book mr-1.5" aria-hidden="true"></i>{{ $tujuan['mata_pelajaran']?->nama_mapel ?? '-' }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-2 text-xs text-slate-500">Hanya kelas dan mapel yang Anda ampu di TA aktif yang bisa dipilih.</p>
                    </fieldset>

                    @if(in_array($kontenType, ['ujian', 'latihan']))
                        <fieldset>
                            <legend class="text-sm font-extrabold text-slate-900">Opsi salin</legend>
                            <label class="mt-3 flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3 text-xs leading-5 text-slate-600">
                                <input type="checkbox" name="sertakan_soal" value="1" checked class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span><strong class="text-slate-900">Sertakan semua soal</strong> — duplikat seluruh soal beserta kunci jawaban (jawaban siswa lama tidak ikut tersalin).</span>
                            </label>
                        </fieldset>
                    @endif

                    <p class="flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 p-4 text-xs leading-5 text-slate-600">
                        <i class="fa-solid fa-circle-info mt-0.5 text-sky-600" aria-hidden="true"></i>
                        <span>
                            <strong class="text-slate-800">Catatan:</strong> setelah disalin, konten muncul di kelas tujuan dengan tanggal mulai hari ini.
                            @if($kontenType === 'tugas')
                                Anda bisa mengubah deadline dan detail lain setelah disalin.
                            @elseif(in_array($kontenType, ['ujian', 'latihan']))
                                Status awal nonaktif; aktifkan secara manual setelah memeriksa jadwal.
                            @endif
                            File materi/lampiran tidak diduplikasi di penyimpanan (memakai path yang sama untuk menghemat ruang).
                        </span>
                    </p>

                    <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-4 sm:flex-row sm:justify-end">
                        <a href="{{ route('guru.lms.arsip.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Batal</a>
                        <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-xs font-bold text-white hover:bg-brand-700"><i class="fa-solid fa-copy" aria-hidden="true"></i>Salin sekarang</button>
                    </div>
                </form>
            @endif
        </div>
    </section>
</div>
@endsection
