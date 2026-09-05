@extends('layouts.app')

@section('title', 'Pratinjau - '.($previewTitle ?? 'Konten LMS'))
@section('page-title', 'Mode Pratinjau')
@section('page-subtitle', 'Tinjauan konten '.($kontenLabel ?? '').' sebagaimana dilihat siswa')

@section('content')
<div
    class="min-w-0 w-full space-y-5"
    data-monitoring-lms-preview
    x-data="{
        note: { type: @js($kontenType === 'latihan' ? 'ujian' : $kontenType), id: @js((string) $kontenId), label: @js($kontenLabel ?? 'Konten'), title: @js($previewTitle ?? '-') },
        noteText: '',
        submittingNote: false,
        openNote(detail = null) {
            if (detail) this.note = detail;
            this.noteText = '';
            this.$nextTick(() => this.$refs.noteDialog.showModal());
        },
        openImage(source) {
            this.$refs.previewImage.src = source;
            this.$refs.imageDialog.showModal();
        }
    }"
    @monitoring-note.window="openNote($event.detail)"
>
    <section class="sticky top-20 z-20 flex flex-col gap-3 rounded-2xl border border-brand-200 bg-white/95 p-4 shadow-lg backdrop-blur sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-start gap-3"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700"><i class="fas fa-eye" aria-hidden="true"></i></span><div class="min-w-0"><h2 class="text-sm font-extrabold text-slate-950">Mode pratinjau aman</h2><p class="mt-1 text-xs leading-5 text-slate-500">Tampilan menyerupai sisi siswa; aksi mengerjakan atau mengumpulkan dinonaktifkan.</p></div></div>
        <button type="button" @click="openNote()" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-comment-dots" aria-hidden="true"></i>Kirim catatan</button>
    </section>

    <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-6" @click="const image = $event.target.closest('[data-expandable-image]'); if (image) openImage(image.src)">
        @yield('preview-content')
    </article>

    <dialog x-ref="imageDialog" class="m-auto w-[calc(100%-2rem)] max-w-5xl overflow-hidden rounded-2xl bg-slate-950 p-0 shadow-2xl backdrop:bg-slate-950/70" @click.self="$el.close()"><div class="relative flex max-h-[88vh] min-h-48 items-center justify-center p-3 sm:p-5"><img x-ref="previewImage" src="" alt="Pratinjau gambar" class="max-h-[82vh] max-w-full rounded-xl object-contain"><button type="button" @click="$refs.imageDialog.close()" class="absolute right-3 top-3 flex h-10 w-10 items-center justify-center rounded-xl bg-white/90 text-slate-700 shadow-lg hover:bg-white" aria-label="Tutup gambar"><i class="fas fa-xmark" aria-hidden="true"></i></button></div></dialog>

    @include('monitoring-lms.partials.modal-catatan')
</div>
@endsection
