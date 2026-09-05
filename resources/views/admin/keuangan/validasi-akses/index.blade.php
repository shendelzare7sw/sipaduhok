@extends('layouts.app')

@section('title', 'Validasi Akses')
@section('page-title', 'Validasi Akses Ujian & Rapor')
@section('page-subtitle', 'Kendalikan akses berdasarkan pembayaran dan persetujuan Ketua PKBM')

@section('content')
@php
    $hasActiveFilter = request()->hasAny(['search', 'cabang_id', 'jenjang', 'kelas_id', 'status_ujian', 'status_rapor']);
    $quickClasses = $quickKelasList ?? $kelasList;
    $studentIds = $siswa->getCollection()->map(fn ($student) => (string) $student->id)->values();
    $raporEligibleIds = $siswa->getCollection()->filter(fn ($student) => $student->validasi_rapor_ketua)->map(fn ($student) => (string) $student->id)->values();
    $inputClass = 'h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $isBendahara = request()->routeIs('bendahara.*');
    $routePrefix = $isBendahara ? 'bendahara.validasi-akses' : 'admin.keuangan.validasi-akses';
    $tagihanRoutePrefix = $isBendahara ? 'bendahara.tagihan' : 'admin.keuangan.tagihan';
    $validatorLabel = $isBendahara ? 'Bendahara' : 'Admin';
@endphp

<div class="min-w-0 w-full space-y-5" data-validasi-akses x-data="{
    selected: [],
    allIds: @js($studentIds),
    raporEligible: @js($raporEligibleIds),
    toggleAll() { this.selected = this.selected.length === this.allIds.length ? [] : [...this.allIds]; },
    selectedEligible() { return this.selected.filter(id => this.raporEligible.includes(id)); },
    async runBulk(type) {
        if (!this.selected.length) {
            await Swal.fire({ icon: 'info', title: 'Pilih siswa dahulu', text: 'Pilih minimal satu siswa untuk melanjutkan.', confirmButtonColor: '#285dcc' });
            return;
        }
        const eligible = type === 'rapor' ? this.selectedEligible() : this.selected;
        if (!eligible.length) {
            await Swal.fire({ icon: 'warning', title: 'Belum dapat divalidasi', text: 'Siswa yang dipilih belum mendapat persetujuan rapor dari Ketua PKBM.', confirmButtonColor: '#285dcc' });
            return;
        }
        const skipped = this.selected.length - eligible.length;
        const result = await Swal.fire({ icon: 'question', title: `Validasi akses ${type}?`, text: `${eligible.length} siswa akan divalidasi${skipped ? `; ${skipped} siswa yang belum disetujui Ketua akan dilewati` : ''}.`, showCancelButton: true, confirmButtonText: 'Ya, validasi', cancelButtonText: 'Batal', confirmButtonColor: '#285dcc', reverseButtons: true });
        if (result.isConfirmed) {
            this.$refs.bulkType.value = type;
            this.$nextTick(() => this.$refs.bulkForm.requestSubmit());
        }
    },
    async openDispensasi() {
        if (!this.selected.length) {
            await Swal.fire({ icon: 'info', title: 'Pilih siswa dahulu', text: 'Centang siswa yang akan diajukan dispensasi.', confirmButtonColor: '#285dcc' });
            return;
        }
        this.$refs.dispensasiDialog.showModal();
    }
}">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-center sm:p-5">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="fas fa-route" aria-hidden="true"></i></span>
            <div class="min-w-0"><h2 class="text-sm font-extrabold text-slate-950">Alur pembukaan akses</h2><p class="mt-1 text-xs text-slate-500">Rapor memerlukan persetujuan Ketua sebelum dapat divalidasi {{ $validatorLabel }}.</p></div>
        </div>
        <ol class="grid border-t border-slate-200 text-xs sm:grid-cols-3">
            @foreach([
                ['1', 'Ketua menyetujui', 'Persetujuan awal khusus akses rapor', 'bg-amber-50 text-amber-700'],
                ['2', $validatorLabel.' memvalidasi', 'Periksa pembayaran atau dispensasi', 'bg-brand-50 text-brand-700'],
                ['3', 'Akses terbuka', 'Siswa dapat membuka ujian atau rapor', 'bg-emerald-50 text-emerald-700'],
            ] as [$number, $title, $description, $tone])
                <li class="flex gap-3 border-slate-200 p-4 sm:border-r sm:last:border-r-0"><span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg font-extrabold {{ $tone }}">{{ $number }}</span><div><p class="font-extrabold text-slate-800">{{ $title }}</p><p class="mt-1 leading-5 text-slate-500">{{ $description }}</p></div></li>
            @endforeach
        </ol>
    </section>

    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach([
            ['Total siswa', $totalSiswa, 'Siswa aktif terdaftar', 'fa-user-graduate', 'bg-brand-50 text-brand-600'],
            ['Ujian valid', $validasiUjian, 'Akses telah divalidasi', 'fa-file-signature', 'bg-emerald-50 text-emerald-600'],
            ['Rapor valid', $validasiRapor, 'Akses telah divalidasi', 'fa-file-invoice', 'bg-violet-50 text-violet-600'],
            ['Belum validasi', $belumValidasi, 'Masih dalam antrean', 'fa-hourglass-half', 'bg-amber-50 text-amber-600'],
        ] as [$label, $value, $description, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate text-xl font-extrabold text-slate-950">{{ $value }}</p><p class="mt-1 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span></div><p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] text-slate-500">{{ $description }}</p></article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-user-shield text-brand-600" aria-hidden="true"></i>Daftar kendali akses siswa</h2><p class="mt-1 text-xs leading-5 text-slate-500">Cari siswa, pilih akses yang perlu dibuka, atau ajukan dispensasi kepada Ketua PKBM.</p></header>

        <form action="{{ route($routePrefix.'.index') }}" method="GET" class="grid gap-2 border-b border-slate-200 p-4 sm:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_repeat(5,minmax(130px,0.45fr))_auto] sm:p-5">
            <label class="relative sm:col-span-2 xl:col-span-1"><span class="sr-only">Cari siswa</span><i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NISN..." class="{{ $inputClass }} !pl-10"></label>
            <select name="cabang_id" class="{{ $inputClass }}" @change="$el.form.submit()"><option value="">Semua cabang</option>@foreach($cabangList as $cabang)<option value="{{ $cabang->id }}" @selected(request('cabang_id') == $cabang->id)>{{ $cabang->nama_cabang }}</option>@endforeach</select>
            <select name="jenjang" class="{{ $inputClass }}" @change="$el.form.submit()"><option value="">Semua jenjang</option>@foreach($jenjangList as $jenjang)<option value="{{ $jenjang }}" @selected(request('jenjang') == $jenjang)>{{ $jenjang }}</option>@endforeach</select>
            <select name="kelas_id" class="{{ $inputClass }}" @change="$el.form.submit()"><option value="">Semua kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected(request('kelas_id') == $kelas->id)>{{ $kelas->nama_kelas }} · {{ $kelas->cabang->nama_cabang ?? 'Tanpa cabang' }}</option>@endforeach</select>
            <select name="status_ujian" class="{{ $inputClass }}" @change="$el.form.submit()"><option value="">Status ujian</option><option value="valid" @selected(request('status_ujian') === 'valid')>Valid</option><option value="belum" @selected(request('status_ujian') === 'belum')>Belum</option></select>
            <select name="status_rapor" class="{{ $inputClass }}" @change="$el.form.submit()"><option value="">Status rapor</option><option value="valid" @selected(request('status_rapor') === 'valid')>Valid</option><option value="belum" @selected(request('status_rapor') === 'belum')>Belum</option></select>
            <div class="flex gap-2"><button type="submit" class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white hover:bg-slate-900"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button>@if($hasActiveFilter)<a href="{{ route($routePrefix.'.index') }}" class="inline-flex h-11 items-center justify-center rounded-xl bg-red-50 px-3 text-xs font-bold text-red-700 no-underline ring-1 ring-inset ring-red-100" aria-label="Reset filter" title="Reset filter"><i class="fas fa-xmark" aria-hidden="true"></i></a>@endif</div>
        </form>

        <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex items-center gap-3"><label class="inline-flex cursor-pointer items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" @change="toggleAll()" :checked="allIds.length > 0 && selected.length === allIds.length" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Pilih semua</label><span class="rounded-full bg-white px-2.5 py-1 text-[10px] font-bold text-brand-700 ring-1 ring-slate-200"><span x-text="selected.length">0</span> terpilih</span></div>
            <div class="grid grid-cols-2 gap-2 sm:flex"><button type="button" @click="runBulk('ujian')" :disabled="!selected.length" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300"><i class="fas fa-check-double" aria-hidden="true"></i>Validasi ujian</button><button type="button" @click="runBulk('rapor')" :disabled="!selected.length" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-violet-600 px-3 text-xs font-bold text-white hover:bg-violet-700 disabled:cursor-not-allowed disabled:bg-slate-300"><i class="fas fa-file-circle-check" aria-hidden="true"></i>Validasi rapor</button><button type="button" @click="openDispensasi()" class="col-span-2 inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-amber-100 px-3 text-xs font-bold text-amber-800 hover:bg-amber-200"><i class="fas fa-hand-holding-heart" aria-hidden="true"></i>Ajukan dispensasi @if(($dispensasiPending ?? 0) > 0)<span class="rounded-full bg-red-600 px-1.5 py-0.5 text-[9px] text-white">{{ $dispensasiPending }}</span>@endif</button></div>
        </div>

        <form x-ref="bulkForm" action="{{ route($routePrefix.'.bulk-validasi-selected') }}" method="POST" class="hidden">@csrf<input x-ref="bulkType" type="hidden" name="tipe"><template x-for="id in selected" :key="id"><input type="hidden" name="siswa_ids[]" :value="id"></template></form>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($siswa as $s)
                <article class="p-4">
                    <div class="flex min-w-0 items-start gap-3"><input type="checkbox" value="{{ $s->id }}" x-model="selected" class="mt-3 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xs font-extrabold text-brand-700">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</span><div class="min-w-0 flex-1"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $s->nama_lengkap }}</h3><p class="mt-0.5 break-words text-[11px] text-slate-500">{{ $s->nisn ?: 'NISN belum tersedia' }} · {{ $s->cabang->nama_cabang ?? '-' }}</p></div><span class="shrink-0 rounded-full bg-brand-50 px-2 py-1 text-[9px] font-bold text-brand-700">{{ $s->kelas->nama_kelas ?? '-' }}</span></div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Total tagihan</dt><dd class="mt-1 whitespace-nowrap font-bold text-slate-800">Rp {{ number_format($s->total_tagihan, 0, ',', '.') }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Sisa</dt><dd class="mt-1 whitespace-nowrap font-extrabold {{ $s->sisa_tagihan > 0 ? 'text-red-700' : 'text-emerald-700' }}">Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Akses ujian</dt><dd class="mt-1 font-bold {{ ($s->punya_akses_ujian ?? false) ? 'text-emerald-700' : 'text-amber-700' }}">{{ ($s->punya_akses_ujian ?? false) ? 'Valid' : 'Belum' }}</dd></div><div><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Akses rapor</dt><dd class="mt-1 font-bold {{ $s->validasi_rapor_bendahara ? 'text-emerald-700' : 'text-amber-700' }}">{{ $s->validasi_rapor_bendahara ? 'Valid' : ($s->validasi_rapor_ketua ? 'Siap divalidasi' : 'Tunggu Ketua') }}</dd></div></dl>
                    <div class="mt-3 grid grid-cols-3 gap-2">
                        <form action="{{ $s->validasi_ujian_bendahara ? route($routePrefix.'.batalkan-ujian', $s->id) : route($routePrefix.'.validasi-ujian', $s->id) }}" method="POST" data-confirm data-confirm-title="{{ $s->validasi_ujian_bendahara ? 'Batalkan validasi ujian?' : 'Validasi akses ujian?' }}" data-confirm-message="Akses ujian {{ $s->nama_lengkap }} akan diperbarui." data-confirm-text="Ya, perbarui">@csrf<button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-1.5 rounded-xl {{ $s->validasi_ujian_bendahara ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} text-[11px] font-bold ring-1 ring-inset ring-current/10"><i class="fas {{ $s->validasi_ujian_bendahara ? 'fa-rotate-left' : 'fa-check' }}" aria-hidden="true"></i>Ujian</button></form>
                        @if($s->validasi_rapor_bendahara || $s->validasi_rapor_ketua)<form action="{{ $s->validasi_rapor_bendahara ? route($routePrefix.'.batalkan-rapor', $s->id) : route($routePrefix.'.validasi-rapor', $s->id) }}" method="POST" data-confirm data-confirm-title="{{ $s->validasi_rapor_bendahara ? 'Batalkan validasi rapor?' : 'Validasi akses rapor?' }}" data-confirm-message="Akses rapor {{ $s->nama_lengkap }} akan diperbarui." data-confirm-text="Ya, perbarui">@csrf<button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-1.5 rounded-xl {{ $s->validasi_rapor_bendahara ? 'bg-red-50 text-red-700' : 'bg-violet-50 text-violet-700' }} text-[11px] font-bold ring-1 ring-inset ring-current/10"><i class="fas {{ $s->validasi_rapor_bendahara ? 'fa-rotate-left' : 'fa-check' }}" aria-hidden="true"></i>Rapor</button></form>@else<button type="button" disabled class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-xl bg-slate-100 text-[11px] font-bold text-slate-400"><i class="fas fa-lock" aria-hidden="true"></i>Rapor</button>@endif
                        <a href="{{ route($tagihanRoutePrefix.'.show', $s->id) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-xl bg-blue-50 text-[11px] font-bold text-blue-700 no-underline ring-1 ring-inset ring-blue-100"><i class="fas fa-file-invoice" aria-hidden="true"></i>Tagihan</a>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center text-sm text-slate-500"><i class="fas fa-search mb-3 block text-4xl text-slate-300" aria-hidden="true"></i>Data siswa tidak ditemukan.<p class="mt-1 text-xs">Coba ubah kata kunci atau filter aktif.</p></div>
            @endforelse
        </div>

        @if($siswa->isNotEmpty())
            <div class="hidden overflow-x-auto lg:block"><table class="w-full min-w-[1060px] table-fixed text-left text-xs">
                <colgroup><col class="w-12"><col class="w-12"><col><col class="w-36"><col class="w-36"><col class="w-36"><col class="w-32"><col class="w-32"><col class="w-36"></colgroup>
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-3 py-3"><input type="checkbox" @change="toggleAll()" :checked="allIds.length > 0 && selected.length === allIds.length" class="h-4 w-4 rounded border-slate-300 text-brand-600"></th><th class="px-2 py-3 text-center">No</th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Tagihan</th><th class="px-3 py-3">Sisa</th><th class="px-3 py-3">Ujian</th><th class="px-3 py-3">Rapor</th><th class="px-4 py-3 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">@foreach($siswa as $index => $s)<tr class="hover:bg-slate-50/70">
                    <td class="px-3 py-4"><input type="checkbox" value="{{ $s->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td><td class="px-2 py-4 text-center text-slate-400">{{ $siswa->firstItem() + $index }}</td>
                    <td class="min-w-0 px-3 py-4"><div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 font-extrabold text-brand-700">{{ strtoupper(substr($s->nama_lengkap, 0, 1)) }}</span><div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900" title="{{ $s->nama_lengkap }}">{{ $s->nama_lengkap }}</p><p class="truncate text-[11px] text-slate-500" title="{{ $s->nisn }} · {{ $s->cabang->nama_cabang ?? '-' }}">{{ $s->nisn ?: 'Tanpa NISN' }} · {{ $s->cabang->nama_cabang ?? '-' }}</p></div></div></td>
                    <td class="px-3 py-4"><span class="inline-flex max-w-full truncate rounded-full bg-brand-50 px-2.5 py-1 text-[10px] font-bold text-brand-700">{{ $s->kelas->nama_kelas ?? '-' }}</span></td><td class="whitespace-nowrap px-3 py-4 font-bold text-slate-700">Rp {{ number_format($s->total_tagihan, 0, ',', '.') }}</td><td class="whitespace-nowrap px-3 py-4 font-extrabold {{ $s->sisa_tagihan > 0 ? 'text-red-700' : 'text-emerald-700' }}">Rp {{ number_format($s->sisa_tagihan, 0, ',', '.') }}</td>
                    <td class="px-3 py-4">@if($s->punya_akses_ujian ?? false)<span class="inline-flex whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700"><i class="fas fa-check-circle mr-1" aria-hidden="true"></i>Valid</span><p class="mt-1 text-[10px] text-slate-400">{{ $s->validasi_ujian_bendahara ? \Carbon\Carbon::parse($s->tanggal_validasi_ujian_bendahara)->format('d/m/Y') : ($s->is_lunas ? 'Lunas' : 'Dispensasi') }}</p>@else<span class="inline-flex whitespace-nowrap rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold text-amber-700">Belum</span>@endif</td>
                    <td class="px-3 py-4">@if($s->validasi_rapor_bendahara)<span class="inline-flex whitespace-nowrap rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold text-emerald-700">Valid</span><p class="mt-1 text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($s->tanggal_validasi_rapor_bendahara)->format('d/m/Y') }}</p>@elseif($s->validasi_rapor_ketua)<span class="inline-flex whitespace-nowrap rounded-full bg-violet-50 px-2.5 py-1 text-[10px] font-bold text-violet-700">Siap</span>@else<span class="inline-flex whitespace-nowrap rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">Tunggu Ketua</span>@endif</td>
                    <td class="px-4 py-4"><div class="flex justify-end gap-1.5"><form action="{{ $s->validasi_ujian_bendahara ? route($routePrefix.'.batalkan-ujian', $s->id) : route($routePrefix.'.validasi-ujian', $s->id) }}" method="POST" data-confirm data-confirm-title="{{ $s->validasi_ujian_bendahara ? 'Batalkan validasi ujian?' : 'Validasi akses ujian?' }}" data-confirm-message="Akses ujian {{ $s->nama_lengkap }} akan diperbarui." data-confirm-text="Ya, perbarui">@csrf<x-cleanflow.table-action type="submit" :tone="$s->validasi_ujian_bendahara ? 'delete' : 'success'" icon="fas {{ $s->validasi_ujian_bendahara ? 'fa-rotate-left' : 'fa-check' }}" label="{{ $s->validasi_ujian_bendahara ? 'Batalkan validasi ujian' : 'Validasi akses ujian' }}" /></form>@if($s->validasi_rapor_bendahara || $s->validasi_rapor_ketua)<form action="{{ $s->validasi_rapor_bendahara ? route($routePrefix.'.batalkan-rapor', $s->id) : route($routePrefix.'.validasi-rapor', $s->id) }}" method="POST" data-confirm data-confirm-title="{{ $s->validasi_rapor_bendahara ? 'Batalkan validasi rapor?' : 'Validasi akses rapor?' }}" data-confirm-message="Akses rapor {{ $s->nama_lengkap }} akan diperbarui." data-confirm-text="Ya, perbarui">@csrf<x-cleanflow.table-action type="submit" :tone="$s->validasi_rapor_bendahara ? 'delete' : 'visibility'" icon="fas {{ $s->validasi_rapor_bendahara ? 'fa-rotate-left' : 'fa-check' }}" label="{{ $s->validasi_rapor_bendahara ? 'Batalkan validasi rapor' : 'Validasi akses rapor' }}" /></form>@else<x-cleanflow.table-action type="button" icon="fas fa-lock" label="Menunggu persetujuan Ketua" disabled />@endif<x-cleanflow.table-action href="{{ route($tagihanRoutePrefix.'.show', $s->id) }}" tone="view" icon="fas fa-file-invoice" label="Detail tagihan" /></div></td>
                </tr>@endforeach</tbody>
            </table></div>
        @endif
        @if($siswa->hasPages())<div class="border-t border-slate-200 px-4 py-3 sm:px-5">{{ $siswa->withQueryString()->links() }}</div>@endif
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5"><div><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-bolt text-amber-500" aria-hidden="true"></i>Validasi kilat per kelas</h2><p class="mt-1 text-xs leading-5 text-slate-500">Validasi satu kelas sekaligus sesuai cabang dan jenjang pada filter.</p></div><span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-600">{{ $quickClasses->count() }} kelas</span></header>
        <div class="grid gap-3 p-4 sm:grid-cols-2 xl:grid-cols-3 sm:p-5">@forelse($quickClasses as $kelas)<article class="rounded-xl border border-slate-200 p-4"><div class="flex min-w-0 items-start justify-between gap-3"><div class="min-w-0"><h3 class="truncate text-sm font-extrabold text-slate-900">{{ $kelas->nama_kelas }}</h3><p class="mt-1 truncate text-[11px] text-slate-500" title="{{ $kelas->cabang->nama_cabang ?? '-' }}">{{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? '-' }}</p></div><span class="shrink-0 rounded-lg bg-brand-50 px-2 py-1 text-[10px] font-bold text-brand-700">{{ $kelas->siswa_aktif_count ?? $kelas->siswa->count() }} siswa</span></div><div class="mt-4 grid grid-cols-2 gap-2"><form action="{{ route($routePrefix.'.bulk-validasi-ujian', $kelas->id) }}" method="POST" data-confirm data-confirm-title="Validasi ujian sekelas?" data-confirm-message="Seluruh siswa kelas {{ $kelas->nama_kelas }} akan divalidasi untuk akses ujian." data-confirm-text="Ya, validasi">@csrf<button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-emerald-50 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-100 hover:bg-emerald-100"><i class="fas fa-check-double" aria-hidden="true"></i>Ujian</button></form><form action="{{ route($routePrefix.'.bulk-validasi-rapor', $kelas->id) }}" method="POST" data-confirm data-confirm-title="Validasi rapor sekelas?" data-confirm-message="Siswa kelas {{ $kelas->nama_kelas }} yang sudah disetujui Ketua akan divalidasi." data-confirm-text="Ya, validasi">@csrf<button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-violet-50 text-xs font-bold text-violet-700 ring-1 ring-inset ring-violet-100 hover:bg-violet-100"><i class="fas fa-file-circle-check" aria-hidden="true"></i>Rapor</button></form></div></article>@empty<div class="col-span-full py-10 text-center text-sm text-slate-500"><i class="fas fa-school mb-3 block text-4xl text-slate-300" aria-hidden="true"></i>Tidak ada kelas untuk filter aktif.</div>@endforelse</div>
    </section>

    <dialog x-ref="dispensasiDialog" class="m-auto w-[calc(100%-2rem)] max-w-lg overflow-hidden rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-slate-950/60" @click.self="$el.close()">
        <form action="{{ route($routePrefix.'.dispensasi') }}" method="POST">@csrf<template x-for="id in selected" :key="id"><input type="hidden" name="siswa_ids[]" :value="id"></template><header class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 sm:p-5"><div><h2 class="text-base font-extrabold text-slate-950">Ajukan dispensasi</h2><p class="mt-1 text-xs text-slate-500"><strong x-text="selected.length"></strong> siswa akan diajukan kepada Ketua PKBM.</p></div><button type="button" @click="$refs.dispensasiDialog.close()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Tutup"><i class="fas fa-xmark" aria-hidden="true"></i></button></header>
            <div class="space-y-4 p-4 sm:p-5"><label class="block text-xs font-bold text-slate-700">Jenis akses<select name="tipe" required class="{{ $inputClass }} mt-2"><option value="ujian">Akses ujian</option><option value="rapor">Akses rapor</option></select></label><label class="block text-xs font-bold text-slate-700">Periode<select name="periode" class="{{ $inputClass }} mt-2"><option value="">Semua periode</option><option value="pts_ganjil">PTS Ganjil</option><option value="pas_ganjil">PAS Ganjil</option><option value="pts_genap">PTS Genap</option><option value="pas_genap">PAS Genap</option><option value="ujian_akhir">Ujian Akhir</option></select></label><label class="block text-xs font-bold text-slate-700">Alasan dispensasi <span class="text-red-600">*</span><textarea name="alasan" rows="4" maxlength="500" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Jelaskan alasan secara ringkas dan objektif."></textarea></label><p class="rounded-xl bg-blue-50 p-3 text-[11px] leading-5 text-blue-700"><i class="fas fa-circle-info mr-1" aria-hidden="true"></i>Pengajuan tidak langsung membuka akses; Ketua PKBM tetap harus menyetujuinya.</p></div>
            <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5"><button type="button" @click="$refs.dispensasiDialog.close()" class="h-10 px-4 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-amber-500 px-4 text-xs font-bold text-white hover:bg-amber-600"><i class="fas fa-paper-plane" aria-hidden="true"></i>Kirim pengajuan</button></footer>
        </form>
    </dialog>
</div>
@endsection
