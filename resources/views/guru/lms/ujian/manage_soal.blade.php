@extends('layouts.lms-guru')

@php
    $isLatihan = ($tipeUjian ?? 'ujian') === 'latihan';
    $prefix = $isLatihan ? 'guru.lms.latihan' : 'guru.lms.ujian';
    $label = $isLatihan ? 'Latihan' : 'Ujian';
    $args = [$kelas->id, $mapel->id, $ujian->id];
    $relatedCount = $relatedUjianCount ?? 0;
    $smallField = 'block w-full min-w-0 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100';
@endphp

@section('title', 'Kelola Soal: ' . $ujian->judul_ujian)
@section('page-title', 'Kelola Soal')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('scripts')
    {{-- Modul editor soal: dipakai AI Question Generator melalui window.addQuestion, sehingga tetap modul JS (bukan Alpine). Seluruh tampilan memakai utility Tailwind. --}}
    @vite(['resources/js/guru/lms/ujian/manage-soal.js'])
@endpush

@section('content')
<div class="guru-lms-ujian-manage-soal-page min-w-0 w-full space-y-4"
     data-related-count="{{ $relatedCount }}"
     data-storage-base-url="{{ asset('storage') }}">

    <section class="sticky top-20 z-20 flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white/95 p-3 shadow-sm backdrop-blur sm:flex-row sm:items-center sm:justify-between sm:p-4">
        <div class="flex min-w-0 items-center gap-2">
            <a href="{{ route($prefix.'.index', [$kelas->id, $mapel->id]) }}" class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-300 bg-white text-slate-600 no-underline hover:bg-slate-50" title="Kembali" aria-label="Kembali"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i></a>
            <div class="min-w-0">
                <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900 sm:text-base">Kelola soal <span class="rounded-full bg-indigo-600 px-2.5 py-0.5 text-[11px] font-bold text-white" id="totalSoalBadge">0 Soal</span></h2>
                <p class="truncate text-xs text-slate-500" title="{{ $ujian->judul_ujian }}">{{ $label }}: {{ $ujian->judul_ujian }}</p>
            </div>
        </div>
        <div class="grid grid-cols-2 gap-2 sm:flex">
            <button type="button" data-sync-action data-sync-form="toggleStatusForm"
                    data-sync-title="{{ $ujian->is_active ? 'Tarik kembali '.strtolower($label) : 'Rilis '.strtolower($label) }}"
                    data-sync-message="{{ $ujian->is_active ? $label.' akan disembunyikan dari siswa.' : $label.' akan ditampilkan kepada siswa.' }}"
                    title="{{ $ujian->is_active ? 'Sembunyikan dari siswa' : 'Tampilkan ke siswa' }}"
                    class="inline-flex min-h-10 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl px-4 text-xs font-bold ring-1 ring-inset {{ $ujian->is_active ? 'bg-rose-50 text-rose-700 ring-rose-200 hover:bg-rose-100' : 'bg-emerald-50 text-emerald-700 ring-emerald-200 hover:bg-emerald-100' }}">
                <i class="fa-solid {{ $ujian->is_active ? 'fa-eye-slash' : 'fa-eye' }}" aria-hidden="true"></i>{{ $ujian->is_active ? 'Tarik kembali' : 'Rilis '.strtolower($label) }}
            </button>
            <button type="button" data-sync-action data-sync-form="mainForm" data-sync-title="Simpan semua soal" data-sync-message="Semua perubahan soal akan disimpan." class="inline-flex min-h-10 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white shadow-sm hover:bg-indigo-700">
                <i class="fa-solid fa-floppy-disk" aria-hidden="true"></i>Simpan semua
            </button>
        </div>
    </section>

    <div class="flex flex-wrap justify-end gap-2">
        <a href="{{ route($prefix.'.soal.template', $args) }}" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-emerald-200 bg-emerald-50 px-3 text-xs font-bold text-emerald-700 no-underline hover:bg-emerald-100"><i class="fa-solid fa-download" aria-hidden="true"></i>Template Excel</a>
        <button type="button" data-open-dialog="importSoalDialog" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50 px-3 text-xs font-bold text-indigo-700 hover:bg-indigo-100"><i class="fa-solid fa-file-import" aria-hidden="true"></i>Impor dari Excel</button>
        @if($aiQuestionGeneratorEnabled)
            <button type="button" data-open-ai-sidebar title="Buka AI Question Generator" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 px-3 text-xs font-bold text-white shadow-sm hover:from-indigo-700 hover:to-violet-700"><i class="fa-solid fa-robot" aria-hidden="true"></i>AI Question Generator</button>
        @endif
    </div>

    <form action="{{ route($prefix.'.soal.storeAll', $args) }}" method="POST" id="mainForm" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input type="hidden" name="sync_kelas" id="sync_kelas_main" value="0">
        <input type="hidden" name="original_judul" value="{{ $ujian->judul_ujian }}">

        <div id="soalAccordion" class="space-y-3"></div>

        <button type="button" data-add-question class="flex w-full flex-col items-center justify-center gap-1 rounded-2xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 px-4 py-6 text-indigo-700 transition hover:border-indigo-400 hover:bg-indigo-50">
            <span class="flex items-center gap-2 text-sm font-extrabold"><i class="fa-solid fa-circle-plus" aria-hidden="true"></i>Tambah soal baru</span>
            <span class="text-xs text-slate-500">Klik untuk menambah soal ke nomor selanjutnya</span>
        </button>
    </form>

    <form action="{{ route($prefix.'.toggleStatus', $args) }}" method="POST" id="toggleStatusForm" class="hidden">
        @csrf
        <input type="hidden" name="sync_kelas" id="sync_kelas_status" value="0">
    </form>

    <dialog id="syncConfirmDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <div class="px-5 py-5">
            <h3 class="text-base font-extrabold text-slate-900" id="syncModalTitle">Konfirmasi aksi</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600" id="syncModalMessage">Apakah Anda yakin?</p>
            @if($relatedCount > 0)
                <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-indigo-200 bg-indigo-50 p-3 text-xs text-indigo-900">
                    <input type="checkbox" id="syncConfirmCheckbox" checked class="mt-0.5 h-4 w-4 shrink-0 rounded border-indigo-300 text-indigo-600 focus:ring-indigo-500">
                    <span><strong class="block">Terapkan juga ke {{ $relatedCount }} kelas lain</strong>Aksi ini (dan soal-soalnya) diduplikasi ke semua {{ strtolower($label) }} terkait di kelas lain.</span>
                </label>
            @endif
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" data-close-dialog class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" id="btnConfirmSync" class="min-h-10 rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white hover:bg-indigo-700">Ya, lanjutkan</button>
            </div>
        </div>
    </dialog>

    <dialog id="deleteQuestionDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <div class="px-5 py-5">
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-triangle-exclamation text-rose-600" aria-hidden="true"></i>Hapus soal ini?</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Soal dihapus dari daftar. Perubahan tersimpan setelah Anda menekan <strong>Simpan semua</strong>.</p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" data-close-dialog class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="button" data-confirm-remove class="min-h-10 rounded-xl bg-rose-600 px-4 text-xs font-bold text-white hover:bg-rose-700">Hapus</button>
            </div>
        </div>
    </dialog>

    <dialog id="importSoalDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form action="{{ route($prefix.'.soal.import', $args) }}" method="POST" enctype="multipart/form-data" class="px-5 py-5">
            @csrf
            <h3 class="text-base font-extrabold text-slate-900">Impor soal {{ strtolower($label) }} dari Excel</h3>
            <label class="mt-4 block text-xs font-bold text-slate-700">File Excel (.xlsx)
                <input type="file" name="file_soal" accept=".xlsx,.xls" required class="mt-1 block w-full rounded-xl border border-slate-300 px-3 py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700">
            </label>
            <p class="mt-3 flex items-start gap-2 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-900"><i class="fa-solid fa-circle-info mt-0.5" aria-hidden="true"></i><span>Soal yang diimpor akan <strong>ditambahkan</strong> ke daftar soal yang sudah ada. Unduh template terlebih dahulu untuk format yang benar.</span></p>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" data-close-dialog class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white hover:bg-indigo-700"><i class="fa-solid fa-upload" aria-hidden="true"></i>Impor</button>
            </div>
        </form>
    </dialog>
</div>

@include('components.ai-sidebar', [
    'ujianId' => $ujian->id,
    'kelasId' => $kelas->id,
    'mapelId' => $mapel->id,
    'subjectName' => $mapel->nama_mapel,
    'generateUrl' => route($prefix.'.soal.ai-generate', $args),
])

{{-- Template soal baru. Class hook (.soal-item, .question-input, .narasi-input, .type-select, dll.) dibaca manage-soal.js dan AI generator. --}}
<template id="soalTemplate">
    <div class="soal-item overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm" data-index="{INDEX}">
        <input type="hidden" name="soal[{INDEX}][id]" value="{ID}">

        <div class="flex items-center gap-2 px-3 py-2.5 sm:px-4">
            <button type="button" data-remove-question title="Hapus soal" aria-label="Hapus soal" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-xs text-rose-700 ring-1 ring-inset ring-rose-100 hover:bg-rose-100"><i class="fa-solid fa-trash" aria-hidden="true"></i></button>
            <button type="button" data-toggle-soal aria-expanded="false" class="flex min-h-10 min-w-0 flex-1 items-center gap-2 rounded-lg px-2 text-left hover:bg-slate-50">
                <span class="flex h-7 min-w-7 shrink-0 items-center justify-center rounded-lg bg-indigo-600 px-1.5 text-xs font-extrabold text-white"><span class="soal-number">{NUMBER}</span></span>
                <span class="soal-type-badge shrink-0 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">Pilihan Ganda</span>
                <span class="preview-text min-w-0 flex-1 truncate text-xs text-slate-500">(Masukkan pertanyaan...)</span>
                <i class="fa-solid fa-chevron-down shrink-0 text-xs text-slate-400 transition-transform" data-soal-chevron aria-hidden="true"></i>
            </button>
        </div>

        <div class="soal-body hidden space-y-4 border-t border-slate-100 bg-slate-50 px-3 py-4 sm:px-5">
            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_10rem]">
                <label class="block text-xs font-bold text-slate-700">Tipe soal
                    <select name="soal[{INDEX}][tipe_soal]" class="type-select mt-1 {{ $smallField }}">
                        <option value="pilihan_ganda">Pilihan Ganda</option>
                        <option value="pilihan_ganda_kompleks">Pilihan Ganda Kompleks</option>
                        <option value="benar_salah">Benar - Salah</option>
                        <option value="isian_singkat">Isian Singkat</option>
                        <option value="uraian">Uraian / Essay</option>
                    </select>
                </label>
                <label class="block text-xs font-bold text-slate-700">Bobot nilai
                    <input type="number" name="soal[{INDEX}][bobot_nilai]" value="10" min="1" class="mt-1 {{ $smallField }}">
                </label>
            </div>

            <label class="block text-xs font-bold text-slate-700">Narasi / teks bacaan <span class="font-normal text-slate-500">(opsional)</span>
                <textarea name="soal[{INDEX}][narasi]" rows="2" placeholder="Masukkan narasi/teks bacaan jika soal berbasis narasi..." class="narasi-input mt-1 {{ $smallField }}"></textarea>
                <span class="mt-1 block font-normal text-slate-500">Soal dengan narasi yang sama dikelompokkan saat ujian.</span>
            </label>

            <div>
                <label class="block text-xs font-bold text-slate-700">Gambar soal <span class="font-normal text-slate-500">(opsional)</span>
                    <input type="file" name="soal[{INDEX}][image]" accept="image/png,image/jpeg,image/jpg,image/gif" data-preview-image="{INDEX}" class="image-upload mt-1 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-xs file:mr-3 file:rounded-lg file:border-0 file:bg-indigo-50 file:px-3 file:py-1.5 file:font-bold file:text-indigo-700">
                </label>
                <p class="mt-1 text-[11px] text-slate-500"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>Maksimal 2 MB. Format JPG, PNG, atau GIF.</p>
                <input type="hidden" name="soal[{INDEX}][existing_image]" class="existing-image-path" value="{IMAGE_PATH}">
                <div class="image-preview-container mt-2 hidden" id="imagePreview{INDEX}">
                    <div class="flex items-start gap-2 rounded-xl border border-sky-200 bg-white p-2">
                        <img src="" alt="Pratinjau gambar soal" class="preview-img max-h-48 max-w-full rounded-lg border border-slate-200 object-contain">
                        <button type="button" data-remove-image="{INDEX}" title="Hapus gambar" aria-label="Hapus gambar" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-xs text-rose-700 hover:bg-rose-100"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    </div>
                    <p class="mt-1 text-[11px] text-slate-500">Gambar ditampilkan saat siswa mengerjakan soal.</p>
                </div>
            </div>

            <label class="block text-xs font-bold text-slate-700">Pertanyaan
                <textarea name="soal[{INDEX}][pertanyaan]" rows="3" placeholder="Tuliskan pertanyaan..." data-update-preview class="question-input mt-1 {{ $smallField }}">{PERTANYAAN}</textarea>
            </label>

            <div class="rounded-xl border border-slate-200 bg-white p-3">
                <h4 class="mb-3 text-xs font-extrabold text-slate-900">Opsi jawaban &amp; kunci</h4>

                <div class="type-section section-pilihan_ganda">
                    <div class="pg-options-container space-y-2"></div>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <button type="button" data-add-pg-option="pilgan" class="inline-flex min-h-8 items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100"><i class="fa-solid fa-plus" aria-hidden="true"></i>Opsi</button>
                        <button type="button" data-remove-pg-option="pilgan" class="inline-flex min-h-8 items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 text-[11px] font-bold text-rose-700 hover:bg-rose-100"><i class="fa-solid fa-minus" aria-hidden="true"></i>Opsi</button>
                        <span class="text-[11px] text-slate-500">(Min 3, maks 5)</span>
                    </div>
                </div>

                <div class="type-section section-pilihan_ganda_kompleks hidden">
                    <p class="mb-2 rounded-lg bg-sky-50 px-2.5 py-1.5 text-[11px] text-sky-900"><i class="fa-solid fa-circle-info mr-1" aria-hidden="true"></i>Penilaian parsial: <strong class="text-emerald-700">+poin</strong> untuk opsi benar, <strong class="text-rose-700">-poin</strong> untuk opsi salah (min. 0).</p>
                    <div class="pgk-options-container space-y-2"></div>
                    <div class="mt-2 flex flex-wrap items-center gap-2">
                        <button type="button" data-add-pg-option="kompleks" class="inline-flex min-h-8 items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 text-[11px] font-bold text-emerald-700 hover:bg-emerald-100"><i class="fa-solid fa-plus" aria-hidden="true"></i>Opsi</button>
                        <button type="button" data-remove-pg-option="kompleks" class="inline-flex min-h-8 items-center gap-1 rounded-lg border border-rose-200 bg-rose-50 px-2.5 text-[11px] font-bold text-rose-700 hover:bg-rose-100"><i class="fa-solid fa-minus" aria-hidden="true"></i>Opsi</button>
                        <span class="text-[11px] text-slate-500">(Min 3, maks 5)</span>
                    </div>
                </div>

                <div class="type-section section-benar_salah hidden">
                    <table class="w-full table-fixed text-left text-xs">
                        <colgroup><col><col class="w-28"></colgroup>
                        <thead class="text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="pb-1">Pernyataan</th><th class="pb-1">Kunci</th></tr></thead>
                        <tbody class="bs-tbody"></tbody>
                    </table>
                    <button type="button" data-add-bs-row class="mt-2 inline-flex min-h-8 items-center gap-1 rounded-lg border border-slate-300 bg-white px-2.5 text-[11px] font-bold text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-plus" aria-hidden="true"></i>Baris</button>
                </div>

                <div class="type-section section-isian_singkat hidden">
                    <label class="block text-xs font-bold text-slate-700">Kunci jawaban
                        <input type="text" name="soal[{INDEX}][kunci_jawaban_isian]" placeholder="Jawaban singkat..." class="mt-1 {{ $smallField }}">
                    </label>
                    <p class="mt-1 text-[11px] italic text-slate-500">AI Assistant tersedia saat koreksi untuk membantu menilai jawaban yang mirip.</p>
                </div>

                <div class="type-section section-uraian hidden">
                    <p class="rounded-lg bg-sky-50 px-3 py-2 text-xs text-sky-900">Soal uraian dikoreksi manual.</p>
                </div>
            </div>
        </div>
    </div>
</template>

<template id="bsRowTemplate">
    <tr>
        <td class="py-1 pr-2"><input type="text" name="soal[{INDEX}][pilihan_jawaban_bs][{ROW}][pernyataan]" placeholder="Pernyataan..." class="{{ $smallField }}"></td>
        <td class="py-1">
            <select name="soal[{INDEX}][pilihan_jawaban_bs][{ROW}][kunci]" class="{{ $smallField }}">
                <option value="B">Benar</option>
                <option value="S">Salah</option>
            </select>
        </td>
    </tr>
</template>

{{-- Guru berhak melihat kunci jawaban; makeVisible membuka field yang di-$hidden pada model untuk form edit ini. --}}
<template id="soalDataTemplate">@json($soalList->makeVisible(['kunci_jawaban', 'jawaban_benar']))</template>
@endsection
