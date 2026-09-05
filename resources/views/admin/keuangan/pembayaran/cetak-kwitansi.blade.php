@extends('layouts.print')

@section('title', 'Kwitansi Pembayaran - '.$pembayaran->kode_pembayaran)
@section('back-url', route('admin.keuangan.pembayaran.show', $pembayaran))
@section('document-width', 'max-w-4xl mx-auto')
@section('report-title', 'Kwitansi Pembayaran')

@section('document-header')
    <div class="border-b-2 border-slate-900 pb-4 text-center">
        <h2 class="text-lg font-extrabold uppercase tracking-wide text-slate-950 print:text-[14pt]">
            {{ preg_replace('/\s*\(Gedung\s+\w+\)$/i', '', $schoolInfo['nama']) }}
        </h2>
        <p class="mt-1 text-xs leading-5 text-slate-700 print:text-[9pt]">{{ $schoolInfo['alamat'] }}</p>
        <p class="text-xs text-slate-700 print:text-[9pt]">Telp. {{ $schoolInfo['telepon'] }} · {{ $schoolInfo['email'] }}</p>
    </div>
@endsection

@section('report-meta')
    <p class="mt-1 text-xs font-semibold text-slate-600 print:text-[8pt]">Nomor {{ $pembayaran->kode_pembayaran }}</p>
@endsection

@section('report-content')
    @php
        $statusClasses = match ($pembayaran->status_validasi) {
            'disetujui' => 'bg-emerald-100 text-emerald-800',
            'ditolak' => 'bg-rose-100 text-rose-800',
            default => 'bg-amber-100 text-amber-800',
        };
    @endphp

    <div data-payment-receipt class="grid gap-5 sm:grid-cols-2 print:grid-cols-2">
        <section class="rounded-xl border border-slate-200 p-4 print:rounded-none print:border-slate-400">
            <h2 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 print:text-[8pt]">Informasi pembayaran</h2>
            <dl class="mt-3 grid grid-cols-[7.5rem_minmax(0,1fr)] gap-x-3 gap-y-2 text-sm print:text-[9pt]">
                <dt class="text-slate-500">Tanggal bayar</dt>
                <dd class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->locale('id')->translatedFormat('d F Y') }}</dd>
                <dt class="text-slate-500">Metode</dt>
                <dd class="font-semibold text-slate-900">{{ $pembayaran->payment_channel_label }}</dd>
                <dt class="text-slate-500">Status</dt>
                <dd><span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-extrabold {{ $statusClasses }} print:border print:border-slate-500 print:bg-white print:text-slate-900">{{ ucfirst($pembayaran->status_validasi) }}</span></dd>
            </dl>
        </section>

        <section class="rounded-xl border border-slate-200 p-4 print:rounded-none print:border-slate-400">
            <h2 class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 print:text-[8pt]">Identitas siswa</h2>
            <dl class="mt-3 grid grid-cols-[5rem_minmax(0,1fr)] gap-x-3 gap-y-2 text-sm print:text-[9pt]">
                <dt class="text-slate-500">Nama</dt>
                <dd class="font-bold text-slate-950">{{ $pembayaran->siswa->nama_lengkap ?? '-' }}</dd>
                <dt class="text-slate-500">NIS</dt>
                <dd class="font-semibold text-slate-900">{{ $pembayaran->siswa->nis ?? '-' }}</dd>
                <dt class="text-slate-500">Kelas</dt>
                <dd class="font-semibold text-slate-900">{{ $pembayaran->siswa->kelas->nama_kelas ?? '-' }}</dd>
            </dl>
        </section>
    </div>

    <section class="mt-5 overflow-hidden rounded-xl border border-slate-300 print:rounded-none">
        <table class="w-full table-fixed border-collapse text-sm print:text-[9pt]">
            <colgroup><col><col class="w-40 sm:w-48"></colgroup>
            <thead class="bg-slate-100">
                <tr>
                    <th class="border-b border-slate-300 px-4 py-3 text-left text-[11px] font-extrabold uppercase tracking-wider text-slate-600">Keterangan</th>
                    <th class="border-b border-slate-300 px-4 py-3 text-right text-[11px] font-extrabold uppercase tracking-wider text-slate-600">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach($relatedPayments as $payment)
                    <tr class="break-inside-avoid">
                        <td class="px-4 py-3 align-top">
                            <p class="font-bold text-slate-950">
                                {{ $jenisTagihan[$payment->tagihan->jenis_tagihan ?? ''] ?? ucfirst(str_replace('_', ' ', $payment->tagihan->jenis_tagihan ?? 'Tagihan')) }}
                                @if($payment->tagihan && $payment->tagihan->bulan)
                                    · {{ \Carbon\Carbon::create()->month($payment->tagihan->bulan)->locale('id')->translatedFormat('F') }}
                                @endif
                            </p>
                            <p class="mt-1 text-xs leading-5 text-slate-600 print:text-[8pt]">
                                Total tagihan Rp {{ number_format($payment->tagihan->jumlah ?? 0, 0, ',', '.') }} ·
                                @if($payment->is_lunas)
                                    <span class="font-bold text-emerald-700 print:text-slate-900">Lunas</span>
                                @else
                                    Sisa <span class="font-bold text-rose-700 print:text-slate-900">Rp {{ number_format($payment->sisa_current, 0, ',', '.') }}</span>
                                @endif
                            </p>
                        </td>
                        <td class="px-4 py-3 text-right align-top font-extrabold tabular-nums text-slate-950">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-slate-900 text-white print:bg-white print:text-slate-950">
                <tr>
                    <th class="px-4 py-3 text-right text-xs font-extrabold uppercase tracking-wider">Total dibayar</th>
                    <td class="px-4 py-3 text-right text-base font-black tabular-nums print:text-[11pt]">Rp {{ number_format($totalBayar, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    </section>

    <section class="mt-5 rounded-xl bg-slate-100 p-4 text-sm print:rounded-none print:border print:border-slate-400 print:bg-white print:text-[9pt]">
        <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 print:text-[8pt]">Terbilang</p>
        <p class="mt-1 font-bold italic text-slate-950">{{ terbilang($totalBayar) }} rupiah</p>
        @if($pembayaran->catatan)
            <div class="mt-3 border-t border-slate-200 pt-3 print:border-slate-400">
                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-500 print:text-[8pt]">Catatan</p>
                <p class="mt-1 text-slate-800">{{ $pembayaran->catatan }}</p>
            </div>
        @endif
    </section>
@endsection

@section('report-footer')
    <footer class="mt-8 text-[11px] leading-5 text-slate-700 print:mt-6 print:text-[8pt]">
        <div class="grid grid-cols-2 gap-10 text-center">
            <div>
                <p>Penerima,</p>
                <div class="h-16 print:h-14"></div>
                <p class="border-t border-slate-900 pt-1 font-bold text-slate-950">{{ $pembayaran->validator->nama_lengkap ?? 'Bendahara' }}</p>
            </div>
            <div>
                <p>Pembayar,</p>
                <div class="h-16 print:h-14"></div>
                <p class="border-t border-slate-900 pt-1 font-bold text-slate-950">{{ $parentName }}</p>
            </div>
        </div>
        <div class="mt-6 border-t border-slate-200 pt-3 text-center print:border-slate-400">
            <p class="font-semibold text-slate-900">Kwitansi ini merupakan bukti pembayaran yang sah.</p>
            <p>
                Dicetak {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                @if($pembayaran->tanggal_validasi)
                    · Divalidasi {{ \Carbon\Carbon::parse($pembayaran->tanggal_validasi)->locale('id')->translatedFormat('d F Y, H:i') }} WIB
                @endif
            </p>
        </div>
    </footer>
@endsection
