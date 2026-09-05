@extends('layouts.app')

@section('title', 'Buat Tagihan Massal')
@section('page-title', 'Buat Tagihan Massal')
@section('page-subtitle', 'Buat tagihan yang sama untuk beberapa kelas sekaligus')

@section('content')
@php
    $classOptions = $kelasList->map(fn ($kelas) => [
        'id' => (string) $kelas->id,
        'name' => $kelas->nama_kelas,
        'level' => $kelas->jenjang,
        'branchId' => (string) $kelas->cabang_id,
        'branch' => $kelas->cabang->nama_cabang ?? 'Cabang belum diatur',
    ])->values();
    $selectedClasses = collect(old('kelas_ids', []))->map(fn ($id) => (string) $id)->values();
    $customNames = old('custom_jenis_tagihan', []);
    $customAmounts = old('custom_tagihan', []);
    $customDates = old('custom_tanggal_jatuh_tempo', []);
    $customItems = collect($customNames)->map(fn ($name, $key) => [
        'id' => (string) $key,
        'name' => $name,
        'amount' => $customAmounts[$key] ?? '0',
        'date' => $customDates[$key] ?? $defaultDueDate,
    ])->values();
@endphp

<div
    data-tagihan-bulk-create
    class="min-w-0 w-full space-y-5"
    x-data="{
        classes: @js($classOptions),
        selected: @js($selectedClasses),
        branch: '',
        level: '',
        query: '',
        globalDate: @js(old('global_jatuh_tempo', $defaultDueDate)),
        customs: @js($customItems),
        nextCustom: {{ max(1, count($customItems) + 1) }},
        get visibleClasses() { const needle = this.query.trim().toLowerCase(); return this.classes.filter(item => (!this.branch || item.branchId === this.branch) && (!this.level || item.level === this.level) && (!needle || `${item.name} ${item.level} ${item.branch}`.toLowerCase().includes(needle))); },
        get allVisibleSelected() { return this.visibleClasses.length > 0 && this.visibleClasses.every(item => this.selected.includes(item.id)); },
        toggleVisible() { const ids = this.visibleClasses.map(item => item.id); this.selected = this.allVisibleSelected ? this.selected.filter(id => !ids.includes(id)) : [...new Set([...this.selected, ...ids])]; },
        addCustom() { this.customs.push({ id: `new-${this.nextCustom++}`, name: '', amount: '0', date: this.globalDate || @js($defaultDueDate) }); },
        formatCurrency(value) { const digits = String(value ?? '').replace(/\D/g, '').replace(/^0+/, '') || '0'; return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
        applyGlobalDate() { if (this.globalDate) { this.$root.querySelectorAll('[data-due-date]').forEach(input => input.value = this.globalDate); this.customs.forEach(item => item.date = this.globalDate); } },
        async removeCustom(index) { const result = await Swal.fire({ icon: 'question', title: 'Hapus jenis custom?', text: 'Isian pada kartu ini akan dihapus.', showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal', confirmButtonColor: '#dc2626', reverseButtons: true }); if (result.isConfirmed) this.customs.splice(index, 1); },
        async submitForm(event) {
            if (!this.selected.length) { await Swal.fire({ icon: 'warning', title: 'Pilih kelas dahulu', text: 'Pilih minimal satu kelas penerima tagihan.', confirmButtonColor: '#285dcc' }); return; }
            const amountInputs = [...event.target.querySelectorAll('[data-currency]')];
            const hasAmount = amountInputs.some(input => Number(input.value.replace(/\D/g, '')) > 0);
            if (!hasAmount) { await Swal.fire({ icon: 'warning', title: 'Nominal belum diisi', text: 'Isi minimal satu nominal tagihan lebih dari Rp 0.', confirmButtonColor: '#285dcc' }); return; }
            const result = await Swal.fire({ icon: 'question', title: 'Buat tagihan massal?', html: `Tagihan akan diterapkan kepada seluruh siswa aktif di <strong>${this.selected.length} kelas</strong>. Data sejenis yang sudah ada akan dilewati.`, showCancelButton: true, confirmButtonText: 'Ya, buat tagihan', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true });
            if (result.isConfirmed) { amountInputs.forEach(input => input.value = input.value.replace(/\D/g, '') || '0'); event.target.submit(); }
        }
    }"
>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left"></i>Kembali ke tagihan</a>
        <a href="{{ route('admin.keuangan.tagihan.generate-spp') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-violet-50 px-4 text-sm font-bold text-violet-700 no-underline hover:bg-violet-100"><i class="fas fa-calendar-days"></i>Butuh SPP bulanan?</a>
    </div>

    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><div class="flex items-start gap-3 text-sm leading-6 text-blue-900"><i class="fas fa-circle-info mt-1 text-blue-700"></i><p><strong>Alur kerja:</strong> pilih kelas penerima, isi nominal yang diperlukan, periksa jatuh tempo, lalu simpan. Tagihan dibuat hanya untuk siswa aktif; data sejenis yang sudah ada tidak dibuat ulang.</p></div></section>

    <form method="POST" action="{{ route('admin.keuangan.tagihan.bulk-create') }}" class="space-y-5" @submit.prevent="submitForm($event)">
        @csrf

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"><div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-school"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">1. Pilih kelas penerima</h2><p class="mt-1 text-sm text-slate-500"><strong class="text-brand-700" x-text="selected.length"></strong> kelas dipilih dari <span x-text="visibleClasses.length"></span> hasil yang terlihat.</p></div><span class="inline-flex min-h-9 items-center gap-2 self-start rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700"><i class="fas fa-calendar-check"></i>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</span></div></header>

            <div class="grid gap-3 border-b border-slate-100 bg-slate-50 p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-5">
                <label><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select x-model="branch" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua cabang</option>@foreach($kelasList->unique('cabang_id') as $kelas)@if($kelas->cabang)<option value="{{ $kelas->cabang_id }}">{{ $kelas->cabang->nama_cabang }}</option>@endif @endforeach</select></label>
                <label><span class="mb-1 block text-xs font-bold text-slate-600">Jenjang</span><select x-model="level" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua jenjang</option>@foreach($kelasList->pluck('jenjang')->unique()->sort() as $jenjang)<option value="{{ $jenjang }}">{{ $jenjang }}</option>@endforeach</select></label>
                <label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold text-slate-600">Cari kelas</span><span class="relative block"><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" x-model="query" placeholder="Nama kelas atau cabang..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span></label>
            </div>

            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5"><button type="button" @click="toggleVisible" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-check-double"></i><span x-text="allVisibleSelected ? 'Batalkan hasil terlihat' : 'Pilih semua hasil terlihat'"></span></button><button type="button" @click="selected = []" :disabled="!selected.length" class="min-h-9 rounded-xl px-3 text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:opacity-40">Kosongkan pilihan</button></div>

            <div class="grid max-h-[28rem] gap-3 overflow-y-auto p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-5">
                <template x-for="item in visibleClasses" :key="item.id"><label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 transition" :class="selected.includes(item.id) ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 hover:border-brand-200 hover:bg-slate-50'"><input type="checkbox" name="kelas_ids[]" :value="item.id" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="min-w-0"><strong class="block truncate text-sm text-slate-950" x-text="item.name"></strong><span class="mt-1 block truncate text-xs text-slate-500" x-text="`${item.level} · ${item.branch}`"></span></span></label></template>
                <div x-show="!visibleClasses.length" class="col-span-full py-10 text-center text-sm text-slate-500">Tidak ada kelas yang cocok dengan filter.</div>
            </div>
            @error('kelas_ids')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
            @error('kelas_ids.*')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between"><div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fas fa-file-invoice-dollar"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">2. Isi nominal tagihan</h2><p class="mt-1 text-sm text-slate-500">Isi Rp 0 untuk jenis yang tidak ingin dibuat.</p></div><label class="w-full sm:w-64"><span class="mb-1 block text-xs font-bold text-slate-600">Samakan jatuh tempo</span><input type="date" x-model="globalDate" @change="applyGlobalDate" min="{{ $tagihanDateMin }}" max="{{ $tagihanDateMax }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label></div></header>

            <div class="grid gap-3 p-4 md:grid-cols-2 xl:grid-cols-3 sm:p-5">
                @foreach($jenisTagihan as $key => $label)
                    <article class="rounded-xl border border-slate-200 p-4"><h3 class="text-sm font-extrabold text-slate-950">{{ $label }}</h3><label class="mt-3 block"><span class="mb-1 block text-xs font-bold text-slate-600">Nominal</span><span class="flex h-11 overflow-hidden rounded-xl border border-slate-300 bg-white focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100"><span class="flex items-center border-r border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-500">Rp</span><input data-currency type="text" inputmode="numeric" name="tagihan[{{ $key }}]" value="{{ old('tagihan.' . $key, '0') }}" @input="$el.value = formatCurrency($el.value)" class="min-w-0 flex-1 border-0 px-3 text-sm outline-none" placeholder="0"></span></label><label class="mt-3 block"><span class="mb-1 block text-xs font-bold text-slate-600">Jatuh tempo</span><input data-due-date type="date" name="tanggal_jatuh_tempo[{{ $key }}]" value="{{ old('tanggal_jatuh_tempo.' . $key, $defaultDueDate) }}" min="{{ $tagihanDateMin }}" max="{{ $tagihanDateMax }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label></article>
                @endforeach

                <template x-for="(item, index) in customs" :key="item.id"><article class="relative rounded-xl border border-violet-200 bg-violet-50/30 p-4"><button type="button" @click="removeCustom(index)" class="absolute right-3 top-3 flex h-8 w-8 items-center justify-center rounded-lg bg-red-50 text-red-600 hover:bg-red-100" aria-label="Hapus jenis tagihan custom"><i class="fas fa-trash text-xs"></i></button><label class="block pr-10"><span class="mb-1 block text-xs font-bold text-violet-700">Nama jenis custom</span><input type="text" :name="`custom_jenis_tagihan[${item.id}]`" x-model="item.name" required placeholder="Contoh: Les tambahan" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-100"></label><label class="mt-3 block"><span class="mb-1 block text-xs font-bold text-slate-600">Nominal</span><span class="flex h-11 overflow-hidden rounded-xl border border-slate-300 bg-white focus-within:border-violet-500 focus-within:ring-2 focus-within:ring-violet-100"><span class="flex items-center border-r border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-500">Rp</span><input data-currency type="text" inputmode="numeric" :name="`custom_tagihan[${item.id}]`" x-model="item.amount" @input="item.amount = formatCurrency($el.value)" class="min-w-0 flex-1 border-0 px-3 text-sm outline-none" placeholder="0"></span></label><label class="mt-3 block"><span class="mb-1 block text-xs font-bold text-slate-600">Jatuh tempo</span><input data-due-date type="date" :name="`custom_tanggal_jatuh_tempo[${item.id}]`" x-model="item.date" min="{{ $tagihanDateMin }}" max="{{ $tagihanDateMax }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-violet-500 focus:ring-2 focus:ring-violet-100"></label></article></template>

                <button type="button" @click="addCustom" class="flex min-h-48 flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-300 p-4 text-center text-sm font-bold text-slate-600 hover:border-brand-300 hover:bg-brand-50 hover:text-brand-700"><span class="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100"><i class="fas fa-plus"></i></span>Tambah jenis tagihan custom</button>
            </div>
            @error('tagihan')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
            @error('tagihan.*')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
            @error('tanggal_jatuh_tempo.*')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
        </section>

        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900"><div class="flex items-start gap-3"><i class="fas fa-triangle-exclamation mt-1 text-amber-700"></i><div><strong>Periksa sebelum menyimpan</strong><ul class="mt-1 list-disc space-y-1 pl-5"><li>Tagihan akan dibuat untuk seluruh siswa aktif dalam kelas terpilih.</li><li>Data masuk ke tahun ajaran {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}.</li><li>Gunakan Generate SPP untuk tagihan bulanan agar periode tidak terlewat.</li></ul></div></div></section>

        <footer class="flex flex-col-reverse gap-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5"><p class="text-xs text-slate-500"><strong x-text="selected.length"></strong> kelas penerima dipilih.</p><div class="flex gap-2"><a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-check"></i>Buat tagihan</button></div></footer>
    </form>
</div>
@endsection
