@extends('layouts.app')

@section('title', 'Edit Rapor')
@section('page-title', 'Edit Rapor')
@section('page-subtitle', $rapor->siswa->nama_lengkap . ' · ' . $rapor->kelas->nama_kelas)

@section('content')
@php
    $importLocked = $rapor->status !== 'draft' || $rapor->siswa->validasi_rapor_wali;
    $returnUrl = route('wali.rapor.index', ['semester' => $rapor->semester, 'jenis_rapor' => $rapor->jenis_rapor, 'siswa_id' => $rapor->siswa_id]);
    $alignmentOptions = ['left' => 'Kiri', 'center' => 'Tengah', 'right' => 'Kanan', 'justify' => 'Rata kiri-kanan'];
    $deskripsiAlignment = old('deskripsi_alignment', $rapor->deskripsi_alignment ?? 'left');
    $keteranganEkstraAlignment = old('keterangan_ekstra_alignment', $rapor->keterangan_ekstra_alignment ?? 'left');
    $catatanAlignment = old('catatan_alignment', $rapor->catatan_alignment ?? 'center');
    $extraRows = old('kegiatan_ekstra', $kegiatanEkstra->map(fn ($item) => ['kegiatan_nama' => $item->kegiatan_nama, 'predikat' => $item->predikat, 'keterangan' => $item->keterangan])->values()->all());
    $periodRapor = $rapor->tahunAjaran?->getRaporPeriod($rapor->semester, $rapor->jenis_rapor);
    $totalPresensiPeriode = $periodRapor ? \App\Models\Presensi::where('siswa_id', $rapor->siswa_id)->where('kelas_id', $rapor->kelas_id)->whereBetween('tanggal', [$periodRapor['start'], $periodRapor['end']])->count() : 0;
    $gradeCount = $rapor->raporNilai->count();
    $gradeAverage = $gradeCount ? $rapor->raporNilai->avg('nilai_angka') : null;
@endphp
<div class="min-w-0 w-full space-y-4"
    x-data="{
        sick: {{ (int) old('jumlah_sakit', $rapor->jumlah_sakit) }},
        leave: {{ (int) old('jumlah_izin', $rapor->jumlah_izin) }},
        absent: {{ (int) old('jumlah_alpha', $rapor->jumlah_alpha) }},
        descriptionAlignment: '{{ $deskripsiAlignment }}',
        extraAlignment: '{{ $keteranganEkstraAlignment }}',
        noteAlignment: '{{ $catatanAlignment }}',
        extras: {{ \Illuminate\Support\Js::from($extraRows) }},
        syncAll: true,
        orderDirty: false,
        orderStatus: '',
        moveGrade(button, direction) {
            const item = button.closest('[data-grade-item]');
            const list = item?.parentElement;
            if (!item || !list) return;
            if (direction === 'up' && item.previousElementSibling) list.insertBefore(item, item.previousElementSibling);
            if (direction === 'down' && item.nextElementSibling) list.insertBefore(item.nextElementSibling, item);
            this.orderDirty = true;
            this.orderStatus = 'Urutan berubah; simpan urutan sebelum meninggalkan halaman.';
        },
        async saveOrder() {
            const ids = Array.from(this.$refs.gradeList.querySelectorAll('[data-grade-item]')).map(item => Number(item.dataset.id));
            this.orderStatus = 'Menyimpan urutan...';
            try {
                const response = await fetch('{{ route('wali.rapor.reorder-nilai', $rapor->id) }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ order: ids, sync_all: this.syncAll })
                });
                if (!response.ok) throw new Error('Gagal menyimpan urutan');
                this.orderDirty = false;
                this.orderStatus = 'Urutan tersimpan.';
            } catch (error) {
                this.orderStatus = 'Urutan belum tersimpan. Coba lagi.';
            }
        }
    }">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wide text-sky-700">Rapor {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }} · Semester {{ ucfirst($rapor->semester) }}</p><h1 class="mt-1 text-lg font-extrabold text-slate-900">{{ $rapor->siswa->nama_lengkap }}</h1><p class="mt-1 text-xs text-slate-500">{{ $rapor->kelas->nama_kelas }} · {{ $rapor->tahunAjaran->nama_tahun_ajaran ?? '—' }} · {{ ucfirst($rapor->status) }}</p></div><a href="{{ $returnUrl }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Daftar rapor</a></div>
        <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4"><a href="{{ route('wali.rapor.export-excel', $rapor->id) }}" class="inline-flex min-h-9 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-xs font-bold text-emerald-800">Export / template Excel</a><button type="button" @click="$refs.importDialog.showModal()" @disabled($importLocked) class="inline-flex min-h-9 items-center rounded-lg border border-emerald-200 px-3 text-xs font-bold text-emerald-800 disabled:cursor-not-allowed disabled:opacity-40">Import Excel</button><button type="button" @click="$refs.resetDialog.showModal()" class="inline-flex min-h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-bold text-amber-900">Reset nilai</button><button type="button" @click="$refs.formatDialog.showModal()" class="inline-flex min-h-9 items-center rounded-lg border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-800">Salin format</button><a href="{{ route('wali.rapor.preview', $rapor->id) }}" target="_blank" rel="noopener" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Pratinjau</a></div>
        @if($importLocked)<p class="mt-2 text-[11px] text-amber-800">{{ $rapor->status !== 'draft' ? 'Tarik kembali rapor yang sudah diterbitkan sebelum import.' : 'Batalkan kiriman ke Ketua sebelum import.' }}</p>@endif
    </header>
    @if($errors->any())<div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800" role="alert"><strong>Ada data yang perlu diperbaiki:</strong><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

    <form action="{{ route('wali.rapor.update', $rapor->id) }}" method="POST" id="raporUpdateForm" class="space-y-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="kegiatan_ekstra_present" value="1">
        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h2 class="text-sm font-extrabold text-slate-900">Kehadiran</h2><p class="mt-1 text-xs leading-5 text-slate-600">Sumber: presensi kelas pada periode {{ $rapor->jenis_rapor === 'tengah_semester' ? 'PTS' : 'PAS' }} semester {{ ucfirst($rapor->semester) }}. {{ $totalPresensiPeriode }} record tercatat.@if($periodRapor) {{ \Carbon\Carbon::parse($periodRapor['start'])->locale('id')->translatedFormat('d M Y') }}–{{ \Carbon\Carbon::parse($periodRapor['end'])->locale('id')->translatedFormat('d M Y') }}.@endif</p>
            <div class="mt-3 grid grid-cols-2 gap-2 sm:grid-cols-4">@foreach(['jumlah_sakit' => ['Sakit', 'sick'], 'jumlah_izin' => ['Izin', 'leave'], 'jumlah_alpha' => ['Alpha', 'absent']] as $field => [$label, $model])<label class="text-xs font-bold text-slate-700">{{ $label }}<input type="number" name="{{ $field }}" x-model.number="{{ $model }}" min="0" required class="mt-1 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm font-normal text-slate-800"></label>@endforeach<div class="rounded-lg bg-sky-50 p-3"><p class="text-[11px] font-bold text-sky-700">Total tidak hadir</p><p class="text-lg font-extrabold text-sky-900" x-text="Number(sick || 0) + Number(leave || 0) + Number(absent || 0)"></p></div></div>
            <div class="mt-3 flex flex-wrap items-center justify-between gap-2"><p class="text-[11px] text-slate-500">Simpan perubahan form sebelum sinkron ulang.</p><button type="submit" form="syncAttendanceForm" class="inline-flex min-h-9 items-center rounded-lg border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-800">Sinkron dari Presensi</button></div>
        </section>

        <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="grade-heading"><div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 px-4 py-3"><div><h2 id="grade-heading" class="text-sm font-extrabold text-slate-900">Nilai mata pelajaran</h2><p class="text-[11px] text-slate-500">{{ $gradeCount }} mapel · Rata-rata {{ $gradeAverage === null ? '—' : number_format((float) $gradeAverage, 2, ',', '.') }}</p></div><div class="flex flex-wrap items-center gap-2"><label class="flex items-center gap-1.5 text-[11px] text-slate-600"><input type="checkbox" x-model="syncAll" class="rounded border-slate-300"> Sinkron urutan ke kelas saya</label><button type="button" @click="saveOrder()" :disabled="!orderDirty" class="inline-flex min-h-9 items-center rounded-lg bg-sky-700 px-3 text-xs font-bold text-white disabled:cursor-not-allowed disabled:bg-slate-300">Simpan urutan</button></div></div>
            <p x-show="orderStatus" x-text="orderStatus" class="border-b border-slate-100 px-4 py-2 text-[11px] text-slate-600" role="status"></p>
            <div x-ref="gradeList" class="divide-y divide-slate-100">
                @forelse($rapor->raporNilai as $raporNilai)
                    @php $defaultKelompok = $raporNilai->mataPelajaran->kelompok ?? null; @endphp
                    <article data-grade-item data-id="{{ $raporNilai->id }}" class="grid gap-3 px-4 py-3 sm:grid-cols-[minmax(140px,170px)_minmax(0,1fr)] lg:grid-cols-[minmax(200px,300px)_minmax(140px,170px)_minmax(0,1fr)] lg:items-start">
                        <div class="flex min-w-0 items-start justify-between gap-3 sm:col-span-2 lg:col-span-1"><div class="min-w-0"><h3 class="text-sm font-bold text-slate-900">{{ $raporNilai->mataPelajaran->nama_mapel ?? 'Mata pelajaran tidak tersedia' }}</h3><p class="mt-0.5 text-[11px] text-slate-500">Nilai {{ $raporNilai->nilai_angka === null ? '—' : number_format((float) $raporNilai->nilai_angka, 2, ',', '.') }}@if($defaultKelompok) · Kelompok default {{ $defaultKelompok }}@endif</p></div><div class="flex shrink-0 gap-1"><button type="button" @click="moveGrade($el, 'up')" aria-label="Naikkan urutan {{ $raporNilai->mataPelajaran->nama_mapel ?? 'mapel' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-700">↑</button><button type="button" @click="moveGrade($el, 'down')" aria-label="Turunkan urutan {{ $raporNilai->mataPelajaran->nama_mapel ?? 'mapel' }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-slate-300 text-slate-700">↓</button></div></div>
                        <div class="space-y-2">@if($rapor->jenis_rapor === 'akhir_semester')<label class="block text-[11px] font-bold text-slate-700">Kelompok<select name="kelompok_override[{{ $raporNilai->id }}]" class="mt-1 h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-xs font-normal"><option value="" @selected($raporNilai->kelompok_override === null)>Tidak diatur</option><option value="A" @selected($raporNilai->kelompok_override === 'A')>A</option><option value="B" @selected($raporNilai->kelompok_override === 'B')>B</option></select></label>@endif<label class="flex items-center gap-2 text-[11px] font-bold text-slate-700"><input type="hidden" name="visible[{{ $raporNilai->id }}]" value="0"><input type="checkbox" name="visible[{{ $raporNilai->id }}]" value="1" @checked($raporNilai->is_visible) class="rounded border-slate-300"> Tampil di rapor</label></div>
                        <label class="block min-w-0 text-[11px] font-bold text-slate-700">Deskripsi capaian<textarea name="deskripsi[{{ $raporNilai->id }}]" rows="3" :style="{ textAlign: descriptionAlignment }" placeholder="Deskripsi capaian kompetensi" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-normal leading-5 text-slate-800">{{ old('deskripsi.'.$raporNilai->id, $raporNilai->deskripsi) }}</textarea></label>
                    </article>
                @empty
                    <p class="px-4 py-9 text-center text-sm text-slate-500">Belum ada nilai rapor. Gunakan Reset Nilai setelah nilai siswa tersedia.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><div class="flex flex-wrap items-center justify-between gap-2"><h2 class="text-sm font-extrabold text-slate-900">Kegiatan ekstrakurikuler</h2><button type="button" @click="extras.push({ kegiatan_nama: '', predikat: '', keterangan: '' })" class="inline-flex min-h-9 items-center rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-xs font-bold text-emerald-800">+ Tambah kegiatan</button></div><div class="mt-3 space-y-2"><template x-for="(extra, index) in extras" :key="index"><div class="grid gap-2 rounded-lg border border-slate-200 p-3 sm:grid-cols-[minmax(120px,1fr)_90px_minmax(140px,1.2fr)_auto] sm:items-end"><label class="text-[11px] font-bold text-slate-700">Kegiatan<input type="text" :name="'kegiatan_ekstra[' + index + '][kegiatan_nama]'" x-model="extra.kegiatan_nama" maxlength="100" placeholder="Nama kegiatan" class="mt-1 h-9 w-full rounded-lg border border-slate-300 px-2 text-xs font-normal"></label><label class="text-[11px] font-bold text-slate-700">Predikat<select :name="'kegiatan_ekstra[' + index + '][predikat]'" x-model="extra.predikat" class="mt-1 h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-xs font-normal"><option value="">—</option><option value="A">A</option><option value="B">B</option><option value="C">C</option></select></label><label class="text-[11px] font-bold text-slate-700">Keterangan<input type="text" :name="'kegiatan_ekstra[' + index + '][keterangan]'" x-model="extra.keterangan" :style="{ textAlign: extraAlignment }" class="mt-1 h-9 w-full rounded-lg border border-slate-300 px-2 text-xs font-normal"></label><button type="button" @click="extras.splice(index, 1)" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-bold text-rose-800">Hapus</button></div></template></div></section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h2 class="text-sm font-extrabold text-slate-900">Catatan wali kelas</h2><label class="mt-3 block text-xs font-bold text-slate-700">Catatan dan saran untuk siswa<textarea name="catatan_wali_kelas" rows="5" :style="{ textAlign: noteAlignment }" placeholder="Siswa menunjukkan peningkatan yang baik..." class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 text-sm font-normal leading-5 text-slate-800">{{ old('catatan_wali_kelas', $rapor->catatan_wali_kelas) }}</textarea></label></section>

        <section class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm"><h2 class="text-sm font-extrabold text-slate-900">Pengaturan tampilan</h2><div class="mt-3 grid gap-3 sm:grid-cols-3">@foreach(['deskripsi_alignment' => ['Deskripsi mapel', 'descriptionAlignment', $deskripsiAlignment], 'keterangan_ekstra_alignment' => ['Keterangan ekstra', 'extraAlignment', $keteranganEkstraAlignment], 'catatan_alignment' => ['Catatan wali', 'noteAlignment', $catatanAlignment]] as $field => [$label, $model, $selected])<label class="text-xs font-bold text-slate-700">{{ $label }}<select name="{{ $field }}" x-model="{{ $model }}" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800">@foreach($alignmentOptions as $value => $option)<option value="{{ $value }}" @selected($selected === $value)>{{ $option }}</option>@endforeach</select></label>@endforeach</div></section>

        <div class="sticky bottom-2 z-10 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur"><p class="text-[11px] leading-4 text-slate-600">Perubahan deskripsi, kegiatan, dan kehadiran tersimpan bersama.</p><button type="submit" class="inline-flex min-h-10 items-center gap-2 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white"><i class="fas fa-floppy-disk" aria-hidden="true"></i>Simpan perubahan</button></div>
    </form>

    <form id="syncAttendanceForm" action="{{ route('wali.rapor.kehadiran-auto', $rapor->id) }}" method="POST" class="hidden">
        @csrf
    </form>
    <dialog x-ref="importDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Import rapor Excel</h2></div><form action="{{ route('wali.rapor.import-excel', $rapor->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-4">
        @csrf
        <p class="text-xs leading-5 text-slate-600">Gunakan file export dari rapor yang sama. Import mengganti kegiatan ekstrakurikuler dan hanya diizinkan pada draft yang belum dikirim.</p><label class="block text-xs font-bold text-slate-700">File .xlsx / .xls, maks. 5 MB<input type="file" name="file" accept=".xlsx,.xls" required class="mt-1 block w-full rounded-lg border border-slate-300 p-2 text-xs font-normal"></label><div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.importDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white">Upload & import</button></div>
    </form></dialog>
    <dialog x-ref="resetDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Reset nilai rapor?</h2></div><p class="px-4 py-4 text-xs leading-5 text-slate-700">Nilai angka dibuat ulang dari data nilai siswa terbaru. Deskripsi yang diedit dan urutan mapel akan hilang.</p><form action="{{ route('wali.rapor.reset-nilai', $rapor->id) }}" method="POST" class="flex justify-end gap-2 border-t border-slate-100 p-4">
        @csrf
        <button type="button" @click="$refs.resetDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-amber-600 px-3 text-xs font-bold text-white">Ya, reset</button></form></dialog>
    <dialog x-ref="formatDialog" class="w-[calc(100%-2rem)] max-w-xl rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Salin format rapor</h2></div><form action="{{ route('wali.rapor.apply-format', $rapor->id) }}" method="POST" class="space-y-4 p-4">
        @csrf
        <p class="text-xs leading-5 text-slate-600">Salin format dari rapor {{ $rapor->siswa->nama_lengkap }} ke draft lain pada semester dan jenis rapor yang sama.</p><fieldset><legend class="text-xs font-bold text-slate-800">Target</legend><div class="mt-2 grid grid-cols-2 gap-2"><label class="flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 p-2 text-xs text-slate-700"><input type="radio" name="scope" value="kelas_ini" checked class="!m-0 h-4 w-4 shrink-0 accent-sky-700"><span class="leading-5">Kelas ini</span></label><label class="flex min-h-10 items-center gap-2 rounded-lg border border-slate-200 p-2 text-xs text-slate-700"><input type="radio" name="scope" value="semua_kelas_wali" @disabled($kelasList->count() <= 1) class="!m-0 h-4 w-4 shrink-0 accent-sky-700"><span class="leading-5">Semua kelas saya</span></label></div></fieldset><fieldset><legend class="text-xs font-bold text-slate-800">Bagian yang disalin</legend><div class="mt-2 grid grid-cols-2 gap-2">@foreach(['include_order' => 'Urutan mapel', 'include_deskripsi' => 'Deskripsi', 'include_display' => 'Tampil / kelompok', 'include_kegiatan' => 'Kegiatan ekstra', 'include_catatan' => 'Catatan wali', 'include_alignment' => 'Alignment'] as $field => $label)<label class="rounded-lg border border-slate-200 p-2 text-xs text-slate-700"><input type="checkbox" name="{{ $field }}" value="1" checked> {{ $label }}</label>@endforeach</div></fieldset><label class="flex items-start gap-2 text-xs text-slate-700"><input type="checkbox" name="overwrite_filled" value="1" checked><span>Timpa isi yang sudah ada di rapor target</span></label><div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.formatDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Terapkan</button></div>
    </form></dialog>
</div>
@endsection
