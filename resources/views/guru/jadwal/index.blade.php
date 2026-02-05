@extends('layouts.sneat')

@section('title', 'Jadwal Mengajar')
@section('page-title', 'Jadwal Mengajar')
@section('page-subtitle', 'Jadwal mengajar mingguan Anda')

@section('sidebar-menu')
    @include('guru.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex align-items-center justify-content-between py-3 mb-4">
        <a href="{{ route('guru.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
        <h4 class="fw-bold mb-0">Jadwal Mengajar</h4>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Jadwal Mengajar Saya</h5>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    @if($jadwal->isEmpty())
                        <div class="alert alert-info">Belum ada jadwal mengajar yang ditentukan.</div>
                    @else
                        <div class="row">
                            @foreach($jadwal as $hari => $items)
                                <div class="col-md-6 mb-4">
                                    <div class="card border border-primary h-100">
                                        <div class="card-header bg-primary text-white py-2">
                                            <h6 class="mb-0 text-white"><i class="fas fa-calendar-day me-2"></i>{{ $hari }}</h6>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="table-responsive">
                                                <table class="table table-striped table-hover mb-0">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Jam</th>
                                                            <th>Kelas</th>
                                                            <th>Mapel</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($items as $item)
                                                            <tr>
                                                                <td style="white-space: nowrap;">
                                                                    {{ \Carbon\Carbon::parse($item->jam_mulai)->format('H:i') }} - 
                                                                    {{ \Carbon\Carbon::parse($item->jam_selesai)->format('H:i') }}
                                                                </td>
                                                                <td>
                                                                    @foreach($item->kelas as $kelas)
                                                                        <span class="badge bg-label-info">{{ $kelas->nama_kelas }}</span>
                                                                    @endforeach
                                                                </td>
                                                                <td>{{ $item->mataPelajaran->nama_mapel ?? '-' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
