@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim catatan atau teguran kepada tenaga pendidik dan siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note text-warning me-2"></i>
                        Riwayat Catatan Terkirim
                    </h5>
                    <a href="{{ route('waka.catatan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Kirim Catatan Baru
                    </a>
                </div>
                <div class="card-body">
                    @forelse($catatan as $item)
                        <div
                            class="card mb-3 border-start border-{{ $item->prioritas == 'mendesak' ? 'danger' : ($item->prioritas == 'penting' ? 'warning' : 'primary') }} border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-1">{{ $item->judul }}</h5>
                                    <span
                                        class="badge bg-{{ $item->prioritas == 'mendesak' ? 'danger' : ($item->prioritas == 'penting' ? 'warning' : 'info') }}">
                                        {{ ucfirst($item->prioritas) }}
                                    </span>
                                </div>

                                <p class="card-text text-muted mb-2">{{ Str::limit($item->isi_catatan, 150) }}</p>

                                <div class="d-flex gap-3 text-muted small">
                                    <span>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d M Y, H:i') }}
                                    </span>
                                    <span>
                                        <i
                                            class="fas fa-{{ $item->tipe_penerima == 'semua' ? 'users' : ($item->tipe_penerima == 'role' ? 'user-tag' : 'user') }} me-1"></i>
                                        @if($item->tipe_penerima == 'semua')
                                            Semua Pengguna
                                        @elseif($item->tipe_penerima == 'role')
                                            {{ ucwords(str_replace('_', ' ', $item->role_penerima)) }}
                                        @else
                                            Individu
                                        @endif
                                    </span>
                                </div>

                                <div class="mt-2">
                                    <a href="{{ route('waka.catatan.show', $item->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada catatan yang dikirim</p>
                            <a href="{{ route('waka.catatan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Kirim Catatan Pertama
                            </a>
                        </div>
                    @endforelse

                    @if($catatan->hasPages())
                        <div class="mt-3">
                            {{ $catatan->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
@endsection