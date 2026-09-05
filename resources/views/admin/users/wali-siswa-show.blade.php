@extends('layouts.app')

@section('title', 'Detail Wali Siswa - ' . ($orangTua->name ?? 'N/A'))
@section('page-title', 'Detail Wali Siswa')
@section('page-subtitle', $orangTua->name ?? 'N/A')

@section('content')
@php
    $children = $orangTua->studentParents ?? collect();
    $jenjangList = $children->pluck('siswa.kelas.jenjang')->filter()->unique();
    $cabangList = $children->pluck('siswa.cabang.nama_cabang')->filter()->unique();
    $accountDetails = [
        ['Nama lengkap', $orangTua->name ?: '-'],
        ['Username', $orangTua->username ?: '-'],
        ['Email akun', $orangTua->email ?: '-'],
        ['Email pemulihan', $orangTua->personal_email ?: '-'],
        ['Terdaftar sejak', $orangTua->created_at?->locale('id')->translatedFormat('d F Y') ?: '-'],
    ];
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.users.wali-siswa') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-bold text-slate-700 no-underline transition hover:border-brand-300 hover:text-brand-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.edit-wali-siswa', $orangTua->id) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-brand-200 bg-brand-50 px-4 text-sm font-bold text-brand-700 no-underline transition hover:bg-brand-100"><i class="fas fa-pen" aria-hidden="true"></i>Edit data</a>
            <form action="{{ route('admin.users.toggle-wali-siswa-status', $orangTua->id) }}" method="POST" data-confirm data-confirm-title="{{ $orangTua->is_active ? 'Nonaktifkan akun wali?' : 'Aktifkan akun wali?' }}" data-confirm-message="{{ $orangTua->is_active ? 'Wali tidak dapat login sampai akun diaktifkan kembali.' : 'Wali akan dapat login kembali ke aplikasi.' }}" data-confirm-text="{{ $orangTua->is_active ? 'Ya, nonaktifkan' : 'Ya, aktifkan' }}">
                @csrf
                <button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-xl px-4 text-sm font-bold {{ $orangTua->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}"><i class="fas fa-{{ $orangTua->is_active ? 'ban' : 'check' }}" aria-hidden="true"></i>{{ $orangTua->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
            </form>
        </div>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-brand-700 to-brand-500 px-5 py-6 text-white sm:px-6">
            <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                <span class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-2 border-white/30 bg-white/15 text-3xl"><i class="fas fa-user-group" aria-hidden="true"></i></span>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-100">Wali siswa</p>
                    <h2 class="mt-1 break-words text-2xl font-extrabold !text-white">{{ $orangTua->name }}</h2>
                    <p class="mt-1 text-sm text-blue-100">{{ '@' . $orangTua->username }}</p>
                    <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $orangTua->is_active ? 'bg-emerald-400/20 text-emerald-50 ring-1 ring-emerald-200/40' : 'bg-red-400/20 text-red-50 ring-1 ring-red-200/40' }}">{{ $orangTua->is_active ? 'Akun aktif' : 'Akun nonaktif' }}</span>
                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/30">{{ $children->count() }} anak terhubung</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-4 xl:grid-cols-[minmax(0,0.85fr)_minmax(0,1.15fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-address-card" aria-hidden="true"></i></span>
                <h3 class="font-extrabold text-slate-950">Informasi akun</h3>
            </div>
            <dl class="divide-y divide-slate-100 px-4 sm:px-5">
                @foreach($accountDetails as [$label, $value])
                    <div class="grid gap-1 py-3.5 sm:grid-cols-[9rem_minmax(0,1fr)] sm:gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                        <dd class="min-w-0 break-words text-sm font-semibold text-slate-800">{{ $value }}</dd>
                    </div>
                @endforeach
                <div class="grid gap-1 py-3.5 sm:grid-cols-[9rem_minmax(0,1fr)] sm:gap-4">
                    <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">WhatsApp</dt>
                    <dd class="text-sm font-semibold">
                        @if($orangTua->phone)
                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', $orangTua->phone) }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 text-emerald-700 no-underline hover:text-emerald-800"><i class="fab fa-whatsapp" aria-hidden="true"></i>{{ $orangTua->phone }}</a>
                        @else - @endif
                    </dd>
                </div>
            </dl>

            @if($children->isNotEmpty())
                <div class="border-t border-slate-200 bg-slate-50 px-4 py-4 sm:px-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-slate-400">Ringkasan anak</p>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach($jenjangList as $jenjang)<span class="rounded-full bg-blue-100 px-2.5 py-1 text-xs font-bold text-blue-700">{{ $jenjang }}</span>@endforeach
                        @foreach($cabangList as $cabang)<span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">{{ $cabang }}</span>@endforeach
                    </div>
                </div>
            @endif
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3"><span class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600"><i class="fas fa-user-graduate" aria-hidden="true"></i></span><h3 class="font-extrabold text-slate-950">Data anak</h3></div>
                <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">{{ $children->count() }} siswa</span>
            </div>

            @if($children->isNotEmpty())
                <div class="grid gap-3 p-4 sm:p-5">
                    @foreach($children as $sp)
                        <article class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <h4 class="break-words text-sm font-extrabold text-slate-900">{{ $sp->siswa->nama_lengkap }}</h4>
                                    <p class="mt-1 text-xs text-slate-500">NIS {{ $sp->siswa->nis ?: '-' }} · NISN {{ $sp->siswa->nisn ?: '-' }}</p>
                                </div>
                                <span class="rounded-full bg-violet-100 px-2.5 py-1 text-xs font-bold text-violet-700">{{ ucwords(str_replace('_', ' ', $sp->relationship)) }}</span>
                            </div>
                            <div class="mt-3 flex flex-wrap gap-1.5">
                                <span class="rounded-md bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">{{ $sp->siswa->kelas->jenjang ?? '-' }}</span>
                                <span class="rounded-md bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">{{ $sp->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                                <span class="rounded-md bg-white px-2 py-1 text-[10px] font-semibold text-slate-600 ring-1 ring-slate-200">{{ $sp->siswa->cabang->nama_cabang ?? 'Tanpa cabang' }}</span>
                                @if($sp->is_primary)<span class="rounded-md bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700"><i class="fas fa-star mr-1" aria-hidden="true"></i>Kontak utama</span>@endif
                                @if($sp->can_access_academic)<span class="rounded-md bg-cyan-50 px-2 py-1 text-[10px] font-bold text-cyan-700"><i class="fas fa-check mr-1" aria-hidden="true"></i>Akses akademik</span>@endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-14 text-center">
                    <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-link-slash" aria-hidden="true"></i></span>
                    <h4 class="mt-4 font-extrabold text-slate-800">Belum ada anak terhubung</h4>
                    <p class="mx-auto mt-1 max-w-sm text-sm leading-relaxed text-slate-500">Hubungkan akun wali melalui halaman edit siswa ketika datanya sudah tersedia.</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
