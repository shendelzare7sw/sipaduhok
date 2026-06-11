@extends('layouts.sneat')

@section('title', 'Edit Tagihan - ' . $siswa->nama_lengkap)
@section('page-title', 'Edit Tagihan Siswa')
@section('page-subtitle', $siswa->nama_lengkap)

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @vite(['resources/css/admin/keuangan/tagihan/edit.css'])
@endsection

@section('content')
<div class="tagihan-edit-page">
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
                                <th class="col-jenis">Jenis Tagihan</th>
                                <th class="col-tahun">Tahun Ajaran</th>
                                <th class="col-jumlah">Jumlah (Rp)</th>
                                <th class="col-jatuh-tempo">Jatuh Tempo</th>
                                <th class="col-aksi text-center">Aksi</th>
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
                                        <div class="input-group input-group-sm tagihan-amount-input">
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
                                               value="{{ old('tanggal_jatuh_tempo.'.$key, now()->addMonth()->format('Y-m-d')) }}"
                                               class="form-control form-control-sm due-date-input"
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
    @vite(['resources/js/admin/keuangan/tagihan/edit.js'])
@endsection
