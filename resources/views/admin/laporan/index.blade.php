@extends('layouts.app')

@section('title', 'Pusat Laporan')
@section('page-title', 'Pusat Laporan')
@section('page-subtitle', 'Pilih data dan cakupan sebelum membuka dokumen cetak')

@section('content')
@php
    $routePrefix = $routePrefix ?? 'admin.laporan';
    $supportsAcademic = $supportsAcademic ?? false;
    $classOptions = $kelasList->map(fn ($kelas) => ['id' => (string) $kelas->id, 'name' => $kelas->nama_kelas, 'level' => $kelas->jenjang, 'branchId' => (string) $kelas->cabang_id, 'branch' => $kelas->cabang->nama_cabang ?? 'Cabang belum diatur'])->values();
    $fieldClass = 'h-10 w-full rounded-xl border border-slate-300 bg-white px-3 text-xs text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100';
    $cards = [
        ['key' => 'siswa', 'title' => 'Daftar siswa', 'desc' => 'Data siswa menurut periode, lokasi, dan kelas.', 'icon' => 'fa-user-graduate', 'tone' => 'bg-blue-50 text-blue-700'],
        ['key' => 'tenaga-pendidik', 'title' => 'Tenaga pendidik', 'desc' => 'Daftar guru, wali kelas, dan staf sekolah.', 'icon' => 'fa-chalkboard-user', 'tone' => 'bg-emerald-50 text-emerald-700'],
        ['key' => 'kelas', 'title' => 'Daftar kelas', 'desc' => 'Kelas, wali, jumlah siswa, dan kapasitas.', 'icon' => 'fa-school', 'tone' => 'bg-violet-50 text-violet-700'],
        ['key' => 'wali-kelas', 'title' => 'Wali kelas', 'desc' => 'Penugasan wali dan jumlah siswa per kelas.', 'icon' => 'fa-user-tie', 'tone' => 'bg-amber-50 text-amber-700'],
        ['key' => 'guru-pengajar', 'title' => 'Guru pengajar', 'desc' => 'Guru beserta kelas dan mata pelajaran.', 'icon' => 'fa-person-chalkboard', 'tone' => 'bg-cyan-50 text-cyan-700'],
        ['key' => 'rekap', 'title' => 'Rekap statistik', 'desc' => 'Ringkasan sekolah per cabang dan jenjang.', 'icon' => 'fa-chart-column', 'tone' => 'bg-rose-50 text-rose-700'],
    ];
@endphp

<div data-admin-report-center class="min-w-0 w-full space-y-4" x-data="{ classes: @js($classOptions), studentBranch: '', studentLevel: '', studentClass: '', get studentLevels() { return [...new Set(this.classes.filter(item => !this.studentBranch || item.branchId === this.studentBranch).map(item => item.level))].sort(); }, get studentClasses() { return this.classes.filter(item => (!this.studentBranch || item.branchId === this.studentBranch) && (!this.studentLevel || item.level === this.studentLevel)); }, resetStudentLevel() { this.studentLevel = ''; this.studentClass = ''; }, resetStudentClass() { this.studentClass = ''; } }">
    <section class="grid grid-cols-2 gap-3 xl:grid-cols-4">
        @foreach([['Siswa aktif', $stats['totalSiswa'], 'fa-user-graduate', 'bg-blue-50 text-blue-700'], ['Tenaga pendidik', $stats['totalGuru'], 'fa-chalkboard-user', 'bg-emerald-50 text-emerald-700'], ['Kelas aktif', $stats['totalKelas'], 'fa-school', 'bg-violet-50 text-violet-700'], ['Cabang aktif', $stats['totalCabang'], 'fa-building', 'bg-amber-50 text-amber-700']] as [$label, $value, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm"><div class="flex items-start justify-between gap-3"><div class="min-w-0"><strong class="block text-xl font-extrabold tabular-nums text-slate-950">{{ $value }}</strong><span class="mt-1 block truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</span></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}"></i></span></div></article>
        @endforeach
    </section>

    <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5"><div class="flex items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="fas fa-route"></i></span><div><h2 class="text-lg font-extrabold text-slate-950">Pilih dokumen yang dibutuhkan</h2><p class="mt-1 text-sm leading-6 text-slate-500">Tentukan filter di dalam kartu, lalu pratinjau dokumen pada tab baru sebelum dicetak.</p></div></div></section>

    <section class="grid gap-4 lg:grid-cols-2">
        @foreach($cards as $card)
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <header class="flex items-start gap-3 border-b border-slate-200 p-4 sm:p-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $card['tone'] }}"><i class="fas {{ $card['icon'] }}"></i></span><div><h3 class="font-extrabold text-slate-950">{{ $card['title'] }}</h3><p class="mt-1 text-xs leading-5 text-slate-500">{{ $card['desc'] }}</p></div></header>
                <form action="{{ route($routePrefix.'.'.$card['key']) }}" method="GET" target="_blank" class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5">
                    @if($card['key'] === 'siswa')
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Tahun ajaran</span><select name="tahun_ajaran_id" class="{{ $fieldClass }}">@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected($tahunAjaranAktif?->id === $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' · Aktif' : '' }}</option>@endforeach</select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Status siswa</span><select name="status" class="{{ $fieldClass }}"><option value="aktif">Aktif</option><option value="lulus">Lulus / alumni</option></select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select name="cabang_id" x-model="studentBranch" @change="resetStudentLevel" class="{{ $fieldClass }}"><option value="">Semua cabang</option>@foreach($cabangs as $branch)<option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>@endforeach</select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Jenjang</span><select name="jenjang" x-model="studentLevel" @change="resetStudentClass" class="{{ $fieldClass }}"><option value="">Semua jenjang</option><template x-for="level in studentLevels" :key="level"><option :value="level" x-text="level"></option></template></select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Kelas</span><select name="kelas_id" x-model="studentClass" class="{{ $fieldClass }}"><option value="">Semua kelas</option><template x-for="item in studentClasses" :key="item.id"><option :value="item.id" x-text="`${item.name} · ${item.level} · ${item.branch}`"></option></template></select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Urutan</span><select name="sort_by" class="{{ $fieldClass }}"><option value="nama">Nama (abjad)</option><option value="kelas">Per kelas</option><option value="cabang">Per cabang</option></select></label>
                    @elseif($card['key'] === 'tenaga-pendidik')
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Peran</span><select name="role" class="{{ $fieldClass }}"><option value="">Semua peran</option><option value="wali_kelas">Wali Kelas</option><option value="guru_pengajar">Guru Pengajar</option><option value="bendahara">Bendahara</option><option value="sekretaris">Sekretaris</option></select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Status akun</span><select name="status" class="{{ $fieldClass }}"><option value="aktif">Aktif</option><option value="nonaktif">Nonaktif</option></select></label>
                        <label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold text-slate-600">Urutan</span><select name="sort_by" class="{{ $fieldClass }}"><option value="nama">Nama (abjad)</option><option value="nip">NIP</option></select></label>
                    @elseif(in_array($card['key'], ['kelas', 'wali-kelas']))
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Tahun ajaran</span><select name="tahun_ajaran_id" class="{{ $fieldClass }}">@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected($tahunAjaranAktif?->id === $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' · Aktif' : '' }}</option>@endforeach</select></label>
                        <label><span class="mb-1 block text-xs font-bold text-slate-600">Jenjang</span><select name="jenjang" class="{{ $fieldClass }}"><option value="">Semua jenjang</option>@foreach(['PAUD','SD','SMP','SMA'] as $level)<option value="{{ $level }}">{{ $level }}</option>@endforeach</select></label>
                        @if($card['key'] === 'kelas')<label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select name="cabang_id" class="{{ $fieldClass }}"><option value="">Semua cabang</option>@foreach($cabangs as $branch)<option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>@endforeach</select></label>@endif
                    @else
                        <label class="sm:col-span-2"><span class="mb-1 block text-xs font-bold text-slate-600">Tahun ajaran</span><select name="tahun_ajaran_id" class="{{ $fieldClass }}">@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected($tahunAjaranAktif?->id === $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' · Aktif' : '' }}</option>@endforeach</select></label>
                    @endif
                    <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700 sm:col-span-2"><i class="fas fa-print"></i>Buka pratinjau</button>
                </form>
            </article>
        @endforeach

        @if($supportsAcademic)
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"><header class="flex items-start gap-3 border-b border-slate-200 p-4 sm:p-5"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-700"><i class="fas fa-arrow-trend-up"></i></span><div><h3 class="font-extrabold text-slate-950">Rekap akademik per TA</h3><p class="mt-1 text-xs leading-5 text-slate-500">Snapshot kenaikan, kelulusan, tunggakan, dan dispensasi.</p></div></header><form action="{{ route($routePrefix.'.rekap-akademik') }}" method="GET" target="_blank" class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5"><label><span class="mb-1 block text-xs font-bold text-slate-600">Tahun ajaran</span><select name="tahun_ajaran_id" class="{{ $fieldClass }}">@foreach($tahunAjarans as $ta)<option value="{{ $ta->id }}" @selected($tahunAjaranAktif?->id === $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' · Aktif' : '' }}</option>@endforeach</select></label><label><span class="mb-1 block text-xs font-bold text-slate-600">Cabang</span><select name="cabang_id" class="{{ $fieldClass }}"><option value="">Semua cabang</option>@foreach($cabangs as $branch)<option value="{{ $branch->id }}">{{ $branch->nama_cabang }}</option>@endforeach</select></label><button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700 sm:col-span-2"><i class="fas fa-print"></i>Buka pratinjau</button></form></article>
        @endif
    </section>
</div>
@endsection
