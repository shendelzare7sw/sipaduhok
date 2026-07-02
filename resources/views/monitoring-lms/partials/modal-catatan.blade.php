@once
    @push('styles')
        @vite(['resources/css/monitoring-lms/modal-catatan.css'])
    @endpush

    @push('scripts')
        @vite(['resources/js/monitoring-lms/modal-catatan.js'])
    @endpush
@endonce

<div class="modal fade" id="modalKirimCatatan" tabindex="-1" aria-labelledby="modalKirimCatatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content modal-catatan-content">
            <form id="formKirimCatatan" method="POST" action="{{ route($baseRoute . '.catatan') }}">
                @csrf
                <input type="hidden" name="konten_type" id="catatanKontenType" value="">
                <input type="hidden" name="konten_id" id="catatanKontenId" value="">

                <div class="modal-header modal-catatan-header align-items-start">
                    <div>
                        <h5 class="modal-title fw-bold mb-1 modal-catatan-title" id="modalKirimCatatanLabel">
                            <i class="fas fa-comment-dots me-2 modal-catatan-title-icon"></i>Kirim Catatan untuk Guru
                        </h5>
                        <p class="text-muted mb-0 modal-catatan-subtitle" id="catatanKontenLabel">Konten</p>
                    </div>
                    <button type="button" class="btn-close modal-catatan-close-btn" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body modal-catatan-body">
                    <div class="alert alert-info modal-catatan-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Catatan akan dikirim sebagai notifikasi langsung kepada guru yang membuat konten ini.
                    </div>

                    <label for="isi_catatan" class="form-label fw-semibold modal-catatan-label">Isi Catatan / Revisi</label>
                    <textarea name="isi_catatan" id="isi_catatan" rows="6"
                        class="form-control modal-catatan-textarea"
                        placeholder="Tuliskan masukan, koreksi, atau instruksi revisi untuk guru..."
                        required minlength="5" maxlength="5000"></textarea>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">Min. 5 karakter</small>
                        <small class="text-muted"><span id="charCount">0</span>/5000</small>
                    </div>
                </div>

                <div class="modal-footer modal-catatan-footer">
                    <button type="button" class="btn btn-light modal-catatan-button" data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary modal-catatan-button" id="btnKirimCatatan">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
