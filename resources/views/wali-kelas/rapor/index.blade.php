@extends('layouts.app')

@section('title', 'Kelola Rapor')
@section('page-title', 'Kelola Rapor')
@section('page-subtitle', isset($kelas) && $kelas ? 'Rapor kelas '.$kelas->nama_kelas : 'Kelola rapor siswa')

@section('content')
<div class="min-w-0 w-full space-y-4"
    x-data="{ confirmAction: '', confirmTitle: '', confirmName: '', confirmNote: '', confirmLabel: '', confirmDanger: false, confirmDelete: false, scheduledAction: '', scheduledName: '', scheduledDate: '', uploadStudent: '', uploadName: '' }">
    @if($error ?? false)
        <p class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800" role="alert">{{ $error }}</p>
    @else
        @php
            $visibleRaporList = ($selectedSiswaId ?? null)
                ? $raporList->filter(fn ($item) => $item['siswa']->id === $selectedSiswaId)
                : $raporList;
            $reportType = $jenisRapor === 'tengah_semester' ? 'Tengah Semester (PTS)' : 'Akhir Semester (PAS)';
        @endphp
        <header class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-3"><div><p class="text-xs font-bold uppercase tracking-wide text-sky-700">Kelas {{ $kelas->nama_kelas }} · {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '—' }}</p><h1 class="mt-1 text-lg font-extrabold text-slate-900">Kelola rapor siswa</h1><p class="mt-1 text-xs text-slate-500">{{ $reportType }} · Semester {{ ucfirst($semester) }}</p></div><a href="{{ route('wali.nilai.index', ['semester' => $semester]) }}" class="inline-flex min-h-10 items-center gap-2 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700"><i class="fas fa-chart-line" aria-hidden="true"></i>Lihat nilai</a></div>
            <div class="mt-4 flex flex-wrap gap-2 border-t border-slate-100 pt-4">
                <button type="button" data-action="{{ route('wali.rapor.generate-all') }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Generate semua rapor?'; confirmName = '{{ $kelas->nama_kelas }}'; confirmNote = 'Siswa yang sudah memiliki rapor akan dilewati.'; confirmLabel = 'Ya, generate'; confirmDanger = false; confirmDelete = false; $refs.confirmDialog.showModal()" class="inline-flex min-h-10 items-center rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Generate semua</button>
                <button type="button" @if(($statusCount['belum_dibuat'] ?? 0) > 0) disabled title="Generate {{ $statusCount['belum_dibuat'] }} rapor yang belum ada lebih dahulu." @else data-action="{{ route('wali.rapor.kirim-validasi-semua') }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Kirim semua rapor ke Ketua?'; confirmName = '{{ $kelas->nama_kelas }}'; confirmNote = 'Pastikan semua draft sudah lengkap. Rapor yang telah dikirim tidak dikirim ulang.'; confirmLabel = 'Ya, kirim semua'; confirmDanger = false; confirmDelete = false; $refs.confirmDialog.showModal()" @endif class="inline-flex min-h-10 items-center rounded-lg bg-emerald-600 px-3 text-xs font-bold text-white disabled:cursor-not-allowed disabled:bg-slate-300">Kirim semua ke Ketua</button>
                <button type="button" @click="$refs.templateDialog.showModal()" class="inline-flex min-h-10 items-center rounded-lg border border-sky-200 bg-sky-50 px-3 text-xs font-bold text-sky-800">Terapkan template</button>
                <a href="{{ route('wali.template-capaian.index') }}" class="inline-flex min-h-10 items-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Kelola template</a>
            </div>
            @if(($statusCount['belum_dibuat'] ?? 0) > 0)<p class="mt-2 text-[11px] text-amber-800">{{ $statusCount['belum_dibuat'] }} rapor belum dibuat; kirim semua baru tersedia setelahnya.</p>@endif
        </header>

        <form action="{{ route('wali.rapor.index') }}" method="GET" class="grid gap-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-[170px_220px_minmax(170px,1fr)_auto] lg:items-end">
            <label class="text-xs font-bold text-slate-700">Semester<select name="semester" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="ganjil" @selected($semester === 'ganjil')>Ganjil</option><option value="genap" @selected($semester === 'genap')>Genap</option></select></label>
            <label class="text-xs font-bold text-slate-700">Jenis rapor<select name="jenis_rapor" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="akhir_semester" @selected($jenisRapor === 'akhir_semester')>Akhir semester (PAS)</option><option value="tengah_semester" @selected($jenisRapor === 'tengah_semester')>Tengah semester (PTS)</option></select></label>
            <label class="text-xs font-bold text-slate-700">Siswa<select name="siswa_id" class="mt-1 h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm font-normal text-slate-800"><option value="">Semua siswa</option>@foreach($raporList as $item)<option value="{{ $item['siswa']->id }}" @selected(($selectedSiswaId ?? null) === $item['siswa']->id)>{{ $item['siswa']->nama_lengkap }}</option>@endforeach</select></label>
            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-slate-800 px-4 text-xs font-bold text-white">Terapkan</button>
        </form>
        <section class="grid grid-cols-3 gap-2 sm:gap-3" aria-label="Ringkasan rapor">@foreach(['Draft' => $statusCount['draft'] ?? 0, 'Diterbitkan' => $statusCount['diterbitkan'] ?? 0, 'Belum dibuat' => $statusCount['belum_dibuat'] ?? 0] as $label => $value)<div class="rounded-xl border border-slate-200 bg-white p-3 shadow-sm"><p class="text-[10px] font-bold uppercase tracking-wide text-slate-500 sm:text-xs">{{ $label }}</p><p class="mt-1 text-xl font-extrabold text-slate-900">{{ $value }}</p></div>@endforeach</section>
        <p class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-xs leading-5 text-sky-900">Alur: buat draft → kirim ke Ketua → persetujuan Ketua → pemeriksaan keuangan → jadwalkan/terbitkan → akses wali siswa.</p>

        <section class="rounded-xl border border-slate-200 bg-white shadow-sm" aria-labelledby="rapor-heading">
            <div class="border-b border-slate-100 px-4 py-3"><h2 id="rapor-heading" class="text-sm font-extrabold text-slate-900">Rapor {{ $reportType }} · {{ ucfirst($semester) }}</h2><p class="text-xs text-slate-500">{{ $visibleRaporList->count() }} siswa ditampilkan.</p></div>
            <div class="divide-y divide-slate-100">
                @forelse($visibleRaporList as $item)
                    @php
                        $siswa = $item['siswa'];
                        $rapor = $item['rapor'];
                        $status = $rapor?->status;
                        $avg = $rapor?->raporNilai->isNotEmpty() ? $rapor->raporNilai->avg('nilai_angka') : null;
                        $review = $rapor?->status_review_ketua;
                        $validation = $siswa->validasi_rapor_bendahara ? 'Akses terbuka' : ($siswa->validasi_rapor_ketua ? 'Tunggu Bendahara' : ($siswa->validasi_rapor_wali ? 'Tunggu Ketua' : 'Belum dikirim'));
                        $validationTone = $siswa->validasi_rapor_bendahara ? 'bg-emerald-50 text-emerald-800' : ($siswa->validasi_rapor_wali ? 'bg-sky-50 text-sky-800' : 'bg-slate-100 text-slate-700');
                    @endphp
                    <article class="grid grid-cols-[minmax(0,1fr)_auto] gap-x-2 gap-y-2 px-4 py-3 lg:grid-cols-[minmax(170px,1.3fr)_minmax(160px,1fr)_minmax(130px,0.8fr)_auto] lg:items-center lg:gap-x-4">
                        <div class="col-start-1 row-start-1 min-w-0"><h3 class="text-sm font-bold text-slate-900">{{ $siswa->nama_lengkap }}</h3><p class="text-[11px] text-slate-500">NIS {{ $siswa->nis ?: '—' }} · {{ $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>@if(in_array($review, ['revisi','perlu_revisi'], true) && $rapor->catatan_revisi_ketua)<p class="mt-1 rounded-lg bg-rose-50 px-2 py-1 text-[11px] text-rose-800">Revisi: {{ \Illuminate\Support\Str::limit($rapor->catatan_revisi_ketua, 90) }}</p>@endif</div>
                        <div class="col-start-1 row-start-2 flex flex-wrap items-center gap-2 lg:col-start-2 lg:row-start-1"><span class="rounded-full px-2 py-1 text-[11px] font-bold {{ $validationTone }}">{{ $validation }}</span><span class="rounded-full px-2 py-1 text-[11px] font-bold {{ !$rapor ? 'bg-slate-100 text-slate-700' : ($status === 'diterbitkan' ? 'bg-emerald-50 text-emerald-800' : 'bg-amber-50 text-amber-900') }}">{{ !$rapor ? 'Belum dibuat' : ($status === 'diterbitkan' ? 'Diterbitkan' : 'Draft') }}</span></div>
                        <p class="col-start-1 row-start-3 text-xs text-slate-600 lg:col-start-3 lg:row-start-1">Rata-rata <strong class="text-slate-900">{{ $avg === null ? '—' : number_format((float) $avg, 2, ',', '.') }}</strong>@if($rapor?->tanggal_rilis)<span class="block text-[11px] text-slate-500">Rilis {{ $rapor->tanggal_rilis->format('d/m/Y') }}</span>@endif</p>
                        <div class="col-start-2 row-span-3 row-start-1 flex flex-col items-end gap-1.5 self-start lg:col-start-4 lg:row-span-1 lg:flex-row lg:items-center lg:self-center">
                            @if($rapor)
                                <details class="group relative" @click.outside="$el.removeAttribute('open')">
                                    <summary aria-label="Aksi rapor {{ $siswa->nama_lengkap }}" class="flex min-h-10 cursor-pointer list-none items-center justify-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 text-xs font-bold text-slate-700 hover:bg-slate-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-sky-600 [&::-webkit-details-marker]:hidden">Aksi <svg aria-hidden="true" viewBox="0 0 16 16" fill="none" class="h-3.5 w-3.5 shrink-0 transition-transform group-open:rotate-180"><path d="m4 6 4 4 4-4" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"/></svg></summary>
                                    <div class="absolute right-0 top-full z-30 mt-1 grid w-44 gap-0.5 rounded-xl border border-slate-200 bg-white p-1.5 shadow-xl">
                                <a href="{{ route('wali.rapor.edit', $rapor->id) }}" class="flex min-h-9 items-center rounded-lg px-3 text-xs font-bold text-sky-800 hover:bg-sky-50">Edit</a>
                                <a href="{{ route('wali.rapor.preview', $rapor->id) }}" target="_blank" rel="noopener" class="flex min-h-9 items-center rounded-lg px-3 text-xs font-bold text-slate-700 hover:bg-slate-50">Pratinjau</a>
                                <a href="{{ route('wali.rapor.export-excel', $rapor->id) }}" class="flex min-h-9 items-center rounded-lg px-3 text-xs font-bold text-slate-700 hover:bg-slate-50">Excel</a>
                                @if($status === 'draft')
                                    @if(!$siswa->validasi_rapor_wali)
                                        <button type="button" data-action="{{ route('wali.rapor.kirim-validasi', $rapor->id) }}" data-name="{{ $siswa->nama_lengkap }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Kirim rapor ke Ketua?'; confirmName = $el.dataset.name; confirmNote = 'Rapor akan menunggu review. Kiriman dapat dibatalkan sebelum Ketua memprosesnya.'; confirmLabel = 'Ya, kirim'; confirmDanger = false; confirmDelete = false; $refs.confirmDialog.showModal()" class="flex min-h-9 w-full items-center rounded-lg px-3 text-left text-xs font-bold text-emerald-700 hover:bg-emerald-50">Kirim ke Ketua</button>
                                    @elseif(!$siswa->validasi_rapor_ketua)
                                        <button type="button" data-action="{{ route('wali.rapor.batalkan-kirim-validasi', $rapor->id) }}" data-name="{{ $siswa->nama_lengkap }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Batalkan kiriman?'; confirmName = $el.dataset.name; confirmNote = 'Status validasi kembali ke awal. Data rapor tidak terhapus.'; confirmLabel = 'Ya, batalkan'; confirmDanger = true; confirmDelete = false; $refs.confirmDialog.showModal()" class="flex min-h-9 w-full items-center rounded-lg px-3 text-left text-xs font-bold text-amber-800 hover:bg-amber-50">Batalkan kiriman</button>
                                    @endif
                                    @if($siswa->validasi_rapor_bendahara)
                                        <button type="button" data-action="{{ route('wali.rapor.terbitkan', $rapor->id) }}" data-name="{{ $siswa->nama_lengkap }}" data-date="{{ $rapor->tanggal_rilis?->format('Y-m-d') }}" @click="scheduledAction = $el.dataset.action; scheduledName = $el.dataset.name; scheduledDate = $el.dataset.date; $refs.scheduleDialog.showModal()" class="flex min-h-9 w-full items-center rounded-lg px-3 text-left text-xs font-bold text-sky-800 hover:bg-sky-50">Atur rilis</button>
                                        <button type="button" data-action="{{ route('wali.rapor.terbitkan', $rapor->id) }}" data-name="{{ $siswa->nama_lengkap }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Rilis rapor sekarang?'; confirmName = $el.dataset.name; confirmNote = 'Tanggal rilis diatur hari ini dan rapor dapat dilihat wali siswa.'; confirmLabel = 'Ya, rilis'; confirmDanger = false; confirmDelete = false; $refs.confirmDialog.showModal()" class="flex min-h-9 w-full items-center rounded-lg px-3 text-left text-xs font-bold text-emerald-700 hover:bg-emerald-50">Rilis sekarang</button>
                                    @endif
                                    <button type="button" data-action="{{ route('wali.rapor.destroy', $rapor->id) }}" data-name="{{ $siswa->nama_lengkap }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Hapus draft rapor?'; confirmName = $el.dataset.name; confirmNote = 'Draft dihapus permanen; nilai siswa tetap tersimpan.'; confirmLabel = 'Ya, hapus'; confirmDanger = true; confirmDelete = true; $refs.confirmDialog.showModal()" class="flex min-h-9 w-full items-center rounded-lg px-3 text-left text-xs font-bold text-rose-700 hover:bg-rose-50">Hapus draft</button>
                                @elseif($status === 'diterbitkan')
                                    <button type="button" data-action="{{ route('wali.rapor.tarik-kembali', $rapor->id) }}" data-name="{{ $siswa->nama_lengkap }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Tarik kembali rapor?'; confirmName = $el.dataset.name; confirmNote = 'Rapor kembali menjadi draft dan tidak terlihat oleh wali siswa sampai diterbitkan ulang.'; confirmLabel = 'Ya, tarik'; confirmDanger = true; confirmDelete = false; $refs.confirmDialog.showModal()" class="flex min-h-9 w-full items-center rounded-lg px-3 text-left text-xs font-bold text-amber-800 hover:bg-amber-50">Tarik kembali</button>
                                @endif
                                    </div>
                                </details>
                            @else
                                <button type="button" aria-label="Generate rapor {{ $siswa->nama_lengkap }}" data-action="{{ route('wali.rapor.generate-single', $siswa->id) }}" data-name="{{ $siswa->nama_lengkap }}" @click="confirmAction = $el.dataset.action; confirmTitle = 'Generate rapor siswa?'; confirmName = $el.dataset.name; confirmNote = 'Draft dibuat dari nilai yang telah diinput untuk semester dan jenis rapor ini.'; confirmLabel = 'Ya, generate'; confirmDanger = false; confirmDelete = false; $refs.confirmDialog.showModal()" class="inline-flex min-h-10 min-w-24 items-center justify-center rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Generate</button>
                                <button type="button" aria-label="Upload PDF rapor {{ $siswa->nama_lengkap }}" data-id="{{ $siswa->id }}" data-name="{{ $siswa->nama_lengkap }}" @click="uploadStudent = $el.dataset.id; uploadName = $el.dataset.name; $refs.uploadDialog.showModal()" class="inline-flex min-h-10 min-w-24 items-center justify-center rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Upload PDF</button>
                            @endif
                        </div>
                    </article>
                @empty
                    <p class="px-4 py-9 text-center text-sm text-slate-500">Tidak ada siswa yang cocok.</p>
                @endforelse
            </div>
        </section>
    @endif

    <dialog x-ref="confirmDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900" x-text="confirmTitle"></h2><p class="mt-1 text-xs text-slate-500" x-text="confirmName"></p></div><p class="px-4 py-4 text-xs leading-5 text-slate-700" x-text="confirmNote"></p><form :action="confirmAction" method="POST" class="flex justify-end gap-2 border-t border-slate-100 p-4">
        @csrf
        <template x-if="confirmDelete"><input type="hidden" name="_method" value="DELETE"></template>
        <input type="hidden" name="semester" value="{{ $semester ?? 'ganjil' }}">
        <input type="hidden" name="jenis_rapor" value="{{ $jenisRapor ?? 'akhir_semester' }}">
        <button type="button" @click="$refs.confirmDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg px-3 text-xs font-bold text-white" :class="confirmDanger ? 'bg-rose-600' : 'bg-sky-700'" x-text="confirmLabel"></button>
    </form></dialog>
    <dialog x-ref="scheduleDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Atur tanggal rilis</h2><p class="text-xs text-slate-500" x-text="scheduledName"></p></div><form :action="scheduledAction" method="POST" class="space-y-4 p-4">
        @csrf
        <label class="block text-xs font-bold text-slate-700">Tanggal rilis<input type="date" name="tanggal_rilis" x-model="scheduledDate" min="{{ now()->format('Y-m-d') }}" required class="mt-1 h-10 w-full rounded-lg border border-slate-300 px-3 text-sm font-normal text-slate-800"></label><p class="text-xs leading-5 text-slate-600">Rapor diterbitkan dengan tanggal ini; wali siswa dapat melihatnya mulai tanggal rilis.</p><div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.scheduleDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Atur & terbitkan</button></div>
    </form></dialog>
    <dialog x-ref="uploadDialog" class="w-[calc(100%-2rem)] max-w-md rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Upload rapor PDF</h2><p class="text-xs text-slate-500" x-text="uploadName"></p></div><form action="{{ route('wali.rapor.create-with-mode') }}" method="POST" enctype="multipart/form-data" class="space-y-4 p-4">
        @csrf
        <input type="hidden" name="siswa_id" :value="uploadStudent"><input type="hidden" name="semester" value="{{ $semester ?? 'ganjil' }}"><input type="hidden" name="jenis_rapor" value="{{ $jenisRapor ?? 'akhir_semester' }}"><input type="hidden" name="mode" value="upload_pdf"><label class="block text-xs font-bold text-slate-700">File PDF maksimal 10 MB<input type="file" name="uploaded_pdf" accept=".pdf,application/pdf" required class="mt-1 block w-full rounded-lg border border-slate-300 p-2 text-xs font-normal"></label><div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.uploadDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button><button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Upload PDF</button></div>
    </form></dialog>
    <dialog x-ref="templateDialog" class="w-[calc(100%-2rem)] max-w-xl rounded-2xl border border-slate-200 bg-white p-0 shadow-2xl backdrop:bg-slate-950/50" @click.self="$el.close()"><div class="border-b border-slate-100 px-4 py-3"><h2 class="text-sm font-extrabold text-slate-900">Terapkan template ke kelas</h2></div><form action="{{ route('wali.rapor.apply-template-batch') }}" method="POST" class="space-y-4 p-4">
        @csrf
        <input type="hidden" name="semester" value="{{ $semester ?? 'ganjil' }}"><input type="hidden" name="jenis_rapor" value="{{ $jenisRapor ?? 'akhir_semester' }}">
        @if(($templateMapelList ?? collect())->isEmpty())
            <p class="rounded-lg bg-amber-50 p-3 text-xs text-amber-900">Belum ada rapor untuk periode ini. Generate rapor terlebih dahulu.</p>
        @else
            <p class="text-xs leading-5 text-slate-600">Pilih template per mapel. Yang dibiarkan Lewati tidak berubah. Secara default hanya deskripsi kosong yang diisi.</p><div class="max-h-[45vh] divide-y divide-slate-100 overflow-y-auto rounded-lg border border-slate-200">@foreach($templateMapelList as $mapel)@php $options = $templatesByMapel[$mapel->id] ?? collect(); @endphp<div class="grid gap-2 p-3 text-xs sm:grid-cols-[minmax(130px,0.8fr)_minmax(180px,1.2fr)] sm:items-center"><span class="font-bold text-slate-800">{{ $mapel->nama_mapel }}</span>@if($options->isEmpty())<span class="text-slate-500">Belum ada template.</span>@else<select name="templates[{{ $mapel->id }}]" class="h-9 w-full rounded-lg border border-slate-300 bg-white px-2 text-xs"><option value="">Lewati</option>@foreach($options as $template)<option value="{{ $template->id }}">{{ \Illuminate\Support\Str::limit($template->template_text, 70) }}</option>@endforeach</select>@endif</div>@endforeach</div>
            <label class="flex items-start gap-2 text-xs text-slate-700"><input type="checkbox" name="overwrite" value="1" class="mt-0.5 rounded border-slate-300"><span>Timpa deskripsi yang sudah terisi</span></label>
        @endif
        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4"><button type="button" @click="$refs.templateDialog.close()" class="min-h-10 rounded-lg border border-slate-300 px-3 text-xs font-bold text-slate-700">Batal</button>@if(($templateMapelList ?? collect())->isNotEmpty())<button type="submit" class="min-h-10 rounded-lg bg-sky-700 px-3 text-xs font-bold text-white">Terapkan</button>@endif</div>
    </form></dialog>
</div>
@endsection
