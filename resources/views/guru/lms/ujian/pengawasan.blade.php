@extends('layouts.lms-guru')

@section('title', 'Pengawasan Ujian')
@section('page-title', 'Pengawasan Ujian: ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@section('content')
<div class="min-w-0 w-full space-y-5"
     data-url="{{ route('guru.lms.ujian.pengawasan.data', [$kelas->id, $mapel->id, $ujian->id]) }}"
     data-total="{{ max(1, $soalList->count()) }}"
     x-data="{
        siswa: [], stats: {}, terakhir: 'Memuat...', memuat: false, dipilih: null, dimuat: false, timer: null,
        get total() { return Number(this.$root.dataset.total); },
        init() { this.ambil(); this.timer = setInterval(() => this.ambil(), 5000); },
        destroy() { clearInterval(this.timer); },
        async ambil() {
            if (this.memuat) return;
            this.memuat = true;
            try {
                const res = await fetch(this.$root.dataset.url, { headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
                const data = await res.json();
                if (! data.success) throw new Error(data.message || 'Gagal memuat data pengawasan.');
                this.siswa = data.students || [];
                this.stats = data.stats || {};
                this.terakhir = 'Terakhir: ' + new Date(data.generated_at).toLocaleTimeString('id-ID');
            } catch (e) {
                this.terakhir = 'Gagal memuat data';
                console.error('Monitoring fetch error:', e);
            } finally { this.memuat = false; this.dimuat = true; }
        },
        persen(s) { return Math.min(100, Math.max(0, Number(s.progress_percent || 0))); },
        durasi(v) { const d = Math.round(Number(v || 0)); const m = Math.floor(d / 60); return m <= 0 ? `${d % 60} dtk` : `${m} mnt ${d % 60} dtk`; },
        status(s) {
            if (s.is_focus_lost) return ['Keluar fokus', 'bg-rose-50 text-rose-700'];
            if (s.status === 'sedang_mengerjakan' && s.is_online) return ['Aktif', 'bg-emerald-50 text-emerald-700'];
            if (s.status === 'sedang_mengerjakan') return ['Tidak aktif', 'bg-amber-50 text-amber-700'];
            if (s.status === 'selesai' || s.status === 'dinilai') return ['Dikumpulkan', 'bg-indigo-50 text-indigo-700'];
            return ['Belum mulai', 'bg-slate-100 text-slate-600'];
        },
        koneksi(s) {
            if (s.is_focus_lost) return ['Keluar fokus', 'bg-amber-500'];
            if (s.is_online) return ['Aktif', 'bg-emerald-500'];
            return [s.last_activity_label || '-', 'bg-slate-300'];
        },
        pelanggaranTone(n) { return n >= 3 ? 'bg-rose-600 text-white' : (n > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600'); },
        sel(st) {
            let c = 'bg-white text-slate-500 ring-slate-200';
            if (st.is_visited && ! st.is_answered) c = 'bg-sky-50 text-sky-700 ring-sky-200';
            if (st.is_answered) c = 'bg-emerald-500 text-white ring-emerald-500';
            if (st.is_doubt) c = 'bg-amber-400 text-white ring-amber-400';
            return c + (st.is_current ? ' outline outline-2 outline-offset-2 outline-indigo-600' : '');
        },
        detail(s) { this.dipilih = s; this.$refs.detailDialog.showModal(); },
     }">

    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:px-5">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-50"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i>Kembali</a>
            <a href="{{ route('guru.lms.ujian.hasil', [$kelas->id, $mapel->id, $ujian->id]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-indigo-200 bg-indigo-50 px-4 text-xs font-bold text-indigo-700 no-underline hover:bg-indigo-100"><i class="fa-solid fa-chart-column" aria-hidden="true"></i>Lihat hasil</a>
        </div>
        <p class="flex items-center gap-2 text-xs text-slate-500"><i class="fa-solid fa-rotate" :class="memuat && 'fa-spin'" aria-hidden="true"></i>Muat ulang otomatis setiap 5 detik. <strong class="text-slate-700" x-text="terakhir">Memuat...</strong></p>
    </div>

    <section class="grid grid-cols-2 gap-2 sm:grid-cols-3 sm:gap-3 xl:grid-cols-6" aria-label="Ringkasan pengawasan">
        @foreach([
            ['total', 'Total peserta', 'text-indigo-700'],
            ['sedang_mengerjakan', 'Mengerjakan', 'text-amber-700'],
            ['aktif', 'Aktif', 'text-emerald-700'],
            ['keluar_fokus', 'Keluar fokus', 'text-rose-700'],
            ['selesai', 'Dikumpulkan', 'text-emerald-700'],
            ['total_pelanggaran', 'Total pelanggaran', 'text-rose-700'],
        ] as [$key, $label, $tone])
            <article class="rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm sm:p-4">
                <p class="text-2xl font-extrabold tabular-nums {{ $tone }}" x-text="stats.{{ $key }} ?? 0">0</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</p>
            </article>
        @endforeach
    </section>

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex items-center justify-between gap-3 border-b border-slate-200 px-4 py-3 sm:px-5">
            <h2 class="flex items-center gap-2 text-sm font-extrabold text-slate-900"><i class="fa-solid fa-desktop text-indigo-600" aria-hidden="true"></i>Aktivitas siswa di browser</h2>
            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">{{ $soalList->count() }} soal</span>
        </header>

        <div class="hidden grid-cols-[minmax(0,1.4fr)_8rem_minmax(0,1.2fr)_6rem_4rem_6rem_minmax(0,1fr)_6rem] gap-3 bg-slate-50 px-5 py-2 text-[11px] font-bold uppercase tracking-wide text-slate-500 xl:grid">
            <span>Nama siswa</span><span class="text-center">Status</span><span>Progres</span><span class="text-center">Soal aktif</span><span class="text-center">Ragu</span><span class="text-center">Pelanggaran</span><span>Aktivitas terakhir</span><span class="text-center">Detail</span>
        </div>

        <p x-show="! dimuat" class="px-5 py-10 text-center text-sm text-slate-500">Memuat data pengawasan...</p>
        <p x-cloak x-show="dimuat && ! siswa.length" class="px-5 py-10 text-center text-sm text-slate-500">Belum ada peserta ujian.</p>

        <div class="divide-y divide-slate-100">
            <template x-for="(s, i) in siswa" :key="i">
                <article class="grid min-w-0 grid-cols-2 gap-3 px-4 py-3 sm:px-5 xl:grid-cols-[minmax(0,1.4fr)_8rem_minmax(0,1.2fr)_6rem_4rem_6rem_minmax(0,1fr)_6rem] xl:items-center">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-extrabold text-slate-900" x-text="s.nama"></p>
                        <p class="mt-0.5 flex items-center gap-1.5 text-[11px] text-slate-500"><span class="h-2 w-2 shrink-0 rounded-full" :class="koneksi(s)[1]"></span><span class="truncate" x-text="koneksi(s)[0]"></span></p>
                    </div>
                    <span class="justify-self-end xl:justify-self-center"><span class="whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-extrabold" :class="status(s)[1]" x-text="status(s)[0]"></span></span>
                    <div class="col-span-2 min-w-0 xl:col-span-1">
                        <div class="flex justify-between text-[11px] text-slate-500"><span x-text="`${Number(s.answered_count || 0)}/${total} dijawab`"></span><strong class="text-slate-700" x-text="`${persen(s)}%`"></strong></div>
                        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-slate-200"><div class="h-full rounded-full bg-indigo-500 transition-all" x-effect="$el.style.width = persen(s) + '%'"></div></div>
                    </div>
                    <dl class="col-span-2 grid grid-cols-4 gap-2 text-center text-[11px] xl:contents">
                        <div class="rounded-lg bg-slate-50 p-1.5 xl:bg-transparent xl:p-0"><dt class="font-bold uppercase text-slate-500 xl:sr-only">Soal</dt><dd class="font-semibold text-slate-800" x-text="s.current_nomor_soal ? `No. ${s.current_nomor_soal}` : '-'"></dd></div>
                        <div class="rounded-lg bg-slate-50 p-1.5 xl:bg-transparent xl:p-0"><dt class="font-bold uppercase text-slate-500 xl:sr-only">Ragu</dt><dd><span class="rounded-full bg-amber-100 px-2 py-0.5 font-bold text-amber-800" x-text="s.doubt_count || 0"></span></dd></div>
                        <div class="rounded-lg bg-slate-50 p-1.5 xl:bg-transparent xl:p-0"><dt class="font-bold uppercase text-slate-500 xl:sr-only">Langgar</dt><dd><span class="rounded-full px-2 py-0.5 font-bold" :class="pelanggaranTone(s.focus_lost_count || 0)" :title="(s.focus_lost_count || 0) > 0 ? `Total keluar fokus ${durasi(s.focus_lost_total_seconds)}` : 'Belum ada pelanggaran'" x-text="s.focus_lost_count || 0"></span></dd></div>
                        <div class="rounded-lg bg-slate-50 p-1.5 text-left xl:bg-transparent xl:p-0"><dt class="font-bold uppercase text-slate-500 xl:sr-only">Terakhir</dt><dd class="truncate text-slate-700" x-text="s.last_activity_label || '-'"></dd><dd class="truncate text-slate-400" x-show="s.waktu_selesai" x-text="`Dikumpulkan: ${s.waktu_selesai}`"></dd></div>
                    </dl>
                    <button type="button" @click="detail(s)" class="col-span-2 inline-flex min-h-9 items-center justify-center gap-1.5 rounded-lg border border-indigo-200 bg-indigo-50 px-3 text-xs font-bold text-indigo-700 hover:bg-indigo-100 xl:col-span-1"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>Detail</button>
                </article>
            </template>
        </div>
    </section>

    <dialog x-ref="detailDialog" @click.self="$el.close()" class="w-[calc(100%-1rem)] max-w-4xl rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50">
        <template x-if="dipilih">
            <div>
                <header class="flex items-start justify-between gap-3 border-b border-slate-100 px-5 py-4">
                    <div class="min-w-0">
                        <h3 class="truncate text-base font-extrabold text-slate-900" x-text="dipilih.nama || 'Detail siswa'"></h3>
                        <p class="mt-0.5 text-xs text-slate-500" x-text="`${dipilih.status_label || '-'} · Pelanggaran ${dipilih.focus_lost_count || 0} kali · Total keluar fokus ${durasi(dipilih.focus_lost_total_seconds)}`"></p>
                    </div>
                    <button type="button" @click="$refs.detailDialog.close()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                </header>
                <div class="grid max-h-[70vh] gap-5 overflow-y-auto px-5 py-4 lg:grid-cols-[minmax(0,7fr)_minmax(0,5fr)]">
                    <section>
                        <h4 class="text-sm font-extrabold text-slate-900">Peta soal</h4>
                        <div class="mt-3 grid grid-cols-6 gap-2 sm:grid-cols-8">
                            <template x-for="st in (dipilih.statuses || [])" :key="st.nomor_soal">
                                <span class="flex h-9 items-center justify-center rounded-lg text-xs font-extrabold ring-1 ring-inset" :class="sel(st)" :title="`Soal ${st.nomor_soal}`" x-text="st.nomor_soal"></span>
                            </template>
                        </div>
                        <div class="mt-3 flex flex-wrap gap-3 text-[11px] text-slate-500">
                            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-emerald-500"></span>Dijawab</span>
                            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-amber-400"></span>Ragu</span>
                            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded bg-sky-50 ring-1 ring-sky-200"></span>Dikunjungi</span>
                            <span class="flex items-center gap-1.5"><span class="h-3 w-3 rounded outline outline-2 outline-indigo-600"></span>Sedang dibuka</span>
                        </div>
                    </section>
                    <section>
                        <h4 class="text-sm font-extrabold text-slate-900">Timeline pengawasan</h4>
                        <p x-show="! (dipilih.recent_logs || []).length" class="mt-3 text-xs text-slate-500">Belum ada aktivitas pengawasan yang tercatat.</p>
                        <ol class="mt-3 space-y-2">
                            <template x-for="(log, li) in (dipilih.recent_logs || [])" :key="li">
                                <li class="rounded-xl border border-slate-200 p-3">
                                    <div class="flex justify-between gap-2 text-xs"><strong class="text-slate-900" x-text="log.event_label || log.event_type"></strong><span class="shrink-0 text-slate-400" x-text="log.occurred_at || ''"></span></div>
                                    <p class="mt-1 text-xs text-slate-600" x-text="log.description || '-'"></p>
                                    <p class="mt-1 text-[11px] text-slate-400" x-show="log.event_type === 'focus_lost' && log.metadata && log.metadata.duration_seconds" x-text="`Durasi: ${durasi(log.metadata?.duration_seconds)}`"></p>
                                </li>
                            </template>
                        </ol>
                    </section>
                </div>
                <footer class="flex justify-end border-t border-slate-100 px-5 py-3"><button type="button" @click="$refs.detailDialog.close()" class="min-h-10 rounded-xl border border-slate-300 px-4 text-xs font-bold text-slate-700 hover:bg-slate-50">Tutup</button></footer>
            </div>
        </template>
    </dialog>
</div>
@endsection
