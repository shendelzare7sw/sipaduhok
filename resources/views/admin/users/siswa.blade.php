@extends('layouts.app')

@section('title', 'Data Siswa')
@section('page-title', 'Data Siswa')
@section('page-subtitle', 'Kelola siswa aktif, alumni, dan penempatan kelas')

@section('content')
@php
    $hasFilters = request()->hasAny(['search', 'jenjang', 'kelas_id', 'kelas_nama', 'cabang_id', 'status']);
    $statusTone = ['aktif' => 'bg-emerald-50 text-emerald-700', 'lulus' => 'bg-blue-50 text-blue-700', 'pindah' => 'bg-amber-50 text-amber-700', 'keluar' => 'bg-red-50 text-red-700'];
@endphp

<div class="min-w-0 space-y-5">
    @if(session('hapus_siswa_konfirmasi'))
        @php $konf = session('hapus_siswa_konfirmasi'); @endphp
        <section class="rounded-2xl border-2 border-red-300 bg-red-50 p-4 text-red-900 sm:p-5"><h2 class="text-sm font-extrabold"><i class="fas fa-triangle-exclamation mr-1.5" aria-hidden="true"></i>Konfirmasi terakhir: hapus permanen</h2><p class="mt-2 text-xs leading-5">Anda akan menghapus <strong>{{ $konf['nama'] }}</strong> beserta seluruh data keuangan, akademik, dan akun login:</p><ul class="mt-2 list-disc space-y-1 pl-5 text-xs">@foreach($konf['blockers'] as $blocker)<li>{{ $blocker }}</li>@endforeach</ul><div class="mt-4 flex flex-wrap gap-2"><form action="{{ route('admin.users.delete-siswa', $konf['id']) }}" method="POST" data-confirm data-confirm-title="Hapus permanen?" data-confirm-message="Seluruh data siswa tidak dapat dikembalikan." data-confirm-text="Ya, hapus permanen">@csrf @method('DELETE')<input type="hidden" name="konfirmasi_permanen" value="1"><button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-red-600 px-4 text-xs font-bold text-white hover:bg-red-700"><i class="fas fa-trash" aria-hidden="true"></i>Hapus Permanen</button></form><a href="{{ route('admin.users.siswa') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-white px-4 text-xs font-bold text-slate-700 no-underline ring-1 ring-slate-200">Batal</a></div></section>
    @endif

    @if(session('import_warnings'))<section class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-xs text-amber-900"><p class="font-extrabold"><i class="fas fa-exclamation-triangle mr-1.5" aria-hidden="true"></i>Beberapa data import dilewati</p><ul class="mt-2 list-disc space-y-1 pl-5">@foreach(session('import_warnings') as $warning)<li>{{ $warning }}</li>@endforeach</ul></section>@endif

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between"><div class="flex min-w-0 items-center gap-3"><a href="{{ route('admin.users.index') }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600 no-underline hover:bg-slate-200"><i class="fas fa-arrow-left" aria-hidden="true"></i></a><div class="min-w-0"><h2 class="text-base font-extrabold text-slate-900">Daftar Siswa</h2><p class="mt-1 text-xs text-slate-500">{{ $siswa->total() }} siswa ditemukan</p></div></div><div class="grid grid-cols-4 gap-2 sm:flex"><a href="{{ route('admin.users.siswa.print') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200" title="Cetak"><i class="fas fa-print" aria-hidden="true"></i><span class="hidden sm:inline">Cetak</span></a><a href="{{ route('admin.users.siswa-template') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-cyan-50 px-3 text-xs font-bold text-cyan-700 no-underline hover:bg-cyan-100" title="Template"><i class="fas fa-download" aria-hidden="true"></i><span class="hidden sm:inline">Template</span></a><a href="{{ route('admin.users.import-siswa') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-emerald-50 px-3 text-xs font-bold text-emerald-700 no-underline hover:bg-emerald-100" title="Import"><i class="fas fa-file-import" aria-hidden="true"></i><span class="hidden sm:inline">Import</span></a><a href="{{ route('admin.users.create-siswa') }}" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fas fa-plus" aria-hidden="true"></i><span class="hidden sm:inline">Tambah</span></a></div></div>

<form action="{{ route('admin.users.siswa') }}" method="GET" class="mt-4 grid gap-2 sm:grid-cols-2 xl:grid-cols-[minmax(220px,1fr)_repeat(4,minmax(130px,0.4fr))_auto]"><label class="relative"><span class="sr-only">Cari siswa</span><i class="fas fa-search pointer-events-none absolute left-3 top-3.5 text-xs text-slate-400" aria-hidden="true"></i><input type="search" name="search" value="{{ request('search') }}" placeholder="Nama, NIS, atau NISN..." class="h-10 w-full rounded-xl border border-slate-200 !pl-10 pr-3 text-xs outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></label><select name="cabang_id" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700"><option value="">Semua Cabang</option>@foreach($cabangList as $cabang)<option value="{{ $cabang->id }}" {{ request('cabang_id') == $cabang->id ? 'selected' : '' }}>{{ $cabang->nama_cabang }}</option>@endforeach</select><select name="jenjang" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700"><option value="">Semua Jenjang</option>@foreach($jenjangs as $jenjang)<option value="{{ $jenjang }}" {{ request('jenjang') === $jenjang ? 'selected' : '' }}>{{ $jenjang }}</option>@endforeach</select><select name="kelas_id" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700"><option value="">Semua Kelas</option>@foreach($kelasList as $kelas)<option value="{{ $kelas->id }}" @selected((string) request('kelas_id') === (string) $kelas->id)>{{ $kelas->nama_kelas }} · {{ $kelas->jenjang }} · {{ $kelas->cabang->nama_cabang ?? 'Cabang tidak tersedia' }}</option>@endforeach</select><select name="status" class="h-10 rounded-xl border border-slate-200 bg-white px-3 text-xs text-slate-700"><option value="">Semua Status</option>@foreach(['aktif', 'lulus', 'pindah', 'keluar'] as $status)<option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>@endforeach</select><div class="flex gap-2"><button type="submit" class="inline-flex h-10 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-800 px-4 text-xs font-bold text-white"><i class="fas fa-filter" aria-hidden="true"></i>Filter</button>@if($hasFilters)<a href="{{ route('admin.users.siswa') }}" class="inline-flex h-10 items-center justify-center rounded-xl bg-red-50 px-3 text-red-700 no-underline"><i class="fas fa-times" aria-hidden="true"></i></a>@endif</div></form>
        </header>

        <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50 px-4 py-2.5"><label class="flex cursor-pointer items-center gap-2 text-[11px] font-bold text-slate-600"><input type="checkbox" data-bulk-select-all="siswa-bulk-form" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Pilih semua halaman ini</label><form id="siswa-bulk-form" action="{{ route('admin.users.bulk-delete-siswa') }}" method="POST" data-confirm data-confirm-title="Hapus siswa terpilih?" data-confirm-message="Data terkait juga dapat ikut terhapus." data-confirm-text="Ya, hapus data">@csrf<button type="submit" data-bulk-submit="siswa-bulk-form" disabled class="inline-flex h-8 items-center justify-center gap-1.5 rounded-lg bg-red-600 px-3 text-[10px] font-bold text-white disabled:cursor-not-allowed disabled:opacity-40"><i class="fas fa-trash" aria-hidden="true"></i>Hapus (<span data-bulk-count>0</span>)</button></form></div>

        <div class="divide-y divide-slate-100 lg:hidden">@forelse($siswa as $s)<article class="p-4"><div class="flex min-w-0 items-start gap-3"><input type="checkbox" name="ids[]" value="{{ $s->id }}" form="siswa-bulk-form" data-bulk-item class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><div class="min-w-0 flex-1"><div class="flex items-start justify-between gap-2"><div class="min-w-0"><h3 class="truncate text-sm font-extrabold text-slate-900">{{ $s->user->name ?? $s->nama_lengkap }}</h3><p class="mt-1 truncate text-[11px] text-slate-500">NIS {{ $s->nis }} · NISN {{ $s->nisn }}</p></div><span class="shrink-0 whitespace-nowrap rounded-full px-2.5 py-1 text-[9px] font-bold {{ $statusTone[$s->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($s->status) }}</span></div><div class="mt-3 grid grid-cols-2 gap-2 rounded-xl bg-slate-50 p-3 text-[11px]"><div class="min-w-0"><p class="text-slate-400">Kelas</p><p class="mt-0.5 truncate whitespace-nowrap font-semibold text-slate-700">{{ $s->kelas->nama_kelas ?? ($s->status === 'lulus' ? 'Lulus' : 'Belum masuk kelas') }} <span class="ml-1 font-medium text-slate-400">{{ $s->kelas->jenjang ?? '' }}</span></p></div><div class="min-w-0"><p class="text-slate-400">Cabang</p><p class="mt-0.5 truncate font-semibold text-slate-700">{{ $s->cabang->nama_cabang ?? '-' }}</p></div></div><div class="mt-3 grid grid-cols-3 gap-2"><a href="{{ route('admin.users.show-siswa', $s->id) }}" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 no-underline"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a><a href="{{ route('admin.users.edit-siswa', $s->id) }}" class="inline-flex h-9 items-center justify-center gap-1 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a><form action="{{ route('admin.users.delete-siswa', $s->id) }}" method="POST" data-confirm data-confirm-title="Hapus siswa?" data-confirm-message="{{ $s->nama_lengkap }} dan akun loginnya akan dihapus." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<button type="submit" class="h-9 w-full rounded-lg bg-red-50 text-xs font-bold text-red-700"><i class="fas fa-trash mr-1" aria-hidden="true"></i>Hapus</button></form></div></div></div></article>@empty<div class="p-12 text-center text-xs text-slate-500"><i class="fas fa-user-graduate mb-3 block text-3xl text-slate-300" aria-hidden="true"></i>{{ $hasFilters ? 'Tidak ada siswa yang sesuai filter.' : 'Belum ada data siswa.' }}</div>@endforelse</div>

        @if($siswa->isNotEmpty())
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full min-w-[1080px] table-fixed text-left text-sm">
                    <colgroup>
                        <col class="w-12">
                        <col class="w-12">
                        <col class="w-[22%]">
                        <col class="w-[16%]">
                        <col class="w-28">
                        <col>
                        <col class="w-24">
                        <col class="w-32">
                    </colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="px-3 py-3"><span class="sr-only">Pilih</span></th>
                            <th class="px-2 py-3 text-center">No</th>
                            <th class="px-3 py-3">Nama Siswa</th>
                            <th class="px-3 py-3">NIS / NISN</th>
                            <th class="px-3 py-3 whitespace-nowrap">Kelas</th>
                            <th class="px-3 py-3">Cabang</th>
                            <th class="px-3 py-3">Status</th>
                            <th class="px-3 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($siswa as $index => $s)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-3 py-3"><input type="checkbox" name="ids[]" value="{{ $s->id }}" form="siswa-bulk-form" data-bulk-item class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td>
                                <td class="px-2 py-3 text-center text-xs tabular-nums text-slate-400">{{ $siswa->firstItem() + $index }}</td>
                                <td class="px-3 py-3"><p class="truncate font-bold text-slate-800" title="{{ $s->user->name ?? $s->nama_lengkap }}">{{ $s->user->name ?? $s->nama_lengkap }}</p><p class="mt-0.5 text-[11px] text-slate-500">{{ $s->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></td>
                                <td class="px-3 py-3 text-xs text-slate-600"><p class="truncate">{{ $s->nis }}</p><p class="truncate text-slate-400">{{ $s->nisn }}</p></td>
                                <td class="px-3 py-3 text-xs text-slate-600"><p class="whitespace-nowrap font-semibold">{{ $s->kelas->nama_kelas ?? ($s->status === 'lulus' ? 'Lulus' : 'Belum masuk') }} <span class="ml-1 font-medium text-slate-400">{{ $s->kelas->jenjang ?? '' }}</span></p></td>
                                <td class="px-3 py-3 text-xs text-slate-600"><p class="truncate" title="{{ $s->cabang->nama_cabang ?? '-' }}">{{ $s->cabang->nama_cabang ?? '-' }}</p></td>
                                <td class="px-3 py-3"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[9px] font-bold {{ $statusTone[$s->status] ?? 'bg-slate-100 text-slate-600' }}">{{ ucfirst($s->status) }}</span></td>
                                <td class="px-3 py-3"><div class="flex justify-end gap-1.5"><x-cleanflow.table-action href="{{ route('admin.users.show-siswa', $s->id) }}" tone="view" icon="fas fa-eye" label="Detail siswa" /><x-cleanflow.table-action href="{{ route('admin.users.edit-siswa', $s->id) }}" tone="edit" icon="fas fa-edit" label="Edit siswa" /><form action="{{ route('admin.users.delete-siswa', $s->id) }}" method="POST" data-confirm data-confirm-title="Hapus siswa?" data-confirm-message="{{ $s->nama_lengkap }} dan akun loginnya akan dihapus." data-confirm-text="Ya, hapus">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus siswa" /></form></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
        @if($siswa->hasPages())<footer class="border-t border-slate-200 px-4 py-3">{{ $siswa->appends(request()->except('page'))->links() }}</footer>@endif
    </section>
</div>
@endsection
