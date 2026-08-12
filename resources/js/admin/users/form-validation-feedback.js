const fieldHints = {
    username: 'Nama pengguna untuk login. Gunakan huruf, angka, titik, atau garis bawah yang mudah diingat dan belum dipakai akun lain.',
    email: 'Alamat email akun. Jika wajib, isi email aktif dan unik; jika opsional, boleh dikosongkan bila belum tersedia.',
    personal_email: 'Email pribadi untuk pemulihan akun jika user lupa password atau tidak bisa login.',
    password: 'Password akun untuk login. Ikuti minimal karakter yang diminta dan simpan dengan aman.',
    password_confirmation: 'Ketik ulang password yang sama agar sistem bisa memastikan tidak ada salah ketik.',
    role: 'Pilih jabatan agar hak akses menu dan fitur sesuai tugas pengguna.',
    cabang_id: 'Pilih cabang penempatan. Beberapa role pusat otomatis diarahkan ke Gedung Utama.',
    cabang_id_hidden: 'Cabang otomatis mengikuti aturan role yang dipilih.',
    is_active: 'Tentukan apakah akun boleh login. Nonaktif membuat user tidak bisa masuk tanpa menghapus data.',
    name: 'Nama lengkap wali siswa sesuai identitas atau data keluarga.',
    nama_lengkap: 'Isi nama lengkap sesuai identitas resmi. Untuk tenaga pendidik, sertakan gelar jika ada.',
    nip: 'Nomor induk pegawai jika tersedia. Boleh dikosongkan untuk guru atau staf yang belum memiliki NIP.',
    jenis_kelamin: 'Pilih jenis kelamin sesuai data identitas.',
    telepon: 'Nomor telepon atau WhatsApp aktif yang dapat dihubungi sekolah.',
    phone: 'Nomor telepon atau WhatsApp wali siswa yang dapat dihubungi sekolah.',
    tempat_lahir: 'Ketik atau pilih kota/negara tempat lahir sesuai dokumen identitas.',
    tanggal_lahir: 'Tanggal lahir sesuai dokumen identitas.',
    pendidikan_terakhir: 'Pendidikan terakhir tenaga pendidik, misalnya S1, S2, SMA, atau setara.',
    alamat: 'Alamat domisili lengkap agar data administrasi mudah diverifikasi.',
    address: 'Alamat domisili wali siswa. Boleh dikosongkan jika belum tersedia.',
    nisn: 'Nomor Induk Siswa Nasional. Pastikan angkanya benar karena dipakai sebagai identitas utama siswa.',
    nis: 'Nomor Induk Siswa di sekolah/PKBM. Pastikan tidak tertukar dengan NISN.',
    kelas_id: 'Pilih kelas aktif siswa. Cabang siswa akan mengikuti cabang dari kelas yang dipilih.',
    tanggal_masuk: 'Tanggal siswa mulai terdaftar atau mulai belajar di sekolah/PKBM.',
    agama: 'Pilih agama siswa. Data ini dipakai untuk akses LMS mata pelajaran agama yang sesuai.',
    status: 'Status akademik siswa. Status pindah atau keluar akan membatasi akses sesuai aturan sistem.',
    nama_ayah: 'Nama ayah kandung jika tersedia. Boleh dikosongkan jika belum diketahui.',
    nama_ibu: 'Nama ibu kandung jika tersedia. Boleh dikosongkan jika belum diketahui.',
    telepon_orangtua: 'Nomor WhatsApp wali/orang tua untuk komunikasi sekolah.',
    parent_option: 'Pilih cara mengatur akun wali siswa: hubungkan akun lama, buat akun baru, atau lewati dulu.',
    parent_id: 'Pilih akun wali siswa yang sudah ada untuk dihubungkan ke siswa ini.',
    existing_relationship: 'Pilih hubungan wali siswa yang sudah ada dengan siswa ini.',
    existing_relationship_lainnya: 'Isi jika hubungan keluarga tidak ada pada pilihan yang tersedia.',
    parent_name: 'Nama lengkap wali siswa baru yang akan dibuatkan akun login.',
    parent_username: 'Username untuk akun wali siswa baru. Harus unik dan mudah diingat.',
    parent_email: 'Email akun wali siswa baru. Dipakai untuk identitas akun dan harus unik.',
    parent_password: 'Password awal akun wali siswa baru. Berikan kepada wali melalui jalur yang aman.',
    parent_phone: 'Nomor WhatsApp wali siswa baru. Opsional jika belum tersedia.',
    new_relationship: 'Pilih hubungan wali siswa baru dengan siswa ini.',
    new_relationship_lainnya: 'Isi jika hubungan keluarga tidak ada pada pilihan yang tersedia.',
    is_primary: 'Centang jika wali ini menjadi kontak utama siswa.',
    can_access_academic: 'Centang jika wali boleh melihat data akademik siswa.',
    searchParent: 'Gunakan untuk mencari wali siswa berdasarkan nama atau username sebelum memilih.',
    filterParentStatus: 'Gunakan untuk mempersempit daftar wali siswa berdasarkan status hubungan.',
    add_parent_option: 'Pilih tindakan jika ingin menambah atau menghubungkan wali siswa ke data siswa ini.',
    add_existing_parent_id: 'Pilih akun wali siswa yang sudah ada dan belum terhubung dengan siswa ini.',
    add_existing_relationship: 'Pilih hubungan keluarga untuk wali siswa yang akan dihubungkan.',
    add_existing_relationship_lainnya: 'Isi jika hubungan keluarga tambahan tidak tersedia di daftar.',
    add_new_parent_name: 'Nama lengkap wali siswa baru yang akan dibuat dan dihubungkan ke siswa ini.',
    add_new_parent_username: 'Username unik untuk akun wali siswa baru.',
    add_new_parent_email: 'Email akun wali siswa baru. Dipakai untuk identitas akun dan harus unik.',
    add_new_parent_password: 'Password awal untuk akun wali siswa baru.',
    add_new_parent_phone: 'Nomor WhatsApp wali siswa baru untuk komunikasi sekolah.',
    add_new_relationship: 'Pilih hubungan wali siswa baru dengan siswa ini.',
    add_new_relationship_lainnya: 'Isi jika memilih hubungan Lainnya.',
    hubungan_keluarga: 'Pilih hubungan keluarga jika akun wali siswa ini dihubungkan dengan siswa.',
    hubungan_keluarga_lainnya: 'Isi jika memilih hubungan Lainnya.',
    filterJenjang: 'Filter daftar berdasarkan jenjang agar pilihan lebih mudah ditemukan.',
    filterCabang: 'Filter daftar berdasarkan cabang.',
    filterKelas: 'Filter daftar berdasarkan kelas.',
    searchStudent: 'Cari siswa berdasarkan nama atau NISN sebelum menghubungkan akun wali siswa.',
};

const arrayFieldHints = [
    [/^siswa_ids\[\]$/, 'Centang siswa yang akan dihubungkan dengan akun wali siswa ini.'],
    [/^relationships\[.+\]$/, 'Pilih hubungan keluarga wali dengan siswa ini.'],
    [/^relationships_lainnya\[.+\]$/, 'Isi jika hubungan keluarga tidak tersedia pada daftar.'],
    [/^remove_parents\[\]$/, 'Ditandai otomatis saat hubungan wali siswa dihapus dari data siswa.'],
];

function ready(callback) {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', callback);
        return;
    }

    callback();
}

function getFieldKey(field) {
    return field.name || field.id || '';
}

function getHint(field) {
    if (field.dataset.fieldHint) {
        return field.dataset.fieldHint;
    }

    const key = getFieldKey(field);
    if (fieldHints[key]) {
        return fieldHints[key];
    }

    const arrayHint = arrayFieldHints.find(([pattern]) => pattern.test(key));
    if (arrayHint) {
        return arrayHint[1];
    }

    if (field.type === 'hidden' || field.closest('.modal, .student-list, .kelas-list, .parent-list-block, .child-status-list')) {
        return '';
    }

    if (field.required || field.dataset.requiredWhenVisible === 'true') {
        return 'Wajib diisi sebelum data dapat disimpan.';
    }

    return 'Opsional, boleh dikosongkan jika belum tersedia.';
}

function getFieldGroup(field) {
    return field.closest('.form-group') || field.closest('.col') || field.parentElement;
}

function groupAlreadyHasHint(group) {
    if (!group) return true;
    return Boolean(group.querySelector(':scope > .field-hint, :scope > small.text-muted, :scope > .section-note'));
}

function appendHint(field) {
    const group = getFieldGroup(field);
    const hint = getHint(field);

    if (!group || !hint || groupAlreadyHasHint(group)) {
        return;
    }

    const hintElement = document.createElement('small');
    hintElement.className = 'field-hint';
    hintElement.textContent = hint;
    group.appendChild(hintElement);
}

function normalizeLabelText(text) {
    return text
        .replace('*', '')
        .replace(/\(Opsional\)|\(Penting.*?\)|\(Kosongkan.*?\)/gi, '')
        .replace(/\s+/g, ' ')
        .trim();
}

function getLabel(field) {
    const group = getFieldGroup(field);
    const label = group?.querySelector('.form-label');
    const text = normalizeLabelText(label?.textContent || field.name || field.id || 'Field ini');
    return text || 'Field ini';
}

function isInHiddenConditionalBlock(field) {
    if (field.name === 'kelas_id') {
        return false;
    }

    const hiddenBlock = field.closest('.d-none, .is-hidden, [hidden]');
    return Boolean(hiddenBlock);
}

function isRequiredForCurrentState(field) {
    if (field.required) {
        return true;
    }

    return field.dataset.requiredWhenVisible === 'true' && !isInHiddenConditionalBlock(field);
}

function fieldIsEmpty(field, form) {
    if (field.type === 'radio') {
        return !form.querySelector(`input[type="radio"][name="${CSS.escape(field.name)}"]:checked`);
    }

    if (field.type === 'checkbox') {
        return !field.checked;
    }

    return !String(field.value || '').trim();
}

function getPlacementElement(field) {
    const group = getFieldGroup(field);
    if (!group) return field;

    if (field.type === 'radio') {
        return group.querySelector('.radio-group') || field.closest('.radio-group') || field;
    }

    if (field.name === 'kelas_id') {
        return group.querySelector('.kelas-display, .kelas-display-edit') || field;
    }

    return field.closest('.password-wrapper, .password-wrap, .password-field-wrap') || field;
}

function clearFieldError(field) {
    const group = getFieldGroup(field);
    if (!group) return;

    group.querySelectorAll('.js-field-error, .server-field-error').forEach((element) => element.remove());
    group.querySelectorAll('.is-invalid').forEach((element) => element.classList.remove('is-invalid'));
    field.classList.remove('is-invalid');
}

function showFieldError(field, message, type = 'js') {
    const group = getFieldGroup(field);
    const placement = getPlacementElement(field);

    if (!group || !placement) return;

    if (type === 'js') {
        group.querySelectorAll('.js-field-error').forEach((element) => element.remove());
    }

    const invalidTarget = field.name === 'kelas_id'
        ? placement
        : field.type === 'radio'
            ? placement
            : field;

    invalidTarget.classList.add('is-invalid');

    if (type === 'server' && group.querySelector('.field-error:not(.js-field-error)')) {
        return;
    }

    const errorElement = document.createElement('div');
    errorElement.className = type === 'server'
        ? 'text-danger field-error server-field-error'
        : 'text-danger field-error js-field-error';
    errorElement.textContent = message;

    if (placement.nextSibling) {
        placement.parentNode.insertBefore(errorElement, placement.nextSibling);
        return;
    }

    placement.parentNode.appendChild(errorElement);
}

function validateField(field, form) {
    if (field.disabled || isInHiddenConditionalBlock(field)) {
        return null;
    }

    const label = getLabel(field);

    if (isRequiredForCurrentState(field) && fieldIsEmpty(field, form)) {
        return field.dataset.requiredMessage || `${label} wajib diisi.`;
    }

    if (field.type === 'email' && field.value.trim() && !field.checkValidity()) {
        return `${label} harus berupa alamat email yang valid.`;
    }

    if (field.minLength > 0 && field.value && field.value.length < field.minLength) {
        return `${label} minimal ${field.minLength} karakter.`;
    }

    if (field.name === 'password_confirmation') {
        const passwordField = form.querySelector('input[name="password"]');
        if (passwordField?.value && field.value && passwordField.value !== field.value) {
            return 'Konfirmasi password harus sama dengan password.';
        }
    }

    return null;
}

function collectValidationFields(form) {
    const fields = Array.from(form.querySelectorAll('input, select, textarea'));
    const seenRadioNames = new Set();

    return fields.filter((field) => {
        if (!field.name || ['_token', '_method', '_return_url'].includes(field.name)) {
            return false;
        }

        if (field.disabled) {
            return false;
        }

        if (field.type === 'hidden' && field.name !== 'kelas_id') {
            return false;
        }

        if (field.closest('.modal, .student-list, .kelas-list, .parent-list-block') && field.type !== 'radio') {
            return false;
        }

        if (field.type === 'radio') {
            if (seenRadioNames.has(field.name)) {
                return false;
            }
            seenRadioNames.add(field.name);
        }

        return true;
    });
}

function showSummary(form, errors) {
    form.parentElement?.querySelectorAll('.client-validation-summary').forEach((element) => element.remove());

    if (errors.length === 0) {
        return;
    }

    const summary = document.createElement('div');
    summary.className = 'client-validation-summary';
    summary.innerHTML = '<strong><i class="fas fa-exclamation-circle"></i> Data belum bisa disimpan:</strong>';

    const list = document.createElement('ul');
    errors.forEach((item) => {
        const li = document.createElement('li');
        li.textContent = item.message;
        list.appendChild(li);
    });
    summary.appendChild(list);

    form.parentElement?.insertBefore(summary, form);
}

function validateForm(form) {
    const errors = [];

    collectValidationFields(form).forEach((field) => {
        clearFieldError(field);
        const message = validateField(field, form);
        if (!message) return;

        showFieldError(field, message);
        errors.push({ field, message });
    });

    showSummary(form, errors);

    if (errors.length > 0) {
        const firstField = errors[0].field;
        const firstTarget = getPlacementElement(firstField) || firstField;
        firstTarget.scrollIntoView({ behavior: 'smooth', block: 'center' });

        if (typeof firstTarget.focus === 'function' && firstTarget.matches?.('input, select, textarea, button')) {
            firstTarget.focus({ preventScroll: true });
        } else if (typeof firstField.focus === 'function') {
            firstField.focus({ preventScroll: true });
        }

        return false;
    }

    return true;
}

function findFieldForError(form, key) {
    const candidates = [key];

    if (/^siswa_ids\.\d+$/.test(key)) {
        candidates.push('siswa_ids[]');
    }

    const bracketName = key.replace(/\.([^\.]+)/g, '[$1]');
    candidates.push(bracketName);

    return candidates
        .map((candidate) => form.querySelector(`[name="${CSS.escape(candidate)}"]`))
        .find(Boolean);
}

function applyServerErrors(form) {
    const errors = window.adminUserValidationErrors || {};
    Object.entries(errors).forEach(([key, messages]) => {
        const field = findFieldForError(form, key);
        if (!field) return;

        showFieldError(field, messages[0] || 'Field ini belum valid.', 'server');
    });
}

ready(() => {
    document.querySelectorAll('form[data-admin-user-form]').forEach((form) => {
        collectValidationFields(form).forEach((field) => {
            appendHint(field);
            field.addEventListener('input', () => clearFieldError(field));
            field.addEventListener('change', () => clearFieldError(field));
        });

        applyServerErrors(form);

        form.addEventListener('submit', (event) => {
            if (!validateForm(form)) {
                event.preventDefault();
                event.stopImmediatePropagation();
            }
        }, true);
    });
});
