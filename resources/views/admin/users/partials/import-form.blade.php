<div class="grid min-w-0 gap-5 xl:grid-cols-[minmax(0,1fr)_340px]">
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <header class="border-b border-slate-200 p-4 sm:p-5"><h2 class="text-base font-extrabold text-slate-900"><i class="fas fa-file-excel mr-2 text-emerald-600" aria-hidden="true"></i>Upload File Excel</h2><p class="mt-1 text-xs text-slate-500">Gunakan template agar urutan dan format kolom sesuai.</p></header>
        <form action="{{ $storeRoute }}" method="POST" enctype="multipart/form-data">@csrf
            <div class="space-y-4 p-4 sm:p-5">
                <div class="rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 p-5 text-center sm:p-8"><span class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-xl text-emerald-600"><i class="fas fa-cloud-upload-alt" aria-hidden="true"></i></span><label for="import-file-{{ $variant }}" class="mt-4 block text-sm font-extrabold text-slate-900">Pilih file Excel</label><p class="mt-1 text-xs text-slate-500">Format .xlsx atau .xls, maksimal 5 MB.</p><input id="import-file-{{ $variant }}" type="file" name="file" accept=".xlsx,.xls" required class="mt-4 block w-full rounded-xl border border-slate-200 bg-white text-xs text-slate-600 file:mr-3 file:border-0 file:bg-brand-600 file:px-4 file:py-3 file:text-xs file:font-bold file:text-white hover:file:bg-brand-700">@error('file')<p class="mt-2 text-left text-xs font-semibold text-red-600">{{ $message }}</p>@enderror</div>
                <a href="{{ $templateRoute }}" class="inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-emerald-50 text-xs font-bold text-emerald-700 no-underline ring-1 ring-emerald-200 hover:bg-emerald-100"><i class="fas fa-download" aria-hidden="true"></i>Download Template Excel</a>
            </div>
            <footer class="flex justify-end gap-2 border-t border-slate-200 bg-slate-50 px-4 py-3 sm:px-5"><a href="{{ $backRoute }}" class="inline-flex h-10 items-center justify-center rounded-xl px-4 text-xs font-bold text-slate-600 no-underline hover:bg-slate-200">Batal</a><button type="submit" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 text-xs font-bold text-white hover:bg-brand-700"><i class="fas fa-upload" aria-hidden="true"></i>Import Data</button></footer>
        </form>
    </section>

    <aside class="space-y-4">
        <section class="rounded-2xl border border-blue-200 bg-blue-50 p-4"><h2 class="text-sm font-extrabold text-blue-950"><i class="fas fa-list-ol mr-1.5" aria-hidden="true"></i>{{ $heading }}</h2><ol class="mt-3 list-decimal space-y-2 pl-5 text-xs leading-5 text-blue-900">@foreach($instructions as $instruction)<li>{!! $instruction !!}</li>@endforeach</ol></section>
        <section class="rounded-2xl border border-amber-200 bg-amber-50 p-4"><h2 class="text-sm font-extrabold text-amber-900"><i class="fas fa-shield-alt mr-1.5" aria-hidden="true"></i>Sebelum Import</h2><ul class="mt-2 list-disc space-y-1 pl-4 text-xs leading-5 text-amber-800"><li>Jangan mengubah nama kolom template.</li><li>Periksa kembali cabang dan kelas.</li><li>Simpan salinan file untuk koreksi data.</li></ul></section>
    </aside>
</div>
