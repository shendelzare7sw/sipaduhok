<!-- Confirm Modal Component -->
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" id="confirmModalHeader">
                <h5 class="modal-title" id="confirmModalTitle">Konfirmasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="confirmModalIcon" class="text-center mb-3">
                    <!-- Icon will be inserted here -->
                </div>
                <p id="confirmModalMessage" class="text-center mb-0">Apakah Anda yakin?</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x me-1"></i> Batal
                </button>
                <button type="button" class="btn btn-primary" id="confirmModalBtn">
                    <i class="bx bx-check me-1"></i> Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>
</div>
