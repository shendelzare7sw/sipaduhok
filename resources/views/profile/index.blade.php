@extends('layouts.app')

@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Identitas, foto, dan informasi kontak')

@section('content')
@php
    $roleLabel = $user->role_label;
    $branchName = $roleName === 'siswa'
        ? ($profileData?->cabang?->nama_cabang)
        : ($user->cabang?->nama_cabang);
    $phoneValue = $roleName === 'siswa'
        ? ($profileData?->telepon_orangtua)
        : ($profileData?->telepon);
    $genderLabel = match ($profileData?->jenis_kelamin) {
        'L' => 'Laki-laki',
        'P' => 'Perempuan',
        default => '-',
    };
    $inputClass = 'min-h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20';
    $readOnlyClass = 'min-h-11 w-full rounded-xl border border-slate-200 bg-slate-100 px-3 text-sm font-semibold text-slate-600';
    $labelClass = 'mb-1.5 block text-xs font-bold text-slate-700';
    $errorClass = 'mt-1.5 block text-[11px] font-semibold text-red-600';
@endphp

<div
    data-profile-page
    class="grid min-w-0 grid-cols-1 gap-5 xl:grid-cols-[minmax(17rem,0.7fr)_minmax(0,1.6fr)]"
    x-data="{
        previewUrl: null,
        uploading: false,
        choosePhoto(event) {
            const file = event.target.files?.[0];
            if (!file) return;
            if (this.previewUrl) URL.revokeObjectURL(this.previewUrl);
            this.previewUrl = URL.createObjectURL(file);
        },
        uploadPhoto() {
            if (!this.$refs.photoInput.files?.length) return;
            this.uploading = true;
            this.$refs.photoForm.requestSubmit();
        }
    }"
>
    <div class="min-w-0 space-y-5">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="bg-gradient-to-br from-[#173f6c] via-[#176bab] to-[#159bd4] px-5 py-7 text-center text-white">
                <div class="mx-auto h-28 w-28 overflow-hidden rounded-3xl border-4 border-white/25 bg-white/15 shadow-lg ring-1 ring-white/20">
                    <template x-if="previewUrl">
                        <img :src="previewUrl" alt="Pratinjau foto profil" class="h-full w-full object-cover">
                    </template>
                    <div x-show="!previewUrl" class="h-full w-full">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}" alt="Foto profil {{ $user->name }}" class="h-full w-full object-cover">
                        @else
                            <span class="flex h-full w-full items-center justify-center text-4xl font-extrabold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        @endif
                    </div>
                </div>
                <h2 class="mt-4 break-words text-lg font-extrabold text-white">{{ $user->name }}</h2>
                <span class="mt-2 inline-flex rounded-full border border-white/20 bg-white/15 px-3 py-1 text-[11px] font-bold text-white backdrop-blur">{{ $roleLabel }}</span>
            </div>

            <div class="space-y-4 p-4 sm:p-5">
                <form x-ref="photoForm" action="{{ route('profile.upload-foto') }}" method="POST" enctype="multipart/form-data" @submit="uploading = true">
                    @csrf
                    <input x-ref="photoInput" type="file" name="foto_profil" accept="image/jpeg,image/png" class="hidden" @change="choosePhoto($event)">
                    <div class="grid grid-cols-1 gap-2 {{ $user->foto_profil ? 'sm:grid-cols-2' : '' }}">
                        <button type="button" @click="$refs.photoInput.click()" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 text-xs font-bold text-blue-700 transition hover:bg-blue-100">
                            <i class="fas fa-image" aria-hidden="true"></i> Pilih foto
                        </button>
                        @if($user->foto_profil)
                            <button type="button" form="delete-profile-photo" onclick="document.getElementById('delete-profile-photo').requestSubmit()" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-red-200 bg-red-50 px-3 text-xs font-bold text-red-700 transition hover:bg-red-100">
                                <i class="fas fa-trash" aria-hidden="true"></i> Hapus
                            </button>
                        @endif
                    </div>
                    <button x-cloak x-show="previewUrl" type="button" @click="uploadPhoto()" :disabled="uploading" class="mt-2 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white shadow-sm transition hover:bg-brand-700 disabled:cursor-wait disabled:opacity-60">
                        <i class="fas" :class="uploading ? 'fa-spinner fa-spin' : 'fa-upload'" aria-hidden="true"></i>
                        <span x-text="uploading ? 'Mengunggah...' : 'Simpan foto baru'"></span>
                    </button>
                    @error('foto_profil')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror
                    <p class="mt-2 text-center text-[10px] leading-4 text-slate-400">JPG atau PNG, maksimal 2 MB.</p>
                </form>

                @if($user->foto_profil)
                    <form id="delete-profile-photo" action="{{ route('profile.delete-foto') }}" method="POST" data-confirm data-confirm-title="Hapus foto profil?" data-confirm-message="Avatar default akan digunakan kembali." data-confirm-text="Ya, hapus">
                        @csrf
                        @method('DELETE')
                    </form>
                @endif

                <dl class="divide-y divide-slate-100 border-t border-slate-100 text-xs">
                    <div class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-3 py-3">
                        <dt class="font-semibold text-slate-500">Email akun</dt>
                        <dd class="break-all text-right font-bold text-slate-800">{{ $user->email }}</dd>
                    </div>
                    @if($branchName)
                        <div class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-3 py-3">
                            <dt class="font-semibold text-slate-500">Cabang</dt>
                            <dd class="break-words text-right font-bold text-slate-800">{{ $branchName }}</dd>
                        </div>
                    @endif
                    <div class="grid grid-cols-[5.5rem_minmax(0,1fr)] gap-3 py-3">
                        <dt class="font-semibold text-slate-500">Terdaftar</dt>
                        <dd class="text-right font-bold text-slate-800">{{ $user->created_at->locale('id')->translatedFormat('d M Y') }}</dd>
                    </div>
                </dl>
            </div>
        </section>

        @if($roleName === 'siswa' && $profileData)
            <section class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600"><i class="fas fa-graduation-cap" aria-hidden="true"></i></span>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900">Status Akademik</h3>
                        <p class="text-[11px] text-slate-500">Penempatan aktif siswa.</p>
                    </div>
                </div>
                <dl class="mt-4 grid grid-cols-2 gap-3">
                    <div class="rounded-xl bg-slate-50 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Kelas</dt>
                        <dd class="mt-1 text-sm font-extrabold text-slate-900">{{ $profileData->kelas?->nama_kelas ?? '-' }}</dd>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Status</dt>
                        <dd class="mt-1 text-sm font-extrabold {{ $profileData->status === 'aktif' ? 'text-emerald-700' : 'text-slate-700' }}">{{ ucfirst($profileData->status ?? '-') }}</dd>
                    </div>
                    <div class="col-span-2 rounded-xl bg-slate-50 p-3">
                        <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">Tahun ajaran</dt>
                        <dd class="mt-1 text-sm font-extrabold text-slate-900">{{ $profileData->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</dd>
                    </div>
                </dl>
            </section>
        @endif
    </div>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600"><i class="fas fa-address-card" aria-hidden="true"></i></span>
                <div class="min-w-0">
                    <h2 class="text-sm font-extrabold text-slate-900 sm:text-base">Informasi Detail</h2>
                    <p class="mt-0.5 text-[11px] text-slate-500">Periksa identitas dan perbarui kontak Anda.</p>
                </div>
            </div>
            <a href="{{ route('account.settings') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 no-underline transition hover:border-brand-200 hover:text-brand-700">
                <i class="fas fa-cog" aria-hidden="true"></i> Pengaturan akun
            </a>
        </header>

        @if($profileData)
            <form action="{{ route('profile.update') }}" method="POST" class="space-y-5 p-4 sm:p-5">
                @csrf
                @method('PUT')

                <div class="grid min-w-0 grid-cols-1 gap-4 sm:grid-cols-2">
                    <div class="min-w-0">
                        <label for="profile-name" class="{{ $labelClass }}">Nama Lengkap</label>
                        <input id="profile-name" type="text" class="{{ $readOnlyClass }}" value="{{ $profileData->nama_lengkap ?? $user->name }}" readonly>
                    </div>

                    @if(isset($profileData->nip) || isset($profileData->nis))
                        <div class="min-w-0">
                            <label for="profile-number" class="{{ $labelClass }}">{{ $roleName === 'siswa' ? 'NIS' : 'NIP' }}</label>
                            <input id="profile-number" type="text" class="{{ $readOnlyClass }}" value="{{ $profileData->nip ?? $profileData->nis ?? '-' }}" readonly>
                        </div>
                    @endif

                    <div class="min-w-0">
                        <label for="profile-gender" class="{{ $labelClass }}">Jenis Kelamin</label>
                        <input id="profile-gender" type="text" class="{{ $readOnlyClass }}" value="{{ $genderLabel }}" readonly>
                    </div>

                    <div class="min-w-0">
                        <label for="no_telepon" class="{{ $labelClass }}">{{ $roleName === 'siswa' ? 'Telepon Orang Tua / Wali' : 'No. Telepon / WhatsApp' }}</label>
                        <div class="flex overflow-hidden rounded-xl border {{ $errors->has('no_telepon') ? 'border-red-400' : 'border-slate-200' }} focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20">
                            <span class="flex w-11 shrink-0 items-center justify-center border-r border-slate-200 bg-slate-50 text-slate-400"><i class="fas fa-phone" aria-hidden="true"></i></span>
                            <input type="text" name="no_telepon" id="no_telepon" value="{{ old('no_telepon', $phoneValue) }}" placeholder="08xxxxxxxxxx" class="min-h-11 min-w-0 flex-1 border-0 px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0">
                        </div>
                        @error('no_telepon')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror
                    </div>

                    <div class="min-w-0 sm:col-span-2">
                        <label for="personal_email" class="{{ $labelClass }}">Email Pemulihan</label>
                        <div class="flex overflow-hidden rounded-xl border {{ $errors->has('personal_email') ? 'border-red-400' : 'border-slate-200' }} focus-within:border-brand-500 focus-within:ring-2 focus-within:ring-brand-500/20">
                            <span class="flex w-11 shrink-0 items-center justify-center border-r border-slate-200 bg-slate-50 text-slate-400"><i class="fas fa-envelope" aria-hidden="true"></i></span>
                            <input type="email" name="personal_email" id="personal_email" value="{{ old('personal_email', $user->personal_email) }}" placeholder="nama@email.com" class="min-h-11 min-w-0 flex-1 border-0 px-3 text-sm text-slate-800 outline-none placeholder:text-slate-400 focus:ring-0">
                        </div>
                        <p class="mt-1.5 text-[11px] leading-5 text-slate-500">Digunakan untuk membantu pemulihan akun.</p>
                        @error('personal_email')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror
                    </div>

                    <div class="min-w-0 sm:col-span-2">
                        <label for="alamat" class="{{ $labelClass }}">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" rows="4" class="{{ $inputClass }} resize-y py-3">{{ old('alamat', $profileData->alamat) }}</textarea>
                        @error('alamat')<span class="{{ $errorClass }}">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="flex flex-col-reverse gap-2 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                    <a href="{{ route('dashboard') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 text-sm font-bold text-slate-600 no-underline transition hover:bg-slate-50"><i class="fas fa-arrow-left" aria-hidden="true"></i> Dashboard</a>
                    <button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white shadow-sm transition hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i> Simpan perubahan</button>
                </div>
            </form>
        @else
            <div class="p-4 sm:p-5">
                <dl class="grid min-w-0 grid-cols-1 overflow-hidden rounded-xl border border-slate-200 sm:grid-cols-2">
                    @foreach([
                        ['Nama Lengkap', $user->name],
                        ['Email Utama', $user->email],
                        ['Role Akses', $roleLabel],
                        ['Status Akun', $user->is_active === false ? 'Nonaktif' : 'Aktif'],
                    ] as [$term, $description])
                        <div class="min-w-0 border-b border-slate-100 p-4 sm:odd:border-r last:border-b-0 sm:[&:nth-last-child(-n+2)]:border-b-0">
                            <dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $term }}</dt>
                            <dd class="mt-1 break-words text-sm font-extrabold text-slate-900">{{ $description }}</dd>
                        </div>
                    @endforeach
                </dl>
                <div class="mt-5 flex flex-col gap-3 rounded-xl border border-blue-100 bg-blue-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <h3 class="text-sm font-extrabold text-blue-950">Perlu mengubah data akun?</h3>
                        <p class="mt-1 text-xs leading-5 text-blue-700">Nama, email, password, dan keamanan dikelola pada pengaturan akun.</p>
                    </div>
                    <a href="{{ route('account.settings') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-brand-700"><i class="fas fa-cog" aria-hidden="true"></i> Buka pengaturan</a>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
