@extends('layouts.app')

@section('title', 'Edit Tagihan - '.$siswa->nama_lengkap)
@section('page-title', 'Edit Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('content')
<div data-tagihan-edit class="min-w-0 w-full space-y-5" x-data="{
    formatCurrency(event) { const digits = event.target.value.replace(/\D/g, ''); event.target.value = digits ? new Intl.NumberFormat('id-ID').format(Number(digits)) : '0'; },
    async save(event) { const result = await Swal.fire({ icon: 'question', title: 'Simpan perubahan tagihan?', text: 'Nominal dan jatuh tempo yang dapat diedit akan diperbarui.', showCancelButton: true, confirmButtonText: 'Ya, simpan', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true }); if (result.isConfirmed) event.target.submit(); },
    async deleteItem(url, label) {
        const result = await Swal.fire({ icon: 'warning', title: 'Hapus tagihan?', html: `<strong>${label}</strong> akan dihapus permanen.`, showCancelButton: true, confirmButtonText: 'Ya, hapus', cancelButtonText: 'Batal', confirmButtonColor: '#dc2626', reverseButtons: true });
        if (!result.isConfirmed) return;
        try {
            const response = await fetch(url, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept': 'application/json' } });
            const data = await response.json();
            if (!response.ok) throw new Error(data.error || 'Tagihan tidak dapat dihapus.');
            await Swal.fire({ icon: 'success', title: 'Tagihan dihapus', text: data.message || 'Data berhasil diperbarui.', timer: 1200, showConfirmButton: false });
            window.location.reload();
        } catch (error) { Swal.fire({ icon: 'error', title: 'Gagal menghapus', text: error.message, confirmButtonColor: '#285dcc' }); }
    }
}">
    <section class="overflow-hidden rounded-2xl bg-gradient-to-r from-brand-800 to-brand-600 p-5 text-white shadow-sm sm:p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-4"><span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-xl font-black ring-1 ring-white/20">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</span><div class="min-w-0"><p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-blue-100">Atur tagihan siswa</p><h1 class="truncate text-xl font-black !text-white">{{ $siswa->nama_lengkap }}</h1><p class="mt-1 truncate text-xs text-blue-100">NISN {{ $siswa->nisn ?: '-' }} · {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }} · {{ $siswa->cabang->nama_cabang ?? 'Cabang belum diatur' }}</p></div></div>
            <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-white/20 bg-white/10 px-4 text-sm font-bold text-white no-underline hover:bg-white/20"><i class="fas fa-arrow-left"></i>Kembali ke detail</a>
        </div>
    </section>

    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><div class="flex items-start gap-3"><i class="fas fa-circle-info mt-1 text-blue-700"></i><p class="text-sm leading-6 text-blue-900">SPP bulanan sebaiknya dibuat melalui <a href="{{ route('admin.keuangan.tagihan.generate-spp') }}" class="font-extrabold text-blue-800 underline">Generate SPP</a> agar 12 bulan dan jatuh temponya tersusun otomatis.</p></div></section>

    <form action="{{ route('admin.keuangan.tagihan.update', $siswa->id) }}" method="POST" @submit.prevent="save($event)" class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        @method('PUT')
        <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-950"><i class="fas fa-pen-to-square text-amber-600"></i>Nominal dan jatuh tempo</h2><p class="mt-1 text-sm text-slate-500">Isi Rp 0 bila siswa tidak memiliki kewajiban pada jenis tersebut.</p></header>

        <div class="divide-y divide-slate-100">
            @foreach($jenisTagihan as $key => $label)
                @php
                    $tagihanRecord = $allTagihan->firstWhere('jenis_tagihan', $key);
                    $isCustom = !in_array($key, $standardJenisTagihan);
                    $hasPembayaran = $tagihanRecord && $tagihanRecord->pembayaran()->where('status_validasi', 'disetujui')->exists();
                    $isReadOnly = $hasPembayaran;
                    $canDelete = $tagihanRecord && !$hasPembayaran && ($isCustom || $tagihanRecord->status === 'belum_bayar');
                    $selectedYearId = (string) old('tahun_ajaran_id.'.$key, $tagihanRecord?->tahun_ajaran_id ?? $tahunAjaran->id);
                    $rawValue = intval($tagihanExist[$key] ?? 0);
                    $dueValue = old('tanggal_jatuh_tempo.'.$key, $tagihanRecord?->tanggal_jatuh_tempo?->toDateString() ?? $defaultDueDate);
                @endphp
                <article class="p-4 sm:p-5 {{ $isReadOnly ? 'bg-slate-50/80' : '' }}">
                    <div class="grid gap-4 md:grid-cols-[minmax(11rem,1fr)_minmax(10rem,.8fr)_minmax(11rem,.8fr)_minmax(10rem,.7fr)_auto] md:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2"><span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-[10px] font-extrabold text-slate-500">{{ $loop->iteration }}</span><h3 class="font-extrabold text-slate-950">{{ $label }}</h3></div>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @if($isReadOnly)<span class="inline-flex rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700"><i class="fas fa-lock mr-1"></i>Sudah dibayar wali</span>
                                @elseif($isCustom && $tagihanRecord)<span class="inline-flex rounded-full bg-violet-50 px-2 py-1 text-[10px] font-bold text-violet-700">Tagihan khusus</span>@endif
                                @if($key === 'spp')<span class="inline-flex rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">Bulanan</span>@endif
                            </div>
                        </div>

                        <label class="block"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500 md:sr-only">Tahun ajaran</span><select name="tahun_ajaran_id[{{ $key }}]" {{ $isReadOnly ? 'disabled' : '' }} class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500" @change="const option = $event.target.selectedOptions[0]; const due = $event.target.closest('article').querySelector('[data-due-date]'); due.min = option.dataset.start; due.max = option.dataset.end; if (!due.value || due.value < due.min || due.value > due.max) due.value = option.dataset.defaultDate">
                            @foreach($allYears as $thn)<option value="{{ $thn->id }}" data-start="{{ $thn->tanggal_mulai->toDateString() }}" data-end="{{ $thn->tanggal_selesai->toDateString() }}" data-default-date="{{ $thn->getDefaultTagihanDueDate()->toDateString() }}" @selected($selectedYearId == $thn->id)>{{ $thn->nama_tahun_ajaran }}</option>@endforeach
                        </select></label>

                        <label class="block"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500 md:sr-only">Jumlah</span><span class="flex h-11 overflow-hidden rounded-xl border border-slate-300 bg-white focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-100"><span class="flex items-center bg-slate-50 px-3 text-xs font-bold text-slate-500">Rp</span><input type="text" inputmode="numeric" name="tagihan[{{ $key }}]" value="{{ number_format(old('tagihan.'.$key, $rawValue), 0, ',', '.') }}" {{ $isReadOnly ? 'disabled' : '' }} @input="formatCurrency($event)" class="min-w-0 flex-1 border-0 px-3 text-right text-sm font-bold tabular-nums text-slate-900 outline-none disabled:bg-slate-100 disabled:text-slate-500"></span>@error('tagihan.'.$key)<span class="mt-1 block text-xs font-semibold text-red-600">{{ $message }}</span>@enderror</label>

                        <label class="block"><span class="mb-1.5 block text-[10px] font-bold uppercase tracking-wide text-slate-500 md:sr-only">Jatuh tempo</span><input data-due-date type="date" name="tanggal_jatuh_tempo[{{ $key }}]" value="{{ $dueValue }}" min="{{ $tagihanDateMin }}" max="{{ $tagihanDateMax }}" {{ $isReadOnly ? 'disabled' : '' }} class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500"></label>

                        <div class="flex justify-end md:pt-1">
                            @if($canDelete && $tagihanRecord)
                                <x-cleanflow.table-action type="button" tone="delete" icon="fas fa-trash" label="Hapus {{ $label }}" data-delete-url="{{ route('admin.keuangan.tagihan.destroy-item', $tagihanRecord->id) }}" data-delete-label="{{ $label }}" x-on:click="deleteItem($el.dataset.deleteUrl, $el.dataset.deleteLabel)" />
                            @else
                                <span class="hidden h-9 w-9 md:block"></span>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <footer class="flex flex-col gap-3 border-t border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5"><p class="text-xs leading-5 text-slate-600"><i class="fas fa-shield-halved mr-1 text-emerald-600"></i>Tagihan yang sudah dibayar dikunci untuk menjaga integritas transaksi.</p><div class="flex gap-2"><a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-slate-300 px-5 text-sm font-bold text-slate-700 no-underline hover:bg-white">Batal</a><button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-save"></i>Simpan</button></div></footer>
    </form>

    <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><div class="flex items-start gap-3"><i class="fas fa-triangle-exclamation mt-1 text-amber-700"></i><div class="text-sm leading-6 text-amber-900"><strong>Perlu diperhatikan</strong><ul class="mt-1 list-disc space-y-1 pl-5"><li>Tagihan yang telah dibayar wali tidak dapat diedit atau dihapus.</li><li>Nominal Rp 0 tetap dapat diubah selama belum memiliki pembayaran.</li><li>Perubahan nominal dapat memengaruhi status pembayaran siswa.</li><li>Periode aktif saat ini: <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong>.</li></ul></div></div></section>
</div>
@endsection
