@extends('layouts.sneat')

@section('title', 'Admin - Pengaturan KKM')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik / Promotion /</span> Pengaturan KKM</h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Mata Pelajaran & KKM</h5>
            <form action="{{ route('admin.akademik.promotion.kkm.index') }}" method="GET" class="d-flex">
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
            <form action="{{ route('admin.akademik.promotion.kkm.store') }}" method="POST">
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
