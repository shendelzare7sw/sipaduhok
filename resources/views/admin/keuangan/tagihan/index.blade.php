@extends('layouts.app')

@section('title', 'Kelola Tagihan')
@section('page-title', 'Kelola Tagihan')
@section('page-subtitle', 'Pantau kewajiban siswa dan pilih proses tagihan yang sesuai')

@section('content')
@php
    $isAdminContext = request()->routeIs('admin.*');
    $tagihanRoute = $isAdminContext ? 'admin.keuangan.tagihan' : 'bendahara.tagihan';
    $pembayaranRoute = $isAdminContext ? 'admin.keuangan.pembayaran' : 'bendahara.pembayaran';
    $students = $siswaList ?? collect();
    $pageIds = collect($students->items())->pluck('id')->map(fn ($id) => (string) $id)->values();
    $studentNames = collect($students->items())->mapWithKeys(fn ($student) => [(string) $student->id => $student->nama_lengkap ?? 'Siswa'])->all();
    $filters = $filters ?? [];
@endphp

<div
    data-tagihan-index
    class="min-w-0 w-full space-y-4"
    x-data="{
        selected: [],
        pageIds: @js($pageIds),
        names: @js($studentNames),
        actionsOpen: false,
        search: @js((string) ($filters['search'] ?? '')),
        get allSelected() { return this.pageIds.length > 0 && this.pageIds.every(id => this.selected.includes(id)); },
        toggleAll() { this.selected = this.allSelected ? [] : [...this.pageIds]; },
        @if($isAdminContext)
        async resetSelected() {
            if (!this.selected.length) return;
            const preview = this.selected.slice(0, 4).map(id => this.names[id]).join(', ');
            const more = this.selected.length > 4 ? `, dan ${this.selected.length - 4} lainnya` : '';
            const result = await Swal.fire({
                icon: 'warning',
                title: 'Reset tagihan terpilih?',
                html: `<strong>${this.selected.length} siswa</strong> akan dikembalikan ke kondisi kosong.<br><span class='text-sm'>${preview}${more}</span><br><br>Semua tagihan dan pembayaran pada tahun ajaran ini akan dihapus permanen.`,
                showCancelButton: true,
                confirmButtonText: 'Ya, reset tagihan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#dc2626',
                reverseButtons: true,
                focusCancel: true,
            });
            if (!result.isConfirmed) return;
            this.$refs.resetIds.value = JSON.stringify(this.selected);
            this.$nextTick(() => this.$refs.resetForm.submit());
        }
        @endif
    }"
    @keydown.escape.window="actionsOpen = false"
>
    @if(!empty($tunggakanSummary))
        <section class="rounded-2xl border border-red-200 bg-red-50 p-4 shadow-sm sm:p-5">
            <div class="flex items-start gap-3">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-700"><i class="fas fa-triangle-exclamation" aria-hidden="true"></i></span>
                <div class="min-w-0 flex-1">
                    <h2 class="font-extrabold text-red-900">Tunggakan tahun sebelumnya</h2>
                    <p class="mt-1 text-sm leading-6 text-red-800">
                        <strong>{{ $tunggakanSummary['jumlah_siswa'] }} siswa</strong> memiliki tunggakan senilai
                        <strong>Rp {{ number_format($tunggakanSummary['total_tunggakan'], 0, ',', '.') }}</strong> dari periode sebelumnya.
                    </p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @foreach($tunggakanSummary['per_tahun'] as $item)
                            <a href="{{ route($tagihanRoute.'.index', ['tahun_ajaran_id' => $item['tahun_ajaran_id']]) }}" class="inline-flex min-h-9 items-center gap-2 rounded-xl border border-red-200 bg-white px-3 text-xs font-bold text-red-700 no-underline hover:bg-red-100">
                                <i class="fas fa-eye" aria-hidden="true"></i>
                                {{ $item['nama_tahun'] }} · {{ $item['jumlah_siswa'] }} siswa · Rp {{ number_format($item['total'], 0, ',', '.') }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    <section class="rounded-2xl border {{ ($isAlumniMode ?? false) ? 'border-amber-200 bg-amber-50' : 'border-blue-200 bg-blue-50' }} p-4">
        <div class="flex items-start gap-3">
            <i class="fas {{ ($isAlumniMode ?? false) ? 'fa-user-graduate text-amber-700' : 'fa-circle-info text-blue-700' }} mt-1" aria-hidden="true"></i>
            <p class="text-sm leading-6 {{ ($isAlumniMode ?? false) ? 'text-amber-900' : 'text-blue-900' }}">
                @if($isAlumniMode ?? false)
                    <strong>Mode Alumni Menunggak:</strong> sisa tagihan dihitung lintas seluruh tahun ajaran untuk membantu proses pelunasan akhir.
                @else
                    Total mencakup seluruh kewajiban siswa pada periode terpilih. Gunakan <strong>Tagihan massal</strong> bila nominal seragam untuk banyak siswa.
                @endif
            </p>
        </div>
    </section>

    <section class="overflow-visible rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5">
            <div class="flex flex-col gap-4 xl:flex-row xl:items-start xl:justify-between">
                <div class="min-w-0">
                    <h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-950"><i class="fas fa-list text-brand-600" aria-hidden="true"></i>Daftar tagihan siswa</h2>
                    <p class="mt-1 text-sm text-slate-500">{{ $students->total() }} siswa ditemukan.</p>
                </div>

                <div class="relative flex w-full flex-wrap gap-2 sm:w-auto">
                    @if($isAdminContext)
                        <a href="{{ route($tagihanRoute.'.import') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-cyan-50 px-3 text-xs font-bold text-cyan-700 no-underline hover:bg-cyan-100 sm:text-sm"><i class="fas fa-file-import" aria-hidden="true"></i>Import</a>
                    @endif
                    <a href="{{ route($tagihanRoute.'.cetak-laporan', request()->query()) }}" target="_blank" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-3 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200 sm:text-sm"><i class="fas fa-print" aria-hidden="true"></i>Cetak</a>
                    <a href="{{ route($tagihanRoute.'.duplicate') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-violet-50 px-3 text-xs font-bold text-violet-700 no-underline hover:bg-violet-100 sm:text-sm"><i class="fas fa-copy" aria-hidden="true"></i>Duplikasi</a>
                    <div class="static sm:relative" @click.outside="actionsOpen = false">
                        <button type="button" @click="actionsOpen = !actionsOpen" :aria-expanded="actionsOpen" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-3 text-xs font-bold text-white hover:bg-brand-700 sm:text-sm">
                            <i class="fas fa-plus" aria-hidden="true"></i>Buat tagihan<i class="fas fa-chevron-down text-[10px]" aria-hidden="true"></i>
                        </button>
                        <div x-cloak x-show="actionsOpen" x-transition.origin.top.left class="absolute inset-x-0 top-full z-30 mt-2 w-auto overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-xl sm:inset-x-auto sm:right-0 sm:w-64">
                            <a href="{{ route($tagihanRoute.'.bulk-create') }}" class="flex items-start gap-3 rounded-lg px-3 py-2.5 text-sm no-underline hover:bg-slate-50"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700"><i class="fas fa-users"></i></span><span><strong class="block text-slate-900">Tagihan massal</strong><small class="text-slate-500">Satu tagihan untuk banyak siswa</small></span></a>
                            <a href="{{ route($tagihanRoute.'.create-custom') }}" class="flex items-start gap-3 rounded-lg px-3 py-2.5 text-sm no-underline hover:bg-slate-50"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-700"><i class="fas fa-user-plus"></i></span><span><strong class="block text-slate-900">Tagihan khusus</strong><small class="text-slate-500">Buat untuk siswa tertentu</small></span></a>
                            <a href="{{ route($tagihanRoute.'.generate-spp') }}" class="flex items-start gap-3 rounded-lg px-3 py-2.5 text-sm no-underline hover:bg-slate-50"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-700"><i class="fas fa-calendar-days"></i></span><span><strong class="block text-slate-900">Generate SPP</strong><small class="text-slate-500">Buat kewajiban bulanan</small></span></a>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route($tagihanRoute.'.index') }}" method="GET" class="mt-4 grid gap-3 md:grid-cols-2 xl:grid-cols-[minmax(15rem,1.5fr)_repeat(3,minmax(10rem,.7fr))_auto]">
                @if($isAlumniMode ?? false)<input type="hidden" name="tunggakan_alumni" value="1">@endif
                <label class="relative block min-w-0">
                    <span class="sr-only">Cari siswa</span>
                    <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400" aria-hidden="true"></i>
                    <input type="search" name="search" x-model="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama atau NISN..." class="h-11 w-full rounded-xl border border-slate-300 bg-white !pl-10 pr-9 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    <button x-cloak x-show="search" type="button" @click="search = ''; $nextTick(() => $el.previousElementSibling.focus())" class="absolute right-2 top-1/2 flex h-7 w-7 -translate-y-1/2 items-center justify-center rounded-lg bg-slate-100 text-xs text-slate-500" aria-label="Hapus pencarian"><i class="fas fa-times"></i></button>
                </label>
                <select name="tahun_ajaran_id" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    @forelse($allTahunAjaran ?? [] as $ta)
                        <option value="{{ $ta->id }}" @selected(optional($selectedYear)->id == $ta->id)>{{ $ta->nama_tahun_ajaran }}{{ $ta->is_active ? ' · Aktif' : '' }}</option>
                    @empty
                        <option value="">Tidak ada tahun ajaran</option>
                    @endforelse
                </select>
                <select name="kelas_id" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    <option value="">Semua kelas</option>
                    @foreach($kelasList ?? [] as $kelas)
                        <option value="{{ $kelas->id }}" @selected(($filters['kelas_id'] ?? '') == $kelas->id)>{{ $kelas->nama_kelas }} · {{ $kelas->jenjang ?? '-' }} · {{ optional($kelas->cabang)->nama_cabang ?? 'Cabang belum diatur' }}</option>
                    @endforeach
                </select>
                <select name="status_tagihan" class="h-11 min-w-0 rounded-xl border border-slate-300 bg-white px-3 text-sm text-slate-800 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    <option value="">Semua status</option>
                    <option value="belum_lunas" @selected(($filters['status_tagihan'] ?? '') === 'belum_lunas')>Belum lunas</option>
                    <option value="lunas" @selected(($filters['status_tagihan'] ?? '') === 'lunas')>Lunas</option>
                    <option value="kosong" @selected(($filters['status_tagihan'] ?? '') === 'kosong')>Belum ada tagihan</option>
                </select>
                <div class="flex gap-2 md:col-span-2 xl:col-span-1">
                    <button type="submit" class="inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-4 text-sm font-bold text-white hover:bg-slate-800 xl:flex-none"><i class="fas fa-filter"></i>Terapkan</button>
                    <a href="{{ route($tagihanRoute.'.index', ($isAlumniMode ?? false) ? ['tunggakan_alumni' => 1] : []) }}" class="inline-flex h-11 items-center justify-center rounded-xl border border-slate-300 px-4 text-sm font-bold text-slate-600 no-underline hover:bg-slate-50" aria-label="Reset filter"><i class="fas fa-rotate-left"></i></a>
                </div>
            </form>

            <div class="mt-2">
                @if($isAlumniMode ?? false)
                    <a href="{{ route($tagihanRoute.'.index') }}" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-amber-100 px-3 text-xs font-bold text-amber-800 no-underline"><i class="fas fa-user-graduate"></i>Alumni Menunggak aktif <i class="fas fa-times"></i></a>
                @else
                    <a href="{{ route($tagihanRoute.'.index', ['tunggakan_alumni' => 1]) }}" class="inline-flex min-h-9 items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 text-xs font-bold text-amber-800 no-underline hover:bg-amber-100"><i class="fas fa-user-graduate"></i>Lihat alumni menunggak</a>
                @endif
            </div>
        </header>

        @if($students->isEmpty())
            <div class="px-5 py-16 text-center"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-400"><i class="fas fa-folder-open"></i></span><h3 class="mt-4 font-extrabold text-slate-900">Data siswa tidak ditemukan</h3><p class="mt-1 text-sm text-slate-500">Ubah filter atau pilih tahun ajaran lainnya.</p></div>
        @else
            @if($isAdminContext)
                <div class="flex items-center gap-3 border-b border-slate-100 bg-slate-50 px-4 py-3 xl:hidden">
                    <input type="checkbox" :checked="allSelected" @change="toggleAll" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <button type="button" @click="toggleAll" class="text-xs font-bold text-slate-700">Pilih semua siswa di halaman ini</button>
                </div>
            @endif

            <div class="divide-y divide-slate-100 xl:hidden">
                @foreach($students as $index => $siswa)
                    @php
                        $isPaid = $siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0;
                        $isEmpty = $siswa->total_tagihan == 0;
                    @endphp
                    <article class="p-4" :class="selected.includes('{{ $siswa->id }}') && 'bg-blue-50/60'">
                        <div class="flex items-start gap-3">
                            @if($isAdminContext)
                                <input type="checkbox" value="{{ $siswa->id }}" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            @endif
                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0"><h3 class="truncate text-sm font-extrabold text-slate-950">{{ $siswa->nama_lengkap }}</h3><p class="mt-0.5 truncate text-xs text-slate-500">NISN {{ $siswa->nisn ?: '-' }} · {{ ($siswa->status ?? '') === 'lulus' ? 'Alumni' : 'Siswa aktif' }}</p></div>
                                    <span class="inline-flex shrink-0 rounded-full px-2 py-1 text-[10px] font-extrabold {{ $isPaid ? 'bg-emerald-50 text-emerald-700' : ($isEmpty ? 'bg-slate-100 text-slate-600' : 'bg-red-50 text-red-700') }}">{{ $isPaid ? 'Lunas' : ($isEmpty ? 'Kosong' : 'Belum lunas') }}</span>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2 rounded-xl bg-slate-50 p-3 text-xs">
                                    <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Kelas</span><strong class="text-slate-800">{{ optional($siswa->kelas)->nama_kelas ?? '-' }}</strong></div>
                                    <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Cabang</span><strong class="text-slate-800">{{ optional($siswa->cabang)->kode_cabang ?? '-' }}</strong></div>
                                    <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Total</span><strong class="tabular-nums text-slate-800">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</strong></div>
                                    <div><span class="block text-[10px] font-bold uppercase tracking-wide text-slate-400">Sisa</span><strong class="tabular-nums {{ $siswa->sisa_tagihan > 0 ? 'text-red-700' : 'text-emerald-700' }}">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</strong></div>
                                </div>
                                <div class="mt-3 flex justify-end gap-1.5">
                                    <x-cleanflow.table-action :href="route($tagihanRoute.'.show', $siswa->id)" tone="view" icon="fas fa-eye" label="Lihat detail tagihan" />
                                    <x-cleanflow.table-action :href="route($tagihanRoute.'.edit', $siswa->id)" tone="edit" icon="fas fa-pen" label="Edit tagihan" />
                                    <x-cleanflow.table-action :href="route($pembayaranRoute.'.riwayat-siswa', $siswa->id)" tone="success" icon="fas fa-clock-rotate-left" label="Riwayat pembayaran" />
                                    <x-cleanflow.table-action :href="route($tagihanRoute.'.cetak', $siswa->id)" tone="neutral" icon="fas fa-print" label="Cetak tagihan" target="_blank" />
                                </div>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="hidden overflow-x-auto xl:block">
                <table class="w-full table-fixed text-left text-sm">
                    <colgroup>@if($isAdminContext)<col class="w-12">@endif<col class="w-12"><col><col class="w-36"><col class="w-24"><col class="w-20"><col class="w-32"><col class="w-32"><col class="w-32"><col class="w-24"><col class="w-52"></colgroup>
                    <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr>@if($isAdminContext)<th class="px-3 py-3 text-center"><input type="checkbox" :checked="allSelected" @change="toggleAll" class="h-4 w-4 rounded border-slate-300 text-brand-600"></th>@endif<th class="px-2 py-3">No</th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">NISN</th><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Cabang</th><th class="px-3 py-3 text-right">Total</th><th class="px-3 py-3 text-right">Terbayar</th><th class="px-3 py-3 text-right">Sisa</th><th class="px-3 py-3 text-center">Status</th><th class="py-3 pl-6 pr-3 text-right">Aksi</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($students as $index => $siswa)
                            @php
                                $isPaid = $siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0;
                                $isEmpty = $siswa->total_tagihan == 0;
                            @endphp
                            <tr class="hover:bg-slate-50" :class="selected.includes('{{ $siswa->id }}') && 'bg-blue-50/70'">
                                @if($isAdminContext)<td class="px-3 py-4 text-center"><input type="checkbox" value="{{ $siswa->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600"></td>@endif
                                <td class="px-2 py-4 text-xs tabular-nums text-slate-400">{{ $students->firstItem() + $index }}</td>
                                <td class="px-3 py-4"><strong class="block truncate text-slate-950" title="{{ $siswa->nama_lengkap }}">{{ $siswa->nama_lengkap }}</strong><span class="block text-xs text-slate-500">{{ ($siswa->status ?? '') === 'lulus' ? 'Alumni' : 'Siswa aktif' }}</span></td>
                                <td class="truncate px-3 py-4 font-semibold text-slate-700" title="{{ $siswa->nisn }}">{{ $siswa->nisn ?: '-' }}</td>
                                <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-lg bg-blue-50 px-2 py-1 text-[10px] font-bold text-blue-700">{{ optional($siswa->kelas)->nama_kelas ?? '-' }}</span></td>
                                <td class="px-3 py-4"><span class="inline-flex whitespace-nowrap rounded-lg bg-slate-100 px-2 py-1 text-[10px] font-bold text-slate-700">{{ optional($siswa->cabang)->kode_cabang ?? '-' }}</span></td>
                                <td class="whitespace-nowrap px-3 py-4 text-right text-xs font-bold tabular-nums text-slate-800">Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-right text-xs font-bold tabular-nums text-emerald-700">Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-right text-xs font-extrabold tabular-nums {{ $siswa->sisa_tagihan > 0 ? 'text-red-700' : 'text-emerald-700' }}">Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}</td>
                                <td class="px-3 py-4 text-center"><span class="inline-flex whitespace-nowrap rounded-full px-2 py-1 text-[10px] font-extrabold {{ $isPaid ? 'bg-emerald-50 text-emerald-700' : ($isEmpty ? 'bg-slate-100 text-slate-600' : 'bg-red-50 text-red-700') }}">{{ $isPaid ? 'Lunas' : ($isEmpty ? 'Kosong' : 'Belum lunas') }}</span></td>
                                <td class="py-4 pl-6 pr-3"><div class="flex justify-end gap-1.5"><x-cleanflow.table-action :href="route($tagihanRoute.'.show', $siswa->id)" tone="view" icon="fas fa-eye" label="Lihat detail tagihan" /><x-cleanflow.table-action :href="route($tagihanRoute.'.edit', $siswa->id)" tone="edit" icon="fas fa-pen" label="Edit tagihan" /><x-cleanflow.table-action :href="route($pembayaranRoute.'.riwayat-siswa', $siswa->id)" tone="success" icon="fas fa-clock-rotate-left" label="Riwayat pembayaran" /><x-cleanflow.table-action :href="route($tagihanRoute.'.cetak', $siswa->id)" tone="neutral" icon="fas fa-print" label="Cetak tagihan" target="_blank" /></div></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <footer class="border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">{{ $students->withQueryString()->links() }}</footer>
        @endif
    </section>

    @if($isAdminContext)
        <template x-teleport="body">
            <div x-cloak x-show="selected.length" x-transition class="fixed bottom-14 left-1/2 z-[1040] flex w-[calc(100%-2rem)] max-w-xl -translate-x-1/2 items-center gap-3 rounded-2xl border border-slate-700 bg-slate-900 px-3 py-3 text-white shadow-2xl min-[769px]:bottom-16 sm:w-auto sm:px-4">
                <span class="min-w-0 flex-1 whitespace-nowrap text-xs font-bold sm:text-sm"><i class="fas fa-square-check mr-1 text-blue-300"></i><span x-text="selected.length"></span> siswa dipilih</span>
                <button type="button" @click="selected = []" class="min-h-9 rounded-xl bg-white/10 px-3 text-xs font-bold hover:bg-white/20">Batal</button>
                <button type="button" @click="resetSelected" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-red-600 px-3 text-xs font-bold hover:bg-red-500"><i class="fas fa-trash-arrow-up"></i>Reset</button>
            </div>
        </template>

        <form x-ref="resetForm" action="{{ route($tagihanRoute.'.reset-tagihan') }}" method="POST" class="hidden">
            @csrf
            <input x-ref="resetIds" type="hidden" name="siswa_ids">
            <input type="hidden" name="tahun_ajaran_id" value="{{ optional($selectedYear)->id }}">
        </form>
    @endif
</div>
@endsection
