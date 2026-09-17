@extends('layouts.app')

@section('title', 'Arsip Kelas: ' . $kelas->nama_kelas)
@section('page-title', 'Arsip Kelas: ' . $kelas->nama_kelas)
@section('page-subtitle', 'TA ' . ($kelas->tahunAjaran->nama_tahun_ajaran ?? '—') . ' · ' . ($kelas->cabang->nama_cabang ?? '—'))

@section('content')
<div class="min-w-0 w-full space-y-4">
    <header class="rounded-xl border border-sky-200 bg-sky-50 p-4"><div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wide text-sky-700">Mode arsip · baca saja</p><h1 class="mt-1 text-lg font-extrabold text-slate-900">{{ $kelas->nama_kelas }}</h1><p class="mt-1 text-xs text-slate-600">{{ $kelas->jenjang }} · TA {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '—' }} · {{ $kelas->cabang->nama_cabang ?? '—' }}</p></div><a href="{{ route('wali.arsip.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-sky-200 bg-white px-3 text-xs font-bold text-sky-800"><i class="fas fa-arrow-left" aria-hidden="true"></i>Daftar arsip</a></div></header>
    <section class="grid grid-cols-2 gap-2 sm:gap-3 lg:grid-cols-4" aria-label="Ringkasan arsip">
        @foreach(['Siswa tercatat' => $totalSiswa ?? 0, 'Total rapor' => $totalRapor ?? 0, 'Record presensi' => $totalPresensi ?? 0, 'Record nilai' => $totalNilai ?? 0] as $label => $value)
            <div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $label }}</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $value }}</p>@if($label === 'Total rapor')<p class="text-[11px] text-slate-500">{{ $totalRaporTerbit ?? 0 }} diterbitkan</p>@endif</div>
        @endforeach
    </section>
    <nav class="flex gap-1 overflow-x-auto rounded-xl border border-slate-200 bg-white p-1 shadow-sm" aria-label="Bagian arsip">
        @foreach(['siswa' => ['Siswa', 'show'], 'rapor' => ['Rapor', 'rapor'], 'presensi' => ['Presensi', 'presensi'], 'nilai' => ['Nilai', 'nilai']] as $tab => [$label, $route])
            <a href="{{ route('wali.arsip.'.$route, $kelas->id) }}" @if($activeTab === $tab) aria-current="page" @endif class="inline-flex min-h-9 shrink-0 items-center justify-center rounded-lg px-3 text-xs font-bold {{ $activeTab === $tab ? 'bg-sky-700 text-white' : 'text-slate-600 hover:bg-slate-50' }}">{{ $label }}</a>
        @endforeach
    </nav>

    @if($activeTab === 'siswa')
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Siswa yang tercatat</h2></div><div class="divide-y divide-slate-100">
            @forelse($siswaList ?? [] as $siswa)
                @php $snap = $siswa->statusNaikKelas->first(); @endphp
                <article class="grid gap-2 px-4 py-3 text-xs sm:grid-cols-[minmax(180px,1.4fr)_minmax(120px,0.8fr)_minmax(130px,0.8fr)] sm:items-center"><div><h3 class="text-sm font-bold text-slate-900">{{ $siswa->nama_lengkap }}</h3><p class="text-[11px] text-slate-500">NIS {{ $siswa->nis ?: '—' }} · NISN {{ $siswa->nisn ?: '—' }} · {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div><div><span class="block text-[11px] text-slate-500">Status kini</span><strong class="text-slate-800">{{ ucfirst($siswa->status ?? '—') }}</strong></div><div><span class="block text-[11px] text-slate-500">Hasil TA ini</span><strong class="text-slate-800">{{ $snap ? str_replace('_', ' ', $snap->status_kelulusan) : '—' }}</strong>@if($snap?->kelas_tujuan)<span class="block text-[11px] text-slate-500">→ {{ $snap->kelas_tujuan }}</span>@endif</div></article>
            @empty
                <p class="px-4 py-9 text-center text-sm text-slate-500">Belum ada siswa tercatat.</p>
            @endforelse
        </div></section>
    @elseif($activeTab === 'rapor')
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Rapor historis</h2><p class="text-xs text-slate-500">Informasi rapor kelas ini ditampilkan tanpa aksi edit.</p></div><div class="divide-y divide-slate-100">
            @forelse($raporList ?? [] as $rapor)
                <article class="grid gap-2 px-4 py-3 text-xs sm:grid-cols-[minmax(180px,1.4fr)_minmax(140px,0.8fr)_minmax(120px,0.8fr)] sm:items-center"><div><h3 class="text-sm font-bold text-slate-900">{{ $rapor->siswa->nama_lengkap ?? 'Siswa tidak tersedia' }}</h3><p class="text-[11px] text-slate-500">Semester {{ ucfirst($rapor->semester ?? '—') }} · {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }}</p></div><div><span class="block text-[11px] text-slate-500">Status rapor</span><strong class="text-slate-800">{{ ucfirst($rapor->status ?? '—') }}</strong>@if($rapor->status_review_ketua)<span class="block text-[11px] text-slate-500">Ketua: {{ ucfirst($rapor->status_review_ketua) }}</span>@endif</div><p class="text-[11px] text-slate-500">Diubah {{ $rapor->updated_at?->format('d/m/Y H:i') ?? '—' }}</p></article>
            @empty
                <p class="px-4 py-9 text-center text-sm text-slate-500">Belum ada rapor di kelas ini.</p>
            @endforelse
        </div></section>
    @elseif($activeTab === 'presensi')
        <form method="GET" action="{{ route('wali.arsip.presensi', $kelas->id) }}" class="flex flex-wrap items-end gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><label class="min-w-[180px] flex-1 text-xs font-bold text-slate-700">Bulan<select name="bulan" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="">Semua bulan</option>@foreach(($bulanTersedia ?? collect()) as $bln)<option value="{{ $bln }}" @selected(($bulanFilter ?? '') === $bln)>{{ \Carbon\Carbon::createFromFormat('Y-m', $bln)->locale('id')->translatedFormat('F Y') }}</option>@endforeach</select></label><button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white">Terapkan</button><a href="{{ route('wali.arsip.presensi', $kelas->id) }}" class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Reset</a></form>
        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Rekap presensi historis</h2></div><div class="divide-y divide-slate-100">
            @forelse($rekapPresensi ?? [] as $r)
                @php $persen = $r->total > 0 ? round(($r->hadir / $r->total) * 100, 1) : 0; @endphp
                <article class="flex flex-wrap items-center justify-between gap-3 px-4 py-3"><div class="min-w-0"><h3 class="text-sm font-bold text-slate-900">{{ $r->siswa->nama_lengkap ?? 'Siswa #'.$r->siswa_id }}</h3><p class="mt-1 text-[11px] text-slate-500">Hadir {{ $r->hadir }} · Sakit {{ $r->sakit }} · Izin {{ $r->izin }} · Alpha {{ $r->alpha }} · Total {{ $r->total }}</p></div><strong class="text-sm {{ $persen >= 80 ? 'text-emerald-700' : ($persen >= 60 ? 'text-amber-800' : 'text-rose-700') }}">{{ $persen }}%</strong></article>
            @empty
                <p class="px-4 py-9 text-center text-sm text-slate-500">Belum ada record presensi untuk periode ini.</p>
            @endforelse
        </div></section>
    @elseif($activeTab === 'nilai')
        <form method="GET" action="{{ route('wali.arsip.nilai', $kelas->id) }}" class="flex flex-wrap items-end gap-2 rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><label class="min-w-[180px] flex-1 text-xs font-bold text-slate-700">Semester<select name="semester" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="">Semua semester</option>@foreach(($semesterTersedia ?? collect()) as $sm)<option value="{{ $sm }}" @selected(($semesterFilter ?? '') === $sm)>Semester {{ ucfirst($sm) }}</option>@endforeach</select></label><button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white">Terapkan</button><a href="{{ route('wali.arsip.nilai', $kelas->id) }}" class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Reset</a></form>
        @forelse($nilaiBySiswa ?? [] as $siswaId => $nilaiItems)
            @php $siswa = $nilaiItems->first()->siswa; @endphp
            <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">{{ $siswa->nama_lengkap ?? 'Siswa #'.$siswaId }}</h2><p class="text-[11px] text-slate-500">NIS {{ $siswa->nis ?? '—' }}</p></div><div class="divide-y divide-slate-100">
                @foreach($nilaiItems as $n)
                    <article class="grid gap-2 px-4 py-3 text-xs sm:grid-cols-[minmax(150px,1.3fr)_repeat(4,minmax(70px,0.5fr))] sm:items-center"><div><h3 class="font-bold text-slate-900">{{ $n->mataPelajaran->nama_mapel ?? 'Mapel tidak tersedia' }}</h3><p class="text-[11px] text-slate-500">Semester {{ ucfirst($n->semester ?? '—') }}</p></div>@foreach(['Tugas' => 'rata_tugas', 'PTS' => 'pts', 'PAS' => 'pas', 'Akhir' => 'nilai_akhir'] as $label => $field)<div><span class="text-[11px] text-slate-500">{{ $label }}</span><strong class="ml-1 text-slate-900 sm:block sm:ml-0">{{ $n->$field === null ? '—' : number_format((float) $n->$field, 1, ',', '.') }}</strong></div>@endforeach</article>
                @endforeach
            </div></section>
        @empty
            <p class="rounded-xl border border-slate-200 bg-white px-4 py-9 text-center text-sm text-slate-500 shadow-sm">Belum ada record nilai untuk semester ini.</p>
        @endforelse
    @endif
</div>
@endsection
