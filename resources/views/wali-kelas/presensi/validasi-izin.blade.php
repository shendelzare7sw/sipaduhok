@extends('layouts.app')

@section('title', 'Validasi Izin Ketidakhadiran')
@section('page-title', 'Validasi Izin')
@section('page-subtitle', 'Tinjau pengajuan sakit dan izin siswa')

@section('content')
<div class="min-w-0 w-full space-y-4" x-data="{ rejectAction: '', rejectName: '', rejectDate: '', previewUrl: '', previewType: '', previewName: '' }">
    <header class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm"><div><h1 class="text-lg font-extrabold text-slate-900">Validasi izin</h1><p class="mt-0.5 text-xs text-slate-500">Periksa keterangan dan bukti sebelum mengambil keputusan.</p></div><a href="{{ route('wali.presensi.index') }}" class="inline-flex min-h-9 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a></header>

    @if($error ?? false)<p class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800" role="alert">{{ $error }}</p>@endif

    <div class="inline-flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3"><span class="flex h-9 w-9 items-center justify-center rounded-lg bg-white text-amber-700"><i class="fas fa-clock" aria-hidden="true"></i></span><div><p class="text-[11px] font-bold uppercase text-amber-800">Menunggu keputusan</p><p class="text-lg font-extrabold leading-none text-amber-900">{{ $pengajuanPending->count() }}</p></div></div>

    @if($pengajuanPending->isEmpty())
        <section class="rounded-xl border border-slate-200 bg-white px-4 py-9 text-center shadow-sm"><i class="fas fa-circle-check text-2xl text-emerald-500" aria-hidden="true"></i><h2 class="mt-3 text-sm font-bold text-slate-900">Tidak ada pengajuan tertunda</h2><p class="mt-1 text-xs text-slate-500">Semua pengajuan izin telah diproses.</p></section>
    @else
        <section class="space-y-3" aria-label="Pengajuan izin tertunda">
            @foreach($pengajuanPending as $presensi)
                @php
                    $buktiPath = $presensi->bukti_file;
                    $keteranganText = $presensi->keterangan ?? '-';
                    if (! $buktiPath && preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan ?? '', $matches)) {
                        $buktiPath = $matches[1];
                        $keteranganText = trim(preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan));
                    }
                    $extension = strtolower(pathinfo($buktiPath ?? '', PATHINFO_EXTENSION));
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
                    $isPdf = $extension === 'pdf';
                    $buktiUrl = $buktiPath ? ($presensi->bukti_file ? route('wali.presensi.preview-bukti', $presensi->id) : asset('storage/'.$buktiPath)) : null;
                @endphp
                <article class="grid min-w-0 gap-4 rounded-xl border border-slate-200 bg-white px-4 py-4 shadow-sm lg:grid-cols-[minmax(0,1fr)_180px]">
                    <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><h2 class="text-sm font-extrabold text-slate-900">{{ $presensi->siswa->nama_lengkap ?? 'Siswa tidak ditemukan' }}</h2><span class="rounded-full px-2 py-1 text-[11px] font-bold {{ $presensi->status === 'sakit' ? 'bg-amber-50 text-amber-800' : 'bg-sky-50 text-sky-800' }}">{{ ucfirst($presensi->status) }}</span></div><p class="mt-1 text-xs text-slate-500">NIS {{ $presensi->siswa->nis ?? '-' }} · {{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->translatedFormat('d F Y') }}</p><p class="mt-3 break-words text-xs leading-5 text-slate-700">{{ $keteranganText ?: '-' }}</p>@if($presensi->inputBy)<p class="mt-2 text-[11px] text-slate-500">Diajukan oleh {{ $presensi->inputBy->name }}</p>@endif
                        @if($buktiUrl)
                            <div class="mt-3 flex flex-wrap gap-2"><button type="button" data-url="{{ $buktiUrl }}" data-type="{{ $isImage ? 'image' : ($isPdf ? 'pdf' : 'other') }}" data-name="{{ $presensi->siswa->nama_lengkap ?? 'Bukti izin' }}" @click="previewUrl = $el.dataset.url; previewType = $el.dataset.type; previewName = $el.dataset.name; if (previewType !== 'other') $refs.previewDialog.showModal(); else window.open(previewUrl, '_blank', 'noopener')" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-800"><i class="fas fa-eye" aria-hidden="true"></i>Lihat bukti</button><a href="{{ $buktiUrl }}" download class="inline-flex min-h-9 items-center gap-1.5 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-download" aria-hidden="true"></i>Unduh</a></div>
                        @endif
                    </div>
                    <div class="grid grid-cols-2 gap-2 lg:grid-cols-1 lg:content-center"><form action="{{ route('wali.presensi.proses-validasi-izin', $presensi->id) }}" method="POST">@csrf<input type="hidden" name="status" value="setuju"><button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-1.5 rounded-lg bg-emerald-700 px-3 text-xs font-bold text-white hover:bg-emerald-800"><i class="fas fa-check" aria-hidden="true"></i>Setujui</button></form><button type="button" data-action="{{ route('wali.presensi.proses-validasi-izin', $presensi->id) }}" data-name="{{ $presensi->siswa->nama_lengkap ?? '-' }}" data-date="{{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->translatedFormat('d F Y') }}" @click="rejectAction = $el.dataset.action; rejectName = $el.dataset.name; rejectDate = $el.dataset.date; $refs.rejectDialog.showModal()" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-lg bg-rose-50 px-3 text-xs font-bold text-rose-800 hover:bg-rose-100"><i class="fas fa-ban" aria-hidden="true"></i>Tolak</button></div>
                </article>
            @endforeach
        </section>
    @endif

    <dialog x-ref="previewDialog" class="w-[calc(100%-2rem)] max-w-4xl rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/70" @close="previewUrl = ''" @click.self="$el.close()"><div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3"><h2 class="truncate text-sm font-extrabold text-slate-900">Bukti · <span x-text="previewName"></span></h2><button type="button" @click="$refs.previewDialog.close()" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-500" aria-label="Tutup"><i class="fas fa-xmark" aria-hidden="true"></i></button></div><div class="max-h-[75vh] overflow-auto p-3"><template x-if="previewType === 'image'"><img :src="previewUrl" alt="Bukti izin" class="mx-auto max-h-[70vh] max-w-full rounded-lg object-contain"></template><template x-if="previewType === 'pdf'"><iframe :src="previewUrl" title="Bukti izin PDF" class="h-[70vh] w-full rounded-lg border border-slate-200"></iframe></template></div></dialog>

    <dialog x-ref="rejectDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-5 py-4"><h2 class="text-base font-extrabold text-slate-900">Tolak pengajuan izin?</h2><p class="mt-1 text-xs text-slate-500"><span x-text="rejectName"></span> · <span x-text="rejectDate"></span></p></div><form :action="rejectAction" method="POST" class="space-y-4 px-5 py-4">@csrf<input type="hidden" name="status" value="tolak"><p class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs leading-5 text-rose-800">Penolakan akan mengubah status kehadiran menjadi <strong>Alpha</strong> dan mengirimkan pemberitahuan kepada wali siswa.</p><label class="block text-xs font-bold text-slate-700">Alasan penolakan (opsional)<textarea name="keterangan" rows="3" placeholder="Jelaskan alasan agar mudah dipahami wali siswa" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm text-slate-800"></textarea></label><div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.rejectDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-4 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-rose-700 px-4 text-xs font-bold text-white">Ya, tolak</button></div></form></dialog>
</div>
@endsection
