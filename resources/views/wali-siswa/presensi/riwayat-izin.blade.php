@extends('layouts.app')

@section('title', 'Riwayat Pengajuan Izin - ' . $siswa->nama_lengkap)
@section('page-title', 'Riwayat Pengajuan Izin')

@section('content')
<div class="min-w-0 w-full space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Riwayat Pengajuan Izin</h4>
            <p class="mt-0.5 text-sm text-slate-500">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-1">·</span>
                <i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <a href="{{ route('wali-siswa.presensi.anak', $siswa->id) }}" class="inline-flex items-center gap-1.5 self-start rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    @if($pengajuanIzin->count() > 0)
        {{-- Riwayat List --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
                <i class="fa-solid fa-history text-brand-600"></i>
                <h5 class="text-base font-semibold text-slate-800">Riwayat Pengajuan Izin ({{ $pengajuanIzin->count() }})</h5>
            </div>
            <div>
                @foreach($pengajuanIzin as $index => $presensi)
                    @php
                        $statusValidasi = $presensi->status_validasi;
                        if (!$statusValidasi) {
                            if (str_contains($presensi->keterangan ?? '', 'ditolak')) {
                                $statusValidasi = 'ditolak';
                            } elseif (str_contains($presensi->keterangan ?? '', 'Divalidasi')) {
                                $statusValidasi = 'disetujui';
                            }
                        }

                        $isValidated = in_array($statusValidasi, ['disetujui', 'ditolak']);
                        $isApproved = $statusValidasi === 'disetujui';
                        $isRejected = $statusValidasi === 'ditolak';

                        $buktiPath = $presensi->bukti_file;
                        if (!$buktiPath && preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan ?? '', $matches)) {
                            $buktiPath = $matches[1];
                        }

                        $keteranganText = preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan ?? '-');
                        $buktiUrl = $buktiPath ? asset('storage/' . $buktiPath) : null;
                        $buktiExtension = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                        $isBuktiImage = in_array($buktiExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                        $isBuktiPdf = $buktiExtension === 'pdf';

                        $statusBadgeClasses = $isRejected
                            ? 'bg-red-100 text-red-700'
                            : ($presensi->status == 'sakit' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700');
                        $statusIconBgClasses = $isRejected
                            ? 'bg-red-50 text-red-500'
                            : ($presensi->status == 'sakit' ? 'bg-amber-50 text-amber-500' : 'bg-blue-50 text-blue-500');
                    @endphp

                    <div class="border-b border-slate-200 p-4 last:border-b-0 {{ $index % 2 == 0 ? 'bg-white' : 'bg-slate-50/50' }}"
                         x-data="{ showBukti: false }">
                        <div class="flex flex-col gap-3 md:flex-row">
                            {{-- Left: Details --}}
                            <div class="flex flex-1 items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $statusIconBgClasses }}">
                                    <i class="fa-solid {{ $isRejected ? 'fa-times' : ($presensi->status == 'sakit' ? 'fa-notes-medical' : 'fa-file-alt') }}"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="mb-2 flex flex-wrap items-center gap-1.5">
                                        <span class="rounded-full px-2.5 py-0.5 text-[11px] font-bold {{ $statusBadgeClasses }}">
                                            {{ strtoupper($presensi->status) }}
                                        </span>
                                        @if($isValidated)
                                            @if($isApproved)
                                                <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[11px] font-bold text-emerald-700"><i class="fa-solid fa-check mr-0.5"></i> Disetujui</span>
                                            @elseif($isRejected)
                                                <span class="rounded-full bg-red-100 px-2.5 py-0.5 text-[11px] font-bold text-red-700"><i class="fa-solid fa-times mr-0.5"></i> Ditolak</span>
                                            @endif
                                        @else
                                            <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[11px] font-bold text-amber-700"><i class="fa-solid fa-clock mr-0.5"></i> Menunggu Validasi</span>
                                        @endif
                                    </div>
                                    <div class="mb-1 text-xs text-slate-500">
                                        <i class="fa-solid fa-calendar mr-1"></i>
                                        <strong>Tanggal:</strong>
                                        {{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        <i class="fa-solid fa-comment mr-1"></i>
                                        <strong>Keterangan:</strong>
                                        {{ $keteranganText }}
                                    </div>

                                    @if($buktiPath)
                                        <button type="button" @click="showBukti = true"
                                                class="mt-2 inline-flex items-center gap-1.5 rounded-lg border border-brand-300 px-3 py-1.5 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                            <i class="fa-solid fa-paperclip"></i> Lihat Bukti
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="shrink-0 md:w-44">
                                @if(!$isValidated)
                                    <a href="{{ route('wali-siswa.presensi.edit-izin', $presensi->id) }}"
                                       class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-amber-500 px-3 py-2 text-xs font-semibold text-white transition hover:bg-amber-600">
                                        <i class="fa-solid fa-edit"></i> Edit Pengajuan
                                    </a>
                                @else
                                    <div class="rounded-lg {{ $isApproved ? 'border border-emerald-200 bg-emerald-50' : 'border border-red-200 bg-red-50' }} p-3 text-center">
                                        <p class="text-xs font-bold {{ $isApproved ? 'text-emerald-700' : 'text-red-700' }}">{{ $isApproved ? 'Sudah Disetujui' : 'Ditolak' }}</p>
                                        <p class="mt-0.5 text-[10px] text-slate-500">Tidak dapat diedit lagi</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Alpine Dialog for Bukti --}}
                        @if($buktiPath)
                            <template x-teleport="body">
                                <div x-show="showBukti" x-cloak
                                     class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4"
                                     @keydown.escape.window="showBukti = false">
                                    <div @click.outside="showBukti = false"
                                         x-show="showBukti" x-transition
                                         class="relative w-full max-w-4xl overflow-hidden rounded-xl bg-white shadow-2xl">
                                        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-3">
                                            <h5 class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                                                <i class="fa-solid fa-paperclip text-brand-600"></i> Lampiran Bukti
                                            </h5>
                                            <button @click="showBukti = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        </div>
                                        <div class="p-0">
                                            @if($isBuktiImage)
                                                <div class="p-4 text-center">
                                                    <img src="{{ $buktiUrl }}" alt="Lampiran bukti {{ $siswa->nama_lengkap }}" class="mx-auto max-h-[75vh] rounded-lg object-contain">
                                                </div>
                                            @elseif($isBuktiPdf)
                                                <iframe src="{{ $buktiUrl }}" title="Lampiran bukti {{ $siswa->nama_lengkap }}" class="h-[75vh] w-full border-0"></iframe>
                                            @else
                                                <div class="px-5 py-12 text-center">
                                                    <i class="fa-solid fa-file text-5xl text-slate-300"></i>
                                                    <p class="mt-3 text-sm text-slate-500">Format lampiran tidak dapat dipreview.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </template>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @else
        {{-- Empty State --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="px-6 py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-400">
                    <i class="fa-solid fa-inbox text-2xl"></i>
                </div>
                <h5 class="font-bold text-slate-700">Belum Ada Pengajuan Izin</h5>
                <p class="mt-1 text-sm text-slate-500">Anda belum pernah mengajukan izin untuk {{ $siswa->nama_lengkap }}</p>
                <a href="{{ route('wali-siswa.presensi.ajukan-izin', $siswa->id) }}" class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                    <i class="fa-solid fa-plus"></i> Ajukan Izin Sekarang
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
