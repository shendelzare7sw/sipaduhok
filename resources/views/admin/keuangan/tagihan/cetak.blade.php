@extends('layouts.print')

@section('title', 'Rincian Tagihan - '.$siswa->nama_lengkap)
@section('back-url', route('admin.keuangan.tagihan.show', $siswa->id))
@section('document-width', 'max-w-5xl mx-auto')
@section('report-title', 'Rincian Tagihan Siswa')

@section('report-meta')
    <p class="mt-1 text-xs font-semibold text-slate-600 print:text-[8pt]">Tahun Ajaran {{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</p>
@endsection

@section('report-content')
    @php $isPaid = $sisaTagihan <= 0 && $totalTagihan > 0; @endphp

    <section class="grid grid-cols-2 gap-x-8 gap-y-3 border-y border-slate-300 py-4 text-sm print:text-[9pt]">
        <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">Nama siswa</span><strong class="mt-1 block text-slate-950">{{ $siswa->nama_lengkap }}</strong></div>
        <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">NISN</span><strong class="mt-1 block text-slate-950">{{ $siswa->nisn ?: '-' }}</strong></div>
        <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">Kelas</span><strong class="mt-1 block text-slate-950">{{ $siswa->kelas->nama_kelas ?? '-' }} · {{ $siswa->kelas->jenjang ?? '-' }}</strong></div>
        <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">Cabang</span><strong class="mt-1 block text-slate-950">{{ $siswa->cabang->nama_cabang ?? '-' }}</strong></div>
    </section>

    <table class="mt-5 w-full table-fixed border-collapse text-sm print:mt-4 print:text-[8pt]">
        <colgroup><col class="w-10"><col><col class="w-40"><col class="w-32"><col class="w-28"></colgroup>
        <thead class="bg-slate-100"><tr><th class="border border-slate-400 px-2 py-2 text-center">No</th><th class="border border-slate-400 px-3 py-2 text-left">Jenis tagihan</th><th class="border border-slate-400 px-3 py-2 text-right">Jumlah</th><th class="border border-slate-400 px-3 py-2 text-center">Jatuh tempo</th><th class="border border-slate-400 px-3 py-2 text-center">Status</th></tr></thead>
        <tbody>
            @forelse($tagihan as $index => $item)
                <tr class="break-inside-avoid"><td class="border border-slate-300 px-2 py-2 text-center">{{ $index + 1 }}</td><td class="border border-slate-300 px-3 py-2 font-semibold text-slate-900">{{ $jenisTagihan[$item->jenis_tagihan] ?? ucwords(str_replace('_', ' ', $item->jenis_tagihan)) }}</td><td class="border border-slate-300 px-3 py-2 text-right font-bold tabular-nums">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td><td class="border border-slate-300 px-3 py-2 text-center">{{ $item->tanggal_jatuh_tempo?->format('d/m/Y') ?? '-' }}</td><td class="border border-slate-300 px-3 py-2 text-center font-bold {{ $item->status === 'sudah_bayar' ? 'text-emerald-700' : 'text-red-700' }}">{{ $item->status === 'sudah_bayar' ? 'LUNAS' : 'BELUM' }}</td></tr>
            @empty
                <tr><td colspan="5" class="border border-slate-300 px-3 py-8 text-center text-slate-500">Belum ada tagihan pada periode ini.</td></tr>
            @endforelse
        </tbody>
        <tfoot class="bg-slate-100"><tr><th colspan="2" class="border border-slate-400 px-3 py-2 text-right">TOTAL TAGIHAN</th><td class="border border-slate-400 px-3 py-2 text-right font-extrabold tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td><td colspan="2" class="border border-slate-400"></td></tr></tfoot>
    </table>

    <section class="mt-5 ml-auto w-full max-w-sm border border-slate-300 text-sm print:mt-4 print:text-[8pt]"><h2 class="border-b border-slate-300 bg-slate-100 px-3 py-2 font-extrabold">Ringkasan pembayaran</h2><dl class="divide-y divide-slate-200"><div class="flex justify-between gap-4 px-3 py-2"><dt>Total tagihan</dt><dd class="font-bold tabular-nums">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</dd></div><div class="flex justify-between gap-4 px-3 py-2"><dt>Total terbayar</dt><dd class="font-bold tabular-nums text-emerald-700">Rp {{ number_format($tagihanLunas, 0, ',', '.') }}</dd></div><div class="flex justify-between gap-4 px-3 py-2"><dt class="font-extrabold">Sisa tagihan</dt><dd class="font-extrabold tabular-nums {{ $isPaid ? 'text-emerald-700' : 'text-red-700' }}">Rp {{ number_format($sisaTagihan, 0, ',', '.') }}</dd></div></dl></section>
@endsection

@section('report-footer')
    <footer class="mt-8 grid grid-cols-2 gap-12 text-center text-[10px] leading-5 print:mt-6 print:text-[8pt]"><div><p>Orang Tua/Wali Siswa</p><div class="h-16"></div><p class="border-t border-slate-900 pt-1">( ................................ )</p></div><div><p>Tangerang Selatan, {{ now()->locale('id')->translatedFormat('d F Y') }}</p><p>Bendahara</p><div class="h-12"></div><p class="border-t border-slate-900 pt-1">( ................................ )</p></div></footer>
@endsection
