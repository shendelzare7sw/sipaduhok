@extends('layouts.app')

@section('title', 'Pengaturan KKM')
@section('page-title', 'Pengaturan KKM')
@section('page-subtitle', 'Atur nilai minimum setiap mata pelajaran sebelum simulasi kenaikan kelas')

@section('content')
@php
    $routePrefix = request()->routeIs('waka.*') ? 'waka.kenaikan-kelas' : 'admin.akademik.kenaikan-kelas';
    $tahunLabel = $tahun->nama_tahun_ajaran ?? $tahun->nama ?? $tahun->tahun_ajaran ?? '-';
    $configured = collect($existingKKM ?? [])->filter(fn ($value) => $value !== null);
@endphp

<div
    class="min-w-0 w-full space-y-5"
    x-data="{
        bulk: 70,
        applyBulk() {
            const value = Math.max(0, Math.min(100, Number(this.bulk) || 0));
            this.$root.querySelectorAll('[data-kkm-input]').forEach((input) => {
                input.value = value;
                input.dispatchEvent(new Event('input'));
            });
        }
    }"
>
    <section class="flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5 lg:flex-row lg:items-center lg:justify-between">
        <div class="min-w-0">
            <p class="text-[10px] font-extrabold uppercase tracking-[0.16em] text-brand-600">Tahun Ajaran {{ $tahunLabel }}</p>
            <h2 class="mt-1 text-base font-extrabold text-slate-900">Nilai KKM per mata pelajaran</h2>
            <p class="mt-1 text-xs text-slate-500">Pilih jenjang, periksa nilai, lalu simpan seluruh perubahan sekaligus.</p>
        </div>

        <form action="{{ route($routePrefix . '.kkm.index') }}" method="GET" class="sm:w-48">
            <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500">
                Jenjang
                <select name="jenjang" data-auto-submit class="mt-1.5 h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-xs font-bold text-slate-700 outline-none focus:border-brand-500">
                    @foreach (['PAUD', 'SD', 'SMP', 'SMA'] as $option)
                        <option value="{{ $option }}" @selected($jenjang === $option)>{{ $option }}</option>
                    @endforeach
                </select>
            </label>
        </form>
    </section>

    <section class="grid grid-cols-3 gap-2 sm:gap-3">
        <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
            <p class="truncate text-[10px] font-bold uppercase text-slate-500">Mata Pelajaran</p>
            <p class="mt-2 text-xl font-extrabold text-slate-900">{{ $mapelList->count() }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
            <p class="truncate text-[10px] font-bold uppercase text-slate-500">Sudah Diatur</p>
            <p class="mt-2 text-xl font-extrabold text-emerald-700">{{ $configured->count() }}</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4">
            <p class="truncate text-[10px] font-bold uppercase text-slate-500">Rata-rata</p>
            <p class="mt-2 text-xl font-extrabold text-brand-700">{{ $configured->isEmpty() ? 70 : number_format($configured->avg(), 0) }}</p>
        </article>
    </section>

    @if ($errors->any())
        <section class="rounded-2xl border border-red-200 bg-red-50 p-4 text-xs text-red-800">
            <p class="font-extrabold">Beberapa nilai KKM belum valid:</p>
            <ul class="mt-2 list-disc space-y-1 pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </section>
    @endif

    <form action="{{ route($routePrefix . '.kkm.store') }}" method="POST" class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
        <input type="hidden" name="jenjang" value="{{ $jenjang }}">

        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900">
                        <i class="fas fa-book-open text-brand-600" aria-hidden="true"></i>
                        Daftar {{ $jenjang }}
                    </h2>
                    <p class="mt-1 text-xs text-slate-500">Semua nilai harus berada di antara 0 dan 100.</p>
                </div>

                @if ($mapelList->isNotEmpty())
                    <div class="flex items-end gap-2">
                        <label class="block flex-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">
                            Isi semua
                            <input type="number" x-model="bulk" min="0" max="100" class="mt-1 h-10 w-full rounded-xl border border-slate-200 px-3 text-xs font-bold outline-none focus:border-brand-500 sm:w-28">
                        </label>
                        <button type="button" @click="applyBulk()" class="inline-flex h-10 items-center justify-center rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 hover:bg-slate-200">
                            Terapkan
                        </button>
                    </div>
                @endif
            </div>
        </header>

        @if ($mapelList->isEmpty())
            <div class="px-5 py-14 text-center">
                <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400">
                    <i class="fas fa-book-open" aria-hidden="true"></i>
                </span>
                <h3 class="mt-4 text-sm font-extrabold text-slate-800">Belum ada mata pelajaran</h3>
                <p class="mt-1 text-xs text-slate-500">Jenjang {{ $jenjang }} belum memiliki mata pelajaran yang dapat diatur.</p>
            </div>
        @else
            <div class="hidden grid-cols-[56px_minmax(0,1fr)_100px_120px_140px] bg-slate-50 px-4 py-3 text-[10px] font-bold uppercase tracking-wider text-slate-500 lg:grid">
                <span class="text-center">No</span>
                <span>Mata Pelajaran</span>
                <span>Jenjang</span>
                <span class="text-center">Saat Ini</span>
                <span class="text-right">KKM Baru</span>
            </div>

            <div class="divide-y divide-slate-100">
                @foreach ($mapelList as $index => $mapel)
                    @php
                        $value = old('kkm.' . $mapel->id, $existingKKM[$mapel->id] ?? 70);
                    @endphp
                    <article class="grid grid-cols-[minmax(0,1fr)_88px] items-center gap-3 p-4 hover:bg-slate-50/80 lg:grid-cols-[56px_minmax(0,1fr)_100px_120px_140px] lg:gap-0">
                        <span class="hidden text-center text-xs text-slate-500 lg:block">{{ $index + 1 }}</span>
                        <div class="min-w-0 lg:pr-3">
                            <p class="text-[10px] font-bold uppercase tracking-wide text-slate-400 lg:hidden">{{ $jenjang }} · Mata pelajaran {{ $index + 1 }}</p>
                            <h3 class="mt-1 break-words text-sm font-extrabold text-slate-900 lg:mt-0 lg:truncate" title="{{ $mapel->nama_mapel }}">{{ $mapel->nama_mapel }}</h3>
                            <p class="mt-1 text-[11px] text-slate-500 lg:hidden">Nilai tersimpan: {{ $existingKKM[$mapel->id] ?? 70 }}</p>
                        </div>
                        <span class="hidden text-xs font-bold text-slate-700 lg:block">{{ $mapel->jenjang }}</span>
                        <span class="hidden text-center lg:block">
                            <span class="inline-flex min-w-10 justify-center rounded-lg bg-brand-50 px-2 py-1 text-xs font-extrabold text-brand-700">{{ $existingKKM[$mapel->id] ?? 70 }}</span>
                        </span>
                        <label class="block text-[10px] font-bold uppercase tracking-wide text-slate-500 lg:text-right">
                            <span class="lg:hidden">KKM</span>
                            <input
                                type="number"
                                name="kkm[{{ $mapel->id }}]"
                                value="{{ $value }}"
                                min="0"
                                max="100"
                                required
                                data-kkm-input
                                class="mt-1 h-11 w-full rounded-xl border border-slate-200 px-2 text-center text-sm font-extrabold text-brand-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 lg:mt-0 lg:h-10 lg:w-24 @error('kkm.' . $mapel->id) !border-red-400 @enderror"
                            >
                        </label>
                    </article>
                @endforeach
            </div>

            <footer class="flex flex-col gap-2 border-t border-slate-200 bg-slate-50/70 p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-[11px] text-slate-500">Perubahan baru diterapkan setelah disimpan.</p>
                <button type="submit" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-xs font-bold text-white hover:bg-brand-700">
                    <i class="fas fa-save" aria-hidden="true"></i>
                    Simpan seluruh KKM
                </button>
            </footer>
        @endif
    </form>
</div>
@endsection
