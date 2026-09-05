@php
    $role = auth()->user()->role ?? null;
    $canContactDeveloper = in_array($role, ['admin', 'super_admin', 'waka', 'wakil_kepala_sekolah'], true);
@endphp

<footer class="border-t border-slate-200 bg-white px-4 py-2 text-[11px] text-slate-500 sm:px-6 lg:px-8">
    <div class="flex min-h-8 w-full flex-wrap items-center justify-center gap-x-4 gap-y-1 text-center sm:justify-between sm:text-left">
        <p class="min-w-0">
            Copyright &copy; {{ date('Y') }}
            <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="font-bold text-brand-700 no-underline hover:text-brand-800">SIPADUHOK</a>
            <span class="whitespace-nowrap">&middot; PKBM House of Knowledge</span>
        </p>

        @if($canContactDeveloper)
            <a href="https://wa.me/6282113100791?text=Halo%20Developer,%20saya%20menemukan%20kendala/bug%20pada%20sistem"
                target="_blank" rel="noopener noreferrer"
                class="inline-flex items-center justify-center gap-1.5 font-bold text-emerald-700 no-underline hover:text-emerald-800">
                <i class="fab fa-whatsapp text-sm" aria-hidden="true"></i>
                Kontak Developer
            </a>
        @endif
    </div>
</footer>

<button type="button" data-scroll-to-top class="fixed bottom-14 right-6 z-[1040] hidden h-12 w-12 items-center justify-center rounded-full bg-brand-600 text-white shadow-lg shadow-brand-900/20 transition hover:-translate-y-0.5 hover:bg-brand-700 min-[769px]:bottom-[76px] min-[769px]:h-14 min-[769px]:w-14" aria-label="Kembali ke atas">
    <i class="fas fa-angle-up" aria-hidden="true"></i>
</button>
