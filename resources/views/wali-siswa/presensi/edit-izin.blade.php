@extends('layouts.app')

@section('title', 'Edit Pengajuan Izin')
@section('page-title', 'Edit Pengajuan Izin')

@section('content')
<div class="min-w-0 w-full space-y-4">

    {{-- Page Header --}}
    <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
        <div>
            <h4 class="text-lg font-bold text-slate-800">Edit Pengajuan Izin</h4>
            <p class="mt-0.5 text-sm text-slate-500">
                <i class="fa-solid fa-user-graduate mr-1"></i>{{ $presensi->siswa->nama_lengkap }}
                <span class="mx-1">·</span>
                <i class="fa-solid fa-school mr-1"></i>{{ $presensi->siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
    </div>

    @php
        $keteranganText = preg_replace('/\s*\(Bukti: .+?\)/', '', $presensi->keterangan);
        $keteranganText = preg_replace('/\s*-\s*Diajukan oleh wali siswa.+/', '', $keteranganText);

        $buktiPath = null;
        if (preg_match('/\(Bukti: (.+?)\)/', $presensi->keterangan, $matches)) {
            $buktiPath = $matches[1];
        }

        $extension = $buktiPath ? pathinfo($buktiPath, PATHINFO_EXTENSION) : '';
        $isImage = in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
        $isPdf = strtolower($extension) === 'pdf';
    @endphp

    <div class="mx-auto max-w-3xl">
        {{-- Form Card --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center gap-2 border-b border-slate-200 px-4 py-3">
                <i class="fa-solid fa-edit text-amber-500"></i>
                <h5 class="text-base font-semibold text-slate-800">Edit Pengajuan Izin/Sakit</h5>
            </div>
            <div class="p-4">
                {{-- Info Callout --}}
                <div class="mb-4 flex items-start gap-3 rounded-lg border border-blue-200 bg-blue-50 p-3.5 text-sm text-blue-800">
                    <i class="fa-solid fa-info-circle mt-0.5 shrink-0"></i>
                    <div>
                        <strong>Perhatian:</strong> Anda dapat mengedit pengajuan izin yang belum divalidasi oleh wali kelas.
                        Pastikan data yang dimasukkan sudah benar.
                    </div>
                </div>

                <form action="{{ route('wali-siswa.presensi.update-izin', $presensi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')

                    {{-- Tanggal (Read Only) --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">Tanggal</label>
                        <input type="text"
                               value="{{ \Carbon\Carbon::parse($presensi->tanggal)->locale('id')->isoFormat('dddd, D MMMM YYYY') }}"
                               readonly
                               class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500 cursor-not-allowed">
                        <p class="mt-1 text-xs text-slate-400">Tanggal tidak dapat diubah</p>
                    </div>

                    {{-- Jenis --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">
                            Jenis Izin <span class="text-red-500">*</span>
                        </label>
                        <select name="jenis" required
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('jenis') !border-red-400 !ring-red-400/20 @enderror">
                            <option value="">-- Pilih Jenis Izin --</option>
                            <option value="sakit" {{ old('jenis', $presensi->status) === 'sakit' ? 'selected' : '' }}>Sakit</option>
                            <option value="izin" {{ old('jenis', $presensi->status) === 'izin' ? 'selected' : '' }}>Izin</option>
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
                                  class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm text-slate-800 transition focus:border-brand-500 focus:ring-2 focus:ring-brand-500/20 @error('keterangan') !border-red-400 !ring-red-400/20 @enderror">{{ old('keterangan', trim($keteranganText)) }}</textarea>
                        @error('keterangan')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-slate-400">Maksimal 500 karakter</p>
                    </div>

                    {{-- Bukti Lama --}}
                    @if($buktiPath)
                        <div>
                            <label class="mb-1.5 block text-sm font-bold text-slate-700">Bukti Saat Ini</label>
                            <div class="rounded-lg border border-brand-200 bg-brand-50/30 p-4">
                                @if($isImage)
                                    <div class="mb-3 text-center">
                                        <img src="{{ asset('storage/' . $buktiPath) }}" alt="Bukti" class="mx-auto max-h-[150px] rounded-lg object-contain">
                                    </div>
                                @elseif($isPdf)
                                    <div class="mb-3 text-center">
                                        <i class="fa-solid fa-file-pdf text-4xl text-red-500"></i>
                                        <p class="mt-2 text-xs text-slate-500">File PDF</p>
                                    </div>
                                @else
                                    <div class="mb-3 text-center">
                                        <i class="fa-solid fa-file text-4xl text-slate-400"></i>
                                        <p class="mt-2 text-xs text-slate-500">File {{ strtoupper($extension) }}</p>
                                    </div>
                                @endif

                                <div class="space-y-2">
                                    <a href="{{ asset('storage/' . $buktiPath) }}" target="_blank"
                                       class="flex w-full items-center justify-center gap-1.5 rounded-lg border border-brand-300 px-3 py-2 text-xs font-semibold text-brand-700 transition hover:bg-brand-50">
                                        <i class="fa-solid fa-eye"></i> Lihat Bukti
                                    </a>
                                    <label class="flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                                        <input type="checkbox" name="hapus_bukti" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500/20">
                                        <i class="fa-solid fa-trash text-xs"></i> Hapus bukti ini
                                    </label>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Upload Bukti Baru --}}
                    <div>
                        <label class="mb-1.5 block text-sm font-bold text-slate-700">{{ $buktiPath ? 'Ganti Bukti Baru' : 'Upload Bukti' }}</label>
                        <input type="file" name="bukti" accept=".jpg,.jpeg,.png,.pdf"
                               class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm text-slate-800 file:mr-3 file:rounded-md file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-brand-700 hover:file:bg-brand-100 @error('bukti') !border-red-400 @enderror">
                        @error('bukti')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-slate-400">
                            <i class="fa-solid fa-paperclip mr-1"></i>
                            Format: JPG, PNG, PDF. Maksimal 2MB.
                            @if($buktiPath)
                                <br>Upload file baru akan mengganti bukti lama.
                            @endif
                        </p>
                    </div>

                    {{-- Buttons --}}
                    <div class="flex flex-wrap gap-2 pt-2">
                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600">
                            <i class="fa-solid fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('wali-siswa.presensi.riwayat-izin', $presensi->siswa_id) }}" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            <i class="fa-solid fa-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
