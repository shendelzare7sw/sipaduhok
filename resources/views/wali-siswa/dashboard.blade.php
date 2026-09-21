@extends('layouts.app')

@section('title', 'Dashboard Wali Siswa')
@section('page-title', 'Dashboard Wali Siswa')
@section('page-subtitle', 'Monitoring pendidikan & keuangan anak')

@section('content')
<div class="min-w-0 w-full space-y-5">

    @if(isset($message))
        {{-- Empty state --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm border-l-4 border-l-amber-400">
            <div class="px-6 py-12 text-center">
                <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-xl bg-amber-50 text-amber-400">
                    <i class="fa-solid fa-circle-info text-2xl"></i>
                </div>
                <h5 class="text-lg font-bold text-slate-800">Belum Ada Data Anak</h5>
                <p class="mt-1 text-sm text-slate-500">{{ $message }}</p>
            </div>
        </div>
    @else

        {{-- Children Cards --}}
        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
            @foreach($children as $child)
                @php
                    $childSummary = $summary[$child->id] ?? [
                        'can_finance' => false,
                        'can_academic' => false,
                        'total_tagihan' => 0,
                        'total_bayar' => 0,
                        'sisa_tagihan' => 0
                    ];
                    $canFinance = $childSummary['can_finance'] ?? false;
                    $canAcademic = $childSummary['can_academic'] ?? false;
                    $persentaseBayar = $childSummary['total_tagihan'] > 0
                        ? ($childSummary['total_bayar'] / $childSummary['total_tagihan']) * 100
                        : 0;
                    $gradientIndex = $loop->index % 4;
                    $avatarGradients = [
                        'from-blue-500 to-blue-600',
                        'from-violet-500 to-purple-600',
                        'from-cyan-500 to-cyan-600',
                        'from-emerald-500 to-emerald-600',
                    ];
                @endphp
                <div class="rounded-xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                    <div class="p-5 sm:p-6">
                        {{-- Child Header --}}
                        <div class="mb-5 flex items-center gap-3">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br {{ $avatarGradients[$gradientIndex] }} text-lg font-bold text-white">
                                @if($child->user && $child->user->foto_profil)
                                    <img src="{{ asset('storage/' . $child->user->foto_profil) }}" alt="avatar" class="h-full w-full object-cover">
                                @elseif($child->foto)
                                    <img src="{{ asset('storage/' . $child->foto) }}" alt="avatar" class="h-full w-full object-cover">
                                @else
                                    {{ strtoupper(substr($child->nama_lengkap, 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="text-[1.05rem] font-bold leading-snug text-slate-800 break-words">{{ $child->nama_lengkap }}</div>
                                <div class="mt-0.5 flex flex-wrap items-center gap-1.5 text-xs text-slate-500">
                                    <span><i class="fa-solid fa-school mr-1"></i>{{ $child->kelas->nama_kelas ?? '-' }}</span>
                                    <span>·</span>
                                    <span>{{ $child->cabang->nama_cabang ?? '-' }}</span>
                                    @if($child->status === 'lulus')
                                        <span class="rounded-full bg-slate-800 px-2 py-0.5 text-[10px] font-bold text-white">ALUMNI</span>
                                    @endif
                                </div>
                            </div>
                            @if($child->status !== 'lulus')
                                @if($canFinance)
                                    @if($childSummary['sisa_tagihan'] <= 0)
                                        <span class="shrink-0 rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-700"><i class="fa-solid fa-check mr-1"></i>Lunas</span>
                                    @elseif($persentaseBayar >= 50)
                                        <span class="shrink-0 rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-700">Sebagian</span>
                                    @else
                                        <span class="shrink-0 rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-700">Belum Lunas</span>
                                    @endif
                                @else
                                    <span class="shrink-0 rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-500"><i class="fa-solid fa-lock mr-1"></i>Dikelola wali lain</span>
                                @endif
                            @endif
                        </div>

                        @if($canFinance)
                            {{-- Finance Summary --}}
                            <div class="mb-4 grid grid-cols-3 gap-2">
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-3 text-center">
                                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Total Tagihan</div>
                                    <div class="mt-1 text-sm font-bold text-blue-600">Rp {{ number_format($childSummary['total_tagihan'] / 1000, 0) }}K</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-3 text-center">
                                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Dibayar</div>
                                    <div class="mt-1 text-sm font-bold text-emerald-600">Rp {{ number_format($childSummary['total_bayar'] / 1000, 0) }}K</div>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-2 py-3 text-center">
                                    <div class="text-[10px] font-semibold uppercase tracking-wide text-slate-500">Sisa</div>
                                    <div class="mt-1 text-sm font-bold {{ $childSummary['sisa_tagihan'] > 0 ? 'text-red-600' : 'text-emerald-600' }}">Rp {{ number_format($childSummary['sisa_tagihan'] / 1000, 0) }}K</div>
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="mb-4">
                                <div class="mb-1 flex items-center justify-between">
                                    <span class="text-xs text-slate-500">Progress Pembayaran</span>
                                    <span class="text-xs font-bold {{ $persentaseBayar >= 100 ? 'text-emerald-600' : ($persentaseBayar >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ number_format($persentaseBayar, 0) }}%</span>
                                </div>
                                <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                                    <div class="h-full rounded-full transition-all duration-700 {{ $persentaseBayar >= 100 ? 'bg-emerald-500' : ($persentaseBayar >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                                         x-data x-init="$el.style.width = '{{ min($persentaseBayar, 100) }}%'"></div>
                                </div>
                            </div>
                        @else
                            <div class="mb-4 flex items-start gap-2 rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs text-slate-500">
                                <i class="fa-solid fa-lock mt-0.5 shrink-0 text-slate-400"></i>
                                <span>Akses keuangan untuk siswa ini dikelola oleh wali lain.</span>
                            </div>
                        @endif

                        {{-- Action Buttons --}}
                        <div class="grid grid-cols-2 gap-2">
                            @if($canFinance)
                                <a href="{{ route('wali-siswa.tagihan.anak', $child->id) }}" class="flex items-center justify-center gap-1.5 rounded-lg bg-brand-600 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-brand-700 whitespace-nowrap">
                                    <i class="fa-solid fa-wallet"></i> Tagihan
                                </a>
                            @endif
                            @if($canAcademic)
                                <a href="{{ route('wali-siswa.rapor.anak', $child->id) }}" class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-brand-400 hover:text-brand-600 whitespace-nowrap">
                                    <i class="fa-solid fa-file-alt"></i> Rapor
                                </a>
                                @if($child->status !== 'lulus')
                                    <a href="{{ route('wali-siswa.presensi.ajukan-izin', $child->id) }}" class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-brand-400 hover:text-brand-600 whitespace-nowrap">
                                        <i class="fa-solid fa-paper-plane"></i> Ajukan Izin
                                    </a>
                                @else
                                    <span class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-400 opacity-50 cursor-default whitespace-nowrap">
                                        <i class="fa-solid fa-graduation-cap"></i> Lulus
                                    </span>
                                @endif
                                <a href="{{ route('wali-siswa.presensi.riwayat-izin', $child->id) }}" class="flex items-center justify-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-brand-400 hover:text-brand-600 whitespace-nowrap">
                                    <i class="fa-solid fa-history"></i> Riwayat Izin
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Overall Summary (if more than 1 child) --}}
        @if($children->count() > 1 && collect($summary)->contains(fn (array $item): bool => (bool) ($item['can_finance'] ?? false)))
            @php
                $totalSemuaTagihan = collect($summary)->sum('total_tagihan');
                $totalSemuaBayar = collect($summary)->sum('total_bayar');
                $totalSemuaSisa = collect($summary)->sum('sisa_tagihan');
                $totalPersentase = $totalSemuaTagihan > 0 ? ($totalSemuaBayar / $totalSemuaTagihan) * 100 : 0;
            @endphp
            <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <h5 class="flex items-center gap-2 text-base font-semibold text-slate-800">
                        <i class="fa-solid fa-chart-pie text-brand-600"></i> Ringkasan Keseluruhan ({{ $children->count() }} Anak)
                    </h5>
                </div>
                <div class="grid grid-cols-1 divide-y divide-slate-200 md:grid-cols-3 md:divide-x md:divide-y-0">
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-500">
                            <i class="fa-solid fa-receipt"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Total Semua Tagihan</div>
                            <div class="text-base font-bold text-slate-800">Rp {{ number_format($totalSemuaTagihan, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Total Sudah Dibayar</div>
                            <div class="text-base font-bold text-emerald-600">Rp {{ number_format($totalSemuaBayar, 0, ',', '.') }}</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 px-5 py-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $totalSemuaSisa > 0 ? 'bg-red-50 text-red-500' : 'bg-emerald-50 text-emerald-500' }}">
                            <i class="fa-solid fa-wallet"></i>
                        </div>
                        <div>
                            <div class="text-xs text-slate-500">Total Sisa Tagihan</div>
                            <div class="text-base font-bold {{ $totalSemuaSisa > 0 ? 'text-red-600' : 'text-emerald-600' }}">Rp {{ number_format($totalSemuaSisa, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <div class="border-t border-slate-200 px-5 py-4">
                    <div class="mb-2 flex items-center justify-between text-xs text-slate-500">
                        <span>Progress Keseluruhan</span>
                        <span class="font-bold {{ $totalPersentase >= 100 ? 'text-emerald-600' : ($totalPersentase >= 50 ? 'text-amber-600' : 'text-red-600') }}">{{ number_format($totalPersentase, 1) }}%</span>
                    </div>
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-slate-200">
                        <div class="h-full rounded-full transition-all duration-700 {{ $totalPersentase >= 100 ? 'bg-emerald-500' : ($totalPersentase >= 50 ? 'bg-amber-500' : 'bg-red-500') }}"
                             x-data x-init="$el.style.width = '{{ min($totalPersentase, 100) }}%'"></div>
                    </div>
                </div>
            </div>
        @endif

    @endif
</div>
@endsection
