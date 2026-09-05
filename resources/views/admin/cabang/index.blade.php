@extends('layouts.app')

@section('title', 'Manajemen Cabang')
@section('page-title', 'Data Cabang')
@section('page-subtitle', 'Kelola lokasi dan unit PKBM House of Knowledge')

@section('content')
@php
    $statItems = [
        ['value' => number_format($stats['totalCabang']), 'label' => 'Total Cabang', 'meta' => 'Semua unit terdaftar', 'icon' => 'fa-building', 'tone' => 'bg-blue-50 text-blue-600'],
        ['value' => number_format($stats['cabangAktif']), 'label' => 'Aktif Beroperasi', 'meta' => 'Dapat digunakan', 'icon' => 'fa-check-circle', 'tone' => 'bg-emerald-50 text-emerald-600'],
        ['value' => number_format($stats['cabangNonAktif']), 'label' => 'Nonaktif', 'meta' => 'Tidak beroperasi', 'icon' => 'fa-pause-circle', 'tone' => 'bg-red-50 text-red-600'],
        ['value' => number_format($stats['totalSiswaSemuaCabang']), 'label' => 'Total Siswa', 'meta' => 'Di seluruh cabang', 'icon' => 'fa-user-graduate', 'tone' => 'bg-violet-50 text-violet-600'],
    ];
@endphp

<div class="min-w-0 space-y-5">
    <x-cleanflow.stat-grid :items="$statItems" />

    @if(request('status') || request('search'))
        <section class="flex flex-col gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 sm:flex-row sm:items-center sm:justify-between">
            <p class="min-w-0"><i class="fas fa-filter mr-2 text-blue-600" aria-hidden="true"></i>Filter aktif <span class="ml-1 inline-flex rounded-full bg-blue-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ $cabangs->total() }} data</span></p>
            <a href="{{ route('admin.cabang.index') }}" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-white px-3 text-xs font-bold text-blue-700 no-underline ring-1 ring-blue-200 hover:bg-blue-100"><i class="fas fa-times" aria-hidden="true"></i>Reset filter</a>
        </section>
    @endif

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="min-w-0">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fas fa-list text-brand-600" aria-hidden="true"></i>Daftar Cabang</h2>
                <p class="mt-1 text-xs text-slate-500">Lokasi, status, dan jumlah data pada setiap unit.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.cabang.index') }}" method="GET" class="min-w-0 flex-1 sm:flex-none">
                    <label for="cabang-status" class="sr-only">Filter status cabang</label>
                    <select id="cabang-status" name="status" data-auto-submit class="h-10 w-full rounded-xl border border-slate-200 bg-white pl-3 pr-8 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 sm:w-40">
                        <option value="">Semua Status</option>
                        <option value="aktif" {{ request('status') === 'aktif' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="nonaktif" {{ request('status') === 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </form>
                <a href="{{ route('admin.cabang.create') }}" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white no-underline hover:bg-brand-700 sm:px-4"><i class="fas fa-plus" aria-hidden="true"></i><span class="hidden sm:inline">Cabang Baru</span><span class="sm:hidden">Tambah</span></a>
            </div>
        </header>

        <div class="divide-y divide-slate-100 md:hidden">
            @forelse($cabangs as $cabang)
                <article class="min-w-0 p-4">
                    <div class="flex min-w-0 items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="truncate text-sm font-extrabold text-slate-900">{{ $cabang->nama_cabang }}</h3>
                            <p class="mt-1 text-[11px] font-semibold text-slate-500">Kode {{ $cabang->kode_cabang }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $cabang->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}"><i class="fas {{ $cabang->is_active ? 'fa-check-circle' : 'fa-power-off' }}" aria-hidden="true"></i>{{ $cabang->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                    </div>
                    <div class="mt-3 space-y-1.5 text-xs leading-5 text-slate-600">
                        <p class="break-words"><i class="fas fa-map-marker-alt mr-2 w-3 text-center text-slate-400" aria-hidden="true"></i>{{ $cabang->alamat ?: '-' }}</p>
                        <p><i class="fas fa-phone-alt mr-2 w-3 text-center text-slate-400" aria-hidden="true"></i>{{ $cabang->telepon ?: '-' }}</p>
                    </div>
                    <div class="mt-3 grid grid-cols-3 gap-2 rounded-xl bg-slate-50 p-3 text-center">
                        <div><p class="text-sm font-extrabold text-blue-700">{{ $cabang->siswa_count }}</p><p class="text-[10px] text-slate-500">Siswa</p></div>
                        <div><p class="text-sm font-extrabold text-emerald-700">{{ $cabang->kelas_count }}</p><p class="text-[10px] text-slate-500">Kelas</p></div>
                        <div><p class="text-sm font-extrabold text-violet-700">{{ $cabang->users_count }}</p><p class="text-[10px] text-slate-500">Akun</p></div>
                    </div>
                    <div class="mt-4 grid grid-cols-3 gap-2">
                        <a href="{{ route('admin.cabang.show', $cabang) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-blue-50 text-xs font-bold text-blue-700 no-underline hover:bg-blue-100"><i class="fas fa-eye" aria-hidden="true"></i>Detail</a>
                        <a href="{{ route('admin.cabang.edit', $cabang) }}" class="inline-flex min-h-9 items-center justify-center gap-1 rounded-lg bg-amber-50 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100"><i class="fas fa-edit" aria-hidden="true"></i>Edit</a>
                        <form action="{{ route('admin.cabang.destroy', $cabang) }}" method="POST" data-confirm data-confirm-title="Hapus cabang?" data-confirm-message="Cabang {{ $cabang->nama_cabang }} akan dihapus permanen." data-confirm-text="Ya, hapus cabang">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-1 rounded-lg bg-red-50 text-xs font-bold text-red-700 hover:bg-red-100"><i class="fas fa-trash" aria-hidden="true"></i>Hapus</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-building" aria-hidden="true"></i></span><h3 class="mt-4 text-sm font-extrabold text-slate-800">Tidak ada data cabang</h3><p class="mt-1 text-xs text-slate-500">Tambahkan cabang pertama atau ubah filter.</p></div>
            @endforelse
        </div>

        @if($cabangs->isNotEmpty())
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[900px] border-collapse text-left text-sm">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500"><tr><th class="px-5 py-3 text-center">No</th><th class="px-5 py-3">Cabang</th><th class="px-5 py-3">Kontak & Alamat</th><th class="px-5 py-3">Statistik</th><th class="px-5 py-3 text-center">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($cabangs as $index => $cabang)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-5 py-4 text-center text-xs text-slate-500">{{ $cabangs->firstItem() + $index }}</td>
                                <td class="px-5 py-4"><p class="font-bold text-slate-900">{{ $cabang->nama_cabang }}</p><span class="mt-1 inline-flex rounded bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">{{ $cabang->kode_cabang }}</span></td>
                                <td class="max-w-xs px-5 py-4 text-xs leading-5 text-slate-600"><p><i class="fas fa-phone-alt mr-1 text-slate-400" aria-hidden="true"></i>{{ $cabang->telepon ?: '-' }}</p><p class="truncate" title="{{ $cabang->alamat }}"><i class="fas fa-map-marker-alt mr-1 text-slate-400" aria-hidden="true"></i>{{ $cabang->alamat ?: '-' }}</p></td>
                                <td class="px-5 py-4"><div class="flex items-center gap-3 text-xs"><span title="Siswa" class="font-bold text-blue-700"><i class="fas fa-user-graduate mr-1" aria-hidden="true"></i>{{ $cabang->siswa_count }}</span><span title="Kelas" class="font-bold text-emerald-700"><i class="fas fa-chalkboard mr-1" aria-hidden="true"></i>{{ $cabang->kelas_count }}</span><span title="Akun" class="font-bold text-violet-700"><i class="fas fa-users mr-1" aria-hidden="true"></i>{{ $cabang->users_count }}</span></div></td>
                                <td class="px-5 py-4 text-center"><span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $cabang->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $cabang->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="px-5 py-4"><div class="flex justify-end gap-1.5"><x-cleanflow.table-action href="{{ route('admin.cabang.show', $cabang) }}" tone="view" icon="fas fa-eye" label="Detail cabang" /><x-cleanflow.table-action href="{{ route('admin.cabang.edit', $cabang) }}" tone="edit" icon="fas fa-edit" label="Edit cabang" /><form action="{{ route('admin.cabang.destroy', $cabang) }}" method="POST" data-confirm data-confirm-title="Hapus cabang?" data-confirm-message="Cabang {{ $cabang->nama_cabang }} akan dihapus permanen." data-confirm-text="Ya, hapus cabang">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus cabang" /></form></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($cabangs->hasPages())
            <footer class="flex flex-col gap-2 border-t border-slate-200 px-4 py-3 text-[11px] text-slate-500 sm:flex-row sm:items-center sm:justify-between"><span>Menampilkan {{ $cabangs->firstItem() ?? 0 }}–{{ $cabangs->lastItem() ?? 0 }} dari {{ $cabangs->total() }}</span>{{ $cabangs->appends(request()->query())->links() }}</footer>
        @endif
    </section>
</div>
@endsection
