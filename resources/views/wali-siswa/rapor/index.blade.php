@extends('layouts.app')

@section('title', 'Rapor - ' . $siswa->nama_lengkap)
@section('page-title', 'Rapor')

@section('content')
<div class="min-w-0 w-full space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Rapor</h4>
            <p class="mt-0.5 text-sm text-slate-500">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-1">·</span>
                <i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <a href="{{ route('wali-siswa.dashboard') }}" class="inline-flex items-center gap-1.5 self-start rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- Student Info Card --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4">
        <div class="flex min-w-0 items-start gap-3 sm:items-center sm:gap-4">
            <div class="flex h-14 w-14 aspect-square shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-brand-50 shadow-md sm:h-16 sm:w-16">
                @if($siswa->user && $siswa->user->foto_profil)
                    <img src="{{ asset('storage/' . $siswa->user->foto_profil) }}" alt="avatar" class="h-full w-full rounded-full object-cover">
                @elseif($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="avatar" class="h-full w-full rounded-full object-cover">
                @else
                    <span class="flex h-full w-full items-center justify-center rounded-full bg-brand-600 text-xl font-bold text-white">
                        {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                    </span>
                @endif
            </div>
            <div class="min-w-0">
                <h5 class="break-words text-base font-semibold leading-snug text-slate-800 sm:text-lg">{{ $siswa->nama_lengkap }}</h5>
                <div class="mt-1 flex flex-wrap gap-x-3 gap-y-1 text-xs text-slate-500 sm:gap-x-4">
                    <span><i class="fa-solid fa-id-card mr-1"></i>NISN: {{ $siswa->nisn }}</span>
                    <span><i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}</span>
                    <span><i class="fa-solid fa-building mr-1"></i>{{ $siswa->cabang->nama_cabang ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Daftar Rapor --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center justify-between border-b border-slate-200 px-4 py-3">
            <h5 class="flex items-center gap-2 text-base font-semibold text-slate-800">
                <i class="fa-solid fa-file-alt text-brand-600"></i> Daftar Rapor
            </h5>
            <span class="rounded-full bg-brand-100 px-2.5 py-0.5 text-xs font-bold text-brand-700">{{ $rapor->count() }} Rapor</span>
        </div>
        <div class="p-4">
            @if(isset($locked) && $locked)
                <div class="flex items-start gap-3 rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                    <i class="fa-solid fa-lock mt-0.5 shrink-0"></i>
                    <div>
                        <strong>Akses Terkunci.</strong><br>
                        Rapor belum dapat dilihat karena belum divalidasi oleh Wali Kelas.
                        Silakan hubungi Wali Kelas atau selesaikan administrasi jika diperlukan.
                    </div>
                </div>
            @elseif($rapor->isEmpty())
                <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mt-0.5 shrink-0"></i>
                    <div>Belum ada rapor yang tersedia untuk siswa ini.</div>
                </div>
            @else
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($rapor as $r)
                        @php
                            $tanggalRilis = $r->tanggal_rilis ?? $r->tanggal_terbit ?? $r->created_at;
                        @endphp
                        <div class="rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <div class="p-4">
                                <div class="mb-2.5 flex items-start justify-between">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                        <i class="fa-solid fa-book-open"></i>
                                    </div>
                                    <span class="rounded-full bg-brand-600 px-2.5 py-0.5 text-[11px] font-bold text-white">Semester {{ $r->semester }}</span>
                                </div>

                                <h5 class="mb-1 text-sm font-bold text-slate-800">{{ $r->tahunAjaran->nama_tahun_ajaran ?? 'Tahun Ajaran' }}</h5>
                                <p class="mb-2.5 text-xs text-slate-500">
                                    <i class="fa-solid fa-calendar mr-1"></i>
                                    {{ $tanggalRilis ? $tanggalRilis->locale('id')->translatedFormat('d F Y') : 'Tanggal rilis belum tersedia' }}
                                </p>

                                @if($r->nilai_rata_rata)
                                    <div class="mb-3">
                                        <div class="mb-1.5 flex items-center justify-between">
                                            <span class="text-[11px] font-bold text-slate-500">Nilai Rata-rata</span>
                                            @php
                                                $avg = $r->nilai_rata_rata;
                                                $colorClass = $avg >= 85 ? 'text-emerald-600' : ($avg >= 70 ? 'text-brand-600' : ($avg >= 60 ? 'text-amber-600' : 'text-red-600'));
                                                $barColor = $avg >= 85 ? 'bg-emerald-500' : ($avg >= 70 ? 'bg-brand-500' : ($avg >= 60 ? 'bg-amber-500' : 'bg-red-500'));
                                                $percentage = ($avg / 100) * 100;
                                            @endphp
                                            <strong class="{{ $colorClass }}">{{ number_format($avg, 2) }}</strong>
                                        </div>
                                        <div class="h-2 w-full overflow-hidden rounded-full bg-slate-200">
                                            <div class="h-full rounded-full {{ $barColor }} transition-all duration-700"
                                                 x-data x-init="$el.style.width = '{{ min($percentage, 100) }}%'"></div>
                                        </div>
                                    </div>
                                @endif

                                <a href="{{ route('wali-siswa.rapor.detail', $r->id) }}" class="flex w-full items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-brand-700">
                                    <i class="fa-solid fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
