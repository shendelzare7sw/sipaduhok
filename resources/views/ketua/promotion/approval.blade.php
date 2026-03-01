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
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0 fs-6">Permintaan Izin Khusus (Dispensasi)</h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success btn-sm" id="btnBulkApprove" style="display: none !important;" data-bs-toggle="modal" data-bs-target="#modalBulkApprove">
                    <i class='bx bx-check'></i> <span class="d-none d-sm-inline">Setujui</span> Terpilih (<span id="approveCount">0</span>)
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnBulkReject" style="display: none !important;" data-bs-toggle="modal" data-bs-target="#modalBulkReject">
                    <i class='bx bx-x'></i> <span class="d-none d-sm-inline">Tolak</span> Terpilih (<span id="rejectCount">0</span>)
                </button>
                <a href="{{ route('ketua.promotion.approval.history') }}" class="btn btn-primary btn-sm">
                    <i class='bx bx-history'></i> <span class="d-none d-sm-inline">Riwayat</span>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="select-all" class="form-check-input" onclick="toggleSelectAll()"></th>
                        <th>Siswa</th>
                        <th class="d-none d-md-table-cell">Kelas</th>
                        <th class="d-none d-lg-table-cell">Diajukan Oleh</th>
                        <th class="d-none d-xl-table-cell">Alasan</th>
                        <th class="d-none d-sm-table-cell">Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                    <tr>
                        <td class="ps-3">
                            <input type="checkbox" class="form-check-input req-checkbox" value="{{ $req->id }}" onchange="updateSelectedCount()">
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $req->nama_siswa }}</div>
                            <div class="d-md-none small text-muted">{{ $req->nama_kelas }}</div>
                            <div class="d-lg-none small text-muted">{{ $req->pengaju }}</div>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $req->nama_kelas }}</td>
                        <td class="d-none d-lg-table-cell">{{ $req->pengaju }}</td>
                        <td class="d-none d-xl-table-cell" style="max-width: 220px; white-space: normal;">
                            <small>{{ \Illuminate\Support\Str::limit($req->alasan_pengajuan, 80) }}</small>
                        </td>
                        <td class="d-none d-sm-table-cell">
                            <small>{{ \Carbon\Carbon::parse($req->tanggal_pengajuan)->locale('id')->translatedFormat('d M Y') }}</small>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalApprove{{ $req->id }}" title="Setujui">
                                    <i class="fas fa-check"></i><span class="d-none d-md-inline ms-1">Setujui</span>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#modalReject{{ $req->id }}" title="Tolak">
                                    <i class="fas fa-times"></i><span class="d-none d-md-inline ms-1">Tolak</span>
                                </button>
                            </div>

                            <!-- Modal Approve Individual -->
                            <div class="modal fade" id="modalApprove{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form action="{{ route('ketua.promotion.approval.update', $req->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="approve">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title text-white"><i class="fas fa-check-circle me-2"></i>Konfirmasi Persetujuan</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda akan menyetujui dispensasi naik kelas untuk:</p>
                                                <ul class="mb-3">
                                                    <li><strong>Siswa:</strong> {{ $req->nama_siswa }}</li>
                                                    <li><strong>Kelas:</strong> {{ $req->nama_kelas }}</li>
                                                    <li><strong>Diajukan oleh:</strong> {{ $req->pengaju }}</li>
                                                </ul>
                                                <div class="alert alert-info text-wrap mb-0">
                                                    <i class="bx bx-info-circle me-1"></i>
                                                    Persetujuan ini <strong>TIDAK menghapus tunggakan</strong> siswa. Status tunggakan tetap tercatat, namun siswa diizinkan naik kelas.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Ya, Setujui</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Reject Individual -->
                            <div class="modal fade" id="modalReject{{ $req->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <form action="{{ route('ketua.promotion.approval.update', $req->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="action" value="reject">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title text-white"><i class="fas fa-times-circle me-2"></i>Konfirmasi Penolakan</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Anda akan menolak pengajuan dispensasi untuk siswa <strong>{{ $req->nama_siswa }}</strong>.</p>
                                                <p class="text-muted small mb-0">Siswa tidak akan bisa naik kelas jika tunggakan tidak dilunasi.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i>Ya, Tolak</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada permintaan menunggu persetujuan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bulk Approve -->
<div class="modal fade" id="modalBulkApprove" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('ketua.promotion.approval.bulk-update') }}" method="POST" id="bulkApproveForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="approve">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title text-white"><i class="fas fa-check-circle me-2"></i>Setujui Dispensasi Terpilih</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="bulk-approve-ids"></div>
                    <p>Anda akan menyetujui <strong><span id="modalApproveCount">0</span> pengajuan</strong> dispensasi naik kelas sekaligus.</p>
                    <div class="alert alert-info text-wrap">
                        <i class="bx bx-info-circle me-1"></i>
                        Persetujuan ini <strong>TIDAK menghapus tunggakan</strong> siswa. Status tunggakan tetap tercatat, namun siswa diizinkan naik kelas.
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-check me-1"></i>Ya, Setujui Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Bulk Reject -->
<div class="modal fade" id="modalBulkReject" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('ketua.promotion.approval.bulk-update') }}" method="POST" id="bulkRejectForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="action" value="reject">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white"><i class="fas fa-times-circle me-2"></i>Tolak Dispensasi Terpilih</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="bulk-reject-ids"></div>
                    <p>Anda akan menolak <strong><span id="modalRejectCount">0</span> pengajuan</strong> dispensasi sekaligus.</p>
                    <p class="text-muted small">Siswa tidak akan bisa naik kelas jika tunggakan tidak dilunasi.</p>
                    <div class="mb-0">
                        <label class="form-label">Catatan <span class="text-muted fw-normal">(opsional)</span></label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Tambahkan catatan jika perlu..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-times me-1"></i>Ya, Tolak Semua</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function toggleSelectAll() {
    const isChecked = document.getElementById('select-all').checked;
    document.querySelectorAll('.req-checkbox').forEach(cb => cb.checked = isChecked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.req-checkbox:checked');
    const all = document.querySelectorAll('.req-checkbox');
    const count = checked.length;

    document.getElementById('approveCount').textContent = count;
    document.getElementById('rejectCount').textContent = count;

    const btnApprove = document.getElementById('btnBulkApprove');
    const btnReject = document.getElementById('btnBulkReject');
    btnApprove.style.display = count > 0 ? 'inline-flex' : 'none';
    btnReject.style.display = count > 0 ? 'inline-flex' : 'none';

    document.getElementById('select-all').indeterminate = count > 0 && count < all.length;
    document.getElementById('select-all').checked = all.length > 0 && count === all.length;
}

function injectIds(containerId, countId) {
    const container = document.getElementById(containerId);
    container.innerHTML = '';
    const checked = document.querySelectorAll('.req-checkbox:checked');
    document.getElementById(countId).textContent = checked.length;
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
}

document.getElementById('modalBulkApprove').addEventListener('show.bs.modal', function () {
    injectIds('bulk-approve-ids', 'modalApproveCount');
});

document.getElementById('modalBulkReject').addEventListener('show.bs.modal', function () {
    injectIds('bulk-reject-ids', 'modalRejectCount');
});
</script>
@endsection
