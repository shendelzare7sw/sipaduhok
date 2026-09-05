@props(['ctx' => null])

@php
    $unreadCount = \App\Models\Notification::where('user_id', auth()->id())->unread()->count();
@endphp

<div id="admin-notification-dropdown" class="relative"
    data-context="{{ $ctx ?? '' }}"
    data-recent-url="{{ route('notifications.recent') }}"
    data-unread-url="{{ route('notifications.unread-count') }}"
    data-read-url-template="{{ url('/notifications/__ID__/read') }}">
    <button type="button" data-notification-toggle
        class="relative flex h-11 w-11 items-center justify-center rounded-xl border border-slate-200 text-slate-600 transition hover:border-brand-500 hover:text-brand-600"
        aria-label="Buka notifikasi" aria-expanded="false" aria-controls="admin-notification-panel">
        <i class="fa-regular fa-bell"></i>
        <span id="admin-notification-count" class="absolute -right-1 -top-1 {{ $unreadCount > 0 ? 'flex' : 'hidden' }} min-h-5 min-w-5 items-center justify-center rounded-full bg-red-500 px-1 text-[10px] font-bold text-white ring-2 ring-white">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
    </button>

    <section id="admin-notification-panel" class="fixed left-4 right-4 top-[4.75rem] z-50 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl sm:absolute sm:left-auto sm:right-0 sm:top-full sm:mt-2 sm:w-96" aria-label="Notifikasi terbaru">
        <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900">Notifikasi</h2>
                <p class="text-[10px] text-slate-500">Pembaruan terbaru untuk Anda</p>
            </div>
            <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                @csrf
                <button type="submit" class="text-[11px] font-bold text-brand-600 hover:text-brand-700">Tandai dibaca</button>
            </form>
        </div>
        <div id="admin-notification-list" class="max-h-[min(28rem,65vh)] overflow-y-auto">
            <div class="flex items-center justify-center gap-2 px-4 py-10 text-xs text-slate-500">
                <i class="fa-solid fa-circle-notch animate-spin text-brand-500"></i> Memuat notifikasi...
            </div>
        </div>
        <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}" class="flex min-h-11 items-center justify-center gap-2 border-t border-slate-100 bg-slate-50 px-4 py-3 text-xs font-bold text-brand-600 hover:bg-brand-50">
            Lihat semua notifikasi <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </section>
</div>
