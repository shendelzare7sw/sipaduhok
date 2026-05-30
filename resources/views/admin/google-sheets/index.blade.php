@extends('layouts.sneat')

@section('title', 'Sinkronisasi Google Sheets')

@section('page-title', 'Sinkronisasi Google Sheets')
@section('page-subtitle', 'Kelola sinkronisasi data dengan Google Sheets')

@section('sidebar-menu')
    @include('admin.partials.sneat-sidebar-menu')
@endsection

@section('styles')
<style>
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        margin-bottom: 24px;
    }

    .card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header h5 {
        margin: 0;
        font-size: 18px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .card-body {
        padding: 24px;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-success {
        background: #d1fae5;
        color: #047857;
    }

    .status-failed {
        background: #fee2e2;
        color: #dc2626;
    }

    .status-partial {
        background: #fef3c7;
        color: #d97706;
    }

    .status-pending {
        background: #dbeafe;
        color: #0284c7;
    }

    .status-disabled {
        background: #f3f4f6;
        color: #6b7280;
    }

    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .module-card {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 16px;
        background: white;
    }

    .module-card h6 {
        margin: 0 0 8px 0;
        font-size: 14px;
        font-weight: 600;
        color: #1a1a1a;
    }

    .module-card p {
        margin: 8px 0;
        font-size: 13px;
        color: #6b7280;
    }

    .module-tier {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .tier-1 {
        background: #dbeafe;
        color: #0284c7;
    }

    .tier-2 {
        background: #e9d5ff;
        color: #7c3aed;
    }

    .sync-info {
        background: #f0fdf4;
        border-left: 3px solid #22c55e;
        padding: 12px;
        border-radius: 4px;
        margin: 12px 0;
        font-size: 13px;
    }

    .sync-info.failed {
        background: #fef2f2;
        border-left-color: #ef4444;
    }

    .sync-info.pending {
        background: #eff6ff;
        border-left-color: #3b82f6;
    }

    .sync-buttons {
        display: flex;
        gap: 8px;
        margin-top: 12px;
        flex-wrap: wrap;
    }

    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
        border: 1px solid #d1d5db;
        background: white;
        color: #374151;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-sm:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .btn-sm.primary {
        background: #3b82f6;
        color: white;
        border-color: #3b82f6;
    }

    .btn-sm.primary:hover {
        background: #2563eb;
    }

    .btn-sm.success {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .btn-sm.success:hover {
        background: #059669;
    }

    .btn-sm.danger {
        background: #ef4444;
        color: white;
        border-color: #ef4444;
    }

    .btn-sm.danger:hover {
        background: #dc2626;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
    }

    .table thead {
        background: #f9fafb;
    }

    .table th {
        padding: 12px;
        text-align: left;
        font-weight: 600;
        color: #374151;
        border-bottom: 1px solid #e5e7eb;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid #f3f4f6;
    }

    .table tbody tr:hover {
        background: #fafbfc;
    }

    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
    }

    .empty-state img {
        max-width: 120px;
        margin-bottom: 16px;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #0c4a6e;
    }

    .alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #15803d;
    }

    .alert-danger {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
    }

    .setup-btn {
        padding: 12px 24px;
        background: #3b82f6;
        color: white;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .setup-btn:hover {
        background: #2563eb;
    }
</style>
@endsection

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <strong>Error!</strong>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Setup Status & Action Header -->
            @if (!$isEnabled || !$credentialsExists)
                <div class="alert alert-info" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong><i class="fas fa-info-circle"></i> Pengaturan Diperlukan!</strong> Silakan konfigurasi kredensial Google Sheets Anda untuk mengaktifkan sinkronisasi.
                    </div>
                    <a href="{{ route('admin.google-sheets.setup') }}" class="btn btn-primary" style="margin: 0; white-space: nowrap;">
                        <i class="fas fa-cog"></i> Panduan Pengaturan
                    </a>
                </div>
            @else
                <div class="alert alert-success" style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong><i class="fas fa-check-circle"></i> Terhubung!</strong> Google Sheets sudah dikonfigurasi. ID: <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 3px;">{{ $spreadsheetId }}</code>
                    </div>
                    <a href="{{ route('admin.google-sheets.setup') }}" class="btn btn-sm btn-secondary" style="margin: 0; white-space: nowrap;">
                        <i class="fas fa-cog"></i> Ubah Konfigurasi
                    </a>
                </div>
            @endif

            <!-- Modules Section -->
            <div class="card">
                <div class="card-header">
                    <h5>Modul Data</h5>
                </div>
                <div class="card-body">
                    <div class="module-grid">
                        @foreach ($modules as $moduleKey => $module)
                            <div class="module-card">
                                <div>
                                    <span class="module-tier {{ $module['tier'] === 1 ? 'tier-1' : 'tier-2' }}">
                                        {{ $module['tier'] === 1 ? 'HARIAN' : 'MINGGUAN' }}
                                    </span>
                                </div>
                                <h6>{{ $module['sheet_name'] }}</h6>

                                @if (isset($lastSyncs[$moduleKey]))
                                    @if ($lastSyncs[$moduleKey]['push'])
                                        <div class="sync-info">
                                            <strong><i class="fas fa-arrow-up"></i> Kirim:</strong> {{ $lastSyncs[$moduleKey]['push']->getStatusLabel() }}
                                            <br>
                                            <small>{{ $lastSyncs[$moduleKey]['push']->synced_at->copy()->locale('id')->diffForHumans() }}</small>
                                        </div>
                                    @endif

                                    @if ($lastSyncs[$moduleKey]['pull'])
                                        <div class="sync-info">
                                            <strong><i class="fas fa-arrow-down"></i> Ambil:</strong> {{ $lastSyncs[$moduleKey]['pull']->getStatusLabel() }}
                                            <br>
                                            <small>{{ $lastSyncs[$moduleKey]['pull']->synced_at->copy()->locale('id')->diffForHumans() }}</small>
                                        </div>
                                    @endif
                                @else
                                    <div class="sync-info pending">
                                        <small>Belum ada riwayat sinkronisasi</small>
                                    </div>
                                @endif

                                @if ($isEnabled && $credentialsExists)
                                    <div class="sync-buttons">
                                        <button type="button" class="btn-sm success" onclick="pushModule('{{ $moduleKey }}')">
                                            <i class="fas fa-arrow-up"></i> Kirim
                                        </button>
                                        <button type="button" class="btn-sm primary" onclick="pullPreview('{{ $moduleKey }}')">
                                            <i class="fas fa-eye"></i> Pratinjau
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Sync History -->
            <div class="card">
                <div class="card-header">
                    <h5>Riwayat Sinkronisasi Terbaru</h5>
                </div>
                <div class="card-body">
                    @if ($syncHistory->isEmpty())
                        <div class="empty-state">
                            <p><i class="fas fa-chart-bar"></i> Belum ada riwayat sinkronisasi</p>
                            <small>Mulai dengan mengirim atau mengambil data dari modul di atas</small>
                        </div>
                    @else
                        <div style="overflow-x: auto;">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Modul</th>
                                        <th>Arah</th>
                                        <th>Nama Sheet</th>
                                        <th>Baris</th>
                                        <th>Status</th>
                                        <th>Disinkron oleh</th>
                                        <th>Waktu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($syncHistory as $log)
                                        <tr>
                                            <td><strong>{{ $log->module }}</strong></td>
                                            <td>{{ $log->getDirectionLabel() }}</td>
                                            <td>{{ $log->sheet_name }}</td>
                                            <td>{{ $log->rows_synced }}</td>
                                            <td>
                                                <span class="status-badge {{ $log->status === 'success' ? 'status-success' : ($log->status === 'failed' ? 'status-failed' : 'status-partial') }}">
                                                    {{ $log->getStatusLabel() }}
                                                </span>
                                            </td>
                                            <td>{{ $log->user->name ?? 'Sistem' }}</td>
                                            <td><small>{{ $log->synced_at->copy()->locale('id')->translatedFormat('d M Y H:i') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentPullModule = null;

    function pushModule(module) {
        if (!confirm('Kirim data ' + module + ' ke Google Sheets?')) return;

        fetch('{{ route("admin.google-sheets.push", ":module") }}'.replace(':module', module), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✓ Pekerjaan sinkronisasi telah antri! Periksa kembali sebentar lagi.');
                setTimeout(() => location.reload(), 1000);
            } else {
                alert('✕ Kesalahan: ' + data.message);
            }
        })
        .catch(e => alert('✕ Kesalahan: ' + e.message));
    }

    function pullPreview(module) {
        // Navigate ke halaman pull preview
        window.location.href = '{{ route("admin.google-sheets.pull-preview", ":module") }}'.replace(':module', module);
    }
</script>
@endsection
