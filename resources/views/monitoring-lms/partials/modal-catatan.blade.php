<div class="modal fade" id="modalKirimCatatan" tabindex="-1" aria-labelledby="modalKirimCatatanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 12px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.15);">
            <form id="formKirimCatatan" method="POST" action="{{ route($baseRoute . '.catatan') }}">
                @csrf
                <input type="hidden" name="konten_type" id="catatanKontenType" value="">
                <input type="hidden" name="konten_id" id="catatanKontenId" value="">

                <div class="modal-header" style="border-bottom: 1px solid var(--border-color); padding: 18px 22px;">
                    <div>
                        <h5 class="modal-title fw-bold mb-1" id="modalKirimCatatanLabel" style="color: var(--text-main);">
                            <i class="fas fa-comment-dots me-2" style="color: var(--primary-color);"></i>Kirim Catatan untuk Guru
                        </h5>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;" id="catatanKontenLabel">Konten</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" style="padding: 22px;">
                    <div class="alert alert-info" style="border: none; background: rgba(67, 97, 238, 0.06); color: #2b4162; border-radius: 10px; font-size: 0.82rem;">
                        <i class="fas fa-info-circle me-2"></i>
                        Catatan akan dikirim sebagai notifikasi langsung kepada guru yang membuat konten ini.
                    </div>

                    <label for="isi_catatan" class="form-label fw-semibold" style="color: var(--text-main);">Isi Catatan / Revisi</label>
                    <textarea name="isi_catatan" id="isi_catatan" rows="6"
                        class="form-control"
                        placeholder="Tuliskan masukan, koreksi, atau instruksi revisi untuk guru..."
                        required minlength="5" maxlength="5000"
                        style="border-radius: 10px; border: 1px solid var(--border-color); padding: 12px 14px; font-size: 0.88rem; resize: vertical;"></textarea>
                    <div class="d-flex justify-content-between mt-2">
                        <small class="text-muted">Min. 5 karakter</small>
                        <small class="text-muted"><span id="charCount">0</span>/5000</small>
                    </div>
                </div>

                <div class="modal-footer" style="border-top: 1px solid var(--border-color); padding: 14px 22px; gap: 8px;">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 8px; padding: 9px 18px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary" id="btnKirimCatatan" style="border-radius: 8px; padding: 9px 18px;">
                        <i class="fas fa-paper-plane me-2"></i>Kirim Catatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const modal = document.getElementById('modalKirimCatatan');
    const form = document.getElementById('formKirimCatatan');
    const textarea = document.getElementById('isi_catatan');
    const charCount = document.getElementById('charCount');
    const btnKirim = document.getElementById('btnKirimCatatan');

    if (!modal) return;

    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    document.querySelectorAll('[data-monitoring-catatan]').forEach(btn => {
        btn.addEventListener('click', function() {
            document.getElementById('catatanKontenType').value = this.dataset.kontenType;
            document.getElementById('catatanKontenId').value = this.dataset.kontenId;
            document.getElementById('catatanKontenLabel').textContent =
                (this.dataset.kontenLabel || 'Konten') + ': ' + (this.dataset.kontenJudul || '-');
            textarea.value = '';
            charCount.textContent = '0';
            new bootstrap.Modal(modal).show();
        });
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        btnKirim.disabled = true;
        btnKirim.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengirim...';

        fetch(form.action, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: new FormData(form),
        })
        .then(r => r.json().then(data => ({ ok: r.ok, data })))
        .then(({ ok, data }) => {
            if (!ok) throw new Error(data.message || 'Gagal mengirim catatan');
            bootstrap.Modal.getInstance(modal).hide();
            showFlash('success', data.message || 'Catatan berhasil dikirim.');
        })
        .catch(err => {
            showFlash('danger', err.message || 'Terjadi kesalahan.');
        })
        .finally(() => {
            btnKirim.disabled = false;
            btnKirim.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Kirim Catatan';
        });
    });

    function showFlash(type, message) {
        const flash = document.createElement('div');
        flash.className = `alert alert-${type} position-fixed`;
        flash.style.cssText = 'top: 80px; right: 20px; z-index: 2000; min-width: 280px; border-radius: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.15);';
        flash.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}`;
        document.body.appendChild(flash);
        setTimeout(() => flash.remove(), 3500);
    }
})();
</script>
@endpush
