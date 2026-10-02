@extends('layouts.lms-guru')

@section('title', 'Kelas Virtual')
@section('page-title', 'Kelas Virtual (Meeting)')
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
@php $args = [$kelas->id, $mapel->id]; @endphp

<div class="min-w-0 w-full space-y-5"
     x-data="{
        hapusUrl: '', hapusJudul: '', tersalin: null,
        bukaHapus(el) { this.hapusUrl = el.dataset.url; this.hapusJudul = el.dataset.judul; this.$refs.terkait.checked = false; this.$refs.hapusDialog.showModal(); },
        async salin(el) {
            const link = el.dataset.link;
            try {
                if (navigator.clipboard && window.isSecureContext) { await navigator.clipboard.writeText(link); }
                else { const t = document.createElement('textarea'); t.value = link; document.body.appendChild(t); t.select(); document.execCommand('copy'); t.remove(); }
                this.tersalin = el.dataset.id;
                setTimeout(() => { if (this.tersalin === el.dataset.id) this.tersalin = null; }, 2000);
                window.Swal?.fire({ toast: true, position: 'bottom-end', icon: 'success', title: 'Link meeting tersalin', showConfirmButton: false, timer: 2500 });
            } catch (e) {
                window.Swal?.fire({ icon: 'error', title: 'Gagal menyalin link', text: 'Salin tautan secara manual dari tombol Mulai meeting.' });
            }
        },
     }">
    <header class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="min-w-0">
            <h2 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-video text-indigo-600" aria-hidden="true"></i>Kelas virtual</h2>
            <p class="mt-0.5 text-xs text-slate-500">{{ $mapel->nama_mapel }} · Kelas {{ $kelas->nama_kelas }}</p>
        </div>
        <a href="{{ route('guru.lms.meeting.create', $args) }}" class="inline-flex min-h-10 items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-indigo-600 px-4 text-xs font-bold text-white no-underline shadow-sm hover:bg-indigo-700"><i class="fa-solid fa-plus" aria-hidden="true"></i>Jadwalkan meeting</a>
    </header>

    @forelse($meetings as $meeting)
        <article class="min-w-0 rounded-2xl border bg-white p-4 shadow-sm sm:p-5 {{ $meeting->is_active ? 'border-indigo-200' : 'border-slate-200 bg-slate-50' }}">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h3 class="min-w-0 break-words text-base font-extrabold text-slate-900">{{ $meeting->judul }}</h3>
                        <span class="rounded-full px-2.5 py-0.5 text-[10px] font-extrabold {{ $meeting->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-200 text-slate-600' }}">{{ $meeting->is_active ? 'Aktif' : 'Selesai / nonaktif' }}</span>
                    </div>
                    <p class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500">
                        <span class="rounded-md bg-sky-50 px-2 py-0.5 font-bold text-sky-700"><i class="fa-solid fa-video mr-1" aria-hidden="true"></i>{{ ucfirst(str_replace('_', ' ', $meeting->platform)) }}</span>
                        <span><i class="fa-regular fa-calendar mr-1" aria-hidden="true"></i>{{ $meeting->waktu_mulai->translatedFormat('l, d F Y') }}</span>
                        <span><i class="fa-regular fa-clock mr-1" aria-hidden="true"></i>{{ $meeting->waktu_mulai->format('H:i') }} - {{ $meeting->waktu_selesai ? $meeting->waktu_selesai->format('H:i') : 'Selesai' }}</span>
                    </p>
                    @if($meeting->deskripsi)
                        <p class="mt-2 text-sm leading-6 text-slate-600">{{ $meeting->deskripsi }}</p>
                    @endif
                </div>
                <div class="grid shrink-0 grid-cols-2 gap-2 sm:flex sm:flex-wrap md:justify-end">
                    <a href="{{ $meeting->link_meeting }}" target="_blank" rel="noopener" class="inline-flex min-h-10 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl bg-indigo-600 px-3 text-xs font-bold text-white no-underline hover:bg-indigo-700"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>Mulai meeting</a>
                    <button type="button" data-id="{{ $meeting->id }}" data-link="{{ $meeting->link_meeting }}" @click="salin($el)" class="inline-flex min-h-10 items-center justify-center gap-1.5 whitespace-nowrap rounded-xl border px-3 text-xs font-bold transition" :class="tersalin === '{{ $meeting->id }}' ? 'border-emerald-300 bg-emerald-50 text-emerald-700' : 'border-slate-300 bg-white text-slate-700 hover:bg-slate-50'">
                        <i class="fa-regular" :class="tersalin === '{{ $meeting->id }}' ? 'fa-circle-check' : 'fa-copy'" aria-hidden="true"></i><span x-text="tersalin === '{{ $meeting->id }}' ? 'Tersalin' : 'Salin link'">Salin link</span>
                    </button>
                    <a href="{{ route('guru.lms.meeting.edit', [...$args, $meeting->id]) }}" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-xl bg-amber-50 px-3 text-xs font-bold text-amber-700 no-underline ring-1 ring-inset ring-amber-100 hover:bg-amber-100"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>Edit</a>
                    <button type="button" data-url="{{ route('guru.lms.meeting.destroy', [...$args, $meeting->id]) }}" data-judul="{{ $meeting->judul }}" @click="bukaHapus($el)" class="inline-flex min-h-10 items-center justify-center gap-1.5 rounded-xl bg-rose-50 px-3 text-xs font-bold text-rose-700 ring-1 ring-inset ring-rose-100 hover:bg-rose-100"><i class="fa-solid fa-trash" aria-hidden="true"></i>Hapus</button>
                </div>
            </div>
        </article>
    @empty
        <section class="rounded-2xl border border-slate-200 bg-white px-5 py-14 text-center shadow-sm">
            <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-500"><i class="fa-solid fa-video" aria-hidden="true"></i></span>
            <h3 class="mt-4 font-extrabold text-slate-900">Belum ada jadwal kelas virtual</h3>
            <a href="{{ route('guru.lms.meeting.create', $args) }}" class="mt-4 inline-flex min-h-10 items-center gap-1.5 rounded-xl bg-indigo-50 px-4 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-plus" aria-hidden="true"></i>Buat jadwal baru</a>
        </section>
    @endforelse

    <div>{{ $meetings->links() }}</div>

    <dialog x-ref="hapusDialog" @click.self="$el.close()" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <form method="POST" :action="hapusUrl" data-confirmed="true" class="px-5 py-5">
            @csrf
            @method('DELETE')
            <h3 class="flex items-center gap-2 text-base font-extrabold text-slate-900"><i class="fa-solid fa-triangle-exclamation text-rose-600" aria-hidden="true"></i>Hapus meeting?</h3>
            <p class="mt-2 text-sm leading-6 text-slate-600">Jadwal <strong x-text="hapusJudul"></strong> akan dihapus.</p>
            <label class="mt-4 flex cursor-pointer items-start gap-2.5 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs font-semibold text-rose-800">
                <input type="checkbox" name="hapus_terkait" value="1" x-ref="terkait" class="mt-0.5 h-4 w-4 rounded border-rose-300 text-rose-600 focus:ring-rose-500">
                Hapus juga meeting ini dari kelas lain (jika ada duplikat).
            </label>
            <div class="mt-5 flex justify-end gap-2">
                <button type="button" @click="$refs.hapusDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Batal</button>
                <button type="submit" class="min-h-10 rounded-xl bg-rose-600 px-4 text-xs font-bold text-white hover:bg-rose-700">Ya, hapus</button>
            </div>
        </form>
    </dialog>
</div>
@endsection
