<div id="admin-search-backdrop" class="fixed inset-0 z-[60] hidden bg-slate-950/50 backdrop-blur-sm"></div>
<section id="admin-search-panel" class="fixed left-1/2 top-4 z-[70] hidden w-[calc(100%-2rem)] max-w-2xl -translate-x-1/2 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:top-20" role="dialog" aria-modal="true" aria-label="Cari menu">
    <div class="flex items-center gap-3 border-b border-slate-200 px-4">
        <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
        <input id="admin-menu-search" type="search" class="h-14 min-w-0 flex-1 border-0 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400 focus:ring-0" placeholder="Contoh: import siswa, jadwal, pembayaran..." autocomplete="off">
        <button type="button" data-search-close class="rounded-lg border border-slate-200 px-2 py-1 text-[10px] font-bold text-slate-500 hover:bg-slate-50">ESC</button>
    </div>
    <div class="max-h-[70vh] overflow-y-auto p-2">
        <div id="admin-search-results"></div>
        <div id="admin-search-empty" class="hidden px-4 py-12 text-center">
            <i class="fa-regular fa-folder-open text-3xl text-slate-300"></i>
            <p class="mt-3 text-sm font-semibold text-slate-700">Menu tidak ditemukan</p>
            <p class="mt-1 text-xs text-slate-500">Coba kata lain seperti &ldquo;siswa&rdquo;, &ldquo;jadwal&rdquo;, atau &ldquo;tagihan&rdquo;.</p>
        </div>
    </div>
</section>
