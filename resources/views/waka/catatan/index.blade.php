@extends('layouts.sneat')

@section('title', 'Daftar Catatan')
@section('page-title', 'Manajemen Catatan')
@section('page-subtitle', 'Kirim catatan atau teguran kepada tenaga pendidik dan siswa')

@section('sidebar-menu')
    @include('waka.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        @media (max-width: 576px) {
            .card-header.d-flex {
                flex-direction: column;
                gap: 10px;
                align-items: flex-start !important;
            }
            .card-header .btn { width: 100%; }
            .card-body .d-flex.justify-content-between.align-items-start {
                flex-direction: column;
                gap: 6px;
            }
            .card-body .d-flex.gap-3 {
                flex-wrap: wrap;
                gap: 6px !important;
            }
            .card-title { font-size: 15px; }
        }
    </style>
@endsection

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note text-warning me-2"></i>
                        Riwayat Catatan Terkirim
                    </h5>
                    <a href="{{ route('waka.catatan.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i> Kirim Catatan Baru
                    </a>
                </div>
                <div class="card-body">
                    @forelse($catatan as $item)
                        <div
                            class="card mb-3 border-start border-{{ $item->prioritas == 'mendesak' ? 'danger' : ($item->prioritas == 'penting' ? 'warning' : 'primary') }} border-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="card-title mb-1">{{ $item->judul }}</h5>
                                    <span
                                        class="badge bg-{{ $item->prioritas == 'mendesak' ? 'danger' : ($item->prioritas == 'penting' ? 'warning' : 'info') }}">
                                        {{ ucfirst($item->prioritas) }}
                                    </span>
                                </div>

                                <p class="card-text text-muted mb-2">{{ Str::limit($item->isi_catatan, 150) }}</p>

                                <div class="d-flex gap-3 text-muted small">
                                    <span>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ \Carbon\Carbon::parse($item->tanggal_kirim)->format('d M Y, H:i') }}
                                    </span>
                                    <span>
                                        <i
                                            class="fas fa-{{ $item->tipe_penerima == 'semua' ? 'users' : ($item->tipe_penerima == 'role' ? 'user-tag' : 'user') }} me-1"></i>
                                        @if($item->tipe_penerima == 'semua')
                                            Semua Pengguna
                                        @elseif($item->tipe_penerima == 'role')
                                            {{ ucwords(str_replace('_', ' ', $item->role_penerima)) }}
                                        @else
                                            Individu
                                        @endif
                                    </span>
                                </div>

                                <div class="mt-2 d-flex gap-2">
                                    <a href="{{ route('waka.catatan.show', $item->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Lihat Detail
                                    </a>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDeleteCatatan({{ $item->id }}, '{{ addslashes($item->judul) }}')"
                                        title="Hapus dari riwayat">
                                        <i class="fas fa-trash me-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-inbox fa-3x mb-3"></i>
                            <p>Belum ada catatan yang dikirim</p>
                            <a href="{{ route('waka.catatan.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-1"></i> Kirim Catatan Pertama
                            </a>
                        </div>
                    @endforelse

                    @if($catatan->hasPages())
                        <div class="mt-3">
                            {{ $catatan->links() }}
                        </div>
                    @endif
                </div>
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
        form.action = '/waka/catatan/' + id;
        modal.hide();
        form.submit();
    };
}
</script>
@endsection