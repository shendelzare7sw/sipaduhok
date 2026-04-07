@extends('layouts.sneat')

@section('title', 'Edit Tagihan - ' . $siswa->nama_lengkap)
@section('page-title', 'Edit Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

{{-- SweetAlert2 --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

@section('styles')
<style>
    .student-avatar {
        width: 60px;
        height: 60px;
        flex-shrink: 0;
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        font-weight: 600;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.3);
    }
    
    @media (max-width: 768px) {
        .table-responsive {
            border: none !important;
        }
        .table-responsive table {
            border-collapse: separate;
            border-spacing: 0 1rem;
        }
        .table-responsive thead {
            display: none;
        }
        .table-responsive tbody tr {
            display: block;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            margin-bottom: 1rem;
        }
        .table-responsive tbody td {
            display: block;
            text-align: left !important;
            padding: 0.75rem 1rem;
            border: none;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-responsive tbody td:last-child {
            border-bottom: none;
        }
        .table-responsive tbody td::before {
            content: attr(data-label);
            display: block;
            font-weight: 700;
            font-size: 0.75rem;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 0.5rem;
        }
        .input-group, .form-control, .form-select {
            max-width: 100% !important;
        }
    }
    
    /* SweetAlert Styling */
    .swal2-popup {
        font-family: 'Public Sans', sans-serif;
        border-radius: 1rem;
    }
    
    .swal2-title {
        font-size: 1.5rem;
        color: #566a7f;
    }
    
    .swal2-html-container {
        color: #697a8d;
    }
</style>
@endsection

@section('content')
<div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
<div class="container-fluid px-0">

    {{-- Breadcrumb --}}
    <div class="mb-3">
        <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="text-primary text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Detail Tagihan
        </a>
    </div>

    {{-- Info Siswa Card --}}
    <div class="card shadow mb-4 border-start border-primary border-4">
        <div class="card-body bg-primary bg-opacity-10">
            <div class="d-flex gap-3 align-items-center">
                <div class="student-avatar">
                    {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                </div>
                <div>
                    <h5 class="mb-1 fw-bold text-gray-800">{{ $siswa->nama_lengkap }}</h5>
                    <p class="mb-0 text-muted small">
                        <i class="fas fa-id-card me-1"></i> NISN: {{ $siswa->nisn }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-school me-1"></i> Kelas: {{ $siswa->kelas->nama_kelas ?? '-' }}
                        <span class="mx-2">|</span>
                        <i class="fas fa-building me-1"></i> {{ $siswa->cabang->nama_cabang ?? '-' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Tagihan --}}
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-white">
            <h6 class="m-0 fw-bold text-warning">
                <i class="fas fa-edit me-2"></i>Input/Edit Tagihan
            </h6>
        </div>
        <div class="card-body">
            {{-- Info Alert tentang SPP --}}
            <div class="alert alert-info border-start border-info border-4 mb-4">
                <div class="d-flex align-items-start">
                    <i class="fas fa-info-circle me-2 mt-1"></i>
                    <div>
                        <strong>Informasi Penting:</strong>
                        <p class="mb-0 mt-1">Untuk tagihan <strong>SPP Bulanan</strong>, silakan gunakan fitur <a href="{{ route('admin.keuangan.tagihan.generate-spp') }}" class="alert-link fw-bold">"Generate SPP"</a> yang akan membuat 12 tagihan SPP otomatis (Januari-Desember) dengan tanggal jatuh tempo yang lebih akurat.</p>
                    </div>
                </div>
            </div>

            <form action="{{ route('admin.keuangan.tagihan.update', $siswa->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" width="50">No</th>
                                <th style="min-width: 200px;">Jenis Tagihan</th>
                                <th style="min-width: 180px;">Tahun Ajaran</th>
                                <th style="min-width: 200px;">Jumlah (Rp)</th>
                                <th style="min-width: 150px;">Jatuh Tempo</th>
                                <th style="min-width: 80px;" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jenisTagihan as $key => $label)
                                @php
                                    // Cari tagihan record untuk item ini
                                    $tagihanRecord = $allTagihan->firstWhere('jenis_tagihan', $key);
                                    // Cek apakah ini tipe custom (tidak ada di standard list)
                                    $isCustom = !in_array($key, $standardJenisTagihan);
                                    // Cek apakah sudah ada pembayaran
                                    $hasPembayaran = $tagihanRecord && $tagihanRecord->pembayaran()->where('status_validasi', 'disetujui')->exists();
                                    // PROTEKSI: Hanya lock jika ada pembayaran dari orang tua
                                    // Rp 0 (setting admin) tetap bisa diedit
                                    $isReadOnly = $hasPembayaran;
                                    $canDelete = $tagihanRecord && !$hasPembayaran && ($isCustom || $tagihanRecord->status === 'belum_bayar');
                                @endphp
                                <tr class="{{ $isReadOnly ? 'table-light opacity-75' : '' }}">
                                    <td class="text-center align-middle fw-bold text-gray-600" data-label="No">{{ $loop->iteration }}</td>
                                    <td class="align-middle" data-label="Jenis Tagihan">
                                        <strong>{{ $label }}</strong>
                                        @if($isReadOnly)
                                            <br><small class="badge bg-success">✓ Sudah Dibayar Orang Tua</small>
                                        @elseif($isCustom && $tagihanRecord)
                                            <br><small class="badge bg-success">Custom</small>
                                        @endif
                                        @if($key === 'spp')
                                            <br><small class="text-muted">Tagihan bulanan</small>
                                        @endif
                                    </td>
                                    <td class="align-middle" data-label="Tahun Ajaran">
                                        <select name="tahun_ajaran_id[{{ $key }}]" class="form-select form-select-sm" {{ $isReadOnly ? 'disabled' : '' }}>
                                            @foreach($allYears as $thn)
                                                <option value="{{ $thn->id }}" {{ ($tahunAjaran->id == $thn->id) ? 'selected' : '' }}>
                                                    {{ $thn->nama_tahun_ajaran }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="align-middle" data-label="Jumlah (Rp)">
                                        <div class="input-group input-group-sm" style="max-width: 250px;">
                                            <span class="input-group-text bg-white">Rp</span>
                                            @php
                                                $rawValue = intval($tagihanExist[$key] ?? 0);
                                            @endphp
                                            <input type="text"
                                                   name="tagihan[{{ $key }}]"
                                                   class="form-control currency-input"
                                                   value="{{ number_format(old('tagihan.'.$key, $rawValue), 0, ',', '.') }}"
                                                   placeholder="0"
                                                   {{ $isReadOnly ? 'disabled' : '' }}>
                                        </div>
                                        @error('tagihan.'.$key)
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </td>
                                    <td class="align-middle" data-label="Jatuh Tempo">
                                        <input type="date"
                                               name="tanggal_jatuh_tempo[{{ $key }}]"
                                               class="form-control form-control-sm"
                                               value="{{ old('tanggal_jatuh_tempo.'.$key, now()->addMonth()->format('Y-m-d')) }}"
                                               style="max-width: 200px;"
                                               {{ $isReadOnly ? 'disabled' : '' }}>
                                    </td>
                                    <td class="align-middle text-center" data-label="Aksi">
                                        @if($canDelete && $tagihanRecord)
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger delete-tagihan-btn"
                                                    data-tagihan-id="{{ $tagihanRecord->id }}"
                                                    data-tagihan-label="{{ $label }}"
                                                    data-delete-url="{{ route('admin.keuangan.tagihan.destroy-item', $tagihanRecord->id) }}"
                                                    title="Hapus tagihan">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            Masukkan nominal 0 jika siswa tidak memiliki tagihan untuk jenis tersebut.
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}" class="btn btn-secondary shadow-sm">
                                <i class="fas fa-times me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-primary shadow-sm fw-bold">
                                <i class="fas fa-save me-1"></i> Simpan Tagihan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Warning Alert --}}
    <div class="alert alert-warning border-start border-warning border-4 shadow-sm">
        <div class="d-flex">
            <i class="fas fa-exclamation-triangle fa-lg me-2 mt-1"></i>
            <div>
                <strong>Catatan:</strong>
                <ul class="mb-0 mt-2">
                    <li>Tagihan yang sudah dibayar oleh orang tua tidak dapat diedit atau dihapus untuk menjaga integritas data transaksi.</li>
                    <li>Tagihan dengan nominal Rp 0 (setting admin) tetap dapat diedit kapan saja untuk fleksibilitas perubahan.</li>
                    <li>Perubahan tagihan akan mempengaruhi status pembayaran siswa.</li>
                    <li>Tahun ajaran: <strong>{{ $tahunAjaran->nama_tahun_ajaran ?? '-' }}</strong></li>
                </ul>
            </div>
        </div>
    </div>

</div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currencyInputs = document.querySelectorAll('.currency-input');

        // Format number with thousand separator (Indonesian format: dot)
        function formatCurrency(value) {
            // Remove all non-digit characters
            let numericValue = String(value).replace(/\D/g, '');
            // Remove leading zeros
            numericValue = numericValue.replace(/^0+/, '') || '0';
            // Format with dots as thousand separator
            return numericValue.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        }

        // Apply formatting to each currency input
        currencyInputs.forEach(input => {
            // Real-time formatting as user types
            input.addEventListener('input', function(e) {
                const cursorPos = this.selectionStart;
                const oldLength = this.value.length;
                
                this.value = formatCurrency(this.value);
                
                // Adjust cursor position after formatting
                const newLength = this.value.length;
                const diff = newLength - oldLength;
                this.setSelectionRange(cursorPos + diff, cursorPos + diff);
            });

            // Handle paste event
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                this.value = formatCurrency(pastedText);
            });
        });

        // Handle delete tagihan with SweetAlert
        document.querySelectorAll('.delete-tagihan-btn').forEach(button => {
            button.addEventListener('click', function() {
                const tagihanLabel = this.getAttribute('data-tagihan-label');
                const tagihanId = this.getAttribute('data-tagihan-id');
                const deleteUrl = this.getAttribute('data-delete-url');

                Swal.fire({
                    title: 'Hapus Tagihan?',
                    html: `Apakah Anda yakin ingin menghapus tagihan <strong>${tagihanLabel}</strong>?<br><small class="text-muted">Aksi ini tidak dapat dibatalkan.</small>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Submit DELETE request via fetch
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => {
                            if (response.ok) {
                                Swal.fire({
                                    title: 'Terhapus!',
                                    text: 'Tagihan berhasil dihapus.',
                                    icon: 'success',
                                    confirmButtonText: 'OK'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                return response.json().then(data => {
                                    throw new Error(data.error || 'Gagal menghapus tagihan');
                                });
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'Error!',
                                text: error.message || 'Terjadi kesalahan saat menghapus tagihan',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                        });
                    }
                });
            });
        });
    });
</script>
@endsection