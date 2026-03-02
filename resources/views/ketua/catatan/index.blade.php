@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim dan kelola catatan instruksi untuk pengguna')

@section('sidebar-menu')
    @include('ketua.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        /* === STYLE STAT CARD VIBRANT (KONSISTEN & DIPERBAIKI) === */
        .stat-card {
            padding: 24px;
            border-radius: 12px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            height: 100%;
            color: white !important;
            /* Paksa teks putih agar kontras */
            border: none;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-content {
            position: relative;
            z-index: 2;
        }

        .stat-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.9;
            margin-bottom: 8px;
            color: white !important;
        }

        .stat-number {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 4px;
            line-height: 1;
            color: white !important;
        }

        .stat-desc {
            font-size: 13px;
            opacity: 0.8;
            color: white !important;
        }

        .stat-icon-bg {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 60px;
            opacity: 0.15;
            z-index: 1;
            color: white !important;
        }

        /* Perbaikan Sintaks Gradien */
        .bg-grad-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        /* Typo pada bg-grad-green sebelumnya telah diperbaiki di bawah ini */
        .bg-grad-green {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .bg-grad-orange {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }

        .bg-grad-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        }

        /* === CATATAN ITEM IMPROVEMENT === */
        .catatan-item {
            background: white;
            border: 1px solid #e3e6f0;
            border-left: 5px solid #165fac;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 16px;
            transition: all 0.3s ease;
            position: relative;
        }

        .catatan-item:hover {
            transform: translateX(10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        }

        /* Border dinamis berdasarkan prioritas */
        .border-biasa {
            border-left-color: #4e73df;
        }

        .border-penting {
            border-left-color: #f6c23e;
        }

        .border-mendesak {
            border-left-color: #e74a3b;
        }

        .catatan-title {
            font-size: 18px;
            font-weight: 700;
            color: #4e73df;
        }

        .catatan-meta {
            font-size: 12px;
            color: #858796;
            display: flex;
            gap: 15px;
            align-items: center;
        }

        /* Badges Custom */
        .badge-pill-custom {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .badge-semua {
            background: #eef2ff;
            color: #4338ca;
            border: 1px solid #c7d2fe;
        }

        .badge-role {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
        }

        .badge-individu {
            background: #fff1f2;
            color: #be123c;
            border: 1px solid #fecdd3;
        }

        .read-box {
            background: #f8f9fc;
            padding: 6px 12px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        /* Mobile Responsive */
        @media (max-width: 576px) {
            .stat-card { padding: 16px; }
            .stat-number { font-size: 22px; }
            .stat-title { font-size: 10px; }
            .stat-icon-bg { font-size: 40px; right: 10px; }

            .catatan-item { padding: 14px; }
            .catatan-item .d-flex.justify-content-between.align-items-start {
                flex-direction: column;
                gap: 8px;
            }
            .catatan-title { font-size: 15px; }
            .catatan-meta { flex-wrap: wrap; gap: 8px; font-size: 11px; }

            .badge-pill-custom {
                font-size: 9px;
                padding: 3px 10px;
                white-space: nowrap;
                display: inline-block;
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .catatan-item .d-flex.justify-content-between.align-items-center {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start !important;
            }

            .read-box { font-size: 11px; padding: 5px 10px; }

            .card-header.d-flex { flex-direction: column; gap: 10px; align-items: flex-start !important; }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid px-0" style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">

        {{-- Alert Success --}}
        {{-- Alert Success --}}

        {{-- STATS GRID --}}

        {{-- STATS GRID --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card bg-grad-blue">
                    <div class="stat-content">
                        <div class="stat-title">Total Catatan</div>
                        <div class="stat-number">{{ $catatan->total() }}</div>
                        <div class="stat-desc">Memo yang dikirim</div>
                    </div>
                    <div class="stat-icon-bg"><i class="fas fa-paper-plane"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                {{-- Kartu ini yang diperbaiki backgroundnya --}}
                <div class="stat-card bg-grad-green">
                    <div class="stat-content">
                        <div class="stat-title">Publik (Semua)</div>
                        <div class="stat-number">{{ $catatan->where('tipe_penerima', 'semua')->count() }}</div>
                        <div class="stat-desc">Instruksi Massal</div>
                    </div>
                    <div class="stat-icon-bg"><i class="fas fa-globe"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card bg-grad-orange">
                    <div class="stat-content">
                        <div class="stat-title">Total Pembaca</div>
                        <div class="stat-number">{{ $catatan->sum(fn($c) => $c->totalPembaca()) }}</div>
                        <div class="stat-desc">Akumulasi View</div>
                    </div>
                    <div class="stat-icon-bg"><i class="fas fa-eye"></i></div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="stat-card bg-grad-purple">
                    <div class="stat-content">
                        <div class="stat-title">Mendesak</div>
                        <div class="stat-number">{{ $catatan->where('prioritas', 'mendesak')->count() }}</div>
                        <div class="stat-desc">Butuh Respon Cepat</div>
                    </div>
                    <div class="stat-icon-bg"><i class="fas fa-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>

        {{-- HEADER CARD --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-history mr-2"></i>Riwayat Catatan Instruksi
                </h6>
                <a href="{{ route('ketua.catatan.create') }}" class="btn btn-primary btn-sm shadow-sm font-weight-bold">
                    <i class="fas fa-plus-circle mr-1"></i> Buat Catatan Baru
                </a>
            </div>
            <div class="card-body">
                @if($catatan->count() > 0)
                    <div class="catatan-list">
                        @foreach($catatan as $cat)
                            <div class="catatan-item border-{{ $cat->prioritas }}">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <h5 class="catatan-title mb-1">{{ $cat->judul }}</h5>
                                        <div class="catatan-meta">
                                            <span><i class="far fa-clock mr-1 text-info"></i>
                                                {{ $cat->tanggal_kirim->format('d M Y, H:i') }}</span>
                                            <span>
                                                <i class="fas fa-layer-group mr-1 text-warning"></i>
                                                <span class="text-uppercase font-weight-bold"
                                                    style="letter-spacing: 0.5px;">{{ $cat->prioritas }}</span>
                                            </span>
                                        </div>
                                    </div>
                                    <div>
                                        @if($cat->tipe_penerima === 'semua')
                                            <span class="badge-pill-custom badge-semua"><i class="fas fa-broadcast-tower mr-1"></i>
                                                SEMUA PENGGUNA</span>
                                        @elseif($cat->tipe_penerima === 'role')
                                            <span class="badge-pill-custom badge-role"><i class="fas fa-users mr-1"></i> ROLE:
                                                {{ strtoupper(str_replace('_', ' ', $cat->role_penerima)) }}</span>
                                        @else
                                            <span class="badge-pill-custom badge-individu"><i class="fas fa-user mr-1"></i> INDIVIDU:
                                                {{ $cat->penerima->name ?? 'User' }}</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="text-gray-800 mb-4" style="line-height: 1.6;">
                                    {{ \Illuminate\Support\Str::limit($cat->isi_catatan, 250) }}
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="read-box">
                                        <i class="fas fa-chart-line text-success"></i>
                                        <span>Telah dibaca oleh <strong class="text-success">{{ $cat->totalPembaca() }}</strong>
                                            pengguna</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('ketua.catatan.show', $cat->id) }}"
                                            class="btn btn-light border btn-sm font-weight-bold text-primary shadow-sm">
                                            Lihat Rincian <i class="fas fa-arrow-right ml-1"></i>
                                        </a>
                                        <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            onclick="confirmDeleteCatatan({{ $cat->id }}, '{{ addslashes($cat->judul) }}')"
                                            title="Hapus dari riwayat">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($catatan->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $catatan->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <div class="mb-4">
                            <i class="fas fa-pen-nib fa-4x text-gray-200"></i>
                        </div>
                        <h5 class="text-gray-500 font-weight-bold">Belum ada catatan instruksi yang dikirim</h5>
                        <p class="small text-muted">Instruksi Anda sangat penting untuk kelancaran operasional institusi.</p>
                        <a href="{{ route('ketua.catatan.create') }}" class="btn btn-primary mt-3 px-4">
                            Mulai Kirim Instruksi
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

{{-- Delete Confirm Modal --}}
<div class="modal fade" id="deleteCatatanModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Hapus Catatan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-2">
                <p class="mb-1">Hapus catatan "<strong id="deleteCatatanJudul"></strong>" dari riwayat?</p>
                <p class="text-muted small mb-0"><i class="fas fa-info-circle me-1"></i>Catatan yang sudah terkirim ke penerima tidak akan terpengaruh.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmDeleteCatatanBtn">
                    <i class="fas fa-trash me-1"></i> Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<form id="deleteCatatanForm" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('scripts')
<script>
function confirmDeleteCatatan(id, judul) {
    document.getElementById('deleteCatatanJudul').textContent = judul;
    const modal = new bootstrap.Modal(document.getElementById('deleteCatatanModal'));
    modal.show();
    document.getElementById('confirmDeleteCatatanBtn').onclick = function() {
        const form = document.getElementById('deleteCatatanForm');
        form.action = '/ketua/catatan/' + id;
        modal.hide();
        form.submit();
    };
}
</script>
@endsection