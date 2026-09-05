@extends('layouts.app')

@section('title', 'Persetujuan Dispensasi Naik Kelas')
@section('page-title', 'Persetujuan Dispensasi')
@section('page-subtitle', 'Tinjau permintaan izin naik kelas dengan tunggakan')

@section('content')
@php
    $availableIds = $requests->pluck('id')->map(fn ($id) => (string) $id)->values();
@endphp
<div class="min-w-0 w-full space-y-5"
    x-data="{
        selected: [],
        available: @js($availableIds),
        decision: { action: 'approve', url: '', name: '', bulk: false },
        toggleAll() { this.selected = this.selected.length === this.available.length ? [] : [...this.available]; },
        openOne(action, url, name) {
            this.decision = { action, url, name, bulk: false };
            this.$refs.decisionDialog.showModal();
        },
        openBulk(action) {
            if (!this.selected.length) return;
            this.decision = { action, url: @js(route('ketua.kenaikan-kelas.approval.bulk-update')), name: `${this.selected.length} pengajuan terpilih`, bulk: true };
            this.$refs.decisionDialog.showModal();
        }
    }">
    <section class="grid grid-cols-2 gap-3 lg:grid-cols-3">
        @foreach([
            ['Menunggu keputusan', $requests->count(), 'Antrean tahun ajaran aktif', 'fa-hourglass-half', 'bg-amber-50 text-amber-700'],
            ['Pengaju terlibat', $requests->pluck('diajukan_oleh')->unique()->count(), 'Sumber pengajuan saat ini', 'fa-user-group', 'bg-brand-50 text-brand-700'],
            ['Siswa ditinjau', $requests->pluck('siswa_id')->unique()->count(), 'Tidak menghapus tunggakan', 'fa-user-graduate', 'bg-emerald-50 text-emerald-700'],
        ] as [$label, $value, $description, $icon, $tone])
            <article class="min-w-0 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm {{ $loop->last ? 'col-span-2 lg:col-span-1' : '' }}">
                <div class="flex items-start justify-between gap-3"><div class="min-w-0"><strong class="block text-xl font-extrabold text-slate-950">{{ $value }}</strong><span class="mt-1 block truncate text-[10px] font-bold uppercase tracking-wide text-slate-500">{{ $label }}</span></div><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $tone }}"><i class="fas {{ $icon }}" aria-hidden="true"></i></span></div>
                <p class="mt-3 truncate border-t border-slate-100 pt-3 text-[11px] text-slate-500">{{ $description }}</p>
            </article>
        @endforeach
    </section>

    <section class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="flex flex-col gap-4 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between sm:p-5">
            <div><h2 class="flex items-center gap-2 text-base font-extrabold text-slate-950"><i class="fas fa-check-double text-brand-600" aria-hidden="true"></i>Permintaan izin khusus</h2><p class="mt-1 text-xs leading-5 text-slate-500">Setujui hanya setelah alasan dan identitas siswa ditinjau.</p></div>
            <a href="{{ route('ketua.kenaikan-kelas.approval.history') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-xs font-bold text-slate-700 no-underline hover:bg-slate-200"><i class="fas fa-clock-rotate-left" aria-hidden="true"></i>Riwayat keputusan</a>
        </header>

        @if($requests->isNotEmpty())
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-slate-50 px-4 py-3 sm:px-5">
                <label class="inline-flex cursor-pointer items-center gap-2 text-xs font-bold text-slate-700"><input type="checkbox" @change="toggleAll()" :checked="available.length > 0 && selected.length === available.length" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">Pilih semua</label>
                <div class="flex flex-wrap gap-2"><button type="button" @click="openBulk('approve')" :disabled="!selected.length" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-emerald-600 px-3 text-xs font-bold text-white hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300"><i class="fas fa-check" aria-hidden="true"></i>Setujui <span x-show="selected.length">(<span x-text="selected.length"></span>)</span></button><button type="button" @click="openBulk('reject')" :disabled="!selected.length" class="inline-flex min-h-9 items-center gap-2 rounded-xl bg-red-600 px-3 text-xs font-bold text-white hover:bg-red-700 disabled:cursor-not-allowed disabled:bg-slate-300"><i class="fas fa-xmark" aria-hidden="true"></i>Tolak <span x-show="selected.length">(<span x-text="selected.length"></span>)</span></button></div>
            </div>
        @endif

        <div class="hidden overflow-x-auto lg:block">
            <table class="w-full min-w-[58rem] table-fixed text-left text-xs">
                <colgroup><col class="w-12"><col><col class="w-44"><col class="w-44"><col class="w-60"><col class="w-32"><col class="w-36"></colgroup>
                <thead class="bg-slate-50 text-[10px] font-bold uppercase tracking-wide text-slate-500"><tr><th class="px-4 py-3"></th><th class="px-3 py-3">Siswa</th><th class="px-3 py-3">Kelas</th><th class="px-3 py-3">Diajukan oleh</th><th class="px-3 py-3">Alasan</th><th class="px-3 py-3">Tanggal</th><th class="py-3 pl-5 pr-4 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($requests as $req)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-4"><input type="checkbox" value="{{ $req->id }}" x-model="selected" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500"></td>
                            <td class="px-3 py-4"><p class="truncate text-sm font-bold text-slate-900" title="{{ $req->nama_siswa }}">{{ $req->nama_siswa }}</p></td>
                            <td class="px-3 py-4"><p class="truncate font-semibold text-slate-700" title="{{ $req->nama_kelas }}">{{ $req->nama_kelas }}</p></td>
                            <td class="px-3 py-4"><p class="truncate text-slate-600" title="{{ $req->pengaju }}">{{ $req->pengaju }}</p></td>
                            <td class="px-3 py-4"><p class="line-clamp-2 whitespace-normal leading-5 text-slate-600" title="{{ $req->alasan_pengajuan }}">{{ $req->alasan_pengajuan ?: 'Tidak ada alasan' }}</p></td>
                            <td class="whitespace-nowrap px-3 py-4 text-slate-600">{{ \Carbon\Carbon::parse($req->tanggal_pengajuan)->locale('id')->translatedFormat('d M Y') }}</td>
                            <td class="py-4 pl-5 pr-4"><div class="flex justify-end gap-2"><x-cleanflow.table-action type="button" tone="success" icon="fas fa-check" label="Setujui dispensasi" data-action-url="{{ route('ketua.kenaikan-kelas.approval.update', $req->id) }}" data-student-name="{{ $req->nama_siswa }}" x-on:click="openOne('approve', $el.dataset.actionUrl, $el.dataset.studentName)" /><x-cleanflow.table-action type="button" tone="delete" icon="fas fa-xmark" label="Tolak dispensasi" data-action-url="{{ route('ketua.kenaikan-kelas.approval.update', $req->id) }}" data-student-name="{{ $req->nama_siswa }}" x-on:click="openOne('reject', $el.dataset.actionUrl, $el.dataset.studentName)" /></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-4 py-14 text-center text-sm text-slate-500"><i class="fas fa-circle-check mb-3 block text-4xl text-emerald-300" aria-hidden="true"></i>Tidak ada permintaan yang menunggu keputusan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="divide-y divide-slate-100 lg:hidden">
            @forelse($requests as $req)
                <article class="p-4"><div class="flex min-w-0 items-start gap-3"><input type="checkbox" value="{{ $req->id }}" x-model="selected" class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-brand-600 focus:ring-brand-500"><span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-xs font-extrabold text-brand-700">{{ strtoupper(substr($req->nama_siswa, 0, 1)) }}</span><div class="min-w-0 flex-1"><h3 class="break-words text-sm font-extrabold text-slate-900">{{ $req->nama_siswa }}</h3><p class="mt-0.5 text-[11px] text-slate-500">{{ $req->nama_kelas }} &middot; {{ \Carbon\Carbon::parse($req->tanggal_pengajuan)->locale('id')->translatedFormat('d M Y') }}</p></div></div><div class="mt-3 rounded-xl bg-slate-50 p-3"><p class="text-[9px] font-bold uppercase tracking-wide text-slate-400">Alasan dari {{ $req->pengaju }}</p><p class="mt-1 break-words text-xs leading-5 text-slate-600">{{ $req->alasan_pengajuan ?: 'Tidak ada alasan' }}</p></div><div class="mt-3 grid grid-cols-2 gap-2"><button type="button" @click="openOne('approve', @js(route('ketua.kenaikan-kelas.approval.update', $req->id)), @js($req->nama_siswa))" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-3 text-xs font-bold text-white"><i class="fas fa-check" aria-hidden="true"></i>Setujui</button><button type="button" @click="openOne('reject', @js(route('ketua.kenaikan-kelas.approval.update', $req->id)), @js($req->nama_siswa))" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-red-600 px-3 text-xs font-bold text-white"><i class="fas fa-xmark" aria-hidden="true"></i>Tolak</button></div></article>
            @empty
                <div class="p-12 text-center text-sm text-slate-500"><i class="fas fa-circle-check mb-3 block text-4xl text-emerald-300" aria-hidden="true"></i>Tidak ada permintaan yang menunggu keputusan.</div>
            @endforelse
        </div>
    </section>

    <dialog x-ref="decisionDialog" class="m-auto w-[calc(100%-2rem)] max-w-lg overflow-hidden rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-slate-950/60" @click.self="$el.close()">
        <form :action="decision.url" method="POST">
            @csrf
            @method('PUT')
            <input type="hidden" name="action" :value="decision.action">
            <template x-if="decision.bulk"><template x-for="id in selected" :key="id"><input type="hidden" name="ids[]" :value="id"></template></template>
            <header class="flex items-start justify-between gap-4 border-b border-slate-200 p-4 sm:p-5"><div><p class="text-[10px] font-bold uppercase tracking-wide" :class="decision.action === 'approve' ? 'text-emerald-700' : 'text-red-700'" x-text="decision.action === 'approve' ? 'Persetujuan' : 'Penolakan'"></p><h2 class="mt-1 text-base font-extrabold text-slate-950" x-text="decision.name"></h2></div><button type="button" @click="$refs.decisionDialog.close()" class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200" aria-label="Tutup"><i class="fas fa-xmark" aria-hidden="true"></i></button></header>
            <div class="space-y-4 p-4 sm:p-5"><p class="rounded-xl border p-3 text-xs leading-5" :class="decision.action === 'approve' ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-red-200 bg-red-50 text-red-800'" x-text="decision.action === 'approve' ? 'Siswa diizinkan naik kelas, tetapi tunggakan tetap tercatat.' : 'Siswa harus menyelesaikan tunggakan sebelum dapat naik kelas.'"></p><label class="block"><span class="text-xs font-bold text-slate-700">Catatan <span class="font-normal text-slate-400">(opsional)</span></span><textarea name="catatan" rows="3" maxlength="1000" class="mt-2 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm text-slate-900 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100" placeholder="Tambahkan alasan atau arahan tindak lanjut..."></textarea></label></div>
            <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5"><button type="button" @click="$refs.decisionDialog.close()" class="h-10 px-4 text-xs font-bold text-slate-600">Batal</button><button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl px-4 text-xs font-bold text-white" :class="decision.action === 'approve' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700'"><i class="fas" :class="decision.action === 'approve' ? 'fa-check' : 'fa-xmark'" aria-hidden="true"></i><span x-text="decision.action === 'approve' ? 'Ya, setujui' : 'Ya, tolak'"></span></button></footer>
        </form>
    </dialog>
</div>
@endsection
