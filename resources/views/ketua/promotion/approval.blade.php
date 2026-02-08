@extends('layouts.sneat')

@section('title', 'Persetujuan Dispensasi Naik Kelas')
@section('page-title', 'Persetujuan Dispensasi')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Ketua PKBM /</span> Approval Dispensasi</h4>



    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Permintaan Izin Khusus (Dispensasi)</h5>
            <a href="{{ route('ketua.promotion.approval.history') }}" class="btn btn-primary btn-sm">
                <i class='bx bx-history'></i> Riwayat
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Siswa</th>
                        <th>Kelas</th>
                        <th>Diajukan Oleh</th>
                        <th>Alasan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($req->tanggal_pengajuan)->format('d M Y') }}</td>
                        <td>{{ $req->nama_siswa }}</td>
                        <td>{{ $req->nama_kelas }}</td>
                        <td>{{ $req->pengaju }}</td>
                        <td style="max-width: 250px; white-space: normal;">{{ $req->alasan_pengajuan }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <!-- Approve Button Trigger -->
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $req->id }}">
                                    Setujui
                                </button>
                                
                                <!-- Reject Button Trigger -->
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject{{ $req->id }}">
                                    Tolak
                                </button>
                            </div>

                            <!-- Modal Approve -->
                            <div class="modal fade" id="modalApprove{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('ketua.promotion.approval.update', $req->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="approve">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title text-white">Konfirmasi Persetujuan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <i class="fas fa-check-circle fa-3x text-success"></i>
                                                </div>
                                                <p>Anda akan menyetujui dispensasi naik kelas untuk:</p>
                                                <ul class="text-start">
                                                    <li><strong>Siswa:</strong> {{ $req->nama_siswa }}</li>
                                                    <li><strong>Kelas:</strong> {{ $req->nama_kelas }}</li>
                                                </ul>
                                                <div class="alert alert-info text-wrap" style="word-break: break-word;">
                                                    <strong>Catatan:</strong> Persetujuan ini TIDAK menghapus tunggakan siswa. Status tunggakan akan tetap tercatat, namun siswa diizinkan untuk naik kelas.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success">Ya, Setujui</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Reject -->
                            <div class="modal fade" id="modalReject{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('ketua.promotion.approval.update', $req->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="action" value="reject">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title text-white">Konfirmasi Penolakan</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="text-center mb-3">
                                                    <i class="fas fa-times-circle fa-3x text-danger"></i>
                                                </div>
                                                <p>Anda akan menolak pengajuan dispensasi untuk siswa <strong>{{ $req->nama_siswa }}</strong>.</p>
                                                <p class="text-muted small">Siswa tidak akan bisa naik kelas jika tunggakan tidak dilunasi.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada permintaan menunggu persetujuan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
