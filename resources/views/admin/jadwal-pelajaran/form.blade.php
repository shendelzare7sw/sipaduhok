@extends('layouts.app')

@section('title', isset($jadwalPelajaran) ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran')
@section('page-title', isset($jadwalPelajaran) ? 'Edit Jadwal Pelajaran' : 'Tambah Jadwal Pelajaran')
@section('page-subtitle', 'Susun waktu belajar, kelas, mata pelajaran, dan guru dalam satu alur')

@section('content')
@php
    $editing = isset($jadwalPelajaran);
    $selectedKelas = collect(old('kelas_ids', $editing ? $jadwalPelajaran->kelas->pluck('id')->all() : (request('kelas_id') ? [request('kelas_id')] : [])))->map(fn ($id) => (string) $id)->values()->all();
    $selectedTahun = (string) old('tahun_ajaran_id', $editing ? $jadwalPelajaran->tahun_ajaran_id : ($currentTahunAjaran?->id ?? ''));
    $selectedMapel = (string) old('mata_pelajaran_id', $editing ? $jadwalPelajaran->mata_pelajaran_id : '');
    $selectedGuru = (string) old('guru_id', $editing ? $jadwalPelajaran->guru_id : '');
    $selectedHari = old('hari', $editing ? $jadwalPelajaran->hari : '');
    $kelasData = (object) $kelasList->mapWithKeys(fn ($kelas) => [(string) $kelas->id => ['jenjang' => $kelas->jenjang, 'cabang' => (string) $kelas->cabang_id]])->all();
    $oldMapelJenjang = (object) old('mapel_per_jenjang', []);
    $branches = $kelasList->map(fn ($kelas) => ['id' => (string) $kelas->cabang_id, 'nama' => $kelas->cabang->nama_cabang ?? 'Tanpa cabang'])->unique('id')->values();
    $jenjangs = $kelasList->pluck('jenjang')->filter()->unique()->values();
    $inputClass = 'mt-1.5 block h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
@endphp

<div class="min-w-0 w-full space-y-4" x-data="{
    selectedKelas: @js($selectedKelas), kelas: @js($kelasData), kelasSearch: '', kelasCabang: '', kelasJenjang: '', guruSearch: '',
    guruId: @js($selectedGuru), hari: @js($selectedHari), mapelId: @js($selectedMapel), openIstirahat: false,
    get selectedJenjang() { return [...new Set(this.selectedKelas.map(id => this.kelas[id]?.jenjang).filter(Boolean))]; },
    get selectedCabang() { return [...new Set(this.selectedKelas.map(id => this.kelas[id]?.cabang).filter(Boolean))]; },
    kelasVisible(nama, cabang, jenjang) { return (!this.kelasSearch || nama.includes(this.kelasSearch.toLowerCase())) && (!this.kelasCabang || cabang === this.kelasCabang) && (!this.kelasJenjang || jenjang === this.kelasJenjang); },
    guruVisible(nama, cabang) { return (!this.guruSearch || nama.includes(this.guruSearch.toLowerCase())) && (!this.selectedCabang.length || !cabang || this.selectedCabang.includes(cabang)); }
}">
    <header class="flex min-w-0 items-start gap-3">
        <a href="{{ route('admin.jadwal-pelajaran.index', $editing ? ['tahun_ajaran_id' => $jadwalPelajaran->tahun_ajaran_id] : request()->query()) }}" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700" aria-label="Kembali"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-brand-600">Langkah 6 · Persiapan data</p><h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $editing ? 'Edit jadwal ' . $jadwalPelajaran->mataPelajaran->nama_mapel : 'Susun jadwal baru' }}</h2><p class="mt-1 text-sm text-slate-500">Pilih kelas terlebih dahulu; pilihan mapel dan guru akan menyesuaikan otomatis.</p></div>
    </header>

    @if($errors->any())
        <aside class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><p class="font-extrabold">Jadwal belum dapat disimpan.</p><ul class="mt-2 list-disc space-y-1 pl-5 text-xs">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></aside>
    @endif

    <form action="{{ $editing ? route('admin.jadwal-pelajaran.update', $jadwalPelajaran) : route('admin.jadwal-pelajaran.store') }}" method="POST" class="space-y-4">
        @csrf
        @if($editing) @method('PUT') @endif
        <input type="hidden" name="is_multi_jenjang" :value="selectedJenjang.length > 1 ? 1 : 0">

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-calendar-alt"></i></span><div><h3 class="font-extrabold text-slate-950">1. Periode dan kelas</h3><p class="mt-0.5 text-xs text-slate-500">Jadwal dapat diterapkan ke beberapa kelas sekaligus.</p></div></header>
            <div class="space-y-4 p-4 sm:p-5">
                <label class="block max-w-xl"><span class="{{ $labelClass }}">Tahun ajaran <span class="text-red-500">*</span></span>
                    @if($editing)
                        <select class="{{ $inputClass }} bg-slate-50" disabled>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected($selectedTahun === (string) $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>@endforeach</select><input type="hidden" name="tahun_ajaran_id" value="{{ $selectedTahun }}">
                    @else
                        <select name="tahun_ajaran_id" class="{{ $inputClass }}" required @change="window.location.href = @js(route('admin.jadwal-pelajaran.create')) + '?tahun_ajaran_id=' + $event.target.value"><option value="">Pilih tahun ajaran</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected($selectedTahun === (string) $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>@endforeach</select>
                    @endif
                </label>

                <div>
                    <div class="flex flex-wrap items-end justify-between gap-2"><div><p class="{{ $labelClass }}">Kelas tujuan <span class="text-red-500">*</span></p><p class="mt-1 text-xs text-slate-500">Pilih satu atau beberapa kelas yang menerima jadwal sama.</p></div><span class="rounded-lg bg-brand-50 px-2.5 py-1 text-xs font-bold text-brand-700"><span x-text="selectedKelas.length"></span> dipilih</span></div>
                    <div class="mt-3 grid gap-2 sm:grid-cols-3"><label class="relative"><i class="fas fa-search absolute left-3.5 top-3.5 text-xs text-slate-400"></i><input type="search" x-model="kelasSearch" class="h-11 w-full rounded-xl border border-slate-300 !pl-10 pr-3 text-sm outline-none focus:border-brand-500" placeholder="Cari kelas..."></label><select x-model="kelasCabang" class="h-11 rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Semua cabang</option>@foreach($branches as $branch)<option value="{{ $branch['id'] }}">{{ $branch['nama'] }}</option>@endforeach</select><select x-model="kelasJenjang" class="h-11 rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Semua jenjang</option>@foreach($jenjangs as $jenjang)<option value="{{ $jenjang }}">{{ $jenjang }}</option>@endforeach</select></div>
                    <div class="mt-3 grid max-h-72 gap-2 overflow-y-auto rounded-xl border border-slate-200 bg-slate-50 p-2 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse($kelasList as $kelas)
                            <label x-show="kelasVisible(@js(strtolower($kelas->nama_kelas)), @js((string) $kelas->cabang_id), @js($kelas->jenjang))" class="flex cursor-pointer items-start gap-3 rounded-xl border border-slate-200 bg-white p-3 transition hover:border-brand-300"><input type="checkbox" name="kelas_ids[]" value="{{ $kelas->id }}" x-model="selectedKelas" class="mt-0.5 h-4 w-4 rounded border-slate-300 text-brand-600"><span class="min-w-0"><span class="block truncate text-sm font-extrabold text-slate-900">{{ $kelas->nama_kelas }}</span><span class="mt-0.5 block truncate text-xs text-slate-500">{{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? 'Tanpa cabang' }}</span></span></label>
                        @empty
                            <p class="col-span-full p-5 text-center text-sm text-slate-500">Belum ada kelas pada tahun ajaran ini.</p>
                        @endforelse
                    </div>
                    @error('kelas_ids')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
                </div>
            </div>
        </section>

        <section class="grid min-w-0 gap-4 xl:grid-cols-2">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950">2. Mata pelajaran</h3><p class="mt-0.5 text-xs text-slate-500">Daftar otomatis dibatasi sesuai jenjang kelas.</p></header>
                <div class="p-4 sm:p-5">
                    <p x-show="selectedKelas.length === 0" class="rounded-xl bg-amber-50 p-3 text-xs font-semibold text-amber-800"><i class="fas fa-arrow-up mr-1"></i>Pilih kelas terlebih dahulu.</p>
                    <label x-show="selectedKelas.length > 0 && selectedJenjang.length <= 1" class="block"><span class="{{ $labelClass }}">Mata pelajaran <span class="text-red-500">*</span></span><select name="mata_pelajaran_id" x-model="mapelId" :disabled="selectedJenjang.length > 1" :required="selectedKelas.length > 0 && selectedJenjang.length <= 1" class="{{ $inputClass }}"><option value="">Pilih mata pelajaran</option>@foreach($mataPelajaranList as $mapel)<option value="{{ $mapel->id }}" x-show="!selectedJenjang.length || selectedJenjang.includes(@js($mapel->jenjang))">{{ $mapel->nama_mapel }} ({{ $mapel->jenjang }})</option>@endforeach</select></label>
                    <div x-show="selectedJenjang.length > 1" class="space-y-3"><p class="rounded-xl bg-blue-50 p-3 text-xs text-blue-800">Kelas lintas jenjang dipilih. Tentukan mapel untuk setiap jenjang.</p>@foreach($mataPelajaranList->groupBy('jenjang') as $jenjang => $mapels)<label x-show="selectedJenjang.includes(@js($jenjang))" class="block"><span class="{{ $labelClass }}">Jenjang {{ $jenjang }} <span class="text-red-500">*</span></span><select name="mapel_per_jenjang[{{ $jenjang }}]" :disabled="!selectedJenjang.includes(@js($jenjang))" :required="selectedJenjang.includes(@js($jenjang))" class="{{ $inputClass }}"><option value="">Pilih mapel {{ $jenjang }}</option>@foreach($mapels as $mapel)<option value="{{ $mapel->id }}" @selected((string) ($oldMapelJenjang->{$jenjang} ?? ($editing && $jadwalPelajaran->mataPelajaran->jenjang === $jenjang ? $jadwalPelajaran->mata_pelajaran_id : '')) === (string) $mapel->id)>{{ $mapel->nama_mapel }}</option>@endforeach</select></label>@endforeach</div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950">3. Guru pengajar</h3><p class="mt-0.5 text-xs text-slate-500">Opsional; tanpa guru jadwal disimpan berstatus kosong.</p></header>
                <div class="space-y-3 p-4 sm:p-5"><label class="relative block"><i class="fas fa-search absolute left-3.5 top-3.5 text-xs text-slate-400"></i><input type="search" x-model="guruSearch" class="h-11 w-full rounded-xl border border-slate-300 !pl-10 pr-3 text-sm outline-none focus:border-brand-500" placeholder="Cari nama guru..."></label><div class="max-h-64 space-y-2 overflow-y-auto pr-1"><label class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-slate-300 p-3"><input type="radio" name="guru_id" value="" x-model="guruId" class="h-4 w-4 border-slate-300 text-brand-600"><span class="text-sm font-bold text-slate-600">Belum ditentukan</span></label>@foreach($guruList as $guru)<label x-show="guruVisible(@js(strtolower($guru->nama_lengkap)), @js((string) ($guru->user->cabang_id ?? '')))" class="flex cursor-pointer items-center gap-3 rounded-xl border border-slate-200 p-3 hover:border-brand-300"><input type="radio" name="guru_id" value="{{ $guru->id }}" x-model="guruId" class="h-4 w-4 border-slate-300 text-brand-600"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-xs font-extrabold text-slate-700">{{ strtoupper(substr($guru->nama_lengkap, 0, 2)) }}</span><span class="min-w-0"><span class="block truncate text-sm font-extrabold text-slate-900">{{ $guru->nama_lengkap }}</span><span class="block truncate text-xs text-slate-500">{{ $guru->user->cabang->nama_cabang ?? 'Semua cabang' }}</span></span></label>@endforeach</div></div>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950">4. Hari dan waktu</h3><p class="mt-0.5 text-xs text-slate-500">Sistem tetap memeriksa bentrok kelas dan guru saat disimpan.</p></header>
            <div class="grid gap-4 p-4 sm:grid-cols-3 sm:p-5"><label class="block"><span class="{{ $labelClass }}">Hari <span class="text-red-500">*</span></span><select name="hari" x-model="hari" class="{{ $inputClass }}" required><option value="">Pilih hari</option>@foreach($hariList as $hari)<option value="{{ $hari }}">{{ $hari }}</option>@endforeach</select></label><label class="block"><span class="{{ $labelClass }}">Jam mulai <span class="text-red-500">*</span></span><input type="time" name="jam_mulai" value="{{ old('jam_mulai', $editing ? \Carbon\Carbon::parse($jadwalPelajaran->jam_mulai)->format('H:i') : '07:00') }}" class="{{ $inputClass }}" required></label><label class="block"><span class="{{ $labelClass }}">Jam selesai <span class="text-red-500">*</span></span><input type="time" name="jam_selesai" value="{{ old('jam_selesai', $editing ? \Carbon\Carbon::parse($jadwalPelajaran->jam_selesai)->format('H:i') : '08:30') }}" class="{{ $inputClass }}" required></label></div>
            <div class="border-t border-slate-200 px-4 py-4 sm:px-5">
                @if($pengaturanIstirahat->isNotEmpty())
                    <button type="button" @click="openIstirahat = !openIstirahat" class="flex w-full items-center justify-between gap-3 text-left"><span><span class="block text-sm font-extrabold text-slate-800"><i class="fas fa-mug-hot mr-2 text-amber-500"></i>Cek waktu istirahat</span><span class="mt-0.5 block text-xs text-slate-500">Ditampilkan sesuai jenjang kelas dan hari yang dipilih.</span></span><i class="fas fa-chevron-down text-slate-400 transition" :class="openIstirahat && 'rotate-180'"></i></button>
                    <div x-cloak x-show="openIstirahat" class="mt-3 grid gap-2 sm:grid-cols-2 lg:grid-cols-3">@foreach($pengaturanIstirahat as $jenjang => $items)@foreach($items as $item)<article x-show="(!selectedJenjang.length || selectedJenjang.includes(@js($jenjang))) && (!hari || @js(is_array($item->hari_aktif) ? $item->hari_aktif : [$item->hari_aktif]).includes(hari))" class="rounded-xl border border-amber-200 bg-amber-50 p-3"><div class="flex items-center justify-between gap-2"><span class="text-xs font-extrabold text-amber-800">{{ $jenjang }} · Istirahat {{ $item->urutan }}</span><strong class="whitespace-nowrap text-xs text-slate-800">{{ substr($item->jam_mulai, 0, 5) }}–{{ substr($item->jam_selesai, 0, 5) }}</strong></div><p class="mt-1 text-xs text-slate-600">{{ $item->nama_istirahat }}</p></article>@endforeach @endforeach</div>
                @else
                    <p class="text-xs text-amber-800"><i class="fas fa-triangle-exclamation mr-1"></i>Belum ada waktu istirahat. <a href="{{ route('admin.pengaturan-istirahat.index') }}" class="font-bold text-amber-900">Atur sekarang</a>.</p>
                @endif
            </div>
            <div class="border-t border-slate-200 p-4 sm:p-5"><label class="block"><span class="{{ $labelClass }}">Keterangan <span class="text-xs font-medium text-slate-400">(opsional)</span></span><textarea name="keterangan" rows="3" class="mt-1.5 block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Catatan tambahan untuk jadwal ini">{{ old('keterangan', $editing ? $jadwalPelajaran->keterangan : '') }}</textarea></label></div>
        </section>

        @if($editing && $jadwalPelajaran->histories->isNotEmpty())
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-history mr-2 text-brand-600"></i>Riwayat perubahan</h3></header><div class="divide-y divide-slate-100">@foreach($jadwalPelajaran->histories->sortByDesc('changed_at')->take(10) as $history)<article class="grid gap-1 px-4 py-3 text-xs sm:grid-cols-[140px_160px_minmax(0,1fr)] sm:px-5"><time class="font-semibold text-slate-500">{{ $history->changed_at->format('d/m/Y H:i') }}</time><strong class="text-slate-800">{{ ucfirst(str_replace('_', ' ', $history->field_changed)) }}</strong><p class="min-w-0 break-words text-slate-600">{{ $history->old_value ?: '—' }} <i class="fas fa-arrow-right mx-1 text-slate-300"></i> {{ $history->new_value ?: '—' }}</p></article>@endforeach</div></section>
        @endif

        <div class="sticky bottom-3 z-10 flex flex-wrap items-center justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none"><a href="{{ route('admin.jadwal-pelajaran.index', $editing ? ['tahun_ajaran_id' => $jadwalPelajaran->tahun_ajaran_id] : request()->query()) }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-save"></i>{{ $editing ? 'Simpan perubahan' : 'Simpan jadwal' }}</button></div>
    </form>
</div>
@endsection
