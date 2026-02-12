@extends('layouts.sneat')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Persetujuan Dispensasi
                    </h5>
                    <a href="{{ route('ketua.promotion.approval.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('ketua.promotion.approval.history') }}" method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Cari Siswa</label>
                                <input type="text" name="q" class="form-control" placeholder="Nama atau NIS..." value="{{ $filters['q'] ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Cabang</label>
                                <select name="cabang" class="form-select">
                                    <option value="">Semua Cabang</option>
                                    @foreach($cabangs as $cabang)
                                        <option value="{{ $cabang->id }}" {{ ($filters['cabang'] ?? '') == $cabang->id ? 'selected' : '' }}>
                                            {{ $cabang->nama_cabang }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Kelas</label>
                                <select name="kelas" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ ($filters['kelas'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="DISETUJUI" {{ ($filters['status'] ?? '') == 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="DITOLAK" {{ ($filters['status'] ?? '') == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100"><i class="bx bx-search"></i></button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal Pengajuan</th>
                                    <th>Siswa</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th>Diajukan Oleh</th>
                                    <th>Disetujui/Ditolak Oleh</th>
                                    <th>Tanggal Keputusan</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->translatedFormat('d F Y') }}</td>
                                    <td class="fw-bold">{{ $item->nama_siswa }}</td>
                                    <td>{{ $item->nama_kelas }}</td>
                                    <td>
                                        @if($item->status == 'DISETUJUI')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                        @elseif($item->status == 'DITOLAK')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $item->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $item->pengaju }}</td>
                                    <td>{{ $item->penyetuju ?? '-' }}</td>
                                    <td>
                                        @if($item->tanggal_persetujuan)
                                            {{ \Carbon\Carbon::parse($item->tanggal_persetujuan)->translatedFormat('d F Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $item->catatan_ketua ?? '-' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada riwayat persetujuan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
