@extends('layouts.sneat')

@section('title', 'Admin - Validasi Dispensasi')
@section('page-title', 'Validasi Dispensasi')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Keuangan / Promotion /</span> Validasi Dispensasi</h4>

    <div class="card">
        <div class="card-header d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="mb-0 fs-6">Kandidat Dispensasi <span class="d-none d-md-inline">(Akademik Tuntas, Keuangan Belum Lunas)</span></h5>
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm" id="btnBulkDispensasi" style="display: none !important;" data-bs-toggle="modal" data-bs-target="#modalBulkDispensasi">
                    <i class='bx bx-send'></i> <span class="d-none d-sm-inline">Ajukan</span> Terpilih (<span id="selectedCount">0</span>)
                </button>
                <a href="{{ route('admin.keuangan.promotion.validation.history') }}" class="btn btn-primary btn-sm">
                    <i class='bx bx-history'></i> <span class="d-none d-sm-inline">Riwayat</span>
                </a>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle mb-0">
                <thead>
                    <tr>
                        <th width="40" class="ps-3"><input type="checkbox" id="select-all" class="form-check-input" onclick="toggleSelectAll()"></th>
                        <th>Nama Siswa</th>
                        <th class="d-none d-md-table-cell">Kelas</th>
                        <th class="d-none d-sm-table-cell">Status Akademik</th>
                        <th>Tunggakan</th>
                        <th class="d-none d-lg-table-cell">Status Pengajuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($candidates as $candidate)
                    <tr>
                        <td class="ps-3">
                            @if(!$candidate['pending_request'])
                                <input type="checkbox" class="form-check-input siswa-checkbox" value="{{ $candidate['siswa']->id }}" onchange="updateSelectedCount()">
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $candidate['siswa']->nama_lengkap }}</div>
                            <div class="d-md-none small text-muted">{{ $candidate['siswa']->kelas->nama_kelas ?? '-' }}</div>
                        </td>
                        <td class="d-none d-md-table-cell">{{ $candidate['siswa']->kelas->nama_kelas ?? '-' }}</td>
                        <td class="d-none d-sm-table-cell">
                            <span class="badge bg-success">Tuntas ({{ $candidate['academic']['percentage'] }}%)</span>
                        </td>
                        <td>
                            <span class="text-danger fw-semibold">Rp {{ number_format($candidate['financial']['unpaid_amount'], 0, ',', '.') }}</span>
                        </td>
                        <td class="d-none d-lg-table-cell">
                            @if($candidate['pending_request'])
                                <span class="badge bg-warning">{{ $candidate['pending_request']->status }}</span>
                            @else
                                <span class="badge bg-secondary">Belum Diajukan</span>
                            @endif
                        </td>
                        <td>
                            @if(!$candidate['pending_request'])
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalDispensasi{{ $candidate['siswa']->id }}">
                                    <span class="d-none d-sm-inline">Ajukan</span> Dispensasi
                                </button>

                                <!-- Modal Individual -->
                                <div class="modal fade" id="modalDispensasi{{ $candidate['siswa']->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.keuangan.promotion.validation.store') }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ajukan Izin Khusus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <input type="hidden" name="siswa_id" value="{{ $candidate['siswa']->id }}">
                                                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                                                    <p class="text-wrap">Mengajukan izin naik kelas untuk siswa <strong>{{ $candidate['siswa']->nama_lengkap }}</strong> meskipun masih memiliki tunggakan.</p>
                                                    <div class="mb-3">
                                                        <label class="form-label">Alasan Pengajuan</label>
                                                        <textarea name="alasan" class="form-control" rows="3" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Kirim ke Ketua PKBM</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <small class="text-muted">Menunggu persetujuan</small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Tidak ada siswa yang memerlukan dispensasi saat ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Bulk Dispensasi -->
<div class="modal fade" id="modalBulkDispensasi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <form action="{{ route('admin.keuangan.promotion.validation.bulk-store') }}" method="POST" id="bulkDispensasiForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bx bx-send me-2"></i>Ajukan Dispensasi Terpilih</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="tahun_ajaran_id" value="{{ $tahun->id }}">
                    <div id="bulk-siswa-ids"></div>

                    <div class="alert alert-info d-flex align-items-center gap-2">
                        <i class="bx bx-info-circle fs-5"></i>
                        <div>Anda akan mengajukan dispensasi naik kelas untuk <strong><span id="modalSelectedCount">0</span> siswa</strong> sekaligus.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Alasan Pengajuan <span class="text-muted fw-normal">(berlaku untuk semua)</span></label>
                        <textarea name="alasan" class="form-control" rows="4" required placeholder="Contoh: Orang tua berjanji melunasi bulan depan, kondisi ekonomi kurang mampu, dll."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bx bx-send me-1"></i>Kirim ke Ketua PKBM</button>
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
    document.querySelectorAll('.siswa-checkbox').forEach(cb => cb.checked = isChecked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.siswa-checkbox:checked');
    const all = document.querySelectorAll('.siswa-checkbox');
    const count = checked.length;

    document.getElementById('selectedCount').textContent = count;
    const btn = document.getElementById('btnBulkDispensasi');
    btn.style.display = count > 0 ? 'inline-flex' : 'none';

    document.getElementById('select-all').indeterminate = count > 0 && count < all.length;
    document.getElementById('select-all').checked = all.length > 0 && count === all.length;
}

document.getElementById('modalBulkDispensasi').addEventListener('show.bs.modal', function () {
    const container = document.getElementById('bulk-siswa-ids');
    container.innerHTML = '';
    const checked = document.querySelectorAll('.siswa-checkbox:checked');
    document.getElementById('modalSelectedCount').textContent = checked.length;
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'siswa_ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
});
</script>
@endsection
