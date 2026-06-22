<div class="modal fade" id="deleteTenagaPendidikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content delete-modal-content">
            <button type="button" class="delete-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body delete-modal-body">
                <div class="delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <h4 class="delete-modal-title">Hapus Tenaga Pendidik?</h4>

                <p class="delete-modal-text">
                    Apakah Anda yakin ingin menghapus data tenaga pendidik ini?
                </p>

                <div class="delete-summary">
                    <div class="delete-summary-title">
                        <i class="fas fa-chalkboard-teacher"></i>
                        <span id="deleteTenagaPendidikName"></span>
                    </div>
                    <div class="delete-summary-row">
                        <small>
                            <i class="fas fa-tag"></i>
                            <span id="deleteTenagaPendidikRole"></span>
                        </small>
                        <small>
                            <i class="fas fa-envelope"></i>
                            <span id="deleteTenagaPendidikEmail"></span>
                        </small>
                    </div>
                </div>

                <div class="delete-info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait termasuk akun login.
                    </p>
                </div>

                <div class="delete-modal-actions">
                    <button type="button" class="btn btn-cancel-delete" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <form id="deleteTenagaPendidikForm" method="POST" class="d-inline" style="flex:1;display:flex;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-confirm-delete" style="width:100%;">
                            <i class="fas fa-trash"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteSiswaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content delete-modal-content">
            <button type="button" class="delete-close-btn" data-bs-dismiss="modal" aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body delete-modal-body">
                <div class="delete-modal-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <h4 class="delete-modal-title">Hapus Siswa?</h4>

                <p class="delete-modal-text">
                    Apakah Anda yakin ingin menghapus data siswa ini?
                </p>

                <div class="delete-summary">
                    <div class="delete-summary-title">
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

                <div class="delete-info-box">
                    <i class="fas fa-info-circle"></i>
                    <p>
                        Tindakan ini tidak dapat dibatalkan dan akan menghapus semua data terkait termasuk akun login siswa.
                    </p>
                </div>

                <div class="delete-modal-actions">
                    <button type="button" class="btn btn-cancel-delete" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <form id="deleteSiswaForm" method="POST" class="d-inline" style="flex:1;display:flex;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-confirm-delete" style="width:100%;">
                            <i class="fas fa-trash"></i> Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
