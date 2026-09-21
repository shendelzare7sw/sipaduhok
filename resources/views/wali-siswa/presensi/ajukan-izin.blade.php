@extends('layouts.app')

@section('title', 'Ajukan Izin - ' . $siswa->nama_lengkap)
@section('page-title', 'Ajukan Izin')

@section('content')
<div class="min-w-0 w-full space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Pengajuan Izin/Sakit</h4>
            <p class="mt-0.5 text-sm text-slate-500">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-1">·</span>
                <i class="fa-solid fa-school mr-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
    </div>

    {{-- Student Info Card --}}
    <div class="rounded-xl border border-slate-200 bg-white p-3.5 shadow-sm sm:p-4">
        <div class="flex min-w-0 items-start gap-3 sm:items-center sm:gap-4">
            <div class="flex h-14 w-14 aspect-square shrink-0 items-center justify-center overflow-hidden rounded-full border-2 border-white bg-brand-50 shadow-md sm:h-16 sm:w-16">
                @if($siswa->user && $siswa->user->foto_profil)
                    <img src="{{ asset('storage/' . $siswa->user->foto_profil) }}" alt="avatar" class="h-full w-full object-cover rounded-full">
                @elseif($siswa->foto)
                    <img src="{{ asset('storage/' . $siswa->foto) }}" alt="avatar" class="h-full w-full object-cover rounded-full">
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

    <div class="mx-auto max-w-2xl space-y-4">
        {{-- Form Card --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
                <i class="fa-solid fa-file-medical text-brand-600"></i>
                <h5 class="text-base font-semibold text-slate-800">Form Pengajuan Izin/Sakit</h5>
            </div>
            <div class="p-4">
                {{-- Info Callout --}}
                <div class="mb-4 flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3.5 text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mt-0.5 shrink-0"></i>
                    <div>
                        <strong>Informasi:</strong> Sebagai wali siswa/wali, Anda mengajukan izin ketidakhadiran untuk anak Anda.
                        Pastikan melampirkan bukti yang valid seperti surat dokter atau keterangan izin.
                    </div>
                </div>

                <form action="{{ route('wali-siswa.presensi.store-izin', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    {{-- Tanggal --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">
                            Tanggal <span class="text-red-500">*</span>
                        </label>
                        <input type="date" name="tanggal" value="{{ old('tanggal') }}" required
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('tanggal') !border-red-400 !ring-red-400/20 @enderror">
                        @error('tanggal')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-400">Pilih tanggal ketidakhadiran anak Anda</p>
                    </div>

                    {{-- Jenis --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">
                            Jenis Izin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" required
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('jenis') !border-red-400 !ring-red-400/20 @enderror">
                            <option value="">-- Pilih Jenis Izin --</option>
                            <option value="sakit" {{ old('jenis') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ old('jenis') === 'izin' ? 'selected' : '' }}>Izin</option>
                        </select>
                        @error('jenis')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Keterangan --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">
                            Keterangan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="keterangan" rows="4" required placeholder="Jelaskan alasan ketidakhadiran anak Anda secara detail..."
                                  class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('keterangan') !border-red-400 !ring-red-400/20 @enderror">{{ old('keterangan') }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-400">Maksimal 500 karakter</p>
                    </div>

                    {{-- Upload Bukti --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Upload Bukti</label>
                        <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-800 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 @error('bukti') !border-red-400 @enderror">
                        @error('bukti')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-400">
                            <i class="fa-solid fa-paperclip mr-1"></i>
                            Format: JPG, PNG, PDF. Maksimal 2MB.<br>
                            Contoh: Surat dokter, surat keterangan, atau foto resep obat
                        </p>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                            <i class="fa-solid fa-paper-plane"></i> Ajukan Izin
                        </button>
                        <a href="{{ route('wali-siswa.presensi.anak', $siswa->id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            <i class="fa-solid fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Panduan --}}
        <div class="rounded-xl border border-slate-200 border-l-4 border-l-brand-500 bg-white p-4 shadow-sm">
            <h6 class="mb-2.5 flex items-center gap-2 font-bold text-slate-800">
                <i class="fa-solid fa-lightbulb text-amber-500"></i> Panduan Pengajuan Izin
            </h6>
            <ul class="space-y-1.5 pl-4 text-sm text-slate-600 list-disc marker:text-slate-400">
                <li>Pengajuan izin akan divalidasi oleh Wali Kelas</li>
                <li>Pastikan bukti yang dilampirkan jelas dan valid</li>
                <li>Untuk sakit, lampirkan surat dokter atau foto resep</li>
                <li>Untuk izin keperluan keluarga, berikan keterangan yang jelas</li>
                <li>Ajukan izin maksimal H-1 atau pada hari yang sama jika mendadak</li>
                <li>Pengajuan izin yang disetujui akan masuk ke rekap presensi anak</li>
            </ul>
        </div>
    </div>

</div>
@endsection
