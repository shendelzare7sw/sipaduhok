@extends('layouts.lms-guru')

@section('title', 'Pengawasan Ujian')
@section('page-title', 'Pengawasan Ujian: ' . $ujian->judul_ujian)
@section('page-subtitle', $mapel->nama_mapel . ' - ' . $kelas->nama_kelas)

@section('sidebar-menu')
    @include('guru.partials.sidebar-lms')
@endsection

@push('styles')
    @vite(['resources/css/guru/lms/ujian/pengawasan.css'])
@endpush

@push('scripts')
    @vite(['resources/js/guru/lms/ujian/pengawasan.js'])
@endpush

@section('content')
    <div class="guru-lms-ujian-pengawasan-page"
        data-monitoring-url="{{ route('guru.lms.ujian.pengawasan.data', [$kelas->id, $mapel->id, $ujian->id]) }}"
        data-total-questions="{{ max(1, $soalList->count()) }}">
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
                                    <span><span class="question-cell question-cell-legend answered d-inline-flex"></span> Dijawab</span>
                                    <span><span class="question-cell question-cell-legend doubt d-inline-flex"></span> Ragu</span>
                                    <span><span class="question-cell question-cell-legend visited d-inline-flex"></span> Dikunjungi</span>
                                    <span><span class="question-cell question-cell-legend current d-inline-flex"></span> Sedang dibuka</span>
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
    </div>
@endsection
