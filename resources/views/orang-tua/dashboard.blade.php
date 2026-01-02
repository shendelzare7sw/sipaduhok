@extends('layouts.sneat')

@section('title', 'Dashboard Orang Tua')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Dashboard Orang Tua</h4>
            <p class="text-muted mb-0">Monitoring Pendidikan & Keuangan Anak</p>
        </div>
        <div class="d-none d-sm-block">
            <span class="badge bg-label-primary">
                <i class="fas fa-user-circle me-1"></i>
                {{ Auth::user()->name }}
            </span>
        </div>
    </div>

    @if(isset($message))
        <!-- No Children Message -->
        <div class="alert alert-warning d-flex align-items-center" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <div>{{ $message }}</div>
        </div>
    @else
        <!-- Children Summary Cards -->
        <div class="row">
            @foreach($children as $child)
                @php
                    $childSummary = $summary[$child->id] ?? [
                        'total_tagihan' => 0,
                        'total_bayar' => 0,
                        'sisa_tagihan' => 0
                    ];
                    $persentaseBayar = $childSummary['total_tagihan'] > 0
                        ? ($childSummary['total_bayar'] / $childSummary['total_tagihan']) * 100
                        : 0;
                @endphp
                <div class="col-md-6 col-xl-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <div class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="fas fa-user-graduate"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h5 class="mb-0">{{ $child->nama_lengkap }}</h5>
                                        <small class="text-muted">
                                            {{ $child->kelas->nama_kelas ?? 'Belum ada kelas' }}
                                        </small>
                                    </div>
                                </div>
                                @if($childSummary['sisa_tagihan'] <= 0)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Lunas
                                    </span>
                                @elseif($persentaseBayar >= 50)
                                    <span class="badge bg-warning">Sebagian</span>
                                @else
                                    <span class="badge bg-danger">Belum Lunas</span>
                                @endif
                            </div>

                            <div class="mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-building me-1"></i>{{ $child->cabang->nama_cabang ?? '-' }}
                                </small>
                            </div>

                            <hr class="my-3">

                            <!-- Financial Summary -->
                            <div class="mb-3">
                                <div class="row g-2 mb-3">
                                    <div class="col-4 text-center">
                                        <div class="border rounded p-2">
                                            <small class="d-block text-muted mb-1">Total</small>
                                            <strong class="d-block text-primary small">
                                                {{ number_format($childSummary['total_tagihan'] / 1000, 0) }}K
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="border rounded p-2">
                                            <small class="d-block text-muted mb-1">Dibayar</small>
                                            <strong class="d-block text-success small">
                                                {{ number_format($childSummary['total_bayar'] / 1000, 0) }}K
                                            </strong>
                                        </div>
                                    </div>
                                    <div class="col-4 text-center">
                                        <div class="border rounded p-2">
                                            <small class="d-block text-muted mb-1">Sisa</small>
                                            <strong class="d-block {{ $childSummary['sisa_tagihan'] > 0 ? 'text-danger' : 'text-success' }} small">
                                                {{ number_format($childSummary['sisa_tagihan'] / 1000, 0) }}K
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted">Progress Pembayaran</small>
                                        <small class="fw-bold {{ $persentaseBayar >= 100 ? 'text-success' : ($persentaseBayar >= 50 ? 'text-warning' : 'text-danger') }}">
                                            {{ number_format($persentaseBayar, 0) }}%
                                        </small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar {{ $persentaseBayar >= 100 ? 'bg-success' : ($persentaseBayar >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                             role="progressbar"
                                             style="width: {{ min($persentaseBayar, 100) }}%"
                                             aria-valuenow="{{ $persentaseBayar }}"
                                             aria-valuemin="0"
                                             aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('orang-tua.tagihan.anak', $child->id) }}" class="btn btn-primary btn-sm w-100">
                                        <i class="fas fa-wallet me-1"></i>
                                        <span class="d-none d-sm-inline">Tagihan</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('orang-tua.rapor.anak', $child->id) }}" class="btn btn-outline-secondary btn-sm w-100">
                                        <i class="fas fa-file-alt me-1"></i>
                                        <span class="d-none d-sm-inline">Rapor</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('orang-tua.presensi.ajukan-izin', $child->id) }}" class="btn btn-warning btn-sm w-100">
                                        <i class="fas fa-notes-medical me-1"></i>
                                        <span class="d-none d-sm-inline">Ajukan Izin</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('orang-tua.presensi.riwayat-izin', $child->id) }}" class="btn btn-outline-info btn-sm w-100">
                                        <i class="fas fa-history me-1"></i>
                                        <span class="d-none d-sm-inline">Riwayat Izin</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Overall Summary -->
        @if($children->count() > 1)
            @php
                $totalSemuaTagihan = collect($summary)->sum('total_tagihan');
                $totalSemuaBayar = collect($summary)->sum('total_bayar');
                $totalSemuaSisa = collect($summary)->sum('sisa_tagihan');
                $totalPersentase = $totalSemuaTagihan > 0 ? ($totalSemuaBayar / $totalSemuaTagihan) * 100 : 0;
            @endphp
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-primary"></i>
                        Ringkasan Keseluruhan ({{ $children->count() }} Anak)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-primary">
                                        <i class="fas fa-receipt"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Total Semua Tagihan</small>
                                    <h5 class="mb-0 fw-bold">Rp {{ number_format($totalSemuaTagihan, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded bg-success">
                                        <i class="fas fa-check-circle"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Total Sudah Dibayar</small>
                                    <h5 class="mb-0 text-success fw-bold">Rp {{ number_format($totalSemuaBayar, 0, ',', '.') }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center p-3 bg-light rounded">
                                <div class="avatar flex-shrink-0 me-3">
                                    <span class="avatar-initial rounded {{ $totalSemuaSisa > 0 ? 'bg-danger' : 'bg-success' }}">
                                        <i class="fas fa-wallet"></i>
                                    </span>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Total Sisa Tagihan</small>
                                    <h5 class="mb-0 {{ $totalSemuaSisa > 0 ? 'text-danger' : 'text-success' }} fw-bold">
                                        Rp {{ number_format($totalSemuaSisa, 0, ',', '.') }}
                                    </h5>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Overall Progress -->
                    <div class="mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="fw-bold">Progress Keseluruhan</span>
                            <span class="badge {{ $totalPersentase >= 100 ? 'bg-success' : ($totalPersentase >= 50 ? 'bg-warning' : 'bg-danger') }}">
                                {{ number_format($totalPersentase, 1) }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar {{ $totalPersentase >= 100 ? 'bg-success' : ($totalPersentase >= 50 ? 'bg-warning' : 'bg-danger') }}"
                                 role="progressbar"
                                 style="width: {{ min($totalPersentase, 100) }}%"
                                 aria-valuenow="{{ $totalPersentase }}"
                                 aria-valuemin="0"
                                 aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
@endsection
