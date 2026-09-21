@extends('layouts.app')

@section('title', 'Detail Rapor')
@section('page-title', 'Detail Rapor')

@include('partials.anti-screenshot')

@section('content')
<div class="min-w-0 w-full space-y-4 protected-content" x-data="{ showRequestDownload: false }">

    {{-- Page Header --}}
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Detail Rapor - Semester {{ $rapor->semester }}</h4>
            <p class="mt-0.5 text-sm text-slate-500">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $rapor->siswa->nama_lengkap }}
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            @php
                $activeDownload = \App\Models\RequestDownloadRapor::where('rapor_id', $rapor->id)
                    ->where('user_id', auth()->id())
                    ->where('status', 'disetujui')
                    ->where('download_expired_at', '>', now())
                    ->first();
                $pendingRequest = \App\Models\RequestDownloadRapor::where('rapor_id', $rapor->id)
                    ->where('user_id', auth()->id())
                    ->where('status', 'menunggu')
                    ->exists();
            @endphp

            @if($activeDownload)
                <a href="{{ route('wali-siswa.rapor.download', $activeDownload->download_token) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">
                    <i class="fa-solid fa-download"></i> Download Rapor
                </a>
                <span class="self-center text-xs text-slate-500">Berlaku hingga {{ $activeDownload->download_expired_at->format('d/m/Y H:i') }}</span>
            @elseif($pendingRequest)
                    <button disabled class="inline-flex items-center gap-1.5 rounded-lg bg-slate-300 px-3 py-2 text-xs font-semibold text-white cursor-not-allowed">
                    <i class="fa-solid fa-hourglass-half"></i> Menunggu Persetujuan
                </button>
            @else
                <button type="button" @click="showRequestDownload = true"
                         class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-brand-700">
                    <i class="fa-solid fa-download"></i> Minta Download
                </button>
            @endif

            <a href="{{ route('wali-siswa.rapor.anak', $rapor->siswa_id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Rapor Header --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="p-4">
            <div class="flex flex-col gap-4 lg:flex-row">
                <div class="flex-1">
                    <h5 class="mb-3 flex items-center gap-2 text-base font-semibold text-slate-800">
                        <i class="fa-solid fa-file-alt text-brand-600"></i> Rapor Semester {{ $rapor->semester }}
                    </h5>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        {{-- Student --}}
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 aspect-square shrink-0 items-center justify-center overflow-hidden rounded-full border border-white bg-brand-50 shadow-sm">
                                @if($rapor->siswa->user && $rapor->siswa->user->foto_profil)
                                    <img src="{{ asset('storage/' . $rapor->siswa->user->foto_profil) }}" alt="avatar" class="h-full w-full rounded-full object-cover">
                                @elseif($rapor->siswa->foto)
                                    <img src="{{ asset('storage/' . $rapor->siswa->foto) }}" alt="avatar" class="h-full w-full rounded-full object-cover">
                                @else
                                    <span class="flex h-full w-full items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">
                                        {{ strtoupper(substr($rapor->siswa->nama_lengkap, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">Nama Siswa</div>
                                <div class="font-semibold text-slate-800">{{ $rapor->siswa->nama_lengkap }}</div>
                            </div>
                        </div>

                        {{-- NISN --}}
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400">
                                <i class="fa-solid fa-id-card"></i>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">NISN</div>
                                <div class="font-semibold text-slate-800">{{ $rapor->siswa->nisn }}</div>
                            </div>
                        </div>

                        {{-- Kelas --}}
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400">
                                <i class="fa-solid fa-school"></i>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">Kelas</div>
                                <div class="font-semibold text-slate-800">{{ $rapor->siswa->kelas->nama_kelas ?? '-' }}</div>
                            </div>
                        </div>

                        {{-- Tahun Ajaran --}}
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-400">
                                <i class="fa-solid fa-calendar"></i>
                            </div>
                            <div>
                                <div class="text-[11px] text-slate-500">Tahun Ajaran</div>
                                <div class="font-semibold text-slate-800">{{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Score Summary --}}
                @if($rapor->raporNilai->count() > 0)
                    <div class="flex shrink-0 items-center justify-center lg:w-48">
                        <div class="w-full rounded-xl bg-slate-50 p-4 text-center">
                            <div class="mb-2 text-xs text-slate-500">Nilai Rata-rata</div>
                            @php
                                $avg = $rapor->raporNilai->avg('nilai_angka');
                                $colorClass = $avg >= 85 ? 'text-emerald-600' : ($avg >= 70 ? 'text-brand-600' : ($avg >= 60 ? 'text-amber-600' : 'text-red-600'));
                                $predikat = $avg >= 90 ? 'A' : ($avg >= 75 ? 'B' : ($avg >= 60 ? 'C' : 'D'));
                                $predikatBg = $avg >= 85 ? 'bg-emerald-100 text-emerald-700' : ($avg >= 70 ? 'bg-brand-100 text-brand-700' : ($avg >= 60 ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700'));
                            @endphp
                            <div class="text-3xl font-bold {{ $colorClass }}">{{ number_format($avg, 2) }}</div>
                            <span class="mt-1.5 inline-block rounded-full px-3 py-0.5 text-xs font-bold {{ $predikatBg }}">
                                Predikat {{ $predikat }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Nilai Per Mata Pelajaran --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
            <i class="fa-solid fa-chart-bar text-brand-600"></i>
            <h5 class="text-base font-semibold text-slate-800">Nilai Per Mata Pelajaran</h5>
        </div>
        <div class="p-4">
            @if($rapor->raporNilai->isEmpty())
                <div class="flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mt-0.5 shrink-0"></i>
                    <div>Belum ada nilai yang diinput untuk rapor ini.</div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 bg-slate-50">
                                <th class="px-4 py-2.5 text-center text-[11px] font-extrabold uppercase tracking-wide text-slate-500 w-10">No</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Mata Pelajaran</th>
                                <th class="px-4 py-2.5 text-center text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Nilai</th>
                                <th class="px-4 py-2.5 text-center text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Predikat</th>
                                <th class="px-4 py-2.5 text-left text-[11px] font-extrabold uppercase tracking-wide text-slate-500">Capaian Kompetensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($rapor->raporNilai as $index => $nilai)
                                @php
                                    $nilaiAkhir = $nilai->nilai_angka;
                                    if ($nilaiAkhir >= 90) {
                                        $predikat = 'A';
                                        $badgeClass = 'bg-emerald-100 text-emerald-700';
                                    } elseif ($nilaiAkhir >= 75) {
                                        $predikat = 'B';
                                        $badgeClass = 'bg-brand-100 text-brand-700';
                                    } elseif ($nilaiAkhir >= 60) {
                                        $predikat = 'C';
                                        $badgeClass = 'bg-amber-100 text-amber-700';
                                    } else {
                                        $predikat = 'D';
                                        $badgeClass = 'bg-red-100 text-red-700';
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-4 py-2.5 text-center text-slate-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-2.5 font-semibold text-slate-800">{{ $nilai->mataPelajaran->nama_mapel ?? '-' }}</td>
                                    <td class="px-4 py-2.5 text-center text-base font-bold text-slate-800">{{ number_format($nilaiAkhir, 2) }}</td>
                                    <td class="px-4 py-2.5 text-center">
                                        <span class="inline-block rounded-full px-2.5 py-0.5 text-xs font-bold {{ $badgeClass }}">{{ $predikat }}</span>
                                    </td>
                                    <td class="px-4 py-2.5 text-sm text-slate-500">
                                        {{ $nilai->deskripsi ?: '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t border-slate-200 bg-slate-50">
                                <td colspan="2" class="px-4 py-2.5 text-right font-bold text-slate-700">Rata-rata:</td>
                                <td class="px-4 py-2.5 text-center text-base font-bold text-brand-600">{{ number_format($rapor->raporNilai->avg('nilai_angka'), 2) }}</td>
                                <td colspan="2"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Catatan Wali Kelas --}}
    @if($rapor->catatan_wali_kelas)
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
                <i class="fa-solid fa-comment-alt text-brand-600"></i>
                <h5 class="text-base font-semibold text-slate-800">Catatan Wali Kelas</h5>
            </div>
            <div class="p-4">
                <div class="rounded-lg border-l-4 border-l-brand-500 bg-slate-50 p-4 text-sm text-slate-700">
                    {{ $rapor->catatan_wali_kelas }}
                </div>
            </div>
        </div>
    @endif

    {{-- Alpine Dialog for Request Download --}}
    <template x-teleport="body">
        <div x-show="showRequestDownload" x-cloak
             class="fixed inset-0 z-[70] flex items-center justify-center bg-black/60 p-4"
             @keydown.escape.window="showRequestDownload = false">
            <div @click.outside="showRequestDownload = false"
                 x-show="showRequestDownload" x-transition
                 class="relative w-full max-w-md overflow-hidden rounded-xl bg-white shadow-2xl">
                <form action="{{ route('wali-siswa.rapor.request-download', $rapor->id) }}" method="POST">
                    @csrf
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <h5 class="flex items-center gap-2 text-sm font-bold text-slate-800">
                            <i class="fa-solid fa-download text-brand-600"></i> Minta Download Rapor
                        </h5>
                        <button type="button" @click="showRequestDownload = false" class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                    <div class="p-5">
                        <p class="mb-4 text-sm text-slate-500">Kirim permintaan untuk mendapatkan link download rapor. Setelah disetujui, link download akan tersedia selama 24 jam.</p>
                        <div>
                            <label class="mb-1.5 block text-sm font-bold text-slate-700">Alasan (opsional)</label>
                            <textarea name="alasan" rows="2" placeholder="Contoh: Untuk keperluan pendaftaran sekolah lanjutan..."
                                      class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3">
                        <button type="button" @click="showRequestDownload = false" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100">
                            Batal
                        </button>
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-4 py-2 text-sm font-bold text-white transition hover:bg-brand-700">
                            <i class="fa-solid fa-paper-plane"></i> Kirim Permintaan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
