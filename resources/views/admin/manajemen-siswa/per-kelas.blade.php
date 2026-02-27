@extends('layouts.sneat')

@section('title', 'Siswa Kelas ' . $kelas->nama_kelas)

@section('page-title', 'Kelola Siswa Kelas')
@section('page-subtitle', 'Kelas ' . $kelas->nama_kelas . ' - ' . ($kelas->tahunAjaran->nama_tahun_ajaran ?? ''))

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('content')
    <style>
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #6b7280;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            color: #3b82f6;
        }

        .breadcrumb .current {
            color: #111827;
            font-weight: 500;
        }

        .info-banner {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 16px;
            padding: 24px 32px;
            color: white;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .info-banner h2 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .info-banner-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }

        .info-banner-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.9;
        }

        .info-banner-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
        }

        .info-banner-stat {
            text-align: center;
            background: rgba(255, 255, 255, 0.15);
            padding: 12px 20px;
            border-radius: 10px;
            min-width: 80px;
        }

        .info-banner-stat-value {
            font-size: 24px;
            font-weight: 700;
        }

        .info-banner-stat-label {
            font-size: 11px;
            opacity: 0.8;
        }

        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        @media (max-width: 992px) {
            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .card-header h5 {
            margin: 0;
            font-size: 16px;
            font-weight: 600;
            color: #111827;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .card-header h5 i {
            color: #3b82f6;
        }

        .card-body {
            padding: 24px;
        }

        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 16px;
        }

        .form-row select {
            flex: 1;
            padding: 10px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
        }

        .form-row select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        }

        .btn-danger {
            background: #fee2e2;
            color: #dc2626;
            border: none;
        }

        .btn-danger:hover {
            background: #fecaca;
        }

        .btn-outline {
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
        }

        .btn-outline:hover {
            background: #f9fafb;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 12px 16px;
            background: #f9fafb;
            color: #4b5563;
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e5e7eb;
        }

        .table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            color: #374151;
            font-size: 14px;
        }

        .table tr:hover td {
            background: #f9fafb;
        }

        .siswa-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .siswa-avatar {
            width: 36px;
            height: 36px;
            min-width: 36px;
            min-height: 36px;
            flex-shrink: 0;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 13px;
        }

        .siswa-avatar.female {
            background: linear-gradient(135deg, #ec4899 0%, #db2777 100%);
        }

        .badge {
            padding: 4px 10px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 600;
        }

        .badge-l {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-p {
            background: #fce7f3;
            color: #9d174d;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .kuota-warning {
            background: #fef3c7;
            border: 1px solid #fcd34d;
            border-radius: 8px;
            padding: 12px 16px;
            color: #92400e;
            font-size: 13px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>

    <div style="max-width: 1200px; margin: 0 auto; padding: 0 1rem;">
        <div class="breadcrumb">
            <a href="{{ route('admin.dashboard') }}"><i class="fas fa-home"></i></a>
            <span>/</span>
            <a href="{{ route('admin.manajemen-siswa.index') }}">Manajemen Siswa</a>
            <span>/</span>
            <span class="current">Kelas {{ $kelas->nama_kelas }}</span>
        </div>

        <div class="info-banner">
            <div>
                <h2>Kelas {{ $kelas->nama_kelas }}</h2>
                <div class="info-banner-meta">
                    <div class="info-banner-item"><i class="fas fa-building"></i> {{ $kelas->cabang->nama_cabang ?? '-' }}
                    </div>
                    <div class="info-banner-item"><i class="fas fa-layer-group"></i> {{ $kelas->jenjang }}</div>
                    <div class="info-banner-item"><i class="fas fa-calendar"></i>
                        {{ $kelas->tahunAjaran->nama_tahun_ajaran ?? '-' }}</div>
                    @if($kelas->waliKelas)
                        <div class="info-banner-item"><i class="fas fa-user-tie"></i> {{ $kelas->waliKelas->nama_lengkap }}
                        </div>
                    @endif
                </div>
            </div>
            <div class="info-banner-stats">
                <div class="info-banner-stat">
                    <div class="info-banner-stat-value">{{ $stats['totalSiswa'] }}</div>
                    <div class="info-banner-stat-label">Total Siswa</div>
                </div>
                <div class="info-banner-stat">
                    <div class="info-banner-stat-value">{{ $stats['siswaLaki'] }}</div>
                    <div class="info-banner-stat-label">Laki-laki</div>
                </div>
                <div class="info-banner-stat">
                    <div class="info-banner-stat-value">{{ $stats['siswaPerempuan'] }}</div>
                    <div class="info-banner-stat-label">Perempuan</div>
                </div>
                <div class="info-banner-stat">
                    <div class="info-banner-stat-value">{{ $stats['sisaKuota'] }}</div>
                    <div class="info-banner-stat-label">Sisa Kuota</div>
                </div>
            </div>
        </div>

        <div class="grid-2">
            {{-- Tambah Siswa --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-plus-circle"></i> Tambah Siswa</h5>
                </div>
                <div class="card-body">
                    @if($stats['sisaKuota'] <= 0)
                        <div class="kuota-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Kuota kelas sudah penuh! Tidak bisa menambah siswa.
                        </div>
                    @endif

                    @if($availableSiswa->count() > 0 && $stats['sisaKuota'] > 0)
                        <form action="{{ route('admin.manajemen-siswa.add-to-kelas', $kelas) }}" method="POST">
                            @csrf
                            <div class="form-row">
                                <select name="siswa_id" required>
                                    <option value="">-- Pilih Siswa --</option>
                                    @foreach($availableSiswa as $s)
                                        <option value="{{ $s->id }}">{{ $s->nama_lengkap }} ({{ $s->nisn }})</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Tambah
                                </button>
                            </div>
                        </form>
                        <small style="color: #6b7280;">Menampilkan siswa tanpa kelas dari cabang
                            {{ $kelas->cabang->nama_cabang ?? '' }}</small>
                    @elseif($stats['sisaKuota'] > 0)
                        <div class="empty-state">
                            <i class="fas fa-check-circle"></i>
                            <p>Semua siswa di cabang ini sudah memiliki kelas</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Daftar Siswa di Kelas --}}
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users"></i> Daftar Siswa ({{ $stats['totalSiswa'] }})</h5>
                </div>
                <div class="card-body" style="padding: 0;">
                    @if($siswaList->count() > 0)
                        <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Siswa</th>
                                    <th>JK</th>
                                    <th style="width: 60px;"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($siswaList as $index => $siswa)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="siswa-info">
                                                <div class="siswa-avatar {{ $siswa->jenis_kelamin == 'P' ? 'female' : '' }}">
                                                    {{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}
                                                </div>
                                                <span>{{ $siswa->nama_lengkap }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $siswa->jenis_kelamin == 'L' ? 'badge-l' : 'badge-p' }}">
                                                {{ $siswa->jenis_kelamin }}
                                            </span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.manajemen-siswa.remove-from-kelas', $kelas) }}"
                                                method="POST" id="deleteForm{{ $siswa->id }}">
                                                @csrf
                                                <input type="hidden" name="siswa_id" value="{{ $siswa->id }}">
                                                <button type="button" class="btn btn-danger btn-sm" title="Keluarkan"
                                                    onclick="confirmRemoveSiswa('{{ $siswa->id }}', '{{ $siswa->nama_lengkap }}')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <p>Belum ada siswa di kelas ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div style="margin-top: 24px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <a href="{{ route('admin.manajemen-siswa.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Siswa
            </a>
            <a href="{{ route('admin.manajemen-siswa.print', ['kelas_id' => $kelas->id]) }}" class="btn btn-primary"
                target="_blank">
                <i class="fas fa-print"></i> Cetak Daftar Kelas
            </a>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: none; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); overflow: hidden;">
                <button type="button" data-bs-dismiss="modal" aria-label="Close" style="position: absolute; top: 16px; right: 16px; background: rgba(0,0,0,0.05); border: none; color: #6b7280; transition: all 0.2s; padding: 0; border-radius: 50%; display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; z-index: 10;" onmouseover="this.style.background='rgba(0,0,0,0.1)'; this.style.color='#1f2937'" onmouseout="this.style.background='rgba(0,0,0,0.05)'; this.style.color='#6b7280'">
                    <i class="fas fa-times" style="font-size: 16px;"></i>
                </button>
                <div class="modal-body" style="padding: 40px 30px 30px; text-align: center;">
                    <div style="width: 80px; height: 80px; background: #fee2e2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px;">
                        <i class="fas fa-exclamation-triangle" style="font-size: 36px; color: #ef4444;"></i>
                    </div>
                    
                    <h4 style="font-weight: 700; color: #111827; margin-bottom: 12px;">Keluarkan Siswa?</h4>
                    
                    <p style="color: #4b5563; font-size: 15px; margin-bottom: 24px; line-height: 1.6;">
                        Apakah Anda yakin ingin mengeluarkan <br>
                        <strong style="color: #111827; font-size: 16px;" id="siswaName"></strong><br>
                        dari <span style="font-weight: 500;">Kelas {{ $kelas->nama_kelas }}</span>?
                    </p>
                    
                    <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 12px; padding: 16px; margin-bottom: 28px; text-align: left; display: flex; align-items: flex-start; gap: 12px;">
                        <i class="fas fa-info-circle" style="color: #64748b; font-size: 20px; margin-top: 2px;"></i>
                        <p style="color: #64748b; font-size: 13px; margin: 0; line-height: 1.5;">
                            Siswa akan dikeluarkan dari kelas ini, namun data siswa tetap tersimpan dan dapat ditambahkan kembali kapan saja.
                        </p>
                    </div>
                    
                    <div style="display: flex; gap: 16px;">
                        <button type="button" class="btn" data-bs-dismiss="modal" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; background: #f1f5f9; color: #475569; border: none; transition: all 0.2s;" onmouseover="this.style.background='#e2e8f0'; this.style.color='#1e293b'" onmouseout="this.style.background='#f1f5f9'; this.style.color='#475569'">
                            Batal
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteBtn" style="flex: 1; padding: 12px; border-radius: 12px; font-weight: 600; border: none; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);">
                            <i class="fas fa-user-minus me-2"></i> Ya, Keluarkan
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let deleteFormId = null;

        function confirmRemoveSiswa(siswaId, siswaName) {
            deleteFormId = siswaId;
            document.getElementById('siswaName').textContent = siswaName;

            const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
            modal.show();
        }

        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (deleteFormId) {
                document.getElementById('deleteForm' + deleteFormId).submit();
            }
        });
    </script>
@endsection