@extends('layouts.sneat')

@section('title', 'Request Download Rapor')
@section('page-title', 'Request Download Rapor')
@section('page-subtitle', 'Kelola permintaan download rapor dari orang tua')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    <div class="alert alert-light border border-primary border-opacity-25 shadow-sm mb-4">
        <div class="small text-muted">
            <i class="fas fa-info-circle text-primary me-1"></i>
            Orang tua dapat mengajukan permintaan download rapor dari halaman detail rapor anak.
            Setelah Anda <strong>setujui</strong>, link download akan aktif selama <strong>24 jam</strong>.
            Setelah expired, orang tua perlu mengajukan ulang.
            @if($kelas)
                | Kelas: <strong>{{ $kelas->nama_kelas }}</strong>
            @endif
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-primary"><i class="fas fa-download me-2"></i>Daftar Permintaan Download</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" width="50">No</th>
                            <th>Orang Tua</th>
                            <th>Siswa</th>
                            <th>Rapor</th>
                            <th>Alasan</th>
                            <th class="text-center">Tanggal</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" width="160">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $index => $req)
                            <tr>
                                <td class="text-center align-middle fw-bold">{{ $requests->firstItem() + $index }}</td>
                                <td class="align-middle">
                                    <div class="fw-bold">{{ $req->user->name ?? '-' }}</div>
                                </td>
                                <td class="align-middle">
                                    <div class="fw-bold">{{ $req->siswa->nama_lengkap ?? '-' }}</div>
                                    <small class="text-muted">{{ $req->siswa->kelas->nama_kelas ?? '-' }}</small>
                                </td>
                                <td class="align-middle small">
                                    {{ $req->rapor ? ucwords(str_replace('_', ' ', $req->rapor->jenis_rapor)) : '-' }}
                                    <br><small class="text-muted">{{ $req->rapor->semester ?? '' }}</small>
                                </td>
                                <td class="align-middle small">{{ Str::limit($req->alasan, 50) ?: '-' }}</td>
                                <td class="text-center align-middle small">{{ $req->tanggal_request ? $req->tanggal_request->format('d/m/Y H:i') : '-' }}</td>
                                <td class="text-center align-middle">
                                    @if($req->status === 'menunggu')
                                        <span class="badge bg-warning text-white"><i class="fas fa-clock"></i> Menunggu</span>
                                    @elseif($req->status === 'disetujui')
                                        <span class="badge bg-success"><i class="fas fa-check"></i> Disetujui</span>
                                        @if($req->isExpired())
                                            <div class="small text-muted">Link expired</div>
                                        @else
                                            <div class="small text-success">Aktif</div>
                                        @endif
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-times"></i> Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center align-middle">
                                    @if($req->status === 'menunggu')
                                        <button type="button" class="btn btn-success btn-sm" title="Setujui"
                                                onclick="showDownloadAction('{{ route('wali.rapor.request-download.approve', $req->id) }}', 'Setujui permintaan download dari {{ $req->user->name ?? "" }}?', 'Setujui', 'btn-success', 'bg-success')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button type="button" class="btn btn-danger btn-sm" title="Tolak"
                                                onclick="showDownloadAction('{{ route('wali.rapor.request-download.reject', $req->id) }}', 'Tolak permintaan download dari {{ $req->user->name ?? "" }}?', 'Tolak', 'btn-danger', 'bg-danger')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @else
                                        <span class="text-muted small">{{ $req->tanggal_keputusan ? $req->tanggal_keputusan->format('d/m/Y') : '-' }}</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted fst-italic">Belum ada permintaan download rapor</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-4 py-3 bg-light border-top">
                <div class="d-flex justify-content-center">
                    {{ $requests->links() }}
                </div>
            </div>
        </div>
    </div>

</div>
</div>

{{-- Modal Konfirmasi Aksi Download --}}
<div class="modal fade" id="downloadActionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <form id="downloadActionForm" method="POST">
                @csrf
                <div class="modal-header" id="downloadModalHeader">
                    <h5 class="modal-title fw-bold text-white" id="downloadModalTitle">
                        <i class="fas fa-question-circle me-2"></i>Konfirmasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4">
                    <div class="text-center">
                        <i class="fas fa-question-circle fa-3x mb-3" id="downloadModalIcon"></i>
                        <p class="fw-bold mb-0" id="downloadModalMessage">Apakah Anda yakin?</p>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Batal</button>
                    <button type="submit" class="btn fw-bold" id="downloadSubmitBtn"><i class="fas fa-check me-1"></i> Konfirmasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showDownloadAction(action, message, btnLabel, btnClass, headerClass) {
    document.getElementById('downloadActionForm').action = action;
    document.getElementById('downloadModalMessage').textContent = message;
    document.getElementById('downloadModalTitle').innerHTML = '<i class="fas fa-question-circle me-2"></i>' + btnLabel;
    document.getElementById('downloadModalHeader').className = 'modal-header text-white ' + headerClass;
    document.getElementById('downloadModalIcon').className = 'fas fa-question-circle fa-3x mb-3 ' + (headerClass.includes('success') ? 'text-success' : 'text-danger');

    const submitBtn = document.getElementById('downloadSubmitBtn');
    submitBtn.className = 'btn fw-bold ' + btnClass;
    submitBtn.innerHTML = '<i class="fas fa-check me-1"></i> ' + btnLabel;

    new bootstrap.Modal(document.getElementById('downloadActionModal')).show();
}
</script>
@endsection
