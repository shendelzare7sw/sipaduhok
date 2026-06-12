document.addEventListener('DOMContentLoaded', () => {
    const page = document.querySelector('.guru-lms-ujian-pengawasan-page');

    if (!page) {
        return;
    }

    const dataUrl = page.dataset.monitoringUrl || '';
    const totalQuestions = Number(page.dataset.totalQuestions || 1);
    let latestStudents = [];
    let isFetching = false;

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, (char) => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;',
    }[char]));

    const setText = (id, value) => {
        const element = page.querySelector(`#${id}`);
        if (element) {
            element.textContent = value;
        }
    };

    const formatSeconds = (value) => {
        const seconds = Math.round(Number(value || 0));
        const minutes = Math.floor(seconds / 60);
        const rest = seconds % 60;

        if (minutes <= 0) {
            return `${rest} dtk`;
        }

        return `${minutes} mnt ${rest} dtk`;
    };

    const applyProgressBars = (root = page) => {
        root.querySelectorAll('[data-progress]').forEach((bar) => {
            const percent = Math.min(100, Math.max(0, Number(bar.dataset.progress || 0)));
            bar.style.width = `${percent}%`;
        });
    };

    const progressBar = (percent) => `
        <div class="progress">
            <div class="progress-bar" data-progress="${percent}"></div>
        </div>
    `;

    const renderStats = (stats) => {
        setText('statTotal', stats.total || 0);
        setText('statWorking', stats.sedang_mengerjakan || 0);
        setText('statActive', stats.aktif || 0);
        setText('statFocusLost', stats.keluar_fokus || 0);
        setText('statSubmitted', stats.selesai || 0);
        setText('statViolations', stats.total_pelanggaran || 0);
    };

    const statusBadge = (student) => {
        if (student.is_focus_lost) {
            return '<span class="badge bg-danger">Keluar Fokus</span>';
        }

        if (student.status === 'sedang_mengerjakan' && student.is_online) {
            return '<span class="badge bg-success">Aktif</span>';
        }

        if (student.status === 'sedang_mengerjakan') {
            return '<span class="badge bg-warning text-dark">Tidak Aktif</span>';
        }

        if (student.status === 'selesai' || student.status === 'dinilai') {
            return '<span class="badge bg-primary">Dikumpulkan</span>';
        }

        return '<span class="badge bg-secondary">Belum Mulai</span>';
    };

    const violationBadge = (count, totalSeconds) => {
        const color = count >= 3 ? 'danger' : (count > 0 ? 'warning text-dark' : 'secondary');
        const title = count > 0 ? `Total keluar fokus ${formatSeconds(totalSeconds)}` : 'Belum ada pelanggaran';
        return `<span class="badge bg-${color}" title="${escapeHtml(title)}">${count}</span>`;
    };

    const connectionLabel = (student) => {
        if (student.is_focus_lost) {
            return '<span class="status-dot warning"></span>Keluar fokus';
        }

        if (student.is_online) {
            return '<span class="status-dot online"></span>Aktif';
        }

        return '<span class="status-dot offline"></span>' + escapeHtml(student.last_activity_label || '-');
    };

    const renderCards = (students) => {
        const cards = page.querySelector('#monitorCards');

        if (!cards) {
            return;
        }

        if (!students.length) {
            cards.innerHTML = '<div class="text-center text-muted py-4">Belum ada peserta ujian.</div>';
            return;
        }

        cards.innerHTML = students.map((student, index) => {
            const answered = Number(student.answered_count || 0);
            const percent = Math.min(100, Math.max(0, Number(student.progress_percent || 0)));
            const current = student.current_nomor_soal ? `No. ${student.current_nomor_soal}` : '-';

            return `
                <div class="student-monitor-card">
                    <div class="student-card-head">
                        <div>
                            <div class="student-name">${escapeHtml(student.nama)}</div>
                            <div class="student-meta">${connectionLabel(student)}</div>
                        </div>
                        <div class="text-end">${statusBadge(student)}</div>
                    </div>

                    <div class="progress-caption">
                        <span>${answered}/${totalQuestions} dijawab</span>
                        <strong>${percent}%</strong>
                    </div>
                    ${progressBar(percent)}

                    <div class="student-card-grid">
                        <div class="student-card-metric">
                            <div class="metric-label">Soal Aktif</div>
                            <div class="metric-value">${escapeHtml(current)}</div>
                        </div>
                        <div class="student-card-metric">
                            <div class="metric-label">Ragu-ragu</div>
                            <div class="metric-value"><span class="badge bg-warning text-dark">${student.doubt_count || 0}</span></div>
                        </div>
                        <div class="student-card-metric">
                            <div class="metric-label">Pelanggaran</div>
                            <div class="metric-value">${violationBadge(student.focus_lost_count || 0, student.focus_lost_total_seconds || 0)}</div>
                        </div>
                        <div class="student-card-metric">
                            <div class="metric-label">Terakhir</div>
                            <div class="metric-value">${escapeHtml(student.last_activity_label || '-')}</div>
                        </div>
                    </div>

                    <button type="button" class="btn btn-outline-primary btn-sm w-100" data-student-detail="${index}">
                        <i class="fas fa-search me-1"></i>Detail Pengawasan
                    </button>
                </div>
            `;
        }).join('');

        applyProgressBars(cards);
    };

    const renderRows = (students) => {
        const rows = page.querySelector('#monitorRows');

        if (!rows) {
            return;
        }

        if (!students.length) {
            rows.innerHTML = '<tr><td colspan="8" class="text-center text-muted py-4">Belum ada peserta ujian.</td></tr>';
            renderCards(students);
            return;
        }

        rows.innerHTML = students.map((student, index) => {
            const answered = Number(student.answered_count || 0);
            const percent = Math.min(100, Math.max(0, Number(student.progress_percent || 0)));
            const current = student.current_nomor_soal ? `No. ${student.current_nomor_soal}` : '-';

            return `
                <tr>
                    <td>
                        <strong>${escapeHtml(student.nama)}</strong>
                        <div class="small text-muted">${connectionLabel(student)}</div>
                    </td>
                    <td class="text-center">${statusBadge(student)}</td>
                    <td>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>${answered}/${totalQuestions} dijawab</span>
                            <span>${percent}%</span>
                        </div>
                        ${progressBar(percent)}
                    </td>
                    <td class="text-center fw-semibold">${escapeHtml(current)}</td>
                    <td class="text-center"><span class="badge bg-warning text-dark">${student.doubt_count || 0}</span></td>
                    <td class="text-center">${violationBadge(student.focus_lost_count || 0, student.focus_lost_total_seconds || 0)}</td>
                    <td>
                        <div class="small">${escapeHtml(student.last_activity_label || '-')}</div>
                        <div class="text-muted small">${student.waktu_selesai ? 'Dikumpulkan: ' + escapeHtml(student.waktu_selesai) : ''}</div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm" data-student-detail="${index}">
                            <i class="fas fa-search me-1"></i>Detail
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        applyProgressBars(rows);
        renderCards(students);
    };

    const renderStudentDetail = (student) => {
        setText('detailStudentName', student.nama || 'Detail Siswa');
        setText(
            'detailStudentMeta',
            `${student.status_label || '-'} | Pelanggaran ${student.focus_lost_count || 0} kali | Total keluar fokus ${formatSeconds(student.focus_lost_total_seconds || 0)}`,
        );

        const grid = page.querySelector('#detailQuestionGrid');

        if (grid) {
            grid.innerHTML = (student.statuses || []).map((status) => {
                let cellClass = '';

                if (status.is_answered) cellClass = 'answered';
                if (status.is_visited && !status.is_answered) cellClass = 'visited';
                if (status.is_doubt) cellClass = 'doubt';
                if (status.is_current) cellClass += ' current';

                return `<div class="question-cell ${cellClass.trim()}" title="Soal ${status.nomor_soal}">${status.nomor_soal}</div>`;
            }).join('');
        }

        const logs = page.querySelector('#detailLogs');

        if (!logs) {
            return;
        }

        if (!student.recent_logs || !student.recent_logs.length) {
            logs.innerHTML = '<div class="text-muted small">Belum ada aktivitas pengawasan yang tercatat.</div>';
            return;
        }

        logs.innerHTML = student.recent_logs.map((log) => {
            const duration = log.event_type === 'focus_lost' && log.metadata && log.metadata.duration_seconds
                ? `<div class="small text-muted">Durasi: ${formatSeconds(log.metadata.duration_seconds)}</div>`
                : '';

            return `
                <div class="log-item">
                    <div class="d-flex justify-content-between gap-2">
                        <strong>${escapeHtml(log.event_label || log.event_type)}</strong>
                        <span class="text-muted small">${escapeHtml(log.occurred_at || '')}</span>
                    </div>
                    <div class="small">${escapeHtml(log.description || '-')}</div>
                    ${duration}
                </div>
            `;
        }).join('');
    };

    const showStudentDetail = (index) => {
        const student = latestStudents[index];

        if (!student) {
            return;
        }

        renderStudentDetail(student);
        const modal = new window.bootstrap.Modal(page.querySelector('#studentDetailModal'));
        modal.show();
    };

    const fetchMonitoring = async () => {
        if (isFetching || !dataUrl) {
            return;
        }

        isFetching = true;

        try {
            const response = await fetch(dataUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const data = await response.json();

            if (!data.success) {
                throw new Error(data.message || 'Gagal memuat data pengawasan.');
            }

            latestStudents = data.students || [];
            renderStats(data.stats || {});
            renderRows(latestStudents);
            setText('lastUpdated', 'Terakhir: ' + new Date(data.generated_at).toLocaleTimeString('id-ID'));
        } catch (error) {
            setText('lastUpdated', 'Gagal memuat data');
            console.error('Monitoring fetch error:', error);
        } finally {
            isFetching = false;
        }
    };

    page.addEventListener('click', (event) => {
        const detailButton = event.target.closest('[data-student-detail]');

        if (detailButton) {
            showStudentDetail(Number(detailButton.dataset.studentDetail));
        }
    });

    fetchMonitoring();
    window.setInterval(fetchMonitoring, 5000);
});
