@extends('layouts.app')

@section('title', 'Presensi')
@section('page-title', 'Presensi Kehadiran')
@section('page-subtitle', 'Lihat rekap presensi kehadiran')

@section('content')
@php
    $statCards = [
        ['label' => 'Hari hadir', 'value' => $rekap['hadir'], 'icon' => 'fa-circle-check', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['label' => 'Hari sakit', 'value' => $rekap['sakit'], 'icon' => 'fa-notes-medical', 'tone' => 'bg-amber-50 text-amber-600'],
        ['label' => 'Hari izin', 'value' => $rekap['izin'], 'icon' => 'fa-envelope-open-text', 'tone' => 'bg-sky-50 text-sky-600'],
        ['label' => 'Hari alpha', 'value' => $rekap['alpha'], 'icon' => 'fa-circle-xmark', 'tone' => 'bg-rose-50 text-rose-600'],
    ];

    $statusTones = [
        'hadir' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'sakit' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'izin' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'alpha' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];
@endphp

<div class="min-w-0 w-full space-y-5"
     x-data="{
        bukti: { type: '', url: '', download: '' },
        lihat(data) { this.bukti = { type: data.type, url: data.url, download: data.download }; this.$refs.buktiDialog.showModal(); },
        reset() { this.bukti = { type: '', url: '', download: '' }; },
     }">

    <header class="flex flex-col gap-1 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:px-5">
        <p class="text-[10px] font-bold uppercase tracking-[0.14em] text-sky-700">Kehadiran saya</p>
        <h1 class="text-lg font-extrabold text-slate-900">Riwayat presensi {{ now()->locale('id')->translatedFormat('F Y') }}</h1>
        <p class="text-xs text-slate-500">Data presensi diperbarui oleh wali kelas setelah pembelajaran. Pengajuan izin dilakukan melalui Wali Siswa.</p>
    </header>

    <section class="grid grid-cols-2 gap-2 sm:gap-3 xl:grid-cols-4" aria-label="Rekap presensi bulan ini">
        @foreach($statCards as $stat)
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
                <div class="flex items-start justify-between gap-2">
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold text-slate-900 sm:text-2xl">{{ number_format($stat['value']) }}</p>
                        <p class="mt-1 text-[10px] font-bold uppercase leading-4 tracking-wide text-slate-500">{{ $stat['label'] }}</p>
                    </div>
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $stat['tone'] }}"><i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i></span>
                </div>
            </article>
        @endforeach
    </section>

    @forelse($presensi as $minggu => $dataList)
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" aria-labelledby="minggu-{{ $minggu }}">
            <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
                <h2 id="minggu-{{ $minggu }}" class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-calendar-week text-brand-600" aria-hidden="true"></i>Minggu ke-{{ $minggu }}</h2>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">{{ $dataList->count() }} catatan</span>
            </header>

            <div class="hidden grid-cols-[11rem_8rem_12rem_minmax(0,1fr)] gap-3 bg-slate-50 px-5 py-2 text-[11px] font-bold uppercase tracking-wide text-slate-500 lg:grid">
                <span>Tanggal</span><span>Hari</span><span>Status</span><span>Keterangan</span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach($dataList as $item)
                    @php
                        if ($item->status_validasi == 'pending') {
                            $statusLabel = 'Menunggu validasi';
                            $statusTone = 'bg-amber-50 text-amber-700 ring-amber-200';
                        } elseif ($item->status_validasi == 'ditolak') {
                            $statusLabel = 'Ditolak';
                            $statusTone = 'bg-rose-50 text-rose-700 ring-rose-200';
                        } else {
                            $statusLabel = ucfirst($item->status);
                            $statusTone = $statusTones[$item->status] ?? 'bg-slate-100 text-slate-600 ring-slate-200';
                        }
                    @endphp
                    <article class="grid min-w-0 grid-cols-[minmax(0,1fr)_auto] items-start gap-x-3 gap-y-2 px-4 py-3 sm:px-5 lg:grid-cols-[11rem_8rem_12rem_minmax(0,1fr)] lg:items-center">
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-slate-900">{{ $item->tanggal->locale('id')->translatedFormat('d F Y') }}</p>
                            <p class="text-xs text-slate-500 lg:hidden">{{ ucfirst($item->tanggal->locale('id')->dayName) }}</p>
                        </div>
                        <p class="hidden text-sm text-slate-500 lg:block">{{ ucfirst($item->tanggal->locale('id')->dayName) }}</p>
                        <div class="lg:order-none">
                            <span class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-extrabold ring-1 ring-inset {{ $statusTone }}">{{ $statusLabel }}</span>
                        </div>
                        <div class="col-span-2 min-w-0 text-xs text-slate-500 lg:col-span-1">
                            <p class="break-words">{{ $item->keterangan ?? 'Tidak ada catatan' }}</p>
                            @if($item->bukti_file)
                                @php
                                    $ext = strtolower(pathinfo($item->bukti_file, PATHINFO_EXTENSION));
                                    $buktiType = $ext === 'pdf' ? 'pdf' : (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'file');
                                @endphp
                                <button type="button"
                                        data-type="{{ $buktiType }}"
                                        data-url="{{ $buktiType === 'pdf' ? preview_url($item->bukti_file) : asset('storage/' . $item->bukti_file) }}"
                                        data-download="{{ asset('storage/' . $item->bukti_file) }}"
                                        @click="lihat($el.dataset)"
                                        class="mt-2 inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-brand-50 px-3 text-xs font-bold text-brand-700 ring-1 ring-inset ring-brand-100 hover:bg-brand-100">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>Lihat bukti
                                </button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @empty
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-calendar-xmark" aria-hidden="true"></i></span>
            <h2 class="mt-4 font-extrabold text-slate-900">Belum ada data presensi bulan ini</h2>
            <p class="mt-1 text-sm text-slate-500">Data presensi akan diperbarui otomatis oleh wali kelas setelah pembelajaran.</p>
        </section>
    @endforelse

    <dialog x-ref="buktiDialog" @close="reset()" @click.self="$el.close()" class="w-[calc(100%-2rem)] max-w-3xl rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-paperclip text-brand-600" aria-hidden="true"></i>Bukti presensi</h2>
            <button type="button" @click="$refs.buktiDialog.close()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
        <div class="max-h-[70vh] overflow-auto bg-slate-50">
            <template x-if="bukti.type === 'pdf'"><iframe :src="bukti.url" title="Pratinjau bukti PDF" class="h-[65vh] w-full border-0"></iframe></template>
            <template x-if="bukti.type === 'image'"><img :src="bukti.url" alt="Pratinjau bukti" class="mx-auto max-h-[65vh] w-auto max-w-full object-contain p-3"></template>
            <template x-if="bukti.type === 'file'"><p class="px-5 py-12 text-center text-sm text-slate-500">File ini tidak dapat dipratinjau. Silakan unduh untuk membukanya.</p></template>
        </div>
        <div class="flex justify-end gap-2 border-t border-slate-100 px-5 py-4">
            <button type="button" @click="$refs.buktiDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Tutup</button>
            <a :href="bukti.download" download class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fa-solid fa-download" aria-hidden="true"></i>Unduh</a>
        </div>
    </dialog>
</div>
@endsection
