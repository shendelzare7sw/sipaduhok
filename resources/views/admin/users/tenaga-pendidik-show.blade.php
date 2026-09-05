@extends('layouts.app')

@section('title', 'Detail Tenaga Pendidik - ' . ($tenagaPendidik->nama_lengkap ?? 'N/A'))
@section('page-title', 'Detail Tenaga Pendidik')
@section('page-subtitle', $tenagaPendidik->nama_lengkap ?? 'N/A')

@section('content')
@php
    $user = $tenagaPendidik->user;
    $details = [
        ['NIP', $tenagaPendidik->nip ?: '-'],
        ['Jenis kelamin', $tenagaPendidik->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
        ['Tempat, tanggal lahir', trim(($tenagaPendidik->tempat_lahir ?: '-') . ($tenagaPendidik->tanggal_lahir ? ', ' . \Carbon\Carbon::parse($tenagaPendidik->tanggal_lahir)->locale('id')->translatedFormat('d F Y') : ''))],
        ['Pendidikan terakhir', $tenagaPendidik->pendidikan_terakhir ?: '-'],
        ['Alamat', $tenagaPendidik->alamat ?: '-'],
    ];
    $accountDetails = [
        ['Email akun', $user->email ?: '-'],
        ['Email pemulihan', $user->personal_email ?: '-'],
        ['Nomor telepon', $tenagaPendidik->telepon ?: '-'],
        ['Username', $user->username ?: '-'],
        ['Cabang', $user->cabang->nama_cabang ?? 'Pusat'],
    ];
@endphp

<div class="space-y-4">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('admin.users.tenaga-pendidik') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 text-sm font-bold text-slate-700 no-underline transition hover:border-brand-300 hover:text-brand-700">
            <i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali
        </a>
        <a href="{{ route('admin.users.edit-tenaga-pendidik', $tenagaPendidik->user_id) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white no-underline shadow-sm transition hover:bg-brand-700">
            <i class="fas fa-pen" aria-hidden="true"></i>Edit data
        </a>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-brand-700 to-brand-500 px-5 py-6 text-white sm:px-6">
            <div class="flex flex-col items-center gap-4 text-center sm:flex-row sm:text-left">
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border-2 border-white/40 bg-white/15 text-3xl">
                    @if($tenagaPendidik->foto)
                        <img src="{{ asset('storage/' . $tenagaPendidik->foto) }}" alt="Foto {{ $tenagaPendidik->nama_lengkap }}" class="h-full w-full object-cover">
                    @else
                        <i class="fas fa-user" aria-hidden="true"></i>
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-bold uppercase tracking-widest text-blue-100">Tenaga pendidik</p>
                    <h2 class="mt-1 break-words text-2xl font-extrabold !text-white">{{ $tenagaPendidik->nama_lengkap }}</h2>
                    <div class="mt-3 flex flex-wrap justify-center gap-2 sm:justify-start">
                        <span class="rounded-full bg-white/15 px-3 py-1 text-xs font-bold ring-1 ring-white/30">{{ ucwords(str_replace('_', ' ', $user->role)) }}</span>
                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $user->is_active ? 'bg-emerald-400/20 text-emerald-50 ring-1 ring-emerald-200/40' : 'bg-red-400/20 text-red-50 ring-1 ring-red-200/40' }}">{{ $user->is_active ? 'Akun aktif' : 'Akun nonaktif' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="grid gap-4 lg:grid-cols-2">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-id-card" aria-hidden="true"></i></span>
                <h3 class="font-extrabold text-slate-950">Informasi pribadi</h3>
            </div>
            <dl class="divide-y divide-slate-100 px-4 sm:px-5">
                @foreach($details as [$label, $value])
                    <div class="grid gap-1 py-3.5 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                        <dd class="min-w-0 break-words text-sm font-semibold text-slate-800">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-3 border-b border-slate-200 px-4 py-4 sm:px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-user-lock" aria-hidden="true"></i></span>
                <h3 class="font-extrabold text-slate-950">Kontak dan akun</h3>
            </div>
            <dl class="divide-y divide-slate-100 px-4 sm:px-5">
                @foreach($accountDetails as [$label, $value])
                    <div class="grid gap-1 py-3.5 sm:grid-cols-[10rem_minmax(0,1fr)] sm:gap-4">
                        <dt class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt>
                        <dd class="min-w-0 break-words text-sm font-semibold text-slate-800">{{ $value }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>
    </div>
</div>
@endsection
