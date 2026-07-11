@extends('layouts.sneat')

@section('title', 'Admin - Riwayat Dispensasi')
@section('page-title', 'Riwayat Dispensasi')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    @vite(['resources/css/admin/keuangan/promotion/history.css'])
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-primary fw-bold">
                        <i class="bi bi-clock-history me-2"></i>Riwayat Pengajuan Dispensasi
                    </h5>
                    <a href="{{ route('admin.keuangan.kenaikan-kelas.validation.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>Kembali
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.keuangan.kenaikan-kelas.validation.history') }}" method="GET" class="mb-4">
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

                    <form id="bulkDeleteForm" method="POST" action="{{ route('admin.keuangan.kenaikan-kelas.validation.history.bulk-delete') }}">
                        @csrf
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="confirmBulkDelete()" id="btnBulkDelete" disabled>
                                <i class="bx bx-trash me-1"></i> Hapus Terpilih
                            </button>
                            
                            <div class="form-check d-md-none">
                                <input class="form-check-input" type="checkbox" id="checkAllMobile">
                                <label class="form-check-label" for="checkAllMobile">
                                    Pilih Semua
                                </label>
                            </div>
                        </div>

                        <div class="table-responsive text-nowrap">
                            <table class="table table-hover table-card-mobile align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 40px;" class="mobile-hide"><input class="form-check-input" type="checkbox" id="checkAll"></th>
                                        <th>Siswa</th>
                                    <th>Tanggal Pengajuan</th>
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
                                    <td class="mobile-hide text-center">
                                        <input class="form-check-input history-checkbox" type="checkbox" name="ids[]" value="{{ $item->id }}">
                                    </td>
                                    <td class="mobile-card-head">
                                        <div class="d-flex justify-content-between align-items-center gap-2 history-head-row">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="form-check form-check-inline m-0 mobile-only-cell">
                                                    <input class="form-check-input history-checkbox" type="checkbox" name="ids[]" value="{{ $item->id }}" style="transform: scale(1.2);">
                                                </div>
                                                <span class="text-wrap text-break lh-sm fw-semibold history-student-name">{{ $item->nama_siswa }}</span>
                                            </div>
                                            <span class="mobile-only-cell flex-shrink-0 ms-auto">
                                                @if($item->status == 'DISETUJUI')
                                                    <span class="text-success"><i class="bi bi-check-circle-fill"></i></span>
                                                @elseif($item->status == 'DITOLAK')
                                                    <span class="text-danger"><i class="bi bi-x-circle-fill"></i></span>
                                                @else
                                                    <span class="text-secondary"><i class="bi bi-clock-fill"></i></span>
                                                @endif
                                            </span>
                                        </div>
                                    </td>
                                    <td data-label="Tgl Pengajuan" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->locale('id')->translatedFormat('d F Y') }}</div>
                                    </td>
                                    <td data-label="Kelas" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ $item->nama_kelas }}</div>
                                    </td>
                                    <td data-label="Status" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">
                                            @if($item->status == 'DISETUJUI')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                                            @elseif($item->status == 'DITOLAK')
                                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $item->status }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Diajukan Oleh" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ $item->pengaju }}</div>
                                    </td>
                                    <td data-label="Disetujui Oleh" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">{{ $item->penyetuju ?? '-' }}</div>
                                    </td>
                                    <td data-label="Tgl Keputusan" class="force-d-flex-mobile">
                                        <div class="mobile-text-end">
                                            @if($item->tanggal_persetujuan)
                                                {{ \Carbon\Carbon::parse($item->tanggal_persetujuan)->locale('id')->translatedFormat('d F Y') }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Catatan" class="force-d-flex-mobile">
                                        <div class="mobile-text-end text-wrap text-break lh-sm">{{ $item->catatan_ketua ?? '-' }}</div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada riwayat pengajuan.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkAll = document.getElementById('checkAll');
        const checkAllMobile = document.getElementById('checkAllMobile');
        const checkboxes = document.querySelectorAll('.history-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');

        function updateButtonState() {
            const checkedCount = document.querySelectorAll('.history-checkbox:checked').length;
            if(btnBulkDelete) {
                btnBulkDelete.disabled = checkedCount === 0;
            }
            const allChecked = checkedCount === checkboxes.length && checkboxes.length > 0;
            if (checkAll) {
                checkAll.checked = allChecked;
            }
            if (checkAllMobile) {
                checkAllMobile.checked = allChecked;
            }
        }

        function toggleAll(checked) {
            checkboxes.forEach(cb => cb.checked = checked);
            updateButtonState();
        }

        if (checkAll) {
            checkAll.addEventListener('change', function() {
                toggleAll(this.checked);
            });
        }
        
        if (checkAllMobile) {
            checkAllMobile.addEventListener('change', function() {
                toggleAll(this.checked);
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateButtonState);
        });
    });

    function confirmBulkDelete() {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Riwayat yang dipilih akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#8592a3',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('bulkDeleteForm').submit();
            }
        });
    }
</script>
@endsection
