@extends('layouts.lms-guru')

@section('title', 'Pengawasan Ujian')
@section('page-title', 'Pengawasan Ujian: ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
<style>
    .monitor-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .monitor-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .monitor-refresh {
        display: flex;
        align-items: center;
        gap: 6px;
        color: #64748b;
        font-size: 13px;
        min-width: 0;
    }

    .monitor-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(min(160px, 100%), 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .monitor-stat {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        min-height: 96px;
        min-width: 0;
    }

    .monitor-stat .value {
        font-size: 28px;
        font-weight: 800;
        line-height: 1;
    }

    .monitor-stat .label {
        color: #64748b;
        font-size: 13px;
        margin-top: 8px;
        overflow-wrap: anywhere;
    }

    .monitor-panel {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
    }

    .monitor-panel-header {
        padding: 14px 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        font-weight: 700;
    }

    .progress {
        height: 8px;
        background: #e5e7eb;
    }

    .monitor-table-wrap {
        overflow-x: auto;
    }

    .monitor-table {
        min-width: 920px;
        table-layout: fixed;
    }

    .monitor-table th,
    .monitor-table td {
        vertical-align: middle;
        white-space: normal;
        overflow-wrap: anywhere;
    }

    .monitor-table .col-name { width: 22%; }
    .monitor-table .col-status { width: 13%; }
    .monitor-table .col-progress { width: 20%; }
    .monitor-table .col-current { width: 10%; }
    .monitor-table .col-small { width: 8%; }
    .monitor-table .col-activity { width: 14%; }
    .monitor-table .col-action { width: 9%; }

    .monitor-card-list {
        display: none;
        padding: 12px;
        gap: 12px;
    }

    .student-monitor-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        background: #fff;
    }

    .student-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 12px;
    }

    .student-name {
        font-weight: 800;
        color: #0f172a;
        line-height: 1.35;
        overflow-wrap: anywhere;
    }

    .student-meta {
        color: #64748b;
        font-size: 12px;
        margin-top: 4px;
    }

    .student-card-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 8px;
        margin: 12px 0;
    }

    .student-card-metric {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px;
        min-width: 0;
    }

    .student-card-metric .metric-label {
        color: #64748b;
        font-size: 11px;
        margin-bottom: 4px;
    }

    .student-card-metric .metric-value {
        font-weight: 800;
        color: #0f172a;
        overflow-wrap: anywhere;
    }

    .progress-caption {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        font-size: 12px;
        margin-bottom: 5px;
    }

    .question-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(42px, 1fr));
        gap: 8px;
    }

    .question-cell {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        min-height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        background: #f8fafc;
    }

    .question-cell.answered {
        background: #dcfce7;
        border-color: #16a34a;
        color: #166534;
    }

    .question-cell.doubt {
        background: #fef3c7;
        border-color: #f59e0b;
        color: #92400e;
    }

    .question-cell.visited {
        background: #e0f2fe;
        border-color: #0284c7;
        color: #075985;
    }

    .question-cell.current {
        box-shadow: 0 0 0 3px rgba(22, 95, 172, 0.22);
        border-color: #165fac;
    }

    .log-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
        max-height: 260px;
        overflow: auto;
    }

    .log-item {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 10px 12px;
        background: #f8fafc;
    }

    .status-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
    }

    .status-dot.online { background: #16a34a; }
    .status-dot.offline { background: #94a3b8; }
    .status-dot.warning { background: #f59e0b; }

    @media (max-width: 992px) {
        .monitor-table-wrap {
            display: none;
        }

        .monitor-card-list {
            display: grid;
        }
    }

    @media (max-width: 768px) {
        .monitor-toolbar {
            align-items: stretch;
        }

        .monitor-actions,
        .monitor-refresh {
            width: 100%;
        }

        .monitor-actions .btn {
            flex: 1 1 145px;
        }

        .monitor-refresh {
            align-items: flex-start;
            line-height: 1.45;
        }

        .monitor-stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .monitor-stat {
            min-height: 88px;
            padding: 14px;
        }

        .monitor-stat .value {
            font-size: 22px;
        }

        .monitor-panel-header {
            padding: 12px;
        }

        .question-grid {
            grid-template-columns: repeat(auto-fill, minmax(36px, 1fr));
            gap: 6px;
        }

        .question-cell {
            min-height: 36px;
            border-radius: 6px;
        }
    }

    @media (max-width: 380px) {
        .monitor-stats-grid,
        .student-card-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    <div class="monitor-toolbar mb-3">
        <div class="monitor-actions">
            <a href="{{ route('guru.lms.ujian.index', [$kelas->id, $mapel->id]) }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
            <a href="{{ route('guru.lms.ujian.hasil', [$kelas->id, $mapel->id, $ujian->id]) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-chart-bar me-1"></i>Lihat Hasil
            </a>
        </div>
        <div class="monitor-refresh">
            <i class="fas fa-sync-alt me-1"></i>
            <span>Muat ulang otomatis setiap 5 detik. <span id="lastUpdated" class="fw-semibold">Memuat...</span></span>
        </div>
    </div>

    <div class="monitor-stats-grid">
        <div class="monitor-stat">
            <div class="value text-primary" id="statTotal">0</div>
            <div class="label">Total Peserta</div>
        </div>
        <div class="monitor-stat">
            <div class="value text-warning" id="statWorking">0</div>
            <div class="label">Mengerjakan</div>
        </div>
        <div class="monitor-stat">
            <div class="value text-success" id="statActive">0</div>
            <div class="label">Aktif</div>
        </div>
        <div class="monitor-stat">
            <div class="value text-danger" id="statFocusLost">0</div>
            <div class="label">Keluar Fokus</div>
        </div>
        <div class="monitor-stat">
            <div class="value text-success" id="statSubmitted">0</div>
            <div class="label">Dikumpulkan</div>
        </div>
        <div class="monitor-stat">
            <div class="value text-danger" id="statViolations">0</div>
            <div class="label">Total Pelanggaran</div>
        </div>
    </div>

    <div class="monitor-panel">
        <div class="monitor-panel-header">
            <span><i class="fas fa-desktop me-2"></i>Aktivitas Siswa Dalam Browser</span>
            <span class="badge bg-light text-dark border">{{ $soalList->count() }} soal</span>
        </div>
        <div class="monitor-table-wrap">
            <table class="table table-hover align-middle mb-0 monitor-table">
                <thead class="table-light">
                    <tr>
                        <th class="col-name">Nama Siswa</th>
                        <th class="text-center col-status">Status</th>
                        <th class="col-progress">Progres</th>
                        <th class="text-center col-current">Soal Aktif</th>
                        <th class="text-center col-small">Ragu</th>
                        <th class="text-center col-small">Pelanggaran</th>
                        <th class="col-activity">Aktivitas Terakhir</th>
                        <th class="text-center col-action">Detail</th>
                    </tr>
                </thead>
                <tbody id="monitorRows">
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">Memuat data pengawasan...</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="monitor-card-list" id="monitorCards">
            <div class="text-center text-muted py-4">Memuat data pengawasan...</div>
        </div>
    </div>

    <div class="modal fade" id="studentDetailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1" id="detailStudentName">Detail Siswa</h5>
                        <div class="small text-muted" id="detailStudentMeta">-</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-lg-7">
                            <h6 class="fw-bold mb-2">Peta Soal</h6>
                            <div class="question-grid mb-3" id="detailQuestionGrid"></div>
                            <div class="d-flex gap-2 flex-wrap small text-muted">
                                <span><span class="question-cell answered d-inline-flex" style="width:24px;min-height:24px;"></span> Dijawab</span>
                                <span><span class="question-cell doubt d-inline-flex" style="width:24px;min-height:24px;"></span> Ragu</span>
                                <span><span class="question-cell visited d-inline-flex" style="width:24px;min-height:24px;"></span> Dikunjungi</span>
                                <span><span class="question-cell current d-inline-flex" style="width:24px;min-height:24px;"></span> Sedang dibuka</span>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <h6 class="fw-bold mb-2">Timeline Pengawasan</h6>
                            <div class="log-list" id="detailLogs"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    const dataUrl = "{{ route('guru.lms.ujian.pengawasan.data', [$kelas->id, $mapel->id, $ujian->id]) }}";
    const totalQuestions = {{ max(1, $soalList->count()) }};
    let latestStudents = [];
    let isFetching = false;

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[char];
        });
    }

    function setText(id, value) {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    }

    function formatSeconds(seconds) {
        seconds = Math.round(Number(seconds || 0));
        const minutes = Math.floor(seconds / 60);
        const rest = seconds % 60;
        if (minutes <= 0) return `${rest} dtk`;
        return `${minutes} mnt ${rest} dtk`;
    }

    function renderStats(stats) {
        setText('statTotal', stats.total || 0);
        setText('statWorking', stats.sedang_mengerjakan || 0);
        setText('statActive', stats.aktif || 0);
        setText('statFocusLost', stats.keluar_fokus || 0);
        setText('statSubmitted', stats.selesai || 0);
        setText('statViolations', stats.total_pelanggaran || 0);
    }

    function statusBadge(student) {
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
    }

    function violationBadge(count, totalSeconds) {
        const color = count >= 3 ? 'danger' : (count > 0 ? 'warning text-dark' : 'secondary');
        const title = count > 0 ? `Total keluar fokus ${formatSeconds(totalSeconds)}` : 'Belum ada pelanggaran';
        return `<span class="badge bg-${color}" title="${escapeHtml(title)}">${count}</span>`;
    }

    function connectionLabel(student) {
        if (student.is_focus_lost) {
            return '<span class="status-dot warning"></span>Keluar fokus';
        }
        if (student.is_online) {
            return '<span class="status-dot online"></span>Aktif';
        }
        return '<span class="status-dot offline"></span>' + escapeHtml(student.last_activity_label || '-');
    }

    function renderRows(students) {
        const rows = document.getElementById('monitorRows');
        if (!rows) return;

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
                        <div class="progress">
                            <div class="progress-bar" style="width:${percent}%"></div>
                        </div>
                    </td>
                    <td class="text-center fw-semibold">${escapeHtml(current)}</td>
                    <td class="text-center"><span class="badge bg-warning text-dark">${student.doubt_count || 0}</span></td>
                    <td class="text-center">${violationBadge(student.focus_lost_count || 0, student.focus_lost_total_seconds || 0)}</td>
                    <td>
                        <div class="small">${escapeHtml(student.last_activity_label || '-')}</div>
                        <div class="text-muted small">${student.waktu_selesai ? 'Dikumpulkan: ' + escapeHtml(student.waktu_selesai) : ''}</div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="showStudentDetail(${index})">
                            <i class="fas fa-search me-1"></i>Detail
                        </button>
                    </td>
                </tr>
            `;
        }).join('');

        renderCards(students);
    }

    function renderCards(students) {
        const cards = document.getElementById('monitorCards');
        if (!cards) return;

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
                    <div class="progress">
                        <div class="progress-bar" style="width:${percent}%"></div>
                    </div>

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

                    <button type="button" class="btn btn-outline-primary btn-sm w-100" onclick="showStudentDetail(${index})">
                        <i class="fas fa-search me-1"></i>Detail Pengawasan
                    </button>
                </div>
            `;
        }).join('');
    }

    function renderStudentDetail(student) {
        setText('detailStudentName', student.nama || 'Detail Siswa');
        setText('detailStudentMeta', `${student.status_label || '-'} | Pelanggaran ${student.focus_lost_count || 0} kali | Total keluar fokus ${formatSeconds(student.focus_lost_total_seconds || 0)}`);

        const grid = document.getElementById('detailQuestionGrid');
        grid.innerHTML = (student.statuses || []).map((status) => {
            let cls = '';
            if (status.is_answered) cls = 'answered';
            if (status.is_visited && !status.is_answered) cls = 'visited';
            if (status.is_doubt) cls = 'doubt';
            if (status.is_current) cls += ' current';
            return `<div class="question-cell ${cls.trim()}" title="Soal ${status.nomor_soal}">${status.nomor_soal}</div>`;
        }).join('');

        const logs = document.getElementById('detailLogs');
        if (!student.recent_logs || !student.recent_logs.length) {
            logs.innerHTML = '<div class="text-muted small">Belum ada aktivitas pengawasan yang tercatat.</div>';
        } else {
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
        }
    }

    function showStudentDetail(index) {
        const student = latestStudents[index];
        if (!student) return;

        renderStudentDetail(student);
        const modal = new bootstrap.Modal(document.getElementById('studentDetailModal'));
        modal.show();
    }

    async function fetchMonitoring() {
        if (isFetching) return;
        isFetching = true;

        try {
            const response = await fetch(dataUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
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
    }

    fetchMonitoring();
    setInterval(fetchMonitoring, 5000);
</script>
@endpush
