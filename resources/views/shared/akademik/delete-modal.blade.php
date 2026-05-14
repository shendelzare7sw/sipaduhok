<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ $modalTitle ?? 'Hapus Data' }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                <h6 class="fw-bold mb-2">Apakah Anda yakin ingin menghapus data ini?</h6>
                <p class="text-muted mb-0" id="{{ $itemLabelId ?? 'deleteAkademikName' }}"></p>
                <small class="text-danger d-block mt-2">Tindakan ini tidak dapat dibatalkan.</small>
            </div>
            <div class="modal-footer border-0 bg-light">
                <button type="button" class="ak-btn secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>Batal
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ak-btn danger">
                        <i class="fas fa-trash"></i>Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
