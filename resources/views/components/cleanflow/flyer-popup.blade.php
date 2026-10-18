{{--
    Pop-up flyer aktif, dimiliki layout SIA & LMS agar target Siswa, Guru, Wali Kelas, Wali Siswa,
    dan "Semua Pengguna" sampai ke perannya. Hanya tampil SEKALI PER LOGIN: penanda sesi
    `flyer_awal_login` dipasang saat login (AppServiceProvider) dan dipakai di halaman pertama
    setelah masuk; flyer muncul hanya bila halaman itu dashboard peran. Halaman Pilih Kelas wali
    kelas adalah jalur menuju dashboard, jadi tidak menghabiskan penanda. Halaman lain (termasuk
    Latihan & Ujian siswa) tidak pernah menampilkan flyer.
--}}
@php
    $flyer = null;
    if (auth()->check() && session()->has('flyer_awal_login') && ! request()->routeIs('wali.pilih-kelas')) {
        session()->forget('flyer_awal_login');
        $dashboard = request()->routeIs('admin.dashboard', 'ketua.dashboard', 'waka.dashboard', 'sekretaris.dashboard', 'bendahara.dashboard',
            'wali.dashboard', 'guru.dashboard', 'siswa.sia.dashboard', 'wali-siswa.dashboard');
        if ($dashboard) {
            $targetFlyer = match (auth()->user()->role) {
                'siswa' => 'siswa',
                'guru_pengajar' => 'guru',
                'wali_kelas' => 'wali_kelas',
                'orang_tua' => 'orang_tua',
                default => 'semua',
            };
            $flyer = \App\Models\Flyer::getPopupFlyers($targetFlyer, 1)->first();
        }
    }
@endphp
@if($flyer)
    <div data-flyer-popup x-data="{ open: false, init() { setTimeout(() => this.open = true, 1200); }, close() { this.open = false; } }"
        @keydown.escape.window="close()" class="print:hidden">
        <template x-teleport="body">
            <div x-cloak x-show="open" x-transition.opacity class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-950/60 p-4 backdrop-blur-sm" @click.self="close()" role="dialog" aria-modal="true" aria-label="{{ $flyer->judul }}">
                <div class="relative max-h-full w-full max-w-md overflow-y-auto rounded-2xl bg-white shadow-2xl">
                    <button type="button" @click="close()" class="absolute right-3 top-3 flex h-9 w-9 items-center justify-center rounded-full bg-white/90 text-slate-600 shadow ring-1 ring-slate-200 hover:bg-white hover:text-slate-900" aria-label="Tutup"><i class="fa-solid fa-xmark" aria-hidden="true"></i></button>
                    <img src="{{ $flyer->gambar_url }}" alt="{{ $flyer->judul }}" class="max-h-[60vh] w-full object-cover">
                    <div class="p-5 text-center">
                        <h3 class="text-base font-extrabold text-brand-700">{{ $flyer->judul }}</h3>
                        @if($flyer->deskripsi)<p class="mt-2 text-sm leading-6 text-slate-600">{{ $flyer->deskripsi }}</p>@endif
                        @if($flyer->link_url)
                            <a href="{{ $flyer->link_url }}" target="_blank" rel="noopener" class="mt-4 inline-flex min-h-10 items-center justify-center rounded-full bg-brand-600 px-5 text-xs font-extrabold text-white no-underline hover:bg-brand-700">Lihat selengkapnya</a>
                        @endif
                    </div>
                </div>
            </div>
        </template>
    </div>
@endif
