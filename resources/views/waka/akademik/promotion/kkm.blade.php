@extends('layouts.sneat')

@section('title', 'Pengaturan KKM')
@section('page-title', 'Pengaturan KKM')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/waka/akademik/promotion/kkm.css'])
@endsection

@section('content')
@php
    $routePrefix = 'waka.kenaikan-kelas';
    $tahunLabel = $tahun->nama_tahun_ajaran ?? $tahun->nama ?? $tahun->tahun_ajaran ?? '-';
    $totalMapel = $mapelList->count();
    $configuredCount = collect($existingKKM ?? [])->filter(function ($value) {
        return $value !== null;
    })->count();
    $averageKkm = collect($existingKKM ?? [])->filter(function ($value) {
        return $value !== null;
    })->avg();
@endphp

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="promotion-page">
        <div class="page-panel mb-4">
            <div class="panel-main">
                <span class="panel-kicker">Akademik</span>
                <h4 class="panel-title">Pengaturan KKM</h4>
                <p class="panel-subtitle mb-0">Atur nilai minimum kelulusan setiap mata pelajaran untuk tahun ajaran aktif.</p>
            </div>
            <form action="{{ route($routePrefix . '.kkm.index') }}" method="GET" class="panel-action">
                <label class="form-label mb-1">Jenjang</label>
                <select name="jenjang" class="form-select" data-auto-submit>
                    <option value="PAUD" {{ $jenjang == 'PAUD' ? 'selected' : '' }}>PAUD</option>
                    <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
            </form>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon primary"><i class="fas fa-calendar-alt"></i></div>
                    <span>Tahun Ajaran</span>
                    <strong>{{ $tahunLabel }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon info"><i class="fas fa-layer-group"></i></div>
                    <span>Jenjang</span>
                    <strong>{{ $jenjang }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon success"><i class="fas fa-book-open"></i></div>
                    <span>Mata Pelajaran</span>
                    <strong>{{ $totalMapel }}</strong>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="summary-card">
                    <div class="summary-icon warning"><i class="fas fa-bullseye"></i></div>
                    <span>Rata-rata KKM</span>
                    <strong>{{ is_null($averageKkm) ? 70 : number_format($averageKkm, 0) }}</strong>
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="content-card-header">
                <div>
                    <h5 class="mb-1">Daftar Mata Pelajaran</h5>
                    <p class="text-muted mb-0">Sudah diatur: {{ $configuredCount }} dari {{ $totalMapel }} mata pelajaran.</p>
                </div>
            </div>

            <div class="content-card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route($routePrefix . '.kkm.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                <input type="hidden" name="jenjang" value="{{ $jenjang }}">

                @if($mapelList->isEmpty())
                    <div class="empty-state">
                        <i class="bx bx-book-open"></i>
                        <h6>Belum ada mata pelajaran</h6>
                        <p>Jenjang <strong>{{ $jenjang }}</strong> belum memiliki mata pelajaran untuk diatur KKM-nya.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-clean align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Mata Pelajaran</th>
                                    <th>Jenjang</th>
                                    <th class="text-center">KKM Saat Ini</th>
                                    <th class="text-md-end">Set KKM Baru</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mapelList as $i => $mapel)
                                <tr>
                                    <td data-label="No" class="text-muted">{{ $i + 1 }}</td>
                                    <td data-label="Mata Pelajaran">
                                        <div class="subject-name">{{ $mapel->nama_mapel }}</div>
                                    </td>
                                    <td data-label="Jenjang">
                                        <span class="soft-badge neutral">{{ $mapel->jenjang }}</span>
                                    </td>
                                    <td data-label="KKM Saat Ini" class="text-center">
                                        <span class="kkm-badge">
                                            {{ $existingKKM[$mapel->id] ?? 70 }}
                                        </span>
                                    </td>
                                    <td data-label="Set KKM Baru" class="text-md-end">
                                        <input type="number"
                                               name="kkm[{{ $mapel->id }}]"
                                               class="form-control form-control-sm kkm-input @error('kkm.'.$mapel->id) is-invalid @enderror"
                                               value="{{ $existingKKM[$mapel->id] ?? 70 }}"
                                               min="0" max="100" required>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="action-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="bx bx-save me-1"></i> Simpan Pengaturan KKM
                        </button>
                    </div>
                @endif
            </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
    @vite(['resources/js/waka/akademik/promotion/kkm.js'])
@endsection
