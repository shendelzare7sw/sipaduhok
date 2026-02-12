@extends('layouts.sneat')

@section('title', 'Admin - Pengaturan KKM')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Akademik / Promotion /</span> Pengaturan KKM</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mb-4">
        <h5 class="card-header">Filter Jenjang</h5>
        <div class="card-body">
            <form action="{{ route('admin.akademik.promotion.kkm.index') }}" method="GET" class="d-flex gap-2">
                <select name="jenjang" class="form-select w-auto" onchange="this.form.submit()">
                    <option value="PAUD" {{ $jenjang == 'PAUD' ? 'selected' : '' }}>PAUD (KB/TK)</option>
                    <option value="SD" {{ $jenjang == 'SD' ? 'selected' : '' }}>SD</option>
                    <option value="SMP" {{ $jenjang == 'SMP' ? 'selected' : '' }}>SMP</option>
                    <option value="SMA" {{ $jenjang == 'SMA' ? 'selected' : '' }}>SMA</option>
                </select>
                <noscript><button type="submit" class="btn btn-primary">Filter</button></noscript>
            </form>
        </div>
    </div>

    <form action="{{ route('admin.akademik.promotion.kkm.store') }}" method="POST">
        @csrf
        <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
        <input type="hidden" name="jenjang" value="{{ $jenjang }}">
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Mata Pelajaran (Jenjang {{ $jenjang }}) - Tahun {{ $tahun->tahun_ajaran }}</h5>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
            </div>
            
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelompok</th>
                            <th width="20%">Nilai KKM (0-100)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mapelList as $index => $mapel)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $mapel->nama_pelajaran }}</td>
                            <td>{{ $mapel->kelompok ?? '-' }}</td>
                            <td>
                                <input type="number" 
                                       name="kkm[{{ $mapel->id }}]" 
                                       class="form-control" 
                                       min="0" max="100" 
                                       value="{{ $existingKKM[$mapel->id] ?? 70 }}" 
                                       required>
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
        </div>
    </form>
</div>
@endsection
