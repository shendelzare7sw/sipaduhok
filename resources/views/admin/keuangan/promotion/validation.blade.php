@extends('layouts.app')

@section('title', 'Validasi Dispensasi')
@section('page-title', 'Validasi Dispensasi')
@section('page-subtitle', 'Ajukan izin khusus untuk kandidat naik kelas dengan tunggakan')

@section('content')
@php
    $candidateCollection = collect($candidates);
    $totalCandidates = $candidateCollection->count();
    $readyCandidates = $candidateCollection->whereNull('pending_request')->count();
    $pendingCandidates = $totalCandidates - $readyCandidates;
    $totalUnpaid = $candidateCollection->sum(fn ($candidate) => $candidate['financial']['unpaid_amount'] ?? 0);
    $selectableIds = $candidateCollection->whereNull('pending_request')->map(fn ($candidate) => (string) $candidate['siswa']->id)->values();
    $routePrefix = request()->routeIs('bendahara.*')
        ? 'bendahara.kenaikan-kelas.validation'
        : 'admin.keuangan.kenaikan-kelas.validation';
@endphp

<div
    class="min-w-0 w-full space-y-5"
    x-data="{
        selected: [],
        available: @js($selectableIds),
        student: null,
        toggleAll() { this.selected = this.selected.length === this.available.length ? [] : [...this.available]; },
        openStudent(student) { this.student = student; this.$refs.studentDialog.showModal(); },
        openBulk() { if (this.selected.length) this.$refs.bulkDialog.showModal(); }
    }"
>
    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach([
            ['Kandidat', $totalCandidates, 'Akademik tuntas, masih menunggak', 'fa-user-graduate', 'bg-brand-50 text-brand-600'],
            ['Siap diajukan', $readyCandidates, 'Belum memiliki pengajuan aktif', 'fa-paper-plane', 'bg-emerald-50 text-emerald-600'],
            ['Menunggu', $pendingCandidates, 'Sudah masuk antrean persetujuan', 'fa-hourglass-half', 'bg-amber-50 text-amber-600'],
            ['Total tunggakan', 'Rp '.number_format($totalUnpaid, 0, ',', '.'), 'Dari kandidat yang tampil', 'fa-money-bill-wave', 'bg-red-50 text-red-600'],
        ] as [$label, $value, $description, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><p class="truncate text-lg font-extrabold text-slate-950" title="{{ $value }}">{{ $value }}</p><p class="mt-1 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span></div><p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] text-slate-500" title="{{ $description }}">{{ $description }}</p></article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-clipboard-check text-brand-600" aria-hidden="true"></i>Kandidat dispensasi</h2><p class="mt-1 text-xs leading-5 text-slate-500">Pilih siswa, isi alasan, lalu kirim permohonan ke Ketua PKBM.</p></div>
            <a href="{{ route($routePrefix.'.history') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>Riwayat keputusan</a>
        </header>

        @if($readyCandidates > 0)
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
                <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" @change="toggleAll()" :checked="available.length > 0 && selected.length === available.length" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Pilih semua yang siap</label>
                <button type="button" @click="openBulk()" :disabled="!selected.length" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white shadow-sm hover:bg-brand-700 disabled:cursor-not-allowed disabled:bg-slate-300"><i class="fas fa-paper-plane" aria-hidden="true"></i><span>Ajukan <span x-text="selected.length"></span> siswa</span></button>
            </div>
        @endif

        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-[64rem] table-fixed text-left text-xs">
                <colgroup><col class="w-12"><col><col class="w-48"><col class="w-36"><col class="w-44"><col class="w-36"><col class="w-40"></colgroup>
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3"></th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Kelas & cabang</th><th class="px-3 py-3">Akademik</th><th class="px-3 py-3">Tunggakan</th><th class="px-3 py-3">Pengajuan</th><th class="py-3 pl-6 pr-4 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($candidates as $candidate)
                        @php $student = $candidate['siswa']; $pending = $candidate['pending_request']; @endphp
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-4">@unless($pending)<input type="checkbox" value="{{ $student->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">@endunless</td>
                            <td class="px-3 py-4"><div class="flex min-w-0 items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xs font-extrabold text-brand-700">{{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}</span><div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900" title="{{ $student->nama_lengkap }}">{{ $student->nama_lengkap }}</p><p class="truncate text-[11px] text-slate-500">{{ $student->nisn ?? 'NISN belum tersedia' }}</p></div></div></td>
                            <td class="px-3 py-4"><p class="truncate font-bold text-slate-800">{{ $student->kelas->nama_kelas ?? '-' }}</p><p class="mt-0.5 truncate text-[11px] text-slate-500" title="{{ $student->cabang->nama_cabang ?? '-' }}">{{ $student->cabang->nama_cabang ?? '-' }}</p></td>
                            <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-full bg-emerald-100 px-2.5 py-1 text-[10px] font-bold text-emerald-700"><i class="fas fa-circle-check mr-1" aria-hidden="true"></i>{{ $candidate['academic']['percentage'] }}% tuntas</span></td>
                            <td class="whitespace-nowrap px-3 py-4 font-extrabold text-red-700">Rp {{ number_format($candidate['financial']['unpaid_amount'], 0, ',', '.') }}</td>
                            <td class="px-3 py-4">@if($pending)<span class="inline-flex rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-bold text-amber-700">{{ ucfirst(strtolower($pending->status)) }}</span>@else<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">Belum diajukan</span>@endif</td>
                            <td class="py-4 pl-6 pr-4 text-right">@unless($pending)<button type="button" @click="openStudent({ id: @js((string) $student->id), name: @js($student->nama_lengkap), debt: @js('Rp '.number_format($candidate['financial']['unpaid_amount'], 0, ',', '.')) })" class="inline-flex h-9 items-center gap-2 whitespace-nowrap rounded-xl bg-brand-600 px-3 text-[11px] font-bold text-white hover:bg-brand-700"><i class="fas fa-paper-plane" aria-hidden="true"></i>Ajukan</button>@else<span class="text-[11px] text-slate-500">Menunggu</span>@endunless</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-14 text-center text-sm text-slate-500"><i class="fas fa-circle-check mb-3 block text-4xl text-emerald-300" aria-hidden="true"></i>Tidak ada siswa yang memerlukan dispensasi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($candidates as $candidate)
                @php $student = $candidate['siswa']; $pending = $candidate['pending_request']; @endphp
                <article class="p-4">
                    <div class="flex min-w-0 items-start gap-3">@unless($pending)<input type="checkbox" value="{{ $student->id }}" x-model="selected" class="mt-3 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500">@endunless<span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xs font-extrabold text-brand-700">{{ strtoupper(substr($student->nama_lengkap, 0, 1)) }}</span><div class="min-w-0 flex-1"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $student->nama_lengkap }}</h3><p class="mt-0.5 text-[11px] text-slate-500">{{ $student->nisn ?? 'NISN belum tersedia' }}</p></div>@if($pending)<span class="shrink-0 rounded-full bg-amber-100 px-2 py-1 text-[9px] font-bold text-amber-700">Menunggu</span>@endif</div>
                    <dl class="mt-4 grid grid-cols-2 gap-3 rounded-xl bg-slate-50 p-3 text-xs"><div><dt class="text-[9px] font-bold uppercase text-slate-400">Kelas</dt><dd class="mt-1 font-bold text-slate-800">{{ $student->kelas->nama_kelas ?? '-' }}</dd><dd class="mt-0.5 break-words text-[10px] text-slate-500">{{ $student->cabang->nama_cabang ?? '-' }}</dd></div><div><dt class="text-[9px] font-bold uppercase text-slate-400">Tunggakan</dt><dd class="mt-1 whitespace-nowrap font-extrabold text-red-700">Rp {{ number_format($candidate['financial']['unpaid_amount'], 0, ',', '.') }}</dd><dd class="mt-0.5 text-[10px] font-semibold text-emerald-700">Akademik {{ $candidate['academic']['percentage'] }}%</dd></div></dl>
                    @unless($pending)<button type="button" @click="openStudent({ id: @js((string) $student->id), name: @js($student->nama_lengkap), debt: @js('Rp '.number_format($candidate['financial']['unpaid_amount'], 0, ',', '.')) })" class="mt-3 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-paper-plane" aria-hidden="true"></i>Ajukan dispensasi</button>@endunless
                </article>
            @empty
                <div class="p-12 text-center text-sm text-slate-500"><i class="fas fa-circle-check mb-3 block text-4xl text-emerald-300" aria-hidden="true"></i>Tidak ada siswa yang memerlukan dispensasi.</div>
            @endforelse
        </div>
    </section>

    <dialog x-ref="studentDialog" class="m-auto w-[calc(100%-2rem)] max-w-lg overflow-hidden rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-slate-950/60" @click.self="$el.close()">
        <form action="{{ route($routePrefix.'.store') }}" method="POST">
            @csrf
            <input type="hidden" name="siswa_id" :value="student?.id">
            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
            <header class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 sm:p-5"><div><h2 class="text-base font-extrabold text-slate-950">Ajukan dispensasi</h2><p class="mt-1 text-xs text-slate-500" x-text="student ? `${student.name} · ${student.debt}` : ''"></p></div><button type="button" @click="$refs.studentDialog.close()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Tutup"><i class="fas fa-xmark" aria-hidden="true"></i></button></header>
            <div class="p-4 sm:p-5"><label for="single_alasan" class="text-xs font-bold text-slate-700">Alasan pengajuan <span class="text-red-600">*</span></label><textarea id="single_alasan" name="alasan" rows="4" maxlength="500" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Jelaskan alasan dispensasi secara ringkas dan objektif."></textarea><p class="mt-2 text-[11px] leading-5 text-slate-500">Pengajuan akan diteruskan kepada Ketua PKBM untuk diputuskan.</p></div>
            <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5"><button type="button" @click="$refs.studentDialog.close()" class="h-10 px-4 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-paper-plane" aria-hidden="true"></i>Kirim pengajuan</button></footer>
        </form>
    </dialog>

    <dialog x-ref="bulkDialog" class="m-auto w-[calc(100%-2rem)] max-w-lg overflow-hidden rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-slate-950/60" @click.self="$el.close()">
        <form action="{{ route($routePrefix.'.bulk-store') }}" method="POST">
            @csrf
            <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
            <template x-for="id in selected" :key="id"><input type="hidden" name="siswa_ids[]" :value="id"></template>
            <header class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 sm:p-5"><div><h2 class="text-base font-extrabold text-slate-950">Ajukan secara massal</h2><p class="mt-1 text-xs text-slate-500"><strong x-text="selected.length"></strong> siswa akan memakai alasan yang sama.</p></div><button type="button" @click="$refs.bulkDialog.close()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Tutup"><i class="fas fa-xmark" aria-hidden="true"></i></button></header>
            <div class="p-4 sm:p-5"><label for="bulk_alasan" class="text-xs font-bold text-slate-700">Alasan pengajuan bersama <span class="text-red-600">*</span></label><textarea id="bulk_alasan" name="alasan" rows="4" maxlength="500" required class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Pastikan alasan relevan bagi semua siswa yang dipilih."></textarea></div>
            <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5"><button type="button" @click="$refs.bulkDialog.close()" class="h-10 px-4 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-paper-plane" aria-hidden="true"></i>Kirim semua</button></footer>
        </form>
    </dialog>
</div>
@endsection
