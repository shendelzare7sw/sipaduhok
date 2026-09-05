@extends('layouts.app')

@section('title', 'Detail Siswa - '.$siswa->nama_lengkap)
@section('page-title', 'Detail Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('content')
@php
    $hasAyahKandung = $siswa->orangTua->contains(fn ($parent) => $parent->pivot->relationship === 'ayah_kandung');
    $hasIbuKandung = $siswa->orangTua->contains(fn ($parent) => $parent->pivot->relationship === 'ibu_kandung');
    $existingParentIds = $siswa->orangTua->pluck('id');
    $linkableParents = $availableParents->whereNotIn('id', $existingParentIds);
    $relationships = [
        'ayah_kandung' => 'Ayah Kandung', 'ibu_kandung' => 'Ibu Kandung',
        'ayah_tiri' => 'Ayah Tiri', 'ibu_tiri' => 'Ibu Tiri',
        'kakek' => 'Kakek', 'nenek' => 'Nenek', 'paman' => 'Paman',
        'bibi' => 'Bibi', 'wali' => 'Wali', 'lainnya' => 'Lainnya',
    ];
    $details = [
        ['Nama lengkap', $siswa->nama_lengkap], ['NISN', $siswa->nisn ?: '-'],
        ['NIS', $siswa->nis ?: '-'], ['Jenis kelamin', $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan'],
        ['Tempat, tanggal lahir', trim(($siswa->tempat_lahir ?: '-').', '.($siswa->tanggal_lahir?->locale('id')->translatedFormat('d F Y') ?? '-'))],
        ['Tanggal masuk', $siswa->tanggal_masuk?->locale('id')->translatedFormat('d F Y') ?? '-'],
        ['Cabang', $siswa->cabang->nama_cabang ?? '-'], ['Status', ucfirst($siswa->status)],
        ['Nama ayah', $siswa->nama_ayah ?: '-'], ['Nama ibu', $siswa->nama_ibu ?: '-'],
        ['Telepon wali', $siswa->telepon_orangtua ?: '-'], ['Alamat', $siswa->alamat ?: '-'],
    ];
@endphp

<div x-data="{ parentPanel: {{ $errors->any() ? 'true' : 'false' }}, mode: '{{ old('create_new_parent') === '1' ? 'new' : (old('parent_id') ? 'existing' : '') }}', search: '', parentStatus: '', showPassword: false }" class="min-w-0 w-full space-y-4">
    <header class="overflow-hidden rounded-2xl bg-gradient-to-r from-brand-950 to-brand-700 p-4 text-white shadow-sm sm:p-5 [&_h2]:!text-white [&_a:first-child]:!text-brand-700 [&_a:first-child:hover]:!text-brand-900">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex min-w-0 items-center gap-3"><span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-white/10 text-xl font-extrabold ring-1 ring-inset ring-white/20">{{ strtoupper(substr($siswa->nama_lengkap,0,1)) }}</span><div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wide text-blue-200">Profil siswa</p><h2 class="truncate text-xl font-extrabold sm:text-2xl">{{ $siswa->nama_lengkap }}</h2><p class="mt-1 truncate text-xs text-blue-100">NISN {{ $siswa->nisn ?: '-' }}{{ $siswa->nis ? ' · NIS '.$siswa->nis : '' }} &middot; {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p></div></div>
<div class="flex flex-wrap gap-2"><a href="{{ route('admin.users.edit-siswa',$siswa->id) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-white px-3 text-xs font-bold text-brand-700 no-underline hover:bg-blue-50 hover:text-brand-900"><i class="fas fa-pen"></i>Edit data</a><a href="{{ route('admin.manajemen-siswa.print-kartu',$siswa) }}" target="_blank" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-white/10 px-3 text-xs font-bold text-white no-underline ring-1 ring-inset ring-white/20 hover:bg-white/20"><i class="fas fa-id-card"></i>Cetak kartu</a><a href="{{ route('admin.manajemen-siswa.index') }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-white/10 px-3 text-xs font-bold text-white no-underline ring-1 ring-inset ring-white/20 hover:bg-white/20"><i class="fas fa-arrow-left"></i>Kembali</a></div>
        </div>
    </header>

    @if($errors->any())
        <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"><strong>Data belum dapat disimpan.</strong><ul class="mt-2 list-disc space-y-1 pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="grid min-w-0 gap-4 xl:grid-cols-[minmax(0,1.15fr)_minmax(320px,.85fr)]">
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-user mr-2 text-brand-600"></i>Data pribadi</h3><p class="mt-1 text-xs text-slate-500">Identitas utama dan informasi keluarga siswa.</p></header>
            <dl class="grid sm:grid-cols-2">
                @foreach($details as [$label,$value])
                    <div class="min-w-0 border-b border-slate-100 px-4 py-3 last:border-b-0 sm:px-5 sm:[&:nth-last-child(-n+2)]:border-b-0"><dt class="text-[10px] font-bold uppercase tracking-wide text-slate-400">{{ $label }}</dt><dd class="mt-1 break-words text-sm font-semibold text-slate-800">{{ $value }}</dd></div>
                @endforeach
            </dl>
        </section>

        <section class="self-start overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <header class="border-b border-slate-200 px-4 py-4 sm:px-5"><h3 class="font-extrabold text-slate-950"><i class="fas fa-school mr-2 text-brand-600"></i>Kelas dan penempatan</h3><p class="mt-1 text-xs text-slate-500">Periode aktif: {{ $tahunAjaranAktif?->nama_tahun_ajaran ?? 'belum tersedia' }}.</p></header>
            <div class="space-y-4 p-4 sm:p-5">
                @if($siswa->kelas)
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4"><span class="text-[10px] font-bold uppercase tracking-wide text-emerald-600">Kelas saat ini</span><strong class="mt-1 block text-xl text-emerald-950">{{ $siswa->kelas->nama_kelas }} <small class="text-sm text-emerald-700">{{ $siswa->kelas->jenjang }}</small></strong><p class="mt-1 text-xs text-emerald-700">{{ $siswa->kelas->cabang->nama_cabang ?? '-' }} &middot; {{ $siswa->kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</p>@if($siswa->kelas->waliKelas)<p class="mt-1 text-xs text-emerald-700">Wali kelas: {{ $siswa->kelas->waliKelas->nama_lengkap }}</p>@endif</div>
                @else
                    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800"><i class="fas fa-circle-exclamation mr-2"></i><strong>Belum ditempatkan di kelas.</strong></div>
                @endif
                <form action="{{ route('admin.manajemen-siswa.assign-kelas',$siswa) }}" method="POST" class="space-y-3">@csrf<label class="block"><span class="mb-1.5 block text-xs font-bold text-slate-700">{{ $siswa->kelas ? 'Pindahkan ke kelas' : 'Tempatkan ke kelas' }}</span><select name="kelas_id" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Tanpa kelas</option>@foreach($kelasList->groupBy('jenjang') as $jenjang=>$group)<optgroup label="{{ $jenjang }}">@foreach($group as $kelas)@php $sisaKuota=$kelas->kuota_siswa-$kelas->siswa_count; @endphp<option value="{{ $kelas->id }}" @selected($siswa->kelas_id===$kelas->id) @disabled($sisaKuota<=0 && $siswa->kelas_id!==$kelas->id)>{{ $kelas->nama_kelas }} - {{ $kelas->cabang->nama_cabang ?? '' }} (sisa {{ $sisaKuota }})</option>@endforeach</optgroup>@endforeach</select></label><button type="submit" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white hover:bg-brand-700"><i class="fas fa-check"></i>Simpan penempatan</button></form>
            </div>
        </section>
    </div>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-200 px-4 py-4 sm:px-5"><div><h3 class="font-extrabold text-slate-950"><i class="fas fa-people-roof mr-2 text-brand-600"></i>Wali siswa</h3><p class="mt-1 text-xs text-slate-500">Kelola hubungan keluarga dan hak akses wali.</p></div><button type="button" @click="parentPanel = !parentPanel; if (!parentPanel) mode = ''" class="inline-flex min-h-10 items-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-plus"></i>Tambah wali</button></header>

        <div class="grid gap-3 p-4 md:grid-cols-2 xl:grid-cols-3 sm:p-5">
            @forelse($siswa->orangTua as $parent)
                @php $relationshipLabel=$relationships[$parent->pivot->relationship] ?? ucwords(str_replace('_',' ',$parent->pivot->relationship)); @endphp
                <article class="flex min-w-0 items-start gap-3 rounded-2xl border border-slate-200 p-4"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-xs font-extrabold text-violet-700">{{ strtoupper(substr($parent->name,0,1)) }}</span><div class="min-w-0 flex-1"><strong class="block truncate text-sm text-slate-950" title="{{ $parent->name }}">{{ $parent->name }}</strong><span class="block truncate text-xs text-slate-500">{{ $parent->email }}</span><div class="mt-2 flex flex-wrap gap-1"><span class="rounded-full bg-violet-50 px-2 py-1 text-[10px] font-bold text-violet-700">{{ $relationshipLabel }}</span>@if($parent->pivot->is_primary)<span class="rounded-full bg-amber-50 px-2 py-1 text-[10px] font-bold text-amber-700">Utama</span>@endif @if($parent->pivot->is_financial_responsible)<span class="rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">Keuangan</span>@endif @if($parent->pivot->can_access_academic)<span class="rounded-full bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">Akademik</span>@endif</div></div><form action="{{ route('admin.manajemen-siswa.detach-parent',[$siswa,$parent]) }}" method="POST" data-confirm data-confirm-title="Hapus hubungan wali?" data-confirm-message="Hubungan {{ $parent->name }} sebagai {{ $relationshipLabel }} akan dilepas. Akun wali tidak dihapus." data-confirm-text="Ya, lepas">@csrf @method('DELETE')<x-cleanflow.table-action type="submit" tone="delete" icon="fas fa-unlink" label="Lepas hubungan wali" /></form></article>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-300 p-8 text-center md:col-span-2 xl:col-span-3"><i class="fas fa-users-slash text-3xl text-slate-300"></i><h4 class="mt-3 font-bold text-slate-900">Belum ada wali terhubung</h4><p class="mt-1 text-xs text-slate-500">Hubungkan akun yang sudah ada atau buat akun wali baru.</p></div>
            @endforelse
        </div>

        <div x-cloak x-show="parentPanel" x-transition class="border-t border-slate-200 bg-slate-50 p-4 sm:p-5">
            <div class="mx-auto max-w-5xl space-y-4">
                <div class="flex flex-wrap items-center justify-between gap-3"><div><h4 class="font-extrabold text-slate-950">Tambahkan wali siswa</h4><p class="mt-1 text-xs text-slate-500">Pilih sumber akun terlebih dahulu.</p></div><button type="button" @click="parentPanel=false; mode=''" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white text-slate-500 ring-1 ring-inset ring-slate-200"><i class="fas fa-times"></i></button></div>
                @if($hasAyahKandung || $hasIbuKandung)<p class="rounded-xl border border-blue-200 bg-blue-50 p-3 text-xs text-blue-700">Relasi inti yang sudah ada: {{ collect([$hasAyahKandung ? 'Ayah Kandung' : null,$hasIbuKandung ? 'Ibu Kandung' : null])->filter()->join(' dan ') }}. Opsi tersebut dinonaktifkan agar tidak ganda.</p>@endif
                <div class="grid grid-cols-2 gap-2"><button type="button" @click="mode='existing'" :class="mode==='existing' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-600'" class="min-h-12 rounded-xl border px-3 text-xs font-bold"><i class="fas fa-link mr-2"></i>Akun yang ada</button><button type="button" @click="mode='new'" :class="mode==='new' ? 'border-brand-500 bg-brand-50 text-brand-700' : 'border-slate-200 bg-white text-slate-600'" class="min-h-12 rounded-xl border px-3 text-xs font-bold"><i class="fas fa-user-plus mr-2"></i>Buat akun baru</button></div>

                <form x-cloak x-show="mode==='existing'" action="{{ route('admin.manajemen-siswa.attach-parent',$siswa) }}" method="POST" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4">@csrf
                    <div class="grid gap-3 sm:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Cari wali</span><input type="search" x-model="search" placeholder="Nama atau email..." class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Status akun</span><select x-model="parentStatus" class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Semua akun</option><option value="available">Belum punya siswa</option><option value="has_children">Sudah punya siswa</option></select></label></div>
                    <fieldset><legend class="mb-2 text-xs font-bold text-slate-700">Pilih wali <span class="text-red-500">*</span></legend><div class="max-h-64 space-y-2 overflow-y-auto rounded-xl border border-slate-200 p-2">@forelse($linkableParents as $candidate)@php $hasChildren=$candidate->studentParents->isNotEmpty(); $searchValue=strtolower($candidate->name.' '.$candidate->email); @endphp<label x-show="(search==='' || @js($searchValue).includes(search.toLowerCase())) && (parentStatus==='' || parentStatus==='{{ $hasChildren ? 'has_children' : 'available' }}')" class="flex cursor-pointer items-start gap-3 rounded-xl p-3 hover:bg-slate-50"><input type="radio" name="parent_id" value="{{ $candidate->id }}" required class="mt-1 h-4 w-4 border-slate-300 text-brand-600"><span class="min-w-0"><strong class="block truncate text-sm text-slate-900">{{ $candidate->name }}</strong><small class="block truncate text-slate-500">{{ $candidate->email }}</small><small class="mt-1 block text-slate-400">{{ $hasChildren ? 'Sudah terhubung dengan '.$candidate->studentParents->count().' siswa' : 'Belum memiliki siswa' }}</small></span></label>@empty<p class="p-6 text-center text-sm text-slate-500">Tidak ada akun wali lain yang tersedia.</p>@endforelse</div></fieldset>
                    <div class="grid gap-3 sm:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Hubungan <span class="text-red-500">*</span></span><select name="relationship" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Pilih hubungan</option>@foreach($relationships as $value=>$label)<option value="{{ $value }}" @selected(old('relationship')===$value) @disabled(($value==='ayah_kandung'&&$hasAyahKandung)||($value==='ibu_kandung'&&$hasIbuKandung))>{{ $label }}</option>@endforeach</select></label><div class="space-y-2 pt-1 sm:pt-6"><label class="flex items-center gap-2 text-xs font-semibold text-slate-700"><input type="checkbox" name="is_primary" value="1" @checked(old('is_primary')) class="h-4 w-4 rounded text-brand-600">Penanggung jawab utama</label><label class="flex items-center gap-2 text-xs font-semibold text-slate-700"><input type="checkbox" name="is_financial_responsible" value="1" @checked(old('is_financial_responsible',true)) class="h-4 w-4 rounded text-brand-600">Akses keuangan</label><label class="flex items-center gap-2 text-xs font-semibold text-slate-700"><input type="checkbox" name="can_access_academic" value="1" @checked(old('can_access_academic',true)) class="h-4 w-4 rounded text-brand-600">Akses akademik</label></div></div><button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white"><i class="fas fa-link"></i>Hubungkan wali</button>
                </form>

                <form x-cloak x-show="mode==='new'" action="{{ route('admin.manajemen-siswa.attach-parent',$siswa) }}" method="POST" class="space-y-4 rounded-2xl border border-slate-200 bg-white p-4">@csrf<input type="hidden" name="create_new_parent" value="1">
                    <div class="grid gap-3 sm:grid-cols-2"><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Nama lengkap <span class="text-red-500">*</span></span><input name="new_parent_name" value="{{ old('new_parent_name') }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Username <span class="text-red-500">*</span></span><input name="new_parent_username" value="{{ old('new_parent_username') }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Email <span class="text-red-500">*</span></span><input type="email" name="new_parent_email" value="{{ old('new_parent_email') }}" required class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Password <span class="text-red-500">*</span></span><span class="relative block"><input :type="showPassword ? 'text' : 'password'" name="new_parent_password" minlength="8" required class="h-11 w-full rounded-xl border border-slate-300 pl-3 pr-11 text-sm"><button type="button" @click="showPassword=!showPassword" class="absolute inset-y-0 right-0 w-11 text-slate-400"><i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i></button></span></label><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Nomor telepon/WA</span><input name="new_parent_phone" value="{{ old('new_parent_phone') }}" class="h-11 w-full rounded-xl border border-slate-300 px-3 text-sm"></label><label><span class="mb-1.5 block text-xs font-bold text-slate-700">Hubungan <span class="text-red-500">*</span></span><select name="relationship" required class="h-11 w-full rounded-xl border border-slate-300 bg-white px-3 text-sm"><option value="">Pilih hubungan</option>@foreach($relationships as $value=>$label)<option value="{{ $value }}" @selected(old('relationship')===$value) @disabled(($value==='ayah_kandung'&&$hasAyahKandung)||($value==='ibu_kandung'&&$hasIbuKandung))>{{ $label }}</option>@endforeach</select></label></div>
                    <div class="grid gap-2 sm:grid-cols-3"><label class="flex min-h-10 items-center gap-2 rounded-xl bg-slate-50 px-3 text-xs font-semibold text-slate-700"><input type="checkbox" name="is_primary" value="1" @checked(old('is_primary')) class="h-4 w-4 rounded text-brand-600">Wali utama</label><label class="flex min-h-10 items-center gap-2 rounded-xl bg-slate-50 px-3 text-xs font-semibold text-slate-700"><input type="checkbox" name="is_financial_responsible" value="1" @checked(old('is_financial_responsible',true)) class="h-4 w-4 rounded text-brand-600">Akses keuangan</label><label class="flex min-h-10 items-center gap-2 rounded-xl bg-slate-50 px-3 text-xs font-semibold text-slate-700"><input type="checkbox" name="can_access_academic" value="1" @checked(old('can_access_academic',true)) class="h-4 w-4 rounded text-brand-600">Akses akademik</label></div><button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-xl bg-brand-600 px-4 text-sm font-bold text-white"><i class="fas fa-user-plus"></i>Buat dan hubungkan</button>
                </form>
            </div>
        </div>
    </section>
</div>
@endsection
