@extends('layouts.app')

@section('title', 'Rapor Pending Saya')
@section('page-title', 'Rapor Pending Saya')
@section('page-subtitle', 'Draft dan revisi dari kelas yang pernah Anda walikan')

@section('content')
<div class="min-w-0 w-full space-y-4">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h1 class="text-lg font-extrabold text-slate-900">Rapor yang perlu ditindaklanjuti</h1><p class="mt-1 text-xs text-slate-500">Termasuk rapor tahun ajaran lalu dari kelas yang pernah Anda walikan.</p></header>
    @if($kelasIds->isEmpty())<p class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900">Anda belum pernah ditugaskan sebagai wali kelas. Daftar ini masih kosong.</p>@endif
    <section class="grid grid-cols-3 gap-2 sm:gap-3" aria-label="Ringkasan status rapor">
        @foreach(['Draft' => [$totalDraft, 'text-slate-900'], 'Menunggu Ketua' => [$totalKirim, 'text-sky-800'], 'Perlu revisi' => [$totalRevisi, 'text-amber-800']] as $label => [$value, $tone])
            <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $label }}</p><p class="mt-1 text-xl font-extrabold {{ $tone }}">{{ $value }}</p></div>
        @endforeach
    </section>
    @if($raporList->isEmpty())
        <div class="rounded-xl border border-slate-200 bg-white px-4 py-10 text-center shadow-sm"><i class="fas fa-circle-check text-xl text-emerald-600" aria-hidden="true"></i><h2 class="mt-2 text-sm font-bold text-slate-900">Tidak ada rapor pending</h2><p class="mt-1 text-xs text-slate-500">Semua rapor sudah selesai atau belum ada draft yang dibuat.</p></div>
    @else
        @foreach($raporList->groupBy('kelas_id') as $kelasId => $rapors)
            @php $kelas = $rapors->first()->kelas; @endphp
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 bg-slate-50 px-4 py-3"><div><h2 class="text-sm font-extrabold text-slate-900">{{ $kelas?->nama_kelas ?? 'Kelas tidak tersedia' }}</h2><p class="text-[11px] text-slate-500">TA {{ $kelas?->tahunAjaran?->nama_tahun_ajaran ?? '—' }}{{ $kelas?->tahunAjaran?->is_active ? ' · Aktif' : '' }}</p></div><span class="rounded-full bg-white px-2 py-1 text-[11px] font-bold text-slate-600">{{ $rapors->count() }} rapor</span></div>
                <div class="divide-y divide-slate-100">
                    @foreach($rapors as $rapor)
                        @php
                            $review = $rapor->status_review_ketua;
                            $label = match($review) { 'revisi' => 'Perlu revisi', 'pending' => 'Menunggu Ketua', default => 'Draft' };
                            $tone = match($review) { 'revisi' => 'bg-amber-50 text-amber-900', 'pending' => 'bg-sky-50 text-sky-800', default => 'bg-slate-100 text-slate-700' };
                        @endphp
                        <article class="flex flex-wrap items-start justify-between gap-3 px-4 py-3"><div class="min-w-0 flex-1"><h3 class="text-sm font-bold text-slate-900">{{ $rapor->siswa?->nama_lengkap ?? 'Siswa #'.$rapor->siswa_id }}</h3><p class="mt-0.5 text-[11px] text-slate-500">Semester {{ ucfirst($rapor->semester ?? '—') }} · {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }}@if($rapor->updated_at) · Diubah {{ $rapor->updated_at->locale('id')->translatedFormat('d M Y, H:i') }}@endif</p>@if($rapor->catatan_revisi_ketua)<p class="mt-2 rounded-lg bg-amber-50 px-3 py-2 text-xs text-amber-900"><strong>Catatan Ketua:</strong> {{ \Illuminate\Support\Str::limit($rapor->catatan_revisi_ketua, 200) }}</p>@endif</div><div class="flex w-full items-center justify-between gap-2 sm:w-auto"><span class="rounded-full px-2 py-1 text-[11px] font-bold {{ $tone }}">{{ $label }}</span><a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Buka rapor</a></div></article>
                    @endforeach
                </div>
            </section>
        @endforeach
    @endif
</div>
@endsection
