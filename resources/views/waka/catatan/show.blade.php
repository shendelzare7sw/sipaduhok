@extends('layouts.sneat')

@section('title', 'Detail Catatan')
@section('page-title', 'Detail Catatan')
@section('page-subtitle', 'Informasi lengkap catatan yang dikirim')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-sticky-note text-warning me-2"></i>
                Detail Catatan
            </h5>
            <a href="{{ route('waka.catatan.index') }}" class="btn btn-sm btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
        <div class="card-body">
            <!-- Judul -->
            <div class="mb-4">
                <label class="text-muted small">Judul</label>
                <h4 class="mb-0">{{ $catatan->judul }}</h4>
            </div>

            <!-- Meta Info -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="text-muted small">Pengirim</label>
                    <p class="mb-0">{{ $catatan->pengirim->name }}</p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small">Tanggal Kirim</label>
                    <p class="mb-0">{{ \Carbon\Carbon::parse($catatan->tanggal_kirim)->format('d M Y, H:i') }}</p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small">Tipe Penerima</label>
                    <p class="mb-0">
                        <span class="badge bg-{{ $catatan->tipe_penerima == 'semua' ? 'primary' : ($catatan->tipe_penerima == 'role' ? 'info' : 'warning') }}">
                            @if($catatan->tipe_penerima == 'semua')
                                Semua Pengguna
                            @elseif($catatan->tipe_penerima == 'role')
                                {{ ucwords(str_replace('_', ' ', $catatan->role_penerima)) }}
                            @else
                                Individu
                            @endif
                        </span>
                    </p>
                </div>
                <div class="col-md-3">
                    <label class="text-muted small">Prioritas</label>
                    <p class="mb-0">
                        <span class="badge bg-{{ $catatan->prioritas == 'mendesak' ? 'danger' : ($catatan->prioritas == 'penting' ? 'warning' : 'info') }}">
                            {{ ucfirst($catatan->prioritas) }}
                        </span>
                    </p>
                </div>
            </div>

            <hr>

            <!-- Isi Catatan -->
            <div class="mb-4">
                <label class="text-muted small">Isi Catatan</label>
                <div class="bg-light p-3 rounded">
                    {!! nl2br(e($catatan->isi_catatan)) !!}
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
