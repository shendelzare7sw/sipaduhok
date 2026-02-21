@extends('layouts.sneat')

@section('title', 'Admin - Pengaturan KKM')
@section('title', 'Pengaturan KKM')
@section('page-title', 'Pengaturan KKM')

@section('sidebar-menu')
    @if(auth()->user()->isWakilKepalaSekolah())
        @include('waka.partials.sneat-sidebar-menu')
    @elseif(auth()->user()->isAdmin())
        @include('admin.partials.sneat-sidebar-menu')
    @endif
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <!-- Header removed -->

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            @php
                $routePrefix = request()->routeIs('waka.*') ? 'waka.promotion' : 'admin.akademik.promotion';
            @endphp
            <h5 class="mb-0">Daftar Mata Pelajaran & KKM</h5>
            <form action="{{ route($routePrefix . '.kkm.index') }}" method="GET" class="d-flex">
                <select name="jenjang" class="form-select me-2" onchange="this.form.submit()">
                    <option value="PAUD" {{ $jenjang == 'PAUD' ? 'selected' : '' }}>PAUD</option>
                    <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                    <option value="SMK" {{ $jenjang == 'SMK' ? 'selected' : '' }}>SMK</option>
                </select>
            </form>
        </div>
        <div class="card-body">
            <form action="{{ route($routePrefix . '.kkm.store') }}" method="POST">
                @csrf
                <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                <input type="hidden" name="jenjang" value="{{ $jenjang }}">
                
                <div class="table-responsive text-nowrap">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Mata Pelajaran</th>
                                <th>Jenjang</th>
                                <th>KKM Saat Ini</th>
                                <th>Set KKM Baru</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mapelList as $mapel)
                            <tr>
                                <td>{{ $mapel->nama_mapel }}</td>
                                <td>{{ $mapel->jenjang }}</td>
                                <td>
                                    <span class="badge bg-label-primary">
                                        {{ $existingKKM[$mapel->id] ?? 70 }}
                                    </span>
                                </td>
                                <td>
                                    <input type="number" name="kkm[{{ $mapel->id }}]" 
                                           class="form-control" style="width: 100px;" 
                                           value="{{ $existingKKM[$mapel->id] ?? 70 }}" 
                                           min="0" max="100" required>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data mata pelajaran untuk jenjang ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan KKM</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
