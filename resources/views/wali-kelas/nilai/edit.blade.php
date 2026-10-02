@extends('layouts.app')

@section('title', 'Edit Nilai Siswa')
@section('page-title', 'Edit Nilai Siswa')
@section('page-subtitle', $siswa->nama_lengkap . ' · ' . $kelas->nama_kelas)

@section('content')
@php
    $isKelasAkhir = $kelas->isTingkatAkhir();
    $countGuruUpdate = $nilaiData->filter(fn ($nilai) => $nilai->hasGuruUpdate()
        && $nilai->guru_terakhir_simpan_at
        && (!$nilai->wali_terakhir_edit_at || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at)))->count();
@endphp
<div class="min-w-0 w-full space-y-4"
    x-data="{ previewMapel: '', previewDiff: {}, previewCanSync: false, syncMapel: '', syncAction: '' }">
    <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div class="min-w-0"><p class="text-xs font-bold uppercase tracking-wide text-sky-700">Edit nilai · Semester {{ ucfirst($semester) }}</p><h1 class="mt-1 text-lg font-extrabold text-slate-900">{{ $siswa->nama_lengkap }}</h1><p class="mt-1 text-xs text-slate-500">{{ $kelas->nama_kelas }} · {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? 'Tahun ajaran belum tersedia' }} · NIS {{ $siswa->nis ?: '—' }} · NISN {{ $siswa->nisn ?: '—' }}</p></div>
            <div class="flex w-full flex-wrap gap-2 sm:w-auto">
                <a href="{{ route('wali.nilai.show', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-arrow-left" aria-hidden="true"></i>Detail nilai</a>
                <a href="{{ route('wali.nilai.download-template', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-download" aria-hidden="true"></i>Template</a>
                <button type="button" @click="$refs.importDialog.showModal()" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-emerald-200 bg-emerald-50 px-3 text-xs font-bold text-emerald-800"><i class="fas fa-file-import" aria-hidden="true"></i>Import Excel</button>
            </div>
        </div>
    </header>

    @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert"><p class="font-bold">Ada data yang perlu diperbaiki:</p><ul class="mt-1 list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    @if($countGuruUpdate > 0)
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-xs leading-5 text-amber-900"><strong>{{ $countGuruUpdate }} mapel memiliki perubahan dari guru.</strong> Buka mapel dan bandingkan nilai sebelum memilih sinkronisasi. Nilai edit Wali tidak ditimpa otomatis.</div>
    @endif

    <form action="{{ route('wali.nilai.update', $siswa->id) }}" method="POST" id="nilaiForm" class="space-y-4">
        @csrf
        @method('PUT')
        <input type="hidden" name="semester" value="{{ $semester }}">
        <div class="flex flex-wrap items-center justify-between gap-2"><div><h2 class="text-sm font-extrabold text-slate-900">Nilai per mata pelajaran</h2><p class="text-xs text-slate-500">{{ $mataPelajaranList->count() }} mapel · Kosong tidak sama dengan 0.</p></div><span class="rounded-full bg-sky-50 px-3 py-1 text-xs font-bold text-sky-800">KKM acuan 70</span></div>

        @if($mataPelajaranList->isEmpty())
            <p class="rounded-xl border border-slate-200 bg-white px-4 py-8 text-center text-sm text-slate-500">Belum ada mata pelajaran untuk kelas ini.</p>
        @else
            <div class="grid items-start gap-3 xl:grid-cols-2">
                @foreach($mataPelajaranList as $mapel)
                    @php
                        $nilai = $nilaiData[$mapel->id] ?? null;
                        $guruBaru = $nilai && $nilai->hasGuruUpdate() && $nilai->guru_terakhir_simpan_at
                            && (!$nilai->wali_terakhir_edit_at || $nilai->guru_terakhir_simpan_at->gt($nilai->wali_terakhir_edit_at));
                        $fields = [];
                        foreach (['tugas', 'latihan', 'uh'] as $group) {
                            for ($i = 1; $i <= 5; $i++) {
                                $field = $group.'_'.$i;
                                $fields[$field] = old("nilai.{$mapel->id}.{$field}", $nilai?->$field);
                            }
                        }
                        foreach (['pts', 'pas', 'to_1', 'to_2', 'to_3', 'upk', 'ujian_praktek'] as $field) {
                            $fields[$field] = old("nilai.{$mapel->id}.{$field}", $nilai?->$field);
                        }
                    @endphp
                    <details class="group overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm" @if(collect($errors->keys())->contains(fn ($key) => str_starts_with($key, "nilai.{$mapel->id}."))) open @endif
                        x-data="{ values: {{ \Illuminate\Support\Js::from($fields) }}, average(group) { let scores = []; for (let i = 1; i <= 5; i++) { let value = this.values[group + '_' + i]; if (value !== '' && value !== null && value !== undefined && Number.isFinite(Number(value))) scores.push(Number(value)); } return scores.length ? scores.reduce((a, b) => a + b, 0) / scores.length : null; }, final() { let parts = [this.average('tugas'), this.average('latihan'), this.average('uh'), this.values.pts, this.values.pas]; if (parts.every(v => v === null || v === '' || v === undefined)) return null; return ((Number(parts[0]) || 0) + (Number(parts[1]) || 0) + 2 * (Number(parts[2]) || 0) + 3 * (Number(parts[3]) || 0) + 3 * (Number(parts[4]) || 0)) / 10; }, display(value, digits = 1) { return value === null || value === '' || value === undefined ? '—' : Number(value).toFixed(digits); } }">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 marker:hidden">
                            <span class="min-w-0"><span class="block truncate text-sm font-bold text-slate-900">{{ $mapel->nama_mapel }}</span><span class="text-[11px] text-slate-500">{{ $mapel->kode_mapel }} · <span x-text="'Nilai akhir ' + display(final(), 2)"></span></span></span>
                            <span class="flex shrink-0 items-center gap-2">@if($guruBaru)<span class="rounded-full bg-amber-50 px-2 py-1 text-[11px] font-bold text-amber-800">Update guru</span>@endif<i class="fas fa-chevron-down text-xs text-slate-400 transition-transform group-open:rotate-180" aria-hidden="true"></i></span>
                        </summary>
                        <div class="space-y-4 border-t border-slate-100 px-4 py-4">
                            <input type="hidden" name="nilai[{{ $mapel->id }}][mata_pelajaran_id]" value="{{ $mapel->id }}">
                            @foreach(['tugas' => 'Tugas', 'latihan' => 'Latihan', 'uh' => 'Ulangan harian'] as $group => $title)
                                <fieldset><legend class="text-xs font-bold text-slate-800">{{ $title }} <span class="font-normal text-slate-500">· rata-rata <span x-text="display(average('{{ $group }}'))"></span></span></legend>
                                    <div class="mt-2 grid grid-cols-5 gap-1.5 sm:gap-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @php $field = $group.'_'.$i; @endphp
                                            <label class="min-w-0 text-[11px] font-semibold text-slate-500"><span class="block text-center">{{ $i }}</span><input type="number" name="nilai[{{ $mapel->id }}][{{ $field }}]" x-model="values.{{ $field }}" value="{{ $fields[$field] }}" min="0" max="100" step="0.01" inputmode="decimal" aria-label="{{ $title }} {{ $i }} {{ $mapel->nama_mapel }}" class="mt-1 h-10 w-full min-w-0 rounded-lg border border-slate-300 bg-white px-1 text-center text-sm font-semibold text-slate-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100"></label>
                                        @endfor
                                    </div>
                                </fieldset>
                            @endforeach
                            <fieldset><legend class="text-xs font-bold text-slate-800">Ujian semester</legend><div class="mt-2 grid grid-cols-2 gap-2">@foreach(['pts' => 'PTS', 'pas' => 'PAS'] as $field => $title)<label class="text-xs font-semibold text-slate-600">{{ $title }}<input type="number" name="nilai[{{ $mapel->id }}][{{ $field }}]" x-model="values.{{ $field }}" value="{{ $fields[$field] }}" min="0" max="100" step="0.01" inputmode="decimal" class="mt-1 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm text-slate-900"></label>@endforeach</div></fieldset>
                            @if($isKelasAkhir)
                                <fieldset><legend class="text-xs font-bold text-slate-800">Penilaian tingkat akhir</legend><div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3">@foreach(['to_1' => 'TO 1', 'to_2' => 'TO 2', 'to_3' => 'TO 3', 'upk' => 'UPK', 'ujian_praktek' => 'Ujian praktik'] as $field => $title)<label class="text-xs font-semibold text-slate-600">{{ $title }}<input type="number" name="nilai[{{ $mapel->id }}][{{ $field }}]" x-model="values.{{ $field }}" value="{{ $fields[$field] }}" min="0" max="100" step="0.01" inputmode="decimal" class="mt-1 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm text-slate-900"></label>@endforeach</div></fieldset>
                            @endif
                            <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-3"><p class="text-xs text-slate-500">Perkiraan nilai akhir <strong class="text-slate-900" x-text="display(final(), 2)"></strong></p>
                                @if($nilai)
                                    <div class="flex gap-2">
                                        <button type="button" data-mapel="{{ $mapel->nama_mapel }}" data-diff="{{ json_encode($nilai->diffWithGuru()) }}" data-sync-action="{{ route('wali.nilai.sync-guru', $nilai->id) }}" data-can-sync="{{ $guruBaru ? '1' : '0' }}" @click="previewMapel = $el.dataset.mapel; previewDiff = JSON.parse($el.dataset.diff); previewCanSync = $el.dataset.canSync === '1'; syncMapel = previewMapel; syncAction = $el.dataset.syncAction; $refs.previewDialog.showModal()" class="inline-flex min-h-9 items-center rounded-lg border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-800">Bandingkan guru</button>
                                        @if($guruBaru)<button type="button" data-mapel="{{ $mapel->nama_mapel }}" data-sync-action="{{ route('wali.nilai.sync-guru', $nilai->id) }}" @click="syncMapel = $el.dataset.mapel; syncAction = $el.dataset.syncAction; $refs.syncDialog.showModal()" class="inline-flex min-h-9 items-center rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-bold text-amber-900">Sinkronkan</button>@endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
            <div class="sticky bottom-2 z-10 flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-200 bg-white/95 p-3 shadow-lg backdrop-blur">
                <p class="max-w-xl text-[11px] leading-4 text-slate-600">Nilai 0 tetap dihitung; kolom kosong tidak. Simpan terlebih dahulu sebelum berpindah ke Rapor.</p>
                <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-sky-700 px-4 text-xs font-bold text-white hover:bg-sky-800"><i class="fas fa-floppy-disk" aria-hidden="true"></i>Simpan semua perubahan</button>
            </div>
        @endif
    </form>

    <dialog x-ref="previewDialog" class="w-[calc(100%-2rem)] max-w-lg rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()">
        <div class="border-b border-slate-100 px-4 py-3"><div class="flex items-center justify-between gap-2"><h2 class="text-sm font-extrabold text-slate-900">Bandingkan nilai guru</h2><button type="button" @click="$refs.previewDialog.close()" aria-label="Tutup" class="text-slate-500">✕</button></div><p class="mt-1 text-xs text-slate-500" x-text="previewMapel"></p></div>
        <div class="max-h-[60vh] overflow-y-auto p-4"><p class="mb-3 text-xs text-slate-600">Nilai saat ini dibandingkan dengan snapshot terakhir guru. Sinkronisasi hanya terjadi setelah konfirmasi.</p><p x-show="Object.keys(previewDiff).length === 0" class="rounded-lg bg-emerald-50 p-3 text-xs text-emerald-800">Tidak ada perbedaan dengan snapshot guru.</p><div x-show="Object.keys(previewDiff).length > 0" class="divide-y divide-slate-100 rounded-lg border border-slate-200"><template x-for="entry in Object.entries(previewDiff)" :key="entry[0]"><div class="grid grid-cols-3 gap-2 px-3 py-2 text-xs"><span class="font-semibold text-slate-800" x-text="entry[0].replaceAll('_', ' ').toUpperCase()"></span><span class="text-center text-slate-600" x-text="'Guru: ' + (entry[1].guru ?? '—')"></span><span class="text-right font-bold text-slate-900" x-text="'Kini: ' + (entry[1].current ?? '—')"></span></div></template></div></div>
        <div class="flex justify-end gap-2 border-t border-slate-100 p-4"><button type="button" @click="$refs.previewDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Tutup</button><button type="button" x-show="previewCanSync && Object.keys(previewDiff).length > 0" @click="$refs.previewDialog.close(); $refs.syncDialog.showModal()" class="min-h-10 rounded-lg bg-amber-500 px-3 text-xs font-bold text-white">Sinkronkan dari guru</button></div>
    </dialog>

    <dialog x-ref="syncDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()">
        <div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Sinkronisasi nilai guru</h2><p class="mt-1 text-xs text-slate-500" x-text="syncMapel"></p></div>
        <p class="px-4 py-4 text-xs leading-5 text-slate-700">Nilai edit Wali untuk mapel ini akan diganti oleh snapshot guru. Perubahan pada form lain yang belum disimpan tidak ikut tersimpan.</p>
        <form :action="syncAction" method="POST" class="flex justify-end gap-2 border-t border-slate-100 p-4">@csrf
            <input type="hidden" name="semester" value="{{ $semester }}"><input type="hidden" name="siswa_id" value="{{ $siswa->id }}"><button type="button" @click="$refs.syncDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-amber-500 px-3 text-xs font-bold text-white">Ya, sinkronkan</button></form>
    </dialog>

    <dialog x-ref="importDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()">
        <div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Import nilai Excel</h2><p class="mt-1 text-xs text-slate-500">Semester {{ ucfirst($semester) }} · {{ $siswa->nama_lengkap }}</p></div>
        <form action="{{ route('wali.nilai.import', $siswa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-4">@csrf
            <input type="hidden" name="semester" value="{{ $semester }}"><p class="rounded-lg bg-sky-50 p-3 text-xs leading-5 text-sky-900">Gunakan template untuk siswa ini; jangan ubah kolom kode mata pelajaran.</p><label class="block text-xs font-bold text-slate-700">File Excel (.xlsx / .xls)<input type="file" name="file" accept=".xlsx,.xls" required class="mt-1 block w-full rounded-lg border border-slate-300 bg-white p-2 text-xs"></label><a href="{{ route('wali.nilai.download-template', ['siswa' => $siswa->id, 'semester' => $semester]) }}" class="inline-flex text-xs font-bold text-sky-700 underline">Unduh template</a><div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.importDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white">Upload & import</button></div></form>
    </dialog>
</div>
@endsection
