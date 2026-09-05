import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

const fireAlert = (options) => Swal.fire(options);

const createMessage = (className, text, iconClass = null) => {
    const paragraph = document.createElement('p');
    paragraph.className = className;

    if (iconClass) {
        const icon = document.createElement('i');
        icon.className = iconClass;
        paragraph.append(icon, document.createTextNode(text));
        return paragraph;
    }

    const emphasis = document.createElement('em');
    emphasis.textContent = text;
    paragraph.appendChild(emphasis);
    return paragraph;
};

const setPreviewLoading = (preview) => {
    preview.replaceChildren(createMessage('text-muted mb-0', 'Memuat tagihan...', 'fas fa-spinner fa-spin me-2'));
};

const renderPreviewTable = (preview, data) => {
    const wrapper = document.createElement('div');
    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');
    const headerRow = document.createElement('tr');

    wrapper.className = 'table-responsive';
    table.className = 'table table-sm table-bordered mb-0';
    thead.className = 'table-light';

    ['Jenis Tagihan', 'Jumlah', 'Status'].forEach((title) => {
        const th = document.createElement('th');
        th.textContent = title;
        headerRow.appendChild(th);
    });

    data.forEach((tagihan) => {
        const row = document.createElement('tr');
        const jenisCell = document.createElement('td');
        const jumlahCell = document.createElement('td');
        const statusCell = document.createElement('td');
        const statusBadge = document.createElement('span');
        const isPaid = tagihan.status === 'sudah_bayar';

        jenisCell.textContent = String(tagihan.jenis_tagihan || '').replace(/_/g, ' ').toUpperCase();
        jumlahCell.textContent = `Rp ${new Intl.NumberFormat('id-ID').format(tagihan.jumlah || 0)}`;
        statusBadge.className = `badge ${isPaid ? 'bg-success' : 'bg-warning'}`;
        statusBadge.textContent = isPaid ? 'Lunas' : 'Belum Bayar';

        statusCell.appendChild(statusBadge);
        row.append(jenisCell, jumlahCell, statusCell);
        tbody.appendChild(row);
    });

    thead.appendChild(headerRow);
    table.append(thead, tbody);
    wrapper.appendChild(table);
    preview.replaceChildren(wrapper);
};

const loadPreview = (sourceSelect, preview) => {
    const siswaId = sourceSelect.value;

    if (!siswaId) {
        preview.replaceChildren(createMessage('text-muted mb-0', 'Pilih siswa sumber untuk melihat preview tagihan'));
        return;
    }

    setPreviewLoading(preview);

    fetch(sourceSelect.dataset.previewUrl.replace(':siswa', siswaId))
        .then((response) => response.json())
        .then((data) => {
            if (!data.length) {
                preview.replaceChildren(createMessage('text-danger mb-0', 'Siswa ini belum memiliki tagihan'));
                return;
            }

            renderPreviewTable(preview, data);
        })
        .catch(() => {
            preview.replaceChildren(createMessage('text-danger mb-0', 'Gagal memuat tagihan'));
        });
};

const visibleTargetCheckboxes = () => Array.from(document.querySelectorAll('.target-siswa-item'))
    .filter((item) => !item.classList.contains('hidden'))
    .map((item) => item.querySelector('.target-siswa-checkbox'))
    .filter(Boolean);

const selectVisibleTargets = (checked) => {
    visibleTargetCheckboxes().forEach((checkbox) => {
        checkbox.checked = checked;
    });
};

const confirmDuplicate = () => {
    const form = document.getElementById('duplicateForm');
    const sourceSelect = document.getElementById('source_siswa_id');
    const checkedCount = document.querySelectorAll('.target-siswa-checkbox:checked').length;

    if (!form || !sourceSelect) {
        return;
    }

    if (!sourceSelect.value) {
        fireAlert({
            icon: 'warning',
            title: 'Pilih Siswa Sumber',
            text: 'Silakan pilih siswa sumber terlebih dahulu.',
            confirmButtonColor: '#696cff',
        });
        return;
    }

    if (checkedCount === 0) {
        fireAlert({
            icon: 'warning',
            title: 'Belum ada target',
            text: 'Silakan pilih minimal 1 siswa target.',
            confirmButtonColor: '#696cff',
        });
        return;
    }

    const sourceName = sourceSelect.options[sourceSelect.selectedIndex].text;

    fireAlert({
        title: 'Konfirmasi Duplikasi',
        html: `Anda akan menyalin tagihan dari "<strong>${sourceName}</strong>"<br> ke <strong>${checkedCount}</strong> siswa target.<br><br><small class="text-muted">Pastikan data sudah benar sebelum melanjutkan.</small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#696cff',
        cancelButtonColor: '#8592a3',
        confirmButtonText: 'Ya, Duplikasi',
        cancelButtonText: 'Batal',
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    const sourceSelect = document.getElementById('source_siswa_id');
    const filterKelasSelect = document.getElementById('filter_kelas');
    const preview = document.getElementById('preview_tagihan');

    if (sourceSelect && preview) {
        sourceSelect.addEventListener('change', () => loadPreview(sourceSelect, preview));
    }

    filterKelasSelect?.addEventListener('change', () => {
        const kelasId = filterKelasSelect.value;

        document.querySelectorAll('.target-siswa-item').forEach((item) => {
            const shouldHide = Boolean(kelasId) && item.dataset.kelasId !== kelasId;
            item.classList.toggle('hidden', shouldHide);

            if (shouldHide) {
                const checkbox = item.querySelector('.target-siswa-checkbox');
                if (checkbox) {
                    checkbox.checked = false;
                }
            }
        });
    });

    document.querySelector('[data-select-targets]')?.addEventListener('click', () => selectVisibleTargets(true));
    document.querySelector('[data-deselect-targets]')?.addEventListener('click', () => {
        document.querySelectorAll('.target-siswa-checkbox').forEach((checkbox) => {
            checkbox.checked = false;
        });
    });
    document.querySelector('[data-confirm-duplicate]')?.addEventListener('click', confirmDuplicate);
});
