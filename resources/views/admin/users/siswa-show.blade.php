@extends('layouts.app')

@section('title', 'Detail Siswa - ' . ($siswa->nama_lengkap ?? 'N/A'))
@section('page-title', 'Detail Siswa')
@section('page-subtitle', $siswa->nama_lengkap ?? 'Data siswa')

@section('content')
@php
    $detailGroups = [
        'Data akademik' => [
            'NIS' => $siswa->nis ?: '-',
            'NISN' => $siswa->nisn ?: '-',
            'Kelas' => trim(($siswa->kelas->nama_kelas ?? 'Belum ada kelas') . ' ' . ($siswa->kelas->jenjang ?? '')),
            'Cabang' => $siswa->cabang->nama_cabang ?? '-',
            'Tanggal masuk' => $siswa->tanggal_masuk ? \Carbon\Carbon::parse($siswa->tanggal_masuk)->locale('id')->translatedFormat('d F Y') : '-',
        ],
        'Biodata pribadi' => [
            'Jenis kelamin' => $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            'Tempat lahir' => $siswa->tempat_lahir ?: '-',
            'Tanggal lahir' => $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->locale('id')->translatedFormat('d F Y') : '-',
            'Agama' => $siswa->agama ?: '-',
            'Alamat' => $siswa->alamat ?: '-',
        ],
        'Kontak keluarga' => [
            'Nama ayah' => $siswa->nama_ayah ?: '-',
            'Nama ibu' => $siswa->nama_ibu ?: '-',
            'Nomor WhatsApp' => $siswa->telepon_orangtua ?: '-',
        ],
        'Akun login' => [
            'Username' => $siswa->user->username ?? '-',
            'Email akun' => $siswa->user->email ?? '-',
            'Email pemulihan' => $siswa->user->personal_email ?? '-',
            'Status akun' => ($siswa->user?->is_active ?? false) ? 'Aktif' : 'Nonaktif',
            'Terdaftar' => $siswa->user?->created_at ? \Carbon\Carbon::parse($siswa->user->created_at)->locale('id')->translatedFormat('d F Y H:i') : '-',
        ],
    ];
@endphp

<div class="min-w-0 w-full space-y-4">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-brand-700 via-brand-600 to-blue-500 p-5 text-white sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex min-w-0 items-center gap-4">
                    <div class="flex h-16 w-16 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-white/15 ring-1 ring-white/25">
                        @if($siswa->foto)
                            <img src="{{ asset('storage/' . $siswa->foto) }}" alt="Foto {{ $siswa->nama_lengkap }}" class="h-full w-full object-cover">
                        @else
                            <i class="fas fa-user-graduate text-2xl" aria-hidden="true"></i>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-100">Profil siswa</p>
                        <h2 class="mt-1 truncate text-xl font-extrabold !text-white sm:text-2xl">{{ $siswa->user->name ?? $siswa->nama_lengkap }}</h2>
                        <p class="mt-1 truncate text-xs text-blue-100">NIS {{ $siswa->nis ?: '-' }} · NISN {{ $siswa->nisn ?: '-' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span class="whitespace-nowrap rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold ring-1 ring-white/25">{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }} {{ $siswa->kelas->jenjang ?? '' }}</span>
                    <span class="whitespace-nowrap rounded-full bg-white px-3 py-1.5 text-xs font-extrabold text-brand-700">{{ ucfirst($siswa->status) }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 px-3 py-3 sm:px-5">
            <a href="{{ route('admin.users.siswa') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i>Kembali</a>
            <a href="{{ route('admin.users.edit-siswa', $siswa->id) }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-amber-500 px-4 text-xs font-bold text-white no-underline hover:bg-amber-600"><i class="fas fa-edit" aria-hidden="true"></i>Edit data</a>
        </div>
    </section>

    <div class="grid min-w-0 gap-4 lg:grid-cols-2">
        @foreach($detailGroups as $title => $items)
            <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 px-4 py-3.5 sm:px-5"><h3 class="text-sm font-extrabold text-slate-900">{{ $title }}</h3></div>
                <dl class="divide-y divide-slate-100">
                    @foreach($items as $label => $value)
                        <div class="grid min-w-0 gap-1 px-4 py-3 sm:grid-cols-[9rem_minmax(0,1fr)] sm:gap-4 sm:px-5">
                            <dt class="text-xs font-semibold text-slate-500">{{ $label }}</dt>
                            <dd class="min-w-0 break-words text-sm font-bold text-slate-800">{{ $value }}</dd>
                        </div>
                    @endforeach
                </dl>
            </section>
        @endforeach
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-3.5 sm:px-5">
            <div><h3 class="text-sm font-extrabold text-slate-900">Akun wali terhubung</h3><p class="mt-0.5 text-xs text-slate-500">Akun yang dapat mengakses informasi siswa ini.</p></div>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[10px] font-bold text-slate-600">{{ $siswa->studentParents->count() }} akun</span>
        </div>
        <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-5 xl:grid-cols-3">
            @forelse($siswa->studentParents as $studentParent)
                <article class="min-w-0 rounded-xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0"><h4 class="truncate text-sm font-extrabold text-slate-900">{{ $studentParent->parent->name }}</h4><p class="mt-1 text-xs text-slate-500">{{ ucwords(str_replace('_', ' ', $studentParent->relationship)) }}</p></div>
                        @if($studentParent->is_primary)<span class="shrink-0 rounded-full bg-amber-50 px-2 py-1 text-[9px] font-bold text-amber-700">Utama</span>@endif
                    </div>
                    <div class="mt-3 space-y-1.5 text-xs text-slate-600">
                        <p class="truncate"><i class="fas fa-at mr-2 w-3 text-slate-400" aria-hidden="true"></i>{{ $studentParent->parent->username }}</p>
                        <p class="truncate"><i class="fas fa-envelope mr-2 w-3 text-slate-400" aria-hidden="true"></i>{{ $studentParent->parent->email ?: '-' }}</p>
                        <p><i class="fas fa-circle mr-2 w-3 text-[7px] {{ $studentParent->parent->is_active ? 'text-emerald-500' : 'text-red-500' }}" aria-hidden="true"></i>{{ $studentParent->parent->is_active ? 'Akun aktif' : 'Akun nonaktif' }}</p>
                    </div>
                </article>
            @empty
                <div class="sm:col-span-2 xl:col-span-3 rounded-xl bg-slate-50 p-8 text-center text-xs text-slate-500"><i class="fas fa-user-shield mb-3 block text-2xl text-slate-300" aria-hidden="true"></i>Belum ada akun wali terhubung. Tambahkan melalui halaman edit siswa.</div>
            @endforelse
        </div>
    </section>
</div>
@endsection
