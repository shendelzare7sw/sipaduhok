@extends('layouts.sneat')

@section('title', 'Riwayat Presensi - ' . $siswa->nama_lengkap)
@section('page-title', 'Riwayat Presensi')

@section('sidebar-menu')
    @include('orang-tua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .attendance-history-page {
        --parent-blue: #465fe8;
        --parent-ink: #25324a;
        --parent-muted: #6b7890;
        --parent-line: #dde4f0;
        --parent-soft: #f6f8fc;
    }

    .history-heading,
    .history-filter,
    .history-table-card {
        background: #fff;
        border: 1px solid var(--parent-line);
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(37, 50, 74, .06);
    }

    .history-heading {
        padding: 22px 24px;
    }

    .history-title {
        color: var(--parent-ink);
        font-size: 1.35rem;
        font-weight: 800;
        margin-bottom: 4px;
    }

    .history-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .history-actions .btn,
    .filter-actions .btn {
        align-items: center;
        display: inline-flex;
        justify-content: center;
        min-height: 40px;
        white-space: normal;
    }

    .history-filter {
        padding: 18px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .summary-card {
        background: #fff;
        border: 1px solid var(--parent-line);
        border-radius: 8px;
        min-height: 96px;
        padding: 16px;
        position: relative;
        overflow: hidden;
    }

    .summary-card::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: var(--summary-color);
    }

    .summary-label {
        color: var(--parent-muted);
        font-size: .72rem;
        font-weight: 800;
        margin-bottom: 8px;
        text-transform: uppercase;
    }

    .summary-number {
        color: var(--parent-ink);
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1;
    }

    .summary-hadir { --summary-color: #10b981; }
    .summary-sakit { --summary-color: #f59e0b; }
    .summary-izin { --summary-color: #3b82f6; }
    .summary-alpha { --summary-color: #ef4444; }

    .table-history thead th {
        background: var(--parent-soft);
        color: var(--parent-muted);
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        border-bottom: 1px solid var(--parent-line);
    }

    .table-history td {
        color: var(--parent-ink);
        vertical-align: middle;
    }

    @media (max-width: 991.98px) {
        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 767.98px) {
        .history-heading {
            padding: 20px 16px;
        }

        .history-actions,
        .filter-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
            width: 100%;
        }

        .history-actions .btn,
        .filter-actions .btn {
            width: 100%;
        }

        .table-history thead {
            display: none;
        }

        .table-history tbody tr {
            display: block;
            border-bottom: 1px solid var(--parent-line);
            padding: 14px 16px;
        }

        .table-history tbody td {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 8px 0 !important;
            text-align: right !important;
        }

        .table-history tbody td::before {
            content: attr(data-label);
            color: var(--parent-muted);
            font-size: 11px;
            font-weight: 800;
            text-align: left;
            text-transform: uppercase;
        }

        .table-history tbody td[data-label="Keterangan"] {
            display: block;
            text-align: left !important;
        }

        .table-history tbody td[data-label="Keterangan"]::before {
            display: block;
            margin-bottom: 6px;
        }
    }

    @media (max-width: 575.98px) {
        .summary-grid,
        .history-actions,
        .filter-actions {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y attendance-history-page">
    <div class="history-heading d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <div class="history-title">Riwayat Presensi</div>
            <p class="text-muted mb-0">
                <i class="fas fa-user-graduate me-1"></i>{{ $siswa->nama_lengkap }}
                <span class="mx-2">|</span>
                <i class="fas fa-school me-1"></i>{{ $siswa->kelas->nama_kelas ?? 'Belum ada kelas' }}
            </p>
        </div>
        <div class="history-actions">
            <a href="{{ route('orang-tua.presensi.ajukan-izin', $siswa->id) }}" class="btn btn-warning text-white fw-bold">
                <i class="fas fa-file-medical me-1"></i>Ajukan Izin / Sakit
            </a>
            <a href="{{ route('orang-tua.presensi.anak', $siswa->id) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    <div class="summary-grid">
        <div class="summary-card summary-hadir">
            <div class="summary-label">Hadir</div>
            <div class="summary-number">{{ $rekap['hadir'] }}</div>
        </div>
        <div class="summary-card summary-sakit">
            <div class="summary-label">Sakit</div>
            <div class="summary-number">{{ $rekap['sakit'] }}</div>
        </div>
        <div class="summary-card summary-izin">
            <div class="summary-label">Izin</div>
            <div class="summary-number">{{ $rekap['izin'] }}</div>
        </div>
        <div class="summary-card summary-alpha">
            <div class="summary-label">Alpha</div>
            <div class="summary-number">{{ $rekap['alpha'] }}</div>
        </div>
    </div>

    <div class="history-filter mb-4">
        <form action="{{ route('orang-tua.presensi.riwayat-presensi', $siswa->id) }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold small">Dari Tanggal</label>
                <input type="date" name="tanggal_mulai" value="{{ request('tanggal_mulai') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Sampai Tanggal</label>
                <input type="date" name="tanggal_akhir" value="{{ request('tanggal_akhir') }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="hadir" {{ request('status') === 'hadir' ? 'selected' : '' }}>Hadir</option>
                    <option value="sakit" {{ request('status') === 'sakit' ? 'selected' : '' }}>Sakit</option>
                    <option value="izin" {{ request('status') === 'izin' ? 'selected' : '' }}>Izin</option>
                    <option value="alpha" {{ request('status') === 'alpha' ? 'selected' : '' }}>Alpha</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="filter-actions d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('orang-tua.presensi.riwayat-presensi', $siswa->id) }}" class="btn btn-light border flex-fill">
                        <i class="fas fa-sync-alt me-1"></i>Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="history-table-card">
        <div class="table-responsive">
            <table class="table table-hover table-history mb-0">
                <thead>
                    <tr>
                        <th class="ps-4">Tanggal</th>
                        <th>Hari</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Validasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayat as $item)
                        @php
                            $statusMap = [
                                'hadir' => ['class' => 'success', 'label' => 'Hadir'],
                                'sakit' => ['class' => 'warning', 'label' => 'Sakit'],
                                'izin' => ['class' => 'info', 'label' => 'Izin'],
                                'alpha' => ['class' => 'danger', 'label' => 'Alpha'],
                            ];
                            $status = $statusMap[$item->status] ?? ['class' => 'secondary', 'label' => ucfirst($item->status)];
                            $buktiPath = $item->bukti_file;
                            $buktiUrl = $buktiPath ? asset('storage/' . $buktiPath) : null;
                            $buktiExtension = $buktiPath ? strtolower(pathinfo($buktiPath, PATHINFO_EXTENSION)) : null;
                            $isBuktiImage = in_array($buktiExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                            $isBuktiPdf = $buktiExtension === 'pdf';
                        @endphp
                        <tr>
                            <td data-label="Tanggal" class="ps-4 fw-bold">
                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('j F Y') }}
                            </td>
                            <td data-label="Hari" class="text-muted">
                                {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('l') }}
                            </td>
                            <td data-label="Status" class="text-center">
                                <span class="badge bg-{{ $status['class'] }} rounded-pill px-3 py-2">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td data-label="Validasi" class="text-center">
                                @if($item->status_validasi === 'disetujui')
                                    <span class="badge bg-success rounded-pill">Disetujui</span>
                                @elseif($item->status_validasi === 'ditolak')
                                    <span class="badge bg-danger rounded-pill">Ditolak</span>
                                @elseif($item->status_validasi === 'pending')
                                    <span class="badge bg-warning rounded-pill">Menunggu</span>
                                @else
                                    <span class="badge bg-label-secondary rounded-pill">-</span>
                                @endif
                            </td>
                            <td data-label="Keterangan">
                                <div class="small text-muted">{{ $item->keterangan ?: 'Tidak ada catatan' }}</div>
                                @if($buktiPath)
                                    <button type="button"
                                            class="btn btn-outline-primary btn-sm mt-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#buktiPresensiModal{{ $item->id }}">
                                        <i class="fas fa-paperclip me-1"></i>Lihat Bukti
                                    </button>
                                @endif
                            </td>
                        </tr>

                        @if($buktiPath)
                            <div class="modal fade" id="buktiPresensiModal{{ $item->id }}" tabindex="-1" aria-labelledby="buktiPresensiModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="buktiPresensiModalLabel{{ $item->id }}">
                                                <i class="fas fa-paperclip me-2"></i>Lampiran Bukti
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                                        </div>
                                        <div class="modal-body p-0">
                                            @if($isBuktiImage)
                                                <div class="text-center p-3">
                                                    <img src="{{ $buktiUrl }}"
                                                         alt="Lampiran bukti presensi {{ $siswa->nama_lengkap }}"
                                                         class="img-fluid rounded"
                                                         style="max-height: 75vh;">
                                                </div>
                                            @elseif($isBuktiPdf)
                                                <iframe src="{{ $buktiUrl }}"
                                                        title="Lampiran bukti presensi {{ $siswa->nama_lengkap }}"
                                                        style="width: 100%; height: 75vh; border: 0;"></iframe>
                                            @else
                                                <div class="text-center p-5">
                                                    <i class="fas fa-file fa-3x text-muted mb-3"></i>
                                                    <p class="text-muted mb-0">Format lampiran tidak dapat dipreview.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-calendar-times fa-2x d-block mb-2"></i>
                                Tidak ada data presensi ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="card-footer bg-light border-top">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
