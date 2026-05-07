{{--
    Shared partial untuk halaman "Tarik Tunggakan TA Lama → TA Aktif".
    Dipakai oleh admin & bendahara via wrapper tipis.

    Variabel yang dibutuhkan dari controller:
    - $kandidat (Collection): list kandidat siswa dengan tunggakan asli (belum dialihkan)
    - $taAktif (TahunAjaran): TA aktif (target carryover)
    - $cabangList (Collection<Cabang>)
    - $selectedCabangId (?int)
    - $totalSiswa (int)
    - $grandTotal (float)
    - $baseRouteName (string): "admin.keuangan.tagihan" atau "bendahara.tagihan"
--}}

<style>
    .carryover-summary {
        background: linear-gradient(135deg, #4e73df, #3651d4);
        color: white;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 22px;
        box-shadow: 0 4px 14px -4px rgba(78, 115, 223, 0.4);
    }
    .carryover-summary .label { font-size: 11px; opacity: 0.85; text-transform: uppercase; letter-spacing: .5px; }
    .carryover-summary .value { font-size: 1.5rem; font-weight: 800; line-height: 1.2; }

    .filter-bar {
        background: white;
        padding: 14px 18px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        margin-bottom: 18px;
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        align-items: center;
    }

    .kandidat-table { width: 100%; border-collapse: separate; border-spacing: 0; }
    .kandidat-table th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .5px;
        padding: 12px 14px;
        text-align: left;
        border-bottom: 2px solid #e3e6f0;
    }
    .kandidat-table td {
        padding: 14px;
        border-bottom: 1px solid #eef0f4;
        vertical-align: middle;
        font-size: 13px;
    }
    .kandidat-row:hover { background: #fafbfd; }

    .siswa-info { display: flex; flex-direction: column; gap: 2px; }
    .siswa-info .nama { font-weight: 700; color: #1e293b; }
    .siswa-info .meta { font-size: 11px; color: #64748b; }

    .ta-pill {
        display: inline-block;
        background: #fee2e2;
        color: #b91c1c;
        font-weight: 700;
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 10px;
        margin-right: 4px;
        margin-bottom: 3px;
    }

    .total-tunggakan {
        font-size: 15px;
        font-weight: 800;
        color: #b91c1c;
        line-height: 1.2;
    }

    .detail-link {
        font-size: 11px;
        color: #4e73df;
        text-decoration: none;
        cursor: pointer;
        font-weight: 600;
    }
    .detail-link:hover { text-decoration: underline; color: #3651d4; }

    .checkbox-cell { width: 50px; text-align: center; }
    .checkbox-cell input[type=checkbox] { transform: scale(1.2); cursor: pointer; }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    .empty-state i { font-size: 3rem; color: #cbd5e1; margin-bottom: 12px; display: block; }

    .action-bar {
        position: sticky;
        bottom: 0;
        background: white;
        border-top: 2px solid #e3e6f0;
        padding: 14px 18px;
        margin: 16px -18px -18px -18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        border-radius: 0 0 12px 12px;
        z-index: 5;
    }

    .soft-warning {
        background: #fffbeb;
        border-left: 4px solid #f59e0b;
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 18px;
        color: #78350f;
        font-size: 13px;
    }
    .soft-warning i { color: #d97706; margin-right: 6px; }

    /* SweetAlert detail table */
    .swal2-html-container .detail-table { width: 100%; font-size: 12px; border-collapse: collapse; }
    .swal2-html-container .detail-table th { background: #f8f9fc; padding: 6px 10px; text-align: left; font-size: 11px; color: #4e73df; }
    .swal2-html-container .detail-table td { padding: 7px 10px; border-bottom: 1px solid #eef0f4; }

    @media (max-width: 768px) {
        .kandidat-table th, .kandidat-table td { padding: 10px 8px; font-size: 12px; }
        .action-bar { flex-direction: column; align-items: stretch; }
        .action-bar .btn { width: 100%; }
        .kandidat-table thead { display: none; }
        .kandidat-table, .kandidat-table tbody, .kandidat-table tr, .kandidat-table td { display: block; width: 100%; }
        .kandidat-row { border: 1px solid #e5e7eb; border-radius: 8px; padding: 8px; margin-bottom: 10px; }
        .kandidat-table td { border-bottom: none; padding: 6px 8px !important; }
        .kandidat-table .checkbox-cell { display: inline-block !important; width: auto !important; padding-right: 12px !important; }
    }
</style>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route($baseRouteName . '.index') }}">Tagihan</a></li>
                    <li class="breadcrumb-item active">Tarik Tunggakan</li>
                </ol>
            </nav>
            <h4 class="fw-bold mb-1"><i class="fas fa-arrow-circle-right me-2 text-primary"></i>Tarik Tunggakan ke TA Aktif</h4>
            <p class="text-muted mb-0">Alihkan tunggakan tahun ajaran sebelumnya menjadi tagihan baru di TA aktif. Tagihan asal tetap tersimpan untuk audit.</p>
        </div>
        <div>
            <a href="{{ route($baseRouteName . '.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-triangle me-1"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Soft warning --}}
    <div class="soft-warning">
        <i class="fas fa-info-circle"></i>
        <strong>Best practice:</strong> Pastikan tagihan reguler TA aktif (SPP, Buku, Seragam, dll) sudah dibuat untuk siswa terkait sebelum eksekusi carryover. Carryover hanya menambah tagihan baru — tidak menimpa tagihan yang sudah ada.
    </div>

    {{-- Summary --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="carryover-summary">
                <div class="label">TA Tujuan (Aktif)</div>
                <div class="value">{{ $taAktif->nama_tahun_ajaran }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="carryover-summary" style="background: linear-gradient(135deg, #dc2626, #b91c1c); box-shadow: 0 4px 14px -4px rgba(220, 38, 38, 0.4);">
                <div class="label">Siswa dengan Tunggakan</div>
                <div class="value">{{ $totalSiswa }} <small style="font-size: 0.75rem; font-weight: 600;">siswa</small></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="carryover-summary" style="background: linear-gradient(135deg, #f59e0b, #d97706); box-shadow: 0 4px 14px -4px rgba(245, 158, 11, 0.4);">
                <div class="label">Total Tunggakan</div>
                <div class="value">Rp {{ number_format($grandTotal, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <form method="GET" action="{{ route($baseRouteName . '.carryover') }}" class="filter-bar">
        <div style="flex: 1; min-width: 200px;">
            <label class="form-label small fw-bold mb-1">Cabang</label>
            <select name="cabang_id" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Cabang</option>
                @foreach($cabangList as $c)
                    <option value="{{ $c->id }}" @selected($selectedCabangId == $c->id)>{{ $c->nama_cabang }}</option>
                @endforeach
            </select>
        </div>
        @if($selectedCabangId)
            <div style="align-self: flex-end;">
                <a href="{{ route($baseRouteName . '.carryover') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        @endif
    </form>

    @php
        // Pre-build detail data per siswa untuk konsumsi JS (compact)
        $detailData = [];
        foreach ($kandidat as $row) {
            $tagihanList = [];
            foreach ($row['perTahun'] as $bt) {
                foreach ($bt['tagihan'] as $t) {
                    $tagihanList[] = [
                        'ta' => $bt['tahun_ajaran']->nama_tahun_ajaran ?? '-',
                        'jenis' => ucwords(str_replace('_', ' ', $t->jenis_tagihan ?? '')),
                        'keterangan' => $t->keterangan ?: '-',
                        'sisa' => (float) $t->sisa,
                    ];
                }
            }
            $detailData[$row['siswa']->id] = [
                'nama' => $row['siswa']->nama_lengkap,
                'total' => (float) $row['totalTunggakan'],
                'jumlah' => $row['jumlahItem'],
                'tagihan' => $tagihanList,
                'tas' => collect($row['perTahun'])->map(fn($b) => $b['tahun_ajaran']->nama_tahun_ajaran ?? '-')->values()->all(),
            ];
        }
    @endphp

    <div class="card">
        <div class="card-body p-0">
            @if($kandidat->isEmpty())
                <div class="empty-state">
                    <i class="fas fa-check-circle text-success"></i>
                    <h5 class="fw-bold mb-1">Tidak Ada Tunggakan</h5>
                    <p class="mb-0">Semua siswa sudah lunas atau tunggakan TA lama sudah dialihkan.</p>
                </div>
            @else
                <form id="carryover-form" method="POST" action="{{ route($baseRouteName . '.carryover.execute') }}">
                    @csrf

                    <div class="table-responsive">
                        <table class="kandidat-table">
                            <thead>
                                <tr>
                                    <th class="checkbox-cell"><input type="checkbox" id="check-all" title="Pilih semua"></th>
                                    <th>Siswa</th>
                                    <th>Tunggakan TA</th>
                                    <th style="text-align: right; min-width: 160px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kandidat as $row)
                                    @php $sid = $row['siswa']->id; @endphp
                                    <tr class="kandidat-row">
                                        <td class="checkbox-cell" data-label="Pilih">
                                            <input type="checkbox" name="siswa_ids[]" value="{{ $sid }}" class="siswa-check">
                                        </td>
                                        <td data-label="Siswa">
                                            <div class="siswa-info">
                                                <span class="nama">{{ $row['siswa']->nama_lengkap }}</span>
                                                <span class="meta">
                                                    NIS: {{ $row['siswa']->nis ?: '-' }}
                                                    @if($row['siswa']->kelas)
                                                        · {{ $row['siswa']->kelas->nama_kelas }}
                                                    @endif
                                                    @if($row['siswa']->cabang)
                                                        · {{ $row['siswa']->cabang->nama_cabang }}
                                                    @endif
                                                    @if($row['siswa']->status === 'lulus')
                                                        · <span class="badge bg-secondary">ALUMNI</span>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td data-label="TA Sumber">
                                            @foreach($row['perTahun'] as $bt)
                                                <span class="ta-pill">{{ $bt['tahun_ajaran']->nama_tahun_ajaran ?? 'TA -' }}</span>
                                            @endforeach
                                        </td>
                                        <td data-label="Total" style="text-align: right;">
                                            <span class="total-tunggakan">Rp {{ number_format($row['totalTunggakan'], 0, ',', '.') }}</span>
                                            <div style="font-size: 11px; color: #64748b; margin-top: 2px;">
                                                {{ $row['jumlahItem'] }} tagihan ·
                                                <a class="detail-link" data-detail-id="{{ $sid }}">Detail</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="action-bar">
                        <div>
                            <span class="text-muted small"><span id="selected-count">0</span> siswa terpilih</span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <button type="button" class="btn btn-outline-primary" id="btn-preview" disabled>
                                <i class="fas fa-eye me-1"></i>Pratinjau
                            </button>
                            <button type="button" class="btn btn-primary" id="btn-execute" disabled>
                                <i class="fas fa-arrow-circle-right me-1"></i>Eksekusi Carryover
                            </button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const form = document.getElementById('carryover-form');
    if (!form) return;

    const detailData = @json($detailData ?? new \stdClass());
    const checkAll = document.getElementById('check-all');
    const checks = document.querySelectorAll('.siswa-check');
    const btnPreview = document.getElementById('btn-preview');
    const btnExecute = document.getElementById('btn-execute');
    const selectedCount = document.getElementById('selected-count');
    const previewUrl = '{{ route($baseRouteName . ".carryover.preview") }}';
    const csrfToken = '{{ csrf_token() }}';

    function fmtRp(n) { return 'Rp ' + Number(n).toLocaleString('id-ID'); }

    function refresh() {
        const selected = Array.from(checks).filter(c => c.checked);
        selectedCount.textContent = selected.length;
        const has = selected.length > 0;
        btnPreview.disabled = !has;
        btnExecute.disabled = !has;
    }

    checkAll?.addEventListener('change', () => {
        checks.forEach(c => { c.checked = checkAll.checked; });
        refresh();
    });
    checks.forEach(c => c.addEventListener('change', refresh));

    // Detail per-siswa modal (SweetAlert2)
    document.querySelectorAll('.detail-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const sid = link.getAttribute('data-detail-id');
            const data = detailData[sid];
            if (!data) return;

            let rowsHtml = '';
            data.tagihan.forEach(t => {
                rowsHtml += `<tr><td><span style="color:#b91c1c;font-weight:700;font-size:10px;">${t.ta}</span></td>
                    <td>${t.jenis}<br><small class="text-muted">${t.keterangan}</small></td>
                    <td style="text-align:right;">${fmtRp(t.sisa)}</td></tr>`;
            });

            Swal.fire({
                title: data.nama,
                html: `
                    <div style="text-align:left; font-size:13px; color:#475569;">
                        <strong>${data.jumlah} tagihan tertunggak</strong> dari TA ${data.tas.join(', ')}
                    </div>
                    <table class="detail-table mt-2">
                        <thead><tr><th>TA</th><th>Tagihan</th><th style="text-align:right;">Sisa</th></tr></thead>
                        <tbody>${rowsHtml}</tbody>
                        <tfoot><tr><td colspan="2" style="font-weight:700;text-align:right;padding-top:10px;">Total</td>
                            <td style="font-weight:800;color:#b91c1c;text-align:right;padding-top:10px;">${fmtRp(data.total)}</td></tr></tfoot>
                    </table>
                `,
                width: 640,
                showCancelButton: false,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#6c757d',
            });
        });
    });

    // Pratinjau (fetch from server then show in SweetAlert)
    btnPreview?.addEventListener('click', async () => {
        const ids = Array.from(checks).filter(c => c.checked).map(c => c.value);
        if (!ids.length) return;

        Swal.fire({
            title: 'Memuat pratinjau...',
            html: '<div class="spinner-border text-primary"></div>',
            showConfirmButton: false,
            allowOutsideClick: false,
        });

        try {
            const fd = new FormData();
            fd.append('_token', csrfToken);
            ids.forEach(id => fd.append('siswa_ids[]', id));
            const res = await fetch(previewUrl, { method: 'POST', body: fd, headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (!res.ok) {
                Swal.fire({ icon: 'error', title: 'Gagal', text: data.error || 'Gagal memuat pratinjau.' });
                return;
            }

            let rows = '';
            data.items.forEach(it => {
                rows += `<tr><td>${it.siswa_nama}</td>
                    <td><small class="text-muted">${it.asal_tahun_ajaran} →</small><br>${it.keterangan_baru}</td>
                    <td style="text-align:right;">${fmtRp(it.jumlah)}</td></tr>`;
            });

            Swal.fire({
                title: 'Pratinjau Carryover',
                html: `
                    <div class="alert alert-info text-start" style="font-size:13px;">
                        <strong>${data.total_siswa} siswa</strong> · <strong>${data.total_tagihan} tagihan baru</strong> akan dibuat di TA <strong>${data.tujuan_tahun_ajaran_nama}</strong>.<br>
                        Total: <strong>${fmtRp(data.grand_total)}</strong>
                    </div>
                    <table class="detail-table">
                        <thead><tr><th>Siswa</th><th>Tagihan Baru</th><th style="text-align:right;">Jumlah</th></tr></thead>
                        <tbody>${rows}</tbody>
                    </table>
                `,
                width: 720,
                showCancelButton: true,
                confirmButtonText: 'Lanjut Eksekusi',
                cancelButtonText: 'Tutup',
                confirmButtonColor: '#3651d4',
                cancelButtonColor: '#6c757d',
            }).then(result => {
                if (result.isConfirmed) confirmExecute();
            });
        } catch (e) {
            Swal.fire({ icon: 'error', title: 'Error', text: e.message });
        }
    });

    // Eksekusi dengan konfirmasi SweetAlert
    function confirmExecute() {
        const ids = Array.from(checks).filter(c => c.checked).map(c => c.value);
        Swal.fire({
            title: 'Yakin Eksekusi?',
            html: `<p style="font-size:13px;">Tindakan ini akan membuat <strong>tagihan baru</strong> di TA aktif untuk <strong>${ids.length} siswa</strong>. Tagihan asal di TA lama akan ditandai "Sudah Dialihkan" (tidak dihapus).</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Alihkan',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
        }).then(result => {
            if (result.isConfirmed) form.submit();
        });
    }
    btnExecute?.addEventListener('click', confirmExecute);

    refresh();
})();
</script>
@endpush
