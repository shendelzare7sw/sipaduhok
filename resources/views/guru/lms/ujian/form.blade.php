@extends('layouts.lms-guru')

@php
    $isEdit = isset($ujian);
    $isLatihan = $tipeUjian === 'latihan';
    $label = $isLatihan ? 'Latihan' : 'Ujian';
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $args = [$kelas->id, $mapel->id];
    $tipeTerpilih = old('tipe_ujian', $isEdit ? $ujian->tipe_ujian : '');
    $bisaDiulang = (bool) old('bisa_diulang', $isEdit ? $ujian->bisa_diulang : false);
    $sudahSubmit = (bool) old('_token');
    $tampilkanNilai = (bool) old('tampilkan_nilai', $isEdit ? $ujian->tampilkan_nilai : ! $sudahSubmit);
    $tampilkanRiwayat = (bool) old('tampilkan_riwayat', $isEdit ? $ujian->tampilkan_riwayat : ! $sudahSubmit);
    $tipeGroups = [
        'Ulangan' => ['ulangan_harian' => 'Ulangan Harian'],
        'Semester Ganjil' => ['pts_ganjil' => 'PTS Ganjil', 'pas_ganjil' => 'PAS Ganjil'],
        'Semester Genap' => ['pts_genap' => 'PTS Genap', 'pas_genap' => 'PAS Genap'],
    ];
    if ($isTingkatAkhir) {
        $tipeGroups['Ujian Kelulusan'] = ['to_1' => 'Try Out 1', 'to_2' => 'Try Out 2', 'to_3' => 'Try Out 3', 'upk' => 'UPK', 'ujian_praktek' => 'Ujian Praktek'];
    }
    $input = 'mt-1 block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100 disabled:bg-slate-100 disabled:text-slate-500';
@endphp

@section('title', ($isEdit ? 'Edit ' : 'Buat ') . $label)
@section('page-title', ($isEdit ? 'Edit ' : 'Buat ') . $label . ($isEdit ? '' : ' Baru'))
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5">
    <a href="{{ route($prefix.'.index', $args) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline shadow-sm hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali ke daftar {{ strtolower($label) }}</a>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">
            <p class="font-bold">Periksa kembali isian berikut:</p>
            <ul class="mt-1 list-disc space-y-0.5 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form action="{{ $isEdit ? route($prefix.'.update', [...$args, $ujian->id]) : route($prefix.'.store', $args) }}" method="POST"
          class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
          x-data="{ ulang: @js($bisaDiulang) }">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <header class="border-b border-slate-200 px-4 py-4 sm:px-6">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-circle-plus' }} text-indigo-600" aria-hidden="true"></i>{{ $isEdit ? 'Perbarui '.strtolower($label) : strtolower($label).' baru' }}</h2>
            <p class="mt-1 text-xs text-slate-500">Atur identitas dan jadwal {{ strtolower($label) }}. Soal ditambahkan setelah disimpan melalui menu <strong>Kelola soal</strong>.</p>
        </header>

        <div class="space-y-5 px-4 py-5 sm:px-6">
            <label class="block text-xs font-bold text-slate-700">Judul {{ strtolower($label) }} <span class="text-rose-600">*</span>
                <input type="text" name="judul_ujian" value="{{ old('judul_ujian', $isEdit ? $ujian->judul_ujian : '') }}" required class="{{ $input }}">
                @error('judul_ujian')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <label class="block text-xs font-bold text-slate-700">Deskripsi
                <textarea name="deskripsi" rows="3" class="{{ $input }}">{{ old('deskripsi', $isEdit ? $ujian->deskripsi : '') }}</textarea>
            </label>

            @if($isLatihan)
                {{-- Menu Latihan terpisah dari Ujian: tipe selalu "latihan", tidak perlu dipilih. --}}
                <input type="hidden" name="tipe_ujian" value="latihan">
            @endif
            <div class="grid gap-4 {{ $isLatihan ? 'md:grid-cols-2' : 'md:grid-cols-3' }}">
                @unless($isLatihan)
                <label class="block text-xs font-bold text-slate-700">Tipe <span class="text-rose-600">*</span>
                        <select name="tipe_ujian" required class="{{ $input }}">
                            @unless($isEdit)<option value="">-- Pilih tipe --</option>@endunless
                            @foreach($tipeGroups as $grup => $opsi)
                                <optgroup label="{{ $grup }}">
                                    @foreach($opsi as $value => $text)
                                        <option value="{{ $value }}" @selected($tipeTerpilih == $value)>{{ $text }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    <span class="mt-1 block font-normal text-slate-500">PTS/PAS wajib memiliki 4 tipe soal: pilihan ganda, benar/salah, isian, uraian.</span>
                    @error('tipe_ujian')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                @endunless
                <label class="block text-xs font-bold text-slate-700">Tanggal mulai <span class="text-rose-600">*</span>
                    <input type="datetime-local" name="tanggal_mulai" value="{{ old('tanggal_mulai', $isEdit ? $ujian->tanggal_mulai->format('Y-m-d\TH:i') : '') }}" required class="{{ $input }}">
                    @error('tanggal_mulai')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
                <label class="block text-xs font-bold text-slate-700">Tanggal selesai <span class="text-rose-600">*</span>
                    <input type="datetime-local" name="tanggal_selesai" value="{{ old('tanggal_selesai', $isEdit ? $ujian->tanggal_selesai->format('Y-m-d\TH:i') : '') }}" required class="{{ $input }}">
                    @error('tanggal_selesai')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
                </label>
            </div>

            <label class="block text-xs font-bold text-slate-700 sm:max-w-xs">Durasi (menit) @unless($isEdit)<span class="text-rose-600">*</span>@endunless
                <input type="number" name="durasi_menit" value="{{ old('durasi_menit', $isEdit ? $ujian->durasi_menit : 90) }}" min="0" @unless($isEdit) required @endunless class="{{ $input }}">
                <span class="mt-1 block font-normal text-slate-500">Isi 0 untuk waktu tidak terbatas.</span>
                @error('durasi_menit')<span class="mt-1 block font-semibold text-rose-600">{{ $message }}</span>@enderror
            </label>

            <fieldset class="space-y-3">
                <legend class="text-sm font-extrabold text-slate-900">Penilaian &amp; pengulangan</legend>
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <input type="checkbox" name="tampilkan_nilai" value="1" @checked($tampilkanNilai) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Tampilkan nilai ke siswa</strong>Jika dinonaktifkan, siswa hanya melihat ucapan terima kasih setelah mengerjakan.</span>
                </label>
                <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <input type="checkbox" name="tampilkan_riwayat" value="1" @checked($tampilkanRiwayat) class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Izinkan siswa melihat riwayat &amp; jawaban benar</strong>Siswa dapat mencocokkan jawabannya dengan kunci setelah {{ strtolower($label) }} selesai.</span>
                </label>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <label class="flex cursor-pointer items-start gap-3">
                        <input type="checkbox" name="bisa_diulang" value="1" x-model="ulang" class="mt-0.5 h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs leading-5 text-slate-600"><strong class="block text-sm text-slate-900">Bisa dikerjakan ulang</strong>Siswa dapat mengulang sesuai batas. Nilai yang diambil adalah nilai terbaik.</span>
                    </label>
                    <label x-cloak x-show="ulang" class="ml-7 mt-3 flex items-center gap-2 text-xs font-bold text-slate-600">
                        Diulang
                        <input type="number" name="batas_pengulangan" value="{{ old('batas_pengulangan', $isEdit ? ($ujian->batas_pengulangan ?? 2) : 2) }}" min="0" class="h-10 w-20 rounded-xl border border-slate-300 bg-white px-2 text-center text-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                        kali
                    </label>
                </div>
            </fieldset>

            @unless($isEdit)
                <p class="flex items-start gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs leading-5 text-sky-900"><i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i><span><strong>Catatan:</strong> setelah {{ strtolower($label) }} dibuat, tambahkan soal melalui Kelola soal (tulis manual, impor Excel, atau AI Question Generator).</span></p>
            @endunless

            @include('guru.partials.multi-kelas-selector')
        </div>

        <footer class="flex flex-col-reverse gap-2 border-t border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:justify-end sm:px-6">
            <a href="{{ route($prefix.'.index', $args) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 bg-white px-5 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>{{ $isEdit ? 'Simpan perubahan' : 'Buat '.strtolower($label) }}</button>
        </footer>
    </form>
</div>
@endsection
