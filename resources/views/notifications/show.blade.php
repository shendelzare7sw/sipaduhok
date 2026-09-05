@php
    $userRole = auth()->user()->role ?? 'siswa';
    $ctx = request('ctx');
    $layout = match ($userRole) {
        'siswa' => $ctx === 'lms' ? 'layouts.lms' : 'layouts.app',
        'guru_pengajar' => $ctx === 'lms-guru' ? 'layouts.lms-guru' : 'layouts.app',
        default => 'layouts.app',
    };
    $sidebarPartial = match (true) {
        $userRole === 'siswa' && $ctx === 'lms' => 'siswa.partials.sidebar-lms',
        $userRole === 'siswa' => 'siswa.partials.sidebar-sia',
        $userRole === 'guru_pengajar' && $ctx === 'lms-guru' => 'guru.partials.sidebar-lms-notif',
        $userRole === 'guru_pengajar' => 'guru.partials.sidebar',
        $userRole === 'admin' => 'admin.partials.cleanflow-sidebar',
        $userRole === 'bendahara' => 'bendahara.partials.sidebar',
        $userRole === 'wali_kelas' => 'wali-kelas.partials.sidebar',
        $userRole === 'ketua_pkbm' => 'ketua.partials.sidebar',
        $userRole === 'wakil_kepala_sekolah' => 'waka.partials.sidebar',
        $userRole === 'sekretaris' => 'sekretaris.partials.sidebar',
        $userRole === 'orang_tua' => 'wali-siswa.partials.sidebar',
        default => 'admin.partials.cleanflow-sidebar',
    };
    $typeLabels = [
        'materi' => 'Materi', 'tugas' => 'Tugas', 'ujian' => 'Ujian', 'forum' => 'Forum',
        'pengumuman' => 'Pengumuman', 'deadline' => 'Tenggat', 'nilai' => 'Nilai', 'izin' => 'Izin',
        'catatan' => 'Catatan', 'pembayaran' => 'Keuangan', 'rapor' => 'Rapor', 'sistem' => 'Sistem',
        'kelas' => 'Kelas', 'kenaikan' => 'Kenaikan', 'recovery' => 'Pemulihan',
    ];
    $toneClasses = [
        'primary' => ['bar' => 'bg-blue-600', 'icon' => 'bg-blue-600', 'badge' => 'bg-blue-50 text-blue-700'],
        'success' => ['bar' => 'bg-emerald-600', 'icon' => 'bg-emerald-600', 'badge' => 'bg-emerald-50 text-emerald-700'],
        'danger' => ['bar' => 'bg-red-600', 'icon' => 'bg-red-600', 'badge' => 'bg-red-50 text-red-700'],
        'warning' => ['bar' => 'bg-amber-500', 'icon' => 'bg-amber-500', 'badge' => 'bg-amber-50 text-amber-700'],
        'info' => ['bar' => 'bg-cyan-600', 'icon' => 'bg-cyan-600', 'badge' => 'bg-cyan-50 text-cyan-700'],
        'secondary' => ['bar' => 'bg-slate-600', 'icon' => 'bg-slate-600', 'badge' => 'bg-slate-100 text-slate-700'],
    ];
    $tone = $toneClasses[$notification->color] ?? $toneClasses['secondary'];
    $typeLabel = $typeLabels[$notification->tipe] ?? ucfirst($notification->tipe);
@endphp

@extends($layout)
@section('title', 'Detail Notifikasi')
@section('page-title', 'Detail Notifikasi')
@section('page-subtitle', 'Baca pesan dan buka sumber terkait')

@section('sidebar-menu')
    @include($sidebarPartial)
@endsection

@section('content')
<div data-notification-detail-page class="min-w-0 space-y-4">
    <a href="{{ route('notifications.index', $ctx ? ['ctx' => $ctx] : []) }}" class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 text-xs font-bold text-slate-600 no-underline shadow-sm transition hover:border-brand-200 hover:text-brand-700">
        <i class="fas fa-arrow-left" aria-hidden="true"></i> Kembali ke notifikasi
    </a>

    <article class="min-w-0 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="h-2 {{ $tone['bar'] }}"></div>
        <header class="flex min-w-0 flex-col gap-4 border-b border-slate-200 px-5 py-5 sm:flex-row sm:items-start sm:px-7 sm:py-6">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl text-lg text-white shadow-sm {{ $tone['icon'] }}">
                <i class="{{ $notification->icon ?? 'fas fa-bell' }}" aria-hidden="true"></i>
            </span>
            <div class="min-w-0 flex-1">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="rounded-full px-2.5 py-1 text-[10px] font-extrabold {{ $tone['badge'] }}">{{ $typeLabel }}</span>
                    <time datetime="{{ $notification->created_at->toIso8601String() }}" class="text-[11px] font-medium text-slate-500">
                        <i class="far fa-clock mr-1" aria-hidden="true"></i>{{ $notification->created_at->copy()->locale('id')->translatedFormat('d M Y, H:i') }}
                    </time>
                </div>
                <h2 class="mt-3 break-words text-lg font-extrabold leading-snug text-slate-950 sm:text-xl">{{ $notification->judul }}</h2>
            </div>
        </header>

        <div class="px-5 py-6 sm:px-7 sm:py-8">
            <p class="whitespace-pre-line break-words text-sm leading-7 text-slate-700">{{ $notification->pesan }}</p>

            @if($notification->link && !str_contains($notification->link, '/notifications'))
                <div class="mt-7 border-t border-slate-100 pt-5">
                    <a href="{{ $notification->link }}" class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 text-sm font-bold text-white no-underline shadow-sm transition hover:bg-brand-700 sm:w-auto">
                        <i class="fas fa-external-link-alt" aria-hidden="true"></i> Buka sumber terkait
                    </a>
                </div>
            @endif
        </div>

        <footer class="flex items-center gap-2 border-t border-slate-200 bg-slate-50 px-5 py-3 text-[11px] font-semibold text-emerald-700 sm:px-7">
            <i class="fas fa-check-circle" aria-hidden="true"></i> Notifikasi sudah dibaca
        </footer>
    </article>
</div>
@endsection
