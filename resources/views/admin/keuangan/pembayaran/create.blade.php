@extends('layouts.app')

@section('title', 'Input Pembayaran Tunai')
@section('page-title', 'Input Pembayaran Tunai')
@section('page-subtitle', 'Catat pembayaran yang diterima langsung di loket sekolah')

@section('content')
@php
    $paymentItems = $tagihanBelumLunas->mapWithKeys(fn ($tagihan) => [(string) $tagihan->id => [
        'selected' => false,
        'amount' => (int) $tagihan->sisa_per_item,
        'display' => number_format($tagihan->sisa_per_item, 0, ',', '.'),
        'max' => (int) $tagihan->sisa_per_item,
        'fixed' => $tagihan->jenis_tagihan === 'spp',
    ]]);
    $inputClass = 'mt-2 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
@endphp

<div class="min-w-0 w-full space-y-5" data-payment-create x-data="{
    items: @js($paymentItems),
    parse(value) { return Number(String(value).replace(/\D/g, '')) || 0; },
    format(value) { return new Intl.NumberFormat('id-ID').format(value || 0); },
    updateAmount(id, value) {
        const amount = this.parse(value);
        this.items[id].amount = amount;
        this.items[id].display = this.format(amount);
        if (amount > 0) this.items[id].selected = true;
    },
    selectedItems() { return Object.values(this.items).filter(item => item.selected); },
    count() { return this.selectedItems().length; },
    total() { return this.selectedItems().reduce((sum, item) => sum + item.amount, 0); },
    hasError() { return this.selectedItems().some(item => item.amount < 1 || item.amount > item.max); },
    async submitPayment(form) {
        if (!this.count() || !this.total() || this.hasError()) {
            await Swal.fire({ icon: 'warning', title: 'Periksa nominal pembayaran', text: 'Pilih minimal satu tagihan dan pastikan nominal tidak melebihi sisanya.', confirmButtonColor: '#285dcc' });
            return;
        }
        const result = await Swal.fire({ icon: 'question', title: 'Simpan pembayaran tunai?', text: `${this.count()} tagihan dengan total Rp ${this.format(this.total())} akan dicatat.`, showCancelButton: true, confirmButtonText: 'Ya, simpan', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true, focusCancel: true });
        if (result.isConfirmed) form.submit();
    }
}">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="flex min-w-0 items-center gap-3"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-sm font-extrabold text-brand-700">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</span><div class="min-w-0"><p class="text-[10px] font-bold uppercase tracking-wide text-brand-600">Pembayaran loket</p><h2 class="truncate text-lg font-extrabold text-slate-950" title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</h2><p class="mt-0.5 truncate text-xs text-slate-500">{{ $siswa->nisn ?: 'NISN belum tersedia' }}</p></div></div>
            <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali ke tagihan</a>
        </div>
        <dl class="grid border-t border-slate-200 text-xs sm:grid-cols-3"><div class="border-slate-200 p-4 sm:border-r"><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Kelas</dt><dd class="mt-1 font-extrabold text-slate-800">{{ $siswa->kelas->nama_kelas ?? '-' }} <span class="font-semibold text-slate-500">{{ $siswa->kelas->jenjang ?? '' }}</span></dd></div><div class="border-slate-200 p-4 sm:border-r"><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Cabang</dt><dd class="mt-1 break-words font-extrabold text-slate-800">{{ $siswa->cabang->nama_cabang ?? '-' }}</dd></div><div class="p-4"><dt class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Sisa tagihan</dt><dd class="mt-1 whitespace-nowrap text-base font-extrabold text-red-700">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</dd></div></dl>
    </section>

    <section class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-xs leading-5 text-blue-800"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-blue-600"><i class="fas fa-circle-info" aria-hidden="true"></i></span><div><h2 class="font-extrabold text-blue-900">Khusus pembayaran tunai di loket</h2><p class="mt-1">Pembayaran digital dan Direct Transfer tercatat otomatis dari sistem wali siswa. Aktifkan validasi langsung hanya setelah uang tunai benar-benar diterima.</p></div></section>

    <form action="{{ route('admin.keuangan.pembayaran.store', $siswa->id) }}" method="POST" enctype="multipart/form-data" @submit.prevent="submitPayment($el)" class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1.15fr)_minmax(340px,0.85fr)]">@csrf<input type="hidden" name="metode_pembayaran" value="tunai"><input type="hidden" name="jumlah_bayar" :value="total()">
        <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-file-invoice-dollar text-brand-600" aria-hidden="true"></i>1. Pilih tagihan yang dibayar</h2><p class="mt-1 text-xs text-slate-500">Centang tagihan lalu sesuaikan nominalnya. SPP harus dibayar penuh.</p></header>
            @forelse($tagihanBelumLunas as $tagihan)
                <article class="border-b border-slate-100 p-4 last:border-b-0 sm:p-5" :class="items[@js((string) $tagihan->id)].selected ? 'bg-brand-50/50' : ''">
                    <div class="flex min-w-0 items-start gap-3"><input type="checkbox" name="tagihan_ids[]" value="{{ $tagihan->id }}" x-model="items[@js((string) $tagihan->id)].selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><div class="min-w-0 flex-1"><div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between"><div class="min-w-0"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $jenisTagihan[$tagihan->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan)) }}</h3><p class="mt-1 text-[11px] text-slate-500">Jatuh tempo {{ $tagihan->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}</p><p class="mt-2 whitespace-nowrap text-xs font-extrabold text-red-700">Sisa Rp {{ number_format($tagihan->sisa_per_item, 0, ',', '.') }}</p></div><label class="block w-full sm:max-w-[220px]"><span class="text-[10px] font-bold uppercase tracking-wide text-slate-500">Bayar sejumlah</span><span class="mt-1 flex h-11 overflow-hidden rounded-xl border bg-white" :class="items[@js((string) $tagihan->id)].amount > items[@js((string) $tagihan->id)].max ? 'border-red-400 ring-2 ring-red-100' : 'border-slate-300'"><span class="flex items-center border-r border-slate-200 px-3 text-xs font-bold text-slate-500">Rp</span><input type="text" inputmode="numeric" name="nominal_bayar[{{ $tagihan->id }}]" x-model="items[@js((string) $tagihan->id)].display" @input.debounce.150ms="updateAmount(@js((string) $tagihan->id), $el.value)" @blur="items[@js((string) $tagihan->id)].display = format(items[@js((string) $tagihan->id)].amount)" @readonly($tagihan->jenis_tagihan === 'spp') class="min-w-0 flex-1 border-0 px-3 text-right text-sm font-extrabold text-slate-900 outline-none focus:ring-0 disabled:bg-slate-50"></span><span x-show="items[@js((string) $tagihan->id)].amount > items[@js((string) $tagihan->id)].max" class="mt-1 block text-[10px] font-bold text-red-600">Nominal melebihi sisa tagihan.</span>@if($tagihan->jenis_tagihan === 'spp')<span class="mt-1 block text-[10px] text-slate-500">SPP tidak dapat dicicil.</span>@endif</label></div></div></div>
                </article>
            @empty
                <div class="px-5 py-14 text-center text-sm font-bold text-emerald-700"><i class="fas fa-circle-check mb-3 block text-4xl text-emerald-300" aria-hidden="true"></i>Semua tagihan pada tahun ajaran aktif sudah lunas.</div>
            @endforelse
            @error('tagihan_ids')<p class="border-t border-red-100 bg-red-50 px-4 py-3 text-xs font-bold text-red-700 sm:px-5">{{ $message }}</p>@enderror
        </section>

        <div class="space-y-5">
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm xl:sticky xl:top-24">
                <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-cash-register text-emerald-600" aria-hidden="true"></i>2. Detail pembayaran tunai</h2><p class="mt-1 text-xs text-slate-500">Periksa tanggal, status validasi, dan total sebelum menyimpan.</p></header>
                <div class="space-y-4 p-4 sm:p-5">
                    <div><span class="text-xs font-bold text-slate-700">Metode pembayaran</span><div class="mt-2 flex min-h-11 items-center gap-2 rounded-xl bg-emerald-50 px-3 text-sm font-bold text-emerald-700 ring-1 ring-inset ring-emerald-100"><i class="fas fa-money-bill-wave" aria-hidden="true"></i>Tunai / loket</div></div>
                    <label class="block text-xs font-bold text-slate-700">Tanggal bayar <span class="text-red-600">*</span><input type="date" name="tanggal_bayar" value="{{ old('tanggal_bayar', date('Y-m-d')) }}" required class="{{ $inputClass }}">@error('tanggal_bayar')<span class="mt-1 block text-[11px] text-red-600">{{ $message }}</span>@enderror</label>
                    <div><span class="text-xs font-bold text-slate-700">Jumlah bayar</span><output class="mt-2 flex min-h-11 items-center rounded-xl bg-brand-50 px-3 text-lg font-extrabold text-brand-700 ring-1 ring-inset ring-brand-100" x-text="`Rp ${format(total())}`">Rp 0</output><p class="mt-1 text-[10px] text-slate-500">Dihitung otomatis dari tagihan terpilih.</p>@error('jumlah_bayar')<span class="mt-1 block text-[11px] text-red-600">{{ $message }}</span>@enderror</div>
                    <label class="block text-xs font-bold text-slate-700">Catatan / keterangan<textarea name="catatan" rows="3" maxlength="500" class="{{ $inputClass }} resize-y" placeholder="Contoh: Dibayar oleh {{ $siswa->waliMurid->name ?? 'wali siswa' }}">{{ old('catatan') }}</textarea><span class="mt-1 block text-[10px] font-normal text-slate-500">Opsional, maksimal 500 karakter.</span></label>
                    <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3"><input type="checkbox" name="validasi_langsung" value="1" checked class="mt-0.5 h-4 w-4 rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500"><span><strong class="block text-xs text-emerald-800"><i class="fas fa-circle-check mr-1" aria-hidden="true"></i>Langsung validasi</strong><span class="mt-1 block text-[10px] leading-4 text-emerald-700">Gunakan hanya jika uang sudah diterima.</span></span></label>
                    <div class="rounded-xl border border-brand-200 bg-brand-50 p-3"><div class="flex items-center justify-between text-xs text-brand-700"><span>Tagihan dipilih</span><strong><span x-text="count()">0</span> item</strong></div><div class="mt-3 flex items-center justify-between border-t border-brand-200 pt-3 text-sm font-extrabold text-brand-800"><span>Total bayar</span><strong x-text="`Rp ${format(total())}`">Rp 0</strong></div></div>
                    <button type="submit" :disabled="!count() || !total() || hasError()" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white shadow-sm hover:bg-brand-700 disabled:cursor-not-allowed disabled:bg-slate-300"><i class="fas fa-floppy-disk" aria-hidden="true"></i>Simpan pembayaran</button>
                </div>
            </section>
        </div>
    </form>

    <section class="flex items-start gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs leading-5 text-amber-800"><i class="fas fa-triangle-exclamation mt-0.5 shrink-0 text-amber-600" aria-hidden="true"></i><p>Pastikan nominal benar dan uang tunai sudah diterima. Pembayaran yang tidak divalidasi belum mengubah status tagihan siswa.</p></section>
</div>
@endsection
