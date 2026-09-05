@php
    $errors = $errors ?? new \Illuminate\Support\ViewErrorBag();
    $editing = isset($kelas);
    $selectedTahun = (string) old('tahun_ajaran_id', $editing ? $kelas->tahun_ajaran_id : ($tahunAjarans->firstWhere('is_active', true)?->id ?? ''));
    $selectedCabang = (string) old('cabang_id', $editing ? $kelas->cabang_id : '');
    $selectedJenjang = old('jenjang', $editing ? $kelas->jenjang : '');
    $selectedNama = old('nama_kelas', $editing ? $kelas->nama_kelas : '');
    $selectedWali = (string) old('wali_kelas_id', $editing ? $kelas->wali_kelas_id : '');
    $yearMap = (object) $tahunAjarans->mapWithKeys(fn ($ta) => [(string) $ta->id => date('Y', strtotime($ta->tanggal_mulai))])->all();
    $branchMap = (object) $cabangs->mapWithKeys(fn ($cabang) => [(string) $cabang->id => $cabang->kode_cabang])->all();
    $classSuggestions = [
        'KB' => ['KB1', 'KB2', 'KB3'],
        'TKA' => ['TKA1', 'TKA2', 'TKA3'],
        'TKB' => ['TKB1', 'TKB2', 'TKB3'],
        'SD' => ['1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B', '5A', '5B', '6A', '6B'],
        'SMP' => ['7A', '7B', '8A', '8B', '9A', '9B'],
        'SMA' => ['10A', '10B', '11A', '11B', '12A', '12B'],
    ];
    $inputClass = 'mt-1.5 block h-11 w-full rounded-xl border border-slate-300 bg-white px-3.5 text-sm text-slate-900 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $labelClass = 'block text-sm font-bold text-slate-700';
@endphp

<div
    class="min-w-0 w-full space-y-4"
    x-data="{
        tahunId: @js($selectedTahun),
        cabangId: @js($selectedCabang),
        jenjang: @js($selectedJenjang),
        nama: @js($selectedNama),
        years: @js($yearMap),
        branches: @js($branchMap),
        suggestions: @js($classSuggestions),
        get kode() {
            const tahun = this.years[this.tahunId];
            const cabang = this.branches[this.cabangId];
            const nama = this.nama.trim().toUpperCase().replace(/\s+/g, '');
            return tahun && cabang && this.jenjang && nama
                ? `${cabang}-${this.jenjang}-${nama}-${tahun}`
                : '-';
        }
    }"
>
    <header class="flex min-w-0 items-start gap-3">
        <a href="{{ route('admin.kelas.index') }}" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-600 no-underline hover:border-brand-300 hover:text-brand-700" aria-label="Kembali ke daftar kelas"><i class="fas fa-arrow-left" aria-hidden="true"></i></a>
        <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wider text-brand-600">Data master kelas</p><h2 class="text-xl font-extrabold text-slate-950 sm:text-2xl">{{ $editing ? 'Edit kelas ' . $kelas->nama_kelas : 'Buat kelas baru' }}</h2><p class="mt-1 text-sm text-slate-500">Lengkapi data dasar terlebih dahulu. Siswa ditempatkan setelah kelas tersimpan.</p></div>
    </header>

    @if($errors->any())
        <aside class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><p class="font-extrabold">Data kelas belum dapat disimpan.</p><ul class="mt-2 list-disc space-y-1 pl-5 text-xs">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></aside>
    @endif

    <form action="{{ $editing ? route('admin.kelas.update', $kelas) : route('admin.kelas.store') }}" method="POST" class="space-y-4">
        @csrf
        @if($editing)
            @method('PUT')
            <input type="hidden" name="_return_url" value="{{ url()->previous(route('admin.kelas.index')) }}">
        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="flex items-start gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600"><i class="fas fa-chalkboard" aria-hidden="true"></i></span><div><h3 class="font-extrabold text-slate-950">Identitas kelas</h3><p class="mt-0.5 text-xs text-slate-500">Tahun, cabang, dan jenjang membentuk kode kelas otomatis.</p></div></header>
            <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
                <label class="block"><span class="{{ $labelClass }}">Tahun ajaran <span class="text-red-500">*</span></span><select id="tahun_ajaran_id" name="tahun_ajaran_id" x-model="tahunId" class="{{ $inputClass }}" required><option value="">Pilih tahun ajaran</option>@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}">{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' (Aktif)' : '' }}</option>@endforeach</select>@error('tahun_ajaran_id')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="{{ $labelClass }}">Cabang <span class="text-red-500">*</span></span><select id="cabang_id" name="cabang_id" x-model="cabangId" class="{{ $inputClass }}" required><option value="">Pilih cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select>@error('cabang_id')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="{{ $labelClass }}">Jenjang <span class="text-red-500">*</span></span><select id="jenjang" name="jenjang" x-model="jenjang" class="{{ $inputClass }}" required><option value="">Pilih jenjang</option>@foreach($jenjangs as $item)<option value="{{ $item }}">{{ $item }}</option>@endforeach</select>@error('jenjang')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                <div>
                    <label for="nama_kelas" class="{{ $labelClass }}">Nama kelas <span class="text-red-500">*</span></label>
                    <input id="nama_kelas" type="text" name="nama_kelas" x-model="nama" value="{{ $selectedNama }}" class="{{ $inputClass }}" maxlength="50" placeholder="Contoh: 7A atau KB1" required>
                    @error('nama_kelas')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
                    <div x-cloak x-show="jenjang && suggestions[jenjang]?.length" class="mt-2 flex flex-wrap gap-1.5"><span class="mr-1 self-center text-[10px] font-bold uppercase tracking-wide text-slate-400">Saran</span><template x-for="item in (suggestions[jenjang] || [])" :key="item"><button type="button" @click="nama = item" class="rounded-lg bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600 hover:bg-brand-50 hover:text-brand-700" x-text="item"></button></template></div>
                </div>
            </div>
        </section>

        <section class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_minmax(280px,0.6fr)]">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950">Penanggung jawab dan kapasitas</h3><p class="mt-0.5 text-xs text-slate-500">Wali kelas boleh dikosongkan dan ditentukan kemudian.</p></header>
                <div class="grid gap-4 p-4 sm:grid-cols-2 sm:p-5">
                    <label class="block"><span class="{{ $labelClass }}">Kuota siswa <span class="text-red-500">*</span></span><input id="kuota_siswa" type="number" name="kuota_siswa" value="{{ old('kuota_siswa', $editing ? $kelas->kuota_siswa : 30) }}" min="1" max="100" class="{{ $inputClass }}" required><span class="mt-1.5 block text-xs text-slate-500">Antara 1 sampai 100 siswa.</span>@error('kuota_siswa')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                    <label class="block"><span class="{{ $labelClass }}">Wali kelas <span class="text-xs font-medium text-slate-400">(opsional)</span></span><select id="wali_kelas_id" name="wali_kelas_id" class="{{ $inputClass }}"><option value="">Belum ditentukan</option>@foreach($waliKelasOptions as $wali)<option value="{{ $wali->id }}" @selected($selectedWali === (string) $wali->id)>{{ $wali->nama_lengkap }} · {{ $wali->user->cabang->nama_cabang ?? 'Tanpa cabang' }}</option>@endforeach</select><span class="mt-1.5 block text-xs text-slate-500">Satu wali dapat menangani beberapa kelas.</span>@error('wali_kelas_id')<span class="mt-1.5 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                </div>
            </div>

            <aside class="rounded-2xl border border-brand-200 bg-brand-50 p-4 shadow-sm sm:p-5">
                <div class="flex items-center gap-2 text-brand-700"><i class="fas fa-wand-magic-sparkles" aria-hidden="true"></i><h3 class="text-sm font-extrabold">Kode kelas otomatis</h3></div>
                <p class="mt-3 break-all rounded-xl bg-white px-3 py-3 font-mono text-sm font-extrabold text-slate-800 ring-1 ring-brand-100" x-text="kode">{{ $editing ? $kelas->kode_kelas : '-' }}</p>
                <p class="mt-3 text-xs leading-5 text-brand-800">Kode mengikuti format cabang-jenjang-nama-tahun dan akan dibuat oleh sistem saat disimpan.</p>
            </aside>
        </section>

        <aside class="rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs leading-5 text-blue-900"><p class="font-extrabold"><i class="fas fa-circle-info mr-1" aria-hidden="true"></i>Langkah setelah menyimpan</p><p class="mt-1">Buka detail kelas lalu pilih <strong>Kelola siswa</strong> untuk menempatkan siswa ke kelas ini.</p></aside>

        <div class="sticky bottom-3 z-10 flex flex-wrap items-center justify-end gap-2 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur sm:static sm:border-0 sm:bg-transparent sm:p-0 sm:shadow-none">
            @if($editing)<a href="{{ route('admin.kelas.show', $kelas) }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-blue-50 px-4 text-sm font-bold text-blue-700 no-underline hover:bg-blue-100"><i class="fas fa-eye" aria-hidden="true"></i>Lihat detail</a>@endif
            <a href="{{ route('admin.kelas.index') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a>
            <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>{{ $editing ? 'Simpan perubahan' : 'Simpan kelas' }}</button>
        </div>
    </form>
</div>
