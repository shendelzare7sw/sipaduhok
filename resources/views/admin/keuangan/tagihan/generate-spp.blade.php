@extends('layouts.app')

@section('title', 'Generate SPP Bulanan')
@section('page-title', 'Generate SPP Bulanan')
@section('page-subtitle', 'Buat rangkaian tagihan SPP sesuai periode belajar')

@section('content')
@php
    $classOptions = $kelasList->map(fn ($kelas) => [
        'id' => (string) $kelas->id,
        'name' => $kelas->nama_kelas,
        'level' => $kelas->jenjang,
        'branchId' => (string) $kelas->cabang_id,
        'branch' => $kelas->cabang->nama_cabang ?? 'Cabang belum diatur',
    ])->values();
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
    $monthOptions = collect($sppMonths)->values();
    $firstMonth = $monthOptions->first()['value'] ?? '';
@endphp

<div
    data-tagihan-generate-spp
    class="min-w-0 w-full space-y-5"
    x-data="{
        classes: @js($classOptions),
        students: @js($studentOptions),
        months: @js($monthOptions),
        targetType: @js(old('target_type', 'kelas')),
        sppType: @js(old('tipe_spp', 'setahun')),
        selectedClasses: @js(collect(old('kelas_ids', []))->map(fn ($id) => (string) $id)->values()),
        selectedStudents: @js(collect(old('siswa_ids', []))->map(fn ($id) => (string) $id)->values()),
        classBranch: '',
        classLevel: '',
        studentBranch: '',
        studentLevel: '',
        studentClass: '',
        query: '',
        startMonth: @js(old('bulan_mulai', $firstMonth)),
        monthCount: Number(@js(old('jumlah_bulan', 1))),
        amount: @js(old('jumlah_spp', '')),
        get visibleClasses() { return this.classes.filter(item => (!this.classBranch || item.branchId === this.classBranch) && (!this.classLevel || item.level === this.classLevel)); },
        get visibleClassOptions() { return this.classes.filter(item => (!this.studentBranch || item.branchId === this.studentBranch) && (!this.studentLevel || item.level === this.studentLevel)); },
        get visibleStudents() { const needle = this.query.trim().toLowerCase(); return this.students.filter(item => (!this.studentBranch || item.branchId === this.studentBranch) && (!this.studentLevel || item.level === this.studentLevel) && (!this.studentClass || item.classId === this.studentClass) && (!needle || `${item.name} ${item.nisn} ${item.nis} ${item.class} ${item.branch}`.toLowerCase().includes(needle))); },
        get allVisibleClassesSelected() { return this.visibleClasses.length > 0 && this.visibleClasses.every(item => this.selectedClasses.includes(item.id)); },
        get allVisibleStudentsSelected() { return this.visibleStudents.length > 0 && this.visibleStudents.every(item => this.selectedStudents.includes(item.id)); },
        get startIndex() { const index = this.months.findIndex(item => item.value === this.startMonth); return Math.max(0, index); },
        get remainingMonths() { return Math.max(1, this.months.length - this.startIndex); },
        get effectiveMonthCount() { return this.sppType === 'setahun' ? this.months.length : Math.min(Number(this.monthCount) || 1, this.remainingMonths); },
        get endMonthLabel() { return this.months[Math.min(this.months.length - 1, this.startIndex + this.effectiveMonthCount - 1)]?.label || '-'; },
        get selectedTargetCount() { return this.targetType === 'kelas' ? this.selectedClasses.length : this.selectedStudents.length; },
        toggleVisibleClasses() { const ids = this.visibleClasses.map(item => item.id); this.selectedClasses = this.allVisibleClassesSelected ? this.selectedClasses.filter(id => !ids.includes(id)) : [...new Set([...this.selectedClasses, ...ids])]; },
        toggleVisibleStudents() { const ids = this.visibleStudents.map(item => item.id); this.selectedStudents = this.allVisibleStudentsSelected ? this.selectedStudents.filter(id => !ids.includes(id)) : [...new Set([...this.selectedStudents, ...ids])]; },
        resetStudentClass() { this.studentClass = ''; },
        normalizeRange() { if (this.sppType === 'setahun') { this.startMonth = this.months[0]?.value || ''; } if (this.monthCount > this.remainingMonths) this.monthCount = this.remainingMonths; },
        formatCurrency(value) { const digits = String(value ?? '').replace(/\D/g, '').replace(/^0+/, '') || ''; return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.'); },
        async submitForm(event) {
            if (!this.selectedTargetCount) { await Swal.fire({ icon: 'warning', title: this.targetType === 'kelas' ? 'Pilih kelas dahulu' : 'Pilih siswa dahulu', text: `Pilih minimal satu ${this.targetType} penerima SPP.`, confirmButtonColor: '#285dcc' }); return; }
            const rawAmount = Number(String(this.amount).replace(/\D/g, '')) || 0;
            if (rawAmount <= 0) { await Swal.fire({ icon: 'warning', title: 'Nominal belum valid', text: 'Isi nominal SPP per bulan lebih dari Rp 0.', confirmButtonColor: '#285dcc' }); return; }
            if (!event.target.checkValidity()) { event.target.reportValidity(); return; }
            const targetLabel = `${this.selectedTargetCount} ${this.targetType}`;
            const result = await Swal.fire({ icon: 'question', title: 'Generate SPP?', html: `<strong>${this.effectiveMonthCount} tagihan bulanan</strong> senilai <strong>Rp ${this.amount}</strong> akan diproses untuk <strong>${targetLabel}</strong>.<br><span class='mt-2 block text-sm text-slate-500'>Periode ${this.months[this.startIndex]?.label || '-'} sampai ${this.endMonthLabel}. Data massal yang sudah ada akan dilewati.</span>`, showCancelButton: true, confirmButtonText: 'Ya, generate SPP', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true });
            if (result.isConfirmed) { event.target.elements.jumlah_spp.value = String(this.amount).replace(/\D/g, ''); event.target.submit(); }
        }
    }"
    x-init="normalizeRange()"
>
    <div class="flex flex-wrap items-center justify-between gap-3"><a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left"></i>Kembali ke tagihan</a><span class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700"><i class="fas fa-calendar-check"></i>{{ $tahunAjaran->nama_tahun_ajaran }}</span></div>

    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><div class="flex items-start gap-3 text-sm leading-6 text-blue-900"><i class="fas fa-circle-info mt-1 text-blue-700"></i><p><strong>Alur kerja:</strong> tentukan penerima berdasarkan kelas atau siswa, pilih rentang bulan, lalu isi nominal SPP. Sistem memakai urutan bulan resmi tahun ajaran agar tidak melewati periode.</p></div></section>

    <form method="POST" action="{{ route('admin.keuangan.tagihan.generate-spp.store') }}" class="space-y-5" @submit.prevent="submitForm($event)">
        @csrf

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between"><div><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-users"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">1. Tentukan penerima SPP</h2><p class="mt-1 text-sm text-slate-500">Gunakan kelas untuk proses rutin; gunakan siswa untuk kebutuhan khusus.</p></div><div class="grid grid-cols-2 gap-2 sm:w-80"><label class="cursor-pointer rounded-xl border p-3" :class="targetType === 'kelas' ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200'"><input type="radio" name="target_type" value="kelas" x-model="targetType" class="sr-only"><strong class="block text-sm text-slate-950"><i class="fas fa-school mr-1 text-brand-600"></i>Per kelas</strong><span class="mt-1 block text-[10px] text-slate-500">Untuk banyak siswa</span></label><label class="cursor-pointer rounded-xl border p-3" :class="targetType === 'siswa' ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200'"><input type="radio" name="target_type" value="siswa" x-model="targetType" class="sr-only"><strong class="block text-sm text-slate-950"><i class="fas fa-user mr-1 text-brand-600"></i>Per siswa</strong><span class="mt-1 block text-[10px] text-slate-500">Untuk target khusus</span></label></div></div></header>

            <div x-show="targetType === 'kelas'">
                <div class="grid gap-3 border-b border-slate-100 bg-slate-50 p-4 sm:grid-cols-2 sm:p-5"><label><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select x-model="classBranch" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua cabang</option>@foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select></label><label><span class="mb-1 block text-xs font-bold text-slate-600">Jenjang</span><select x-model="classLevel" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua jenjang</option>@foreach($kelasList->pluck('jenjang')->unique()->sort() as $jenjang)<option value="{{ $jenjang }}">{{ $jenjang }}</option>@endforeach</select></label></div>
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5"><button type="button" @click="toggleVisibleClasses" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-check-double"></i><span x-text="allVisibleClassesSelected ? 'Batalkan hasil terlihat' : 'Pilih semua hasil terlihat'"></span></button><span class="text-xs font-semibold text-slate-500"><strong class="text-slate-900" x-text="selectedClasses.length"></strong> kelas dipilih</span></div>
                <div class="grid max-h-[28rem] gap-3 overflow-y-auto p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-5"><template x-for="item in visibleClasses" :key="item.id"><label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3" :class="selectedClasses.includes(item.id) ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 hover:border-brand-200 hover:bg-slate-50'"><input type="checkbox" name="kelas_ids[]" :value="item.id" x-model="selectedClasses" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="min-w-0"><strong class="block truncate text-sm text-slate-950" x-text="item.name"></strong><span class="mt-1 block truncate text-xs text-slate-500" x-text="`${item.level} · ${item.branch}`"></span></span></label></template><div x-show="!visibleClasses.length" class="col-span-full py-10 text-center text-sm text-slate-500">Tidak ada kelas yang cocok.</div></div>
            </div>

            <div x-cloak x-show="targetType === 'siswa'">
                <div class="grid gap-3 border-b border-slate-100 bg-slate-50 p-4 sm:grid-cols-2 xl:grid-cols-4 sm:p-5"><label><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select x-model="studentBranch" @change="resetStudentClass" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua cabang</option>@foreach($kelasList->pluck('cabang')->unique('id')->filter() as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama_cabang }}</option>@endforeach</select></label><label><span class="mb-1 block text-xs font-bold text-slate-600">Jenjang</span><select x-model="studentLevel" @change="resetStudentClass" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua jenjang</option>@foreach($kelasList->pluck('jenjang')->unique()->sort() as $jenjang)<option value="{{ $jenjang }}">{{ $jenjang }}</option>@endforeach</select></label><label><span class="mb-1 block text-xs font-bold text-slate-600">Kelas</span><select x-model="studentClass" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua kelas</option><template x-for="item in visibleClassOptions" :key="item.id"><option :value="item.id" x-text="`${item.name} · ${item.level} · ${item.branch}`"></option></template></select></label><label><span class="mb-1 block text-xs font-bold text-slate-600">Cari siswa</span><span class="relative block"><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i><input type="search" x-model="query" placeholder="Nama, NIS, atau NISN..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"></span></label></div>
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3 sm:px-5"><button type="button" @click="toggleVisibleStudents" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-brand-50 px-3 text-xs font-bold text-brand-700 hover:bg-brand-100"><i class="fas fa-check-double"></i><span x-text="allVisibleStudentsSelected ? 'Batalkan hasil terlihat' : 'Pilih semua hasil terlihat'"></span></button><span class="text-xs font-semibold text-slate-500"><strong class="text-slate-900" x-text="selectedStudents.length"></strong> siswa dipilih</span></div>
                <div class="grid max-h-[32rem] gap-3 overflow-y-auto p-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 sm:p-5"><template x-for="student in visibleStudents" :key="student.id"><label class="flex cursor-pointer items-start gap-3 rounded-xl border p-3" :class="selectedStudents.includes(student.id) ? 'border-brand-400 bg-brand-50 ring-1 ring-brand-200' : 'border-slate-200 hover:border-brand-200 hover:bg-slate-50'"><input type="checkbox" name="siswa_ids[]" :value="student.id" x-model="selectedStudents" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="min-w-0"><strong class="block truncate text-sm text-slate-950" x-text="student.name"></strong><span class="mt-1 block truncate text-xs text-slate-500" x-text="`NISN ${student.nisn} · ${student.class} ${student.level}`"></span><span class="mt-1 block truncate text-[10px] font-semibold text-slate-400" x-text="student.branch"></span></span></label></template><div x-show="!visibleStudents.length" class="col-span-full py-10 text-center text-sm text-slate-500">Tidak ada siswa yang cocok.</div></div>
            </div>

            @error('kelas_ids')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
            @error('siswa_ids')<div class="border-t border-red-100 bg-red-50 px-5 py-3 text-xs font-semibold text-red-700">{{ $message }}</div>@enderror
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700"><i class="fas fa-calendar-days"></i></span><h2 class="mt-3 text-lg font-extrabold text-slate-950">2. Atur periode dan nominal</h2><p class="mt-1 text-sm text-slate-500">Pilih satu tahun penuh atau rentang bulan tertentu.</p></header>
            <div class="grid gap-5 p-4 lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,.75fr)] sm:p-5">
                <div class="space-y-4">
                    <div class="grid gap-3 sm:grid-cols-2"><label class="cursor-pointer rounded-xl border p-4" :class="sppType === 'setahun' ? 'border-emerald-400 bg-emerald-50 ring-1 ring-emerald-200' : 'border-slate-200'"><input type="radio" name="tipe_spp" value="setahun" x-model="sppType" @change="normalizeRange" class="sr-only"><strong class="block text-sm text-slate-950"><i class="fas fa-calendar-check mr-1 text-emerald-600"></i>SPP setahun</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Buat seluruh {{ count($sppMonths) }} bulan dalam periode aktif.</span></label><label class="cursor-pointer rounded-xl border p-4" :class="sppType === 'sebagian' ? 'border-amber-400 bg-amber-50 ring-1 ring-amber-200' : 'border-slate-200'"><input type="radio" name="tipe_spp" value="sebagian" x-model="sppType" @change="normalizeRange" class="sr-only"><strong class="block text-sm text-slate-950"><i class="fas fa-calendar-alt mr-1 text-amber-600"></i>SPP sebagian</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Cocok untuk siswa baru atau periode susulan.</span></label></div>
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4"><label><span class="mb-2 block text-sm font-bold text-slate-800">Bulan mulai</span><select name="bulan_mulai" x-model="startMonth" @change="normalizeRange" required class="h-12 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">@foreach($sppMonths as $month)<option value="{{ $month['value'] }}">{{ $month['label'] }}</option>@endforeach</select><span x-show="sppType === 'setahun'" class="mt-1 block text-[10px] text-slate-500">Setahun selalu dimulai dari bulan pertama.</span></label><label x-show="sppType === 'sebagian'"><span class="mb-2 block text-sm font-bold text-slate-800">Jumlah bulan</span><select name="jumlah_bulan" x-model.number="monthCount" required class="h-12 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><template x-for="count in remainingMonths" :key="count"><option :value="count" x-text="`${count} bulan`"></option></template></select></label><label><span class="mb-2 block text-sm font-bold text-slate-800">SPP per bulan</span><span class="flex h-12 overflow-hidden rounded-xl border border-slate-300 focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100"><span class="flex items-center border-r border-slate-200 bg-slate-50 px-3 text-sm font-bold text-slate-500">Rp</span><input type="text" inputmode="numeric" name="jumlah_spp" x-model="amount" @input="amount = formatCurrency($el.value)" required placeholder="0" class="min-w-0 flex-1 border-0 px-3 text-sm outline-none"></span></label><label><span class="mb-2 block text-sm font-bold text-slate-800">Tanggal jatuh tempo</span><select name="tanggal_jatuh_tempo" required class="h-12 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">@for($day = 1; $day <= 31; $day++)<option value="{{ $day }}" @selected(old('tanggal_jatuh_tempo', 10) == $day)>Tanggal {{ $day }}</option>@endfor</select></label></div>
                    @error('bulan_mulai')<span class="block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror @error('jumlah_bulan')<span class="block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror @error('jumlah_spp')<span class="block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror @error('tanggal_jatuh_tempo')<span class="block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror
                </div>
                <aside class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><span class="text-xs font-bold uppercase tracking-wide text-amber-700">Ringkasan proses</span><strong class="mt-3 block text-2xl text-slate-950"><span x-text="effectiveMonthCount"></span> bulan</strong><p class="mt-1 text-sm text-slate-600"><span x-text="months[startIndex]?.label || '-'"></span> — <span x-text="endMonthLabel"></span></p><div class="my-4 border-t border-amber-200"></div><dl class="space-y-3 text-sm"><div class="flex justify-between gap-3"><dt class="text-slate-600">Penerima dipilih</dt><dd class="font-extrabold text-slate-950" x-text="`${selectedTargetCount} ${targetType}`"></dd></div><div class="flex justify-between gap-3"><dt class="text-slate-600">Nominal per bulan</dt><dd class="font-extrabold tabular-nums text-slate-950" x-text="`Rp ${amount || '0'}`"></dd></div></dl></aside>
            </div>
        </section>

        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm leading-6 text-amber-900"><div class="flex items-start gap-3"><i class="fas fa-triangle-exclamation mt-1 text-amber-700"></i><div><strong>Periksa sebelum generate</strong><ul class="mt-1 list-disc space-y-1 pl-5"><li>Proses massal melewati SPP yang sudah ada agar riwayat pembayaran aman.</li><li>Untuk satu siswa, data bulan yang sudah ada dapat diperbarui oleh sistem.</li><li>Jatuh tempo disesuaikan otomatis bila bulan tidak memiliki tanggal yang dipilih.</li></ul></div></div></section>

        <footer class="flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5"><p class="text-xs leading-5 text-slate-500">Tujuan tahun ajaran <strong class="text-slate-800">{{ $tahunAjaran->nama_tahun_ajaran }}</strong>.</p><div class="flex gap-2"><a href="{{ route('admin.keuangan.tagihan.index') }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Batal</a><button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-calendar-check"></i>Generate SPP</button></div></footer>
    </form>
</div>
@endsection
