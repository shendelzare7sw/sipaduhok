@extends('layouts.app')

@section('title', 'Tambah Tagihan Custom')
@section('page-title', 'Tambah Tagihan Custom')
@section('page-subtitle', 'Tambahkan satu tagihan khusus kepada beberapa siswa')

@section('content')
@php
    $studentOptions = $siswaList->map(fn ($siswa) => [
        'id' => (string) $siswa->id,
        'name' => $siswa->nama_lengkap,
        'nisn' => $siswa->nisn ?: '-',
        'nis' => $siswa->nis ?: '-',
        'classId' => (string) ($siswa->kelas_id ?? ''),
        'class' => $siswa->kelas->nama_kelas ?? 'Belum ada kelas',
        'level' => $siswa->kelas->jenjang ?? '-',
        'branchId' => (string) ($siswa->cabang_id ?? ''),
        'branch' => $siswa->cabang->nama_cabang ?? 'Cabang belum diatur',
    ])->values();
    $classOptions = $kelasList->map(fn ($kelas) => [
        'id' => (string) $kelas->id,
        'name' => $kelas->nama_kelas,
        'level' => $kelas->jenjang,
        'branchId' => (string) $kelas->cabang_id,
        'branch' => $kelas->cabang->nama_cabang ?? 'Cabang belum diatur',
    ])->values();
@endphp

<div
    data-tagihan-create-custom
    class="min-w-0 w-full space-y-5"
    x-data="{
        students: @js($studentOptions),
        classes: @js($classOptions),
        selected: @js(collect(old('siswa_ids', []))->map(fn ($id) => (string) $id)->values()),
        branch: '',
        level: '',
        classFilter: '',
        query: '',
        amount: @js(old('jumlah', '')),
        get visibleClassOptions() { return this.classes.filter(item => (!this.branch || item.branchId === this.branch) && (!this.level || item.level === this.level)); },
        get visibleStudents() { const needle = this.query.trim().toLowerCase(); return this.students.filter(item => (!this.branch || item.branchId === this.branch) && (!this.level || item.level === this.level) && (!this.classFilter || item.classId === this.classFilter) && (!needle || `${item.name} ${item.nisn} ${item.nis} ${item.class} ${item.branch}`.toLowerCase().includes(needle))); },
        get allVisibleSelected() { return this.visibleStudents.length > 0 && this.visibleStudents.every(item => this.selected.includes(item.id)); },
        toggleVisible() { const ids = this.visibleStudents.map(item => item.id); this.selected = this.allVisibleSelected ? this.selected.filter(id => !ids.includes(id)) : [...new Set([...this.selected, ...ids])]; },
        resetClass() { this.classFilter = ''; },
        formatCurrency(value) { const digits = String(value ?? '').replace(/\D/g, '').replace(/^0+/, '') || ''; return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
        escape(value) { const node = document.createElement('span'); node.textContent = String(value ?? ''); return node.innerHTML; },
        async submitForm(event) {
            if (!this.selected.length) { await Swal.fire({ icon: 'warning', title: 'Pilih siswa dahulu', text: 'Pilih minimal satu siswa penerima tagihan.', confirmButtonColor: '#285dcc' }); return; }
            if (!event.target.checkValidity()) { event.target.reportValidity(); return; }
            const rawAmount = Number(String(this.amount).replace(/\D/g, '')) || 0;
            if (rawAmount <= 0) { await Swal.fire({ icon: 'warning', title: 'Nominal belum valid', text: 'Isi nominal tagihan lebih dari Rp 0.', confirmButtonColor: '#285dcc' }); return; }
            const label = event.target.elements.jenis_tagihan.value.trim();
            const result = await Swal.fire({ icon: 'question', title: 'Simpan tagihan custom?', html: `Tagihan <strong>${this.escape(label)}</strong> senilai <strong>Rp ${this.escape(this.amount)}</strong> akan dibuat untuk <strong>${this.selected.length} siswa</strong>. Data sejenis yang sudah ada akan dilewati.`, showCancelButton: true, confirmButtonText: 'Ya, simpan', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true });
            if (result.isConfirmed) { event.target.elements.jumlah.value = String(this.amount).replace(/\D/g, ''); event.target.submit(); }
        }
    }"
>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left"></i>Kembali ke tagihan</a>
        <span class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700"><i class="fas fa-calendar-check"></i>{{ $tahunAjaran->nama_tahun_ajaran }}</span>
    </div>

    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><div class="flex items-start gap-3 text-sm leading-6 text-blue-900"><i class="fas fa-circle-info mt-1 text-blue-700"></i><p><strong>Alur kerja:</strong> isi detail tagihan satu kali, lalu pilih siswa penerima. Gunakan filter cabang dan kelas agar target mudah diperiksa sebelum disimpan.</p></div></section>

    <form method="POST" action="{{ route('admin.keuangan.tagihan.store-custom') }}" class="space-y-5" @submit.prevent="submitForm($event)">
        @csrf

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fas fa-file-invoice-dollar"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">1. Isi detail tagihan</h2><p class="mt-1 text-sm text-slate-500">Detail yang sama akan diterapkan kepada semua siswa terpilih.</p></header>
            <div class="grid gap-4 p-4 md:grid-cols-2 xl:grid-cols-4 sm:p-5">
                <label class="block"><span class="mb-2 block text-sm font-bold text-slate-800">Jenis tagihan <span class="text-red-600">*</span></span><input type="text" name="jenis_tagihan" value="{{ old('jenis_tagihan') }}" maxlength="255" required placeholder="Contoh: Kegiatan studi wisata" class="h-12 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">@error('jenis_tagihan')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="mb-2 block text-sm font-bold text-slate-800">Nominal <span class="text-red-600">*</span></span><span class="flex h-12 overflow-hidden rounded-xl border border-slate-300 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100"><span class="flex items-center border-r border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-500">Rp</span><input type="text" inputmode="numeric" name="jumlah" x-model="amount" @input="amount = formatCurrency($el.value)" required placeholder="0" class="min-w-0 flex-1 border-0 px-3 text-sm outline-none"></span>@error('jumlah')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="mb-2 block text-sm font-bold text-slate-800">Jatuh tempo <span class="text-red-600">*</span></span><input type="date" name="tanggal_jatuh_tempo" value="{{ old('tanggal_jatuh_tempo', $defaultDueDate) }}" min="{{ $tagihanDateMin }}" max="{{ $tagihanDateMax }}" required class="h-12 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">@error('tanggal_jatuh_tempo')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                <label class="block"><span class="mb-2 block text-sm font-bold text-slate-800">Keterangan</span><input type="text" name="keterangan" value="{{ old('keterangan') }}" placeholder="Catatan opsional" class="h-12 w-full rounded-xl border border-slate-300 px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">@error('keterangan')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
            </div>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"><div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-users"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">2. Pilih siswa penerima</h2><p class="mt-1 text-sm text-slate-500"><strong class="text-brand-700" x-text="selected.length"></strong> siswa dipilih dari <span x-text="visibleStudents.length"></span> hasil yang terlihat.</p></div><span class="inline-flex min-h-9 items-center gap-2 self-start rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-600"><i class="fas fa-user-check"></i>{{ $siswaList->count() }} siswa aktif</span></div></header>

            <div class="grid gap-3 border-b border-slate-100 bg-slate-50 p-4 sm:grid-cols-2 xl:grid-cols-4 sm:p-5">
                <label><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select x-model="branch" @change="resetClass" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua cabang</option>@foreach($cabangList as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select></label>
                <label><span class="mb-1 block text-xs font-bold text-slate-600">Jenjang</span><select x-model="level" @change="resetClass" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua jenjang</option>@foreach($kelasList->pluck('jenjang')->unique()->sort() as $jenjang)<option value="{{ $jenjang }}">{{ $jenjang }}</option>@endforeach</select></label>
                <label><span class="mb-1 block text-xs font-bold text-slate-600">Kelas</span><select x-model="classFilter" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua kelas</option><template x-for="item in visibleClassOptions" :key="item.id"><option :value="item.id" x-text="`${item.name} · ${item.level} · ${item.branch}`"></option></template></select></label>
                <label><span class="mb-1 block text-xs font-bold text-slate-600">Cari siswa</span><span class="relative block"><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" x-model="query" placeholder="Nama, NIS, atau NISN..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span></label>
            </div>

            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5"><button type="button" @click="toggleVisible" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-check-double"></i><span x-text="allVisibleSelected ? 'Batalkan hasil terlihat' : 'Pilih semua hasil terlihat'"></span></button><button type="button" @click="selected = []" :disabled="!selected.length" class="min-h-9 rounded-xl px-3 text-xs font-bold text-slate-600 hover:bg-slate-100 disabled:opacity-40">Kosongkan pilihan</button></div>

            <div class="grid max-h-[34rem] gap-3 overflow-y-auto p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-5">
                <template x-for="student in visibleStudents" :key="student.id"><label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 transition" :class="selected.includes(student.id) ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 hover:border-brand-200 hover:bg-slate-50'"><input type="checkbox" name="siswa_ids[]" :value="student.id" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="min-w-0"><strong class="block truncate text-sm text-slate-950" x-text="student.name"></strong><span class="mt-1 block truncate text-xs text-slate-500" x-text="`NISN ${student.nisn} · ${student.class} ${student.level}`"></span><span class="mt-1 block truncate text-[10px] font-semibold text-slate-400" x-text="student.branch"></span></span></label></template>
                <div x-show="!visibleStudents.length" class="col-span-full py-12 text-center text-sm text-slate-500">Tidak ada siswa yang cocok dengan filter.</div>
            </div>
            @error('siswa_ids')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
            @error('siswa_ids.*')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
        </section>

        <footer class="flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5"><p class="text-xs leading-5 text-slate-500">Tagihan dibuat untuk <strong class="text-slate-800" x-text="selected.length"></strong> siswa pada periode {{ $tahunAjaran->nama_tahun_ajaran }}.</p><div class="flex gap-2"><a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-check"></i>Simpan tagihan</button></div></footer>
    </form>
</div>
@endsection
