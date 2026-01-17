@extends('layouts.sneat')

@section('title', 'Kelola Tagihan')
@section('page-title', 'Kelola Tagihan')
@section('page-subtitle', 'Daftar tagihan semua siswa (Admin)')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
    <style>
        /* Styling Tabel & UI */
        .table thead th {
            background: #f8f9fc;
            color: #4e73df;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e3e6f0;
            vertical-align: middle;
            text-align: center;
        }

        .currency-font {
            font-family: 'Nunito', sans-serif;
            font-weight: 700;
        }

        .badge-status {
            padding: 6px 12px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 10px;
        }

        /* Perbaikan Visual Identitas */
        .student-name {
            font-weight: 700;
            color: #1e293b;
            display: block;
        }

        .student-nisn {
            font-size: 11px;
            color: #64748b;
            font-weight: 600;
        }

        .cabang-badge {
            font-size: 10px;
            background: #f1f5f9;
            color: #475569;
            padding: 2px 8px;
            border-radius: 4px;
            font-weight: 800;
            border: 1px solid #e2e8f0;
        }
    </style>
@endsection

@section('content')
    <div style="max-width: 1400px; margin: 0 auto; padding: 0 1rem;">
        <div class="container-fluid px-0">

            {{-- FILTER BOX --}}
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-white">
                    <h6 class="m-0 fw-bold text-primary"><i class="fas fa-filter me-2"></i>Filter & Cari Data</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.keuangan.tagihan.index') }}" method="GET">
                        <div class="row align-items-end">
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold">BERDASARKAN KELAS</label>
                                <select name="kelas_id"
                                    class="form-control form-control-sm border-start border-primary border-3 shadow-sm">
                                    <option value="">-- Semua Kelas --</option>
                                    @foreach($kelasList as $kelas)
                                        <option value="{{ $kelas->id }}" {{ ($filters['kelas_id'] ?? '') == $kelas->id ? 'selected' : '' }}>
                                            {{ $kelas->nama_kelas }} ({{ $kelas->jenjang }}) - {{ $kelas->cabang->nama_cabang ?? 'Cabang tidak diketahui' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-2">
                                <label class="small fw-bold">CARI NAMA SISWA / NISN</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" name="search"
                                        class="form-control border-start border-primary border-3 shadow-sm"
                                        placeholder="Ketik nama atau NISN..." value="{{ $filters['search'] ?? '' }}">
                                </div>
                            </div>
                            <div class="col-md-4 mb-2">
                                <button type="submit" class="btn btn-primary btn-sm px-4 shadow-sm fw-bold">
                                    <i class="fas fa-search me-1"></i> Cari Data
                                </button>
                                <a href="{{ route('admin.keuangan.tagihan.index') }}"
                                    class="btn btn-light btn-sm border px-3 ms-1 fw-bold text-gray-700">
                                    <i class="fas fa-redo me-1"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            {{-- ACTION BAR & INFO --}}
            <div class="row align-items-center mb-3">
                <div class="col-md-6">
                    <span class="text-gray-600 small fw-bold">
                        <i class="fas fa-user-graduate me-1"></i> Menampilkan {{ $siswaList->count() }} dari
                        {{ $siswaList->total() }} siswa terdaftar
                    </span>
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('admin.keuangan.tagihan.import') }}"
                        class="btn btn-outline-danger btn-sm shadow-sm fw-bold px-3 py-2 me-2">
                        <i class="fas fa-file-import me-1"></i> IMPORT EXCEL
                    </a>
                    <div class="btn-group shadow-sm me-2" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm dropdown-toggle fw-bold" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-plus-circle me-1"></i> Buat Tagihan
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.bulk-create') }}">
                                <i class="fas fa-users text-success me-2"></i> Tagihan Massal (Per Kelas)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.create-custom') }}">
                                <i class="fas fa-user-plus text-primary me-2"></i> Tagihan Custom (Individual)
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.keuangan.tagihan.generate-spp') }}">
                                <i class="fas fa-calendar-alt text-info me-2"></i> Generate SPP Bulanan
                            </a></li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.keuangan.tagihan.duplicate') }}" class="btn btn-outline-info btn-sm shadow-sm fw-bold px-3">
                        <i class="fas fa-copy me-1"></i> Duplikasi
                    </a>
                </div>
            </div>

            {{-- TABEL UTAMA --}}
            <div class="card shadow mb-4">
                <div class="card-body p-0">
                    @if($siswaList->isEmpty())
                        <div class="text-center py-5 text-muted opacity-50">
                            <i class="fas fa-folder-open fa-4x mb-3"></i>
                            <h5>Data siswa tidak ditemukan</h5>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th width="50">NO</th>
                                        <th class="text-start">IDENTITAS SISWA</th>
                                        <th>NISN</th>
                                        <th>KELAS</th>
                                        <th>CABANG</th>
                                        <th>TOTAL TAGIHAN</th>
                                        <th>SUDAH BAYAR</th>
                                        <th>SISA</th>
                                        <th>STATUS</th>
                                        <th width="120">AKSI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($siswaList as $index => $siswa)
                                        <tr>
                                            <td class="text-center align-middle fw-bold text-gray-600">
                                                {{ $siswaList->firstItem() + $index }}</td>
                                            <td class="align-middle">
                                                <span class="student-name">{{ $siswa->nama_lengkap }}</span>
                                                <span class="student-nisn">Siswa Aktif</span>
                                            </td>
                                            <td class="text-center align-middle fw-bold text-gray-800">{{ $siswa->nisn }}</td>
                                            <td class="text-center align-middle">
                                                <span class="badge bg-primary px-2 py-1 fw-bold text-uppercase"
                                                    style="font-size: 10px;">
                                                    {{ $siswa->kelas->nama_kelas ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <span class="cabang-badge">{{ $siswa->cabang->kode_cabang ?? '-' }}</span>
                                            </td>
                                            <td class="align-middle currency-font text-dark">
                                                Rp {{ number_format($siswa->total_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="align-middle currency-font text-success">
                                                Rp {{ number_format($siswa->tagihan_lunas, 0, ',', '.') }}
                                            </td>
                                            <td
                                                class="align-middle currency-font {{ $siswa->sisa_tagihan > 0 ? 'text-danger' : 'text-success' }}">
                                                Rp {{ number_format($siswa->sisa_tagihan, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center align-middle">
                                                @if($siswa->sisa_tagihan <= 0 && $siswa->total_tagihan > 0)
                                                    <span class="badge bg-success badge-status shadow-sm"><i
                                                            class="fas fa-check-circle"></i> LUNAS</span>
                                                @elseif($siswa->total_tagihan == 0)
                                                    <span class="badge bg-light border badge-status text-muted">KOSONG</span>
                                                @else
                                                    <span class="badge bg-danger badge-status shadow-sm"><i
                                                            class="fas fa-times-circle"></i> BELUM LUNAS</span>
                                                @endif
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="btn-group shadow-sm">
                                                    <a href="{{ route('admin.keuangan.tagihan.show', $siswa->id) }}"
                                                        class="btn btn-sm btn-info" title="Lihat Detail">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.tagihan.edit', $siswa->id) }}"
                                                        class="btn btn-sm btn-warning" title="Edit Tagihan">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <a href="{{ route('admin.keuangan.pembayaran.riwayat-siswa', $siswa->id) }}"
                                                        class="btn btn-sm btn-success" title="Riwayat Bayar">
                                                        <i class="fas fa-history"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-light py-3 border-top">
                            <div class="d-flex justify-content-center">
                                {{ $siswaList->withQueryString()->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- INFORMASI TAMBAHAN --}}
            <div class="alert alert-warning border-start border-warning border-4 shadow-sm mt-2">
                <div class="d-flex">
                    <i class="fas fa-info-circle fa-lg me-2 mt-1"></i>
                    <small class="fw-bold text-gray-800">
                        Catatan: Total Tagihan mencakup seluruh kewajiban siswa di periode berjalan. Gunakan fitur "Buat
                        Tagihan Massal" untuk efisiensi waktu jika tagihan per jenjang bersifat seragam.
                    </small>
                </div>
            </div>
        </div>
    </div>
@endsection