@extends('layouts.app')

@section('title', 'Riwayat Presensi Siswa')
@section('page-title', 'Riwayat Presensi')
@section('page-subtitle', 'Telusuri dan koreksi data kehadiran')

@section('content')
<div class="min-w-0 w-full space-y-4" x-data="{ editAction: '', editName: '', editDate: '', editStatus: 'hadir', editValidation: '', editPending: false, editNote: '' }">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm"><div><h1 class="text-lg font-extrabold text-slate-900">Riwayat presensi</h1><p class="mt-0.5 text-xs text-slate-500">Filter catatan lalu perbaiki data yang diperlukan.</p></div><a href="{{ route('wali.presensi.index') }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a></header>

    @if($error ?? false)<p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ $error }}</p>@endif

    <form action="{{ route('wali.presensi.riwayat') }}" method="GET" class="grid grid-cols-2 gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm lg:grid-cols-[minmax(170px,1.4fr)_repeat(3,minmax(130px,1fr))_auto] lg:items-end">
        <label class="col-span-2 min-w-0 text-xs font-bold text-slate-600 lg:col-span-1">Siswa<select name="siswa_id" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-2.5 text-sm text-slate-800"><option value="">Semua siswa</option>@foreach($siswaList as $siswa)<option value="{{ $siswa->id }}" @selected(request('siswa_id') == $siswa->id)>{{ $siswa->nama_lengkap }}</option>@endforeach</select></label>
        <label class="min-w-0 text-xs font-bold text-slate-600">Dari tanggal<input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="mt-1 block h-10 w-full min-w-0 rounded-lg border border-slate-300 px-2 text-sm text-slate-800"></label>
        <label class="min-w-0 text-xs font-bold text-slate-600">Sampai tanggal<input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="mt-1 block h-10 w-full min-w-0 rounded-lg border border-slate-300 px-2 text-sm text-slate-800"></label>
        <label class="min-w-0 text-xs font-bold text-slate-600">Status<select name="status" class="mt-1 block h-10 w-full rounded-lg border border-slate-300 bg-white px-2.5 text-sm text-slate-800"><option value="">Semua status</option>@foreach(['hadir' => 'Hadir', 'sakit' => 'Sakit', 'izin' => 'Izin', 'alpha' => 'Alpha'] as $value => $label)<option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>@endforeach</select></label>
        <div class="flex items-end gap-2"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center gap-1.5 rounded-lg bg-sky-700 px-3 text-xs font-bold text-white hover:bg-sky-800"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button><a href="{{ route('wali.presensi.riwayat') }}" class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700" aria-label="Reset filter"><i class="fas fa-rotate-left" aria-hidden="true"></i></a></div>
    </form>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-label="Daftar riwayat presensi">
        <div class="hidden grid-cols-[120px_minmax(170px,1.2fr)_90px_minmax(120px,1fr)_95px_50px] bg-slate-50 px-4 py-2 text-[11px] font-bold uppercase tracking-wide text-slate-500 lg:grid"><span>Tanggal</span><span>Siswa</span><span>Status</span><span>Keterangan</span><span>Validasi</span><span>Aksi</span></div>
        <div class="divide-y divide-slate-100">
            @forelse($riwayat as $item)
                @php
                    $isLegacyPending = $item->status_validasi === null && in_array($item->status, ['sakit', 'izin'], true) && str_contains($item->keterangan ?? '', 'Diajukan oleh wali siswa');
                    $validation = $item->status_validasi ?: ($isLegacyPending ? 'pending' : '');
                    $validationLabel = match($validation) { 'pending' => 'Menunggu keputusan', 'disetujui' => 'Terverifikasi', 'ditolak' => 'Ditolak', default => in_array($item->status, ['sakit', 'izin'], true) ? 'Dicatat wali' : 'Tidak perlu validasi' };
                    $validationTone = match($validation) { 'pending' => 'bg-amber-50 text-amber-800', 'disetujui' => 'bg-emerald-50 text-emerald-800', 'ditolak' => 'bg-rose-50 text-rose-800', default => 'bg-slate-100 text-slate-700' };
                    $statusTone = match($item->status) { 'hadir' => 'bg-emerald-50 text-emerald-800', 'sakit' => 'bg-amber-50 text-amber-800', 'izin' => 'bg-sky-50 text-sky-800', 'alpha' => 'bg-rose-50 text-rose-800', default => 'bg-slate-100 text-slate-700' };
                @endphp
                <article class="grid min-w-0 gap-2 px-4 py-3 lg:grid-cols-[120px_minmax(170px,1.2fr)_90px_minmax(120px,1fr)_95px_50px] lg:items-center lg:gap-0">
                    <p class="text-xs font-semibold text-slate-700">{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}</p>
                    <div class="min-w-0"><p class="truncate text-sm font-bold text-slate-900" title="{{ $item->siswa->nama_lengkap ?? '-' }}">{{ $item->siswa->nama_lengkap ?? '-' }}</p><p class="text-[11px] text-slate-500">NIS: {{ $item->siswa->nis ?? '-' }}</p></div>
                    <span class="w-fit rounded-full px-2 py-1 text-[11px] font-bold {{ $statusTone }}">{{ ucfirst($item->status) }}</span>
                    <div class="min-w-0"><p class="break-words text-xs text-slate-600">{{ $item->keterangan ?: '-' }}</p>@if($item->bukti_file)<a href="{{ route('wali.presensi.preview-bukti', $item->id) }}" target="_blank" rel="noopener" class="mt-1 inline-flex items-center gap-1 text-[11px] font-bold text-sky-700"><i class="fas fa-paperclip" aria-hidden="true"></i>Lihat bukti</a>@endif</div>
                    <span class="w-fit rounded-full px-2 py-1 text-[11px] font-bold {{ $validationTone }}">{{ $validationLabel }}</span>
                    <button type="button" data-action="{{ route('wali.presensi.riwayat.update', $item->id) }}" data-name="{{ $item->siswa->nama_lengkap ?? '-' }}" data-date="{{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d F Y') }}" data-status="{{ $item->status }}" data-validation="{{ $validation }}" data-pending="{{ $validation === 'pending' ? '1' : '0' }}" data-note="{{ $item->keterangan ?? '' }}" @click="editAction = $el.dataset.action; editName = $el.dataset.name; editDate = $el.dataset.date; editStatus = $el.dataset.status; editValidation = $el.dataset.validation; editPending = $el.dataset.pending === '1'; editNote = $el.dataset.note; $refs.editDialog.showModal()" class="inline-flex h-9 w-9 items-center justify-center rounded-lg border border-sky-200 bg-sky-50 text-sky-700 hover:bg-sky-100" aria-label="{{ $validation === 'pending' ? 'Edit catatan' : 'Edit presensi' }} {{ $item->siswa->nama_lengkap ?? '-' }}"><i class="fas fa-pen text-xs" aria-hidden="true"></i></button>
                </article>
            @empty
                <p class="px-4 py-9 text-center text-sm text-slate-500">Tidak ada riwayat presensi yang cocok.</p>
            @endforelse
        </div>
        @if($riwayat->hasPages())<div class="border-t border-slate-100 px-4 py-3">{{ $riwayat->links() }}</div>@endif
    </section>

    <dialog x-ref="editDialog" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()">
        <div class="border-b border-slate-100 px-5 py-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-base font-extrabold text-slate-900" x-text="editPending ? 'Edit catatan presensi' : 'Edit presensi'"></h2>
                <button type="button" @click="$refs.editDialog.close()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" aria-label="Tutup"><i class="fas fa-xmark" aria-hidden="true"></i></button>
            </div>
            <p class="mt-1 text-xs text-slate-500"><span x-text="editName"></span> · <span x-text="editDate"></span></p>
        </div>
        <form :action="editAction" method="POST" class="space-y-4 px-5 py-4">
            @csrf
            @method('PUT')
            <template x-if="editPending"><input type="hidden" name="status" :value="editStatus"></template>
            <label x-show="!editPending" class="block text-xs font-bold text-slate-700">
                Status kehadiran
                <select name="status" x-model="editStatus" :disabled="editPending" required class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-800">
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alpha">Alpha</option>
                </select>
            </label>
            <div x-show="editPending" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs leading-5 text-amber-900">
                Permohonan sakit/izin masih menunggu keputusan. Ubah catatan di sini, lalu buka
                <a href="{{ route('wali.presensi.validasi-izin') }}" class="font-bold underline">Validasi Izin</a>
                untuk menyetujui atau menolak.
            </div>
            <p x-show="!editPending" class="rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs leading-5 text-sky-900">
                Kehadiran mencatat kondisi siswa. Keputusan atas permohonan wali siswa hanya dilakukan di menu Validasi Izin.
            </p>
            <label class="block text-xs font-bold text-slate-700">
                Keterangan
                <textarea name="keterangan" x-model="editNote" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800"></textarea>
            </label>
            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                <button type="button" @click="$refs.editDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-4 text-xs font-bold text-slate-700">Batal</button>
                <button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white" x-text="editPending ? 'Simpan catatan' : 'Simpan perubahan'"></button>
            </div>
        </form>
    </dialog>
</div>
@endsection
