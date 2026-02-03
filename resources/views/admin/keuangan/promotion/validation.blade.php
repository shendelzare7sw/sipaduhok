@extends('layouts.sneat')

@section('title', 'Admin - Validasi Dispensasi')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Keuangan / Promotion /</span> Validasi Dispensasi</h4>



    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Kandidat Dispensasi (Akademik Tuntas, Keuangan Belum Lunas)</h5>
            <a href="{{ route('admin.keuangan.promotion.validation.history') }}" class="btn btn-primary btn-sm">
                <i class='bx bx-history'></i> Riwayat
            </a>
        </div>
        <div class="table-responsive text-nowrap">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nama Siswa</th>
                        <th>Kelas</th>
                        <th>Status Akademik</th>
                        <th>Tunggakan</th>
                        <th>Status Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                    <tr>
                        <td>{{ $candidate['siswa']->nama_lengkap }}</td>
                        <td>{{ $candidate['siswa']->kelas->nama_kelas ?? '-' }}</td>
                        <td>
                            <span class="badge bg-success">Tuntas ({{ $candidate['academic']['percentage'] }}%)</span>
                        </td>
                        <td>
                            <span class="text-danger">Rp {{ number_format($candidate['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                        </td>
                        <td>
                            @if($candidate['pending_request'])
                                <span class="badge bg-warning">{{ $candidate['pending_request']->status }}</span>
                            @else
                                <span class="badge bg-secondary">Belum Diajukan</span>
                            @endif
                        </td>
                        <td>
                            @if(!$candidate['pending_request'])
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDispensasi{{ $candidate['siswa']->id }}">
                                    Ajukan Dispensasi
                                </button>

                                <!-- Modal -->
                                <div class="modal fade" id="modalDispensasi{{ $candidate['siswa']->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <form action="{{ route('admin.keuangan.promotion.validation.store') }}" method="POST">
                                            @csrf
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ajukan Izin Khusus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="siswa_id" value="{{ $candidate['siswa']->id }}">
                                                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                                                    
                                                    <p class="text-wrap" style="word-break: break-word;">Mengajukan izin naik kelas untuk siswa <strong>{{ $candidate['siswa']->nama_lengkap }}</strong> meskipun masih memiliki tunggakan.</p>
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Pengajuan</label>
                                                        <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Kirim ke Ketua PKBM</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <small class="text-muted">Menunggu persetujuan</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada siswa yang memerlukan dispensasi saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
