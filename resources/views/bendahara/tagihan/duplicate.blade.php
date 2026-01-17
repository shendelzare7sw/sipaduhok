@extends('layouts.sneat')

@section('title', 'Duplikasi Tagihan')
@section('page-title', 'Duplikasi Tagihan')
@section('page-subtitle', 'Salin tagihan dari siswa ke siswa lain')

@section('sidebar-menu')
    @include('bendahara.partials.sneat-sidebar-menu')
@endsection

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
    <div class="container-fluid px-0">

        {{-- Breadcrumb --}}
        <div class="mb-3">
            <a href="{{ route('bendahara.tagihan.index') }}" class="text-primary text-decoration-none">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar Tagihan
            </a>
        </div>

        {{-- Info Alert --}}
        <div class="alert alert-info border-start border-info border-4 shadow-sm mb-4">
            <div class="d-flex">
                <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                <div>
                    <strong>Duplikasi Tagihan</strong>
                    <p class="mb-0 mt-1">Fitur ini memungkinkan Anda menyalin semua tagihan dari satu siswa ke siswa lain secara massal. Sangat berguna saat membuat tagihan untuk siswa baru atau satu kelas.</p>
                </div>
            </div>
        </div>

        {{-- Form Card --}}
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-white">
                <h6 class="m-0 fw-bold text-info">
                    <i class="fas fa-copy me-2"></i>Form Duplikasi Tagihan
                </h6>
            </div>
            <div class="card-body">
                <form action="{{ route('bendahara.tagihan.duplicate.store') }}" method="POST" id="duplicateForm">
                    @csrf

                    <div class="row">
                        {{-- Siswa Sumber --}}
                        <div class="col-md-6 mb-3">
                            <label for="source_siswa_id" class="form-label fw-bold">
                                Siswa Sumber (Copy Dari) <span class="text-danger">*</span>
                            </label>
                            <select name="source_siswa_id" id="source_siswa_id" class="form-select @error('source_siswa_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Siswa Sumber --</option>
                                @foreach($siswaList as $siswa)
                                    <option value="{{ $siswa->id }}" {{ old('source_siswa_id') == $siswa->id ? 'selected' : '' }}>
                                        {{ $siswa->nama_lengkap }} - {{ $siswa->nisn }} ({{ $siswa->kelas->nama_kelas ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('source_siswa_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Filter Kelas untuk Target --}}
                        <div class="col-md-6 mb-3">
                            <label for="filter_kelas" class="form-label fw-bold">
                                Filter Berdasarkan Kelas (Opsional)
                            </label>
                            <select id="filter_kelas" class="form-select">
                                <option value="">-- Semua Kelas --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">
                                        {{ $kelas->nama_kelas }} - {{ $kelas->jenjang }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Filter siswa target berdasarkan kelas</small>
                        </div>

                        {{-- Preview Tagihan Sumber --}}
                        <div class="col-12 mb-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title fw-bold mb-3">
                                        <i class="fas fa-list me-2"></i>Preview Tagihan Sumber
                                    </h6>
                                    <div id="preview_tagihan">
                                        <p class="text-muted mb-0"><em>Pilih siswa sumber untuk melihat preview tagihan</em></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Siswa Target (Multi-select) --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                Siswa Target (Salin Ke) <span class="text-danger">*</span>
                            </label>
                            <div class="border rounded p-3 bg-white" style="max-height: 300px; overflow-y: auto;">
                                <div class="mb-2">
                                    <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAll()">
                                        <i class="fas fa-check-double me-1"></i> Pilih Semua
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                                        <i class="fas fa-times me-1"></i> Batal Pilih
                                    </button>
                                </div>
                                <div id="target_siswa_list" class="row g-2">
                                    @foreach($siswaList as $siswa)
                                        <div class="col-md-6 target-siswa-item" data-kelas-id="{{ $siswa->kelas_id }}">
                                            <div class="form-check">
                                                <input class="form-check-input target-siswa-checkbox" type="checkbox" name="target_siswa_ids[]" value="{{ $siswa->id }}" id="siswa_{{ $siswa->id }}">
                                                <label class="form-check-label" for="siswa_{{ $siswa->id }}">
                                                    <strong>{{ $siswa->nama_lengkap }}</strong><br>
                                                    <small class="text-muted">{{ $siswa->nisn }} - {{ $siswa->kelas->nama_kelas ?? '-' }}</small>
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @error('target_siswa_ids')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                            <small class="text-muted d-block mt-1">Pilih minimal 1 siswa target</small>
                        </div>

                        {{-- Opsi Replace --}}
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">
                                Jika Tagihan Sudah Ada
                            </label>
                            <div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="replace_existing" id="replace_yes" value="1" {{ old('replace_existing', '1') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="replace_yes">
                                        <strong>Replace</strong> - Perbarui dengan nominal baru
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="replace_existing" id="replace_no" value="0" {{ old('replace_existing') == '0' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="replace_no">
                                        <strong>Skip</strong> - Lewati jika sudah ada
                                    </label>
                                </div>
                            </div>
                            <small class="text-muted">Tentukan aksi jika siswa target sudah memiliki tagihan dengan jenis yang sama</small>
                        </div>
                    </div>

                    {{-- Warning Alert --}}
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Proses ini akan menyalin SEMUA tagihan dari siswa sumber</li>
                            <li>Jika memilih "Replace", tagihan yang sudah ada akan diperbarui</li>
                            <li>Jika memilih "Skip", hanya tagihan baru yang akan dibuat</li>
                            <li>Status pembayaran target akan direset menjadi "Belum Bayar"</li>
                        </ul>
                    </div>

                    <div class="mt-4 pt-3 border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div class="text-muted small">
                                <i class="fas fa-info-circle me-1"></i>
                                Tahun Ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran }}</strong>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('bendahara.tagihan.index') }}" class="btn btn-secondary shadow-sm">
                                    <i class="fas fa-times me-1"></i> Batal
                                </a>
                                <button type="button" class="btn btn-info shadow-sm fw-bold" onclick="confirmDuplicate()">
                                    <i class="fas fa-copy me-1"></i> Duplikasi Tagihan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sourceSiswaSelect = document.getElementById('source_siswa_id');
        const filterKelasSelect = document.getElementById('filter_kelas');
        const previewDiv = document.getElementById('preview_tagihan');

        // Load tagihan preview when source siswa changes
        sourceSiswaSelect.addEventListener('change', function() {
            const siswaId = this.value;
            if (!siswaId) {
                previewDiv.innerHTML = '<p class="text-muted mb-0"><em>Pilih siswa sumber untuk melihat preview tagihan</em></p>';
                return;
            }

            // Load via AJAX
            previewDiv.innerHTML = '<p class="text-muted mb-0"><i class="fas fa-spinner fa-spin me-2"></i>Memuat tagihan...</p>';

            fetch(`{{ route('bendahara.tagihan.api.tagihan-preview', ':siswa') }}`.replace(':siswa', siswaId))
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        previewDiv.innerHTML = '<p class="text-danger mb-0">Siswa ini belum memiliki tagihan</p>';
                        return;
                    }

                    let html = '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">';
                    html += '<thead class="table-light"><tr><th>Jenis Tagihan</th><th>Jumlah</th><th>Status</th></tr></thead><tbody>';

                    data.forEach(tagihan => {
                        const statusBadge = tagihan.status === 'sudah_bayar' ? 'bg-success' : 'bg-warning';
                        const statusText = tagihan.status === 'sudah_bayar' ? 'Lunas' : 'Belum Bayar';
                        html += `<tr>
                            <td>${tagihan.jenis_tagihan.replace(/_/g, ' ').toUpperCase()}</td>
                            <td>Rp ${new Intl.NumberFormat('id-ID').format(tagihan.jumlah)}</td>
                            <td><span class="badge ${statusBadge}">${statusText}</span></td>
                        </tr>`;
                    });

                    html += '</tbody></table></div>';
                    previewDiv.innerHTML = html;
                })
                .catch(error => {
                    previewDiv.innerHTML = '<p class="text-danger mb-0">Gagal memuat tagihan</p>';
                });
        });

        // Filter siswa target by kelas
        filterKelasSelect.addEventListener('change', function() {
            const kelasId = this.value;
            const items = document.querySelectorAll('.target-siswa-item');

            items.forEach(item => {
                if (!kelasId || item.dataset.kelasId == kelasId) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                    // Uncheck hidden items
                    const checkbox = item.querySelector('.target-siswa-checkbox');
                    if (checkbox) checkbox.checked = false;
                }
            });
        });
    });

    function selectAll() {
        const checkboxes = document.querySelectorAll('.target-siswa-checkbox');
        checkboxes.forEach(cb => {
            const item = cb.closest('.target-siswa-item');
            if (item.style.display !== 'none') {
                cb.checked = true;
            }
        });
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.target-siswa-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
    }

    function confirmDuplicate() {
        const form = document.getElementById('duplicateForm');
        const sourceSiswaSelect = document.getElementById('source_siswa_id');
        const checkedCount = document.querySelectorAll('.target-siswa-checkbox:checked').length;

        if (!sourceSiswaSelect.value) {
            alert('Pilih siswa sumber terlebih dahulu');
            return;
        }

        if (checkedCount === 0) {
            alert('Pilih minimal 1 siswa target');
            return;
        }

        const sourceName = sourceSiswaSelect.options[sourceSiswaSelect.selectedIndex].text;

        showConfirm({
            title: 'Konfirmasi Duplikasi',
            message: `Anda akan menyalin tagihan dari "${sourceName}" ke ${checkedCount} siswa. Lanjutkan?`,
            type: 'warning',
            confirmText: 'Ya, Duplikasi',
            onConfirm: function() {
                form.submit();
            }
        });
    }
</script>
@endsection
