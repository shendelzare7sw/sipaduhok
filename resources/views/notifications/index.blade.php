@php
    $userRole = auth()->user()->role ?? 'siswa';
    $layout = match ($userRole) {
        'siswa' => 'layouts.lms',
        'guru_pengajar' => 'layouts.lms-guru',
        default => 'layouts.sneat',
    };
@endphp

@extends($layout)

@section('title', 'Notifikasi')

@if($userRole === 'siswa')
@section('sidebar-menu')
    @include('siswa.partials.sidebar-lms')
@endsection
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua notifikasi untuk Anda')
@elseif($userRole === 'guru_pengajar')
@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Semua notifikasi untuk Anda')
@endif

@section('content')
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-bell me-2"></i>Notifikasi
                        </h5>
                        @if($notifications->where('read_at', null)->count() > 0)
                            <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-check-double me-1"></i>Tandai Semua Dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="card-body p-0">
                        {{-- Filter Tabs --}}
                        <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link {{ !isset($tipe) ? 'active' : '' }}"
                                    href="{{ route('notifications.index') }}">
                                    Semua
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'materi' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'materi') }}">
                                    <i class="fas fa-book text-primary"></i> Materi
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'tugas' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'tugas') }}">
                                    <i class="fas fa-tasks text-warning"></i> Tugas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'ujian' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'ujian') }}">
                                    <i class="fas fa-file-alt text-danger"></i> Ujian
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'forum' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'forum') }}">
                                    <i class="fas fa-comments text-info"></i> Forum
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'izin' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'izin') }}">
                                    <i class="fas fa-file-medical text-warning"></i> Izin
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'catatan' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'catatan') }}">
                                    <i class="fas fa-sticky-note text-info"></i> Catatan
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'pembayaran' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'pembayaran') }}">
                                    <i class="fas fa-money-check-alt text-success"></i> Pembayaran
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ ($tipe ?? '') === 'rapor' ? 'active' : '' }}"
                                    href="{{ route('notifications.by-type', 'rapor') }}">
                                    <i class="fas fa-graduation-cap text-primary"></i> Rapor
                                </a>
                            </li>
                        </ul>

                        {{-- Notification List --}}
                        <div class="list-group list-group-flush">
                            @forelse($notifications as $notif)
                                <div class="list-group-item list-group-item-action {{ $notif->read_at ? '' : 'bg-light' }}"
                                    style="border-left: 4px solid var(--bs-{{ $notif->color ?? 'secondary' }});">
                                    <div class="d-flex w-100 justify-content-between align-items-start">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3"
                                                style="width: 40px; height: 40px; border-radius: 50%; background: var(--bs-{{ $notif->color ?? 'secondary' }}); display: flex; align-items: center; justify-content: center;">
                                                <i class="{{ $notif->icon ?? 'fas fa-bell' }} text-white"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1 {{ $notif->read_at ? 'text-muted' : 'fw-bold' }}">
                                                    {{ $notif->judul }}
                                                </h6>
                                                <p class="mb-1 text-muted small">{{ $notif->pesan }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            @if(!$notif->read_at)
                                                <span class="badge bg-primary rounded-pill">Baru</span>
                                            @endif
                                            @if($notif->link)
                                                <form action="{{ route('notifications.mark-read', $notif->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Lihat">
                                                        <i class="fas fa-external-link-alt"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('notifications.destroy', $notif->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                    onclick="return confirm('Hapus notifikasi ini?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada notifikasi</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @if($notifications->hasPages())
                        <div class="card-footer">
                            {{ $notifications->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection