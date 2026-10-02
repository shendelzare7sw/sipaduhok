@extends('layouts.app')

@section('title', 'Arsip Preview - ' . ($previewTitle ?? 'Konten LMS'))
@section('page-title', 'Mode Arsip')
@section('page-subtitle', 'Pratinjau konten ' . ($kontenLabel ?? '') . ' dari arsip Anda')

@section('content')
<div
    class="min-w-0 w-full space-y-5"
    x-data="{
        openImage(source) {
            this.$refs.previewImage.src = source;
            this.$refs.imageDialog.showModal();
        }
    }"
>
    <section class="flex flex-col gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-start gap-3">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-700 ring-1 ring-amber-200"><i class="fa-solid fa-box-archive" aria-hidden="true"></i></span>
            <div class="min-w-0">
                <h2 class="text-sm font-extrabold text-amber-950">Mode arsip (baca saja)</h2>
                <p class="mt-1 text-xs leading-5 text-amber-900/80">
                    Konten ini dari TA <strong>{{ $konten->kelas?->tahunAjaran?->nama_tahun_ajaran ?? '-' }}</strong>.
                    Anda hanya bisa melihat — gunakan tombol "Salin" untuk membuat versi baru di kelas TA aktif.
                </p>
            </div>
        </div>
        <a href="{{ route('guru.lms.arsip.form-salin', [$kontenType, $kontenId]) }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-brand-600 px-4 text-xs font-bold text-white no-underline hover:bg-brand-700"><i class="fa-solid fa-copy" aria-hidden="true"></i>Salin ke kelas aktif</a>
    </section>

    <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" @click="const image = $event.target.closest('[data-expandable-image]'); if (image) openImage(image.src)">
        @yield('preview-content')
    </article>

    <dialog x-ref="imageDialog" class="m-auto w-[calc(100%-2rem)] max-w-5xl overflow-hidden rounded-2xl bg-slate-950 p-0 shadow-2xl backdrop:bg-slate-950/70" @click.self="$el.close()">
        <div class="relative flex max-h-[88vh] min-h-48 items-center justify-center p-3 sm:p-5">
            <img x-ref="previewImage" src="" alt="Pratinjau gambar" class="max-h-[82vh] max-w-full rounded-xl object-contain">
            <button type="button" @click="$refs.imageDialog.close()" class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-xl bg-white/90 text-slate-700 shadow-lg hover:bg-white" aria-label="Tutup gambar"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
        </div>
    </dialog>
</div>
@endsection
