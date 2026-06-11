/* Keuangan carryover behavior extracted from the former shared loader. */
const sweetAlertUrl = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
const sweetAlertCssUrl = 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css';

let sweetAlertPromise = null;

const ensureSweetAlert = () => {
    if (window.Swal?.fire) {
        return Promise.resolve(window.Swal);
    }

    if (sweetAlertPromise) {
        return sweetAlertPromise;
    }

    const hasCss = document.querySelector(`link[href="${sweetAlertCssUrl}"]`);
    if (!hasCss) {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = sweetAlertCssUrl;
        link.dataset.carryoverSwalCss = 'true';
        document.head.appendChild(link);
    }

    sweetAlertPromise = new Promise((resolve, reject) => {
        const existingScript = document.querySelector('script[src*="sweetalert2"]');

        if (existingScript) {
            existingScript.addEventListener('load', () => resolve(window.Swal), { once: true });
            existingScript.addEventListener('error', reject, { once: true });
            return;
        }

        const script = document.createElement('script');
        script.src = sweetAlertUrl;
        script.dataset.carryoverSwal = 'true';
        script.onload = () => resolve(window.Swal);
        script.onerror = reject;
        document.body.appendChild(script);
    });

    return sweetAlertPromise;
};

const escapeHtml = (value) => {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(value ?? ''));
    return div.innerHTML;
};

const fmtRp = (value) => `Rp ${Number(value || 0).toLocaleString('id-ID')}`;

const readCarryoverConfig = () => {
    const configEl = document.getElementById('carryoverConfig');

    if (!configEl) {
        return {
            csrfToken: '',
            detailData: {},
            previewUrl: '',
        };
    }

    try {
        return {
            csrfToken: configEl.dataset.csrfToken || '',
            detailData: JSON.parse(configEl.dataset.detail || '{}'),
            previewUrl: configEl.dataset.previewUrl || '',
        };
    } catch (error) {
        console.error('Gagal membaca konfigurasi carryover.', error);
        return {
            csrfToken: configEl.dataset.csrfToken || '',
            detailData: {},
            previewUrl: configEl.dataset.previewUrl || '',
        };
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('carryover-form');
    const config = readCarryoverConfig();
    const checkAll = document.getElementById('check-all');
    const checks = Array.from(document.querySelectorAll('.siswa-check'));
    const btnPreview = document.getElementById('btn-preview');
    const btnExecute = document.getElementById('btn-execute');
    const selectedCount = document.getElementById('selected-count');

    document.querySelectorAll('[data-carryover-auto-submit]').forEach((field) => {
        field.addEventListener('change', () => field.form?.submit());
    });

    if (!form) {
        return;
    }

    const getSelectedIds = () => checks.filter((check) => check.checked).map((check) => check.value);

    const refresh = () => {
        const selected = getSelectedIds();

        if (selectedCount) {
            selectedCount.textContent = selected.length;
        }

        const hasSelection = selected.length > 0;
        if (btnPreview) {
            btnPreview.disabled = !hasSelection;
        }

        if (btnExecute) {
            btnExecute.disabled = !hasSelection;
        }
    };

    checkAll?.addEventListener('change', () => {
        checks.forEach((check) => {
            check.checked = checkAll.checked;
        });
        refresh();
    });

    checks.forEach((check) => check.addEventListener('change', refresh));

    document.querySelectorAll('.detail-link').forEach((link) => {
        link.addEventListener('click', async (event) => {
            event.preventDefault();

            const sid = link.getAttribute('data-detail-id');
            const data = config.detailData[sid];

            if (!data) {
                return;
            }

            const rowsHtml = (data.tagihan || []).map((tagihan) => `
                <tr>
                    <td><span class="carryover-ta-label">${escapeHtml(tagihan.ta)}</span></td>
                    <td>
                        ${escapeHtml(tagihan.jenis)}
                        <br>
                        <small class="text-muted">${escapeHtml(tagihan.keterangan)}</small>
                    </td>
                    <td class="carryover-number-cell">${fmtRp(tagihan.sisa)}</td>
                </tr>
            `).join('');

            try {
                const Swal = await ensureSweetAlert();

                Swal.fire({
                    title: data.nama,
                    html: `
                        <div class="carryover-dialog-intro">
                            <strong>${escapeHtml(data.jumlah)} tagihan tertunggak</strong> dari TA ${escapeHtml((data.tas || []).join(', '))}
                        </div>
                        <table class="detail-table mt-2">
                            <thead>
                                <tr>
                                    <th>TA</th>
                                    <th>Tagihan</th>
                                    <th class="carryover-number-cell">Sisa</th>
                                </tr>
                            </thead>
                            <tbody>${rowsHtml}</tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="2" class="carryover-detail-total-label">Total</td>
                                    <td class="carryover-detail-total-value">${fmtRp(data.total)}</td>
                                </tr>
                            </tfoot>
                        </table>
                    `,
                    width: 640,
                    showCancelButton: false,
                    confirmButtonText: 'Tutup',
                    confirmButtonColor: '#6c757d',
                });
            } catch (error) {
                console.error(error);
                window.alert(`${data.nama}\n${data.jumlah} tagihan tertunggak. Total ${fmtRp(data.total)}.`);
            }
        });
    });

    const confirmExecute = async () => {
        const ids = getSelectedIds();

        if (!ids.length) {
            return;
        }

        try {
            const Swal = await ensureSweetAlert();
            const result = await Swal.fire({
                title: 'Yakin Eksekusi?',
                html: `
                    <p class="carryover-confirm-text">
                        Tindakan ini akan membuat <strong>tagihan baru</strong> di TA aktif untuk
                        <strong>${ids.length} siswa</strong>. Tagihan asal di TA lama akan ditandai
                        "Sudah Dialihkan" (tidak dihapus).
                    </p>
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Alihkan',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
            });

            if (result.isConfirmed) {
                form.submit();
            }
        } catch (error) {
            console.error(error);
            if (window.confirm(`Alihkan tunggakan untuk ${ids.length} siswa?`)) {
                form.submit();
            }
        }
    };

    btnPreview?.addEventListener('click', async () => {
        const ids = getSelectedIds();

        if (!ids.length) {
            return;
        }

        try {
            const Swal = await ensureSweetAlert();
            Swal.fire({
                title: 'Memuat pratinjau...',
                html: '<div class="spinner-border text-primary"></div>',
                showConfirmButton: false,
                allowOutsideClick: false,
            });

            const formData = new FormData();
            formData.append('_token', config.csrfToken);
            ids.forEach((id) => formData.append('siswa_ids[]', id));

            const response = await fetch(config.previewUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    Accept: 'application/json',
                },
            });
            const data = await response.json();

            if (!response.ok) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.error || 'Gagal memuat pratinjau.' });
                return;
            }

            const rows = (data.items || []).map((item) => `
                <tr>
                    <td>${escapeHtml(item.siswa_nama)}</td>
                    <td>
                        <small class="text-muted">${escapeHtml(item.asal_tahun_ajaran)} &rarr;</small>
                        <br>
                        ${escapeHtml(item.keterangan_baru)}
                    </td>
                    <td class="carryover-number-cell">${fmtRp(item.jumlah)}</td>
                </tr>
            `).join('');

            const result = await Swal.fire({
                title: 'Pratinjau Carryover',
                html: `
                    <div class="alert alert-info text-start carryover-preview-alert">
                        <strong>${escapeHtml(data.total_siswa)} siswa</strong> &middot;
                        <strong>${escapeHtml(data.total_tagihan)} tagihan baru</strong> akan dibuat di TA
                        <strong>${escapeHtml(data.tujuan_tahun_ajaran_nama)}</strong>.<br>
                        Total: <strong>${fmtRp(data.grand_total)}</strong>
                    </div>
                    <table class="detail-table">
                        <thead>
                            <tr>
                                <th>Siswa</th>
                                <th>Tagihan Baru</th>
                                <th class="carryover-number-cell">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>${rows}</tbody>
                    </table>
                `,
                width: 720,
                showCancelButton: true,
                confirmButtonText: 'Lanjut Eksekusi',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#3651d4',
                cancelButtonColor: '#6c757d',
            });

            if (result.isConfirmed) {
                confirmExecute();
            }
        } catch (error) {
            console.error(error);
            window.alert(error.message || 'Gagal memuat pratinjau.');
        }
    });

    btnExecute?.addEventListener('click', confirmExecute);

    refresh();
});
