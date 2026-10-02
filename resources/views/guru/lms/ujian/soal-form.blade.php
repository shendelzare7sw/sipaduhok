@extends('layouts.lms-guru')

@php
    $isLatihan = request()->routeIs('guru.lms.latihan.*');
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $args = [$kelas->id, $mapel->id, $ujian->id];
    $selectedTipe = $soal->tipe_soal ?? old('tipe_soal', 'pilihan_ganda');
    $opsi = ['A', 'B', 'C', 'D', 'E'];
    $pilihanTersimpan = is_array($soal->pilihan_jawaban ?? null) ? $soal->pilihan_jawaban : (json_decode($soal->pilihan_jawaban ?? '{}', true) ?? []);
    $kunciPilgan = $soal->kunci_jawaban ?? '';
    $kunciKompleks = is_array($soal->kunci_jawaban ?? null) ? $soal->kunci_jawaban : (json_decode($soal->kunci_jawaban ?? '[]', true) ?? []);
    $rawBS = is_array($soal->pilihan_jawaban ?? null) ? $soal->pilihan_jawaban : json_decode($soal->pilihan_jawaban ?? '[]', true);
    $barisBS = collect($rawBS ?: [['pernyataan' => '', 'kunci' => 'B']])
        ->map(fn ($bs) => ['pernyataan' => is_array($bs) ? ($bs['pernyataan'] ?? ($bs['text'] ?? '')) : '', 'kunci' => is_array($bs) ? ($bs['kunci'] ?? 'B') : 'B'])
        ->values()->all();
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

@section('title', $soal ? 'Edit Soal' : 'Tambah Soal')
@section('page-title', $soal ? 'Edit Soal' : 'Tambah Soal Baru')
@section('page-subtitle', ($isLatihan ? 'Latihan: ' : 'Ujian: ') . $ujian->judul_ujian)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route($prefix.'.soal.index', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar soal</a>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <ul class="list-disc space-y-0.5 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $soal ? route($prefix.'.soal.update', [...$args, $soal->id]) : route($prefix.'.soal.store', $args) }}" method="POST"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          x-data="{ tipe: @js($selectedTipe), baris: @js($barisBS) }">
        @csrf
        @if($soal)
            @method('PUT')
        @endif

        <header class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-circle-question text-indigo-600" aria-hidden="true"></i>{{ $soal ? 'Edit soal' : 'Soal baru' }}</h2>
            <p class="mt-1 text-xs text-slate-500">Pilih tipe soal terlebih dahulu; opsi jawaban dan kunci menyesuaikan tipe.</p>
        </header>

        <div class="space-y-5 px-4 py-5 sm:px-6">
            <div class="grid gap-4 md:grid-cols-3">
                <label class="block text-xs font-bold text-slate-700">Tipe soal <span class="text-rose-600">*</span>
                    <select name="tipe_soal" x-model="tipe" required class="{{ $input }}">
                        <option value="pilihan_ganda">Pilihan ganda (satu jawaban)</option>
                        <option value="pilihan_ganda_kompleks">Pilihan ganda kompleks (banyak jawaban)</option>
                        <option value="benar_salah">Benar - salah</option>
                        <option value="isian_singkat">Isian singkat</option>
                        <option value="uraian">Uraian / esai</option>
                    </select>
                </label>
                <label class="block text-xs font-bold text-slate-700">Bobot nilai <span class="text-rose-600">*</span>
                    <input type="number" name="bobot" value="{{ old('bobot', $soal->bobot_nilai ?? 10) }}" min="1" required class="{{ $input }}">
                    <span class="mt-1 block font-normal text-slate-500">Nilai jika jawaban benar.</span>
                </label>
                <label class="block text-xs font-bold text-slate-700">No. urut <span class="text-rose-600">*</span>
                    <input type="number" name="urutan" value="{{ $soal->urutan ?? old('urutan', $ujian->soalUjian()->count() + 1) }}" min="1" required class="{{ $input }}">
                </label>
            </div>

            <label class="block text-xs font-bold text-slate-700">Pertanyaan <span class="text-rose-600">*</span>
                <textarea name="pertanyaan" rows="4" required class="{{ $input }}">{{ $soal->pertanyaan ?? old('pertanyaan') }}</textarea>
            </label>

            <section class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <h3 class="text-sm font-extrabold text-slate-900">Opsi jawaban &amp; kunci</h3>

                <div x-show="tipe === 'pilihan_ganda'" class="mt-3 space-y-2">
                    @foreach($opsi as $opt)
                        <div class="flex items-center gap-2">
                            <label class="flex h-10 w-14 shrink-0 cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white text-xs font-extrabold text-slate-700 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                <input type="radio" name="kunci_jawaban_pilgan" value="{{ $opt }}" @checked($kunciPilgan == $opt) class="h-3.5 w-3.5 border-slate-300 text-emerald-600 focus:ring-emerald-500">{{ $opt }}
                            </label>
                            <input type="text" name="pilihan_jawaban_pilgan[{{ $opt }}]" value="{{ $pilihanTersimpan[$opt] ?? '' }}" placeholder="Teks jawaban opsi {{ $opt }}..." class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        </div>
                    @endforeach
                    <p class="text-xs text-sky-700"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>Pilih tombol huruf untuk menentukan kunci jawaban.</p>
                </div>

                <div x-cloak x-show="tipe === 'pilihan_ganda_kompleks'" class="mt-3 space-y-2">
                    @foreach($opsi as $opt)
                        <div class="flex items-center gap-2">
                            <label class="flex h-10 w-14 shrink-0 cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-slate-300 bg-white text-xs font-extrabold text-slate-700 has-[:checked]:border-emerald-400 has-[:checked]:bg-emerald-50 has-[:checked]:text-emerald-700">
                                <input type="checkbox" name="kunci_jawaban_kompleks[]" value="{{ $opt }}" @checked(in_array($opt, $kunciKompleks)) class="h-3.5 w-3.5 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">{{ $opt }}
                            </label>
                            <input type="text" name="pilihan_jawaban_kompleks[{{ $opt }}]" value="{{ $pilihanTersimpan[$opt] ?? '' }}" placeholder="Teks jawaban opsi {{ $opt }}..." class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        </div>
                    @endforeach
                    <p class="text-xs text-sky-700"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>Centang semua jawaban yang benar (lebih dari satu).</p>
                </div>

                <div x-cloak x-show="tipe === 'benar_salah'" class="mt-3 space-y-2">
                    <template x-for="(row, i) in baris" :key="i">
                        <div class="flex items-center gap-2">
                            <input type="text" :name="`pilihan_jawaban_bs[${i}][pernyataan]`" x-model="row.pernyataan" placeholder="Tulis pernyataan..." class="h-10 min-w-0 flex-1 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                            <select :name="`pilihan_jawaban_bs[${i}][kunci]`" x-model="row.kunci" class="h-10 w-28 shrink-0 rounded-xl border border-slate-300 bg-white px-2 text-sm text-slate-800" aria-label="Kunci pernyataan">
                                <option value="B">Benar</option>
                                <option value="S">Salah</option>
                            </select>
                        </div>
                    </template>
                    <button type="button" @click="baris.push({ pernyataan: '', kunci: 'B' })" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-indigo-200 bg-white px-3 text-xs font-bold text-indigo-700 hover:bg-indigo-50"><i class="fa-solid fa-plus" aria-hidden="true"></i>Tambah pernyataan</button>
                </div>

                <div x-cloak x-show="tipe === 'isian_singkat'" class="mt-3">
                    <label class="block text-xs font-bold text-slate-700">Kunci jawaban singkat
                        <input type="text" name="kunci_jawaban_isian" value="{{ $soal->kunci_jawaban ?? '' }}" placeholder="Contoh: Soekarno" class="{{ $input }}">
                    </label>
                    <p class="mt-1 text-xs text-sky-700"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>Penilaian otomatis butuh kecocokan persis. Gunakan AI Assistant di menu Koreksi untuk toleransi salah ketik.</p>
                </div>

                <p x-cloak x-show="tipe === 'uraian'" class="mt-3 flex items-start gap-2 rounded-xl bg-sky-50 px-3 py-2 text-xs text-sky-900"><i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>Soal uraian dikoreksi manual oleh guru.</p>
            </section>
        </div>

        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <button type="reset" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-xs font-bold text-slate-700 hover:bg-slate-50">Reset</button>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan soal</button>
        </footer>
    </form>
</div>
@endsection
