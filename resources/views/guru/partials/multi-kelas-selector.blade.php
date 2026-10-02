{{-- Pilihan duplikasi konten ke kelas lain (mapel sama). Dipakai Materi, Tugas, Ujian, Forum, dan Kelas Virtual. --}}
{{-- Required: $kelasLain (collection of GuruPengajarKelas with kelas relation) --}}
@if(isset($kelasLain) && $kelasLain->count() > 0)
@php
    $ids = isset($relatedClassIds) ? collect($relatedClassIds)->map(fn ($id) => (string) $id)->all() : [];
    $hasRelated = ! empty($ids);
    $semuaKelas = $kelasLain->pluck('kelas_id')->map(fn ($id) => (string) $id)->values()->all();
@endphp

<div class="rounded-2xl border border-indigo-200 bg-indigo-50/50" x-data="{ aktif: @js($hasRelated), dipilih: @js($ids), semua: @js($semuaKelas) }">
    <label class="flex cursor-pointer items-center gap-3 px-4 py-3">
        <input type="checkbox" x-model="aktif" @change="if (! aktif) dipilih = []" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
        <span class="text-sm font-extrabold text-slate-900"><i class="fa-solid fa-copy mr-1.5 text-indigo-600" aria-hidden="true"></i>Tambahkan juga ke kelas lain</span>
    </label>

    <div x-cloak x-show="aktif" class="space-y-3 border-t border-indigo-100 px-4 py-3">
        <p class="text-xs text-slate-500">Konten akan diduplikasi ke kelas yang dipilih (mata pelajaran yang sama).</p>

        @if($hasRelated)
            <p class="flex items-start gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3 py-2 text-xs leading-5 text-sky-900">
                <i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i>
                <span>Item ini terdeteksi juga ada di <strong>{{ count($ids) }}</strong> kelas lain. Kelas terkait sudah dicentang agar pembaruan tersinkron.</span>
            </p>
        @endif

        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($kelasLain as $gpk)
                <label class="flex min-w-0 cursor-pointer items-center gap-2.5 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-700 hover:border-indigo-300">
                    <input type="checkbox" name="kelas_tambahan[]" value="{{ $gpk->kelas_id }}" x-model="dipilih" class="h-4 w-4 shrink-0 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                    <span class="min-w-0 truncate">{{ $gpk->kelas->nama_kelas ?? 'Kelas #'.$gpk->kelas_id }}</span>
                    @if(in_array((string) $gpk->kelas_id, $ids, true))
                        <span class="ml-auto shrink-0 rounded-full bg-sky-100 px-2 py-0.5 text-[10px] font-bold text-sky-800">Terkait</span>
                    @endif
                </label>
            @endforeach
        </div>

        <div class="flex gap-2">
            <button type="button" @click="dipilih = [...semua]" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 hover:bg-slate-50">Pilih semua</button>
            <button type="button" @click="dipilih = []" class="inline-flex min-h-9 items-center rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal semua</button>
        </div>
    </div>
</div>
@endif
