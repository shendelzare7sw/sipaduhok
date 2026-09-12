@extends('layouts.app')

@section('title', 'Duplikasi Tagihan')
@section('page-title', 'Duplikasi Tagihan')
@section('page-subtitle', 'Salin susunan tagihan satu siswa ke siswa lain')

@section('content')
@php
    $tagihanRoute = request()->routeIs('admin.*') ? 'admin.keuangan.tagihan' : 'bendahara.tagihan';
    $students = $siswaList->map(fn ($siswa) => [
        'id' => (string) $siswa->id,
        'name' => $siswa->nama_lengkap,
        'nisn' => $siswa->nisn ?: '-',
        'classId' => (string) ($siswa->kelas_id ?? ''),
        'class' => $siswa->kelas->nama_kelas ?? 'Belum ada kelas',
        'level' => $siswa->kelas->jenjang ?? '-',
        'branch' => $siswa->cabang->nama_cabang ?? 'Cabang belum diatur',
    ])->values();
@endphp

<div
    data-tagihan-duplicate
    data-preview-url="{{ route($tagihanRoute.'.api.tagihan-preview', ':siswa') }}"
    class="min-w-0 w-full space-y-5"
    x-data="{
        students: @js($students),
        source: @js((string) old('source_siswa_id', '')),
        selected: @js(collect(old('target_siswa_ids', []))->map(fn ($id) => (string) $id)->values()),
        classFilter: '',
        query: '',
        replace: @js((string) old('replace_existing', '1')),
        preview: [],
        previewState: 'idle',
        get sourceStudent() { return this.students.find(student => student.id === this.source); },
        get visibleStudents() { const needle = this.query.trim().toLowerCase(); return this.students.filter(student => student.id !== this.source && (!this.classFilter || student.classId === this.classFilter) && (!needle || `${student.name} ${student.nisn} ${student.class} ${student.branch}`.toLowerCase().includes(needle))); },
        get allVisibleSelected() { return this.visibleStudents.length > 0 && this.visibleStudents.every(student => this.selected.includes(student.id)); },
        toggleVisible() { const ids = this.visibleStudents.map(student => student.id); this.selected = this.allVisibleSelected ? this.selected.filter(id => !ids.includes(id)) : [...new Set([...this.selected, ...ids])]; },
        async loadPreview() {
            this.selected = this.selected.filter(id => id !== this.source);
            if (!this.source) { this.preview = []; this.previewState = 'idle'; return; }
            this.previewState = 'loading';
            try { const response = await fetch(this.$root.dataset.previewUrl.replace(':siswa', this.source), { headers: { Accept: 'application/json' } }); if (!response.ok) throw new Error(); this.preview = await response.json(); this.previewState = this.preview.length ? 'ready' : 'empty'; }
            catch (_) { this.preview = []; this.previewState = 'error'; }
        },
        format(value) { return new Intl.NumberFormat('id-ID').format(Number(value || 0)); },
        async submitDuplicate(event) {
            if (!this.source) { await Swal.fire({ icon: 'warning', title: 'Pilih siswa sumber', text: 'Tentukan siswa yang tagihannya akan disalin.', confirmButtonColor: '#285dcc' }); return; }
            if (!this.selected.length) { await Swal.fire({ icon: 'warning', title: 'Pilih siswa target', text: 'Pilih minimal satu siswa penerima tagihan.', confirmButtonColor: '#285dcc' }); return; }
            if (this.previewState !== 'ready') { await Swal.fire({ icon: 'warning', title: 'Sumber belum memiliki tagihan', text: 'Pilih siswa sumber yang memiliki tagihan pada tahun ajaran aktif.', confirmButtonColor: '#285dcc' }); return; }
            const result = await Swal.fire({ icon: 'question', title: 'Duplikasi tagihan?', html: `Semua tagihan <strong>${this.sourceStudent?.name || ''}</strong> akan disalin ke <strong>${this.selected.length} siswa</strong>.<br><span class='text-sm'>Data yang sudah ada akan ${this.replace === '1' ? 'diperbarui' : 'dilewati'}.</span>`, showCancelButton: true, confirmButtonText: 'Ya, duplikasi', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true });
            if (result.isConfirmed) event.target.submit();
        }
    }"
    x-init="source && loadPreview()"
>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route($tagihanRoute.'.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left"></i>Kembali ke tagihan</a>
        <span class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-blue-50 px-3 text-xs font-bold text-blue-700"><i class="fas fa-calendar-check"></i>Tahun ajaran {{ $tahunAjaran->nama_tahun_ajaran }}</span>
    </div>

    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><div class="flex items-start gap-3"><i class="fas fa-circle-info mt-1 text-blue-700"></i><p class="text-sm leading-6 text-blue-900"><strong>Alur kerja:</strong> pilih satu siswa sumber, periksa susunan tagihannya, lalu pilih siswa target. Sumber tidak dapat dipilih sebagai target.</p></div></section>

    <form action="{{ route($tagihanRoute.'.duplicate.store') }}" method="POST" class="space-y-5" @submit.prevent="submitDuplicate($event)">
        @csrf

        <div class="grid gap-5 xl:grid-cols-[minmax(18rem,.8fr)_minmax(0,1.2fr)]">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 p-4 sm:p-5"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-user"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">1. Pilih siswa sumber</h2><p class="mt-1 text-sm text-slate-500">Tagihan pada periode aktif akan dijadikan pola.</p></header>
                <div class="p-4 sm:p-5">
                    <label class="block"><span class="mb-2 block text-sm font-bold text-slate-800">Siswa sumber <span class="text-red-600">*</span></span><select name="source_siswa_id" x-model="source" @change="loadPreview" required class="h-12 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Pilih siswa sumber</option>@foreach($siswaList as $siswa)<option value="{{ $siswa->id }}">{{ $siswa->nama_lengkap }} · {{ $siswa->nisn ?: '-' }} · {{ $siswa->kelas->nama_kelas ?? '-' }} · {{ $siswa->cabang->nama_cabang ?? '-' }}</option>@endforeach</select>@error('source_siswa_id')<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                    <template x-if="sourceStudent"><div class="mt-4 rounded-xl bg-slate-50 p-3"><strong class="block text-sm text-slate-950" x-text="sourceStudent.name"></strong><span class="mt-1 block text-xs text-slate-500" x-text="`${sourceStudent.nisn} · ${sourceStudent.class} ${sourceStudent.level} · ${sourceStudent.branch}`"></span></div></template>
                </div>
            </section>

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="border-b border-slate-200 p-4 sm:p-5"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-cyan-50 text-cyan-700"><i class="fas fa-list-check"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">2. Periksa tagihan sumber</h2><p class="mt-1 text-sm text-slate-500">Pastikan nominal dan jenis tagihan sudah sesuai sebelum disalin.</p></header>
                <div class="min-h-44 p-4 sm:p-5">
                    <div x-show="previewState === 'idle'" class="flex min-h-32 flex-col items-center justify-center text-center text-sm text-slate-500"><i class="fas fa-arrow-left mb-3 text-xl text-slate-300"></i>Pilih siswa sumber untuk memuat tagihan.</div>
                    <div x-cloak x-show="previewState === 'loading'" class="flex min-h-32 items-center justify-center gap-2 text-sm font-semibold text-brand-700"><i class="fas fa-spinner fa-spin"></i>Memuat tagihan...</div>
                    <div x-cloak x-show="previewState === 'empty'" class="flex min-h-32 flex-col items-center justify-center text-center text-sm text-amber-700"><i class="fas fa-inbox mb-3 text-xl"></i>Siswa ini belum memiliki tagihan pada periode aktif.</div>
                    <div x-cloak x-show="previewState === 'error'" class="flex min-h-32 flex-col items-center justify-center text-center text-sm text-red-700"><i class="fas fa-triangle-exclamation mb-3 text-xl"></i>Pratinjau gagal dimuat. Coba pilih ulang siswa.</div>
                    <div x-cloak x-show="previewState === 'ready'" class="divide-y divide-slate-100 rounded-xl border border-slate-200">
                        <template x-for="item in preview" :key="item.id"><div class="flex items-center justify-between gap-3 p-3"><div class="min-w-0"><strong class="block truncate text-sm capitalize text-slate-900" x-text="String(item.jenis_tagihan || '').replaceAll('_', ' ')"></strong><span class="text-xs text-slate-500" x-text="item.status === 'sudah_bayar' ? 'Lunas' : 'Belum bayar'"></span></div><strong class="shrink-0 text-sm tabular-nums text-slate-950" x-text="`Rp ${format(item.jumlah)}`"></strong></div></template>
                    </div>
                </div>
            </section>
        </div>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"><div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-violet-50 text-violet-700"><i class="fas fa-users"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">3. Pilih siswa target</h2><p class="mt-1 text-sm text-slate-500"><span x-text="selected.length"></span> siswa dipilih dari <span x-text="visibleStudents.length"></span> hasil yang terlihat.</p></div><div class="grid gap-2 sm:grid-cols-2 lg:w-[34rem]"><label class="relative"><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" x-model="query" placeholder="Cari nama, NISN, kelas..." class="h-11 w-full rounded-xl border border-slate-300 !pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></label><select x-model="classFilter" @change="selected = selected.filter(id => students.find(student => student.id === id && (!classFilter || student.classId === classFilter)))" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}">{{ $kelas->nama_kelas }} · {{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? '-' }}</option>@endforeach</select></div></div></header>

            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-5"><button type="button" @click="toggleVisible" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-white px-3 text-xs font-bold text-brand-700 ring-1 ring-slate-200 hover:bg-brand-50"><i class="fas fa-check-double"></i><span x-text="allVisibleSelected ? 'Batalkan hasil terlihat' : 'Pilih semua hasil terlihat'"></span></button><button type="button" @click="selected = []" :disabled="!selected.length" class="min-h-9 rounded-xl px-3 text-xs font-bold text-slate-600 hover:bg-slate-200 disabled:opacity-40">Kosongkan pilihan</button></div>

            <div class="max-h-[28rem] overflow-y-auto p-4 sm:p-5">
                <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-3">
                    <template x-for="student in visibleStudents" :key="student.id">
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3 transition" :class="selected.includes(student.id) ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 hover:border-brand-200 hover:bg-slate-50'"><input type="checkbox" name="target_siswa_ids[]" :value="student.id" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="min-w-0"><strong class="block truncate text-sm text-slate-950" x-text="student.name"></strong><span class="mt-1 block truncate text-xs text-slate-500" x-text="`${student.nisn} · ${student.class} ${student.level}`"></span><span class="mt-1 block truncate text-[10px] font-semibold text-slate-400" x-text="student.branch"></span></span></label>
                    </template>
                </div>
                <div x-show="!visibleStudents.length" class="py-12 text-center text-sm text-slate-500">Tidak ada siswa yang cocok dengan filter.</div>
            </div>
            @error('target_siswa_ids')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
        </section>

        <section class="grid gap-3 sm:grid-cols-2">
            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4" :class="replace === '1' ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 bg-white'"><input type="radio" name="replace_existing" value="1" x-model="replace" class="mt-1 h-4 w-4 border-slate-300 text-brand-600"><span><strong class="block text-sm text-slate-950">Perbarui yang sudah ada</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Jenis tagihan yang sama akan mengikuti nominal sumber. Status dihitung ulang oleh sistem.</span></span></label>
            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border p-4" :class="replace === '0' ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 bg-white'"><input type="radio" name="replace_existing" value="0" x-model="replace" class="mt-1 h-4 w-4 border-slate-300 text-brand-600"><span><strong class="block text-sm text-slate-950">Lewati yang sudah ada</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Hanya jenis tagihan yang belum dimiliki siswa target yang dibuat.</span></span></label>
        </section>

        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900"><div class="flex items-start gap-3"><i class="fas fa-triangle-exclamation mt-1 text-amber-700"></i><div><strong>Periksa sebelum melanjutkan</strong><ul class="mt-1 list-disc space-y-1 pl-5"><li>Seluruh jenis tagihan sumber akan diproses.</li><li>Tagihan baru dimulai dengan status belum bayar.</li><li>Pilihan Perbarui tidak menghapus riwayat pembayaran yang sudah ada.</li></ul></div></div></section>

        <footer class="flex flex-col-reverse gap-2 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5"><p class="text-xs text-slate-500">Periode tujuan: <strong class="text-slate-800">{{ $tahunAjaran->nama_tahun_ajaran }}</strong></p><div class="flex gap-2"><a href="{{ route($tagihanRoute.'.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-copy"></i>Duplikasi</button></div></footer>
    </form>
</div>
@endsection
