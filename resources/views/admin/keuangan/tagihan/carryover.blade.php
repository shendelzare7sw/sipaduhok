@extends('layouts.app')

@section('title', 'Tarik Tunggakan')
@section('page-title', 'Tarik Tunggakan')
@section('page-subtitle', 'Alihkan tunggakan lama ke tahun ajaran aktif dengan aman')

@section('content')
@php
    $baseRouteName = 'admin.keuangan.tagihan';
    $detailData = [];

    foreach ($kandidat as $row) {
        $tagihanList = [];

        foreach ($row['perTahun'] as $kelompok) {
            foreach ($kelompok['tagihan'] as $tagihan) {
                $tagihanList[] = [
                    'ta' => $kelompok['tahun_ajaran']->nama_tahun_ajaran ?? '-',
                    'jenis' => ucwords(str_replace('_', ' ', $tagihan->jenis_tagihan ?? '')),
                    'keterangan' => $tagihan->keterangan ?: '-',
                    'sisa' => (float) $tagihan->sisa,
                ];
            }
        }

        $detailData[(string) $row['siswa']->id] = [
            'nama' => $row['siswa']->nama_lengkap,
            'total' => (float) $row['totalTunggakan'],
            'jumlah' => $row['jumlahItem'],
            'tagihan' => $tagihanList,
        ];
    }

    $candidateIds = $kandidat->map(fn ($row) => (string) $row['siswa']->id)->values();
@endphp

<div
    data-tagihan-carryover
    data-preview-url="{{ route($baseRouteName . '.carryover.preview') }}"
    class="min-w-0 w-full space-y-5"
    x-data="{
        selected: [],
        pageIds: @js($candidateIds),
        details: @js($detailData),
        busy: false,
        get allSelected() { return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id)); },
        toggleAll() { this.selected = this.allSelected ? [] : [...this.pageIds]; },
        format(value) { return new Intl.NumberFormat('id-ID').format(Number(value || 0)); },
        escape(value) { const node = document.createElement('span'); node.textContent = String(value ?? ''); return node.innerHTML; },
        async showDetail(id) {
            const data = this.details[String(id)];
            if (!data) return;
            const rows = data.tagihan.map(item => `<div class='flex items-start justify-between gap-4 border-b border-slate-100 py-3 text-left last:border-0'><div class='min-w-0'><strong class='block text-sm text-slate-900'>${this.escape(item.jenis)}</strong><span class='mt-1 block text-xs text-slate-500'>${this.escape(item.ta)} · ${this.escape(item.keterangan)}</span></div><strong class='shrink-0 text-sm tabular-nums text-slate-950'>Rp ${this.format(item.sisa)}</strong></div>`).join('');
            await Swal.fire({ title: this.escape(data.nama), html: `<div class='mb-3 rounded-xl bg-blue-50 p-3 text-left text-sm text-blue-900'><strong>${data.jumlah} tagihan</strong> · Total Rp ${this.format(data.total)}</div><div class='max-h-72 overflow-y-auto'>${rows}</div>`, confirmButtonText: 'Tutup', confirmButtonColor: '#285dcc', width: 640 });
        },
        async previewCarryover() {
            if (!this.selected.length) {
                await Swal.fire({ icon: 'warning', title: 'Pilih siswa dahulu', text: 'Pilih minimal satu siswa yang tunggakannya akan dialihkan.', confirmButtonColor: '#285dcc' });
                return;
            }

            this.busy = true;
            Swal.fire({ title: 'Menyiapkan pratinjau', text: 'Sistem sedang menghitung tagihan yang akan dibuat.', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            try {
                const payload = new FormData();
                payload.append('_token', @js(csrf_token()));
                this.selected.forEach(id => payload.append('siswa_ids[]', id));
                const response = await fetch(this.$root.dataset.previewUrl, { method: 'POST', body: payload, headers: { Accept: 'application/json' } });
                const data = await response.json();
                if (!response.ok) throw new Error(data.message || data.error || 'Pratinjau gagal dimuat.');

                const rows = data.items.map(item => `<div class='flex items-start justify-between gap-4 border-b border-slate-100 py-3 text-left last:border-0'><div class='min-w-0'><strong class='block text-sm text-slate-900'>${this.escape(item.siswa_nama)}</strong><span class='mt-1 block text-xs text-slate-500'>${this.escape(item.asal_tahun_ajaran)} · ${this.escape(item.keterangan_baru)}</span></div><strong class='shrink-0 text-sm tabular-nums text-slate-950'>Rp ${this.format(item.jumlah)}</strong></div>`).join('');
                const result = await Swal.fire({ icon: 'info', title: 'Pratinjau carryover', html: `<div class='grid grid-cols-3 gap-2 rounded-xl bg-slate-50 p-3 text-center'><div><strong class='block text-lg text-slate-950'>${data.total_siswa}</strong><span class='text-xs text-slate-500'>Siswa</span></div><div><strong class='block text-lg text-slate-950'>${data.total_tagihan}</strong><span class='text-xs text-slate-500'>Tagihan</span></div><div><strong class='block text-sm text-slate-950'>Rp ${this.format(data.grand_total)}</strong><span class='text-xs text-slate-500'>Total</span></div></div><p class='my-3 text-left text-xs text-slate-500'>Tujuan: <strong>${this.escape(data.tujuan_tahun_ajaran_nama)}</strong></p><div class='max-h-64 overflow-y-auto'>${rows}</div>`, showCancelButton: true, confirmButtonText: 'Lanjutkan', cancelButtonText: 'Periksa lagi', confirmButtonColor: '#285dcc', reverseButtons: true, width: 720 });
                if (result.isConfirmed) await this.confirmExecute();
            } catch (error) {
                await Swal.fire({ icon: 'error', title: 'Pratinjau gagal', text: error.message || 'Terjadi kesalahan. Silakan coba lagi.', confirmButtonColor: '#285dcc' });
            } finally {
                this.busy = false;
            }
        },
        async confirmExecute() {
            const result = await Swal.fire({ icon: 'warning', title: 'Alihkan tunggakan?', html: `Sistem akan membuat tagihan baru untuk <strong>${this.selected.length} siswa</strong>. Tagihan lama tetap disimpan sebagai riwayat.`, showCancelButton: true, confirmButtonText: 'Ya, alihkan', cancelButtonText: 'Batal', confirmButtonColor: '#285dcc', reverseButtons: true });
            if (result.isConfirmed) this.$refs.form.submit();
        }
    }"
>
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route($baseRouteName . '.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left"></i>Kembali ke tagihan</a>
        <span class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700"><i class="fas fa-calendar-check"></i>Tujuan {{ $taAktif->nama_tahun_ajaran }}</span>
    </div>

    <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4">
        <div class="flex items-start gap-3 text-sm leading-6 text-blue-900"><i class="fas fa-circle-info mt-1 text-blue-700"></i><p><strong>Urutan aman:</strong> pastikan tagihan reguler pada tahun ajaran aktif sudah tersedia, pilih siswa, periksa rincian, lalu buka pratinjau. Carryover menambah tagihan baru tanpa menghapus riwayat lama.</p></div>
    </section>

    <section class="grid grid-cols-2 gap-3 lg:grid-cols-3">
        <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-700"><i class="fas fa-calendar-check"></i></span><div class="min-w-0"><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">TA tujuan</span><strong class="mt-1 block truncate text-base text-slate-950 sm:text-lg">{{ $taAktif->nama_tahun_ajaran }}</strong></div></article>
        <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700"><i class="fas fa-users"></i></span><div class="min-w-0"><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">Siswa menunggak</span><strong class="mt-1 block text-lg text-slate-950">{{ $totalSiswa }} <small class="text-xs font-semibold text-slate-500">siswa</small></strong></div></article>
        <article class="col-span-2 flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-1"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-700"><i class="fas fa-money-bill-wave"></i></span><div class="min-w-0"><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">Total tunggakan</span><strong class="mt-1 block truncate text-lg tabular-nums text-slate-950">Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></div></article>
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div><div class="flex items-center gap-2"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="fas fa-arrow-right-arrow-left"></i></span><h2 class="text-lg font-extrabold text-slate-950">Kandidat tunggakan</h2></div><p class="mt-2 text-sm text-slate-500">Pilih hanya siswa yang tagihannya sudah siap dialihkan.</p></div>
                <form method="GET" action="{{ route($baseRouteName . '.carryover') }}" class="flex min-w-0 gap-2 sm:w-auto"><label class="min-w-0 flex-1 sm:w-72"><span class="sr-only">Filter cabang</span><select name="cabang_id" @change="$el.form.submit()" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100"><option value="">Semua cabang</option>@foreach($cabangList as $cabang)<option value="{{ $cabang->id }}" @selected($selectedCabangId == $cabang->id)>{{ $cabang->nama_cabang }}</option>@endforeach</select></label>@if($selectedCabangId)<a href="{{ route($baseRouteName . '.carryover') }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-700 no-underline hover:bg-slate-50">Reset</a>@endif</form>
            </div>
        </header>

        @if($kandidat->isEmpty())
            <div class="flex min-h-72 flex-col items-center justify-center px-5 py-12 text-center"><span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-600"><i class="fas fa-check"></i></span><h3 class="mt-4 text-base font-extrabold text-slate-950">Tidak ada tunggakan</h3><p class="mt-1 max-w-md text-sm leading-6 text-slate-500">Semua siswa pada cabang ini sudah lunas atau tunggakannya telah dialihkan.</p></div>
        @else
            <form x-ref="form" method="POST" action="{{ route($baseRouteName . '.carryover.execute') }}">
                @csrf
                <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3 sm:px-5"><button type="button" @click="toggleAll" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-white px-3 text-xs font-bold text-brand-700 ring-1 ring-slate-200 hover:bg-brand-50"><i class="fas fa-check-double"></i><span x-text="allSelected ? 'Batalkan semua' : 'Pilih semua'"></span></button><span class="text-xs font-semibold text-slate-500"><strong class="text-slate-900" x-text="selected.length"></strong> dari {{ $totalSiswa }} siswa dipilih</span></div>

                <div class="divide-y divide-slate-100 lg:hidden">
                    @foreach($kandidat as $row)
                        @php $siswa = $row['siswa']; $sid = (string) $siswa->id; @endphp
                        <article class="p-4" :class="selected.includes(@js($sid)) && 'bg-brand-50/50'">
                            <div class="flex items-start gap-3"><input type="checkbox" name="siswa_ids[]" value="{{ $sid }}" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><div class="min-w-0 flex-1"><strong class="block text-sm text-slate-950">{{ $siswa->nama_lengkap }}</strong><span class="mt-1 block text-xs leading-5 text-slate-500">NIS {{ $siswa->nis ?: '-' }} · {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }} · {{ $siswa->cabang->nama_cabang ?? 'Cabang belum diatur' }}</span><div class="mt-3 flex flex-wrap gap-1">@foreach($row['perTahun'] as $kelompok)<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600">{{ $kelompok['tahun_ajaran']->nama_tahun_ajaran ?? 'TA -' }}</span>@endforeach @if($siswa->status === 'lulus')<span class="rounded-lg bg-violet-50 px-2 py-1 text-[10px] font-bold text-violet-700">ALUMNI</span>@endif</div><div class="mt-3 flex items-end justify-between gap-3"><div><strong class="block text-sm tabular-nums text-slate-950">Rp {{ number_format($row['totalTunggakan'], 0, ',', '.') }}</strong><span class="text-xs text-slate-500">{{ $row['jumlahItem'] }} tagihan</span></div><button type="button" @click="showDetail(@js($sid))" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-blue-50 px-3 text-xs font-bold text-blue-700 hover:bg-blue-100"><i class="fas fa-eye"></i>Detail</button></div></div></div>
                        </article>
                    @endforeach
                </div>

                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full table-fixed border-collapse text-left">
                        <colgroup><col class="w-14"><col class="w-[42%]"><col class="w-[25%]"><col class="w-[23%]"><col class="w-16"></colgroup>
                        <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-5 py-3"><span class="sr-only">Pilih</span></th><th class="px-4 py-3">Siswa</th><th class="px-4 py-3">TA sumber</th><th class="px-4 py-3 text-right">Tunggakan</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($kandidat as $row)
                                @php $siswa = $row['siswa']; $sid = (string) $siswa->id; @endphp
                                <tr class="transition hover:bg-slate-50" :class="selected.includes(@js($sid)) && 'bg-brand-50/50'"><td class="px-5 py-4 align-top"><input type="checkbox" name="siswa_ids[]" value="{{ $sid }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td><td class="px-4 py-4 align-top"><strong class="block truncate text-sm text-slate-950">{{ $siswa->nama_lengkap }}</strong><span class="mt-1 block truncate text-xs text-slate-500">NIS {{ $siswa->nis ?: '-' }} · {{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }} · {{ $siswa->cabang->nama_cabang ?? 'Cabang belum diatur' }}</span>@if($siswa->status === 'lulus')<span class="mt-2 inline-flex rounded-lg bg-violet-50 px-2 py-1 text-[10px] font-bold text-violet-700">ALUMNI</span>@endif</td><td class="px-4 py-4 align-top"><div class="flex flex-wrap gap-1">@foreach($row['perTahun'] as $kelompok)<span class="rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-600">{{ $kelompok['tahun_ajaran']->nama_tahun_ajaran ?? 'TA -' }}</span>@endforeach</div></td><td class="px-4 py-4 text-right align-top"><strong class="block whitespace-nowrap text-sm tabular-nums text-slate-950">Rp {{ number_format($row['totalTunggakan'], 0, ',', '.') }}</strong><span class="mt-1 block text-xs text-slate-500">{{ $row['jumlahItem'] }} tagihan</span></td><td class="px-5 py-4 text-right align-top"><x-cleanflow.table-action type="button" tone="view" icon="fas fa-eye" label="Lihat rincian tunggakan" data-detail-id="{{ $sid }}" x-on:click="showDetail($el.dataset.detailId)" /></td></tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <footer class="flex flex-col gap-3 border-t border-slate-200 bg-white p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5"><p class="text-xs leading-5 text-slate-500"><strong class="text-slate-800" x-text="selected.length"></strong> siswa akan diperiksa kembali melalui pratinjau sebelum diproses.</p><button type="button" @click="previewCarryover" :disabled="!selected.length || busy" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white hover:bg-brand-700 disabled:cursor-not-allowed disabled:opacity-45"><i class="fas fa-eye"></i><span x-text="busy ? 'Menyiapkan...' : 'Pratinjau carryover'"></span></button></footer>
            </form>
        @endif
    </section>
</div>
@endsection
