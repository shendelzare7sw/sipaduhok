@extends('layouts.sneat')

@section('title', 'Edit Rapor')
@section('page-title', 'Edit Rapor')
@section('page-subtitle', 'Edit catatan dan kelengkapan rapor siswa')

@section('sidebar-menu')
    @include('wali-kelas.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .table thead th {
        background: #f8f9fc;
        color: #4e73df;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #e3e6f0;
        vertical-align: middle;
    }
    .total-box {
        padding: 10px;
        background: #f0f9ff;
        border-radius: 8px;
        margin-top: 26px;
        text-align: center;
    }
    .drag-handle {
        cursor: grab;
        color: #adb5bd;
        font-size: 16px;
    }
    .drag-handle:hover { color: #4e73df; }
    .sortable-ghost { opacity: 0.4; background: #e8f0fe !important; }
    .sortable-chosen { background: #f0f9ff; }
    .row-hidden { opacity: 0.5; }
    .row-hidden td { text-decoration: line-through; }
    .btn-arrow { padding: 2px 6px; font-size: 11px; line-height: 1; }
    .kelompok-header td {
        background: #e8f0fe !important;
        font-weight: 700;
        color: #4e73df;
        font-size: 13px;
        padding: 10px 16px !important;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Header --}}
    <div class="card shadow-sm mb-4 border-start border-primary border-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="fw-bold text-gray-900 mb-1">Edit Rapor - {{ $rapor->siswa->nama_lengkap }}</h4>
                    <p class="text-muted mb-0">
                        Semester: {{ ucfirst($rapor->semester) }} |
                        Jenis: {{ $rapor->jenis_rapor === 'akhir_semester' ? 'PAS' : 'PTS' }} |
                        Kelas: {{ $rapor->kelas->nama_kelas }}
                    </p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    <button type="button" class="btn btn-warning btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#resetNilaiModal">
                        <i class="fas fa-sync-alt me-1"></i>Reset Nilai
                    </button>
                    <a href="{{ route('wali.rapor.preview', $rapor->id) }}" class="btn btn-info btn-sm shadow-sm" target="_blank">
                        <i class="fas fa-eye me-1"></i>Preview
                    </a>
                    <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary btn-sm shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i>Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Reset Nilai --}}
    <div class="modal fade" id="resetNilaiModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-warning text-white">
                    <h5 class="modal-title fw-bold text-white"><i class="fas fa-sync-alt me-2"></i>Reset Nilai Rapor</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-4 text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                    <h6 class="fw-bold mb-2">Reset semua nilai rapor dari data nilai terbaru?</h6>
                    <div class="alert alert-warning bg-light border-warning mb-0 small text-start">
                        <ul class="mb-0">
                            <li>Nilai angka akan di-generate ulang dari data nilai siswa</li>
                            <li>Deskripsi capaian yang sudah diedit <strong>akan hilang</strong></li>
                            <li>Urutan mata pelajaran akan direset</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="{{ route('wali.rapor.reset-nilai', $rapor->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning"><i class="fas fa-sync-alt me-1"></i> Ya, Reset Nilai</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('wali.rapor.update', $rapor->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Data Kehadiran --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-calendar-check me-2"></i>Data Kehadiran
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Sakit (hari)</label>
                            <input type="number" name="jumlah_sakit" class="form-control"
                                   value="{{ old('jumlah_sakit', $rapor->jumlah_sakit) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Izin (hari)</label>
                            <input type="number" name="jumlah_izin" class="form-control"
                                   value="{{ old('jumlah_izin', $rapor->jumlah_izin) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Alpha (hari)</label>
                            <input type="number" name="jumlah_alpha" class="form-control"
                                   value="{{ old('jumlah_alpha', $rapor->jumlah_alpha) }}"
                                   min="0" required>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="total-box">
                            <small class="text-muted">Total Ketidakhadiran</small>
                            <div class="h3 mb-0 fw-bold text-primary" id="totalKetidakhadiran">
                                {{ $rapor->jumlah_sakit + $rapor->jumlah_izin + $rapor->jumlah_alpha }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Wali Kelas --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-comment-alt me-2"></i>Catatan Wali Kelas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-bold small">Catatan / Komentar untuk Siswa</label>
                    <textarea name="catatan_wali_kelas" class="form-control" rows="5"
                              placeholder="Contoh: Siswa menunjukkan peningkatan yang baik dalam...">{{ old('catatan_wali_kelas', $rapor->catatan_wali_kelas) }}</textarea>
                    <small class="text-muted">Berikan catatan positif dan saran untuk perkembangan siswa</small>
                </div>
            </div>
        </div>

        {{-- Daftar Nilai --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i>Daftar Nilai Mata Pelajaran
                </h6>
                <small class="text-muted"><i class="fas fa-grip-vertical me-1"></i>Drag untuk mengubah urutan</small>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" id="nilaiTable">
                        <thead>
                            <tr>
                                <th width="30"></th>
                                <th width="40">No</th>
                                <th>Mata Pelajaran</th>
                                <th class="text-center" width="100">Nilai</th>
                                @if($rapor->jenis_rapor === 'akhir_semester')
                                    <th class="text-center" width="80">Kelompok</th>
                                @endif
                                <th class="text-center" width="60">Tampil</th>
                                <th width="30"></th>
                                <th>Deskripsi Capaian</th>
                            </tr>
                        </thead>
                        <tbody id="nilaiSortable">
                            @php $totalNilai = 0; $jumlahMapel = 0; @endphp
                            @foreach($rapor->raporNilai as $index => $raporNilai)
                                @php
                                    $totalNilai += $raporNilai->nilai_angka;
                                    $jumlahMapel++;
                                    $kelompok = $raporNilai->kelompok_override ?? ($raporNilai->mataPelajaran->kelompok ?? 'A');
                                @endphp
                                <tr data-id="{{ $raporNilai->id }}" class="{{ !$raporNilai->is_visible ? 'row-hidden' : '' }}">
                                    <td class="text-center align-middle">
                                        <i class="fas fa-grip-vertical drag-handle"></i>
                                    </td>
                                    <td class="text-center align-middle row-number">{{ $index + 1 }}</td>
                                    <td class="align-middle"><strong>{{ $raporNilai->mataPelajaran->nama_mapel }}</strong></td>
                                    <td class="text-center align-middle" style="background: #f0f9ff;">
                                        <strong class="text-primary" style="font-size: 16px;">
                                            {{ number_format($raporNilai->nilai_angka, 2) }}
                                        </strong>
                                    </td>
                                    @if($rapor->jenis_rapor === 'akhir_semester')
                                        <td class="text-center align-middle">
                                            <select name="kelompok_override[{{ $raporNilai->id }}]" class="form-select form-select-sm" style="width: 65px; margin: 0 auto;">
                                                <option value="A" {{ $kelompok === 'A' ? 'selected' : '' }}>A</option>
                                                <option value="B" {{ $kelompok === 'B' ? 'selected' : '' }}>B</option>
                                            </select>
                                        </td>
                                    @endif
                                    <td class="text-center align-middle">
                                        <div class="form-check form-switch d-flex justify-content-center">
                                            <input type="hidden" name="visible[{{ $raporNilai->id }}]" value="0">
                                            <input class="form-check-input visibility-toggle" type="checkbox"
                                                   name="visible[{{ $raporNilai->id }}]" value="1"
                                                   {{ $raporNilai->is_visible ? 'checked' : '' }}
                                                   data-row-id="{{ $raporNilai->id }}">
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <div class="btn-group-vertical">
                                            <button type="button" class="btn btn-light btn-arrow border" onclick="moveRow(this, 'up')" title="Naik"><i class="fas fa-chevron-up"></i></button>
                                            <button type="button" class="btn btn-light btn-arrow border" onclick="moveRow(this, 'down')" title="Turun"><i class="fas fa-chevron-down"></i></button>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <input type="text"
                                               name="deskripsi[{{ $raporNilai->id }}]"
                                               class="form-control form-control-sm"
                                               value="{{ old('deskripsi.' . $raporNilai->id, $raporNilai->deskripsi) }}"
                                               placeholder="Deskripsi capaian kompetensi...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr style="background: #f3f4f6;">
                                <td colspan="{{ $rapor->jenis_rapor === 'akhir_semester' ? 3 : 2 }}" class="text-end fw-bold align-middle" style="padding: 16px;">RATA-RATA:</td>
                                <td class="text-center fw-bold align-middle text-primary" style="font-size: 18px;">
                                    {{ $jumlahMapel > 0 ? number_format($totalNilai / $jumlahMapel, 2) : '0.00' }}
                                </td>
                                <td colspan="{{ $rapor->jenis_rapor === 'akhir_semester' ? 4 : 3 }}"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if($rapor->raporNilai->count() == 0)
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-gray-200 mb-3"></i>
                        <p class="text-muted">Belum ada data nilai</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="card shadow mb-5">
            <div class="card-body text-end">
                <a href="{{ route('wali.rapor.index') }}" class="btn btn-secondary shadow-sm">
                    <i class="fas fa-times me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary shadow-sm px-4 fw-bold">
                    <i class="fas fa-save me-1"></i>Simpan Perubahan
                </button>
            </div>
        </div>
    </form>

</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
    // Auto calculate total ketidakhadiran
    document.querySelectorAll('input[name="jumlah_sakit"], input[name="jumlah_izin"], input[name="jumlah_alpha"]').forEach(input => {
        input.addEventListener('input', function() {
            const sakit = parseInt(document.querySelector('input[name="jumlah_sakit"]').value) || 0;
            const izin = parseInt(document.querySelector('input[name="jumlah_izin"]').value) || 0;
            const alpha = parseInt(document.querySelector('input[name="jumlah_alpha"]').value) || 0;
            document.getElementById('totalKetidakhadiran').textContent = sakit + izin + alpha;
        });
    });

    // SortableJS for drag reorder
    const sortableEl = document.getElementById('nilaiSortable');
    if (sortableEl) {
        new Sortable(sortableEl, {
            handle: '.drag-handle',
            animation: 150,
            ghostClass: 'sortable-ghost',
            chosenClass: 'sortable-chosen',
            onEnd: function() {
                updateRowNumbers();
                saveOrder();
            }
        });
    }

    // Arrow buttons to move rows up/down
    function moveRow(btn, direction) {
        const row = btn.closest('tr');
        const tbody = row.parentNode;

        if (direction === 'up' && row.previousElementSibling) {
            tbody.insertBefore(row, row.previousElementSibling);
        } else if (direction === 'down' && row.nextElementSibling) {
            tbody.insertBefore(row.nextElementSibling, row);
        }

        updateRowNumbers();
        saveOrder();
    }

    // Update row numbers after reorder
    function updateRowNumbers() {
        document.querySelectorAll('#nilaiSortable tr').forEach((row, index) => {
            const numCell = row.querySelector('.row-number');
            if (numCell) numCell.textContent = index + 1;
        });
    }

    // Save order via AJAX
    function saveOrder() {
        const rows = document.querySelectorAll('#nilaiSortable tr[data-id]');
        const order = Array.from(rows).map(row => row.dataset.id);

        fetch('{{ route("wali.rapor.reorder-nilai", $rapor->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ order: order })
        }).catch(err => console.error('Reorder error:', err));
    }

    // Visibility toggle visual feedback
    document.querySelectorAll('.visibility-toggle').forEach(toggle => {
        toggle.addEventListener('change', function() {
            const row = this.closest('tr');
            row.classList.toggle('row-hidden', !this.checked);
        });
    });
</script>
@endsection
