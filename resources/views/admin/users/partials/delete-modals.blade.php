<div class="modal fade" id="deleteTenagaPendidikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data tenaga pendidik:</p>
                <div class="delete-summary">
                    <div class="delete-summary-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span id="deleteTenagaPendidikName"></span>
                    </div>
                    <small class="delete-summary-meta">
                        <span id="deleteTenagaPendidikRole"></span> &bull;
                        <span id="deleteTenagaPendidikEmail"></span>
                    </small>
                </div>
                <p class="danger-note">
                    <i class="fas fa-info-circle"></i>
                    <small class="text-muted">
                        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait termasuk akun login.
                    </small>
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <form id="deleteTenagaPendidikForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4 shadow">
                        <i class="fas fa-trash"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Konfirmasi Hapus
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data siswa:</p>
                <div class="delete-summary">
                    <div class="delete-summary-title delete-summary-title-spaced">
                        <i class="fas fa-user-graduate"></i>
                        <span id="deleteSiswaName"></span>
                    </div>
                    <div class="delete-summary-row">
                        <small>
                            <i class="fas fa-id-card"></i> NIS: <span id="deleteSiswaNis"></span>
                        </small>
                        <small>
                            <i class="fas fa-hashtag"></i> NISN: <span id="deleteSiswaNisn"></span>
                        </small>
                        <small id="deleteSiswaKelasContainer">
                            <i class="fas fa-door-open"></i> <span id="deleteSiswaKelas"></span>
                        </small>
                    </div>
                </div>
                <p class="danger-note">
                    <i class="fas fa-info-circle"></i>
                    <small class="text-muted">
                        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait termasuk akun login siswa.
                    </small>
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i>
                    Batal
                </button>
                <form id="deleteSiswaForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm px-4 shadow">
                        <i class="fas fa-trash"></i>
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
