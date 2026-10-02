@extends('layouts.app')

@section('title', 'Catatan Monitoring')
@section('page-title', 'Catatan Monitoring')
@section('page-subtitle', 'Masukan dari Kepala Sekolah / Wakil Kepala Sekolah / Admin')

@section('content')
<div class="min-w-0 w-full space-y-5">
    @if($catatan->isEmpty())
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fa-solid fa-inbox" aria-hidden="true"></i></span>
            <h2 class="mt-4 font-extrabold text-slate-900">Belum ada catatan</h2>
            <p class="mt-1 text-sm text-slate-500">Catatan dari pimpinan akan muncul di sini.</p>
        </section>
    @else
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            @foreach($catatan as $c)
                @php $belumDibaca = is_null($c->dibaca_pada); @endphp
                <a href="{{ route('guru.lms.catatan-monitoring.show', $c->id) }}" class="block min-w-0 border-b border-slate-100 p-4 no-underline transition last:border-b-0 hover:bg-slate-50 sm:p-5 {{ $belumDibaca ? 'border-l-4 border-l-brand-500 bg-brand-50/40' : '' }}">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="flex min-w-0 flex-wrap items-center gap-2 text-sm font-extrabold text-slate-900">
                            <i class="fa-solid fa-user-shield text-brand-600" aria-hidden="true"></i>
                            <span class="min-w-0 truncate">{{ $c->pengirim->name ?? 'Pimpinan' }}</span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-[10px] font-bold uppercase text-slate-600">{{ str_replace('_', ' ', $c->pengirim_role) }}</span>
                        </p>
                        @if($belumDibaca)
                            <span class="rounded-full bg-brand-600 px-2.5 py-0.5 text-[10px] font-extrabold text-white">Baru</span>
                        @endif
                    </div>

                    <p class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-600">
                        <span class="rounded-md bg-amber-50 px-2 py-0.5 text-[10px] font-extrabold uppercase text-amber-700">{{ $c->kontenLabel() }}</span>
                        <strong class="min-w-0 break-words text-slate-900">{{ $c->kontenJudul() }}</strong>
                        @if($c->mataPelajaran)<span class="text-slate-500">· {{ $c->mataPelajaran->nama_mapel }}</span>@endif
                        @if($c->kelas)<span class="text-slate-500">· {{ $c->kelas->nama_kelas }}</span>@endif
                    </p>

                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ Str::limit($c->isi_catatan, 180) }}</p>

                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2 text-xs">
                        <span class="text-slate-500"><i class="fa-solid fa-clock mr-1" aria-hidden="true"></i>{{ \Carbon\Carbon::parse($c->created_at)->translatedFormat('d F Y, H:i') }}</span>
                        <span class="inline-flex items-center gap-1.5 font-bold text-brand-700">Lihat detail<i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
                    </div>
                </a>
            @endforeach
        </section>

        <div>{{ $catatan->links() }}</div>
    @endif
</div>
@endsection
