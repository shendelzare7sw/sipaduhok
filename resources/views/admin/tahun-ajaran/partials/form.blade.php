@php
    $returnUrl = $isEdit ? url()->previous(route('admin.tahun-ajaran.index')) : route('admin.tahun-ajaran.index');
    $formAction = $isEdit ? route('admin.tahun-ajaran.update', $tahunAjaran->id) : route('admin.tahun-ajaran.store');
    $dateValue = function (string $field) use ($tahunAjaran) {
        $value = old($field, $tahunAjaran?->{$field});
        return $value ? \Carbon\Carbon::parse($value)->format('Y-m-d') : '';
    };
    $fieldClass = 'mt-1.5 h-11 w-full rounded-xl border bg-white px-3 text-sm text-slate-800 outline-none transition focus:ring-2';
@endphp

<div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_320px]">
    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 p-4 sm:p-5"><div class="min-w-0"><h2 class="text-base font-extrabold text-slate-900">{{ $isEdit ? 'Ubah Periode Akademik' : 'Buat Periode Akademik' }}</h2><p class="mt-1 text-xs text-slate-500">Kolom bertanda bintang wajib diisi.</p></div><a href="{{ $returnUrl }}" class="inline-flex h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-arrow-left" aria-hidden="true"></i><span class="hidden sm:inline">Kembali</span></a></header>

        <form action="{{ $formAction }}" method="POST">@csrf @if($isEdit) @method('PUT') <input type="hidden" name="_return_url" value="{{ $returnUrl }}"> @endif
            <div class="space-y-5 p-4 sm:p-5">
                <section>
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><span class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-50 text-blue-600"><i class="fas fa-calendar" aria-hidden="true"></i></span>Periode Utama</h3>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <label class="block sm:col-span-2"><span class="text-xs font-bold text-slate-700">Nama Tahun Ajaran <span class="text-red-500">*</span></span><input type="text" name="nama_tahun_ajaran" value="{{ old('nama_tahun_ajaran', $tahunAjaran?->nama_tahun_ajaran) }}" placeholder="Contoh: 2026/2027" required class="{{ $fieldClass }} {{ $errors->has('nama_tahun_ajaran') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-slate-200 focus:border-brand-500 focus:ring-brand-500/20' }}">@error('nama_tahun_ajaran')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror<p class="mt-1 text-[11px] text-slate-500">Gunakan format konsisten seperti 2026/2027.</p></label>
                        <label class="block"><span class="text-xs font-bold text-slate-700">Tanggal Mulai <span class="text-red-500">*</span></span><input type="date" name="tanggal_mulai" value="{{ $dateValue('tanggal_mulai') }}" required class="{{ $fieldClass }} {{ $errors->has('tanggal_mulai') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-slate-200 focus:border-brand-500 focus:ring-brand-500/20' }}">@error('tanggal_mulai')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                        <label class="block"><span class="text-xs font-bold text-slate-700">Tanggal Selesai <span class="text-red-500">*</span></span><input type="date" name="tanggal_selesai" value="{{ $dateValue('tanggal_selesai') }}" required class="{{ $fieldClass }} {{ $errors->has('tanggal_selesai') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-slate-200 focus:border-brand-500 focus:ring-brand-500/20' }}">@error('tanggal_selesai')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                    </div>
                </section>

                <section class="rounded-2xl border border-emerald-200 bg-emerald-50/60 p-4">
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-emerald-900"><i class="fas fa-calendar-alt text-emerald-600" aria-hidden="true"></i>Pembagian Semester</h3><p class="mt-1 text-xs leading-5 text-emerald-800">Tentukan awal semester genap. Kosongkan jika ingin memakai pembagian otomatis Juli-Desember dan Januari-Juni.</p>
                    <label class="mt-3 block"><span class="text-xs font-bold text-slate-700">Tanggal Mulai Semester Genap</span><input type="date" name="tanggal_mulai_genap" value="{{ $dateValue('tanggal_mulai_genap') }}" class="{{ $fieldClass }} {{ $errors->has('tanggal_mulai_genap') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-emerald-200 focus:border-emerald-500 focus:ring-emerald-500/20' }}">@error('tanggal_mulai_genap')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror</label>
                </section>

                <section class="rounded-2xl border border-violet-200 bg-violet-50/60 p-4">
                    <h3 class="flex items-center gap-2 text-sm font-extrabold text-violet-900"><i class="fas fa-flag-checkered text-violet-600" aria-hidden="true"></i>Batas Penilaian Tengah Semester</h3><p class="mt-1 text-xs leading-5 text-violet-800">Dipakai untuk menghitung periode kehadiran rapor PTS. Kosongkan untuk batas otomatis tiga bulan.</p>
                    <div class="mt-3 grid gap-4 sm:grid-cols-2"><label class="block"><span class="text-xs font-bold text-slate-700">Akhir PTS Ganjil</span><input type="date" name="tanggal_akhir_pts_ganjil" value="{{ $dateValue('tanggal_akhir_pts_ganjil') }}" class="{{ $fieldClass }} {{ $errors->has('tanggal_akhir_pts_ganjil') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-violet-200 focus:border-violet-500 focus:ring-violet-500/20' }}">@error('tanggal_akhir_pts_ganjil')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror<p class="mt-1 text-[11px] text-slate-500">Contoh: 30 September.</p></label><label class="block"><span class="text-xs font-bold text-slate-700">Akhir PTS Genap</span><input type="date" name="tanggal_akhir_pts_genap" value="{{ $dateValue('tanggal_akhir_pts_genap') }}" class="{{ $fieldClass }} {{ $errors->has('tanggal_akhir_pts_genap') ? 'border-red-400 focus:border-red-500 focus:ring-red-500/20' : 'border-violet-200 focus:border-violet-500 focus:ring-violet-500/20' }}">@error('tanggal_akhir_pts_genap')<span class="mt-1 block text-[11px] font-semibold text-red-600">{{ $message }}</span>@enderror<p class="mt-1 text-[11px] text-slate-500">Contoh: 31 Maret.</p></label></div>
                </section>

                <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-slate-200 p-4 hover:bg-slate-50"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $tahunAjaran?->is_active) ? 'checked' : '' }} class="mt-0.5 h-5 w-5 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span><strong class="block text-sm text-slate-900">Jadikan tahun ajaran aktif</strong><span class="mt-1 block text-xs leading-5 text-slate-500">Periode aktif sebelumnya akan dinonaktifkan secara otomatis.</span></span></label>
            </div>
            <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5"><a href="{{ $returnUrl }}" class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-xs font-bold text-slate-600 no-underline hover:bg-slate-200">Batal</a><button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-save" aria-hidden="true"></i>{{ $isEdit ? 'Simpan Perubahan' : 'Simpan Tahun Ajaran' }}</button></footer>
        </form>
    </section>

    <aside class="space-y-4">
        <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><h2 class="text-sm font-extrabold text-blue-900"><i class="fas fa-route mr-1.5 text-blue-600" aria-hidden="true"></i>Langkah Berikutnya</h2><ol class="mt-3 space-y-2 text-xs leading-5 text-blue-900"><li><strong>1. Simpan periode ini</strong></li><li>2. Pastikan cabang sudah tersedia</li><li>3. Masukkan pengguna sekolah</li><li>4. Buat kelas dan mata pelajaran</li><li>5. Susun jadwal pelajaran</li></ol></section>
        @if($isEdit)
            <section class="rounded-2xl border {{ $tahunAjaran->kelas->count() ? 'border-amber-200 bg-amber-50' : 'border-emerald-200 bg-emerald-50' }} p-4"><h2 class="text-sm font-extrabold {{ $tahunAjaran->kelas->count() ? 'text-amber-900' : 'text-emerald-900' }}">{{ $tahunAjaran->kelas->count() ? 'Data sedang digunakan' : 'Aman untuk diedit' }}</h2><p class="mt-2 text-xs leading-5 {{ $tahunAjaran->kelas->count() ? 'text-amber-800' : 'text-emerald-800' }}">{{ $tahunAjaran->kelas->count() ? $tahunAjaran->kelas->count() . ' kelas menggunakan periode ini. Perubahan tanggal dapat memengaruhi laporan.' : 'Belum ada kelas yang memakai periode ini.' }}</p><p class="mt-3 border-t border-current/10 pt-3 text-[11px] opacity-75">Dibuat {{ $tahunAjaran->created_at->format('d M Y') }} · diperbarui {{ $tahunAjaran->updated_at->format('d M Y') }}</p></section>
        @else
            <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><h2 class="text-sm font-extrabold text-amber-900"><i class="fas fa-lightbulb mr-1.5" aria-hidden="true"></i>Perlu Diperhatikan</h2><ul class="mt-2 list-disc space-y-1 pl-4 text-xs leading-5 text-amber-800"><li>Hanya satu tahun ajaran yang aktif.</li><li>Tanggal selesai harus setelah tanggal mulai.</li><li>Nama konsisten memudahkan pencarian.</li></ul></section>
        @endif
    </aside>
</div>
