@extends('layouts.app')

@section('title', 'Manajemen Tahun Ajaran')
@section('page-title', 'Tahun Ajaran')
@section('page-subtitle', 'Kelola periode akademik dan status aktif')


@section('content')
<div class="min-w-0 w-full space-y-5">
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-3" aria-label="Ringkasan tahun ajaran">
        <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:gap-4 sm:p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 sm:h-12 sm:w-12">
                <i class="fas fa-calendar-alt" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <p class="text-xl font-extrabold leading-none text-slate-900 sm:text-2xl">{{ $tahunAjarans->total() }}</p>
                <p class="mt-1.5 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">Total Periode</p>
            </div>
        </article>

        <article class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:gap-4 sm:p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600 sm:h-12 sm:w-12">
                <i class="fas fa-check-circle" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <p class="text-xl font-extrabold leading-none text-slate-900 sm:text-2xl">{{ $tahunAjarans->where('is_active', 1)->count() }}</p>
                <p class="mt-1.5 truncate text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">Periode Aktif</p>
            </div>
        </article>

        <article class="col-span-2 flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:col-span-1 sm:gap-4 sm:p-5">
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600 sm:h-12 sm:w-12">
                <i class="fas fa-clock" aria-hidden="true"></i>
            </span>
            <div class="min-w-0">
                <p class="truncate text-base font-extrabold leading-tight text-slate-900 sm:text-lg" title="{{ $tahunAjarans->where('is_active', 1)->first()->nama_tahun_ajaran ?? 'Tidak ada' }}">
                    {{ $tahunAjarans->where('is_active', 1)->first()->nama_tahun_ajaran ?? 'Tidak ada' }}
                </p>
                <p class="mt-1.5 text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">Tahun Berjalan</p>
            </div>
        </article>
    </section>

    @if(request('status') !== null)
        <section class="flex flex-col gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-900 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-center gap-2">
                <i class="fas fa-filter shrink-0 text-blue-600" aria-hidden="true"></i>
                <p class="min-w-0">
                    Status: <strong>{{ request('status') == '1' ? 'Aktif' : 'Tidak Aktif' }}</strong>
                    <span class="ml-1 inline-flex rounded-full bg-blue-600 px-2 py-0.5 text-[10px] font-bold text-white">{{ $tahunAjarans->total() }} data</span>
                </p>
            </div>
            <a href="{{ route('admin.tahun-ajaran.index') }}" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-white px-3 text-xs font-bold text-blue-700 no-underline ring-1 ring-blue-200 hover:bg-blue-100">
                <i class="fas fa-times" aria-hidden="true"></i> Reset filter
            </a>
        </section>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div class="min-w-0">
                <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900 sm:text-lg">
                    <i class="fas fa-list text-brand-600" aria-hidden="true"></i>
                    Daftar Tahun Ajaran
                </h2>
                <p class="mt-1 text-xs text-slate-500">Periode terbaru dan status penggunaannya.</p>
            </div>
            <div class="flex items-center gap-2">
                <form action="{{ route('admin.tahun-ajaran.index') }}" method="GET" class="min-w-0 flex-1 sm:flex-none">
                    <label for="status-filter" class="sr-only">Filter status</label>
                    <select id="status-filter" name="status" data-auto-submit class="h-10 w-full min-w-0 rounded-xl border border-slate-200 bg-white pl-3 pr-8 text-xs font-semibold text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 sm:w-40">
                        <option value="">Semua Status</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hanya Aktif</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                    </select>
                </form>
                <a href="{{ route('admin.tahun-ajaran.create') }}" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white no-underline shadow-sm hover:bg-brand-700 sm:px-4">
                    <i class="fas fa-plus" aria-hidden="true"></i>
                    <span class="hidden sm:inline">Tahun Ajaran</span>
                    <span class="sm:hidden">Tambah</span>
                </a>
            </div>
        </header>

        @forelse($tahunAjarans as $index => $ta)
            @if($loop->first)
                <div class="divide-y divide-slate-100 md:hidden">
            @endif
            <article class="p-4">
                <div class="flex min-w-0 items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="truncate text-sm font-extrabold text-slate-900">{{ $ta->nama_tahun_ajaran }}</h3>
                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            <i class="far fa-calendar-alt mr-1" aria-hidden="true"></i>
                            {{ \Carbon\Carbon::parse($ta->tanggal_mulai)->format('d M Y') }}–{{ \Carbon\Carbon::parse($ta->tanggal_selesai)->format('d M Y') }}
                        </p>
                    </div>
                    <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $ta->is_active ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200' : 'bg-slate-100 text-slate-600 ring-1 ring-slate-200' }}">
                        <i class="fas {{ $ta->is_active ? 'fa-check-circle' : 'fa-power-off' }}" aria-hidden="true"></i>
                        {{ $ta->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    <a href="{{ route('admin.tahun-ajaran.show', $ta->id) }}" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-blue-50 px-3 text-xs font-bold text-blue-700 no-underline hover:bg-blue-100">
                        <i class="fas fa-eye" aria-hidden="true"></i> Detail
                    </a>
                    <a href="{{ route('admin.tahun-ajaran.edit', $ta->id) }}" class="inline-flex min-h-9 items-center justify-center gap-2 rounded-lg bg-amber-50 px-3 text-xs font-bold text-amber-700 no-underline hover:bg-amber-100">
                        <i class="fas fa-edit" aria-hidden="true"></i> Edit
                    </a>
                    @if(!$ta->is_active)
                        <form action="{{ route('admin.tahun-ajaran.activate', $ta->id) }}" method="POST" data-confirm data-confirm-title="Aktifkan tahun ajaran?" data-confirm-message="{{ $ta->nama_tahun_ajaran }} akan menjadi periode aktif dan periode lain otomatis dinonaktifkan." data-confirm-text="Ya, aktifkan" data-confirm-danger="false">
                            @csrf
                            <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-2 rounded-lg bg-emerald-50 px-3 text-xs font-bold text-emerald-700 hover:bg-emerald-100">
                                <i class="fas fa-power-off" aria-hidden="true"></i> Aktifkan
                            </button>
                        </form>
                    @endif
                    <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST" data-confirm data-confirm-title="Hapus tahun ajaran?" data-confirm-message="{{ $ta->nama_tahun_ajaran }} akan dihapus permanen." data-confirm-text="Ya, hapus periode">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex min-h-9 w-full items-center justify-center gap-2 rounded-lg bg-red-50 px-3 text-xs font-bold text-red-700 hover:bg-red-100">
                            <i class="fas fa-trash" aria-hidden="true"></i> Hapus
                        </button>
                    </form>
                </div>
            </article>
            @if($loop->last)
                </div>
            @endif
        @empty
            <div class="px-5 py-14 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-calendar-times" aria-hidden="true"></i></span>
                <h3 class="mt-4 text-sm font-extrabold text-slate-800">Belum ada tahun ajaran</h3>
                <p class="mt-1 text-xs text-slate-500">Tambahkan periode akademik pertama untuk memulai.</p>
            </div>
        @endforelse

        @if($tahunAjarans->isNotEmpty())
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[760px] border-collapse text-left text-sm">
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-5 py-3 text-center">No</th>
                            <th class="px-5 py-3">Tahun Ajaran</th>
                            <th class="px-5 py-3">Periode</th>
                            <th class="px-5 py-3 text-center">Status</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($tahunAjarans as $index => $ta)
                            <tr class="hover:bg-slate-50/80">
                                <td class="px-5 py-4 text-center text-xs text-slate-500">{{ $tahunAjarans->firstItem() + $index }}</td>
                                <td class="px-5 py-4 font-bold text-slate-900">{{ $ta->nama_tahun_ajaran }}</td>
                                <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-600">
                                    {{ \Carbon\Carbon::parse($ta->tanggal_mulai)->format('d M Y') }}–{{ \Carbon\Carbon::parse($ta->tanggal_selesai)->format('d M Y') }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $ta->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                        <i class="fas {{ $ta->is_active ? 'fa-check-circle' : 'fa-power-off' }}" aria-hidden="true"></i>
                                        {{ $ta->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-1.5">
                                        <x-cleanflow.table-action href="{{ route('admin.tahun-ajaran.show', $ta->id) }}" tone="view" icon="fas fa-eye" label="Detail {{ $ta->nama_tahun_ajaran }}" />
                                        <x-cleanflow.table-action href="{{ route('admin.tahun-ajaran.edit', $ta->id) }}" tone="edit" icon="fas fa-edit" label="Edit {{ $ta->nama_tahun_ajaran }}" />
                                        @if(!$ta->is_active)
                                            <form action="{{ route('admin.tahun-ajaran.activate', $ta->id) }}" method="POST" data-confirm data-confirm-title="Aktifkan tahun ajaran?" data-confirm-message="{{ $ta->nama_tahun_ajaran }} akan menjadi periode aktif dan periode lain otomatis dinonaktifkan." data-confirm-text="Ya, aktifkan" data-confirm-danger="false">
                                                @csrf
                                                <x-cleanflow.table-action type="submit" tone="success" icon="fas fa-power-off" label="Aktifkan {{ $ta->nama_tahun_ajaran }}" />
                                            </form>
                                        @endif
                                        <form action="{{ route('admin.tahun-ajaran.destroy', $ta->id) }}" method="POST" data-confirm data-confirm-title="Hapus tahun ajaran?" data-confirm-message="{{ $ta->nama_tahun_ajaran }} akan dihapus permanen." data-confirm-text="Ya, hapus periode">
                                            @csrf
                                            @method('DELETE')
                                            <x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-trash" label="Hapus {{ $ta->nama_tahun_ajaran }}" />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        @if($tahunAjarans->hasPages())
            <footer class="border-t border-slate-200 px-4 py-3">
                {{ $tahunAjarans->appends(request()->query())->links() }}
            </footer>
        @endif
    </section>
</div>
@endsection
