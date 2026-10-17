{{--
    Pop-up flyer aktif untuk pengguna yang sedang masuk, dimiliki layout SIA & LMS (bukan per dashboard)
    agar target Siswa, Guru, Wali Kelas, Wali Siswa, dan "Semua Pengguna" benar-benar sampai ke perannya.
    Setiap flyer tampil sekali per perangkat; flyer baru atau yang diperbarui tampil lagi.
--}}
@php
    $targetFlyer = match (auth()->user()?->role) {
        'siswa' => 'siswa',
        'guru_pengajar' => 'guru',
        'wali_kelas' => 'wali_kelas',
        'orang_tua' => 'orang_tua',
        default => 'semua',
    };
    $flyer = auth()->check() ? \App\Models\Flyer::getPopupFlyers($targetFlyer, 1)->first() : null;
@endphp
@if($flyer)
    <div data-kunci="flyer:{{ auth()->id() }}:{{ $flyer->id }}:{{ $flyer->updated_at?->timestamp }}"
        x-data="{ open: false, kunci: '', init() { this.kunci = this.$el.dataset.kunci; try { if (! localStorage.getItem(this.kunci)) setTimeout(() => this.open = true, 1200); } catch (e) {} }, close() { this.open = false; try { localStorage.setItem(this.kunci, '1'); } catch (e) {} } }"
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
